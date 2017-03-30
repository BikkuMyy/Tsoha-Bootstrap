<?php

/**
 * Description of AKController
 *
 * @author mari
 */
class AKController extends BaseController {
    
    public static function ainekset(){
        $ainekset = Aines::all();
        View::make('ak/ainekset.html', array('ainekset' => $ainekset));
    }
    
    public static function lisaaAines(){
        View::make('ak/aines.html');
    }
    
    public static function tallennaAines(){
        $params = $_POST;
        $aines = new Aines(array('nimi' => $params['nimi']));
        $aines->save();
        
        Redirect::to('/ainekset', array('message' => 'Aines ' . $params['nimi'] . ' lisätty arkistoosi!'));
    }
    
    public static function kategoriat(){
        $kategoriat = Kategoria::all();
        View::make('ak/kategoriat.html', array('kategoriat' => $kategoriat));
    }
    
    public static function lisaaKategoria(){
        View::make('ak/kategoria.html');
    }
    
    public static function tallennaKategoria(){
        $params = $_POST;
        $kategoria = new Kategoria(array('nimi' => $params['nimi']));
        $kategoria->save();
        
        Redirect::to('/kategoriat', array('message' => 'Kategoria ' . $params['nimi'] . ' lisätty arkistoosi!'));
    }
}
