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
            ->get();
    }
    public function getHorsesPerRace($trackCode, $date, $num){
        return DB::table($this->table)
            ->where('race_track',$trackCode)
            ->where('race_date',$date)
            ->where('race_number', "Race " . $num)
            ->get();
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
            ->get();
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
}
