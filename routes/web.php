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

$router->group(['middleware' => 'cors', 'prefix' => '/api'], function() use ($router) {
    $router->options('{all:.*}', [function() {
        return response('', 200);
    }]);
    $router->post('/users/login', 'UsersController@login');
    $router->post('/users/register', 'UsersController@register');
    $router->get('/genres', 'GenresController@index');
    $router->get('/genres/{id}', 'GenresController@getGenre');
    $router->get('/movies', 'MoviesController@index');
    $router->get('/movies/{id}', 'MoviesController@getMovie');
    $router->get('/movies/{id}/with/genre', 'MoviesController@getMovieWithGenre');

    $router->group(['middleware' => 'auth'], function ($router) {
        $router->get('/users', 'UsersController@index');
        $router->get('/users/logout', 'UsersController@logout');
        $router->get('/users/{id}', 'UsersController@userMovie');
        $router->post('/movies/add', 'MoviesController@store');
        $router->delete('/movies/{id}', 'MoviesController@destroy');
        $router->put('/movies/{id}', 'MoviesController@edit');
        $router->patch('/movies/{id}/like', 'MoviesController@setLike');
        $router->post('/genres/add', 'GenresController@store');
        $router->delete('/genres/{id}', 'GenresController@destroy');
        $router->put('/genres/{id}', 'GenresController@edit');
    });
});