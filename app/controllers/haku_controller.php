<?php

/**
 * Description of HakuController
 *
 * @author mari
 */
class HakuController extends BaseController{
    
    public static function search(){
        $ainekset = Aines::all();
        $kategoriat = Kategoria::all();
        
        View::make('haku/search.html', array('ainekset' => $ainekset, 'kategoriat' => $kategoriat));
    }
    
    public static function makeSearch(){
        $params = $_POST;
        
        if(isset($params['searchword'])){
            self::hakusanaHaku();
        } 
        
        if (isset($params['aines'])){
            
        }
        //hakusanan validointi??
        
        
    }
    
    public static function results(){
        View::make('haku/results.html');
    }
    
    public static function hakusanaHaku(){
        $kayttaja = $_SESSION['user'];
        
        $ruoat = self::haeRuokia($kayttaja);
        $ainekset = self::haeAineksia();
        $kategoriat = self::haeKategorioita();
        
        if(empty($ruoat) && empty($ainekset) && empty($kategoriat)){
            View::make('haku/search.html', array('message' => 'Ei tuloksia annetuilla hakukriteereillä'));
        }
        
        Redirect::to('/tulokset', array('ruoat' => $ruoat, 
                                        'ainekset' => $ainekset,
                                        'kategoriat' => $kategoriat));
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
    
    public static function haeRuokia($kayttaja){
        $params = $_POST;
        $ruoat = array();
        if(isset($params['all'])){
            $tulokset = Ruoka::searchBy($params['searchword'], null);
            $ruoat = array_merge($ruoat, $tulokset);
        }
        if(isset($params['own'])){
            $tulokset = Ruoka::searchBy($params['searchword'], $kayttaja);
            $ruoat = array_merge($ruoat, $tulokset);
        }
        return $ruoat;
    }
    
    
    
}
