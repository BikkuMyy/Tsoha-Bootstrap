<?php

/**
 * Description of arkisto_controller
 *
 * @author mari
 */
class ArkistoController extends BaseController {

    public static function index() {
        View::make('base.html');
    }

    public static function etusivu() {
        View::make('arkisto/etusivu.html');
    }

    public static function login() {
        View::make('arkisto/login.html');
    }

    public static function handle_login() {
        $params = $_POST;
        
        //tähän vielä validointi
        
        $kayttaja = Kayttaja::authenticate($params['username'], $params['password']);

        if (!$kayttaja) {
            View::make('arkisto/login.html', array('message' => 'Virheellinen käyttäjätunnus tai salasana.', 
                                                                'kayttajatunnus' => $kayttaja->kayttajatunnus));
        } else {
            $_SESSION['user'] = $kayttaja->id;
        }

        Redirect::to('/etusivu', array('message' => 'Tervetuloa ' . $kayttaja->kayttajatunnus . '!'));
    }

    public static function signup() {
        View::make('arkisto/signup.html');
    }

    public static function handle_signup() {
        $params = $_POST;
        
        $kayttaja = new Kayttaja(array('kayttajatunnus' => $params['username'],
                'salasana' => $params['password']));
        $errors = $kayttaja->errors();
        
        if (count($errors) > 0){
            View::make('arkisto/signup.html', array('kayttajatunnus' => $kayttaja->kayttajatunnus, 'errors' => $errors));
        }
        
        if($kayttaja->onkoKaytossa()) {
            View::make('arkisto/signup.html', array('message' => 'Valitsemasi käyttäjätunnus '
                                        . $kayttaja->kayttajatunnus . ' on jo käytössä.'));
        } else {
            $kayttaja->save();
            Redirect::to('/login', array('message' => 'Uusi käyttäjä luotu onnistuneesti. '
                                                    . 'Voit nyt kirjautua sisään.'));
        }
    }

    public static function logout() {
        //uloskirjautuminen
    }

}
