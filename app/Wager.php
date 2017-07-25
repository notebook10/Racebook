<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Wager extends Model
{
    protected $table = "wager";
    public function getAllWager(){
        date_default_timezone_set('America/Los_Angeles');
        $pacificDate = date('mdy',time());
        return DB::table($this->table)
            ->where('race_date',$pacificDate)
            ->get();
    }
    public function getWagerForRace($trk, $num, $date){
        return DB::table($this->table)
            ->where('track_code',$trk)
            ->where('race_number','Race ' . $num)
            ->where('race_date',$date)
            ->first();
    }
    public function submitWager($dataArray,$operation,$id){
        if($operation == 0){
            $wager = new Wager();
            $wager->track_code = $dataArray["code"];
            $wager->race_number = "Race " . $dataArray["num"];
            $wager->extracted = serialize($dataArray["wager"]);
            $wager->race_date = $dataArray["date"];
            $wager->save();
        }else if($operation == 1){
            $arr = [
                "track_code" => $dataArray["code"],
                "race_number" => "Race " . $dataArray["num"],
                "extracted" => serialize($dataArray["wager"]),
                "race_date" => $dataArray["date"]
            ];
            return DB::table($this->table)
                ->where("id",$id)
                ->update($arr);
        }
    }
    public function getWagerById($id){
        return DB::table($this->table)
            ->where("id", $id)
            ->first();
    }
}
