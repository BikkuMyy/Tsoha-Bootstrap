<?php

/**
 * Kayttaja-tietokohteen malliluokka
 *
 * @author mari
 */
class Kayttaja extends BaseModel {

    public $id, $kayttajatunnus, $salasana;

    /**
     * Konstruktori, joka kutsuu BaseModel-yliluokan construct-metodia
     * ja määrittää luokanvalidaatiometodien nimet.
     * 
     * @param array $attributes luokan attribuutit
     */
    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_tunnus', 'validate_salasana');
    }

    /**
     * Metodi kutsuu BaseModel-yliluokan validointimetodia 
     * ja palauttaa mahdolliset virheet.
     * 
     * @return array validoinnissa huomatut virheet
     */
    public function validate_tunnus() {
        return parent::validate_string_length($this->kayttajatunnus, 3, 20);
    }

    /**
     * Metodi kutsuu BaseModel-yliluokan validointimetodia 
     * ja palauttaa mahdolliset virheet.
     * 
     * @return array validoinnissa huomatut virheet
     */
    public function validate_salasana() {
        return parent::validate_string_length($this->salasana, 5, 20);
    }

    /**
     * Metodi hakee tietokohdetta vastaavasta tietokantataulusta kaikki rivit 
     * nimen mukaan aakkostettuna.
     * 
     * @return array löydetyt rivit
     */
    public static function all() {
        $query = DB::connection()->prepare('SELECT * FROM Kayttaja');
        $query->execute();
        $rivit = $query->fetchAll();
        $kayttajat = array();

        foreach ($rivit as $rivi) {
            $kayttajat[] = new Kayttaja(array(
                'id' => $rivi['id'],
                'kayttajatunnus' => $rivi['kayttajatunnus'],
                'salasana' => $rivi['salasana']
            ));
        }

        return $kayttajat;
    }

    /**
     * Metodi hakee tietokohdetta vastaavasta tietokantataulusta rivin 
     * parametrina annetun id:n perusteella.
     * 
     * @param integer $id haettavan kohteen id
     * @return Kayttaja löydetty rivi
     */
    public static function find($id) {
        $query = DB::connection()->prepare('SELECT * FROM Kayttaja '
                                         . 'WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $rivi = $query->fetch();

        if ($rivi) {
            $kayttaja = new Kayttaja(array(
                'id' => $rivi['id'],
                'kayttajatunnus' => $rivi['kayttajatunnus'],
                'salasana' => $rivi['salasana']
            ));
        }

        return $kayttaja;
    }
    
    /**
     * Metodi tallentaa uuden rivin tietokohdetta vastaavaan tietokantatauluun.
     */
    public function save() {
        $query = DB::connection()->prepare('INSERT INTO Kayttaja '
                . '(kayttajatunnus, salasana) '
                . 'VALUES (:kayttajatunnus, :salasana) '
                . 'RETURNING id');

        $query->execute(array('kayttajatunnus' => $this->kayttajatunnus,
            'salasana' => $this->salasana));

        $rivi = $query->fetch();
        $this->id = $rivi['id'];
    }

    /**
     * Metodi poistaa käyttäjän tietokannasta kutsuttuaan poistometodia 
     * kaikille siihen liitetyille ruokalajeille..
     */
    public function remove() {
        $ruoat = Ruoka::all($this->id);
        foreach ($ruoat as $ruoka){
            $ruoka->remove();
        }
        
        $query = DB::connection()->prepare('DELETE FROM Kayttaja WHERE id = :id');
        $query->execute(array('id' => $this->id));
    }

    /**
     * Metodi hakee rivin tietokohdetta vastaavasta tietokantataulusta annettujen parametrien perusteella
     * ja palautttaa sitä vastaavan Kayttaja-olion tai null, jos riviä ei löytynyt.
     * 
     * @param string $username haettava käyttäjätunnus
     * @param string $password haettava salasana
     * @return Kayttaja tai null
     */
    public static function authenticate($username, $password) {
        $query = DB::connection()->prepare('SELECT * FROM Kayttaja '
                . 'WHERE kayttajatunnus = :username '
                . 'AND salasana = :password LIMIT 1');

        $query->execute(array('username' => $username, 'password' => $password));
        $rivi = $query->fetch();

        if ($rivi) {
            return new Kayttaja(array(
                'id' => $rivi['id'],
                'kayttajatunnus' => $rivi['kayttajatunnus']));
        } else {
            return NULL;
        }
    }

    /**
     * Metodi tarkistaa, löytyykö tietokodetta vastaavasta tietokantataulusta 
     * parametrina annettua käyttäjätunnusta ja palauttaa true/false sen mukaisesti.
     * 
     * @param string $tunnus
     * @return boolean true, jos löytyy ja false jos ei löydy
     */
    public static function onkoKaytossa($tunnus) {
        $query = DB::connection()->prepare('SELECT kayttajatunnus FROM Kayttaja '
                                         . 'WHERE kayttajatunnus = :tunnus LIMIT 1');

        $query->execute(array('tunnus' => $tunnus));
        $rivi = $query->fetch();

        if ($rivi) {
            return true;
        }
        return false;
    }

    /**
     * Metodi päivittää tietokohdetta vastaavan tietokantataulun rivin arvon käyttäjätunnus,
     * jos taulu ei vielä sisällä vastaavaa käyttäjätunnusta.
     * 
     * @return boolean false, jos tunnus on olemassa ja true jos päivitys onnistui
     */
    public function tarkistaJaPaivitaTunnus() {
        if (self::onkoKaytossa($this->kayttajatunnus)) {
            return false;
        }

        $query = DB::connection()->prepare('UPDATE Kayttaja SET kayttajatunnus = :tunnus '
                                         . 'WHERE id = :id');
        
        $query->execute(array('id' => $this->id,
                              'tunnus' => $this->kayttajatunnus));

        return true;
    }

    /**
     * Metodi etsii tietokohdetta vastaavasta tietokantataulusta riviä 
     * parametrina annetun salasanan sekä käyttäjätunnuksen ja id:n perusteella 
     * ja palauttaa totuusarvon sen mukaisesti.
     * 
     * @param string $salasana haettava salasana
     * @return boolean true, jos rivi löytyi ja false jos ei
     */
    public function tarkistaSalasana($salasana) {
        $query = DB::connection()->prepare('SELECT salasana FROM Kayttaja '
                                         . 'WHERE id = :id '
                                         . 'AND kayttajatunnus = :kayttajatunnus '
                                         . 'AND salasana = :salasana');

        $query->execute(array('id' => $this->id,
                              'kayttajatunnus' => $this->kayttajatunnus,
                              'salasana' => $salasana));
        
        $rivi = $query->fetch();
        if ($rivi) {
            return true;
        }
        return false;
    }
    
    /**
     * Metodi päivittää tietokohdetta vastaavan tietokantataulun rivin salasanan.
     * 
     */
    public function updateSalasana() {
        $query = DB::connection()->prepare('UPDATE Kayttaja SET salasana = :salasana '
                                         . 'WHERE id = :id '
                                         . 'AND kayttajatunnus = :tunnus');
        
        $query->execute(array('id' => $this->id,
                              'tunnus' => $this->kayttajatunnus,
                              'salasana' => $this->salasana));
    }

}
