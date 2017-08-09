<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Timezone;
class Tracks extends Model
{
    protected $table = "tracks";
    public function getAllTracks($date){
        return DB::table($this->table)
            ->where('date',$date)
            ->where('visibility',0)
//            ->orWhere('_showTemp',1)
            ->get();
    }
    public function getAllTracksForAdmin($date){
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
    public static function getTrackNameWithCode($code){
        return DB::table('tracks')
            ->where('code',$code)
            ->first();
    }
    public function getTracksWithoutToday($date){
        $tmzModel = new Timezone();
        $trkArray= [];
        $trkToday = DB::table($this->table)
            ->where("date",$date)
            ->get();
        foreach ($trkToday as $key => $value){
            array_push($trkArray, $value->code);
        }
        $filteredTracks = $tmzModel->getTracksNotToday($trkArray);
        return $filteredTracks;
    }
    public function submitNewTrack($trkName, $trkCode , $date){
        $dataArray = [
            "name" => " " . $trkName,
            "code" => $trkCode,
            "visibility" => "0",
            "date" => $date
        ];
        return DB::table($this->table)
            ->insert($dataArray);
    }
    public function removeTrack($trk,$date,$operation){
        $arr = [
            "visibility" => $operation == 1 ? 1 : 0
        ];
        return DB::table($this->table)
            ->where("code",$trk)
            ->where("date",$date)
            ->update($arr);
    }
    public function getAllTracksTomorrowForAdmin($date){
        return DB::table($this->table)
            ->where('date',$date)
            ->get();
    }
    public function showTemp($date,$trk,$operation){
        if($operation == 1){
            // Show
            return DB::table($this->table)
                ->where("code",$trk)
                ->where("date",$date)
                ->update(["_showTemp"=>1]);
        }else if($operation == 0){
            // Hide
            return DB::table($this->table)
                ->where("code",$trk)
                ->where("date",$date)
                ->update(["_showTemp"=>0]);
        }
    }
    public function getShowTemp(){
        return DB::table($this->table)
            ->where("_showTemp",1)
            ->get();
    }
}
