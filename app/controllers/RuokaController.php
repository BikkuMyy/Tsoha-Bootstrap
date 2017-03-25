<?php

/**
 * Description of RuokaController
 *
 * @author mari
 */
class RuokaController extends BaseController {

    public static function index() {
        $ruoat = Ruoka::all();
        View::make('ruokalajit.html', array('ruoat' => $ruoat));
    }

    public static function single($id) {
        $ruoka = Ruoka::find($id);
        View::make('ruoka.html', array('ruoka' => $ruoka));
    }

    public static function modify($id) {
        $ruoka = Ruoka::find($id);
        View::make('modify.html', array('ruoka' => $ruoka));
    }
    
    public static function create(){
        View::make('new.html');
    }

    public static function store() {
        $params = $_POST;
        
        $ruoka = new Ruoka(array(
            'nimi' => $params ['nimi'],
            'kommentti' => $params ['kommentti']
            //'kayttaja' => 
            //'kategoriat' => 
            //'ainekset' => 
        ));
        
        Kint::dump($params);
        
        $ruoka->save();
        
        Redirect::to('/ruokalajit/' . $ruoka->id, array('message' => 'Peli on lisätty kirjastoosi!'));
    }

}