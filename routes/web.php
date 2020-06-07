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

$router->get('/user', 'UserController@show');
$router->put('/user', 'UserController@save');
$router->get('/user/stats', 'UserController@stats');
$router->get('/user/leaderboard', 'UserController@leaderboard');
$router->post('/user/score', 'UserController@score');
$router->get('/leaderboard', 'ScoreController@leaderboard');
$router->get('/type', 'ScoreTypeController@showAll');
