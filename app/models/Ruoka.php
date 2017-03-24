<?php

/**
 * Description of Ruoka
 *
 * @author mari
 */
class Ruoka extends BaseModel {

    public $id, $nimi, $kayttokerrat, $kommentti, $kayttaja;
    public $kategoriat;
    public $ainekset;
    

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    public static function all() {
        $query = DB::connection()->prepare('SELECT * FROM Ruoka');
        $query->execute();
        $rivit = $query->fetchAll();
        $ruoat = array();

        foreach ($rivit as $rivi) {
            $ruoka_id = $rivi['id'];
            $ruoat[] = new Ruoka(array('id' => $ruoka_id,
                'nimi' => $rivi['nimi'],
                'kayttokerrat' => $rivi['kayttokerrat'],
                'kommentti' => $rivi['kommentti'],
                'kayttaja' => $rivi['kayttaja'],
                'kategoriat' => self::kategoriat($ruoka_id),
                'ainekset' => self::ainekset($ruoka_id)
                ));
        }
        
        return $ruoat;
    }
    
    public static function find($id){
        $query = DB::connection()->prepare('SELECT * FROM Ruoka '
                                            . 'WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $rivi = $query->fetch();
        
        if ($rivi){
            $ruoka = new Ruoka(array(
                'id' => $rivi['id'],
                'nimi' => $rivi['nimi'],
                'kayttokerrat' => $rivi['kayttokerrat'],
                'kommentti' => $rivi['kommentti'],
                'kayttaja' => $rivi['kayttaja'],
                'kategoriat' => self::kategoriat($id),
                'ainekset' => self::ainekset($id)
            ));
        }
        
        return $ruoka;
    }
    
    public static function save(){
        $query = DB::connection()->prepare('INSERT INTO Ruoka '
                                           . '(nimi, kayttokerrat, kommentti, kayttaja) '
                                           . 'VALUES (:nimi, :kayttokerrat, :kommentti, :kayttaja)');
        
        $query->execute(array('nimi' => $this->nimi, 
                              'kayttokerrat' => $this ->kayttokerrat, 
                              'kommentti' => $this->kommentti,
                              'kayttaja' => $this->kayttaja));
        
        $rivi = $query.fetch();
        $this->id = $rivi['id'];
    }
    
    //metodit kategorioiden ja aineksien hakemista varten
    public static function kategoriat($id){
        return Kategoria::kategoriat($id);
        
    }
    
    
    public static function ainekset($id){
        return Aines::ainekset($id);
    }
}
