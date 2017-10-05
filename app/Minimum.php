<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class Minimum extends Model
{
    protected $table = "minimum";
    public function insertMin($dataArray){
        $logsModel = new Logs();
        if($dataArray["operation"] == 0){
            $minimum = new Minimum();
            $minimum->track_code = $dataArray['trk'];
            $minimum->race_date = $dataArray['date'];
            $minimum->race_number = $dataArray['num'];
            $minimum->content = $dataArray['min'];
            $minimum->save();
            $logsArray = [
                'user_id' => Auth::id(),
                'action' => 'Save Minimum => ' . $dataArray['trk'] . ' ' . $dataArray['date']
            ];
            $logsModel->saveLog($logsArray);
            return 0;
        }else{
            $arr = ['content' => $dataArray["min"]];
            DB::table($this->table)
                ->where("track_code", $dataArray["trk"])
                ->where("race_date", $dataArray["date"])
//                ->where("race_number", $dataArray["num"])
                ->update($arr);
            $logsArray = [
                'user_id' => Auth::id(),
                'action' => 'Update minimum => ' . $dataArray["trk"] . ' ' . $dataArray["date"]
            ];
            $logsModel->saveLog($logsArray);
            return 1;
        }
    }
    public function checkMinimum($dataArray){
        return DB::table($this->table)
            ->where('track_code',$dataArray['trk'])
            ->where('race_date',$dataArray['date'])
            ->where('race_number',$dataArray['num'])
            ->get();
    }
    public function getMinimum($dataArray){
        return DB::table($this->table)
            ->where("track_code", $dataArray["trk"])
            ->where("race_date", $dataArray["date"])
            ->get();
    }
}
