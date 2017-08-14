<?php

Route::get('/','HomeController@index')->name('/');
Route::post('login','HomeController@login');
Route::get('register','HomeController@register');
Route::post('insertuser','HomeController@insertuser');
Route::get('logout','HomeController@logout');
Route::match(array('GET','POST'),'test',function(){
    return "<a href='dashboard'>". strtolower("TESTING") ."</a>";
});
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
    Route::match(array('GET','POST'),'dashboard/checkPostTime','HomeController@checkPostTime');
    Route::get('dashboard/past','HomeController@past');
    Route::get('dashboard/pending','HomeController@pending');
    Route::post('dashboard/validateTrackTmz','HomeController@validateTrackTmz');
    Route::post('dashboard/getTrackCode','HomeController@getTrackCode');
    Route::post('dashboard/getWagerForRace','HomeController@getWagerForRace');
    Route::get('dashboard/checkIfOpen','HomeController@checkIfOpen');
    Route::post('dashboard/getMinimum','HomeController@getMinimum');
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
        Route::post('submitResults','AdminController@submitResults');
        Route::post('checkResults','AdminController@checkResults');
        Route::post('getLatestResultID','AdminController@getLatestResultID');
        Route::post('getWagerForRace','HomeController@getWagerForRace');
        Route::post('saveMinimum','AdminController@saveMinimum');
        Route::post('checkMinimum','AdminController@checkMinimum');
        Route::post('checkPayout','AdminController@checkPayout');
        Route::post('scratchBets','AdminController@scratchBets');
        Route::post('getTracksForNewTrack','AdminController@getAllTracksWithoutToday');
        Route::post('submitNewTrack','AdminController@submitNewTrack');
        Route::post('getTracksToday','AdminController@getTracksToday');
        Route::post('submitHorse','AdminController@submitHorse');
        Route::post('submitNewWager','AdminController@submitNewWager');
        Route::post('getHorseData','AdminController@getHorseData');
        Route::post('getWagerByRace','AdminController@getWagerByRace');
        Route::post('submitNewBet','AdminController@submitNewBet');
        Route::post('undoScratch','AdminController@undoScratch');
        Route::post('removeTrack','AdminController@removeTrack');
        Route::post('showTemp','AdminController@showTemp');
        Route::post('cancelWager','AdminController@cancelWager');
        Route::post('noShow','AdminController@noShow');
        Route::post('checkCancelled','AdminController@checkCancelled');
        Route::post('getTracksWithDate','AdminController@getTracksWithDate');
        Route::post('login',function(){
            return "test";
        });
    });
});