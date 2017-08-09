<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Cancelled extends Model
{
    //
    protected $table = "cancelled";
    public function check($dataArray){
        return DB::table($this->table)
            ->where("track_code", $dataArray["trk"])
            ->where("race_number", $dataArray["num"])
            ->where("race_date", $dataArray["date"])
            ->where("status",1)
            ->get();
    }
}
