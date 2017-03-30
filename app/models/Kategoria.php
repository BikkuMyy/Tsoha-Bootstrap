<?php

/**
 * Description of Kategoria
 *
 * @author mari
 */
class Kategoria extends BaseModel {

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
            $kategoriat[] = $rivi['nimi'];
        }

        return $kategoriat;
    }

    public static function find($id) {
        $query = DB::connection()->prepare('SELECT nimi FROM Kategoria '
                . 'WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $rivi = $query->fetch();

        if ($rivi) {
            $kategoria = $rivi['nimi'];
        }

        return $kategoria;
    }
    
    public static function findBy($nimi) {
        $query = DB::connection()->prepare('SELECT * FROM Kategoria '
                . 'WHERE nimi = :nimi LIMIT 1');
        $query->execute(array('nimi' => $nimi));
        $rivi = $query->fetch();

        if ($rivi) {
            $kategoria = new Kategoria(array(
                        'id' => $rivi['id'],
                        'nimi' => $rivi['nimi']));
        }

        return $kategoria;
    }
    
    public function save(){
        $query = DB::connection()->prepare('INSERT INTO Kategoria (nimi) '
                                            . 'VALUES (:nimi) RETURNING id');
        
        $query->execute(array('nimi' => $this->nimi));
        $rivi = $query->fetch();
        $this->id = $rivi['id'];
                
    }

    public static function kategoriat($ruoka_id) {
        $query = DB::connection()->prepare('SELECT * FROM RuokaKategoria '
                . 'WHERE ruoka = :id');

        $query->execute(array('id' => $ruoka_id));

        $rivit = $query->fetchAll();
        $kategoriat = array();


        foreach ($rivit as $rivi) {
            $kategoriat[] = self::find($rivi['kategoria']);
        }
        
        if (empty($kategoriat)) {
            $kategoriat[] = "-";
        }

        return $kategoriat;
    }
    
    public function lisaaRuokaKategoria($ruoka_id){
        $query = DB::connection()->prepare('INSERT INTO RuokaKategoria VALUES '
                                            . '(:ruoka, :kategoria)');
        $query->execute(array('ruoka' => $ruoka_id,
                              'kategoria' => $this->id));
        
    }

}
