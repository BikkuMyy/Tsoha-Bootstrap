<?php

/**
 * Ruoka-tietokohteen malliluokka
 *
 * @author mari
 */
class Ruoka extends BaseModel {

    public $id, $nimi, $kayttokerrat, $kommentti, $kayttaja;
    public $kategoriat = array();
    public $ainekset = array();

    /**
     * Konstruktori, joka kutsuu BaseModel-yliluokan construct-metodia
     * ja määrittää luokanvalidaatiometodien nimet.
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
        return parent::validate_string_length($this->nimi, 3, 25);
    }

    //tarviiko kommentti validoinnin?
//    public function validate_kommentti(){
//        return parent::validate_string_length($this->kommentti, $min, $max);
//    }

    /**
     * Metodi hakee tietokohdetta vastaavasta tietokantataulusta kaikki rivit,
     * joiden kayttaja-viiteavain vastaa parametrina annettua avainta.
     * 
     * @param type $kayttaja_id
     * @return array löydetyt rivit
     */
    public static function all($kayttaja_id) {
        $query = DB::connection()->prepare('SELECT * FROM Ruoka '
                . 'WHERE kayttaja = :kayttaja');

        $query->execute(array('kayttaja' => $kayttaja_id));
        $rivit = $query->fetchAll();
        $ruoat = array();

        foreach ($rivit as $rivi) {
            $ruoka_id = $rivi['id'];
            $ruoat[] = new Ruoka(array('id' => $ruoka_id,
                'nimi' => $rivi['nimi'],
                'kayttokerrat' => $rivi['kayttokerrat'],
                'kommentti' => $rivi['kommentti'],
                'kayttaja' => $rivi['kayttaja'],
                'kategoriat' => Kategoria::kategoriat($ruoka_id),
                'ainekset' => Aines::ainekset($ruoka_id)));
        }

        return $ruoat;
    }

    /**
     * Metodi hakee tietokohdetta vastaavasta tietokantataulusta rivin 
     * parametrina annetun id:n perusteella.
     * 
     * @param type $id
     * @return Ruoka
     */
    public static function find($id) {
        $query = DB::connection()->prepare('SELECT * FROM Ruoka '
                . 'WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $rivi = $query->fetch();

        if ($rivi) {
            $ruoka = new Ruoka(array(
                'id' => $rivi['id'],
                'nimi' => $rivi['nimi'],
                'kayttokerrat' => $rivi['kayttokerrat'],
                'kommentti' => $rivi['kommentti'],
                'kayttaja' => $rivi['kayttaja'],
                'kategoriat' => Kategoria::kategoriat($id),
                'ainekset' => Aines::ainekset($id)
            ));
        }

        return $ruoka;
    }

    /**
     * Metodi hakee parametrina saamansa hakusanan sisältäviä rivejä
     * Ruoka-tietokantataulusta.
     * 
     * @param type $haku hakusana
     * @param type $kayttaja kayttaja
     * @return array haun tulokset
     */
    public static function searchBy($haku, $kayttaja) {
        if ($kayttaja != null) {
            $query = DB::connection()->prepare('SELECT * FROM Ruoka WHERE kayttaja = :kayttaja '
                                             . 'AND nimi LIKE :haku');
            $query->execute(array('kayttaja' => $kayttaja, 'haku' => '%' . $haku . '%'));
        } else {
            $query = DB::connection()->prepare('SELECT * FROM Ruoka WHERE nimi LIKE :haku');
            $query->execute(array('haku' => '%' . $haku . '%'));
        }
        
        $rivit = $query->fetchAll();
        $tulokset = array();
        
        foreach ($rivit as $rivi) {
            $id = $rivi['id'];
            $tulokset[] = new Ruoka(array('id' => $id,'nimi' => $rivi['nimi'],
                            'kommentti' => $rivi['kommentti'],
                            'kayttaja' => $rivi['kayttaja'],
                            'kategoriat' => Kategoria::kategoriat($id),
                            'ainekset' => Aines::ainekset($id)
            ));
        }
        return $tulokset;
    }
    
    public static function seachByCategory($id){
        //haetaan kaikki ruoat, joilla on tietty kategoria
        //vaatii varmaan liitostaulun...
//        $query=DB::connection()->prepare('SELECT * FROM Ruoka WHERE ')
    }
    
    public static function seachByIngredient($id){
        //haetaan kaikki ruoat, joilla on tietty aines
        //vaatii varmaan liitostaulun...
    }
    
    
    /**
     * Metodi tallentaa uuden rivin tietokohdetta vastaavaan tietokantatauluun.
     */
    public function save() {

        $query = DB::connection()->prepare('INSERT INTO Ruoka '
                . '(nimi, kayttokerrat, kommentti, kayttaja) '
                . 'VALUES (:nimi, 0, :kommentti, :kayttaja) '
                . 'RETURNING id');

        $query->execute(array('nimi' => $this->nimi,
            'kommentti' => $this->kommentti,
            'kayttaja' => $this->kayttaja));

        $rivi = $query->fetch();
        $this->id = $rivi['id'];

        self::lisaaKategoriat();
        self::lisaaAinekset();
    }

    /**
     * Metodi päivittää tietokohdetta vastaavan tietokantataulun rivin nimen ja kommentin
     * sekä kutsuu siihen liittyvien liitostauluja päivittäviä metodeja.
     */
    public function update() {
        $query = DB::connection()->prepare('UPDATE Ruoka SET nimi = :nimi, '
                . 'kommentti = :kommentti '
                . 'WHERE id = :id');

        $query->execute(array('id' => $this->id,
            'nimi' => $this->nimi,
            'kommentti' => $this->kommentti));

        Kategoria::paivitaKategoriat($this->kategoriat, $this->id);

        Aines::paivitaAinekset($this->ainekset, $this->id);
    }

    /**
     * Metodi poistaa rivin tietokohdetta vastaavasta tietokantataulusta 
     * ja kaikki siihen liittyvät rivit liitostauluista
     */
    public function remove() {
        $kategoriat = Kategoria::kategoriat($this->id);
        foreach ($kategoriat as $k) {
            $k->poistaRuokaKategoria($this->id);
        }
        $ainekset = Aines::ainekset($this->id);
        foreach ($ainekset as $a) {
            $a->poistaRuokaAines($this->id);
        }

        $query = DB::connection()->prepare('DELETE FROM Ruoka WHERE id = :id');
        $query->execute(array('id' => $this->id));
    }

    /**
     * Metodi lisää rivejä ruoan ja kategorian väliseen liitostauluun.
     */
    public function lisaaKategoriat() {
        foreach ($this->kategoriat as $k) {
            $kategoria = Kategoria::findBy($k);
            $kategoria->lisaaRuokaKategoria($this->id);
        }
    }

    /**
     * Metodi lisää rivejä ruoan ja aineksen väliseen liitostauluun.
     */
    public function lisaaAinekset() {
        foreach ($this->ainekset as $a) {
            $aines = Aines::findBy($a);
            $aines->lisaaRuokaAines($this->id);
        }
    }

}
