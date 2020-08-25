<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/test', function () use ($router) {
    return 'test';
});

$router->get('/activate', array('uses' => 'UserController@activate'));

$router->post('/login', array('uses' => 'UserController@login'));
$router->post('/register', array('uses' => 'UserController@register'));
$router->post('/profile', array('uses' => 'UserController@getProfile'));
$router->post('/updateProfile', array('uses' => 'UserController@updateProfile'));
$router->post('/updateAvatar', array('uses' => 'UserController@updateAvatar'));