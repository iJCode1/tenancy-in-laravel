<?php

use Illuminate\Support\Facades\Route;

// Solicitada para quitar el error
use Illuminate\Support\Facades\Auth;


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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Forma 2 de crear las rutas de los inquilinos
// $domain = sprintf("%s.%s", "{account}", env("APP_DOMAIN"));
// Route::domain($domain)->group(function (){
//     Route::get('users/{id}', function ($account, $id){
//         dd($account);
//     });
// });