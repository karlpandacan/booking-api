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

/** Authentication & Authorization */
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');
$router->post('/register', 'AuthController@register');
$router->get('/token-check', 'AuthController@tokenCheck');

$router->get('/guest/bookings', 'BookingController@index');
$router->group(['middleware' => 'auth'], function () use ($router) {
    /** Bookings */
    $router->get('/bookings', 'BookingController@index');
    $router->get('/bookings/{booking}', 'BookingController@show');
    $router->post('/bookings', 'BookingController@store');
    $router->put('/bookings/{booking}', 'BookingController@update');
    $router->delete('/bookings/{booking}', 'BookingController@destroy');
    $router->get('/rooms', 'BookingController@availableRoom');
});


