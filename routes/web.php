<?php

Route::get('/','HomeController@index');
Route::post('login','HomeController@login');
Route::get('register','HomeController@register');
Route::post('insertuser','HomeController@insertuser');
Route::get('logout','HomeController@logout');

Route::group(['middleware' => ['auth']],function(){
    // User
    Route::get('dashboard','HomeController@dashboard');
    Route::get('dashboard/logout','HomeController@logout');
    Route::post('dashboard/getRaces','HomeController@getRaces');
    Route::post('dashboard/getHorsesPerRace','HomeController@getHorsesPerRace');
    Route::post('dashboard/getRaceTime','HomeController@getRaceTime');
    Route::post('dashboard/getRaceTimeNew','HomeController@getRaceTimeNew');
    Route::post('dashboard/saveBet','HomeController@saveBet');
    Route::post('dashboard/insertBets','HomeController@insertBets');
    Route::post('dashboard/getTrackName','HomeController@getTrackName');
    Route::post('dashboard/getServerTime','HomeController@getServerTime');
    Route::post('dashboard/getUpcomingRaces','HomeController@getUpcomingRaces');
    Route::match(array('GET','POST'),'dashboard/appendUpcomingRaces','HomeController@appendUpcomingRaces');
    Route::post('dashboard/checkPostTime','HomeController@checkPostTime');
    Route::get('dashboard/past','HomeController@past');
    Route::get('dashboard/pending','HomeController@pending');
    Route::post('dashboard/validateTrackTmz','HomeController@validateTrackTmz');
    Route::post('dashboard/getTrackCode','HomeController@getTrackCode');
    Route::post('dashboard/getWagerForRace','HomeController@getWagerForRace');
    // Admin
    Route::group(['prefix' => 'admin'],function(){
        Route::get('dashboard','AdminController@dashboard');
        Route::get('logout','HomeController@logout');
        Route::get('tracks','AdminController@tracks');
        Route::get('timezones','AdminController@timezones');
        Route::post('getTmzValues','AdminController@getTmzValues');
        Route::post('submitTmz','AdminController@submitTmz');
        Route::get('horses','AdminController@horses');
        Route::post('scratch','AdminController@scratch');
        Route::get('wager','AdminController@wager');
        Route::get('bets','AdminController@bets');
        Route::get('results','AdminController@results');
        Route::post('getRaces','HomeController@getRaces');
        Route::post('getBets','AdminController@getBets');
    });
});