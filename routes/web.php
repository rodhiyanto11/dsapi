<?php
date_default_timezone_set('Asia/Jakarta');
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
//for generate key
$router->get('/key',function() {
    return str_random(32);
});
$router->group(['prefix' => 'api'],function() use ($router){




    $router->group(['prefix' => 'admin'], function () use($router) {
        //USER
        $router->group(['prefix'=>'users'],function () use ($router){
            $router->get('/', 'UserController@index');
            $router->post('/', 'UserController@store');
            $router->get('/{id:[\d]+}', [
              
                'uses' => 'UserController@show'
            ]);
            $router->put('/{id:[\d]+}', 'UserController@update');
            $router->delete('/{id:[\d]+}', 'UserController@destroy');
        });




        
    });
    
    //AUTH
    $router->post('/login','AuthController@login');
    $router->post('/register','AuthController@register');
    $router->get('/profile/{id}','UserController@show');






});

