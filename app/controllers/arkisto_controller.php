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

        $testiKayttaja = new Kayttaja (array('kayttajatunnus' => $params['username'],
                                        'salasana' => $params['password']));
        
        $errors = $testiKayttaja->errors();
        if(count($errors) > 0){
            View::make('arkisto/login.html', array('kayttajatunnus' => $testiKayttaja->kayttajatunnus, 'errors' => $errors));
        }

        $kayttaja = Kayttaja::authenticate($params['username'], $params['password']);

        if (!$kayttaja) {
            View::make('arkisto/login.html', array('message' => 'Virheellinen käyttäjätunnus tai salasana.',
                'kayttajatunnus' => $params['username']));
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

        if (count($errors) > 0) {
            View::make('arkisto/signup.html', array('kayttajatunnus' => $kayttaja->kayttajatunnus, 'errors' => $errors));
        }

        if ($kayttaja->onkoKaytossa()) {
            View::make('arkisto/signup.html', array('message' => 'Valitsemasi käyttäjätunnus '
                                                    . $kayttaja->kayttajatunnus . ' on jo käytössä.'));
        } else {
            $kayttaja->save();
            Redirect::to('/login', array('message' => 'Uusi käyttäjä luotu onnistuneesti. '
                                                    . 'Voit nyt kirjautua sisään.'));
        }
    }

    public static function logout() {
        $_SESSION['user'] = null;
        Redirect::to('/login', array('message' => 'Olet kirjautunut ulos'));
    }

    public static function settings() {
        View::make('arkisto/settings.html');
    }

    public static function delete() {
        View::make('arkisto/delete.html');
    }

    public static function remove() {
        $kayttaja_id = $_SESSION['user'];
        $kayttaja = Kayttaja::find($kayttaja_id);
        
        if (!$kayttaja->tarkistaSalasana($_POST['password'])) {
            View::make('arkisto/delete.html', 
                        array('message' => 'Väärä salasana'));
            
        } elseif(!isset($_POST['checked'])){
            View::make('arkisto/delete.html', 
                        array('message' => 'Rastita ruutu "Ymmärrän poistamisen seuraukset", '
                                            . 'jos haluat poistaa käyttäjätilin.'));
        }
        
        $ruoat = Ruoka::all($kayttaja_id);
        foreach($ruoat as $ruoka){
            $ruoka->remove();
        }
        $kayttaja->remove();
        $_SESSION['user'] = null;
        Redirect::to('/', array('user_removed' => 'Käyttäjätili poistettu onnistuneesti.'));
    }
    
    public static function update(){
        //käyttäjän tietojen päivitys -> erillinen salasanalle ja käyttäjänimelle
        //jos submitTunnus -> päivitä tunnus
        //jos submitSalasana -> päivitä salasana
    }

}
