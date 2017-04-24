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
        
        
        
        //hakusanan validointi??
        
        //tulos-lista mukaan tähän
        Redirect::to('/tulokset');
    }
    
    public static function results(){
        View::make('haku/results.html');
    }
}
