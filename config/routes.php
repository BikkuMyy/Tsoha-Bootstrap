<?php

function check_logged_in(){
    BaseController::check_logged_in();
}

//ARKISTO

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

$routes->get('/signup', function() {
    ArkistoController::signup();
});

$routes->post('/signup', function() {
    ArkistoController::handle_signup();
});

$routes->post('/logout', function() {
    ArkistoController::logout();
});

$routes->get('/etusivu', 'check_logged_in', function () {
    ArkistoController::etusivu();
});

$routes->get('/settings', 'check_logged_in', function(){
    ArkistoController::settings();
});

$routes->post('/update', 'check_logged_in', function(){
    ArkistoController::update();
});

$routes->get('/delete', 'check_logged_in', function() {
    ArkistoController::delete();
});

$routes->post('/delete', 'check_logged_in', function(){
    ArkistoController::remove();
});

//RUOKA

$routes->get('/ruokalajit', 'check_logged_in', function() {
    RuokaController::index();
});

$routes->post('/ruoka', 'check_logged_in', function() {
    RuokaController::store();
});

$routes->get('/ruoka/new', 'check_logged_in', function() {
    RuokaController::create();
});

$routes->get('/ruokalajit/:id', 'check_logged_in', function($id) {
    RuokaController::single($id);
});

$routes->post('/ruoka/:id/update', 'check_logged_in', function($id) {
    RuokaController::update($id);
});

$routes->get('/ruokalajit/:id/modify', 'check_logged_in', function($id) {
    RuokaController::modify($id);
});

$routes->post('/ruoka/:id/remove', 'check_logged_in', function($id) {
    RuokaController::remove($id);
});

$routes->get('/ruokalajit/:id/delete', 'check_logged_in', function($id) {
    RuokaController::delete($id);
});

//AINES & KATEGORIA

$routes->get('/ainekset', 'check_logged_in', function() {
    AKController::ainekset();
});

$routes->get('/aines', 'check_logged_in', function() {
    AKController::lisaaAines();
});

$routes->post('/aines', 'check_logged_in', function() {
    AKController::tallennaAines();
});

$routes->get('/kategoriat', 'check_logged_in', function() {
    AKController::kategoriat();
});

$routes->get('/kategoria', 'check_logged_in', function () {
    AKController::lisaaKategoria();
});

$routes->post('/kategoria', 'check_logged_in', function() {
    AKController::tallennaKategoria();
});
