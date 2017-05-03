<?php

/**
 * Ruoka-tietokohteen kontrolleriluokka
 *
 * @author mari
 */
class RuokaController extends BaseController {

    /**
     * metodi näyttää kirjautuneen käyttäjän kaikkien ruokalajien listauksen.
     * 
     */
    public static function index() {
        $ruoat = Ruoka::all(parent::get_user_logged_in()->id);
        View::make('ruoka/ruokalajit.html', array('ruoat' => $ruoat));
    }

    /**
     * Metodi näyttää parametrina saamansa ruoan esittelysivun.
     * 
     * @param type $id
     */
    public static function single($id) {
        $ruoka = Ruoka::find($id);
        View::make('ruoka/ruoka.html', array('ruoka' => $ruoka));
    }

    /**
     * Metodi näyttää uuden ruokalajin lisäysnäkymän.
     */
    public static function create() {
        $kategoriat = Kategoria::all();
        $ainekset = Aines::all();
        View::make('ruoka/new.html', array('kategoriat' => $kategoriat, 'ainekset' => $ainekset));
    }

    /**
     * Metodi käsittelee uuden ruokalajin lisäyslomakkeen tiedot, validoi ne 
     * ja kutsuu ruoan tietokantaan tallentavaa metodia 
     * sekä uudelleenohjaa kyseisen ruoan esittelysivulle.
     */
    public static function store() {
        $params = $_POST;
        $user = $_SESSION['user'];

        $kategoriat = self::tarkista($params['valitutKategoriat']);
        $ainekset = self::tarkista($params['valitutAinekset']);

        $ruoka = new Ruoka(array('nimi' => $params ['nimi'],
            'kommentti' => $params ['kommentti'],
            'kayttaja' => $user,
            'kategoriat' => $kategoriat,
            'ainekset' => $ainekset));

        $errors = $ruoka->errors();
        if (count($errors) > 0) {
            $valitutKategoriat = self::luoValittujenLista($kategoriat, Kategoria::all());
            $valitutAinekset = self::luoValittujenLista($ainekset, Aines::all());

            View::make('ruoka/new.html', array('errors' => $errors, 'ruoka' => $ruoka,
                'ainekset' => $valitutAinekset,
                'kategoriat' => $valitutKategoriat));
        }

        $ruoka->save();
        Redirect::to('/ruokalajit/' . $ruoka->id, array('message' => 'Ruoka ' . $ruoka->nimi
            . ' lisätty arkistoosi!'));
    }

    /**
     * Metodi näyttää parametrina saamansa ruoan muokkaussivun, 
     * jos kirjatunut käyttäjä on myös ruoan lisääjä.
     * 
     * @param type $ruoka_id
     */
    public static function modify($ruoka_id) {
        $ruoka = Ruoka::find($ruoka_id);
        $kayttaja = $_SESSION['user'];

        if ($kayttaja != $ruoka->kayttaja) {
            Redirect::to('/ruokalajit/' . $ruoka->id, array('message' => "Voit muokata vain itse lisäämiesi ruokalajien tietoja."));
        }

        $valitutAinekset = Aines::ainekset($ruoka_id);
        $valitutKategoriat = Kategoria::kategoriat($ruoka_id);

        $kategoriat = self::luoValittujenLista($valitutKategoriat, Kategoria::all());
        $ainekset = self::luoValittujenLista($valitutAinekset, Aines::all());

        View::make('ruoka/modify.html', array('ruoka' => $ruoka,
            'kategoriat' => $kategoriat,
            'ainekset' => $ainekset,
            'valitutKategoriat' => $valitutKategoriat,
            'valitutAinekset' => $valitutAinekset));
    }

    /**
     * Metodi käsittelee ruoan muokkauslomakkeen tiedot, validoi ne 
     * ja kutsuu tietokantataulun riviä muokkaavaa metodia sekä
     * uudelleenohjaa kyseisen ruoan esittelysivulle..
     * 
     * @param type $id
     */
    public function update($id) {
        $params = $_POST;
        $user = parent::get_user_logged_in();
        
        $kategoriat = self::tarkista($params['valitutKategoriat']);
        $ainekset = self::tarkista($params['valitutAinekset']);

        $ruoka = new Ruoka(array('id' => $id,
            'nimi' => $params ['nimi'],
            'kommentti' => $params ['kommentti'],
            'kayttaja' => $user->id,
            'kategoriat' => $kategoriat,
            'ainekset' => $ainekset));

        $errors = $ruoka->errors();
        if (count($errors) > 0) {
            $valitutKategoriat = self::luoValittujenLista($kategoriat, Kategoria::all());
            $valitutAinekset = self::luoValittujenLista($ainekset, Aines::all());

            View::make('ruoka/new.html', array('errors' => $errors, 'ruoka' => $ruoka,
                'ainekset' => $valitutAinekset,
                'kategoriat' => $valitutKategoriat));
        }

        $ruoka->update();

        Redirect::to('/ruokalajit/' . $ruoka->id, array('message' => 'Ruoan tiedot päivitetty'));
    }

    /**
     * Metodi näyttää ruokalajin poistonäkymän,
     * jos kirjautunut käyttäjä on ruokalajin lisääjä.
     * @param type $id
     */
    public static function delete($id) {
        $ruoka = Ruoka::find($id);
        $kayttaja = $_SESSION['user'];

        if ($kayttaja != $ruoka->kayttaja) {
            Redirect::to('/ruokalajit/' . $ruoka->id, array('message' => "Voit poistaa vain itse lisäämiäsi ruokia."));
        }


        View::make('ruoka/remove.html', array('ruoka' => $ruoka));
    }

    /**
     * Metodi kutsuu parametrina saamansa ruokalakin tietokannasta poistavaa metodia
     * ja uudelleenohjaa ruokalajien listaussivulle.
     * 
     * @param type $id poistettavan id
     */
    public static function remove($id) {
        $ruoka = new Ruoka(array('id' => $id));
        $ruoka->remove();

        Redirect::to('/ruokalajit', array('message' => 'Ruokalaji poistettu.'));
    }

    /**
     * Apumetodi, joka vertailee parametreina saamiaan listoja  ja luo sen perusteella listan, 
     * jossa  ainesten/kategorioiden boolean-muuttuja on true,
     * jos ne löytyvät molemmista listoista ja false jos eivät.
     * 
     * @param array $valitut
     * @param array $kaikki
     * @return array lista, jonka olioille asetettu true/false
     */
    public function luoValittujenLista($valitut, $kaikki) {

        $valittujenLista = array();

        foreach ($kaikki as $k) {
            $k->valittu = false;
            
            foreach ($valitut as $v) {

                if ($k->nimi == $v || $k == $v) {
                    $k->valittu = true;
                }
            }
            
            $valittujenLista[] = $k;
        }
        return $valittujenLista;
    }

    /**
     * Apumetodi, joka tarkistaa, onko parametrina annetulla listalla teksti 
     * 'Valitse...' ja palauttaa listan, josta se on poistettu.
     * 
     * @param type $lista
     * @return array parametrina saatu lista, josta teksti mahd. poistettu
     */
    public static function tarkista($lista) {
        if ($lista[0] == 'Valitse...') {
            array_splice($lista, 0, 1);
            return $lista;
        }
        return $lista;
    }

//    public static function muunna($lista){
//        $uusi = array();
//        foreach($lista as $rivi){
//            $uusi[] = $rivi->nimi;
//        }
//    }
}
