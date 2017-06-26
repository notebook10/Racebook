<?php

namespace App\Http\Controllers;

use App\Bets;
use App\Horses;
use Illuminate\Http\Request;
use App\User;
use App\Tracks;
use Auth;
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
        return $theme->of('user/UserPage',$data)->render();
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
        $dataArray = [
            'bettype' => $request->input("bettype"),
            'track' => $request->input("track"),
            'raceNum' => $request->input("raceNum"),
            'racePost' => $request->input("racePost"),
            'betamount' => $request->input("betamount"),
            'bet' => json_encode($request->input("bet")),
            'user' => Auth::user()->id
        ];
        $model = new Bets();
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
        $pdt = date('h:i:s', time());
        date_default_timezone_set('America/Denver'); // Mountain
        $mdt = date('h:i:s', time());
        date_default_timezone_set('America/Chicago'); // Central
        $cdt = date('h:i:s', time());
        date_default_timezone_set('America/New_York'); // Eastern
        $edt = date('h:i:s', time());
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
        $pdtStart = date('h:i A', strtotime($pdt));
        $pdtEnd = strtotime("+30 minutes",strtotime($pdt));
        $mdtEnd = strtotime("+30 minutes",strtotime($mdt));
        $cdtEnd = strtotime("+30 minutes",strtotime($cdt));
        $edtEnd = strtotime("+30 minutes",strtotime($edt));
        $pdtEndCvt = gmdate(" " . "h:i A" . " ", $pdtEnd);
        $pdtResults = $horsesModel->getUpcomingRaces($date,$pdtStart,$pdtEnd);
        $resultArr = [
            'pdtRes' => $pdtResults
        ];
        return $resultArr;
    }
    public function appendUpcomingRaces(Request $request){
        $pacificTime = $request->input("pacific");
        $mtp = strtotime('',$request->input("raceTime"));
        $datetime1 = strtotime("2011-10-10 10:00:00");
        $datetime2 = strtotime("2011-10-10 10:45:00");
        $interval  = abs($datetime2 - $datetime1);
        $minutes   = round($interval / 60);
        return "Asdad";
    }
}
