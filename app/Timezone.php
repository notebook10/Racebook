<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Timezone extends Model
{
    protected $table = "tracks_timezone";
    public static function getTimeZone($trackCode){
        return DB::table("tracks_timezone")
            ->where("track_code",$trackCode)
            ->pluck("time_zone");
    }
    public function getTimezoneByCode($trackCode){
        return DB::table($this->table)
            ->where("track_code",$trackCode)
            ->first();
    }
    public function getTimezoneByID($id){
        return DB::table($this->table)
            ->where("id",$id)
            ->first();
    }
    public function updateTimezoneById($id,$array){
        return DB::table($this->table)
            ->where("id",$id)
            ->update($array);
    }
    public function saveTimezone($dataArray){
        return DB::table($this->table)
            ->insert($dataArray);
    }
    public function getTrkCodeByName($trkName){
        return DB::table($this->table)
            ->where("track_name"," " . $trkName)
            ->first();
    }
}
