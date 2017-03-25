<?php

  class BaseController{

    public static function get_user_logged_in(){
        if(isset($_SESSION['user'])){
            $user_id = $_SESSION['user'];
            
            return Kayttaja::find($user_id);
        }
        
      return null;
    }

    public static function check_logged_in(){
      // Toteuta kirjautumisen tarkistus tähän.
      // Jos käyttäjä ei ole kirjautunut sisään, ohjaa hänet toiselle sivulle (esim. kirjautumissivulle).
    }

  }
