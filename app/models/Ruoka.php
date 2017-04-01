<?php

/**
 * Description of Ruoka
 *
 * @author mari
 */
class Ruoka extends BaseModel {

    public $id, $nimi, $kayttokerrat, $kommentti, $kayttaja;
    public $kategoriat = array();
    public $ainekset = array();
    

    public function __construct($attributes) {
        parent::__construct($attributes);
        //$this->$validators = array('validate_nimi', 'validate_');
    }

    public static function all($kayttaja) {
        $query = DB::connection()->prepare('SELECT * FROM Ruoka WHERE kayttaja = :kayttaja');
        $query->execute(array('kayttaja' => $kayttaja));
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
                'kategoriat' => Kategoria::kategoriat($id),
                'ainekset' => Aines::ainekset($id)
            ));
        }
        
        return $ruoka;
    }
    
    public function save(){
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
    
    public function update(){
        $query = DB::connection()->prepare('UPDATE Ruoka SET nimi = :nimi, kommentti = :kommentti');
        $query->execute(array('nimi' => $this->nimi,
                              'kommentti' => $this->kommentti));
        
        //Kategorioiden ja ainesten päivitys
    }
    
    public static function remove(){
        $kategoriat = Kategoria::kategoriat($this->id);
        foreach ($kategoriat as $k){
            $k->poistaRuokaKategoria($this->id);
        }
        $ainekset = Aines::ainekset($this->id);
        foreach ($ainekset as $a){
            $a->poistaRuokaAines($this->id);
        }
    }
    
    public function lisaaKategoriat(){
        foreach($this->kategoriat as $k){
            $kategoria = Kategoria::findBy($k);
            $kategoria->lisaaRuokaKategoria($this->id);
        }
    }
    
    public function lisaaAinekset(){
        foreach($this->ainekset as $a){
            $aines = Aines::findBy($a);
            $aines->lisaaRuokaAines($this->id);
        }
    }
}
