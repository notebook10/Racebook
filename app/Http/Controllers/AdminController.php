<?php

namespace App\Http\Controllers;

use App\Bets;
use App\Cancelled;
use App\Horses;
use App\Logs;
use App\Minimum;
use App\Payout;
use App\Results;
use App\Scratches;
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
        $resultsModel = new Results();
        if(Auth::check()){
            $theme = Theme::uses('admin')->layout('layout')->setTitle('Admin');
            $dataArray = [
                "mismatched" => $resultsModel->getAllMismatchedResults()
            ];
            return $theme->of('admin/dashboard',$dataArray)->render();
        }else{
            return view('default/login');
        }
    }
    public function tracks(){
        date_default_timezone_set('America/Los_Angeles');
        $betsModel = new Tracks();
        $tracks = $betsModel->getAllTracksForAdmin(date('mdy',time()));
        $dateTomorrow = date('mdy', strtotime('+1 day', time()));
        $tracksTomorrow = $betsModel->getAllTracksTomorrowForAdmin($dateTomorrow);
        $dataArray = [
            'tracks' => $tracks,
            'tracksTomorrow' => $tracksTomorrow
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
        $logsModel = new Logs();
        $id = $request->input("id");
        if($operation == 1){
            // EDIT
            $dataArray = ['time_zone' => $request->input("selectTmz")];
            $tmzModel->updateTimezoneById($id,$dataArray);
            $logsArray = [
                'user_id' => Auth::id(),
                'action' => 'Update timezone of ' . $request->input("selectTmz") // replace $id; wala lumalabas ee
            ];
            $logsModel->saveLog($logsArray);
            return 1;
        }else if($operation == 0){
            // ADD
            $dataArray = [
                'time_zone' => $request->input("selectTmz"),
                'track_name' => " " . $request->input("name"),
                'track_code' => $request->input("code")
            ];
            $logsArray = [
                'user_id' => Auth::id(),
//                'action' => 'Update timezone of ' . $id
                'action' => 'Update timezone of ' . $request->input("name") . " to " . $request->input("selectTmz")
            ];
            $tmzModel->saveTimezone($dataArray);
            $logsModel->saveLog($logsArray);
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
//            'betsToday' => $betsModel->getAll()
            'betsToday' => $betsModel->getAllPastBets()
        ];
        $theme = Theme::uses('admin')->layout('layout')->setTitle('PastBets');
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
            'ddPayout' => $request->input("ddPayout"),
            'quinellaPayout' => $request->input("quinellaPayout")
        ];
        // NEW UPATE
        $resultArray = [
            'exacta' => $request->input('exactaRes') != null ? $request->input('exactaRes') : '',
            'trifecta' => $request->input('trifectaRes') != null ? $request->input('trifectaRes') : '',
            'superfecta' => $request->input('superfectaRes') != null ? $request->input('superfectaRes') : '',
            'dailydouble' => $request->input('ddRes') != null ? $request->input('ddRes') : '',
            'quinella' => $request->input('quinellaRes') != null ? $request->input('quinellaRes') : '',
            'wps' => $request->input('wpsRes') != null ? $request->input('wpsRes') : '',
        ];
        $dataArray = [
            'track_code' => $request->input("tracksToday"),
            'race_number' => $request->input("racePerTrack"),
            'first' => $request->input("first"),
            'second' => $request->input("second"),
            'third' => $request->input("third"),
            'fourth' => $request->input("fourth"),
//            'race_date' => date('mdy', time())
            'race_date' => $request->input("raceDateInp"),
            'payoutArray' => $payoutArray, // For checking if matched to second entry
            'resultsArray' => $resultArray
        ];
        $resultModel = new Results();
        $payoutModel = new Payout();
//        $payoutModel->submitPayout($payoutArray, $request->input("payoutOperation"), $request->input("tracksToday"), $request->input("racePerTrack"), date('mdy', time()));
        // Temp : Block payout operation if results operation == 2
        if($request->input('operation') == 2){
            $payoutOperation = 2;
        }else{
            $payoutOperation = $request->input("payoutOperation");
        }
        // Temp
        $payoutModel->submitPayout($payoutArray,$payoutOperation, $request->input("tracksToday"), $request->input("racePerTrack"), $request->input("raceDateInp"));
//        if($request->input("exacta") != NULL){
//            AdminController::newCancelWager($dataArray,"exacta");
//        }
//        if($request->input("trifecta") != NULL){
//            AdminController::newCancelWager($dataArray,"trifecta");
//        }
        return $resultModel->insertResult($dataArray,$request->input('operation'));
    }
    public function checkResults(Request $request){
        $resultModel = new Results();
        $results = $resultModel->checkResults($request->input("trkCode"), $request->input("raceDate"), $request->input("raceNum"));
        if($results->count() > 0){
            if($results[0]->status == 0){
                // Not matched
                return [1,Auth::id(),$results[0]->graded_by,$results[0]->race_winners,$results]; // Results entered not match
            }else{
                return $results->count() > 0 ? $results : "";
            }
        }else{
            return "";
        }
    }
    public function getLatestResultID(Request $request){
        $resultModel = new Results();
        $betsModel = new Bets();
        $latestRecord = $request->input("lastId");
//        $latestRecord = $resultModel->getLatestResult($request->input("lastId"));
//        $winningCombination = $latestRecord->first()->race_winners;
        $trkCode = $latestRecord[0]["track_code"];
        $raceNum = $latestRecord[0]["race_number"];
        $raceDate = $latestRecord[0]["race_date"];
//        $trkCode = $latestRecord->first()->track_code;
//        $raceNum = $latestRecord->first()->race_number;
//        $raceDate = $latestRecord->first()->race_date;
        // Refresh
        AdminController::refreshResultsForRegrade($raceDate,$trkCode,$raceNum);
        $explode = explode(",",$latestRecord["wps"]);
//        $exacta = $explode[0] . "," . $explode[1];
        $exacta = $latestRecord["exacta"];
        $quinella = $latestRecord["quinella"];
//        $trifecta = $explode[0] . "," . $explode[1] . "," . $explode[2];
        $trifecta = $latestRecord["trifecta"];
        $superfecta = $latestRecord["superfecta"];
        $dailydouble = $latestRecord["dailydouble"];
        $wps = $latestRecord["wps"];
        $execExacta = $betsModel->checkWinners($trkCode,$raceDate,$raceNum,$exacta,"exacta");
        $execExactaBox = $betsModel->checkWinners($trkCode,$raceDate,$raceNum,$exacta,"exactabox");

        if($superfecta != ""){
            $execSuperfecta = $betsModel->checkWinners($trkCode,$raceDate,$raceNum,$superfecta,"superfecta");
            foreach ($execSuperfecta as $key => $value){
                AdminController::updateBetStatus($value->id,$value->bet_amount,$value->bet_type,$value->race_number);
            }
        }
        if($trifecta != ""){
            $execTrifecta = $betsModel->checkWinners($trkCode,$raceDate,$raceNum,$trifecta,"trifecta");
            $execTrifectaBox = $betsModel->checkWinners($trkCode,$raceDate,$raceNum,$trifecta,"trifectabox");
            foreach ($execTrifecta as $key => $value){
                AdminController::updateBetStatus($value->id,$value->bet_amount,$value->bet_type,$value->race_number);
            }
            foreach ($execTrifectaBox as $key => $value){
                AdminController::updateBetStatus($value->id,$value->bet_amount,$value->bet_type,$value->race_number);
            }
        }
        if($wps != ""){
            $w = $explode[0];
            $p = [$explode[0],$explode[1]];

            $execW = $betsModel->checkWps($trkCode,$raceDate,$raceNum,$w,"wps","w");
            $exeP1 = $betsModel->checkWps($trkCode,$raceDate,$raceNum,$p[0],"wps","p");
            $exeP2 = $betsModel->checkWps($trkCode,$raceDate,$raceNum,$p[1],"wps","p");
            $exeS1 = $betsModel->checkWps($trkCode,$raceDate,$raceNum,$explode[0],"wps","s");
            $exeS2 = $betsModel->checkWps($trkCode,$raceDate,$raceNum,$explode[1],"wps","s");
            $exeS3 = $betsModel->checkWps($trkCode,$raceDate,$raceNum,$explode[2],"wps","s");

            foreach ($execW as $key => $value){
                AdminController::updateBetStatusWPS($value->id,$value->bet_amount,"w",$value->race_number);
            }
            foreach ($exeP1 as $key => $value){
                AdminController::updateBetStatusWPS($value->id,$value->bet_amount,"p1",$value->race_number);
            }
            foreach ($exeP2 as $key => $value){
                AdminController::updateBetStatusWPS($value->id,$value->bet_amount,"p2",$value->race_number);
            }
            foreach ($exeS1 as $key => $value){
                AdminController::updateBetStatusWPS($value->id,$value->bet_amount,"s1",$value->race_number);
            }
            foreach ($exeS2 as $key => $value){
                AdminController::updateBetStatusWPS($value->id,$value->bet_amount,"s2",$value->race_number);
            }
            foreach ($exeS3 as $key => $value){
                AdminController::updateBetStatusWPS($value->id,$value->bet_amount,"s3",$value->race_number);
            }
        }
//        $ddSecondRaceRes = $resultModel->getSecondRaceRes($trkCode,$raceNum,$raceDate);
//        $ddFirstRaceRes = $resultModel->getFirstRaceRes($trkCode,$raceNum,$raceDate);
        if($quinella != null){
            $execQuinella = $betsModel->checkWinners($trkCode,$raceDate,$raceNum,$quinella,"quinella"); // explode $exacta
            foreach ($execQuinella as $key => $value){
                AdminController::updateBetStatus($value->id,$value->bet_amount,$value->bet_type,$value->race_number);
            }
        }
        if($dailydouble != null){
            $raceNumLessOne = $raceNum - 1; // Less One
            $execDailyDouble = $betsModel->checkWinners($trkCode,$raceDate,$raceNumLessOne,$dailydouble,"dailydouble");
            foreach ($execDailyDouble as $key => $value){
                AdminController::updateBetStatus($value->id,$value->bet_amount,$value->bet_type,$value->race_number);
            }
            AdminController::gradeWrongBetsDD($raceDate,$trkCode,$raceNumLessOne);
        }
//        if(empty($ddFirstRaceRes)){
//
//        }else{
//            AdminController::refreshResultsForRegradeDD($raceDate,$trkCode,$raceNum);
//            $firstRaceWinner = explode(",",$ddFirstRaceRes->race_winners);
//            $ddCombination = $firstRaceWinner[0] . "," . $explode[0];
//            $exeDD = $betsModel->checkWinnersForDD($trkCode,$raceDate,$raceNum,$ddCombination,"dailydouble");
//            foreach ($exeDD as $key => $value){
//                AdminController::updateBetStatus($value->id,$value->bet_amount,$value->bet_type,$value->race_number);
//            }
//            $ddRaceNum = $raceNum - 1;
//            AdminController::gradeWrongBets($raceDate,$trkCode,$ddRaceNum); ########################################################### <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
//        }
//        if($ddSecondRaceRes == ""){
//
//        }else{
//            $secondRaceWinner = explode(",",$ddSecondRaceRes->race_winners);
//            $ddCombination = $explode[0] . "," . $secondRaceWinner[0];
//            $exeDD = $betsModel->checkWinners($trkCode,$raceDate,$raceNum,$ddCombination,"dailydouble");
//            foreach ($exeDD as $key => $value){
//                AdminController::updateBetStatus($value->id,$value->bet_amount,$value->bet_type,$value->race_number);
//            }
//        }
        // Set Results -------------------------------------------------------------------------------------------------
        foreach ($execExacta as $key => $value){
            AdminController::updateBetStatus($value->id,$value->bet_amount,$value->bet_type,$value->race_number);
        }
        foreach ($execExactaBox as $key => $value){
            AdminController::updateBetStatus($value->id,$value->bet_amount,$value->bet_type,$value->race_number);
        }
//        foreach ($execSuperfecta as $key => $value){
//            AdminController::updateBetStatus($value->id,$value->bet_amount,$value->bet_type,$value->race_number);
//        }
        // Set Defeat
        AdminController::gradeWrongBets($raceDate,$trkCode,$raceNum);
        AdminController::totalBalance();
        AdminController::totalRSLT();
        // Grade pending DD bETS
        if(empty($ddFirstRaceRes)){
            $betsModel->gradePendingDD([
                "track_code" => $trkCode,
                "race_date" => $raceDate,
                "race_number" => $raceNum
            ]);
        }
        // For Cancelling Wager
        $cancelArray = [
            "trk" => $request->input("trk"),
            "num" => $request->input("num"),
            "date" => $request->input("date"),
            "operation" => $request->input("cancelOperation")
        ];
        // Get checked Cancel Wager *
        if($request->input("exacta") == 1){
            AdminController::newCancelWager($cancelArray,"exacta");
            AdminController::newCancelWager($cancelArray,"exactabox");
            AdminController::saveCancel($cancelArray,"exacta");
        }else{
            AdminController::deleteCancel($cancelArray,"exacta");
        }
        if($request->input("trifecta") == 1){
            AdminController::newCancelWager($cancelArray,"trifecta");
            AdminController::newCancelWager($cancelArray,"trifectabox");
            AdminController::saveCancel($cancelArray,"trifecta");
        }else{
            AdminController::deleteCancel($cancelArray,"trifecta");
        }
        if($request->input("superfecta") == 1){
            AdminController::newCancelWager($cancelArray,"superfecta");
            AdminController::saveCancel($cancelArray,"superfecta");
        }else{
            AdminController::deleteCancel($cancelArray,"superfecta");
        }
        if($request->input("dailydouble") == 1){
            AdminController::newCancelWager($cancelArray,"dailydouble");
            AdminController::saveCancel($cancelArray,"dailydouble");
        }else{
            AdminController::deleteCancel($cancelArray,"dailydouble");
        }
        if($request->input("wps") == 1){
            AdminController::newCancelWager($cancelArray,"wps");
            AdminController::saveCancel($cancelArray,"wps");
        }else{
            AdminController::deleteCancel($cancelArray,"wps");
        }
        if($request->input("noshow") == 1){
            AdminController::newCancelWager($cancelArray,"s");
            AdminController::saveCancel($cancelArray,"s");
        }else{
            AdminController::deleteCancel($cancelArray,"s");
        }
        if($request->input("quinella") == 1){
            AdminController::newCancelWager($cancelArray,"quinella");
            AdminController::saveCancel($cancelArray,"quinella");
        }else{
            AdminController::deleteCancel($cancelArray,"quinella");
        }
    }
    public static function updateBetStatus($id, $betAmount, $betType, $raceNum){
        $dataArray = [
            "status" => 1,
            "result" => 1, // 1 for win
            "win_amount" => 0,
            "grading_status" => 1 // for returning balance
        ];
        $bets = DB::table("bets")->where("id",$id)->first();
        $payoutContent = DB::table("payout")
            ->where("track_code",$bets->race_track)
            ->where("race_date",$bets->race_date)
            ->where("race_number",$raceNum)
            ->first();
        $minimumContent = DB::table("minimum")
            ->where("track_code",$bets->race_track)
            ->where("race_date",$bets->race_date)
            ->where("race_number",$raceNum)
            ->first();
//        $temp = $minimumContent->content != null ? json_decode($minimumContent->content) : "";
        if($minimumContent != null){
            $temp = json_decode($minimumContent->content);
        }else{
            $temp = null;
        }
        // Payout (Payout required!)
        switch ($betType){
            case "exacta":
                $payout = json_decode($payoutContent->content)->exactaPayout;
//                $minimum = $temp->exacta == null ? 2 : $temp->exacta;
                if($payout != null){
                    $minimum = $temp == null ? 2 : $temp->exacta;
                    $dataArray["win_amount"] = round((str_replace(',','',$payout) - $minimum) * ($betAmount / $minimum));
                }else{
                    $dataArray["win_amount"] = 0;
                    $dataArray["result"] = 4;
                }
                break;
            case "exactabox":
                $payout = json_decode($payoutContent->content)->exactaPayout;
//                $minimum = $temp->exacta == null ? 2 : $temp->exacta;
                if($payout != null){
                    $minimum = $temp == null ? 2 : $temp->exacta;
                    $dataArray["win_amount"] = round((str_replace(',','',$payout) - $minimum) * ($betAmount / $minimum));
                }else{
                    $dataArray["win_amount"] = 0;
                    $dataArray["result"] = 4;
                }
                break;
            case "trifecta":
                $payout = json_decode($payoutContent->content)->trifectaPayout;
//                $minimum = $temp->trifecta == null ? 2 : $temp->trifecta;
                if($payout != null){
                    $minimum = $temp == null ? 2 : $temp->trifecta;
                    $dataArray["win_amount"] = round((str_replace(',','',$payout) - $minimum) * ($betAmount / $minimum));
                }else{
                    $dataArray["win_amount"] = 0;
                    $dataArray["result"] = 4;
                }
                break;
            case "trifectabox":
                $payout = json_decode($payoutContent->content)->trifectaPayout;
//                $minimum = $temp->trifecta == null ? 2 : $temp->trifecta;
                if($payout != null){
                    $minimum = $temp == null ? 2 : $temp->trifecta;
                    $dataArray["win_amount"] = round((str_replace(',','',$payout) - $minimum) * ($betAmount / $minimum));
                }else{
                    $dataArray["win_amount"] = 0;
                    $dataArray["result"] = 4;
                }
                break;
            case "superfecta":
                $payout = json_decode($payoutContent->content)->superfectaPayout;
//                $minimum = $temp->superfecta == null ? 2 : $temp->superfecta;
                if($payout != null){
                    $minimum = $temp == null ? 2 : $temp->superfecta;
                    $dataArray["win_amount"] = round((str_replace(',','',$payout) - $minimum) * ($betAmount / $minimum));
                }else{
                    $dataArray["win_amount"] = 0;
                    $dataArray["result"] = 4;
                }
                break;
            case "dailydouble":
                // Daily Double Special <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
                $racePlusOne = $raceNum + 1;
                $payoutContent = DB::table("payout")
                    ->where("track_code",$bets->race_track)
                    ->where("race_date",$bets->race_date)
                    ->where("race_number",$racePlusOne)
                    ->first();
                $minimumContent = DB::table("minimum")
                    ->where("track_code",$bets->race_track)
                    ->where("race_date",$bets->race_date)
                    ->where("race_number",$racePlusOne)
                    ->first();
                if($minimumContent != null){
                    $temp = json_decode($minimumContent->content);
                }else{
                    $temp = null;
                }
                // Daily Double Special <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
                $payout = json_decode($payoutContent->content)->ddPayout;
                if($payout != null){
                    $minimum = $temp == null ? 2 : $temp->dailydouble;
                    $dataArray["win_amount"] = round((str_replace(',','',$payout) - $minimum) * ($betAmount / $minimum));
                }else{
                    $dataArray["win_amount"] = 0;
                    $dataArray["result"] = 4;
                }
                break;
            case "quinella":
                $payout = json_decode($payoutContent->content)->quinellaPayout;
                if($payout != null){
                    $minimum = $temp == null ? 2 : $temp->quinella;
                    $dataArray["win_amount"] = round((str_replace(',','',$payout) - $minimum) * ($betAmount / $minimum));
                }else{
                    $dataArray["win_amount"] = 0;
                    $dataArray["result"] = 4;
                }
                break;
            default:
                $dataArray["win_amount"] = 0;
                break;
        }
//        AdminController::returnBalance($bets->player_id,$betAmount,$bets->grading_status,$bets->dsn);
        AdminController::collectBalance($bets->player_id,$bets->bet_amount,$bets->grading_status,$bets->dsn);
        AdminController::collectRSLT($bets->player_id,$dataArray["win_amount"],$bets->grading_status,$bets->race_date,"add",$bets->dsn);
//        AdminController::computerRSLT($bets->player_id,$dataArray["win_amount"],$bets->grading_status,$bets->race_date,"add",$bets->dsn);
        return DB::table("bets")
            ->where("id", $id)
            ->update($dataArray);
    }
    public static function gradeWrongBets($raceDate,$trackCode, $raceNum){
        $dataArray = [
            "status" => 1,
            "result" => 2, // 2 for lose
            "grading_status" => 1
        ];
//        return DB::table("bets")
//            ->where("race_track",$trackCode)
//            ->where("race_date",$raceDate)
//            ->where("race_number", $raceNum)
//            ->where("status",0)
//            ->update($dataArray);
        $loseBets =  DB::table("bets")
            ->where("race_track",$trackCode)
            ->where("race_date",$raceDate)
            ->where("race_number", $raceNum)
            ->where("status",0)
            ->get();
        foreach ($loseBets as $index => $value){
            if($value->bet_type != "dailydouble" and $value->race_number == $raceNum){
                if($value->grading_status == 0){
//                    AdminController::returnBalance($value->player_id,$value->bet_amount,$value->grading_status,$value->dsn);
                    AdminController::collectBalance($value->player_id,$value->bet_amount,$value->grading_status,$value->dsn);
                    AdminController::collectRSLT($value->player_id,$value->bet_amount,$value->grading_status,$value->race_date,"subtract",$value->dsn);
//                    AdminController::computerRSLT($value->player_id,$value->bet_amount,$value->grading_status,$value->race_date,"subtract",$value->dsn);
                }
                DB::table("bets")->where('id',$value->id)->update($dataArray);
            }else{
//                echo $value->race_number . " " . $value->id . " " . $value->bet_type . " " . $value->player_id;
            }
        }

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
            ->where("result","!=",3)
            ->update($dataArray);
    }
    public function saveMinimum(Request $request){
        $minimumArray = [
            'wps' => $request->input('wps'),
            'exacta' => $request->input('exacta'),
            'trifecta' => $request->input('trifecta'),
            'superfecta' => $request->input('superfecta'),
            'dailydouble' => $request->input('dailydouble'),
            'quinella' => $request->input('quinella')
        ];
//        $trk = $request->input("trk");
//        $num = $request->input("num");
//        $date = $request->input("date");
        $dataArray = [
            'min' => json_encode($minimumArray),
            'trk' => $request->input("trk"),
            'num' => $request->input("num"),
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
            'num' => $request->input("num"),
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
            ->where("result","!=",3)
            ->update($dataArray);
    }
    public static function updateBetStatusWPS($id, $betAmount, $wps, $raceNum){
        $dataArray = [
            "status" => 1,
            "result" => 1, // 1 for win
            "win_amount" => 0,
            "grading_status" => 1
        ];
        $bets = DB::table("bets")->where("id",$id)->first();
        $payoutContent = DB::table("payout")
            ->where("track_code",$bets->race_track)
            ->where("race_date",$bets->race_date)
            ->where("race_number",$raceNum)
            ->first();
        $minimumContent = DB::table("minimum")
            ->where("track_code",$bets->race_track)
            ->where("race_date",$bets->race_date)
            ->first();
        $temp = $minimumContent != null ? json_decode($minimumContent->content)->wps : "";
        $minimum = $temp == null ? 2 : $temp;
        switch ($wps){
            case "w":
                $payout = json_decode($payoutContent->content)->wPayout;
                if($payout != null){
                    $dataArray["win_amount"] = round(str_replace(',','',$payout - $minimum) * ($betAmount / $minimum));
                }else{
                    $dataArray["win_amount"] = 0;
                    $dataArray["result"] = 4;
                }
                break;
            case "p1":
                $payout = json_decode($payoutContent->content, true);
                if($payout != null){
                    $dataArray["win_amount"] = round(str_replace(',','',$payout["1pPayout"] - $minimum) * ($betAmount / $minimum));
                }else{
                    $dataArray["win_amount"] = 0;
                    $dataArray["result"] = 4;
                }
                break;
            case "p2":
                $payout = json_decode($payoutContent->content, true);
                if($payout != null){
                    $dataArray["win_amount"] = round(str_replace(',','',$payout["2pPayout"] - $minimum) * ($betAmount / $minimum));
                }else{
                    $dataArray["win_amount"] = 0;
                    $dataArray["result"] = 4;
                }
                break;
            case "s1":
                $payout = json_decode($payoutContent->content, true);
                if($payout != null){
                    $dataArray["win_amount"] = round(str_replace(',','',$payout["1sPayout"] - $minimum) * ($betAmount / $minimum));
                }else{
                    $dataArray["win_amount"] = 0;
                    $dataArray["result"] = 4;
                }
                break;
            case "s2":
                $payout = json_decode($payoutContent->content, true);
                if($payout != null){
                    $dataArray["win_amount"] = round(str_replace(',','',$payout["2sPayout"] - $minimum) * ($betAmount / $minimum));
                }else{
                    $dataArray["win_amount"] = 0;
                    $dataArray["result"] = 4;
                }
                break;
            case "s3":
                $payout = json_decode($payoutContent->content, true);
                if($payout != null){
                    $dataArray["win_amount"] = round(str_replace(',','',$payout["3sPayout"] - $minimum) * ($betAmount / $minimum));
                }else{
                    $dataArray["win_amount"] = 0;
                    $dataArray["result"] = 4;
                }
                break;
            default:
                $dataArray["win_amount"] = 0;
                break;
        }
//        AdminController::returnBalance($bets->player_id,$betAmount,$bets->grading_status,$bets->dsn);
        AdminController::collectBalance($bets->player_id,$bets->bet_amount,$bets->grading_status,$bets->dsn);
        AdminController::collectRSLT($bets->player_id,$dataArray["win_amount"],$bets->grading_status,$bets->race_date,"add",$bets->dsn);
//        AdminController::computerRSLT($bets->player_id,$dataArray["win_amount"],$bets->grading_status,$bets->race_date,"add",$bets->dsn);
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
        $logsModel = new Logs();
        $logsArray = [
            'user_id' => Auth::id(),
            'action' => 'Scratch => ' . $request->input('id')
        ];
        $logsModel->saveLog($logsArray);
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
        $logsModel = new Logs();
        if($trkModel->submitNewTrack($trkName,$trkCode,$date)){
            $logsArray = [
                'user_id' => Auth::id(),
                'action' => 'ADD TRACK'
            ];
            $logsModel->saveLog($logsArray);
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
        $logsModel = new Logs();
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
            $logsArray = [
                'user_id' => Auth::id(),
                'action' => 'Add Horse'
            ];
        }else if($request->input("operation") == 1){
            $id = $request->input("id");
            $res = $horseModel->updateHorse($id, $dataArray);
            $logsArray = [
                'user_id' => Auth::id(),
                'action' => 'Update Horse => ' . $id
            ];
        }
        $logsModel->saveLog($logsArray);
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
        $username = $request->input("player_id");
        $betAmount = $request->input("amount");
        $dsn = $request->input("dsn");
        $currentBalance = AdminController::balanceInquiryAdmin($username,$dsn);
        if($betAmount < $currentBalance) {
            if ($request->input("fourth") != null) {
                $betString = $request->input("first") . ',' . $request->input("second") . ',' . $request->input("third") . ',' . $request->input("fourth");
            } else if ($request->input("third") != null) {
                $betString = $request->input("first") . ',' . $request->input("second") . ',' . $request->input("third");
            } else if ($request->input("second") != null) {
                $betString = $request->input("first") . ',' . $request->input("second");
            } else if ($request->input("first") != null) {
                $betString = $request->input("first");
            }
            if ($request->input("wager") == "w" || $request->input("wager") == "p" || $request->input("wager") == "s") {
                $wager = "wps";
            } else {
                $wager = $request->input("wager");
            }
            if ($request->input("wager") == "w") {
                $type = "w";
            } else if ($request->input("wager") == "p") {
                $type = "p";
            } else if ($request->input("wager") == "s") {
                $type = "s";
            } else {
                $type = "x";
            }
//            $betArray = [
//                'player_id' => $username,
//                'race_number' => $request->input("raceNum"),
//                'race_track' => $request->input("raceTrack"),
//                'bet_type' => $wager,
//                'bet_amount' => $betAmount,
//                'type' => $type,
//                'bet' => $betString,
//                'status' => '0',
//                'created_at' => $pacificDate,
//                'updated_at' => $pacificDate,
//                'race_date' => $raceDate,
//                'result' => $request->input("result"),
//                'win_amount' => $request->input("winamount"),
//                'dsn' => $request->dsn,
//            ];
            $betsModel = new Bets();
            $logsModel = new Logs();
            if ($request->input("betsOperation") == 0){
                $betArray = [
                    'player_id' => $username,
                    'race_number' => $request->input("raceNum"),
                    'race_track' => $request->input("raceTrack"),
                    'bet_type' => $wager,
                    'bet_amount' => $betAmount,
                    'type' => $type,
                    'bet' => $betString,
                    'status' => '0',
                    'created_at' => $pacificDate,
                    'updated_at' => $pacificDate,
                    'race_date' => $raceDate,
                    'result' => $request->input("result"),
                    'win_amount' => $request->input("winamount"),
                    'dsn' => $request->dsn,
                ];
                $res = $betsModel->saveNewBet($betArray);
                $negativeBetAmount = $betAmount - ($betAmount * 2);
                AdminController::updateOdbc($negativeBetAmount,$username,"addBet","worthless",$dsn);
                $logsArray = [
                    'user_id' => Auth::id(),
                    'action' => 'Save new Bet: Race'. $request->input("raceNum") . $request->input("raceTrack") . $betAmount . $username . $dsn
                ];
            } else if ($request->input("betsOperation") == 1){
                $betArray = [
                    'player_id' => $username,
                    'race_number' => $request->input("raceNum"),
                    'race_track' => $request->input("raceTrack"),
                    'bet_type' => $wager,
                    'bet_amount' => $betAmount,
                    'type' => $type,
                    'bet' => $betString,
                    'status' => '0',
                    'created_at' => $pacificDate,
                    'updated_at' => $pacificDate,
                    'race_date' => $request->input("race_date"),
                    'result' => $request->input("result"),
                    'win_amount' => $request->input("winamount"),
                    'dsn' => $request->dsn,
                ];
                $logsArray = [
                    'user_id' => Auth::id(),
                    'action' => 'Update Bet => ' . $request->input("betId") . " " . $request->input("raceTrack") . " Race " . $request->input("raceNum") . "; " . $username . " " . $request->input("winamount") . " " . $dsn
                ];
                $betInfo = Bets::getBetInfoById($request->input("betId"));
                if($betInfo->bet_amount != $betArray["bet_amount"]){
                    // if bet_amount is changed || must update cust.dbf
                    // $betInfo->bet_amount = previous bet &&  $betArray["bet_amount"] = new betAmount
                    $newBet = $betInfo->bet_amount - $betArray["bet_amount"];
                    AdminController::updateOdbc($newBet,$username,"addBet","worthless",$dsn);
                }
                $res = $betsModel->updateBet($betArray, $request->input("betId"));
                if($betInfo->status == 0 && $request->input("result") == 1 && $betInfo->grading_status == 0){
                    // Pending to Win
                    AdminController::updateOdbc($request->input("winamount"),$username,"pendingToWin",$betInfo->race_date,$dsn);
                    AdminController::updateOdbc($betAmount,$username,"addBet","worthless",$dsn);
                    $logsArray["action"] = 'Update Bet : Pending to Win => ' . $request->input("betId") . " " . $request->input("raceTrack") . " Race " . $request->input("raceNum") . "; " . $username . " +" . $request->input("winamount") . " " . $dsn;
                }else if($betInfo->status == 0 && $request->input("result") == 2  && $betInfo->grading_status == 0){
                    // Pending to Lose
                    AdminController::updateOdbc($betAmount,$username,"pendingToLose",$betInfo->race_date,$dsn);
                    AdminController::updateOdbc($betAmount,$username,"addBet","worthless",$dsn);
                    $logsArray["action"] = 'Update Bet : Pending to Lose => ' . $request->input("betId") . " " . $request->input("raceTrack") . " Race " . $request->input("raceNum") . "; " . $username . " -" . $betAmount . " " . $dsn;
                }
                else if($betInfo->status == 1 && $betInfo->result == 2 && $request->input("result") == 1  && $betInfo->grading_status == 1){
                    // Lose to win
                    $arr = [$betAmount,$request->input("winamount")];
                    AdminController::updateOdbc($arr,$username,"loseToWin",$betInfo->race_date,$dsn);
                    $logsArray["action"] = 'Update Bet : Lose to Win => ' . $request->input("betId") . " " . $request->input("raceTrack") . " Race " . $request->input("raceNum") . "; " . $username . " +" . $request->input("winamount") ." +". $betAmount . " " . $dsn;
                }
                else if($betInfo->status == 1 && $betInfo->result == 1 && $request->input("result") == 2  && $betInfo->grading_status == 1){
                    // Win to lose
                    $arr = [$betAmount,$betInfo->win_amount];
                    AdminController::updateOdbc($arr,$username,"winToLose",$betInfo->race_date,$dsn);
                    $logsArray["action"] = 'Update Bet : Win to Lose => ' . $request->input("betId") . " " . $request->input("raceTrack") . " Race " . $request->input("raceNum") . "; " . $username . " -" . $request->input("winamount") . " -" . $betAmount . " " . $dsn;
                }
                else if($betInfo->status == 1 && $betInfo->result == 1 && $request->input("result") == 3  && $betInfo->grading_status == 1){
                    // Win to Aborted
                    $arr = [$betAmount,$betInfo->win_amount];
                    AdminController::updateOdbc($arr,$username,"winToAborted",$betInfo->race_date,$dsn);
                    $logsArray["action"] = 'Update Bet : Win to Abort => ' . $request->input("betId") . " " . $request->input("raceTrack") . " Race " . $request->input("raceNum") . "; " . $username . " -" . $request->input("winamount") . " " . $dsn;
                }
                else if($betInfo->status == 1 && $betInfo->result == 2 && $request->input("result") == 3  && $betInfo->grading_status == 1){
                    // Lose to Aborted
                    $arr = [$betAmount,$betInfo->win_amount];
                    AdminController::updateOdbc($arr,$username,"loseToAborted",$betInfo->race_date,$dsn);
                    $logsArray["action"] = 'Update Bet : Lose to Abort => ' . $request->input("betId") . " " . $request->input("raceTrack") . " Race " . $request->input("raceNum") . "; " . $username . " +" . $betAmount . " " . $dsn;
                }
                else if($betInfo->status == 1 && $betInfo->result == 4 && $request->input("result") == 3  && $betInfo->grading_status == 0){
                    // NoPayout to Aborted
                    AdminController::updateOdbc($betAmount,$username,"noPayoutToAborted",$betInfo->race_date,$dsn);
                }
                else if($betInfo->status == 1 && $betInfo->result == 3 && $request->input("result") == 1  && $betInfo->grading_status == 1){
                    // Aborted to Win
                    $arr = [$betAmount,$request->input("winamount")];
                    AdminController::updateOdbc($arr,$username,"AbortedToWin",$betInfo->race_date,$dsn);
                    $logsArray["action"] = 'Update Bet : Abort to Win => ' . $request->input("betId") . " " . $request->input("raceTrack") . " Race " . $request->input("raceNum") . "; " . $username . " +" . $request->input("winamount") . " " . $dsn;
                }
            }
            $logsModel->saveLog($logsArray);
            if ($res) {
                return 0;
            } else {
                return 1;
            }
        }else{
            return 2; // balance not enough
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
        $logsModel = new Logs();
        $logsArray = [
            'user_id' => Auth::id(),
            'action' => 'Undo Scratch => ' . $request->input("id")
        ];
        $logsModel->saveLog($logsArray);
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
        $logsModel = new Logs();
        $res = $trkModel->removeTrack($request->input("trk"),date("mdy",time()),$request->input("operation"));
        $logsArray = [
            'user_id' => Auth::id(),
            'action' => 'Remove/Show Track Temporarily'
        ];
        $logsModel->saveLog($logsArray);
        if($res){
            return 0;
        }else{
            return 1;
        }
    }
    public function showTemp(Request $request){
        date_default_timezone_set('America/Los_Angeles');
        $dateTomorrow = date('mdy', strtotime('+1 day', time()));
        $trkModel = new Tracks();
        $logsModel = new Logs();
        $res = $trkModel->showTemp($dateTomorrow,$request->input('trk'),$request->input('operation'));
        if($res){
            $logsArray = [
                'user_id' => Auth::id(),
                'action' => 'Show/Show Track Temporarily'
            ];
            $logsModel->saveLog($logsArray);
            return 0;
        }else{
            return 1;
        }
    }
    public function cancelWager(Request $request){
        $betsModel = new Bets();
        $dataArray = [
            "date" => $request->input("date"),
            "wagerType" => $request->input("wagerType"),
            "trk" => $request->input("trk"),
            "num" => $request->input("num")
        ];
        $res = $betsModel->cancelWager($dataArray);
        if($res){
            return 0;
        }else{
            return 1;
        }
    }
    public function noShow(Request $request){
        $betsModel = new Bets();
        $dataArray = [
            "date" => $request->input("date"),
            "trk" => $request->input("trk"),
            "num" => $request->input("num")
        ];
        $res = $betsModel->noShow($dataArray);
        if($res){
            return 0;
        }else{
            return 1;
        }
    }
    public static function newCancelWager($dataArray,$wagerType){
//        date_default_timezone_set('America/Los_Angeles');
//        $date = date("mdy",time()); // CURRENT DATE
        $betsModel = new Bets();
        $dataArray = [
            "date" => $dataArray["date"],
            "wagerType" => $wagerType,
            "trk" => $dataArray["trk"],
            "num" => $dataArray["num"]
        ];
        if($wagerType == "s"){
            $res = $betsModel->cancelWagerShow($dataArray);
        }else{
            $res = $betsModel->cancelWager($dataArray);
        }
        if($res){
            return 0;
        }else{
            return 1;
        }
    }
    public static function saveCancel($dataArray, $wager){
        date_default_timezone_set('America/Los_Angeles');
            // SAVE
        return DB::table("cancelled")
            ->insert([
                "track_code" => $dataArray["trk"],
                "race_number" => $dataArray["num"],
                "race_date" => $dataArray["date"],
                "status" => 1,
                "wager" => $wager,
                "created_at" => date('Y-m-d H:i:s', time()),
                "updated_at" => date('Y-m-d H:i:s', time())
            ]);
    }
    public function checkCancelled(Request $request){
        $cancelledModel = new Cancelled();
        $dataArray = [
            "trk" => $request->input("trk"),
            "num" => $request->input("num"),
            "date" => $request->input("date")
        ];
        $res = $cancelledModel->check($dataArray);
        return $res;
    }
    public static function deleteCancel($dataArray, $wager){
        return DB::table("cancelled")
            ->where("track_code",$dataArray["trk"])
            ->where("race_number",$dataArray["num"])
            ->where("race_date",$dataArray["date"])
            ->where("wager",$wager)
            ->delete();
    }
    public function getTracksWithDate(Request $request){
        $selectedDate =  $request->input("date");
        $tracksModel = new Tracks();
        $tracks = $tracksModel->getAllTracks($selectedDate);
        return $tracks;
    }
    public function getBetInfo(Request $request){
        $betsModel = new Bets();
        $res = $betsModel->getBetInfo($request->input("id"));
        $aArray = [
            "id" => $res->id,
            "player_id" => $res->player_id,
            "bet_amount" => $res->bet_amount,
            "race_track" => $res->race_track,
            "race_number" => $res->race_number,
            "bet_type" => $res->bet_type,
            "type" => $res->type,
            "bet" => $res->bet,
            "result" => $res->result,
            "win_amount" => $res->win_amount,
            "grading_status" => $res->grading_status,
            "dsn" => $res->dsn,
            "race_date" => $res->race_date
        ];
        return $aArray;
    }
    public function pendingBets(){
        $betsModel = new Bets();
        $dataArray = [
            'betsToday' => $betsModel->getAllPendingBets()
        ];
        $theme = Theme::uses('admin')->layout('layout')->setTitle('Pending Bets');
        return $theme->of('admin/pending_bets',$dataArray)->render();
    }
    public static function getUsernameById($id){
        return DB::table("users")
            ->where("id",$id)
            ->first();
    }
    public function getPastBets(Request $request){
        $selectedDate = $request->input("date");
        $betsModel = new Bets();
        $betsBySelectedDate = $betsModel->getPastBetsBySelectedDate($selectedDate);
        $tempArr = [];
        $tempObj = [
            'data' => []
        ];
        foreach ($betsBySelectedDate as $index => $value){
            if($value->result == 0){
                $res = "Null";
                $winAmount = number_format($value->win_amount,2);
            }elseif($value->result == 1){
                $res = "Win";
                $winAmount = number_format($value->win_amount,2);
            }elseif($value->result == 2){
                $res = "Lose";
                $winAmount = '-' . number_format($value->bet_amount,2);
            }elseif($value->result == 3){
                $res = "Aborted";
                $winAmount = number_format($value->win_amount,2);
            }elseif($value->result == 4){
                $res = "NoPayout";
                $winAmount = number_format($value->win_amount,2);
            }
            $tempArr = [
//                'player_id' => AdminController::getUsernameById($value->player_id)->firstname,
                'player_id' => $value->player_id,
                'race_number' => "Race " . $value->race_number,
                'race_track' => Tracks::getTrackNameWithCode($value->race_track)->name,
                'bet_type' => $value->bet_type === "wps" ? $value->type : $value->bet_type,
                'bet_amount' => number_format($value->bet_amount,2),
                'bet' => str_replace(',','-',$value->bet),
                'status' => $value->status === 0 ? "Pending" : "Graded",
                'result' => $res,
//                'win_amount' => number_format($value->win_amount,2),
                'win_amount' => $winAmount,
                'created_at' => $value->created_at,
                'action' => "<input type='button' class='btn btn-primary editBet' value='EDIT' data-id='". $value->id ."'>",
                'dsn' => $value->dsn
            ];
            array_push($tempObj["data"],$tempArr);
        }
        return json_encode($tempObj);
    }
    public function getPendingBets(Request $request){
        $selectedDate = $request->input("date");
        $betsModel = new Bets();
        $betsBySelectedDate = $betsModel->getPendingBetsBySelectedDate($selectedDate);
        $tempArr = [];
        $tempObj = [
            'data' => []
        ];
        foreach ($betsBySelectedDate as $index => $value){
            if($value->result == 0){
                $res = "Null";
            }elseif($value->result == 1){
                $res = "Win";
            }elseif($value->result == 2){
                $res = "Lose";
            }elseif($value->result == 3){
                $res = "Aborted";
            }elseif($value->result == 4){
                $res = "NoPayout";
            }
            $tempArr = [
//                'player_id' => AdminController::getUsernameById($value->player_id)->firstname,
                'player_id' => $value->player_id,
                'race_number' => "Race " . $value->race_number,
                'race_track' => Tracks::getTrackNameWithCode($value->race_track)->name,
                'bet_type' => $value->bet_type === "wps" ? $value->type : $value->bet_type,
                'bet_amount' => number_format($value->bet_amount,2),
                'bet' => str_replace(',','-',$value->bet),
                'status' => $value->status === 0 ? "Pending" : "Graded",
                'result' => $res,
                'win_amount' => number_format($value->win_amount,2),
                'created_at' => $value->created_at,
                'action' => "<input type='button' class='btn btn-primary editBet' value='EDIT' data-id='". $value->id ."'>",
                'dsn' => $value->dsn
            ];
            array_push($tempObj["data"],$tempArr);
        }
        return json_encode($tempObj);
    }
    public function getPendingBetsHome(Request $request){
        $selectedDate = $request->input("date");
        $id = $request->input('id');
        $dsn = $request->input('dsn');
        $betsModel = new Bets();
        $betsBySelectedDate = $betsModel->getPendingBetsHome($selectedDate,$id,$dsn);
        $tempArr = [];
        $tempObj = [
            'data' => []
        ];
        foreach ($betsBySelectedDate as $index => $value){
            if($value->result == 0){
                $res = "Null";
            }elseif($value->result == 1){
                $res = "Win";
            }elseif($value->result == 2){
                $res = "Lose";
            }elseif($value->result == 3){
                $res = "Aborted";
            }elseif($value->result == 4){
                $res = "NoPayout";
            }
            $tempArr = [
//                'player_id' => AdminController::getUsernameById($value->player_id)->firstname,
                'player_id' => $value->player_id,
                'race_number' => "Race " . $value->race_number,
                'race_track' => Tracks::getTrackNameWithCode($value->race_track)->name,
                'bet_type' => $value->bet_type === "wps" ? $value->type : $value->bet_type,
                'bet_amount' => number_format($value->bet_amount,2),
                'bet' => str_replace(',','-',$value->bet),
                'status' => $value->status === 0 ? "Pending" : "Graded",
                'result' => $res,
                'win_amount' => number_format($value->win_amount,2),
                'created_at' => $value->created_at,
                'action' => "<input type='button' class='btn btn-primary editBet' value='EDIT' data-id='". $value->id ."'>"
            ];
            array_push($tempObj["data"],$tempArr);
        }
        return json_encode($tempObj);
    }
    public function getPastHome(Request $request){
        $selectedDate = $request->input("date");
        $id = $request->input('id');
        $dsn = $request->input('dsn');
        $betsModel = new Bets();
        $betsBySelectedDate = $betsModel->getPastBetsHome($selectedDate,$id,$dsn);
        $tempArr = [];
        $tempObj = [
            'data' => []
        ];
        foreach ($betsBySelectedDate as $index => $value){
            if($value->result == 0){
                $res = "Null";
                $winAmount = number_format($value->win_amount,2);
            }elseif($value->result == 1){
                $res = "Win";
                $winAmount = number_format($value->win_amount,2);
            }elseif($value->result == 2){
                $res = "Lose";
                $winAmount = '-' . number_format($value->bet_amount,2);
            }elseif($value->result == 3){
                $res = "Aborted";
                $winAmount = number_format($value->win_amount,2);
            }elseif($value->result == 4){
                $res = "NoPayout";
                $winAmount = number_format($value->win_amount,2);
            }
            $tempArr = [
//                'player_id' => AdminController::getUsernameById($value->player_id)->firstname,
                'player_id' => $value->player_id,
                'race_number' => "Race " . $value->race_number,
                'race_track' => Tracks::getTrackNameWithCode($value->race_track)->name,
                'bet_type' => $value->bet_type === "wps" ? $value->type : $value->bet_type,
                'bet_amount' => number_format($value->bet_amount,2),
                'bet' => str_replace(',','-',$value->bet),
                'status' => $value->status === 0 ? "Pending" : "Graded",
                'result' => $res,
//                'win_amount' => number_format($value->win_amount,2),
                'win_amount' => $winAmount,
                'created_at' => $value->created_at,
                'action' => "<input type='button' class='btn btn-primary editBet' value='EDIT' data-id='". $value->id ."'>"
            ];
            array_push($tempObj["data"],$tempArr);
        }
        return json_encode($tempObj);
    }
    public static function comparePayout($firstEntry, $secondEntry){
        $decodedFirst = json_decode($firstEntry->content,true);
        // If $aCompare array have 1 => mismatched; 0 => matched
        $aCompare = [];
        $aCompare["wPayout"] = $decodedFirst["wPayout"] == $secondEntry["wPayout"] ? 0 : 1;
        $aCompare["1pPayout"] = $decodedFirst["1pPayout"] == $secondEntry["1pPayout"] ? 0 : 1;
        $aCompare["2pPayout"] = $decodedFirst["2pPayout"] == $secondEntry["2pPayout"] ? 0 : 1;
        $aCompare["1sPayout"] = $decodedFirst["1sPayout"] == $secondEntry["1sPayout"] ? 0 : 1;
        $aCompare["2sPayout"] = $decodedFirst["2sPayout"] == $secondEntry["2sPayout"] ? 0 : 1;
        $aCompare["3sPayout"] = $decodedFirst["3sPayout"] == $secondEntry["3sPayout"] ? 0 : 1;
        $aCompare["exactaPayout"] = $decodedFirst["exactaPayout"] == $secondEntry["exactaPayout"] ? 0 : 1;
        $aCompare["trifectaPayout"] = $decodedFirst["trifectaPayout"] == $secondEntry["trifectaPayout"] ? 0 : 1;
        $aCompare["superfectaPayout"] = $decodedFirst["superfectaPayout"] == $secondEntry["superfectaPayout"] ? 0 : 1;
        $aCompare["ddPayout"] = $decodedFirst["ddPayout"] == $secondEntry["ddPayout"] ? 0 : 1;
        $aCompare["quinellaPayout"] = $decodedFirst["quinellaPayout"] == $secondEntry["quinellaPayout"] ? 0 : 1;
        $logsModel = new Logs();
        foreach ($aCompare as $key => $value){
            if($value == 1){
                $logsModel->saveLog(["user_id" => Auth::id(),"action"=>"Mismatched Payout: " . $key]);
            }
        }
        return $aCompare;
    }
    public function getWagerForRaceAdmin(Request $request){
        $wagerModel = new Wager();
        if($request->input('num') != 1){
            $checkDDinFirstRace = $wagerModel->getWagerForRaceFirstRace($request->input('trk'),$request->input('num'),$request->input('date'));
        }else{
            $checkDDinFirstRace = 0;
        }
        $wager = $wagerModel->getWagerForRace($request->input('trk'),$request->input('num'),$request->input('date'));
        if(in_array("Daily Double",unserialize($wager->extracted))){
            $checkDDinArray = unserialize($wager->extracted);
            if($request->input('num') == 1){
                $ddKey = array_search("Daily Double",$checkDDinArray);
                unset($checkDDinArray[$ddKey]);
            }
            return $checkDDinArray;
        }else{
            $checkDDinArray = unserialize($wager->extracted);
            if($checkDDinFirstRace == 1){
                array_push($checkDDinArray,"Daily Double");
            }else{

            }
            return $checkDDinArray;
        }
    }
    public function testODBC(){
        $odbc = odbc_connect('cust','','');
        $query = "select * from cust.dbf";
        $res = odbc_exec($odbc,$query);
        while($row = odbc_fetch_array($res)){
            echo $row["NAME"] . "<br/>";
        }
        $insert = "update cust.dbf as a set CODE = '120' where uname(NAME) = 'TEST'";
        echo $insert;
        $in = odbc_exec($odbc,$insert);
        echo odbc_error($odbc);
        odbc_close($odbc);
    }
    public function scratches(){
        $theme = Theme::uses('admin')->layout('layout')->setTitle('Admin');
        return $theme->of('admin/scratches')->render();
    }
    public function getScratchesToday(Request $request){
        $selectedDate = $request->input("date");
        $scratchesModel = new Scratches();
        $betsBySelectedDate = $scratchesModel->getScratchesByDate($selectedDate);
        $tempArr = [];
        $tempObj = [
            'data' => []
        ];
        foreach ($betsBySelectedDate as $index => $value){
            $tempArr = [
                'race_number' => "Race " . $value->race_number,
                'race_track' => Tracks::getTrackNameWithCode($value->race_track)->name,
                'race_date' => $value->race_date,
                'pnumber' => $value->pnumber,
                'horsename' => $value->horsename
            ];
            array_push($tempObj["data"],$tempArr);
        }
        return json_encode($tempObj);
    }
    public function balanceInquiry(Request $request){
        if (!isset($_SESSION)) session_start();
        $NAME = $request->input("name");
        $odbc = odbc_connect($_SESSION['dsn'],'','');
        $query = "select * from cust.dbf as a where ucase(NAME) = '". strtoupper($NAME) ."'";

        $queryResult = odbc_exec($odbc,$query);
        while($row = odbc_fetch_array($queryResult)){
            $balance = $row["BALANCE"] + $row["CAP"] + $row["CURRENTBET"] + $row["MON_RSLT"] + $row["TUE_RSLT"] + $row["WED_RSLT"] + $row["THU_RSLT"] + $row["FRI_RSLT"] + $row["SAT_RSLT"] + $row["SUN_RSLT"];
        }
        odbc_close($odbc);
        if($balance){
            return $balance;
        }else{
            return "NULL";
        }
    }
    public function updateCurrentBet(Request $request){
        if (!isset($_SESSION)) session_start();
        $NAME = $request->input("name");
        $betTotal = $request->input("betTotal");
        $odbc = odbc_connect($_SESSION['dsn'],'','');
        $getCurrentBetQuery = "select * from cust.dbf as a where ucase(NAME) = '". strtoupper($NAME) ."'";
        $queryResult = odbc_exec($odbc,$getCurrentBetQuery);
        while($row = odbc_fetch_array($queryResult)){
            $CURRENTBET = $row["CURRENTBET"];
        }
        $newCURRENTBET = $CURRENTBET - $betTotal;
        $updateCURRENTBETQuery = "update cust.dbf as a set a.CURRENTBET = '". $newCURRENTBET ."' where ucase(NAME) = '". strtoupper($NAME) ."'";
        $updateResult = odbc_exec($odbc,$updateCURRENTBETQuery);
//        odbc_close($odbc);
        if($updateResult){
            return 0;
        }else{
            return 1; // failed updated
        }
    }
    public static function findDSN($NAME){
        return "cust";
    }
    public static function returnBalance($id,$betAmount,$gradingStatus,$dsn){
        if($gradingStatus == 0){
            $odbc = odbc_connect($dsn,'','');
            $getCurrentBetQuery = "select * from cust.dbf as a where ucase(NAME) = '". strtoupper($id) ."'";
            $queryResult = odbc_exec($odbc,$getCurrentBetQuery);
            while($row = odbc_fetch_array($queryResult)){
                $CURRENTBET = $row["CURRENTBET"];
            }
            $newCurrentBet = $CURRENTBET + $betAmount;
            $updateCURRENTBETQuery = "update cust.dbf as a set a.CURRENTBET = '". $newCurrentBet ."' where ucase(NAME) = '". strtoupper($id) ."'";
            odbc_exec($odbc,$updateCURRENTBETQuery);
            odbc_close($odbc);
        }
    }
    public static function computerRSLT($id,$winAmount,$gradingStatus,$date,$operation,$dsn){
        if($gradingStatus == 0){
            $office = "Office";
            $odbc = odbc_connect($dsn,'','');
            $splitStr = str_split($date,2);
            $newDateStr = $splitStr[2] . $splitStr[0] . $splitStr[1];
            $date = date('N',strtotime(implode('-',str_split($newDateStr,2))));
            $RSLT = "";
            switch ($date){
                case 1:
                    $RSLT = "MON_RSLT";
                    break;
                case 2:
                    $RSLT = "TUE_RSLT";
                    break;
                case 3:
                    $RSLT = "WED_RSLT";
                    break;
                case 4:
                    $RSLT = "THU_RSLT";
                    break;
                case 5:
                    $RSLT = "FRI_RSLT";
                    break;
                case 6:
                    $RSLT = "SAT_RSLT";
                    break;
                case 7:
                    $RSLT = "SUN_RSLT";
                    break;
            }
            $selectQuery = "select * from cust.dbf as a where ucase(NAME) = '". strtoupper($id) ."'";
            $officeSelectQuery = "select * from cust.dbf as a where ucase(NAME) = '". strtoupper($office) ."'";
            $selectResult = odbc_exec($odbc,$selectQuery);
            $officeSelectResult = odbc_exec($odbc,$officeSelectQuery);
            while($row = odbc_fetch_array($selectResult)){
                $oldRSLT = $row[$RSLT];
            }
            while($row = odbc_fetch_array($officeSelectResult)){
                $officeOldRSLT = $row[$RSLT];
            }
            if($operation == "add"){
                $newRSLT = $oldRSLT + $winAmount;
                $officeNewRSLT = $officeOldRSLT + ($winAmount * -1);
//                $newBALANCE = $oldBALANCE + $winAmount;
            }elseif ($operation == "subtract"){
                $newRSLT = $oldRSLT - $winAmount;
//                $newBALANCE = $oldBALANCE - $winAmount;
            }
            $updateQuery = "update cust.dbf as a set ". $RSLT ." = '". $newRSLT ."' where ucase(NAME) = '". strtoupper($id) ."' ";
            $officeUpdateQuery = "update cust.dbf as a set ". $RSLT ." = '". $officeNewRSLT ."' where ucase(NAME) = '". strtoupper($office) ."' ";
            odbc_exec($odbc,$updateQuery);
            odbc_exec($odbc,$officeUpdateQuery);
//            if($operation == "subtract"){
//                $test = odbc_exec($odbc,"select * from cust.dbf as a where ucase(NAME) = '". strtoupper($id) ."'");
//                while($row = odbc_fetch_array($test)){
//                    dd($row["BALANCE"]);
//                }
//            }
            odbc_close($odbc);
        }
    }
    public static function gradeWrongBetsDD($raceDate,$trackCode, $raceNumLessOne){
        $dataArray = [
            "status" => 1,
            "result" => 2, // 2 for lose
            "grading_status" => 1
        ];
        $loseBets =  DB::table("bets")
            ->where("race_track",$trackCode)
            ->where("race_date",$raceDate)
            ->where("race_number", $raceNumLessOne)
            ->where("status",0)
            ->where("bet_type","dailydouble")
            ->get();
        foreach ($loseBets as $index => $value){
            if($value->grading_status == 0){
//                AdminController::returnBalance($value->player_id,$value->bet_amount,$value->grading_status,$value->dsn);
                AdminController::collectBalance($value->player_id,$value->bet_amount,$value->grading_status,$value->dsn);
                AdminController::collectRSLT($value->player_id,$value->bet_amount,$value->grading_status,$value->race_date,"subtract",$value->dsn);
//                AdminController::computerRSLT($value->player_id,$value->bet_amount,$value->grading_status,$value->race_date,"subtract",$value->dsn);
            }
            DB::table("bets")->where('id',$value->id)->update($dataArray);

        }

    }
    public static $usernameArray = [];
    public static $usernameArrayForRSLT = [];
    public static function collectBalance($player_id,$betAmount,$grading_status,$dsn){
        if($grading_status == 0){
            array_push(self::$usernameArray,["name" => $player_id,"betAmount" => $betAmount,"dsn" => $dsn]);
        }
    }
    public static function collectRSLT($player_id,$betAmount,$grading_status,$date,$operation,$dsn){
        if($grading_status == 0){
            array_push(self::$usernameArrayForRSLT,["name" => $player_id,"betAmount" => $betAmount,"dsn" => $dsn,"date" => $date, "operation" => $operation]);
        }
    }
    public static function totalBalance(){
//        $names = ["shawn" => 0,"test" => 0];
        $names = [];
        foreach(self::$usernameArray as $index => $value){
            if (array_key_exists($value["name"],$names)) {
//                echo "Key exists!" . $value["name"] . "<br/>";
            }
            else {
                $names[$value["name"]] = [0,$value["dsn"]];
            }
        }
        foreach(self::$usernameArray as $index => $value){
//            echo $names[$value["name"]] . $value["name"] . "+" . $value["betAmount"] . "<br/>";
            $names[$value["name"]][0] += $value["betAmount"];
        }
//        dd(self::$usernameArray);
//        dd($names);
        foreach ($names as $key => $val){
            AdminController::returnBalance($key,$val[0],0,$val[1]);
        }
    }
    public static function totalRSLT(){
        $names = [];
        foreach(self::$usernameArrayForRSLT as $index => $value){
            if (array_key_exists($value["name"],$names)) {

            }
            else {
                $names[$value["name"]] = [0,$value["dsn"],$value["date"]];
            }
        }
        foreach(self::$usernameArrayForRSLT as $index => $value){
            echo $value["operation"] . $value["betAmount"] . $value["name"] . "<br/>";
            if($value["operation"] == "add"){
                $names[$value["name"]][0] += $value["betAmount"];
//                echo $names[$value["name"]][0] . " PLUS " . "<br/>";
            }elseif ($value["operation"] == "subtract"){
                $names[$value["name"]][0] -= $value["betAmount"];
//                echo $names[$value["name"]][0] . " Substract " . "<br/>";
            }
        }
        foreach ($names as $key => $val){
            var_dump($val[0]);
            AdminController::computerRSLT($key,$val[0],0,$val[2],"add",$val[1]);
        }
    }
    public static function balanceInquiryAdmin($sUsername,$dsn){
        $odbc = odbc_connect($dsn,'','');
        $query = "select * from cust.dbf as a where ucase(NAME) = '". strtoupper($sUsername) ."'";
        $queryResult = odbc_exec($odbc,$query);
        while($row = odbc_fetch_array($queryResult)){
            $balance = $row["BALANCE"] + $row["CAP"] + $row["CURRENTBET"] + $row["MON_RSLT"] + $row["TUE_RSLT"] + $row["WED_RSLT"] + $row["THU_RSLT"] + $row["FRI_RSLT"] + $row["SAT_RSLT"] + $row["SUN_RSLT"];
        }
        odbc_close($odbc);
        if($balance){
            return $balance;
        }else{
            return "NULL";
        }
    }
    public static function updateOdbc($money,$username,$operation,$date,$dsn){
        $office = "Office";
        $odbc = odbc_connect($dsn,'','');
        $getCurrentBetQuery = "select * from cust.dbf as a where ucase(NAME) = '". strtoupper($username) ."'";
        $getOfficeQuery = "select * from cust.dbf as a where a.NAME = 'Office'";
        if($date != "worthless"){
            $splitStr = str_split($date,2);
            $newDateStr = $splitStr[2] . $splitStr[0] . $splitStr[1];
            $date = date('N',strtotime(implode('-',str_split($newDateStr,2))));
            $RSLT = "";
            switch ($date){
                case 1:
                    $RSLT = "MON_RSLT";
                    break;
                case 2:
                    $RSLT = "TUE_RSLT";
                    break;
                case 3:
                    $RSLT = "WED_RSLT";
                    break;
                case 4:
                    $RSLT = "THU_RSLT";
                    break;
                case 5:
                    $RSLT = "FRI_RSLT";
                    break;
                case 6:
                    $RSLT = "SAT_RSLT";
                    break;
                case 7:
                    $RSLT = "SUN_RSLT";
                    break;
            }
            $queryResult = odbc_exec($odbc,$getCurrentBetQuery);
            $officeResult = odbc_exec($odbc,$getOfficeQuery);
            while($row = odbc_fetch_array($queryResult)){
                $CURRENTBET = $row["CURRENTBET"];
                $rsltToday  = $row[$RSLT];
            }
            while($row = odbc_fetch_array($officeResult)){
                $officeCURRENTBET = $row["CURRENTBET"];
                $officeRsltToday  = $row[$RSLT];
            }
        }else{
            $queryResult = odbc_exec($odbc,$getCurrentBetQuery);
            $officeResult = odbc_exec($odbc,$getOfficeQuery);
            while($row = odbc_fetch_array($queryResult)){
                $CURRENTBET = $row["CURRENTBET"];
            }
            while($row = odbc_fetch_array($officeResult)){
                $officeCURRENTBET = $row["CURRENTBET"];
            }
        }
        switch ($operation){
            case "addBet":
                $newCURRENTBET = $CURRENTBET + $money; // money = BetAmount (must be negative)
                $updateCURRENTBETQuery = "update cust.dbf as a set a.CURRENTBET = '". $newCURRENTBET ."' where ucase(NAME) = '". strtoupper($username) ."'";
                odbc_exec($odbc,$updateCURRENTBETQuery);
                break;
            case "subtractBet":
                $newCURRENTBET = $CURRENTBET - $money; // money = BetAmount
                $updateCURRENTBETQuery = "update cust.dbf as a set a.CURRENTBET = '". $newCURRENTBET ."' where ucase(NAME) = '". strtoupper($username) ."'";
                odbc_exec($odbc,$updateCURRENTBETQuery);
                break;
            case "pendingToWin":
                $newRsltToday = $rsltToday + $money;
                $officeTemp = $officeRsltToday + ($money * -1);
//                $officeNewRsltToday = $officeTemp * -1;
                $newRSLT = "update cust.dbf as a set ". $RSLT ." = '". $newRsltToday ."' where ucase(NAME) = '". strtoupper($username) ."'";
                $officeNewRSLT = "update cust.dbf as a set ". $RSLT ." = '". $officeTemp ."' where ucase(NAME) = '". strtoupper($office) ."'";
                odbc_exec($odbc,$newRSLT);
                odbc_exec($odbc,$officeNewRSLT);
                break;
            case "pendingToLose":
                $newRsltToday = $rsltToday - $money;
                $officeTemp = $officeRsltToday - ($money * -1);
//                $officeNewRsltToday = $officeTemp * -1;
                $newRSLT = "update cust.dbf as a set ". $RSLT ." = '". $newRsltToday ."' where ucase(NAME) = '". strtoupper($username) ."'";
                $officeNewRSLT = "update cust.dbf as a set ". $RSLT ." = '". $officeTemp ."' where ucase(NAME) = '". strtoupper($office) ."'";
                odbc_exec($odbc,$newRSLT);
                odbc_exec($odbc,$officeNewRSLT);
                break;
            case "loseToWin":
                // lose to win
                $rslt = $rsltToday + $money[0];
                $newRsltToday = $rslt + $money[1];
                $officeRslt = $officeRsltToday + ($money[0] * -1);
                $officeTemp = $officeRslt + ($money[1] * -1);
//                $officeNewRsltToday = $officeTemp * -1;
                $newRSLT = "update cust.dbf as a set ". $RSLT ." = '". $newRsltToday ."' where ucase(NAME) = '". strtoupper($username) ."'";
                $officeNewRSLT = "update cust.dbf as a set ". $RSLT ." = '". $officeTemp ."' where ucase(NAME) = '". strtoupper($office) ."'";
                odbc_exec($odbc,$newRSLT);
                odbc_exec($odbc,$officeNewRSLT);
                break;
            case "winToLose":
                // win to lose
                $rslt = $rsltToday - $money[1]; // minus previous winAmount
                $newRsltToday = $rslt - $money[0]; // minus betamount
                $officeRslt = $officeRsltToday - ($money[1] * -1);
                $officeTemp = $officeRslt - ($money[0] * -1);
//                $officeNewRsltToday = $officeTemp * -1;
                $newRSLT = "update cust.dbf as a set ". $RSLT ." = '". $newRsltToday ."' where ucase(NAME) = '". strtoupper($username) ."'";
                $officeNewRSLT = "update cust.dbf as a set ". $RSLT ." = '". $officeTemp ."' where ucase(NAME) = '". strtoupper($office) ."'";
                odbc_exec($odbc,$newRSLT);
                odbc_exec($odbc,$officeNewRSLT);
                break;
            case "winToAborted":
                // win to aborted
                $rslt = $rsltToday - $money[1]; // minus previous winAmount
                $officeTemp = $officeRsltToday - ($money[1] * -1);
//                $officeRslt = $officeTemp * -1;
                $newRSLT = "update cust.dbf as a set ". $RSLT ." = '". $rslt ."' where ucase(NAME) = '". strtoupper($username) ."'";
                $officeNewRSLT = "update cust.dbf as a set ". $RSLT ." = '". $officeTemp ."' where ucase(NAME) = '". strtoupper($office) ."'";
                odbc_exec($odbc,$newRSLT);
                odbc_exec($odbc,$officeNewRSLT);
                break;
            case "loseToAborted":
                // lose to aborted
                $rslt = $rsltToday - $money[0]; // minus previous winAmount
                $officeTemp = $officeRsltToday - ($money[0] * -1);
//                $officeRslt = $officeTemp * -1;
                $newRSLT = "update cust.dbf as a set ". $RSLT ." = '". $rslt ."' where ucase(NAME) = '". strtoupper($username) ."'";
                $officeNewRSLT = "update cust.dbf as a set ". $RSLT ." = '". $officeTemp ."' where ucase(NAME) = '". strtoupper($office) ."'";
                odbc_exec($odbc,$newRSLT);
                odbc_exec($odbc,$officeNewRSLT);
                break;
            case "noPayoutToAborted":
                // noPayout to aborted
                $newCURRENTBET = $CURRENTBET + $money;
                $rslt = $rsltToday - $money; // minus previous winAmount
                $officeTemp = $officeRsltToday - ($money * -1);
//                $officeRslt = $officeTemp * -1;
//                $newRSLT = "update cust.dbf as a set ". $RSLT ." = '". $rslt ."',a.CURRENTBET = '". $newCURRENTBET ."' where ucase(NAME) = '". strtoupper($username) ."'";
                $newRSLT = "update cust.dbf as a set a.CURRENTBET = '". $newCURRENTBET ."' where ucase(NAME) = '". strtoupper($username) ."'";
                $officeNewRSLT = "update cust.dbf as a set ". $RSLT ." = '". $officeTemp ."' where ucase(NAME) = '". strtoupper($office) ."'";
                odbc_exec($odbc,$newRSLT);
                odbc_exec($odbc,$officeNewRSLT);
                break;
            case "AbortedToWin":
                // aborted to win
                $rslt = $rsltToday + $money[1]; // minus previous winAmount
                $officeTemp = $officeRsltToday + ($money[1] * -1);
                $newRSLT = "update cust.dbf as a set ". $RSLT ." = '". $rslt ."' where ucase(NAME) = '". strtoupper($username) ."'";
                $officeNewRSLT = "update cust.dbf as a set ". $RSLT ." = '". $officeTemp ."' where ucase(NAME) = '". strtoupper($office) ."'";
                odbc_exec($odbc,$newRSLT);
                odbc_exec($odbc,$officeNewRSLT);
                break;
        }
    }
    public function raceTime(){
        $horseModel = new Horses();
        $racesTime = $horseModel->getRacesTime(["raceDate" => "112917"]);
        $dataArray = [
            'raceTime' => $racesTime
        ];
        $theme = Theme::uses('admin')->layout('layout')->setTitle('RaceTime');
        return $theme->of('admin/raceTime',$dataArray)->render();
    }
    public function submitRaceTime(Request $request){
        $horseModel = new Horses();
        $newTime = trim($request->input("time"));
        $explode = explode(':',$newTime);
        if($explode[0] < 10){
            $sTime = " " . $newTime;
        }else{
            $sTime = $newTime;
        }
        $update = $horseModel->updateRaceTime([
            "date" => $request->input("date"),
            "trk" => $request->input("trk"),
            "num" => $request->input("num"),
            "newTime" => $sTime,
        ]);
        if($update){
            return 0;
        }else{
            return 1;
        }
    }
}
