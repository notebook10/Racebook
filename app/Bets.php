<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Bets extends Model
{
    protected $table = 'bets';
    public function saveBets($dataArray){
        $save = new Bets();
        $save->player_id = $dataArray['user'];
        $save->race_number = $dataArray['raceNum'];
        $save->race_track = $dataArray['track'];
        $save->bet_type = $dataArray['bettype'];
        $save->bet_amount = $dataArray['betamount'];
        $save->post_time = $dataArray['racePost'];
        $save->status = '0';
        $save->type = $dataArray['type'];
        $save->bet = $dataArray['bet'];
        $save->save();
    }
    public function insertBets($dataArray){
        return DB::table($this->table)
            ->insert($dataArray);
    }
    public function getAllBets($authId){
        return DB::table($this->table)
            ->where('player_id',$authId)
            ->get();
    }
    public function getPendingBets($authId){
        return DB::table($this->table)
            ->where('player_id',$authId)
            ->where('status',0)
            ->get();
    }
}
