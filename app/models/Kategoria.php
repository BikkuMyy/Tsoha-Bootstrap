<?php
/**
 * Description of Kategoria
 *
 * @author mari
 */
class Kategoria extends BaseModel{
    
    public $id, $nimi;
    
    public function __construct($attributes) {
        parent::__construct($attributes);
    }
    
    public static function all() {
        $query = DB::connection()->prepare('SELECT * FROM Kategoria');
        $query->execute();
        $rivit = $query->fetchAll();
        $kategoriat = array();

        foreach ($rivit as $rivi) {
            $kategoriat[] = new Kategoria(array(
                'id' => $rivi['id'],
                'nimi' => $rivi['nimi']
            ));
        }
        
        return $kategoriat;
    }
    
    public function find($id) {
        $query = DB::connection()->prepare('SELECT * FROM Kategoria '
                                            . 'WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $rivi = $query->fetch();
        
        if ($rivi){
            $kategoria = new Kategoria(array(
                'id' => $rivi['id'],
                'nimi' => $rivi['nimi']
            ));
        }
        
        return $kategoria;
        
    }
}
