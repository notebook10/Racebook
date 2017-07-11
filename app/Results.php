<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DB;

class Results extends Model
{
    protected $table = "results";
    public function insertResult($dataArray, $operation){
        if($operation == 0){
            // Add
            $result = new Results();
            $result->track_code = $dataArray["track_code"];
            $result->race_number = $dataArray["race_number"];
            $result->race_date = $dataArray["race_date"];
            $result->race_winners = $dataArray["first"] . "," . $dataArray["second"] . "," . $dataArray["third"] . "," . $dataArray["fourth"];
            $result->graded_by = Auth::id();
            $result->save();
        }else{
            // Update
            $refactoredArray = [
//                'track_code' => $dataArray["track_code"],
//                'race_number' => $dataArray["race_number"],
//                'race_date' => $dataArray["race_date"],
                'race_winners' => $dataArray["first"] . "," . $dataArray["second"] . "," . $dataArray["third"] . "," . $dataArray["fourth"]
            ];
            return DB::table($this->table)
                ->where("track_code", $dataArray["track_code"])
                ->where("race_number",$dataArray["race_number"])
                ->where("race_date", $dataArray["race_date"])
                ->update($refactoredArray);
        }
    }
    public function checkResults($trkCode, $date, $raceNum){
        return DB::table($this->table)
            ->where("track_code",$trkCode)
            ->where("race_date",$date)
            ->where("race_number", $raceNum)
            ->first();
    }
    public function getLatestResult(){
        return DB::table($this->table)
            ->orderBy("updated_at","desc")
            ->get();
    }
}
