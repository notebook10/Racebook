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
        $raceDate = date('mdy',time());
        foreach ($dataArray as $key => $value){
            $dataArray[$key]['created_at'] = $pacificDate;
            $dataArray[$key]['updated_at'] = $pacificDate;
            $dataArray[$key]['race_date'] = $raceDate;
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
    public function getAllBets2($authId){
        return DB::table($this->table)
            ->where('player_id',$authId)
            ->where('status','!=',0)
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
            ->orderBy('created_at','desc')
//            ->whereBetween('created_at',[$currentDate . ' 00:00:00',$currentDate . ' 23:59:59'])
            ->get();
    }
    public function getAllPastBets(){
        date_default_timezone_set('America/Los_Angeles');
        $currentDate = date("Y-m-d", time());
        return DB::table($this->table)
            ->orderBy('created_at','desc')
//            ->whereBetween('created_at',[$currentDate . ' 00:00:00',$currentDate . ' 23:59:59'])
            ->where('status','!=',0)
            ->get();
    }
    public function getAllPendingBets(){
        date_default_timezone_set('America/Los_Angeles');
        return DB::table($this->table)
            ->orderBy('created_at','desc')
            ->where('status',0)
            ->get();
    }
    public function getBetsByDate($date){
        $currentDate = $date;
        return DB::table($this->table)
            ->whereBetween("created_at",[$currentDate . " 00:00:00", $currentDate . " 23:59:59"])
            ->get();
    }
//    public function checkExacta($trkCode, $raceDate, $raceNum, $combination, $wagerType){
//        return DB::table($this->table)
//            ->where("race_track", $trkCode)
//            ->where("race_number",$raceNum)
//            ->where("race_date",$raceDate)
//            ->where("bet",$combination)
//            ->where("bet_type",$wagerType)
//            ->get();
//    }
//    public function checkExactaBox($trkCode, $raceDate, $raceNum, $combination){
//        return DB::table($this->table)
//            ->where("race_track", $trkCode)
//            ->where("race_number",$raceNum)
//            ->where("race_date",$raceDate)
//            ->where("bet",$combination)
//            ->where("bet_type","exactabox")
//            ->get();
//    }
//    public function checkTrifecta($trkCode, $raceDate, $raceNum, $combination){
//        return DB::table($this->table)
//            ->where("race_track", $trkCode)
//            ->where("race_number",$raceNum)
//            ->where("race_date",$raceDate)
//            ->where("bet",$combination)
//            ->where("bet_type","trifecta")
//            ->get();
//    }
    public function checkWinners($trkCode, $raceDate, $raceNum, $combination, $wagerType){
        $strExplode = explode(",",$combination);
        switch ($wagerType){
            case "exacta":
                return DB::table($this->table)
                    ->where("race_track", $trkCode)
                    ->where("race_number",$raceNum)
                    ->where("race_date",$raceDate)
                    ->where("bet",$combination)
                    ->where("bet_type",$wagerType)
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){
                        $query->where("bet",$strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1])
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){
                        $query->where("bet",$strExplode[0] . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]))
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){
                        $query->where("bet",$strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]))
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->get();
                break;
            case "exactabox":
                return DB::table($this->table)
                    ->where("race_track", $trkCode)
                    ->where("race_number",$raceNum)
                    ->where("race_date",$raceDate)
                    ->where("bet",$combination)
                    ->where("bet_type",$wagerType)
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){
                        $query->where("bet",$strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1])
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){
                        $query->where("bet",$strExplode[0] . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]))
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){
                        $query->where("bet",$strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]))
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->get();
                break;
            case "trifecta":
                return DB::table($this->table)
                    ->where("race_track", $trkCode)
                    ->where("race_number",$raceNum)
                    ->where("race_date",$raceDate)
                    ->where("bet",$combination)
                    ->where("bet_type",$wagerType)
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // A 2 3
                        $query->where("bet",$strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1] . "," . $strExplode[2])
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // 1 B 3
                        $query->where("bet",$strExplode[0] . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]) . "," . $strExplode[2])
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // 1 2 C
                        $query->where("bet",$strExplode[0] . "," . $strExplode[1] . "," . $strExplode[2] . $this->appendAlpha($strExplode[2]))
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // A B C
                        $query->where("bet",$strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]). "," . $strExplode[2] . $this->appendAlpha($strExplode[2]))
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // 1 B C
                        $query->where("bet",$strExplode[0] . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]) . "," . $strExplode[2] . $this->appendAlpha($strExplode[2]))
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // A 2 C
                        $query->where("bet",$strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1] . "," . $strExplode[2] . $this->appendAlpha($strExplode[2]))
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // A B 3
                        $query->where("bet",$strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]) . "," . $strExplode[2])
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->get();
                break;
            case "trifectabox":
                return DB::table($this->table)
                    ->where("race_track", $trkCode)
                    ->where("race_number",$raceNum)
                    ->where("race_date",$raceDate)
                    ->where("bet",$combination)
                    ->where("bet_type",$wagerType)
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // A 2 3
                        $query->where("bet",$strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1] . "," . $strExplode[2])
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // 1 B 3
                        $query->where("bet",$strExplode[0] . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]) . "," . $strExplode[2])
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // 1 2 C
                        $query->where("bet",$strExplode[0] . "," . $strExplode[1] . "," . $strExplode[2] . $this->appendAlpha($strExplode[2]))
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // A B C
                        $query->where("bet",$strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]). "," . $strExplode[2] . $this->appendAlpha($strExplode[2]))
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // 1 B C
                        $query->where("bet",$strExplode[0] . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]) . "," . $strExplode[2] . $this->appendAlpha($strExplode[2]))
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // A 2 C
                        $query->where("bet",$strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1] . "," . $strExplode[2] . $this->appendAlpha($strExplode[2]))
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // A B 3
                        $query->where("bet",$strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]) . "," . $strExplode[2])
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->get();
                break;
            case "dailydouble":
                return DB::table($this->table)
                    ->where("race_track", $trkCode)
                    ->where("race_number",$raceNum)
                    ->where("race_date",$raceDate)
                    ->where("bet",$combination)
                    ->where("bet_type",$wagerType)
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){
                        $query->where("bet",$strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1])
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){
                        $query->where("bet",$strExplode[0] . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]))
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){
                        $query->where("bet",$strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]))
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->get();
                break;
            case "superfecta":
                return DB::table($this->table)
                    ->where("race_track", $trkCode)
                    ->where("race_number",$raceNum)
                    ->where("race_date",$raceDate)
                    ->where("bet",$combination)
                    ->where("bet_type",$wagerType)
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // A 2 3 4
                        $query->where("bet",$strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1] . "," . $strExplode[2] . "," . $strExplode[3])
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // 1 B 3 4
                        $query->where("bet",$strExplode[0] . "," . $strExplode[1]. $this->appendAlpha($strExplode[1]) . "," . $strExplode[2] . "," . $strExplode[3])
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // 1 2 C 4
                        $query->where("bet",$strExplode[0] . "," . $strExplode[1] . "," . $strExplode[2] . $this->appendAlpha($strExplode[2]) . "," . $strExplode[3])
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // 1 2 3 D
                        $query->where("bet",$strExplode[0] . "," . $strExplode[1] . "," . $strExplode[2] . "," . $strExplode[3] . $this->appendAlpha($strExplode[3]))
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // A B 3 4
                        $query->where("bet",$strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]) . "," . $strExplode[2] . "," . $strExplode[3])
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // 1 B C 4
                        $query->where("bet",$strExplode[0] . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]) . "," . $strExplode[2]  . $this->appendAlpha($strExplode[2]) . "," . $strExplode[3])
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // 1 2 C D
                        $query->where("bet",$strExplode[0] . "," . $strExplode[1] . "," . $strExplode[2]  . $this->appendAlpha($strExplode[2]) . "," . $strExplode[3]  . $this->appendAlpha($strExplode[3]))
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // A 2 3 D
                        $query->where("bet",$strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1] . "," . $strExplode[2] . "," . $strExplode[3]  . $this->appendAlpha($strExplode[3]))
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // 1 B C 4
                        $query->where("bet",$strExplode[0] . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]) . "," . $strExplode[2] . $this->appendAlpha($strExplode[2]) . "," . $strExplode[3])
                            ->where("race_date",$raceDate)
                            ->where("bet_type",$wagerType)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum);
                    })
                    ->get();
                break;
            default:

                break;
        }
