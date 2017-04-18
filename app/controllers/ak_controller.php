<?php

/**
 * Aines- ja kategoria-tietokohteiden kontrolleriluokka
 *
 * @author mari
 */
class AKController extends BaseController {
    
    //AINES
    
    /**
     * Metodi näyttää kaikkien ainesten listausnäkymän.
     */
    public static function ainekset(){
        $ainekset = Aines::all();
        View::make('ak/ainekset.html', array('ainekset' => $ainekset));
    }
    
    /**
     * Metodi näyttää aineksen lisäysnäkymän.
     */
    public static function lisaaAines(){
        View::make('ak/aines.html');
    }
    
    /**
     * Metodi käsittelee uuden aineksen lisäyslomakkeen tiedot, validoi ne 
     * ja kutsuu aineksen tietokantaan tallentavaa metodia 
     * sekä uudelleenohjaa ainesten listaussivulle.
     */
    public static function tallennaAines(){
        $params = $_POST;
        $aines = new Aines(array('nimi' => $params['nimi']));
        
        $errors = $aines->errors();
        if (count($errors) > 0){
            View::make('ak/aines.html', array('errors' => $errors, 'nimi' => $aines->nimi));
        }
        
        $aines->save();
        
        Redirect::to('/ainekset', array('message' => 'Aines ' . $params['nimi'] . ' lisätty arkistoosi!'));
    }
    
    //KATEGORIA
    
    /**
     * Metodi näyttää kaikkien kategorioiden listaussivun.
     */
    public static function kategoriat(){
        $kategoriat = Kategoria::all();
        View::make('ak/kategoriat.html', array('kategoriat' => $kategoriat));
    }
    
    /**
     * Metodi näyttää uuden kategorian lisäyssivun.
     */
    public static function lisaaKategoria(){
        View::make('ak/kategoria.html');
    }
    
    /**
     * Metodi käsittelee uuden kategorian lisäyslomakkeen tiedot, validoi ne 
     * ja kutsuu kategorian tietokantaan tallentavaa metodia 
     * sekä uudelleenohjaa kategorioiden listaussivulle.
     */
    public static function tallennaKategoria(){
        $params = $_POST;
        $kategoria = new Kategoria(array('nimi' => $params['nimi']));
        
        $errors = $kategoria->errors();
        if(count($errors) > 0){
            View::make('ak/kategoria.html', array('errors' => $errors, 'nimi' => $kategoria->nimi));
        }
        
        $kategoria->save();
        
        Redirect::to('/kategoriat', array('message' => 'Kategoria ' . $params['nimi'] . ' lisätty arkistoosi!'));
    }
}
