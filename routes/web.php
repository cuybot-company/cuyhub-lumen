<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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
    return 'CuyHub API LUMEN';
});


$router->group(['prefix' => 'v1'], function () use ($router) {

    $router->group(["prefix" => "auth"], function () use ($router) {
        $router->post('/login', 'AuthController@login');
        $router->post('/register', 'AuthController@register');
    });

    $router->group(["prefix" => "profile", 'middleware' => 'auth'],  function () use ($router) {
        $router->get('/', 'ProfileController@getProfile');
        $router->patch('/', 'ProfileController@updateProfile');
    });

    $router->group(["prefix" => 'users', 'middleware' => ['auth', 'isAdmin']], function () use ($router) {
        $router->get('/', 'UsersController@index');
        $router->post('/', 'UsersController@create');
        $router->get('/{id}', 'UsersController@read');
        $router->patch('/{id}', 'UsersController@update');
        $router->delete('/{id}', 'UsersController@delete');
    });
});
