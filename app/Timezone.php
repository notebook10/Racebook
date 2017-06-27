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
}
