<?php

namespace App\Http\Controllers;

use App\Bets;
use App\Horses;
use App\Results;
use App\Timezone;
use App\Tracks;
use App\Wager;
use Illuminate\Http\Request;
use Theme;
use Auth;
use DB;
class AdminController extends Controller
{
    public function dashboard(){
        if(Auth::check()){
            $theme = Theme::uses('admin')->layout('layout')->setTitle('Admin');
            return $theme->of('admin/dashboard')->render();
        }else{
            return view('default/login');
        }
    }
    public function tracks(){
        date_default_timezone_set('America/Los_Angeles');
        $betsModel = new Tracks();
        $tracks = $betsModel->getAllTracks(date('mdy',time()));
        $dataArray = [
            'tracks' => $tracks
        ];
        $theme = Theme::uses('admin')->layout('layout')->setTitle('Admin');
        return $theme->of('admin/tracks', $dataArray)->render();
    }
    public function timezones(){
        date_default_timezone_set('America/Los_Angeles');
        $tracksModel = new Tracks();
        $timezoneModel = new Timezone();
        $tracksWithTimezone = $tracksModel->getTrackWithTimeZone(date('mdy',time()));
        $dataArray = [
            'tracksAndTimezone' => $tracksWithTimezone
        ];
        $theme = Theme::uses('admin')->layout('layout')->setTitle('Track Timezones');
        return $theme->of('admin/timezones', $dataArray)->render();
    }
    public function getTmzValues(Request $request){
        $id = $request->input("id");
        $tmzModel = new Timezone();
        $resultsRow = $tmzModel->getTimezoneByID($id);
        $dataArray = [
            'name' => $resultsRow->track_name,
            'code' => $resultsRow->track_code,
            'tmz' => $resultsRow->time_zone ? $resultsRow->time_zone : "" ,
            'id' => $resultsRow->id
        ];
        return $dataArray;
    }
    public function submitTmz(Request $request){
        $operation = $request->input("operation");
        $tmzModel = new Timezone();
        $id = $request->input("id");
        if($operation == 1){
            // EDIT
            $dataArray = ['time_zone' => $request->input("selectTmz")];
            $tmzModel->updateTimezoneById($id,$dataArray);
            return 1;
        }else if($operation == 0){
            // ADD
            $dataArray = [
                'time_zone' => $request->input("selectTmz"),
                'track_name' => $request->input("name"),
                'track_code' => $request->input("code")
            ];
            $tmzModel->saveTimezone($dataArray);
            return 0;
        }
    }
    public function horses(){
        date_default_timezone_set('America/Los_Angeles');
        $currentDate = date('mdy',time());
//        $currentDate = "070317";
        $horsesModel = new Horses();
        $dataArray = [
            'horses' => $horsesModel->getHorsesByDate($currentDate)
        ];
        $theme = Theme::uses('admin')->layout('layout')->setTitle('Horses');
        return $theme->of('admin/horses', $dataArray)->render();
    }
    public function scratch(Request $request){
        $id = $request->input("id");
        $horsesModel = new Horses();
        $arr = ['pp' => 'SCRATCHED'];
        return $horsesModel->scratch($id,$arr);
    }
    public function wager(){
        $wagerModel = new Wager();
        $dataArray = [
            'wager' => $wagerModel->getAllWager()
        ];
        $theme = Theme::uses('admin')->layout('layout')->setTitle('WAGER');
        return $theme->of('admin/wager', $dataArray)->render();
    }
    public function bets(){
        $betsModel = new Bets();
        $dataArray = [
            'betsToday' => $betsModel->getAll()
        ];
        $theme = Theme::uses('admin')->layout('layout')->setTitle('BETS');
        return $theme->of('admin/bets', $dataArray)->render();
    }
    public function results(){
        date_default_timezone_set('America/Los_Angeles');
        $tracksModel = new Tracks();
        $dataArray = [
            'tracks' => $tracksModel->getAllTracks(date('mdy',time()))
        ];
        $theme = Theme::uses('admin')->layout('layout')->setTitle('RESULTS');
        return $theme->of('admin/results', $dataArray)->render();
    }
    public function getBets(Request $request){
        $date = $request->input("date");
        $betsModel = new Bets();
        return $betsModel->getBetsByDate($date);
    }
    public function submitResults(Request $request){
        date_default_timezone_set('America/Los_Angeles');
        $dataArray = [
            'track_code' => $request->input("tracksToday"),
            'race_number' => $request->input("racePerTrack"),
            'first' => $request->input("first"),
            'second' => $request->input("second"),
            'third' => $request->input("third"),
            'fourth' => $request->input("fourth"),
            'race_date' => date('mdy', time())
        ];
        $resultModel = new Results();
        return $resultModel->insertResult($dataArray,$request->input('operation'));
    }
    public function checkResults(Request $request){
        $resultModel = new Results();
        $results = $resultModel->checkResults($request->input("trkCode"), $request->input("raceDate"), $request->input("raceNum"));
        return $results ? $results->race_winners : "";
    }
    public function getLatestResultID(Request $request){
        $resultModel = new Results();
        $betsModel = new Bets();
        $latestRecord = $resultModel->getLatestResult($request->input("lastId"));
        $winningCombination = $latestRecord->first()->race_winners;
        $trkCode = $latestRecord->first()->track_code;
        $raceNum = $latestRecord->first()->race_number;
        $raceDate = $latestRecord->first()->race_date;
        // Refresh
        AdminController::refreshResultsForRegrade($raceDate,$trkCode,$raceNum);
        $explode = explode(",",$winningCombination);
        $exacta = $explode[0] . "," . $explode[1];
        $trifecta = $explode[0] . "," . $explode[1] . "," . $explode[2];
        $superfecta = $winningCombination;
        $w = $explode[0];
        $p = [$explode[0],$explode[1]];
        $execExacta = $betsModel->checkWinners($trkCode,$raceDate,$raceNum,$exacta,"exacta");
        $execExactaBox = $betsModel->checkWinners($trkCode,$raceDate,$raceNum,$exacta,"exactabox");
        $execTrifecta = $betsModel->checkWinners($trkCode,$raceDate,$raceNum,$trifecta,"trifecta");
        $execTrifectaBox = $betsModel->checkWinners($trkCode,$raceDate,$raceNum,$trifecta,"trifectabox");
        $execSuperfecta = $betsModel->checkWinners($trkCode,$raceDate,$raceNum,$superfecta,"superfecta");
        $execW = $betsModel->checkWps($trkCode,$raceDate,$raceNum,$w,"wps","w");
        $exeP1 = $betsModel->checkWps($trkCode,$raceDate,$raceNum,$p[0],"wps","p");
        $exeP2 = $betsModel->checkWps($trkCode,$raceDate,$raceNum,$p[1],"wps","p");
        $exeS1 = $betsModel->checkWps($trkCode,$raceDate,$raceNum,$explode[0],"wps","s");
        $exeS2 = $betsModel->checkWps($trkCode,$raceDate,$raceNum,$explode[1],"wps","s");
        $exeS3 = $betsModel->checkWps($trkCode,$raceDate,$raceNum,$explode[2],"wps","s");
        $ddSecondRaceRes = $resultModel->getSecondRaceRes($trkCode,$raceNum,$raceDate);
        if(empty($ddSecondRaceRes)){

        }else{
            $secondRaceWinner = explode(",",$ddSecondRaceRes->race_winners);
            $ddCombination = $explode[0] . "," . $secondRaceWinner[0];
            $exeDD = $betsModel->checkWinners($trkCode,$raceDate,$raceNum,$ddCombination,"dailydouble");
            foreach ($exeDD as $key => $value){
                AdminController::updateBetStatus($value->id);
            }
        }
//        if($ddSecondRaceRes == ""){
//
//        }else{
//            $secondRaceWinner = explode(",",$ddSecondRaceRes->race_winners);
//            $ddCombination = $explode[0] . "," . $secondRaceWinner[0];
//            $exeDD = $betsModel->checkWinners($trkCode,$raceDate,$raceNum,$ddCombination,"dailydouble");
//            foreach ($exeDD as $key => $value){
//                AdminController::updateBetStatus($value->id);
//            }
//        }

        // Set Results -------------------------------------------------------------------------------------------------
        foreach ($execExacta as $key => $value){
            AdminController::updateBetStatus($value->id);
        }
        foreach ($execExactaBox as $key => $value){
            AdminController::updateBetStatus($value->id);
        }
        foreach ($execTrifecta as $key => $value){
            AdminController::updateBetStatus($value->id);
        }
        foreach ($execTrifectaBox as $key => $value){
            AdminController::updateBetStatus($value->id);
        }
        foreach ($execSuperfecta as $key => $value){
            AdminController::updateBetStatus($value->id);
        }
        foreach ($execW as $key => $value){
            AdminController::updateBetStatus($value->id);
        }
        foreach ($exeP1 as $key => $value){
            AdminController::updateBetStatus($value->id);
        }
        foreach ($exeP2 as $key => $value){
            AdminController::updateBetStatus($value->id);
        }
        foreach ($exeS1 as $key => $value){
            AdminController::updateBetStatus($value->id);
        }
        foreach ($exeS2 as $key => $value){
            AdminController::updateBetStatus($value->id);
        }
        foreach ($exeS3 as $key => $value){
            AdminController::updateBetStatus($value->id);
        }
        // Set Defeat
        AdminController::gradeWrongBets($raceDate,$trkCode,$raceNum);
    }
    public static function updateBetStatus($id){
        $dataArray = [
            "status" => 1,
            "result" => 1 // 1 for win
        ];
        return DB::table("bets")
            ->where("id", $id)
            ->update($dataArray);
    }
    public static function gradeWrongBets($raceDate,$trackCode, $raceNum){
        $dataArray = [
            "status" => 1,
            "result" => 2 // 2 for defeat
        ];
        return DB::table("bets")
            ->where("race_track",$trackCode)
            ->where("race_date",$raceDate)
            ->where("race_number", $raceNum)
            ->where("status",0)
            ->update($dataArray);

    }
    public static function refreshResultsForRegrade($raceDate,$trackCode, $raceNum){
        $dataArray = [
            "status" => 0,
            "result" => 0
        ];
        return DB::table("bets")
            ->where("race_track",$trackCode)
            ->where("race_date",$raceDate)
            ->where("race_number", $raceNum)
            ->update($dataArray);
    }
}
