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
        View::make('ruoka/new.html', array('kategoriat' => $kategoriat,'ainekset' => $ainekset));
    }

    /**
     * Metodi käsittelee uuden ruokalajin lisäyslomakkeen tiedot, validoi ne 
     * ja kutsuu ruoan tietokantaan tallentavaa metodia 
     * sekä uudelleenohjaa kyseisen ruoan esittelysivulle.
     */
    public static function store() {
        $params = $_POST;
        $user = parent::get_user_logged_in();

        //jos ei valittuja kategorioita tai aineksia, miten tarkistetaan?
        $kategoriat = self::valitut($params['valitutKategoriat']);
        $ainekset = self::valitut($params['valitutAinekset']);

        $ruoka = new Ruoka(array('nimi' => $params ['nimi'],
            'kommentti' => $params ['kommentti'],
            'kayttaja' => $user->id,
            'kategoriat' => $kategoriat,
            'ainekset' => $ainekset));

        $errors = $ruoka->errors();
        if (count($errors) > 0) {
//            $valitutKategoriat = self::luoValittujenLista($kategoriat, Kategoria::all());
//            $valitutAinekset = self::luoValittujenLista($ainekset, Aines::all());
            $valitutAinekset = Aines::all();
            $valitutKategoriat = Kategoria::all();
            
            View::make('ruoka/new.html', array('errors' => $errors,'ruoka' => $ruoka, 
                                               'ainekset' => $valitutAinekset,
                                               'kategoriat' => $valitutKategoriat));
        }
        
        $ruoka->save();
        Redirect::to('/ruokalajit/' . $ruoka->id, array('message' => 'Ruoka ' . $ruoka->nimi 
                                                      . ' lisätty arkistoosi!'));
    }

    /**
     * Metodi näyttää parametrina saamansa ruoan muokkaussivun.
     * 
     * @param type $ruoka_id
     */
    public static function modify($ruoka_id) {
        $ruoka = Ruoka::find($ruoka_id);
        $kategoriat = self::luoValittujenLista(Kategoria::kategoriat($ruoka_id), Kategoria::all());
        $ainekset = self::luoValittujenLista(Aines::ainekset($ruoka_id), Aines::all());
        
        View::make('ruoka/modify.html', array('ruoka' => $ruoka,
            'kategoriat' => $kategoriat,
            'ainekset' => $ainekset));
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

        $kategoriat = self::valitut($params['valitutKategoriat']);
        $ainekset = self::valitut($params['valitutAinekset']);

        $ruoka = new Ruoka(array('id' => $id,
            'nimi' => $params ['nimi'],
            'kommentti' => $params ['kommentti'],
            'kayttaja' => $user->id,
            'kategoriat' => $kategoriat,
            'ainekset' => $ainekset));
        
        $errors = $ruoka->errors();
        if (count($errors) > 0) {
        //            $valitutKategoriat = self::luoValittujenLista($kategoriat, Kategoria::all());
        //            $valitutAinekset = self::luoValittujenLista($ainekset, Aines::all());
            $valitutAinekset = Aines::all();
            $valitutKategoriat = Kategoria::all();
            
            View::make('ruoka/new.html', array('errors' => $errors,'ruoka' => $ruoka, 
                                               'ainekset' => $valitutAinekset,
                                               'kategoriat' => $valitutKategoriat));
        }
        
        $ruoka->update();

        Redirect::to('/ruokalajit/' . $ruoka->id, array('message' => 'Ruoan tiedot päivitetty'));
    }

    /**
     * Metodi näyttää ruokalajin poistonäkymän.
     * @param type $id
     */
    public static function delete($id) {
        $ruoka = Ruoka::find($id);
        View::make('ruoka/remove.html', array('ruoka' => $ruoka));
    }

    /**
     * Metodi kutsuu parametrina saamansa ruokalakin tietokannasta poistavaa metodia
     * ja uudelleenohjaa ruokalajien listaussivulle.
     * 
     * @param type $id
     */
    public static function remove($id) {
        $ruoka = new Ruoka(array('id' => $id));
        $ruoka->remove();

        Redirect::to('/ruokalajit', array('message' => 'Ruokalaji poistettu.'));
    }

    /**
     * Apumetodi, joka vertailee parametreina saamiaan listoja 
     * ja luo vertailun perusteella listan, 
     * jossa  ainesten/kategorioiden boolean-muuttuja on true,
     * jos ne löytyvät molemmista listoista ja false jos eivät.
     * 
     * @param array $valitut
     * @param array $kaikki
     * @return array
     */
    public function luoValittujenLista($valitut, $kaikki) {
        
        $valittujenLista = array();

        foreach ($kaikki as $k) {

            if (empty($valitut)) {
                $valittujenLista[] = ($k['valittu'] = false);
                continue;
            }

            foreach ($valitut as $v) {

                if ($k->id == $v->id) {
                    $valittujenLista[] = ($k['valittu'] = true);
                } else {
                    $valittujenLista[] =($k['valittu'] = false);
                }
            }
        }
        //Kint::dump($valittujenLista);
        return $valittujenLista;
    }

    /**
     * Apumetodi, joka tarkistaa, onko parametrina annettu lista alustettu 
     * ja palauttaa sen tai tyhjän listan.
     * 
     * @param type $valitut
     * @return array
     */
    public static function valitut($valitut) {
        if (isset($valitut)) {
            return $valitut;
        } else {
            return array();
        }
    }

}
