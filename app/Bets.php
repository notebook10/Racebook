<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
        $save->status = 0;
        $save->bet = $dataArray['bet'];
        $save->save();
    }
}
