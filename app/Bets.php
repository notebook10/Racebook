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
        date_default_timezone_set('America/Los_Angeles');
        $pacificDate = date('Y-m-d H:i:s', time());
        foreach ($dataArray as $key => $value){
            $dataArray[$key]['created_at'] = $pacificDate;
            $dataArray[$key]['updated_at'] = $pacificDate;
        }
        return DB::table($this->table)
            ->insert($dataArray);
    }
    public function getAllBets($authId){
        return DB::table($this->table)
            ->where('player_id',$authId)
            ->orderBy('created_at','desc')
            ->get();
    }
    public function getPendingBets($authId){
        return DB::table($this->table)
            ->where('player_id',$authId)
            ->where('status',0)
            ->orderBy('created_at','desc')
            ->get();
    }
    public function getAll(){
        date_default_timezone_set('America/Los_Angeles');
        $currentDate = date("Y-m-d", time());
        return DB::table($this->table)
            ->whereBetween('created_at',[$currentDate . ' 00:00:00',$currentDate . ' 23:59:59'])
            ->get();
    }
    public function getBetsByDate($date){
        $currentDate = $date;
        return DB::table($this->table)
            ->whereBetween("created_at",[$currentDate . " 00:00:00", $currentDate . " 23:59:59"])
            ->get();
    }
}
