<?php

$routes->get('/', function() {
    ArkistoController::index();
});

$routes->get('/sandbox', function() {
    HelloWorldController::sandbox();
});

$routes->get('/login', function() {
    ArkistoController::login();
});

$routes->post('/login', function() {
    ArkistoController::handle_login();
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

$routes->post('/ruoka/:id/edit', function($id) {
    RuokaController::edit();
});

$routes->get('/ruokalajit/:id/modify', function($id) {
    RuokaController::modify($id);
});

