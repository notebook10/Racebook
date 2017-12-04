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
            ->first(["time_zone","track_name"]);
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
    public function getTracksNotToday($trkArray){
        $arrayOfTracks = [];
        $allTracks = DB::table($this->table)->get();
        $foo = json_decode($allTracks);
//        echo $foo[$index]->track_name;
        foreach ($trkArray as $key => $value){
            foreach ($foo as $index => $val){
                if($foo[$index]->track_code == $value){
                    unset($allTracks[$index]);
                }
            }
        }
        return $allTracks;
    }
}
