<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Tracks extends Model
{
    protected $table = "tracks";
    public function getAllTracks($date){
        return DB::table($this->table)
            ->where('date',$date)
            ->get();
    }
    public function getTrackName($trackCode){
        return DB::table($this->table)
            ->where('code',$trackCode)
            ->first();
    }
    public function getTrackWithTimeZone($date){
        return DB::table($this->table)
            ->where('date',$date)
            ->leftjoin('tracks_timezone','tracks.code','=', 'tracks_timezone.track_code')
            ->select('tracks_timezone.id','tracks.name','tracks.code','tracks.date','tracks_timezone.time_zone')
            ->get();
    }
}
