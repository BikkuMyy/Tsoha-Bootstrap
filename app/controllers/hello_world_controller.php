<?php

class HelloWorldController extends BaseController {

    

    public static function sandbox() {
        // Testaa koodiasi täällä
        $ruoat = Ruoka::all();
        $ruoka = Ruoka::find(1);
        
        Kint::dump($ruoat);
        Kint::dump($ruoka);
    }

}
