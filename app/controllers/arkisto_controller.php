<?php

/**
 * Kayttaja-tietokohteen kontrolleriluokka
 * 
 * @author mari
 */
class ArkistoController extends BaseController {

    /**
     * Metodi näyttää sovelluksen etusivunäkymän.
     */
    public static function index() {
        View::make('arkisto/start.html');
    }

    /**
     * Metodi näyttää kirjautuneen käyttäjän etusivunäkymän.
     */
    public static function etusivu() {
        View::make('arkisto/etusivu.html');
    }

    /**
     * Metodi näyttää kirjautumisnäkymän.
     */
    public static function login() {
        View::make('arkisto/login.html');
    }

    /**
     * Metodi validoi kirjautumislomakkeen tiedot, 
     * kutsuu niiden oikeellisuuden tarkistavaa metodia 
     * ja kirjaa kirjaa käyttäjän sisään sen onnistuessa.
     */
    public static function handle_login() {
        $params = $_POST;

        $testiKayttaja = new Kayttaja(array('kayttajatunnus' => $params['username'],
                                            'salasana' => $params['password']));

        $errors = $testiKayttaja->errors();
        if (count($errors) > 0) {
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

    /**
     * Metodi näyttää rekisteröitymisnäkymän.
     */
    public static function signup() {
        View::make('arkisto/signup.html');
    }

    /**
     * Metodi validoi rekisteröitymislomakkeeen tiedot, 
     * kutsuu käyttäjätunnuksen olemassaolon tarkistavaa metodia 
     * ja onnistuessaan uuden käyttäjän tietokantaan tallentavaa metodia.
     */
    public static function handle_signup() {
        $params = $_POST;

        $kayttaja = new Kayttaja(array('kayttajatunnus' => $params['username'],
            'salasana' => $params['password']));
        $errors = $kayttaja->errors();

        if (count($errors) > 0) {
            View::make('arkisto/signup.html', array('kayttajatunnus' => $kayttaja->kayttajatunnus,
                                                    'errors' => $errors));
        }

        if (Kayttaja::onkoKaytossa($kayttaja->kayttajatunnus)) {
            View::make('arkisto/signup.html', array('message' => 'Valitsemasi käyttäjätunnus '
                                                . $kayttaja->kayttajatunnus . ' on jo käytössä.'));
        } else {
            $kayttaja->save();
            Redirect::to('/login', array('message' => 'Uusi käyttäjä luotu onnistuneesti. '
                                            . 'Voit nyt kirjautua sisään.'));
        }
    }

    /**
     * Metodi kirjaa käyttäjän ulos ja uudelleenohjaa kirjautumisnäkymään.
     */
    public static function logout() {
        $_SESSION['user'] = null;
        Redirect::to('/login', array('message' => 'Olet kirjautunut ulos'));
    }

    /**
     * Metodi näyttää käyttäjän asetusnäkymän.
     */
    public static function settings() {
        View::make('arkisto/settings.html');
    }
    
    /**
     * Metodi näyttää käyttäjätilin poistonäkymän.
     */
    public static function delete() {
        View::make('arkisto/delete.html');
    }

    /**
     * Metodi tarkistaa salasanan oikeellisuuden ja että checkbox on valittuna käyttäjätilin poistolomakkeella,
     * kutsuu käyttäjän poistavaa metodia ja uudelleenohjaa sovelluksen etusivulle.
     */
    public static function remove() {
        $params = $_POST;
        $kayttaja_id = $_SESSION['user'];
        $kayttaja = Kayttaja::find($kayttaja_id);

        if (!$kayttaja->tarkistaSalasana($params['password'])) {
            View::make('arkisto/delete.html', array('message' => 'Väärä salasana'));
            
        } elseif (!isset($params['checked'])) {
            View::make('arkisto/delete.html', array('message' => 'Rastita ruutu '
                                            . '"Ymmärrän poistamisen seuraukset", '
                                            . 'jos haluat poistaa käyttäjätilin.'));
        }
        
        $kayttaja->remove();
        $_SESSION['user'] = null;
        Redirect::to('/', array('user_removed' => 'Käyttäjätili poistettu onnistuneesti.'));
    }

    /**
     * Metodi näyttää käyttäjätunnuksen tai salasanan muokkausnäkymän.
     */
    public static function modify() {
        View::make('arkisto/update.html');
    }
    
    /**
     * Metodi kutsuu muokkauslomakkeen tietojen perusteella joko 
     * käyttäjätunnuksen tai salasanan muuttavaa metodia.
     */
    public static function update() {
        $params = $_POST;

        if (isset($params['username'])) {
            self::paivitaTunnus();
        }

        if (isset($params['new_password'])) {
            self::paivitaSalasana();
        }
    }
    
    /**
     * Metodi validoi muokkauslomakkeen tiedot 
     * ja kutsuu käyttäjätunnuksen tarkistavaa ja päivittävää metodia.
     */
    public function paivitaTunnus() {
        $params = $_POST;

        $kayttaja = Kayttaja::find($_SESSION['user']);
        $kayttaja['kayttajatunnus'] = $params['username'];

        $errors = $kayttaja->validate_tunnus();
        if (count($errors) > 0) {
            View::make('arkisto/update.html', array('errors' => $errors));
        }

        if ($kayttaja->tarkistaJaPaivitaTunnus()) {
            Redirect::to('/settings', array('message' => 'Käyttäjätunnus päivitetty onnistuneesti!'));
        } else {
            View::make('arkisto/update.html', array('message' => 'Valitsemasi käyttäjätunnus on jo käytössä.'));
        }
    }

    /**
     * Metodi validoi muokkauslomakkeen tiedot,
     * kutsuu salasanan tarkistavaa ja päivittävää metodia
     * sekä uudelleenohjaa asetussivulle.
     */
    public function paivitaSalasana() {
        $params = $_POST;
        $kayttaja = Kayttaja::find($_SESSION['user']);
        $kayttaja->salasana = $params['new_password'];

        $errors = $kayttaja->validate_salasana();
        if (count($errors) > 0) {
            View::make('arkisto/update.html', array('errors' => $errors));
        }
        
        if(!$kayttaja->tarkistaSalasana($params['old_password'])){
            View::make('arkisto/update.html', array('message' => 'Vanha salasana väärin.'));
        }
        
        $kayttaja->updateSalasana();
        
        Redirect::to('/settings', array('message' => 'Salasana vaihdettu onnistuneesti!'));
    }

}
