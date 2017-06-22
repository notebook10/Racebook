<?php

Route::get('/','HomeController@index');
Route::post('login','HomeController@login');
Route::get('register','HomeController@register');
Route::post('insertuser','HomeController@insertuser');
Route::get('logout','HomeController@logout');

Route::group(['middleware' => ['auth']],function(){
    // User
    Route::get('dashboard','HomeController@dashboard');
    Route::post('dashboard/getRaces','HomeController@getRaces');
    Route::post('dashboard/getHorsesPerRace','HomeController@getHorsesPerRace');
    Route::post('dashboard/getRaceTime','HomeController@getRaceTime');
    Route::post('dashboard/getRaceTimeNew','HomeController@getRaceTimeNew');
    Route::post('dashboard/saveBet','HomeController@saveBet');
    // Admin
    Route::group(['prefix' => 'admin'],function(){
        Route::get('dashboard','AdminController@dashboard');
        Route::get('logout','HomeController@logout');
    });
});