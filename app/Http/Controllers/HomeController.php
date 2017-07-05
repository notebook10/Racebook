<?php
namespace App\Http\Controllers;

use App\Bets;
use App\Horses;
use App\Timezone;
use Illuminate\Http\Request;
use App\User;
use App\Tracks;
use Auth;
use League\Flysystem\Exception;
use Validator;
use Theme;
use Illuminate\Support\Facades\Redirect;
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
//        dd($racingTracks);
        $data = [
            'tracks' => $racingTracks
        ];
//        return view('user/UserPage',$data);
        $theme = Theme::uses('default')->layout('layout')->setTitle('Dashboard');
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
        return $foo->name;
    }
    public function getServerTime(){
        date_default_timezone_set('America/Los_Angeles'); // Pacific
        $pdtDate = date('F d, Y h:i:s', time());
        $pdt = date('H:i:s', time());
        date_default_timezone_set('America/Denver'); // Mountain
        $mdt = date('H:i:s', time());
        date_default_timezone_set('America/Chicago'); // Central
        $cdt = date('H:i:s', time());
        date_default_timezone_set('America/New_York'); // Eastern
        $edt = date('H:i:s', time());
        $dateArray = [
            "dateTimePDT" => $pdtDate,
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
        $array = [
            'pdt' => date('h:i A',strtotime($pdt)),
            'mdt' => $mdt,
            'cdt' => $cdt,
            'edt' => $edt
        ];
//        dd($array);
        $pdtStart = date('g:i A', strtotime($pdt));
        $mdtStart = date('g:i A', strtotime($mdt));
        $cdtStart = date('g:i A', strtotime($cdt));
        $edtStart = date('g:i A', strtotime($edt));
        $arrayStart = [
            'pdtS' => $pdtStart,
            'mdtS' => $mdtStart,
            'cdtS' => $cdtStart,
            'edtS' => $edtStart
        ];
//        dd($arrayStart);
        $pdtEnd = date("g:i A", strtotime('+30 minutes', strtotime(date("H:i",strtotime($pdt)))));
        $mdtEnd = date("g:i A", strtotime('+30 minutes', strtotime(date("H:i",strtotime($mdt)))));
        $cdtEnd = date("g:i A", strtotime('+30 minutes', strtotime(date("H:i",strtotime($cdt)))));
        $edtEnd = date("g:i A", strtotime('+30 minutes', strtotime(date("H:i",strtotime($edt)))));
//        dd($pdtEnd);
        $pdtResults = $horsesModel->getUpcomingRaces($date,$pdtStart,$pdtEnd);
        $mdtResults = $horsesModel->getUpcomingRaces($date,$mdtStart,$mdtEnd);
        $cdtResults = $horsesModel->getUpcomingRaces($date,$cdtStart,$cdtEnd);
        $edtResults = $horsesModel->getUpcomingRaces($date,$edtStart,$edtEnd);
//        echo $pdt . " " . $pdtStart . " " . $pdtEnd;
        $pdtArr = [];
        $mdtArr = [];
        $cdtArr = [];
        $edtArr = [];
        foreach ($pdtResults as $key => $val){
            $timezone = HomeController::getTimezone($val->race_track);
            $trackname = HomeController::getTrack($val->race_track);
            $to = "";
            if($timezone === "PDT"){
                $to = strtotime($pdt);
//                $mtp = round(abs($to - strtotime($val->race_time)) / 60,2);
                $mtp = round((strtotime($val->race_time) - $to) / 60);
                if(in_array($val->race_track . "|" . trim($trackname) . "@" . $mtp . "&" . trim($val->race_number) , $pdtArr, TRUE)){
                }else{
                    array_push($pdtArr, $val->race_track . "|" . trim($trackname) . "@" . $mtp . "&" . trim($val->race_number) );
                }
            }
        }
        foreach ($mdtResults as $key => $val){
            $timezone = HomeController::getTimezone($val->race_track);
            $trackname = HomeController::getTrack($val->race_track);
            $to = "";
            if($timezone === "MDT"){
                $to = strtotime($mdt);
//                $mtp = round(abs($to - strtotime($val->race_time)) / 60,2);
                $mtp = round((strtotime($val->race_time) - $to) / 60);
                if(in_array($val->race_track . "|" . trim($trackname) . "@" . $mtp . "&" . trim($val->race_number) , $mdtArr, TRUE)){
                }else{
                    array_push($mdtArr, $val->race_track . "|" . trim($trackname) . "@" . $mtp . "&" . trim($val->race_number) );
                }
            }
        }
        foreach ($cdtResults as $key => $val){
            $timezone = HomeController::getTimezone($val->race_track);
            $trackname = HomeController::getTrack($val->race_track);
            $to = "";
            if($timezone === "CDT"){
                $to = strtotime($cdt);
//                $mtp = round(abs($to - strtotime($val->race_time)) / 60,2);
                $mtp = round((strtotime($val->race_time) - $to) / 60);
                if(in_array($val->race_track . "|" . trim($trackname) . "@" . $mtp . "&" . trim($val->race_number) , $cdtArr, TRUE)){
                }else{
                    array_push($cdtArr, $val->race_track . "|" . trim($trackname) . "@" . $mtp . "&" . trim($val->race_number) );
                }
            }
        }
        foreach ($edtResults as $key => $val){
            $timezone = HomeController::getTimezone($val->race_track);
            $trackname = HomeController::getTrack($val->race_track);
            $to = "";
            if($timezone === "EDT"){
                $to = strtotime($edt);
//                $mtp = round(abs($to - strtotime($val->race_time)) / 60,2);
                $mtp = round((strtotime($val->race_time) - $to) / 60);
                if(in_array($val->race_track . "|" . trim($trackname) . "@" . $mtp . "&" . trim($val->race_number) , $edtArr, TRUE)){
                }else{
                    array_push($edtArr, $val->race_track . "|" . trim($trackname) . "@" . $mtp . "&" . trim($val->race_number) );
                }
            }
        }
        // Array merge here
        $mergedArray = array_merge($pdtArr,$edtArr,$mdtArr, $cdtArr);
        return $mergedArray;
    }
//    public function appendUpcomingRaces(Request $request){
//        $pacificTime = $request->input("pacific");
//        $mtp = strtotime('',$request->input("raceTime"));
//        $datetime1 = strtotime("2011-10-10 10:00:00");
//        $datetime2 = strtotime("2011-10-10 10:45:00");
//        $interval  = abs($datetime2 - $datetime1);
//        $minutes   = round($interval / 60);
//        return "Asdad";
//    }
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
        $postTime = $request->input('postTime');
        $trackCode = $request->input('trk');
        $trackTimeZone = HomeController::getTimezone($trackCode);
        $time = $this->getServerTime();
        $timeZoneTime = $time[strtolower($trackTimeZone)];
        $foo = date("g:i A",strtotime($timeZoneTime));
//        dd(date("h:i A",strtotime($timeZoneTime)));
//        echo $postTime . " " . $foo . " " . $trackTimeZone . " ";
        $variable = "";
        if(strtotime($postTime) < strtotime(date("g:i A",strtotime($timeZoneTime)))){
            $variable = "lt"; // race finished
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
}
