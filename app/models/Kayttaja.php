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
        $this->validators = array('validate_tunnus', 'validate_salasana');
    }
    
    public function validate_tunnus(){
        return parent::validate_string_length($this->kayttajatunnus, 3, 20);
    }
    
    public function validate_salasana(){
        return parent::validate_string_length($this->salasana, 5, 20);
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
    
    public function updateTunnus(){
        $query = DB::connection()->prepare('UPDATE Kayttaja SET kayttajatunnus = :tunnus');
        $query->execute(array('tunnus' => $this->kayttajatunnus));
    }
    
    public function updateSalasana(){
        $query = DB::connection()->prepare('UPDATE Kayttaja SET salasana = :salasana');
        $query->execute(array('salasana' => $this->salasana));
    }
    
    public function remove(){
        $query = DB::connection()->prepare('DELETE FROM Kayttaja WHERE id = :id');
        $query->execute(array('id' => $this->id));
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
    
    public function onkoKaytossa(){
        $query=DB::connection()->prepare('SELECT kayttajatunnus FROM Kayttaja '
                                       . 'WHERE kayttajatunnus = :tunnus LIMIT 1');
        
        $query->execute(array('tunnus' => $this->kayttajatunnus));
        $rivi = $query->fetch();
        
        if($rivi){
            return true;
        }
        return false;   
    }
    
    public function tarkistaSalasana($salasana){
        $query=DB::connection()->prepare('SELECT salasana FROM Kayttaja '
                                       . 'WHERE id = :id '
                                       . 'AND kayttajatunnus = :kayttajatunnus '
                                       . 'AND salasana = :salasana');
        
        $query->execute(array('id' => $this->id,
                              'kayttajatunnus' => $this->kayttajatunnus,
                              'salasana' => $salasana));
        $rivi = $query->fetch();
        if($rivi){
            return true;
        }
        return false;
    }
    
    
}
