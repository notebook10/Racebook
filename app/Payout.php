<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Payout extends Model
{
    protected $table = "payout";
    public function submitPayout($dataArray, $operation, $trk, $num , $date){
        if($operation == 0){
            // save
            $payout = new Payout();
            $payout->track_code = $trk;
            $payout->race_date = $date;
            $payout->race_number = $num;
            $payout->content = json_encode($dataArray);
            $payout->save();
        }elseif ($operation == 1){
            // Update
            DB::table($this->table)
                ->where("track_code", $trk)
                ->where("race_date", $date)
                ->where("race_number", $num)
                ->update(["content"=> json_encode($dataArray)]);
        }else{
            return "Else";
        }
    }
    public function checkPayout($trkCode, $raceNum, $date){
        $temp = DB::table($this->table)
            ->where("track_code", $trkCode)
            ->where("race_number", $raceNum)
            ->where("race_date", $date)
            ->orderBy("created_at","DESC")
            ->get();
        return $temp;
    }
    public static function getPayoutForVerification($trkCode, $raceNum, $date){
        return DB::table("payout")
            ->where("track_code", $trkCode)
            ->where("race_number", $raceNum)
            ->where("race_date", $date)
            ->first();
    }
}
