<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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
        $logsModel = new Logs();
        if($operation == 0){
            $logsArray = [
                'user_id' => Auth::id(),
                'action' => 'Save New Wager'
            ];
            $logsModel->saveLog($logsArray);
            $wager = new Wager();
            $wager->track_code = $dataArray["code"];
            $wager->race_number = "Race " . $dataArray["num"];
            $wager->extracted = serialize($dataArray["wager"]);
            $wager->race_date = $dataArray["date"];
            $wager->save();
        }else if($operation == 1){
            $logsArray = [
                'user_id' => Auth::id(),
                'action' => 'Update Wager => ' . $dataArray["code"] . ' ' . "Race " . $dataArray["num"] . ' ' . $dataArray["date"]
            ];
            $logsModel->saveLog($logsArray);
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
    public function getWagerForRaceFirstRace($trk, $num, $date){ // Check DD sa race -1
        $raceMinusOne = $num -1;
        $findDD = DB::table($this->table)
            ->where('track_code',$trk)
            ->where('race_number','Race ' . $raceMinusOne)
            ->where('race_date',$date)
            ->first();
        if(strpos($findDD->extracted,"Daily Double") !== false){
            return 1;
        }else{
            return 0;
        }
    }
}
