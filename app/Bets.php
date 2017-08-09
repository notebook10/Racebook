<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Bets extends Model
{
    protected $table = 'bets';
    public function saveBets($dataArray){
        $save = new Bets();
        $save->player_id = $dataArray['user'];
        $save->race_number = $dataArray['raceNum'];
        $save->race_track = $dataArray['track'];
        $save->bet_type = $dataArray['bettype'];
        $save->bet_amount = $dataArray['betamount'];
        $save->post_time = $dataArray['racePost'];
        $save->status = '0';
        $save->type = $dataArray['type'];
        $save->bet = $dataArray['bet'];
        $save->save();
    }
    public function insertBets($dataArray){
        date_default_timezone_set('America/Los_Angeles');
        $pacificDate = date('Y-m-d H:i:s', time());
        $raceDate = date('mdy',time());
        foreach ($dataArray as $key => $value){
            $dataArray[$key]['created_at'] = $pacificDate;
            $dataArray[$key]['updated_at'] = $pacificDate;
            $dataArray[$key]['race_date'] = $raceDate;
        }
        return DB::table($this->table)
            ->insert($dataArray);
    }
    public function getAllBets($authId){
        return DB::table($this->table)
            ->where('player_id',$authId)
            ->orderBy('created_at','desc')
            ->get();
    }
    public function getPendingBets($authId){
        return DB::table($this->table)
            ->where('player_id',$authId)
            ->where('status',0)
            ->orderBy('created_at','desc')
            ->get();
    }
    public function getAll(){
        date_default_timezone_set('America/Los_Angeles');
        $currentDate = date("Y-m-d", time());
        return DB::table($this->table)
            ->orderBy('created_at','desc')
//            ->whereBetween('created_at',[$currentDate . ' 00:00:00',$currentDate . ' 23:59:59'])
            ->get();
    }
    public function getBetsByDate($date){
        $currentDate = $date;
        return DB::table($this->table)
            ->whereBetween("created_at",[$currentDate . " 00:00:00", $currentDate . " 23:59:59"])
            ->get();
    }
//    public function checkExacta($trkCode, $raceDate, $raceNum, $combination, $wagerType){
//        return DB::table($this->table)
//            ->where("race_track", $trkCode)
//            ->where("race_number",$raceNum)
//            ->where("race_date",$raceDate)
//            ->where("bet",$combination)
//            ->where("bet_type",$wagerType)
//            ->get();
//    }
//    public function checkExactaBox($trkCode, $raceDate, $raceNum, $combination){
//        return DB::table($this->table)
//            ->where("race_track", $trkCode)
//            ->where("race_number",$raceNum)
//            ->where("race_date",$raceDate)
//            ->where("bet",$combination)
//            ->where("bet_type","exactabox")
//            ->get();
//    }
//    public function checkTrifecta($trkCode, $raceDate, $raceNum, $combination){
//        return DB::table($this->table)
//            ->where("race_track", $trkCode)
//            ->where("race_number",$raceNum)
//            ->where("race_date",$raceDate)
//            ->where("bet",$combination)
//            ->where("bet_type","trifecta")
//            ->get();
//    }
    public function checkWinners($trkCode, $raceDate, $raceNum, $combination, $wagerType){
        return DB::table($this->table)
            ->where("race_track", $trkCode)
            ->where("race_number",$raceNum)
            ->where("race_date",$raceDate)
            ->where("bet",$combination)
            ->where("bet_type",$wagerType)
            ->get();
    }
    public function checkWps($trkCode, $raceDate, $raceNum, $combination, $wagerType, $type){
        return DB::table($this->table)
            ->where("race_track", $trkCode)
            ->where("race_number",$raceNum)
            ->where("race_date",$raceDate)
            ->where("bet_type",$wagerType)
            ->where("type",$type)
            ->where("bet",$combination)
            ->get();
    }
    public function checkWinnersForDD($trkCode, $raceDate, $raceNum, $combination, $wagerType){
        return DB::table($this->table)
            ->where("race_track", $trkCode)
            ->where("race_number",$raceNum - 1)
            ->where("race_date",$raceDate)
            ->where("bet",$combination)
            ->where("bet_type",$wagerType)
            ->get();
    }
    public function getBetsForScratch($dataArray){
        return DB::table($this->table)
            ->where("race_track",$dataArray["trk"])
            ->where("race_number", $dataArray["num"])
            ->where("race_date", $dataArray["date"])
            ->get();
    }
    public function scratchBet($id){
        return DB::table($this->table)
            ->where("id", $id)
            ->update([
                "status" => 1,
                "result" => 3, // SCRATCHED !!!
            ]);
    }
    public function saveNewBet($betArray){
        return DB::table($this->table)
            ->insert($betArray);
    }
    public function undoScratch($id){
        return DB::table($this->table)
            ->where("id",$id)
            ->update([
                "status" => 0,
                "result" => 0
            ]);
    }
    public function cancelWager($dataArray){
        return DB::table($this->table)
            ->where("race_date", $dataArray["date"])
            ->where("bet_type", $dataArray["wagerType"])
            ->where("race_track", $dataArray["trk"])
            ->where("race_number", $dataArray["num"])
            ->update([
                "status" => 1,
                "result" => 3
            ]);
    }
    public function noShow($dataArray){
        return DB::table($this->table)
            ->where("race_date", $dataArray["date"])
            ->where("race_track", $dataArray["trk"])
            ->where("race_number", $dataArray["num"])
            ->where("bet_type","wps")
            ->where("type","s")
            ->update([
                "status" => 1,
                "result" => 3
            ]);
    }
    public function cancelWagerShow($dataArray){
        return DB::table($this->table)
            ->where("race_date", $dataArray["date"])
            ->where("type", $dataArray["wagerType"])
            ->where("race_track", $dataArray["trk"])
            ->where("race_number", $dataArray["num"])
            ->update([
                "status" => 1,
                "result" => 3
            ]);
    }
}
