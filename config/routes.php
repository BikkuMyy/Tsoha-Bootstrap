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

$routes->get('/ruokalajit/1', function() {
    ArkistoController::single();
});

$routes->get('/ruokalajit/1/modify', function() {
    ArkistoController::modify();
});