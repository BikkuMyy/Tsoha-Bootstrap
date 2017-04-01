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
    
    public static function etusivu(){
        View::make('arkisto/etusivu.html');
    }
    
    public static function login() {
        View::make('arkisto/login.html');
    }
    
    public static function handle_login(){
        $params = $_POST;
        $kayttaja = Kayttaja::authenticate($params['username'], $params['password']);
        
        if(!$kayttaja){
            View::make('arkisto/login.html');
        } else {
            $_SESSION['user'] = $kayttaja->id;
        }
        
        Redirect::to('/etusivu', array('message' => 'Tervetuloa ' . $kayttaja->kayttajatunnus . '!'));
    }
    
    public static function signup(){
        View::make('arkisto/signup.html');
    }
    
    public static function handle_signup(){
        $params = $_POST;
        
        if(Kayttaja::onkoKaytossa($params['username'])){
            Redirect::to('/signup', array('message' => 'Valitsemasi käyttäjätunnus '
                                          . $params['username'] . ' on jo käytössä.'));
        } else {
            $kayttaja = new Kayttaja(array('kayttajatunnus' => $params['username'], 
                                           'salasana' => $params['password']));
            $kayttaja->save();
            
            Redirect::to('/login', array('message' => 'Uusi käyttäjä luotu onnistuneesti. Voit nyt kirjautua sisään.'));
        }
    }
}
