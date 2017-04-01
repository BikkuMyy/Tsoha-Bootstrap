<?php

/**
 * Description of Aines
 *
 * @author mari
 */
class Aines extends BaseModel {

    public $id, $nimi;

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

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

    public static function find($id) {
        $query = DB::connection()->prepare('SELECT nimi FROM Aines '
                                         . 'WHERE id = :id LIMIT 1');
        
        $query->execute(array('id' => $id));
        $rivi = $query->fetch();

        if ($rivi) {
            $aines = new Aines(array('id'=> $id, 'nimi' => $rivi['nimi']));
        }

        return $aines;
    }
    
    public static function findBy($nimi){
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
    
    public function save(){
        $query = DB::connection()->prepare('INSERT INTO Aines (nimi) '
                                            . 'VALUES (:nimi) RETURNING id');
        
        $query->execute(array('nimi' => $this->nimi));
        $rivi = $query->fetch();
        $this->id = $rivi['id'];
    }
    
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
    
    public function lisaaRuokaAines($ruoka_id){
        $query = DB::connection()->prepare('INSERT INTO RuokaAines VALUES '
                                            . '(:ruoka, :aines)');
        $query->execute(array('ruoka' => $ruoka_id,
                              'aines' => $this->id));
    }
    
    public function poistaRuokaAines($ruoka_id){
        $query = DB::connection()->prepare('DELETE FROM RuokaAines '
                                         . 'WHERE ruoka = : ruoka '
                                         . 'AND aines = :aines');
        $query->execute(array('ruoka' => $ruoka_id, 
                              'aines' => $this->id));
    }

}
