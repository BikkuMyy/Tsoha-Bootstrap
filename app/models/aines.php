<?php

/**
 * Aines-tietokohteen malliluokka
 *
 * @author mari
 */
class Aines extends BaseModel {

    public $id, $nimi, $valittu;

    /**
     * Konstruktori, joka kutsuu BaseModel-yliluokan construct-metodia
     * ja määrittää luokan validaatiometodien nimet.
     * 
     * @param array $attributes luokan attribuutit
     */
    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_nimi');
    }
    
    /**
     * Metodi kutsuu BaseModel-yliluokan validointimetodia 
     * ja palauttaa mahdolliset virheet.
     * 
     * @return array validoinnissa huomatut virheet
     */
    public function validate_nimi(){
        return parent::validate_string_length($this->nimi, 3, 20);
        
    }

    /**
     * Metodi hakee tietokohdetta vastaavasta tietokantataulusta kaikki rivit 
     * nimen mukaan aakkostettuna.
     * 
     * @return array löydetyt rivit
     */
    public static function all() {
        $query = DB::connection()->prepare('SELECT * FROM Aines ORDER BY nimi');
        $query->execute();
        $rivit = $query->fetchAll();
        $ainekset = array();

        foreach ($rivit as $rivi) {
            $ainekset[] = new Aines(array('id' => $rivi['id'],
                'nimi' => $rivi['nimi']));
        }

        return $ainekset;
    }
    
    /**
     * Metodi hakee tietokohdetta vastaavasta tietokantataulusta rivin 
     * parametrina annetun id:n perusteella.
     * 
     * @param integer $id haettavan kohteen id
     * @return Aines löydetty rivi 
     */
    public static function find($id) {
        $query = DB::connection()->prepare('SELECT nimi FROM Aines '
                . 'WHERE id = :id LIMIT 1');

        $query->execute(array('id' => $id));
        $rivi = $query->fetch();

        if ($rivi) {
            $aines = new Aines(array('id' => $id, 'nimi' => $rivi['nimi']));
        }

        return $aines;
    }

    /**
     * Metodi hakee tietokohdetta vastaavasta tietokantataulusta rivin
     * parametrina annetun nimen perusteella.
     * 
     * @param string $nimi haettavan kohteen nimi
     * @return Aines löydetty rivi
     */
    public static function findBy($nimi) {
        $query = DB::connection()->prepare('SELECT * FROM Aines '
                . 'WHERE nimi = :nimi LIMIT 1');

        $query->execute(array('nimi' => $nimi));
        $rivi = $query->fetch();

        if ($rivi) {
            $aines = new Aines(array(
                'id' => $rivi['id'],
                'nimi' => $rivi['nimi']));
        }

        return $aines;
    }
    
    /**
     * Metodi hakee Aines-tietokantataulusta hakusanan sisältäviä rivejä.
     * 
     * @param string $haku hakusana
     * @return array hakutulokset
     */
    public static function searchBy($haku){
        $query = DB::connection()->prepare('SELECT * FROM Aines WHERE nimi LIKE :haku');
        $query->execute(array('haku' => '%'.$haku.'%'));
        $rivit = $query->fetchAll();
        $ainekset = array();

        foreach ($rivit as $rivi) {
            $ainekset[] = new Aines(array('id' => $rivi['id'],
                'nimi' => $rivi['nimi']));
        }

        return $ainekset;
    }

    /**
     * Metodi tallentaa uuden rivin tietokohdetta vastaavaan tietokantatauluun.
     */
    public function save() {
        $query = DB::connection()->prepare('INSERT INTO Aines (nimi) '
                                         . 'VALUES (:nimi) RETURNING id');

        $query->execute(array('nimi' => $this->nimi));
        $rivi = $query->fetch();
        $this->id = $rivi['id'];
    }

    /**
     * Metodi hakee kaikki parametrina annettuun ruokaan liittyvät ainekset
     * näiden kahden tietokohteen välisestä liitostaulusta.
     * 
     * @param integer $ruoka_id haettavan ruoan id
     * @return array löydetyt ainekset
     */
    public static function ainekset($ruoka_id) {
        $query = DB::connection()->prepare('SELECT aines FROM RuokaAines '
                                         . 'WHERE ruoka = :id');

        $query->execute(array('id' => $ruoka_id));

        $rivit = $query->fetchAll();
        $ainekset = array();

        foreach ($rivit as $rivi) {
            $ainekset[] = self::find($rivi['aines']);
        }

        return $ainekset;
    }

    /**
     * Metodi vertaa parametrina annetun listan aineksia toisena parametrina annetun 
     * ruokaan liitettyihin aineksiin ja tilanteen mukaan liittää ruokaan uuden aineksen tai poistaa sen.
     * 
     * @param array $valitut lista aineksia
     * @param integer $ruoka_id
     */
    public function paivitaAinekset($valitut, $ruoka_id) {
        $ainekset = self::ainekset($ruoka_id);

        foreach ($valitut as $v) {
            $valittu = self::findBy($v);

            if (!in_array($valittu, $ainekset)) {
                $valittu->lisaaRuokaAines($ruoka_id);
            }
        }

        foreach ($ainekset as $a) {
            $aines = self::findBy($a->nimi);

            if (!in_array($a->nimi, $valitut)) {
                $aines->poistaRuokaAines($ruoka_id);
            }
        }
    }

    /**
     * Metodi liittää parametrina annettuun ruokaan uuden aineksen 
     * lisäämällä rivin tietokohteiden liitostauluun.
     * 
     * @param integer $ruoka_id
     */
    public function lisaaRuokaAines($ruoka_id) {
        $query = DB::connection()->prepare('INSERT INTO RuokaAines VALUES '
                                         . '(:ruoka, :aines)');

        $query->execute(array('ruoka' => $ruoka_id,
            'aines' => $this->id));
    }

    /**
     * Metodi poistaa parametrina annetusta ruoan ja aineksen liittävän rivin 
     * tietokohteiden välisestä liitostaulusta.
     * 
     * @param integer $ruoka_id
     */
    public function poistaRuokaAines($ruoka_id) {
        $query = DB::connection()->prepare('DELETE FROM RuokaAines '
                                         . 'WHERE ruoka = :ruoka '
                                         . 'AND aines = :aines');

        $query->execute(array('ruoka' => $ruoka_id,
                              'aines' => $this->id));
    }

}
