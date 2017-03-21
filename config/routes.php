<?php

$routes->get('/', function() {
    HelloWorldController::index();
  });

$routes->get('/login', function() {
    ArkistoController::login();
});

$routes->get('/ruokalajit', function() {
    ArkistoController::index();
});

$routes->get('/ruokalajit/1', function() {
    ArkistoController::single();
});

$routes->get('/ruokalajit/1/modify', function() {
    ArkistoController::modify();
});