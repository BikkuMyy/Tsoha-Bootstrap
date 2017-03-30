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
    
    public static function create(){
        $kategoriat = Kategoria::all();
        $ainekset = Aines::all();
        View::make('ruoka/new.html', array('kategoriat' => $kategoriat, 
                                           'ainekset'=> $ainekset));
    }

    public static function store() {
        $params = $_POST;
        $user = parent::get_user_logged_in();
        $kategoriat = $params['valitutKategoriat'];
        $ainekset = $params['valitutAinekset'];
        
        $ruoka = new Ruoka(array(
            'nimi' => $params ['nimi'],
            'kommentti' => $params ['kommentti'],
            'kayttaja' => $user->id,
            'kategoriat' => $kategoriat,
            'ainekset' => $ainekset));
        
        $ruoka->save();
        
        Redirect::to('/ruokalajit/' . $ruoka->id, array('message' => 'Ruoka ' . $params ['nimi'] . ' lisätty arkistoosi!'));
    }
    
    public static function modify($id) {
        $ruoka = Ruoka::find($id);
        $kategoriat = Kategoria::all();
        $valitutKategoriat = Kategoria::kategoriat($id);
        $ainekset = Aines::all();
        $valitutAinekset = Aines::ainekset($id);
        
        View::make('ruoka/modify.html', array('ruoka' => $ruoka, 
                                        'kategoriat' => $kategoriat,
                                        'valitutKategoriat' => $valitutKategoriat,
                                        'ainekset' => $ainekset,
                                        'valitutAinekset' => $valitutAinekset));
    }
    
    public function update($id){
        $params = $_POST;
        $user = parent::get_user_logged_in();
        $kategoriat = $params['valitutKategoriat'];
        $ainekset = $params['valitutAinekset'];
        
        $ruoka = new Ruoka(array(
            'id' => $id,
            'nimi' => $params ['nimi'],
            'kommentti' => $params ['kommentti'],
            'kayttaja' => $user->id,
            'kategoriat' => $kategoriat,
            'ainekset' => $ainekset));
        
        $ruoka->update();
        
        Redirect::to('/ruokalajit/' . $ruoka->id, array('message' => 'Ruoan tiedot päivitetty'));
    }
    
    public static function remove($id){
        $params = $_POST;
        $ruoka = new Ruoka(array('id' => $id));
        
        $ruoka->remove();
        
        Redirect::to('/ruokalajit', array('message' => 'Ruoka ' . $params ['nimi'] . ' poistettu.'));
    }

}