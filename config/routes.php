<?php

$routes->get('/', function() {
    ArkistoController::index();
});

$routes->get('/sandbox', function() {
    HelloWorldController::sandbox();
});

$routes->get('/etusivu', function (){
    ArkistoController::etusivu(); 
});

$routes->get('/login', function() {
    ArkistoController::login();
});

$routes->post('/login', function() {
    ArkistoController::handle_login();
});

$routes->get('/signup', function(){
    ArkistoController::signup();
});

$routes->post('/signup', function(){
    ArkistoController::handle_signup(); 
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

$routes->get('/ainekset', function(){
    AKController::ainekset();
});

$routes->get('/aines', function() {
    AKController::lisaaAines();
});

$routes->post('/aines', function(){
    AKController::tallennaAines();
});

$routes->get('/kategoriat', function(){
    AKController::kategoriat();
});

$routes->get('/kategoria', function (){
    AKController::lisaaKategoria();
});

$routes->post('/kategoria', function(){
    AKController::tallennaKategoria();
});

$routes->get('/ruokalajit/:id', function($id) {
    RuokaController::single($id);
});

$routes->post('/ruoka/:id/update', function($id) {
    RuokaController::update($id);
});

$routes->get('/ruokalajit/:id/modify', function($id) {
    RuokaController::modify($id);
});

$routes->post('/ruokalajit/:id/remove', function($id){
   RuokaController::remove($id); 
});