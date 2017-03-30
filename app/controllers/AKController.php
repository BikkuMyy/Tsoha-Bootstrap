<?php

/**
 * Description of AKController
 *
 * @author mari
 */
class AKController extends BaseController {
    
    public static function lisaaAines(){
        View::make('aines.html');
    }
    
    public static function tallennaAines(){
        $params = $_POST;
        $aines = new Aines(array('nimi' => $params['nimi']));
        $aines->save();
        
        //redirect
    }
    
    public static function lisaaKategoria(){
        View::make('kategoria.html');
    }
    
    public static function tallennaKategoria(){
        $params = $_POST;
        $kategoria = new Kategoria(array('nimi' => $params['nimi']));
        $kategoria->save();
        
        //redirect
    }
}
