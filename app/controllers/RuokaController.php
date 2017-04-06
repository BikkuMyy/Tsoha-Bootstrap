<?php

/**
 * Description of RuokaController
 *
 * @author mari
 */
class RuokaController extends BaseController {

    public static function index() {
        $ruoat = Ruoka::all(parent::get_user_logged_in()->id);
        View::make('ruoka/ruokalajit.html', array('ruoat' => $ruoat));
    }

    public static function single($id) {
        $ruoka = Ruoka::find($id);
        View::make('ruoka/ruoka.html', array('ruoka' => $ruoka));
    }

    public static function create() {
        $kategoriat = Kategoria::all();
        $ainekset = Aines::all();
        View::make('ruoka/new.html', array('kategoriat' => $kategoriat,'ainekset' => $ainekset));
    }

    public static function store() {
        $params = $_POST;
        $user = parent::get_user_logged_in();

        $kategoriat = self::valitut($params['valitutKategoriat']);
        $ainekset = self::valitut($params['valitutAinekset']);

        $ruoka = new Ruoka(array('nimi' => $params ['nimi'],
            'kommentti' => $params ['kommentti'],
            'kayttaja' => $user->id,
            'kategoriat' => $kategoriat,
            'ainekset' => $ainekset));
        

        $errors = $ruoka->errors();
        if (count($errors) > 0) {
//            $valitutKategoriat = self::luoValittujenLista($kategoriat, Kategoria::all());
//            $valitutAinekset = self::luoValittujenLista($ainekset, Aines::all());
            $valitutAinekset = Aines::all();
            $valitutKategoriat = Kategoria::all();
            
            View::make('ruoka/new.html', array('errors' => $errors, 
                                               'ruoka' => $ruoka, 
                                               'ainekset' => $valitutAinekset,
                                               'kategoriat' => $valitutKategoriat));
        }
        
        $ruoka->save();
        Redirect::to('/ruokalajit/' . $ruoka->id, array('message' => 'Ruoka ' . $ruoka->nimi 
                                                      . ' lisätty arkistoosi!'));
    }

    public static function modify($id) {
        $ruoka = Ruoka::find($id);

        $kategoriat = self::luoValittujenLista(Kategoria::kategoriat($id), Kategoria::all());
        $ainekset = self::luoValittujenLista(Aines::ainekset($id), Aines::all());

        View::make('ruoka/modify.html', array('ruoka' => $ruoka,
            'kategoriat' => $kategoriat,
            'ainekset' => $ainekset));
    }

    public function update($id) {
        $params = $_POST;
        $user = parent::get_user_logged_in();

        $kategoriat = self::valitut($params['valitutKategoriat']);
        $ainekset = self::valitut($params['valitutAinekset']);

        $ruoka = new Ruoka(array('id' => $id,
            'nimi' => $params ['nimi'],
            'kommentti' => $params ['kommentti'],
            'kayttaja' => $user->id,
            'kategoriat' => $kategoriat,
            'ainekset' => $ainekset));

        $ruoka->update();

        Redirect::to('/ruokalajit/' . $ruoka->id, array('message' => 'Ruoan tiedot päivitetty'));
    }

    public static function delete($id) {
        $ruoka = Ruoka::find($id);
        View::make('ruoka/remove.html', array('ruoka' => $ruoka));
    }

    public static function remove($id) {
        $ruoka = new Ruoka(array('id' => $id));
        $ruoka->remove();

        Redirect::to('/ruokalajit', array('message' => 'Ruokalaji poistettu.'));
    }

    public function luoValittujenLista($valitut, $kaikki) {
        $valittujenLista = Array();

        foreach ($kaikki as $k) {

            if (empty($valitut)) {
                $valittujenLista[] = new Kategoria(array('nimi' => $k->nimi, 'valittu' => false));
                continue;
            }

            foreach ($valitut as $v) {

                if ($k->id == $v->id) {
                    $valittujenLista[] = new Kategoria(array('nimi' => $k->nimi, 'valittu' => true));
                } else {
                    $valittujenLista[] = new Kategoria(array('nimi' => $k->nimi, 'valittu' => false));
                }
            }
        }
        //Kint::dump($valittujenLista);
        return $valittujenLista;
    }

    public static function valitut($valitut) {
        if (isset($valitut)) {
            return $valitut;
        } else {
            return array();
        }
    }

}
