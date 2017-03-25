<?php

$routes->get('/', function() {
    HelloWorldController::index();
});

$routes->get('/sandbox', function() {
    HelloWorldController::sandbox();
});

$routes->get('/login', function() {
    ArkistoController::login();
});

$routes->get('/ruokalajit', function() {
    RuokaController::index();
});

$routes->post('/ruoka', function() {
    RuokaController::store();
});

$routes->get('/ruoka/new', function() {
    RuokaController::create();
});

$routes->get('/ruokalajit/:id', function($id) {
    RuokaController::single($id);
});

$routes->get('/ruokalajit/:id/modify', function($id) {
    RuokaController::modify($id);
});

