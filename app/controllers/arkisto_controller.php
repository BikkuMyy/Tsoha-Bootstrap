<?php

/**
 * Description of arkisto_controller
 *
 * @author mari
 */
class ArkistoController extends BaseController{
    
    public static function login() {
        View::make('login.html');
    }
    
    public static function index() {
        View::make('ruokalajit.html');
    }
    
    public static function single(){
        View::make('ruoka.html');
    }
    
    public static function modify(){
        View::make('modify.html');
    }
}
