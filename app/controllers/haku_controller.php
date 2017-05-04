<?php

/**
 * Description of HakuController
 *
 * @author mari
 */
class HakuController extends BaseController {

    /**
     * Metodi näyttää hakunäkymän.
     */
    public static function search() {
        $ainekset = Aines::all();
        $kategoriat = Kategoria::all();

        View::make('haku/search.html', array('ainekset' => $ainekset, 'kategoriat' => $kategoriat));
    }

    /**
     * Metodi kutsuu toteuttaa haun tietokannasta sen tyypistä riippuen
     * ja joko uudelleenohjaa tuloksiin tai palauttaa hakusivulle.
     */
    public static function makeSearch() {
        $params = $_POST;
        $ruoat = array();

        if (isset($params['searchword'])) {
            self::hakusanaHaku();
        }
        
        if (isset($params['aines'])) {
            $aines = $params['aines'];
            if(self::tarkista($aines) != null){
                $ruoat = Ruoka::seachByIngredient($aines);
            }
        } 
        
        if (isset($params['kategoria'])) {
            $kategoria = $params['kategoria'];
            if (self::tarkista($kategoria) != null){
                $ruoat = Ruoka::seachByCategory($kategoria);
            }   
        }

        if (empty($ruoat)) {
            View::make('haku/search.html', array('message' => 'Ei tuloksia annetuilla hakukriteereillä'));
        }
        
        Redirect::to('/tulokset', array('ruoat' => $ruoat));
    }

    /**
     * Metodi näyttää haun tulokset listaavan sivun.
     */
    public static function results() {
        View::make('haku/results.html');
    }

    /**
     * Metodi kutsuu ruokia, kategorioita tai aineksia hakusanan perusteella hakevia metodeja
     * ja lopuksi uudelleenohjaa tuloksiin tai palauttaa hakusivulle.
     */
    public static function hakusanaHaku() {
        $ruoat = self::haeRuokia();
        $ainekset = self::haeAineksia();
        $kategoriat = self::haeKategorioita();

        if (empty($ruoat) && empty($ainekset) && empty($kategoriat)) {
            View::make('haku/search.html', array('message' => 'Ei tuloksia annetuilla hakukriteereillä'));
        }

        Redirect::to('/tulokset', array('ruoat' => $ruoat,
            'ainekset' => $ainekset,
            'kategoriat' => $kategoriat));
    }

    /**
     * Metodi hakee aineksia tietokannasta parametrina annetun hakusanan perusteella.
     * @return array haun tulokset
     */
    public static function haeAineksia() {
        $params = $_POST;
        if (isset($params['ingredient'])) {
            return Aines::searchBy($params['searchword']);
        }
        return array();
    }

    /**
     * Metodi hakee kategorioita tietokannasta parametrina annetun hakusanan perusteella.
     * @return array haun tulokset
     */
    public static function haeKategorioita() {
        $params = $_POST;
        if (isset($params['category'])) {
            return Kategoria::searchBy($params['searchword']);
        }
        return array();
    }

    /**
     * Metodi hakee ruokalajeja tietokannasta parametrina annetun hakusanan perusteella.
     * @return array tulokset
     */
    public static function haeRuokia() {
        $params = $_POST;
        $ruoat = array();
        if (isset($params['all'])) {
            $ruoat = Ruoka::searchBy($params['searchword']);
        }
//        if (isset($params['own'])) {
//            $tulokset = Ruoka::searchBy($params['searchword'], $kayttaja);
//            $ruoat = array_merge($ruoat, $tulokset);
//        }
        return $ruoat;
    }
    
    /**
     * Apumetodi, joka tarkistaa, onko parametrina annettu muuttuja teksti 
     * 'Valitse...' ja palauttaa tyhjän listan, jos on.
     * 
     * @param integer $muuttuja
     * @return array parametrina saatu lista, josta teksti mahd. poistettu
     */
    public static function tarkista($muuttuja) {
        if ($muuttuja == 'Valitse...') {
            return null;
        }
        return $muuttuja;
    }

}
