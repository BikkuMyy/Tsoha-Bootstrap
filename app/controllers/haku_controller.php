<?php

/**
 * Description of HakuController
 *
 * @author mari
 */
class HakuController extends BaseController{
    
    public static function search(){
        View::make('haku/search.html');
    }
    
    public static function makeSearch(){
        $params = $_POST;
        $user = $_SESSION['user'];
        
        //hakusanan validointi??
        
        $kaikki = array();
        $ruoat = self::haeRuokia($user);
        $ainekset = self::haeAineksia();
        $kategoriat = self::haeKategorioita();
        if(isset($params['global'])){
            
        }  
     
        //jokaiselle haulle oma apumetodi
        
        //tulos-lista mukaan tähän
        Redirect::to('/tulokset', array('ruoat' => $ruoat, 
                                        'ainekset' => $ainekset,
                                        'kategoriat' => $kategoriat));
    }
    
    public static function results(){
        View::make('haku/results.html');
    }
    
    
    
    public static function haeAineksia(){
        $params = $_POST;
        if (isset($params['ingredient'])){
            return Aines::searchBy($params['searchword']);
        }
        return array();
    }
    
    public static function haeKategorioita(){
        $params = $_POST;
        if (isset($params['category'])){
            return Kategoria::searchBy($params['searchword']);
        }
        return array();
    }
    
    public static function haeRuokia($user){
        $params = $_POST;
        $ruoat = array();
        if(isset($params['all'])){
            $tulokset = Ruoka::searchBy($params['searchword'], null);
            $ruoat = array_merge($ruoat, $tulokset);
        }
        if(isset($params['own'])){
            $tulokset = Ruoka::searchBy($params['searchword'], $user);
            $ruoat = array_merge($ruoat, $tulokset);
        }
        return $ruoat;
    }
    
    
    
}
