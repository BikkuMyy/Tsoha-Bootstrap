<?php

/**
 * Description of arkisto_controller
 *
 * @author mari
 */
class ArkistoController extends BaseController{
    
    public static function login() {
        View::make('login.html');
    }
}
