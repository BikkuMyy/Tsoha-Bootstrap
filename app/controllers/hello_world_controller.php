<?php

class HelloWorldController extends BaseController {

    

    public static function sandbox() {
        // Testaa koodiasi täällä
        $ruoka = new Ruoka(array('nimi' => 'g'));
        $errors = $ruoka->errors();
        Kint:dump($errors);
    }

}
