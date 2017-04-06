<?php
/**
 * Description of Kayttaja
 *
 * @author mari
 */
class Kayttaja extends BaseModel {
    
    public $id, $kayttajatunnus, $salasana;


    public function __construct($attributes) {
        parent::__construct($attributes);
        //$this->$validators('validate_nimi', 'validate_salasana');
    }
    
    public function validate_nimi(){
        parent::validate_string_length($this->nimi, 3);
    }
    
    public function validate_salasana(){
        parent::validate_string_length($this->nimi, 5);
    }

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
    
    public static function find($id){
        $query = DB::connection()->prepare('SELECT * FROM Kayttaja '
                                            . 'WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $rivi = $query->fetch();
        
        if ($rivi){
            $kayttaja = new Kayttaja(array(
                'id' => $rivi['id'],
                'kayttajatunnus' => $rivi['kayttajatunnus'],
                'salasana' => $rivi['salasana']
            ));
        }
        
        return $kayttaja;
    }
    
    public function save(){
        $query = DB::connection()->prepare('INSERT INTO Kayttaja '
                                           . '(kayttajatunnus, salasana) '
                                           . 'VALUES (:kayttajatunnus, :salasana) '
                                           . 'RETURNING id');
        
        $query->execute(array('kayttajatunnus' => $this->kayttajatunnus,
                              'salasana' => $this->salasana));
        
        $rivi = $query->fetch();
        $this->id = $rivi['id'];
    }
    
    public function update(){
        //käyttäjän tietojen päivittäminen (käyttäjätunnuksen tai salasanan vaihto)
    }
    
    public static function authenticate($username, $password){
        $query = DB::connection()->prepare('SELECT * FROM Kayttaja '
                                . 'WHERE kayttajatunnus = :username '
                                . 'AND salasana = :password LIMIT 1');
        
        $query->execute(array('username' => $username, 'password' => $password));
        $rivi = $query->fetch();
        
        if($rivi){
            return new Kayttaja(array(
                'id' => $rivi['id'],
                'kayttajatunnus' => $rivi['kayttajatunnus']));
        } else {
            return NULL;
        }
    }
    
    public static function onkoKaytossa($tunnus){
        $query=DB::connection()->prepare('SELECT kayttajatunnus FROM Kayttaja '
                                       . 'WHERE kayttajatunnus = :tunnus LIMIT 1');
        
        $query->execute(array('tunnus' => $tunnus));
        $rivi = $query->fetch();
        
        if($rivi){
            return true;
        }
        return false;
        
    }
    
    
}
