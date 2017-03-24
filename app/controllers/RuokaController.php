<?php

/**
 * Description of RuokaController
 *
 * @author mari
 */
class RuokaController extends BaseController{
    
    public static function index(){
        $ruoat = Ruoka::all();
        View::make('ruokalajit.html', array('ruoat' => $ruoat));
    }
    
}
