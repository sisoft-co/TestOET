<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware'=>['guest']],function(){
    Route::get('/','Auth\LoginController@showLoginForm');
    Route::post('/login', 'Auth\LoginController@login')->name('login');
    Route::get('/login', 'Auth\LoginController@showLoginForm');
});

//Route::get('error', function(){abort(500);});

Route::group(['middleware'=>['auth']],function(){
    
    Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
    Route::get('/dashboard', 'DashboardController');
    
    Route::post('/notification/get', 'NotificationController@get'); 
    
    Route::get('/main', function () { return view('contenido/contenido'); })->name('main');

    Route::group(['middleware' => ['Operador']], function () {
        Route::put('/user/updateAuthUserPassword', 'UserController@updateAuthUserPassword');
        // Add new Routes
    });

    Route::group(['middleware' => ['Administrador']], function () {

        Route::get('/user', 'UserController@index');
        Route::post('/user/registrar', 'UserController@store');
        Route::put('/user/actualizar', 'UserController@update');
        Route::delete('/user/eliminar/{id}', 'UserController@destroy');
        Route::put('/user/updateAuthUserPassword', 'UserController@updateAuthUserPassword');

        Route::get('/vehiculo', 'VehiculoController@index');
        Route::get('/vehiculo/selectPersonas', 'VehiculoController@selectPersonas');


        Route::get('/rol', 'RolController@index');
        Route::get('/rol/selectRol', 'RolController@selectRol');
    });

});
