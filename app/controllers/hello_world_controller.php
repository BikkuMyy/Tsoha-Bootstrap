<?php

class HelloWorldController extends BaseController {

    public static function index() {
        // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
        View::make('base.html');
    }

    public static function sandbox() {
        // Testaa koodiasi täällä
        $ruoat = Ruoka::all();
        $ruoka = Ruoka::find(1);
        
        Kint::dump($ruoat);
        Kint::dump($ruoka);
    }

}
