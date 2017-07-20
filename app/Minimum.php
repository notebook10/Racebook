<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Minimum extends Model
{
    protected $table = "minimum";
    public function insertMin($dataArray){
        if($dataArray["operation"] == 0){
            $minimum = new Minimum();
            $minimum->track_code = $dataArray['trk'];
            $minimum->race_date = $dataArray['date'];
//            $minimum->race_number = $dataArray['num'];
            $minimum->content = $dataArray['min'];
            $minimum->save();
            return 0;
        }else{
            $arr = ['content' => $dataArray["min"]];
            DB::table($this->table)
                ->where("track_code", $dataArray["trk"])
                ->where("race_date", $dataArray["date"])
//                ->where("race_number", $dataArray["num"])
                ->update($arr);
            return 1;
        }
    }
    public function checkMinimum($dataArray){
        return DB::table($this->table)
            ->where('track_code',$dataArray['trk'])
            ->where('race_date',$dataArray['date'])
//            ->where('race_number',$dataArray['num'])
            ->get();
    }
    public function getMinimum($dataArray){
        return DB::table($this->table)
            ->where("track_code", $dataArray["trk"])
            ->where("race_date", $dataArray["date"])
            ->first();
    }
}
