<?php
namespace App\Http\Controllers;

use App\Bets;
use App\Horses;
use App\Minimum;
use App\Timezone;
use App\Wager;
use Illuminate\Http\Request;
use App\User;
use App\Tracks;
use Auth;
use League\Flysystem\Exception;
use Validator;
use Theme;
use Illuminate\Support\Facades\Redirect;
use DateTime;
use DB;
class HomeController extends Controller
{
    public function index(){
        if(Auth::check()){
            $user = Auth::user();
            $direct = User::checkusertype($user->id);
            return Redirect::to($direct);
        }else{
            return view('default/login');
        }
    }
    public function login(Request $request){
        $username = $request->input('username');
        $password = $request->input('password');
        $data = [
            'username' => $username,
            'password' => $password
        ];
        $rules = [
            'username' => 'required|min:2',
            'password' => 'required|min:2'
        ];
        $validator = Validator::make($data,$rules);
        if($validator->fails()){
            return Redirect::to('/');
        }else{
            if(Auth::attempt(['username' => $username, 'password' => $password])){
                return Redirect::to('/');
            }else{
                return Redirect::to('/')
                    ->withErrors([
                        'validate' => 'Wrong Email or Password!',
                    ]);
            }
        }
    }
    public function insertuser(Request $request){
        $data = [
            'firstname' => $request->input('firstname'),
            'lastname' => $request->input('lastname'),
            'username' => $request->input('username'),
            'password' => $request->input('password'),
            'user_type' => $request->input('usertype')
        ];
        $user = new User();
        $newUser = $user->insertuser($data);
        return $newUser;
    }
    public function register(){
        return view('default/register');
    }
    public function logout(){
        Auth::logout();
        return Redirect::to('/');
    }
    public function dashboard(){
        date_default_timezone_set('America/Los_Angeles');
        $date = date('mdy',time());
        $tracks = new Tracks();
        $racingTracks = $tracks->getAllTracks($date);
        $raceTomorrow = $tracks->getShowTemp();
        $data = [
            'tracks' => $racingTracks,
            'tomorrow' => $raceTomorrow
        ];
//        return view('user/UserPage',$data);
        $theme = Theme::uses('default')->layout('layout')->setTitle('Racebook');
        return $theme->of('user/dashboard',$data)->render();
    }
    public function getRaces(Request $request){
        $tracks = new Horses();
        $races = $tracks->getRaces($request->input("code"), $request->input("date"));
        return $races;
    }
    public function getHorsesPerRace(Request $request){
        $Model = new Horses();
        $horses = $Model->getHorsesPerRace($request->input("code"),$request->input("date"),$request->input("number"));
        return $horses;
    }
    public function saveBet(Request $request){
        $model = new Bets();
        $dataArray = [
            'bettype' => $request->input("bettype"),
            'track' => $request->input("track"),
            'raceNum' => $request->input("raceNum"),
            'racePost' => $request->input("racePost"),
            'betamount' => $request->input("betamount"),
            'bet' => $request->input("value"),
            'type' => $request->input("wpsType"),
//            'user' => Auth::user()->id
        ];
        $temp = $model->saveBets($dataArray);
        return $temp;
    }
    public function getTrackName(Request $request){
        $model = new Tracks();
        $foo = $model->getTrackName($request->input("trk"));
        $tmz = HomeController::getTimezone($request->input("trk"));
        return [$foo->name,$tmz];
    }
    public function getServerTime(){
        date_default_timezone_set('America/Los_Angeles'); // Pacific
        $pdtDate = date('m/d/y h:i:s A', time());
        $pdt = date('H:i:s', time());
        date_default_timezone_set('America/Denver'); // Mountain
        $mdtDate = date('m/d/y h:i:s A', time());
        $mdt = date('H:i:s', time());
        date_default_timezone_set('America/Chicago'); // Central
        $cdt = date('H:i:s', time());
        $cdtDate = date('m/d/y h:i:s A', time());
        date_default_timezone_set('America/New_York'); // Eastern
        $edt = date('H:i:s', time());
        $edtDate = date('m/d/y h:i:s A', time());
        $dateArray = [
            "dateTimePDT" => $pdtDate,
            "dateTimeMDT" => $mdtDate,
            "dateTimeCDT" => $cdtDate,
            "dateTimeEDT" => $edtDate,
            "pdt" => $pdt,
            "mdt" => $mdt,
            "cdt" => $cdt,
            "edt" => $edt
        ];
        return $dateArray;
    }
    public function getUpcomingRaces(Request $request){
        $tracksModel = new Tracks();
        $horsesModel = new Horses();
        $date = $request->input("date");
        $tracksToday = $tracksModel->getAllTracks($date);
        $pdt = $request->input("pdt");
        $mdt = $request->input("mdt");
        $cdt = $request->input("cdt");
        $edt = $request->input("edt");
        $pdtStart = date('g:i A', strtotime($pdt));
        $mdtStart = date('g:i A', strtotime($mdt));
        $cdtStart = date('g:i A', strtotime($cdt));
        $edtStart = date('g:i A', strtotime($edt));
        $pdtEnd = date("g:i A", strtotime('+30 minutes', strtotime(date("H:i",strtotime($pdt)))));
        $mdtEnd = date("g:i A", strtotime('+30 minutes', strtotime(date("H:i",strtotime($mdt)))));
        $cdtEnd = date("g:i A", strtotime('+30 minutes', strtotime(date("H:i",strtotime($cdt)))));
        $edtEnd = date("g:i A", strtotime('+30 minutes', strtotime(date("H:i",strtotime($edt)))));
        $pdtResults = $horsesModel->getUpcomingRaces($date,$pdtStart,$pdtEnd);
        $mdtResults = $horsesModel->getUpcomingRaces($date,$mdtStart,$mdtEnd);
        $cdtResults = $horsesModel->getUpcomingRaces($date,$cdtStart,$cdtEnd);
        $edtResults = $horsesModel->getUpcomingRaces($date,$edtStart,$edtEnd);
        $pdtArr = [];
        $mdtArr = [];
        $cdtArr = [];
        $edtArr = [];
        foreach ($pdtResults as $key => $val){
            $visibility = HomeController::checkTrackVisibility($val->race_track,$date)->visibility;
            if($visibility == 0){
                $timezone = HomeController::getTimezone($val->race_track);
                if($timezone){
                    $trackname = HomeController::getTrack($val->race_track);
                    if($timezone === "PDT"){
                        $to = strtotime($pdt);
                        $time = strtotime($val->race_time) - (5 * 60);
                        $mtp = round(($time - $to) / 60);
                        if(in_array($val->race_track . "|" . trim($trackname) . "@" . $mtp . "&" . trim($val->race_number) . "/" . $val->race_time , $pdtArr, TRUE)){
                        }else{
                            array_push($pdtArr, $val->race_track . "|" . trim($trackname) . "@" . $mtp . "&" . trim($val->race_number) . "/" . $val->race_time );
                        }
                    }
                }else{

                }
            }
        }
        foreach ($mdtResults as $key => $val){
            $visibility = HomeController::checkTrackVisibility($val->race_track,$date)->visibility;
            if($visibility == 0){
                $timezone = HomeController::getTimezone($val->race_track);
                if($timezone){
                    $trackname = HomeController::getTrack($val->race_track);
                    if($timezone === "MDT"){
                        $to = strtotime($mdt);
                        $time = strtotime($val->race_time) - (5 * 60);
                        $mtp = round(($time - $to) / 60);
                        if(in_array($val->race_track . "|" . trim($trackname) . "@" . $mtp . "&" . trim($val->race_number) . "/" . $val->race_time  , $mdtArr, TRUE)){
                        }else{
                            array_push($mdtArr, $val->race_track . "|" . trim($trackname) . "@" . $mtp . "&" . trim($val->race_number) . "/" . $val->race_time  );
                        }
                    }
                }else{

                }
            }
        }
        foreach ($cdtResults as $key => $val){
            $visibility = HomeController::checkTrackVisibility($val->race_track,$date)->visibility;
            if($visibility == 0){
                $timezone = HomeController::getTimezone($val->race_track);
                if($timezone){
                    $trackname = HomeController::getTrack($val->race_track);
                    if($timezone === "CDT"){
                        $to = strtotime($cdt);
                        $time = strtotime($val->race_time) - (5 * 60);
                        $mtp = round(($time - $to) / 60);
                        if(in_array($val->race_track . "|" . trim($trackname) . "@" . $mtp . "&" . trim($val->race_number) . "/" . $val->race_time , $cdtArr, TRUE)){
                        }else{
                            array_push($cdtArr, $val->race_track . "|" . trim($trackname) . "@" . $mtp . "&" . trim($val->race_number) . "/" . $val->race_time  );
                        }
                    }
                }else{

                }
            }
        }
        foreach ($edtResults as $key => $val){
            $visibility = HomeController::checkTrackVisibility($val->race_track,$date)->visibility;
            if($visibility == 0){
                $timezone = HomeController::getTimezone($val->race_track);
                if($timezone){
                    $trackname = HomeController::getTrack($val->race_track);
                    if($timezone === "EDT"){
                        $to = strtotime($edt);
                        $time = strtotime($val->race_time) - (5 * 60);
                        $mtp = round(($time - $to) / 60);
                        if(in_array($val->race_track . "|" . trim($trackname) . "@" . $mtp . "&" . trim($val->race_number) . "/" . $val->race_time  , $edtArr, TRUE)){
                        }else{
                            array_push($edtArr, $val->race_track . "|" . trim($trackname) . "@" . $mtp . "&" . trim($val->race_number) . "/" . $val->race_time  );
                        }
                    }
                }else{

                }
            }
        }
        // Array merge here
        $mergedArray = array_merge($pdtArr,$edtArr,$mdtArr, $cdtArr);
        return $mergedArray;
    }
    public static function getTimezone($trackCode){
        $model = new Timezone();
        $foo = $model->getTimezoneByCode($trackCode);
        return $foo->time_zone;
    }
    public static function getTrack($trackCode){
        $model = new Timezone();
        $foo = $model->getTimezoneByCode($trackCode);
        return $foo->track_name;
    }
    public function checkPostTime(Request $request){
        $date = $request->input("date");
        $postTime = $request->input('postTime');
        $trackCode = $request->input('trk');
        $trackTimeZone = HomeController::getTimezone($trackCode);
        $time = $this->getServerTime();
//        $timeZoneTime = $time[strtolower($trackTimeZone)];
//        if(strtotime($postTime) < strtotime(date("g:i A",strtotime($timeZoneTime)))){
//            $variable = "lt"; // race finished
//        }else{
//            $variable = "gt"; // ok
//        }
//        return $variable;
        $timeZoneTime = $time["dateTime".strtoupper($trackTimeZone)];
        $convertTemp = date("m/d/y h:i A",strtotime($timeZoneTime));
        $dateSlashed = substr($date,0,2) . "/" . substr($date,2,2) . "/" . substr($date,4,2);
        $fiveMinutesEarly = strtotime($dateSlashed . " " . $postTime) - (5 * 60);
        if( $fiveMinutesEarly < strtotime($convertTemp)){
            $variable = "lt"; // close
        }else{
            $variable = "gt"; // ok
        }
        return $variable;
    }
    public function insertBets(Request $request){
        $betsModel = new Bets();
        $betsModel->insertBets($request->input("dataArray"));
        return "0";
    }
    public function past(){
        $betsModel = new Bets();
        $data = [
            'history' => $betsModel->getAllBets(Auth::id())
        ];
        $theme = Theme::uses('default')->layout('layout')->setTitle('History');
        return $theme->of('user/history', $data)->render();
    }
    public function pending(){
        $betsModel = new Bets();
        $data = [
            'pending' => $betsModel->getPendingBets(Auth::id())
        ];
        $theme = Theme::uses('default')->layout('layout')->setTitle('Pending');
        return $theme->of('user/pending', $data)->render();
    }
    public function validateTrackTmz(Request $request){
        $trkCode = $request->input("code");
        $tmzModel = new Timezone();
        $result = $tmzModel->getTimezoneByCode($trkCode);
        if($result == null){
            return null;
        }else{
            echo $result->time_zone;
        }
    }
    public function getTrackCode(Request $request){
        $trkName = $request->input("name");
        $raceNum = $request->input("raceNum");
        $date = $request->input("date");
        $tmzModel = new Timezone();
        $horsesModel = new Horses();
        // GET RACE POST TIME
        $trkCode = $tmzModel->getTrkCodeByName($trkName);
        $firstRacePostTime = $horsesModel->getRaceTime($trkCode->track_code,$date,"Race " . $raceNum);
        $dataArray = [
            'horses' => $horsesModel->getHorsesPerRace($trkCode->track_code,$date,$raceNum),
            'trkCode' => $trkCode->track_code,
            'firstRacePostTime' => $firstRacePostTime->race_time
        ];
        return $dataArray;
    }
    public function getWagerForRace(Request $request){
        $wagerModel = new Wager();
        $wager = $wagerModel->getWagerForRace($request->input('trk'),$request->input('num'),$request->input('date'));
        return unserialize($wager->extracted);
    }
    public function checkIfOpen(Request $request){
        $date = $request->input("date");
        $postTimeArr = $request->input("postTime");
        $trkCode = $request->input("trk");
        $trackTimeZone = HomeController::getTimezone($trkCode);
        $time = $this->getServerTime();
        $timeZoneTime = $time["dateTime".strtoupper($trackTimeZone)];
        $convertTemp = date("m/d/y h:i A",strtotime($timeZoneTime));
        $resArr = [];
        $dateSlashed = substr($date,0,2) . "/" . substr($date,2,2) . "/" . substr($date,4,2);
        foreach ($postTimeArr as $key => $val){
            $time_FiveMinutesEarly =  strtotime($dateSlashed . " " . $postTimeArr[$key]) - (5 * 60);
            if( $time_FiveMinutesEarly < strtotime($convertTemp)){
                array_push($resArr, "lt"); // close
            }else{
                array_push($resArr, "gt"); // ok
            }
//            if(strtotime($dateSlashed . " " . $postTimeArr[$key]) < strtotime($convertTemp)){
//                array_push($resArr, "lt"); // close
//            }else{
//                array_push($resArr, "gt"); // ok
//            }
        }
        return $resArr;
    }
    public function getMinimum(Request $request){
        $minimumModel = new Minimum();
        $arr = [
            "trk" => $request->input("trk"),
            "date" => $request->input("date")
        ];
        if($minimumModel->getMinimum($arr)){
            return response()->json(json_decode($minimumModel->getMinimum($arr)->content));
        }else{
            return 1;
        }

    }
    public static function checkTrackVisibility($trkCode,$date){
        return DB::table("tracks")
            ->where("code",$trkCode)
            ->where("date",$date)
            ->first();
    }
}
