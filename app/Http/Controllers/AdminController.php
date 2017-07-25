<?php

namespace App\Http\Controllers;

use App\Bets;
use App\Horses;
use App\Minimum;
use App\Payout;
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
        $tracks = $betsModel->getAllTracksForAdmin(date('mdy',time()));
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
                'track_name' => " " . $request->input("name"),
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
        $arr = ['pp' => 'SCRATCHED','pnumber' => $request->input("pp")];
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
        $resultsModel = new Results();
        $dataArray = [
            'tracks' => $tracksModel->getAllTracks(date('mdy',time())),
            'results' => $resultsModel->getAllResults()
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
        $payoutArray = [
            'wPayout' => $request->input("wPayout"),
            '1pPayout' => $request->input("1pPayout"),
            '2pPayout' => $request->input("2pPayout"),
            '1sPayout' => $request->input("1sPayout"),
            '2sPayout' => $request->input("2sPayout"),
            '3sPayout' => $request->input("3sPayout"),
            'exactaPayout' => $request->input("exactaPayout"),
            'trifectaPayout' => $request->input("trifectaPayout"),
            'superfectaPayout' => $request->input("superfectaPayout"),
            'ddPayout' => $request->input("ddPayout")
        ];
        $resultModel = new Results();
        $payoutModel = new Payout();
        $payoutModel->submitPayout($payoutArray, $request->input("payoutOperation"), $request->input("tracksToday"), $request->input("racePerTrack"), date('mdy', time()));
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
        $ddFirstRaceRes = $resultModel->getFirstRaceRes($trkCode,$raceNum,$raceDate);
//        if(empty($ddSecondRaceRes)){
//
//        }else{
//            $secondRaceWinner = explode(",",$ddSecondRaceRes->race_winners);
//            $ddCombination = $explode[0] . "," . $secondRaceWinner[0];
//            $exeDD = $betsModel->checkWinners($trkCode,$raceDate,$raceNum,$ddCombination,"dailydouble");
//            foreach ($exeDD as $key => $value){
//                AdminController::updateBetStatus($value->id);
//            }
//        }
        if(empty($ddFirstRaceRes)){

        }else{
            AdminController::refreshResultsForRegradeDD($raceDate,$trkCode,$raceNum);
            $firstRaceWinner = explode(",",$ddFirstRaceRes->race_winners);
            $ddCombination = $firstRaceWinner[0] . "," . $explode[0];
            $exeDD = $betsModel->checkWinnersForDD($trkCode,$raceDate,$raceNum,$ddCombination,"dailydouble");
            foreach ($exeDD as $key => $value){
                AdminController::updateBetStatus($value->id,$value->bet_amount,$value->bet_type);
            }
            $ddRaceNum = $raceNum -1;
            AdminController::gradeWrongBets($raceDate,$trkCode,$ddRaceNum);
        }
        if($ddSecondRaceRes == ""){

        }else{
            $secondRaceWinner = explode(",",$ddSecondRaceRes->race_winners);
            $ddCombination = $explode[0] . "," . $secondRaceWinner[0];
            $exeDD = $betsModel->checkWinners($trkCode,$raceDate,$raceNum,$ddCombination,"dailydouble");
            foreach ($exeDD as $key => $value){
                AdminController::updateBetStatus($value->id,$value->bet_amount,$value->bet_type);
            }
        }
        // Set Results -------------------------------------------------------------------------------------------------
        foreach ($execExacta as $key => $value){
            AdminController::updateBetStatus($value->id,$value->bet_amount,$value->bet_type);
        }
        foreach ($execExactaBox as $key => $value){
            AdminController::updateBetStatus($value->id,$value->bet_amount,$value->bet_type);
        }
        foreach ($execTrifecta as $key => $value){
            AdminController::updateBetStatus($value->id,$value->bet_amount,$value->bet_type);
        }
        foreach ($execTrifectaBox as $key => $value){
            AdminController::updateBetStatus($value->id,$value->bet_amount,$value->bet_type);
        }
        foreach ($execSuperfecta as $key => $value){
            AdminController::updateBetStatus($value->id,$value->bet_amount,$value->bet_type);
        }
        foreach ($execW as $key => $value){
//            AdminController::updateBetStatus($value->id,$value->bet_amount,$value->bet_type);
            AdminController::updateBetStatusWPS($value->id,$value->bet_amount,"w");
        }
        foreach ($exeP1 as $key => $value){
//            AdminController::updateBetStatus($value->id,$value->bet_amount,$value->bet_type);
            AdminController::updateBetStatusWPS($value->id,$value->bet_amount,"p1");
        }
        foreach ($exeP2 as $key => $value){
//            AdminController::updateBetStatus($value->id,$value->bet_amount,$value->bet_type);
            AdminController::updateBetStatusWPS($value->id,$value->bet_amount,"p2");
        }
        foreach ($exeS1 as $key => $value){
//            AdminController::updateBetStatus($value->id,$value->bet_amount,$value->bet_type);
            AdminController::updateBetStatusWPS($value->id,$value->bet_amount,"s1");
        }
        foreach ($exeS2 as $key => $value){
//            AdminController::updateBetStatus($value->id,$value->bet_amount,$value->bet_type);
            AdminController::updateBetStatusWPS($value->id,$value->bet_amount,"s2");
        }
        foreach ($exeS3 as $key => $value){
//            AdminController::updateBetStatus($value->id,$value->bet_amount,$value->bet_type);
            AdminController::updateBetStatusWPS($value->id,$value->bet_amount,"s3");
        }
        // Set Defeat
        AdminController::gradeWrongBets($raceDate,$trkCode,$raceNum);
    }
    public static function updateBetStatus($id, $betAmount, $betType){
        $dataArray = [
            "status" => 1,
            "result" => 1, // 1 for win
            "win_amount" => ""
        ];
        $bets = DB::table("bets")->where("id",$id)->first();
        $payoutContent = DB::table("payout")->where("track_code",$bets->race_track)->where("race_date",$bets->race_date)->first();
        // Payout (Payout required!)
        switch ($betType){
            case "exacta":
                $payout = json_decode($payoutContent->content)->exactaPayout;
                $dataArray["win_amount"] = $payout * ($betAmount / 2);
                break;
            case "exactabox":
                $payout = json_decode($payoutContent->content)->exactaPayout;
                $dataArray["win_amount"] = $payout * ($betAmount / 2);
                break;
            case "trifecta":
                $payout = json_decode($payoutContent->content)->trifectaPayout;
                $dataArray["win_amount"] = $payout * ($betAmount / 2);
                break;
            case "trifectabox":
                $payout = json_decode($payoutContent->content)->trifectaPayout;
                $dataArray["win_amount"] = $payout * ($betAmount / 2);
                break;
            case "superfecta":
                $payout = json_decode($payoutContent->content)->superfectaPayout;
                $dataArray["win_amount"] = $payout * ($betAmount / 2);
                break;
            case "dailydouble":
                $payout = json_decode($payoutContent->content)->ddPayout;
                $dataArray["win_amount"] = $payout * ($betAmount / 2);
                break;
                break;
            default:
                $dataArray["win_amount"] = 0;
                break;
        }
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
            "result" => 0,
            "win_amount" => 0
        ];
        return DB::table("bets")
            ->where("race_track",$trackCode)
            ->where("race_date",$raceDate)
            ->where("race_number", $raceNum)
            ->update($dataArray);
    }
    public function saveMinimum(Request $request){
        $minimumArray = [
            'wps' => $request->input('wps'),
            'exacta' => $request->input('exacta'),
            'trifecta' => $request->input('trifecta'),
            'superfecta' => $request->input('superfecta'),
            'dailydouble' => $request->input('dailydouble')
        ];
//        $trk = $request->input("trk");
//        $num = $request->input("num");
//        $date = $request->input("date");
        $dataArray = [
            'min' => json_encode($minimumArray),
            'trk' => $request->input("trk"),
//            'num' => $request->input("num"),
            'date' => $request->input("date"),
            'operation' => $request->input("operation")
        ];
        $minModel = new Minimum();
        return $minModel->insertMin($dataArray);
    }
    public function checkMinimum(Request $request){
        $minModel = new Minimum();
        $dataArray = [
            'trk' => $request->input("trk"),
//            'num' => $request->input("num"),
            'date' => $request->input("date")
        ];
        $result = $minModel->checkMinimum($dataArray)->first();
        if($result){
            return response()->json(json_decode($result->content));
        }else{
            return 1;
        }
    }
    public function checkPayout(Request $request){
        $payoutModel = new Payout();
        $res = $payoutModel->checkPayout($request->input("trk"), $request->input("num"), $request->input("date"))->first();
        if($res){
            return response()->json(json_decode($res->content));
        }else{
            return 1;
        }
    }
    public static function refreshResultsForRegradeDD($raceDate,$trackCode, $raceNum){
        $dataArray = [
            "status" => 0,
            "result" => 0,
            "win_amount" => 0
        ];
        return DB::table("bets")
            ->where("race_track",$trackCode)
            ->where("race_date",$raceDate)
            ->where("race_number", $raceNum - 1)
            ->where("bet_type","dailydouble")
            ->update($dataArray);
    }
    public static function updateBetStatusWPS($id, $betAmount, $wps){
        $dataArray = [
            "status" => 1,
            "result" => 1, // 1 for win
            "win_amount" => ""
        ];
        $bets = DB::table("bets")->where("id",$id)->first();
        $payoutContent = DB::table("payout")->where("track_code",$bets->race_track)->where("race_date",$bets->race_date)->first();
        switch ($wps){
            case "w":
                $payout = json_decode($payoutContent->content)->wPayout;
                $dataArray["win_amount"] = $payout * ($betAmount / 2);
                break;
            case "p1":
                $payout = json_decode($payoutContent->content, true);
                $dataArray["win_amount"] = $payout["1pPayout"] * ($betAmount / 2);
                break;
            case "p2":
                $payout = json_decode($payoutContent->content, true);
                $dataArray["win_amount"] = $payout["2pPayout"] * ($betAmount / 2);
                break;
            case "s1":
                $payout = json_decode($payoutContent->content, true);
                $dataArray["win_amount"] = $payout["1sPayout"] * ($betAmount / 2);
                break;
            case "s2":
                $payout = json_decode($payoutContent->content, true);
                $dataArray["win_amount"] = $payout["2sPayout"] * ($betAmount / 2);
                break;
            case "s3":
                $payout = json_decode($payoutContent->content, true);
                $dataArray["win_amount"] = $payout["3sPayout"] * ($betAmount / 2);
                break;
            default:
                $dataArray["win_amount"] = 0;
                break;
        }
        return DB::table("bets")
            ->where("id", $id)
            ->update($dataArray);
    }
    public function scratchBets(Request $request){
        $pp = $request->input("pp");
        $trk = $request->input("trk");
        $num = $request->input("num");
        $date = $request->input("date");
        $betsModel = new Bets();
        $dataArray = [
            'trk' => $trk,
            'num' => $num,
            'date' => $date
        ];
        $bets = $betsModel->getBetsForScratch($dataArray);
        if($bets){
            foreach ($bets as $key => $value){
                $betStr = explode(",",$value->bet);
                if(in_array($pp, $betStr)){
                    $betsModel->scratchBet($value->id);
                }else{
                    // Do nothing
                }
            }
        }else{
            return 1; // Empty
        }
    }
    public function getAllTracksWithoutToday(Request $request){
        $date = $request->input("date");
        $tracksModel = new Tracks();
        return $tracksModel->getTracksWithoutToday($date);
    }
    public function submitNewTrack(Request $request){
        $trkCode = $request->input("trkCode");
        $date = $request->input("date");
        $trkName = $request->input("trkName");
        $trkModel = new Tracks();
        if($trkModel->submitNewTrack($trkName,$trkCode,$date)){
            return 0;
        }else{
            return 1;
        }
    }
    public function getTracksToday(Request $request){
        $trkModel = new Tracks();
        return $trkModel->getAllTracks($request->input("date"));
    }
    public function submitHorse(Request $request){
        $formData = $request->input("frm");
        $horseModel = new Horses();
        $dataArray = [
            "pp" => $formData[0]["value"],
            "horse" => $formData[1]["value"],
            "jockey" => $formData[2]["value"],
            "race_number" => "Race " .$formData[3]["value"],
            "race_time" => " " . $formData[4]["value"],
            "race_date" => $request->input("date"),
            "race_track" => $formData[5]["value"]
        ];
        if($request->input("operation") == 0){
            $res = $horseModel->insertNewHorse($dataArray);
        }else if($request->input("operation") == 1){
            $id = $request->input("id");
            $res = $horseModel->updateHorse($id, $dataArray);
        }
        if($res){
            return 0;
        }else{
            return 1;
        }
    }
    public function submitNewWager(Request $request){
        $wagerModel = new Wager();
        $formData = $request->input("frm");
        $date = $request->input("date");
        $wagerArr = [];
        $operation = $request->input("operation");
        $id = $request->input("id");
        foreach ($formData as $index => $value){
            if($formData[$index]["value"] == "exacta"){
                array_push($wagerArr, "Exacta");
                array_push($wagerArr, "Exacta Box");
            }else if($formData[$index]["value"] == "trifecta"){
                array_push($wagerArr, "Trifecta");
                array_push($wagerArr, "Trifecta Box");
            }else if($formData[$index]["value"] == "superfecta"){
                array_push($wagerArr, "Superfecta");
            }else if($formData[$index]["value"] == "dd"){
                array_push($wagerArr, "Daily Double");
            }else if($formData[$index]["value"] == "wps"){
                array_push($wagerArr, "WPS");
            }
        }
        $dataArray = [
            "code" => $formData[0]["value"],
            "num" => $formData[1]["value"],
            "wager" => $wagerArr,
            "date" => $date
        ];
        $res = $wagerModel->submitWager($dataArray,$operation,$id);
        if($res){
            return 0;
        }else{
            return 1;
        }
    }
    public function getHorseData(Request $request){
        $horseModel = new Horses();
        return response()->json($horseModel->getHorseById($request->input("id")));
    }
    public function getWagerByRace(Request $request){
        $wagerModel = new Wager();
        $res = $wagerModel->getWagerById($request->input("id"));
        $dataArray = [
            "id" => $res->id,
            "race_date" => $res->race_date,
            "race_number" => preg_replace('/\D/', '', $res->race_number),
            "race_time" => $res->race_time,
            "track_code" => $res->track_code,
            "extracted" => unserialize($res->extracted)
        ];
        return $dataArray;
    }
    public function submitNewBet(Request $request){
        date_default_timezone_set('America/Los_Angeles');
        $pacificDate = date('Y-m-d H:i:s', time());
        $raceDate = date('mdy',time());
        if($request->input("fourth") != null){
            $betString = $request->input("first") . ',' . $request->input("second") . ',' . $request->input("third") . ',' . $request->input("fourth");
        }else if($request->input("third") != null){
            $betString = $request->input("first") . ',' . $request->input("second") . ',' . $request->input("third");
        }else if($request->input("second") != null){
            $betString = $request->input("first") . ',' . $request->input("second");
        }else if($request->input("first") != null){
            $betString = $request->input("first");
        }
        if($request->input("wager") == "w" || $request->input("wager") == "p" || $request->input("wager") == "s"){
            $wager = "wps";
        }else{
            $wager = $request->input("wager");
        }
        if($request->input("wager") == "w"){
            $type = "w";
        }else if($request->input("wager") == "p"){
            $type = "p";
        }else if($request->input("wager") == "w"){
            $type = "s";
        }else{
            $type = "x";
        }
        $betArray = [
            'player_id' => $request->input("player_id"),
            'race_number' => $request->input("raceNum"),
            'race_track' => $request->input("raceTrack"),
            'bet_type' => $wager,
            'bet_amount' => $request->input("amount"),
            'type' => $type,
            'bet' => $betString,
            'status' => '0',
            'created_at' => $pacificDate,
            'updated_at' => $pacificDate,
            'race_date' => $raceDate
        ];
        $betsModel = new Bets();
        $res = $betsModel->saveNewBet($betArray);
        if($res){
            return 0;
        }else{
            return 1;
        }
    }
    public function undoScratch(Request $request){
        $pnum = $request->input("pnum");
        $trk = $request->input("trk");
        $num = $request->input("num");
        $date = $request->input("date");
        $dataArray = [
            "trk" => $trk,
            "date" => $date,
            "num" => $num
        ];
        $betsModel = new Bets();
        $horseModel = new Horses();
        $undo = $horseModel->undoScratch($request->input("id"),$pnum);
        if($undo){
            $bets = $betsModel->getBetsForScratch($dataArray);
            if($bets){
                foreach ($bets as $key => $value){
                    $betStr = explode(",",$value->bet);
                    if(in_array($pnum, $betStr)){
                        $betsModel->undoScratch($value->id);
                    }else{
                        // Do nothing
                    }
                }
            }else{
                return 1; // Empty
            }
        }

    }
    public function removeTrack(Request $request){
        date_default_timezone_set('America/Los_Angeles');
        $trkModel = new Tracks();
        $res = $trkModel->removeTrack($request->input("trk"),date("mdy",time()),$request->input("operation"));
        if($res){
            return 0;
        }else{
            return 1;
        }
    }
}
