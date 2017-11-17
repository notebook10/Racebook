<?php
Route::match(array('GET','POST'),'/',function(\Illuminate\Http\Request $request){
    if (!isset($_SESSION)) session_start();
    if(isset($_POST["username"])){
        $_SESSION["username"] = $_POST["username"];
        $url = $_POST["url"];
        // Determine DSN
        if(strpos($url,'webbet') !== false){
            $_SESSION["dsn"] = "webbet";
        }elseif(strpos($url,'floyd') !== false){
            $_SESSION["dsn"] = "floyd";        // FLOYD
        }elseif(strpos($url,'123confirm') !== false){
            $_SESSION["dsn"] = "nanc";
        }elseif(strpos($url,'myqualitychoice') !== false){
            $_SESSION["dsn"] = "mqc";
        }elseif(strpos($url,'abconfirm') !== false){
            $_SESSION["dsn"] = "abc";
        }elseif(strpos($url,'myoptions123') !== false){
            $_SESSION["dsn"] = "myoptions";
        }elseif(strpos($url,'backdoorbets') !== false){
            $_SESSION["dsn"] = "backdoor";
        }elseif(strpos($url,'daveyk') !== false){
            $_SESSION["dsn"] = "daveyk";
        }elseif(strpos($url,'luckspeed') !== false){
            $_SESSION["dsn"] = "luck";
        }elseif(strpos($url,'playlowpro') !== false){
            $_SESSION["dsn"] = "lowpro";
        }elseif(strpos($url,'duckhook365') !== false){
            $_SESSION["dsn"] = "duckhook";
        }
        echo $_SESSION["username"] . " " . $_SESSION["dsn"];
        return redirect()->action("HomeController@dashboard");
    }else{
        return redirect()->action("HomeController@dashboard");
    }
})->name('/');
//Route::any('/','HomeController@index')->name('/');
Route::get('admin2','HomeController@index');
Route::post('login','HomeController@login')->name('login');
Route::get('register','HomeController@register');
Route::post('insertuser','HomeController@insertuser');
Route::get('logout','HomeController@logout');
Route::match(array('GET','POST'),'test',function(){
    return "<a href='dashboard'>". strtolower("TESTING") ."</a>";
});
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
Route::get('dashboard/weekly','HomeController@weekly');
Route::post('dashboard/getWeek','HomeController@getWeek');
Route::post('dashboard/past/getWeek','HomeController@getWeek');
Route::post('dashboard/getPendingHome','AdminController@getPendingBetsHome');
Route::post('dashboard/getPastHome','AdminController@getPastHome');
Route::post('dashboard/balanceInquiry','AdminController@balanceInquiry');
Route::post('dashboard/updateCurrentBet','AdminController@updateCurrentBet');
Route::get('dashboard/results','HomeController@displayResults');
Route::post('dashboard/getTracksByDate','HomeController@getTracksByDate');
Route::post('dashboard/getResultsForDisplay','HomeController@getResultsForDisplay');
Route::group(['middleware' => ['auth']],function(){
    // User
//    Route::get('dashboard','HomeController@dashboard');
//    Route::get('dashboard/logout','HomeController@logout');
//    Route::post('dashboard/getRaces','HomeController@getRaces');
//    Route::post('dashboard/getHorsesPerRace','HomeController@getHorsesPerRace');
//    Route::post('dashboard/getRaceTime','HomeController@getRaceTime');
//    Route::post('dashboard/getRaceTimeNew','HomeController@getRaceTimeNew');
//    Route::post('dashboard/saveBet','HomeController@saveBet');
//    Route::post('dashboard/insertBets','HomeController@insertBets');
//    Route::post('dashboard/getTrackName','HomeController@getTrackName');
//    Route::post('dashboard/getServerTime','HomeController@getServerTime');
//    Route::post('dashboard/getUpcomingRaces','HomeController@getUpcomingRaces');
//    Route::match(array('GET','POST'),'dashboard/appendUpcomingRaces','HomeController@appendUpcomingRaces');
//    Route::match(array('GET','POST'),'dashboard/checkPostTime','HomeController@checkPostTime');
//    Route::get('dashboard/past','HomeController@past');
//    Route::get('dashboard/pending','HomeController@pending');
//    Route::post('dashboard/validateTrackTmz','HomeController@validateTrackTmz');
//    Route::post('dashboard/getTrackCode','HomeController@getTrackCode');
//    Route::post('dashboard/getWagerForRace','HomeController@getWagerForRace');
//    Route::get('dashboard/checkIfOpen','HomeController@checkIfOpen');
//    Route::post('dashboard/getMinimum','HomeController@getMinimum');
//    Route::get('dashboard/weekly','HomeController@weekly');
//    Route::post('dashboard/getWeek','HomeController@getWeek');
//    Route::post('dashboard/past/getWeek','HomeController@getWeek');
//    Route::post('dashboard/getPendingHome','AdminController@getPendingBetsHome');
//    Route::post('dashboard/getPastHome','AdminController@getPastHome');
//    Route::post('dashboard/balanceInquiry','AdminController@balanceInquiry');
//    Route::post('dashboard/updateCurrentBet','AdminController@updateCurrentBet');
    // Admin
    Route::group(['prefix' => 'admin'],function(){
        Route::get('dashboard','AdminController@dashboard');
//        Route::get('logout','HomeController@logout');
        Route::get('logout',function(){
            \Illuminate\Support\Facades\Auth::logout();
//            return "Logged Out!";
            return redirect('admin2');
        });
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
        Route::post('getWagerForRaceAdmin','AdminController@getWagerForRaceAdmin'); // changed to adminController from homeController
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
        Route::post('getBetInfo','AdminController@getBetInfo');
        Route::get('pendingBets','AdminController@pendingBets');
        Route::post('getPastBets','AdminController@getPastBets');
        Route::post('getPendingBets','AdminController@getPendingBets');
        Route::any('odbc','AdminController@testODBC');
        Route::get('scratches','AdminController@scratches');
        Route::post('getScratchesToday','AdminController@getScratchesToday');
    });
});