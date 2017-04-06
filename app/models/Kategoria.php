<?php

/**
 * Description of Kategoria
 *
 * @author mari
 */
class Kategoria extends BaseModel {

    public $id, $nimi, $valittu;

    public function __construct($attributes) {
        parent::__construct($attributes);
    }
    
    public function validate_nimi(){
        parent::validate_string_length($this->nimi, 3, 20);
        
    }

    public static function all() {
        $query = DB::connection()->prepare('SELECT * FROM Kategoria ORDER BY nimi');
        $query->execute();
        $rivit = $query->fetchAll();
        $kategoriat = array();

        foreach ($rivit as $rivi) {
            $kategoriat[] = new Kategoria(array('id' => $rivi['id'], 
                                                'nimi' => $rivi['nimi']));
        }

        return $kategoriat;
    }

    public static function find($id) {
        $query = DB::connection()->prepare('SELECT nimi FROM Kategoria '
                                         . 'WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $rivi = $query->fetch();

        if ($rivi) {
            $kategoria = new Kategoria(array('id' => $id,
                                             'nimi' => $rivi['nimi']));
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

    public function save() {
        $query = DB::connection()->prepare('INSERT INTO Kategoria (nimi) '
                                         . 'VALUES (:nimi) RETURNING id');

        $query->execute(array('nimi' => $this->nimi));
        $rivi = $query->fetch();
        $this->id = $rivi['id'];
    }

    public static function kategoriat($ruoka_id) {
        $query = DB::connection()->prepare('SELECT kategoria FROM RuokaKategoria '
                                            . 'WHERE ruoka = :id');

        $query->execute(array('id' => $ruoka_id));

        $rivit = $query->fetchAll();
        $kategoriat = array();


        foreach ($rivit as $rivi) {
            $kategoriat[] = self::find($rivi['kategoria']);
        }

        return $kategoriat;
    }

    public static function paivitaKategoriat($valitut, $ruoka_id) {
        $kategoriat = self::kategoriat($ruoka_id);
        
        foreach ($valitut as $v) {
            $valittu = self::findBy($v);
            
            if(!in_array($valittu, $kategoriat)){
                $valittu->lisaaRuokaKategoria($ruoka_id);
            }
        }
        
        foreach ($kategoriat as $k){
            $kategoria = self::findBy($k);
            
            if(!in_array($kategoria, $valitut)){
                $kategoria->poistaRuokaKategoria($ruoka_id);
            }
        }
    }

    public function lisaaRuokaKategoria($ruoka_id) {
        $query = DB::connection()->prepare('INSERT INTO RuokaKategoria VALUES '
                . '(:ruoka, :kategoria)');
        
        $query->execute(array('ruoka' => $ruoka_id,
                              'kategoria' => $this->id));
    }

    public function poistaRuokaKategoria($ruoka_id){
        $query = DB::connection()->prepare('DELETE FROM RuokaKategoria '
                                         . 'WHERE kategoria = :kategoria '
                                         . 'AND ruoka = :ruoka');
        
        $query->execute(array('ruoka' => $ruoka_id, 
                              'kategoria' => $this->id));
    }

}
