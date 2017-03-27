<?php

/**
 * Description of RuokaController
 *
 * @author mari
 */
class RuokaController extends BaseController {

    public static function index() {
        $ruoat = Ruoka::all(parent::get_user_logged_in()->id);
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
    
    public static function update($id){
        $params = $_POST;
        
        $ruoka = new Ruoka(array(
            'id' => $id,
            'nimi' => $params ['nimi'],
            'kommentti' => $params ['kommentti']
            //'kategoriat' => 
            //'ainekset' => 
        ));
        
        $ruoka->update();
        
        Redirect::to('/ruokalajit/' . $ruoka->id, array('message' => 'Ruoan tiedot päivitetty'));
    }
    
    public static function create(){
        View::make('new.html');
    }

    public static function store($id) {
        $params = $_POST;
        $user = parent::get_user_logged_in();
        
        $ruoka = new Ruoka(array(
            'id' => $id,
            'nimi' => $params ['nimi'],
            'kommentti' => $params ['kommentti'],
            'kayttaja' => $user['id']
            //'kategoriat' => 
            //'ainekset' => 
        ));
        
        //Kint::dump($params);
        
        $ruoka->save();
        
        Redirect::to('/ruokalajit/' . $ruoka->id, array('message' => 'Ruoka ' . $params ['nimi'] . ' lisätty arkistoosi!'));
    }
    
    public static function remove($id){
        $params = $_POST;
        
        $ruoka = new Ruoka(array('id' => $id));
        
        $ruoka->remove();
        
        Redirect::to('/ruokalajit', array('message' => 'Ruoka ' . $params ['nimi'] . ' poistettu.'));
    }

}