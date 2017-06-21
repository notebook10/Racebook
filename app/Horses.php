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
}
