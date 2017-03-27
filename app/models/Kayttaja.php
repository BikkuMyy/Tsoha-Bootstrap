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
    
    public static function save(){
        $query = DB::connection()->prepare('INSERT INTO Kayttaja '
                                           . '(kayttajatunnus, salasana) '
                                           . 'VALUES (:kayttajatunnus, salasana) '
                                           . 'RETURNING id');
        
        $query->execute(array('kayttajatunnus' => $this->kayttajatunnus,
                              'salasana' => $this->salasana));
        
        $rivi = $query.fetch();
        $this->id = $rivi['id'];
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
                'kayttajatunnus' => $rivi['kayttajatunnus']
            ));
        } else {
            return NULL;
        }
    }
}