//        return DB::table($this->table)
//            ->where("race_track", $trkCode)
//            ->where("race_number",$raceNum)
//            ->where("race_date",$raceDate)
//            ->where("bet",$combination)
//            ->where("bet_type",$wagerType)
//            ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType){
//                $query->where("bet","1A,2")
//                ->where("race_date",$raceDate)
//                ->where("bet_type",$wagerType)
//                ->where("race_track", $trkCode)
//                ->where("race_number",$raceNum);
//            })
//            ->get();
    }
    public function checkWps($trkCode, $raceDate, $raceNum, $combination, $wagerType, $type){
        $strExplode = explode(",",$combination);
        return DB::table($this->table)
            ->where("race_track", $trkCode)
            ->where("race_number",$raceNum)
            ->where("race_date",$raceDate)
            ->where("bet_type",$wagerType)
            ->where("type",$type)
            ->where("bet",$combination)
            ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // A
                $query->where("bet",$strExplode[0] . $this->appendAlpha($strExplode[0]))
                    ->where("race_date",$raceDate)
                    ->where("bet_type",$wagerType)
                    ->where("race_track", $trkCode)
                    ->where("race_number",$raceNum);
            })
            ->get();
    }
    public function checkWinnersForDD($trkCode, $raceDate, $raceNum, $combination, $wagerType){
        $strExplode = explode(",",$combination);
        $newRaceNum = $raceNum - 1;
        return DB::table($this->table)
            ->where("race_track", $trkCode)
            ->where("race_number",$newRaceNum)
            ->where("race_date",$raceDate)
            ->where("bet",$combination)
            ->where("bet_type",$wagerType)
            ->orWhere(function($query) use ($raceDate,$trkCode,$newRaceNum,$wagerType,$strExplode){
                $query->where("bet",$strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1])
                    ->where("race_date",$raceDate)
                    ->where("bet_type",$wagerType)
                    ->where("race_track", $trkCode)
                    ->where("race_number",$newRaceNum);
            })
            ->orWhere(function($query) use ($raceDate,$trkCode,$newRaceNum,$wagerType,$strExplode){
                $query->where("bet",$strExplode[0] . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]))
                    ->where("race_date",$raceDate)
                    ->where("bet_type",$wagerType)
                    ->where("race_track", $trkCode)
                    ->where("race_number",$newRaceNum);
            })
            ->orWhere(function($query) use ($raceDate,$trkCode,$newRaceNum,$wagerType,$strExplode){
                $query->where("bet",$strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]))
                    ->where("race_date",$raceDate)
                    ->where("bet_type",$wagerType)
                    ->where("race_track", $trkCode)
                    ->where("race_number",$newRaceNum);
            })
            ->get();
    }
    public function getBetsForScratch($dataArray){
        return DB::table($this->table)
            ->where("race_track",$dataArray["trk"])
            ->where("race_number", $dataArray["num"])
            ->where("race_date", $dataArray["date"])
            ->get();
    }
    public function scratchBet($id){
        return DB::table($this->table)
            ->where("id", $id)
            ->update([
                "status" => 1,
                "result" => 3, // SCRATCHED !!!
            ]);
    }
    public function saveNewBet($betArray){
        return DB::table($this->table)
            ->insert($betArray);
    }
    public function undoScratch($id){
        return DB::table($this->table)
            ->where("id",$id)
            ->update([
                "status" => 0,
                "result" => 0
            ]);
    }
    public function cancelWager($dataArray){
        return DB::table($this->table)
            ->where("race_date", $dataArray["date"])
            ->where("bet_type", $dataArray["wagerType"])
            ->where("race_track", $dataArray["trk"])
            ->where("race_number", $dataArray["num"])
            ->update([
                "status" => 1,
                "result" => 3
            ]);
    }
    public function noShow($dataArray){
        return DB::table($this->table)
            ->where("race_date", $dataArray["date"])
            ->where("race_track", $dataArray["trk"])
            ->where("race_number", $dataArray["num"])
            ->where("bet_type","wps")
            ->where("type","s")
            ->update([
                "status" => 1,
                "result" => 3
            ]);
    }
    public function cancelWagerShow($dataArray){
        return DB::table($this->table)
            ->where("race_date", $dataArray["date"])
            ->where("type", $dataArray["wagerType"])
            ->where("race_track", $dataArray["trk"])
            ->where("race_number", $dataArray["num"])
            ->update([
                "status" => 1,
                "result" => 3
            ]);
    }
    public function appendAlpha($horsePNum){
        switch ($horsePNum){
            case 1:
                return "A";
                break;
            case 2:
                return "B";
                break;
            case 3:
                return "C";
                break;
            case 4:
                return "D";
                break;
            case 5:
                return "E";
                break;
            case 6:
                return "F";
                break;
            case 7:
                return "G";
                break;
            case 8:
                return "H";
                break;
            case 9:
                return "I";
                break;
            case 10:
                return "J";
                break;
            case 11:
                return "K";
                break;
            case 12:
                return "L";
                break;
            case 13:
                return "M";
                break;
            case 14:
                return "N";
                break;
            case 15:
                return "O";
                break;
            case 16:
                return "P";
                break;
            case 17:
                return "Q";
                break;
            case 18:
                return "R";
                break;
            case 19:
                return "S";
                break;
            case 20:
                return "T";
                break;
            default:
                dd("Switch Default");
                break;
        }
    }
    public function getBetInfo($id){
        return DB::table($this->table)
            ->where("id",$id)
            ->first();
    }
    public function updateBet($dataArray,$id){
        return DB::table($this->table)
            ->where("id",$id)
            ->update($dataArray);
    }
    public function getBetsThisWeek(){
        date_default_timezone_set('America/Los_Angeles');
    }
}
