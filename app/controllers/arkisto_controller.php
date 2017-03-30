<?php

/**
 * Description of arkisto_controller
 *
 * @author mari
 */
class ArkistoController extends BaseController{
    
    public static function index() {
        View::make('base.html');
    }
    
    public static function login() {
        View::make('login.html');
    }
    
    public static function handle_login(){
        $params = $_POST;
        $kayttaja = Kayttaja::authenticate($params['username'], $params['password']);
        
        if(!$kayttaja){
            View::make('login.html');
        } else {
            $_SESSION['user'] = $kayttaja->id;
        }
        
        Redirect::to('/ruokalajit', array('message' => 'Tervetuloa ' . $kayttaja->kayttajatunnus . '!'));
    }
    
    public static function signup(){
        View:make('signup.html');
    }
    
    public static function handle_signup(){
        //tarkistetaan, onko käyttäjätunnus käytettävissä
        // luodaan uusi käyttäjä
    }
}
