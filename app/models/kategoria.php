<?php

/**
 * Kategoria-tietokohteen malliluokka
 *
 * @author mari
 */
class Kategoria extends BaseModel {

    public $id, $nimi, $valittu;

    /**
     * Konstruktori, joka kutsuu BaseModel-yliluokan construct-metodia
     * ja määrittää luokan validaatiometodien nimet.
     * 
     * @param type $attributes luokan attribuutit
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
    public function validate_nimi() {
        return parent::validate_string_length($this->nimi, 3, 20);
    }

    /**
     * Metodi hakee tietokohdetta vastaavasta tietokantataulusta kaikki rivit 
     * nimen mukaan aakkostettuna.
     * 
     * @return array löydetyt rivit
     */
    public static function all() {
        $query = DB::connection()->prepare('SELECT * FROM Kategoria ORDER BY nimi');
        $query->execute();
        $rivit = $query->fetchAll();
        $kategoriat = array();

        foreach ($rivit as $rivi) {
            $kategoriat[] = new Kategoria(array('id' => $rivi['id'],
                'nimi' => $rivi['nimi']));
        }

        return $kategoriat;
    }

    /**
     * Metodi hakee tietokohdetta vastaavasta tietokantataulusta rivin 
     * parametrina annetun id:n perusteella.
     * 
     * @param type $id haettavan kohteen id
     * @return Kategoria löydetty rivi 
     */
    public static function find($id) {
        $query = DB::connection()->prepare('SELECT nimi FROM Kategoria '
                . 'WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $rivi = $query->fetch();

        if ($rivi) {
            $kategoria = new Kategoria(array('id' => $id,
                'nimi' => $rivi['nimi']));
        }

        return $kategoria;
    }

    /**
     * Metodi hakee tietokohdetta vastaavasta tietokantataulusta rivin
     * parametrina annetun nimen perusteella.
     * 
     * @param type $nimi haettavan kohteen nimi
     * @return Kategoria löydetty rivi
     */
    public static function findBy($nimi) {
        $query = DB::connection()->prepare('SELECT * FROM Kategoria '
                . 'WHERE nimi = :nimi LIMIT 1');

        $query->execute(array('nimi' => $nimi));
        $rivi = $query->fetch();

        if ($rivi) {
            $kategoria = new Kategoria(array(
                'id' => $rivi['id'],
                'nimi' => $rivi['nimi']));
        }

        return $kategoria;
    }

    /**
     * Metodi hakee Kategoria-tietokantataulusta hakusanan sisältäviä rivejä.
     * 
     * @param type $haku hakusana
     * @return array hakutulokset
     */
    public static function searchBy($haku) {
        $query = DB::connection()->prepare('SELECT * FROM Kategoria WHERE nimi LIKE :haku ');
        $query->execute(array('haku' => '%' . $haku . '%'));
        $rivit = $query->fetchAll();
        $kategoriat = array();

        foreach ($rivit as $rivi) {
            $kategoriat[] = new Kategoria(array('id' => $rivi['id'],
                'nimi' => $rivi['nimi']));
        }

        return $kategoriat;
    }

    /**
     * Metodi tallentaa uuden rivin tietokohdetta vastaavaan tietokantatauluun.
     */
    public function save() {
        $query = DB::connection()->prepare('INSERT INTO Kategoria (nimi) '
                . 'VALUES (:nimi) RETURNING id');

        $query->execute(array('nimi' => $this->nimi));
        $rivi = $query->fetch();
        $this->id = $rivi['id'];
    }

    /**
     * Metodi hakee kaikki parametrina annettuun ruokaan liittyvät kategoriat 
     * näiden kahden tietokohteen välisestä liitostaulusta.
     * 
     * @param type $ruoka_id haettavan ruoan id
     * @return array löydetyt kategoriat
     */
    public static function kategoriat($ruoka_id) {
        $query = DB::connection()->prepare('SELECT kategoria FROM RuokaKategoria '
                . 'WHERE ruoka = :id');

        $query->execute(array('id' => $ruoka_id));

        $rivit = $query->fetchAll();
        $kategoriat = array();


        foreach ($rivit as $rivi) {
            $kategoriat[] = self::find($rivi['kategoria']);
        }

        return $kategoriat;
    }

    /**
     * Metodi vertaa parametrina annetun listan lategorioita toisena parametrina annetun 
     * ruokaan liitettyihin kategorioihin ja tilanteen mukaan liittää ruokaan uuden kategorian tai poistaa sen.
     * 
     * @param type $valitut lista kategorioita
     * @param type $ruoka_id
     */
    public function paivitaKategoriat($valitut, $ruoka_id) {
        $kategoriat = self::kategoriat($ruoka_id);

        foreach ($valitut as $v) {
            $valittu = self::findBy($v);

            if (!in_array($valittu, $kategoriat)) {
                $valittu->lisaaRuokaKategoria($ruoka_id);
            }
        }

        foreach ($kategoriat as $k) {
            $kategoria = self::findBy($k->nimi);

            if (!in_array($k->nimi, $valitut)) {
                $kategoria->poistaRuokaKategoria($ruoka_id);
            }
        }
    }

    /**
     * Metodi liittää parametrina annettuun ruokaan uuden kategorian 
     * lisäämällä rivin tietokohteiden liitostauluun.
     * 
     * @param type $ruoka_id
     */
    public function lisaaRuokaKategoria($ruoka_id) {
        $query = DB::connection()->prepare('INSERT INTO RuokaKategoria VALUES '
                . '(:ruoka, :kategoria)');

        $query->execute(array('ruoka' => $ruoka_id,
            'kategoria' => $this->id));
    }

    /**
     * Metodi poistaa parametrina annetusta ruoan ja kategorian liittävän rivin 
     * tietokohteiden välisestä liitostaulusta.
     * 
     * @param type $ruoka_id
     */
    public function poistaRuokaKategoria($ruoka_id) {
        $query = DB::connection()->prepare('DELETE FROM RuokaKategoria '
                . 'WHERE kategoria = :kategoria '
                . 'AND ruoka = :ruoka');

        $query->execute(array('ruoka' => $ruoka_id,
            'kategoria' => $this->id));
    }

}
