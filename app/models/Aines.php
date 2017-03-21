<?php

/**
 * Description of Aines
 *
 * @author mari
 */
class Aines extends BaseModel{
    
    public $id, $nimi;
    
    public function __construct($attributes) {
        parent::__construct($attributes);
    }
    
    public static function all() {
        $query = DB::connection()->prepare('SELECT * FROM Aines');
        $query->execute();
        $rivit = $query->fetchAll();
        $ainekset = array();

        foreach ($rivit as $rivi) {
            $ainekset[] = new Aines(array(
                'id' => $rivi['id'],
                'nimi' => $rivi['nimi']
            ));
        }
        
        return $ainekset;
    }
    
    public function find($id) {
        $query = DB::connection()->prepare('SELECT * FROM Aines'
                                            . 'WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $rivi = $query->fetch();
        
        if ($rivi){
            $aines = new Aines(array(
                'id' => $rivi['id'],
                'nimi' => $rivi['nimi']
            ));
        }
        
        return $aines;
        
    }
}
