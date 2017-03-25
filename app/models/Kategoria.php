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
            $kategoriat[] = new Kategoria(array(
                'id' => $rivi['id'],
                'nimi' => $rivi['nimi']
            ));
        }

        return $kategoriat;
    }

    public function find($id) {
        $query = DB::connection()->prepare('SELECT nimi FROM Kategoria '
                . 'WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $rivi = $query->fetch();

        if ($rivi) {
            $kategoria = $rivi['nimi'];
        }

        return $kategoria;
    }

    public static function kategoriat($ruoka_id) {
        $query = DB::connection()->prepare('SELECT * FROM RuokaKategoria '
                . 'WHERE ruoka = :id LIMIT 1');

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

}
