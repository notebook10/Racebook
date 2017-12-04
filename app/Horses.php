<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Horses extends Model
{
    protected $table = "horses";
    public function getRaces($trackCode, $date){
        return DB::table($this->table)
            ->where('race_track',$trackCode)
            ->where('race_date',$date)
//            ->get(['pnumber','pp','horse','jockey','race_time','race_number','race_date','race_track']);
            ->get(['race_time','race_number']);
    }
    public function getHorsesPerRace($trackCode, $date, $num){
        return DB::table($this->table)
            ->where('race_track',$trackCode)
            ->where('race_date',$date)
            ->where('race_number', "Race " . $num)
            ->get(['pnumber','pp','horse','jockey','race_time','race_number','race_date','race_track']);
    }
    public function getRaceTime($trackCode, $date, $num){
        return DB::table($this->table)
            ->where('race_track',$trackCode)
            ->where('race_date',$date)
            ->where('race_number', $num)
            ->first();
    }
    public function test1($trackCode, $date, $num,$i){
        return DB::table($this->table)
            ->where('race_track',$trackCode)
            ->where('race_date',$date)
            ->where('race_number', "Race " .$i)
            ->first();
    }
    public function getUpcomingRaces($date , $start, $end){
        return DB::table($this->table)
            ->where('race_date',$date)
            ->whereBetween('race_time',[" " . $start," " . $end]) // -------------------------
            ->get(['pnumber','pp','horse','jockey','race_time','race_number','race_date','race_track']);
    }
    public function getHorsesByDate($date){
        return DB::table($this->table)
            ->where('race_date',$date)
            ->get();
    }
    public function scratch($id,$arr){
        return DB::table($this->table)
            ->where("id",$id)
            ->update($arr);
    }
    public function insertNewHorse($dataArray){
        return DB::table($this->table)
            ->insert($dataArray);
    }
    public function getHorseById($id){
        return DB::table($this->table)
            ->where("id",$id)
            ->first();
    }
    public function updateHorse($id, $dataArray){
        return DB::table($this->table)
            ->where("id", $id)
            ->update($dataArray);
    }
    public function undoScratch($id,$pnum){
        return DB::table($this->table)
            ->where("id",$id)
            ->update([
                "pp" => $pnum
            ]);
    }
    public function getRacesTime($dataArray){
        return DB::table($this->table)
            ->where("race_date",$dataArray["raceDate"])
            ->groupBy('race_number','race_track','race_time')
            ->get(['race_track','race_number','race_time']);
    }
    public function updateRaceTime($dataArray){
        return DB::table($this->table)
            ->where("race_date",$dataArray["date"])
            ->where("race_track",$dataArray["trk"])
            ->where("race_number",$dataArray["num"])
            ->update([
                "race_time" => $dataArray["newTime"]
            ]);
    }
}
