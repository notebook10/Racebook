<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Wager extends Model
{
    protected $table = "wager";
    public function getAllWager(){
        date_default_timezone_set('America/Los_Angeles');
        $pacificDate = date('mdy',time());
        return DB::table($this->table)
            ->where('race_date',$pacificDate)
            ->get();
    }
    public function getWagerForRace($trk, $num, $date){
        return DB::table($this->table)
            ->where('track_code',$trk)
            ->where('race_number','Race ' . $num)
            ->where('race_date',$date)
            ->first();
    }
}