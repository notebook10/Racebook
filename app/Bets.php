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
    public function insertBets($dataArray,$date){
        if (!isset($_SESSION)) session_start();
        date_default_timezone_set('America/Los_Angeles');
        $pacificDate = date('Y-m-d H:i:s', time());
        $raceDate = date('mdy',time());
        foreach ($dataArray as $key => $value){
            $dataArray[$key]['created_at'] = $pacificDate;
            $dataArray[$key]['updated_at'] = $pacificDate;
            $dataArray[$key]['race_date'] = $date;
            $dataArray[$key]['dsn'] = $_SESSION["dsn"];
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
            ->whereBetween('created_at',[$currentDate . ' 00:00:00',$currentDate . ' 23:59:59'])
            ->where('status','!=',0)
            ->get();
    }
    public function getAllPendingBets(){
        date_default_timezone_set('America/Los_Angeles');
        $currentDate = date("Y-m-d", time());
        $currentRaceDate = date("mdy", time());
        return DB::table($this->table)
            ->orderBy('created_at','desc')
            ->where('status',0)
//            ->whereBetween('created_at',[$currentDate . ' 00:00:00',$currentDate . ' 23:59:59'])
            ->where('race_date',$currentRaceDate)
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
                if($strExplode[1] != "ALL"){
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
                }else{
                    // IF second horse is ALL
                    $beforeALL = strlen($strExplode[0]) + 1;
                    $strlen = strlen($strExplode[0]);
                    $withoutALL = substr($combination,$strlen -1,$beforeALL);
                    $beforeALLwAplha = strlen($strExplode[0] . $this->appendAlpha($strExplode[0])) + 1;
                    $strlenwAlpha = strlen($strExplode[0] . $this->appendAlpha($strExplode[0]));
                    $combinationwAll = substr_replace($combination, $this->appendAlpha($strExplode[0]), 1, 0);
                    $withoutALLwAplha = substr($combinationwAll,$strlenwAlpha -2,$beforeALLwAplha); // -2 for (, && Alpha)
                    return DB::table($this->table)
                        ->where("race_track", $trkCode)
                        ->where("race_number",$raceNum)
                        ->where("race_date",$raceDate)
                        ->where("bet","like", $withoutALL . "%")
                        ->where("bet_type",$wagerType)
                        ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode,$withoutALLwAplha){
                            $query->where("bet","like", $withoutALLwAplha . "%")
                                ->where("race_date",$raceDate)
                                ->where("bet_type",$wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number",$raceNum);
                        })
                        ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode,$withoutALLwAplha){
                            $query->where("bet","like", $strExplode[0] . "X,%")
                                ->where("race_date",$raceDate)
                                ->where("bet_type",$wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number",$raceNum);
                        })
                        ->get();
                }
                break;
            case "exactabox":
                if($strExplode[1] != "ALL"){
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
                }else{
                    // IF second horse is ALL
                    $beforeALL = strlen($strExplode[0]) + 1;
                    $strlen = strlen($strExplode[0]);
                    $withoutALL = substr($combination,$strlen -1,$beforeALL);
                    $beforeALLwAplha = strlen($strExplode[0] . $this->appendAlpha($strExplode[0])) + 1;
                    $strlenwAlpha = strlen($strExplode[0] . $this->appendAlpha($strExplode[0]));
                    $combinationwAll = substr_replace($combination, $this->appendAlpha($strExplode[0]), 1, 0);
                    $withoutALLwAplha = substr($combinationwAll,$strlenwAlpha -2,$beforeALLwAplha); // -2 for (, && Alpha)
                    return DB::table($this->table)
                        ->where("race_track", $trkCode)
                        ->where("race_number",$raceNum)
                        ->where("race_date",$raceDate)
                        ->where("bet","like", $withoutALL . "%")
                        ->where("bet_type",$wagerType)
                        ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode,$withoutALLwAplha){
                            $query->where("bet","like", $withoutALLwAplha . "%")
                                ->where("race_date",$raceDate)
                                ->where("bet_type",$wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number",$raceNum);
                        })
                        ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode,$withoutALLwAplha){ // $withoutALLwAplha useless
                            $query->where("bet","like", $strExplode[0] . "X,%")
                                ->where("race_date",$raceDate)
                                ->where("bet_type",$wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number",$raceNum);
                        })
                        ->get();
                }
                break;
            case "trifecta":
                if($strExplode[2] != "ALL") {
                    return DB::table($this->table)
                        ->where("race_track", $trkCode)
                        ->where("race_number", $raceNum)
                        ->where("race_date", $raceDate)
                        ->where("bet", $combination)
                        ->where("bet_type", $wagerType)
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode) { // A 2 3
                            $query->where("bet", $strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1] . "," . $strExplode[2])
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode) { // 1 B 3
                            $query->where("bet", $strExplode[0] . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]) . "," . $strExplode[2])
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode) { // 1 2 C
                            $query->where("bet", $strExplode[0] . "," . $strExplode[1] . "," . $strExplode[2] . $this->appendAlpha($strExplode[2]))
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode) { // A B C
                            $query->where("bet", $strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]) . "," . $strExplode[2] . $this->appendAlpha($strExplode[2]))
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode) { // 1 B C
                            $query->where("bet", $strExplode[0] . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]) . "," . $strExplode[2] . $this->appendAlpha($strExplode[2]))
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode) { // A 2 C
                            $query->where("bet", $strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1] . "," . $strExplode[2] . $this->appendAlpha($strExplode[2]))
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode) { // A B 3
                            $query->where("bet", $strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]) . "," . $strExplode[2])
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->get();
                }else{
                    // 1,2,ALL
                    $beforeALL = strlen($strExplode[0] . strlen($strExplode[1])) + 2;
                    $strlen = strlen($strExplode[0] . $strExplode[1]);
                    $withoutALL = substr($combination, $strlen  - $strlen,$beforeALL);
                    $beforeALLwAplha = strlen($strExplode[0] . $this->appendAlpha($strExplode[0]) . $strExplode[1] . $this->appendAlpha($strExplode[1])) + 2;
                    $strlenwAlpha = strlen($strExplode[0] . $this->appendAlpha($strExplode[0]) . $strExplode[1] . $this->appendAlpha($strExplode[1]));
                    $appendAlphaFirst = substr_replace($combination,$this->appendAlpha($strExplode[0]),strlen($strExplode[0]),0);
                    $appendAlphaSecond = substr_replace($combination,$this->appendAlpha($strExplode[1]),$strlenwAlpha -1,0);
                    $appendAlphaWhole = substr_replace($appendAlphaFirst,$this->appendAlpha($strExplode[1]),$strlenwAlpha,0);
                    $withoutALLfirst = substr($appendAlphaFirst,0,strlen($strExplode[0] . $this->appendAlpha($strExplode[0]) . $strExplode[1]) + 2);
                    $withoutALLsecond = substr($appendAlphaSecond,0,strlen($strExplode[0] . $strExplode[1]  . $this->appendAlpha($strExplode[1])) + 2);
                    $withoutALLwAplhaTri = substr($appendAlphaWhole,0,strlen($appendAlphaWhole) - 3); // less 3 to remove 'ALL'
                    return  DB::table($this->table)
                        ->where("race_track", $trkCode)
                        ->where("race_number", $raceNum)
                        ->where("race_date", $raceDate)
                        ->where("bet", "like", $withoutALL . "%")
                        ->where("bet_type", $wagerType)
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode,$withoutALLfirst) { // A 2 3
                            $query->where("bet","like", $withoutALLfirst . '%' )
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode,$withoutALLsecond) { // A 2 3
                            $query->where("bet","like", $withoutALLsecond . '%' )
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode,$withoutALLwAplhaTri) { // A 2 3
                            $query->where("bet","like", $withoutALLwAplhaTri . '%' )
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode,$withoutALLfirst) { // X 2 ALL
                            $query->where("bet","like", $strExplode[0] . "X," . $strExplode[1] . ',%')
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode,$withoutALLfirst) { // 1 X ALL
                            $query->where("bet","like", $strExplode[0] . "," . $strExplode[1] . 'X,%')
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode,$withoutALLfirst) { // X X ALL
                            $query->where("bet","like", $strExplode[0] . "X," . $strExplode[1] . 'X,%')
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->get();
                }
                break;
            case "trifectabox":
                if($strExplode[2] != "ALL") {
                    return DB::table($this->table)
                        ->where("race_track", $trkCode)
                        ->where("race_number", $raceNum)
                        ->where("race_date", $raceDate)
                        ->where("bet", $combination)
                        ->where("bet_type", $wagerType)
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode) { // A 2 3
                            $query->where("bet", $strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1] . "," . $strExplode[2])
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode) { // 1 B 3
                            $query->where("bet", $strExplode[0] . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]) . "," . $strExplode[2])
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode) { // 1 2 C
                            $query->where("bet", $strExplode[0] . "," . $strExplode[1] . "," . $strExplode[2] . $this->appendAlpha($strExplode[2]))
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode) { // A B C
                            $query->where("bet", $strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]) . "," . $strExplode[2] . $this->appendAlpha($strExplode[2]))
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode) { // 1 B C
                            $query->where("bet", $strExplode[0] . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]) . "," . $strExplode[2] . $this->appendAlpha($strExplode[2]))
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode) { // A 2 C
                            $query->where("bet", $strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1] . "," . $strExplode[2] . $this->appendAlpha($strExplode[2]))
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode) { // A B 3
                            $query->where("bet", $strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1] . $this->appendAlpha($strExplode[1]) . "," . $strExplode[2])
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->get();
                }else{
                    // 1,2,ALL
                    $beforeALL = strlen($strExplode[0] . strlen($strExplode[1])) + 2;
                    $strlen = strlen($strExplode[0] . $strExplode[1]);
                    $withoutALL = substr($combination, $strlen  - $strlen,$beforeALL);
                    $beforeALLwAplha = strlen($strExplode[0] . $this->appendAlpha($strExplode[0]) . $strExplode[1] . $this->appendAlpha($strExplode[1])) + 2;
                    $strlenwAlpha = strlen($strExplode[0] . $this->appendAlpha($strExplode[0]) . $strExplode[1] . $this->appendAlpha($strExplode[1]));
                    $appendAlphaFirst = substr_replace($combination,$this->appendAlpha($strExplode[0]),strlen($strExplode[0]),0);
                    $appendAlphaSecond = substr_replace($combination,$this->appendAlpha($strExplode[1]),$strlenwAlpha -1,0);
                    $appendAlphaWhole = substr_replace($appendAlphaFirst,$this->appendAlpha($strExplode[1]),$strlenwAlpha,0);
                    $withoutALLfirst = substr($appendAlphaFirst,0,strlen($strExplode[0] . $this->appendAlpha($strExplode[0]) . $strExplode[1]) + 2);
                    $withoutALLsecond = substr($appendAlphaSecond,0,strlen($strExplode[0] . $strExplode[1]  . $this->appendAlpha($strExplode[1])) + 2);
                    $withoutALLwAplhaTri = substr($appendAlphaWhole,0,strlen($appendAlphaWhole) - 3); // less 3 to remove 'ALL'
                    return DB::table($this->table)
                        ->where("race_track", $trkCode)
                        ->where("race_number", $raceNum)
                        ->where("race_date", $raceDate)
                        ->where("bet", "like", $withoutALL . "%")
                        ->where("bet_type", $wagerType)
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode,$withoutALLfirst) { // A 2 3
                            $query->where("bet","like", $withoutALLfirst . '%' )
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode,$withoutALLsecond) { // A 2 3
                            $query->where("bet","like", $withoutALLsecond . '%' )
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode,$withoutALLwAplhaTri) { // A 2 3
                            $query->where("bet","like", $withoutALLwAplhaTri . '%' )
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode,$withoutALLfirst) { // X 2 ALL
                            $query->where("bet","like", $strExplode[0] . "X," . $strExplode[1] . ',%')
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode,$withoutALLfirst) { // 1 X ALL
                            $query->where("bet","like", $strExplode[0] . "," . $strExplode[1] . 'X,%')
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->orWhere(function ($query) use ($raceDate, $trkCode, $raceNum, $wagerType, $strExplode,$withoutALLfirst) { // X X ALL
                            $query->where("bet","like", $strExplode[0] . "X," . $strExplode[1] . 'X,%')
                                ->where("race_date", $raceDate)
                                ->where("bet_type", $wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number", $raceNum);
                        })
                        ->get();
                }
                break;
            case "dailydouble":
                if($strExplode[1] != "ALL"){
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
                        ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){
                            $query->where("bet",$strExplode[0] . "X," . $strExplode[1])
                                ->where("race_date",$raceDate)
                                ->where("bet_type",$wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number",$raceNum);
                        })
                        ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){
                            $query->where("bet",$strExplode[0] . "," . $strExplode[1] . "X")
                                ->where("race_date",$raceDate)
                                ->where("bet_type",$wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number",$raceNum);
                        })
                        ->get();
                }else{
                    // IF second horse is ALL
                    return DB::table($this->table)
                        ->where("race_track", $trkCode)
                        ->where("race_number", $raceNum)
                        ->where("race_date", $raceDate)
                        ->where("bet", "like", $strExplode[0] . "," . "%")
                        ->where("bet_type", $wagerType)
                        ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){
                            $query->where("bet","like",$strExplode[0] . $this->appendAlpha($strExplode[0]) . ',%')
                                ->where("race_date",$raceDate)
                                ->where("bet_type",$wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number",$raceNum);
                        })
                        ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){
                            $query->where("bet","like",$strExplode[0] . 'X,%')
                                ->where("race_date",$raceDate)
                                ->where("bet_type",$wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number",$raceNum);
                        })
                        ->get();
                }
                break;
            case "superfecta":
                if(in_array("ALL",$strExplode) == false){
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
                }else{

                    $strlenSuper = strlen($strExplode[0] . $strExplode[1]) + 2;
                    $withoutAllsuper = substr($combination,0,$strlenSuper);
                    $replaceFirstSuper = $strExplode[0] . $this->appendAlpha($strExplode[0]) . ',' . $strExplode[1] . ',';
                    $replaceSecondSuper = $strExplode[0] . ',' . $strExplode[1] . $this->appendAlpha($strExplode[1]) . ',';
                    $replaceWholeSuper = $strExplode[0] . $this->appendAlpha($strExplode[0]) . ',' . $strExplode[1] . $this->appendAlpha($strExplode[1]) . ',';

                    $withoutAllsuperForLast = $strExplode[0] . ',' . $strExplode[1] . ',' . $strExplode[2] . ',';
                    $replaceFirstSuperForLast = $strExplode[0] . $this->appendAlpha($strExplode[0]) . ',' . $strExplode[1] . ',' . $strExplode[2];
                    $replaceSecondSuperForLast = $strExplode[0] . ',' . $strExplode[1] . $this->appendAlpha($strExplode[1]) . ',' . $strExplode[2];
                    $replaceThirdSuperForLast = $strExplode[0] . ',' . $strExplode[1] . ',' . $strExplode[2] . $this->appendAlpha($strExplode[2]);
                    // A 2 C
                    $atwoc = $strExplode[0] . $this->appendAlpha($strExplode[0]) . ',' . $strExplode[1] . ',' . $strExplode[2] . $this->appendAlpha($strExplode[2]);
                    // A B 3
                    $abthree = $strExplode[0] . $this->appendAlpha($strExplode[0]) . ',' . $strExplode[1] . $this->appendAlpha($strExplode[1]) . ',' . $strExplode[2];
                    // 1 B C
                    $onebc = $strExplode[0] . ',' . $strExplode[1] . $this->appendAlpha($strExplode[1]) . ',' . $strExplode[2] . $this->appendAlpha($strExplode[2]);
                    $replaceWholeForLastOnly = $strExplode[0] . $this->appendAlpha($strExplode[0]) . ',' . $strExplode[1] . $this->appendAlpha($strExplode[1]) . ',' . $strExplode[2] . $this->appendAlpha($strExplode[2]);
                    if($strExplode[2] == "ALL"){
                        return DB::table($this->table)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum)
                            ->where("race_date",$raceDate)
                            ->where("bet","like",$withoutAllsuper . "%")
                            ->where("bet_type",$wagerType)
                            ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode,$replaceFirstSuper){
                                $query->where("bet","like",$replaceFirstSuper . "%")
                                    ->where("race_date",$raceDate)
                                    ->where("bet_type",$wagerType)
                                    ->where("race_track", $trkCode)
                                    ->where("race_number",$raceNum);
                            })
                            ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode,$replaceSecondSuper){
                                $query->where("bet","like",$replaceSecondSuper . "%")
                                    ->where("race_date",$raceDate)
                                    ->where("bet_type",$wagerType)
                                    ->where("race_track", $trkCode)
                                    ->where("race_number",$raceNum);
                            })
                            ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode,$replaceWholeSuper){
                                $query->where("bet","like",$replaceWholeSuper . "%")
                                    ->where("race_date",$raceDate)
                                    ->where("bet_type",$wagerType)
                                    ->where("race_track", $trkCode)
                                    ->where("race_number",$raceNum);
                            })
                            ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode,$replaceWholeSuper){ // X 2 ALL ALL
                                $query->where("bet","like", $strExplode[0] . "X," . $strExplode[1] . ",%")
                                    ->where("race_date",$raceDate)
                                    ->where("bet_type",$wagerType)
                                    ->where("race_track", $trkCode)
                                    ->where("race_number",$raceNum);
                            })
                            ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode,$replaceWholeSuper){ // 1 X ALL ALL
                                $query->where("bet","like", $strExplode[0] . "," . $strExplode[1] . "X,%")
                                    ->where("race_date",$raceDate)
                                    ->where("bet_type",$wagerType)
                                    ->where("race_track", $trkCode)
                                    ->where("race_number",$raceNum);
                            })
                            ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode,$replaceWholeSuper){ // X X ALL ALL
                                $query->where("bet","like", $strExplode[0] . "X," . $strExplode[1] . "X,%")
                                    ->where("race_date",$raceDate)
                                    ->where("bet_type",$wagerType)
                                    ->where("race_track", $trkCode)
                                    ->where("race_number",$raceNum);
                            })
                            ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode,$replaceWholeSuper){ // A X ALL ALL
                                $query->where("bet","like", $strExplode[0] . $this->appendAlpha($strExplode[0]) . "," . $strExplode[1] . "X,%")
                                    ->where("race_date",$raceDate)
                                    ->where("bet_type",$wagerType)
                                    ->where("race_track", $trkCode)
                                    ->where("race_number",$raceNum);
                            })
                            ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode,$replaceWholeSuper){ // X B ALL ALL
                                $query->where("bet","like", $strExplode[0] . "X," . $strExplode[1] . $this->appendAlpha($strExplode[1]) . ",%")
                                    ->where("race_date",$raceDate)
                                    ->where("bet_type",$wagerType)
                                    ->where("race_track", $trkCode)
                                    ->where("race_number",$raceNum);
                            })
                            ->get();
                    }else{
                        // LASTTTTTTTTT
                        return DB::table($this->table)
                            ->where("race_track", $trkCode)
                            ->where("race_number",$raceNum)
                            ->where("race_date",$raceDate)
                            ->where("bet","like",$withoutAllsuperForLast . "%")
                            ->where("bet_type",$wagerType)
                            ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode,$replaceFirstSuperForLast){
                                $query->where("bet","like",$replaceFirstSuperForLast . "%")
                                    ->where("race_date",$raceDate)
                                    ->where("bet_type",$wagerType)
                                    ->where("race_track", $trkCode)
                                    ->where("race_number",$raceNum);
                            })
                            ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode,$replaceSecondSuperForLast){
                                $query->where("bet","like",$replaceSecondSuperForLast . "%")
                                    ->where("race_date",$raceDate)
                                    ->where("bet_type",$wagerType)
                                    ->where("race_track", $trkCode)
                                    ->where("race_number",$raceNum);
                            })
                            ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode,$replaceThirdSuperForLast){
                                $query->where("bet","like",$replaceThirdSuperForLast . "%")
                                    ->where("race_date",$raceDate)
                                    ->where("bet_type",$wagerType)
                                    ->where("race_track", $trkCode)
                                    ->where("race_number",$raceNum);
                            })
                            ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode,$atwoc){
                                $query->where("bet","like",$atwoc . "%")
                                    ->where("race_date",$raceDate)
                                    ->where("bet_type",$wagerType)
                                    ->where("race_track", $trkCode)
                                    ->where("race_number",$raceNum);
                            })
                            ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode,$abthree){
                                $query->where("bet","like",$abthree . "%")
                                    ->where("race_date",$raceDate)
                                    ->where("bet_type",$wagerType)
                                    ->where("race_track", $trkCode)
                                    ->where("race_number",$raceNum);
                            })
                            ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode,$onebc){
                                $query->where("bet","like",$onebc . "%")
                                    ->where("race_date",$raceDate)
                                    ->where("bet_type",$wagerType)
                                    ->where("race_track", $trkCode)
                                    ->where("race_number",$raceNum);
                            })
                            ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode,$replaceWholeForLastOnly){
                                $query->where("bet","like",$replaceWholeForLastOnly . "%")
                                    ->where("race_date",$raceDate)
                                    ->where("bet_type",$wagerType)
                                    ->where("race_track", $trkCode)
                                    ->where("race_number",$raceNum);
                            })
                            ->get();
                    }
                }
                break;
            case "quinella":
                if($strExplode[1] != "ALL"){
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
                        ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // << START HERE REVERSED
                            $query->where("bet",$strExplode[1] . "," . $strExplode[0])
                                ->where("race_date",$raceDate)
                                ->where("bet_type",$wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number",$raceNum);
                        })
                        ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){ // <<<<<<<<<<<<
                            $query->where("bet",$strExplode[1] . $this->appendAlpha($strExplode[1]) . "," . $strExplode[0])
                                ->where("race_date",$raceDate)
                                ->where("bet_type",$wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number",$raceNum);
                        })
                        ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){
                            $query->where("bet",$strExplode[1] . "," . $strExplode[0] . $this->appendAlpha($strExplode[0]))
                                ->where("race_date",$raceDate)
                                ->where("bet_type",$wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number",$raceNum);
                        })
                        ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){
                            $query->where("bet",$strExplode[1] . $this->appendAlpha($strExplode[1]) . "," . $strExplode[0] . $this->appendAlpha($strExplode[0]))
                                ->where("race_date",$raceDate)
                                ->where("bet_type",$wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number",$raceNum);
                        })
                        ->get();
                }else{
                    // Quinella ALL
                    return DB::table($this->table)
                        ->where("race_track", $trkCode)
                        ->where("race_number",$raceNum)
                        ->where("race_date",$raceDate)
                        ->where("bet","like",$strExplode[0] . ",%") // 1 ALL
                        ->where("bet_type",$wagerType)
                        ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){
                            $query->where("bet","like",$strExplode[0] . $this->appendAlpha($strExplode[0]) . ",%") // ALL A
                            ->where("race_date",$raceDate)
                                ->where("bet_type",$wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number",$raceNum);
                        })
                        ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){
                            $query->where("bet","like",$strExplode[0] . "X,%") // X ALL
                            ->where("race_date",$raceDate)
                                ->where("bet_type",$wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number",$raceNum);
                        })
                        ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){
                            $query->where("bet","like","%," . $strExplode[0]) // ALL 1
                                ->where("race_date",$raceDate)
                                ->where("bet_type",$wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number",$raceNum);
                        })
                        ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){
                            $query->where("bet","like","%," . $strExplode[0] . $this->appendAlpha($strExplode[0])) // ALL A
                                ->where("race_date",$raceDate)
                                ->where("bet_type",$wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number",$raceNum);
                        })
                        ->orWhere(function($query) use ($raceDate,$trkCode,$raceNum,$wagerType,$strExplode){
                            $query->where("bet","like","%,",$strExplode[0] . "X") // ALL X
                            ->where("race_date",$raceDate)
                                ->where("bet_type",$wagerType)
                                ->where("race_track", $trkCode)
                                ->where("race_number",$raceNum);
                        })
                        ->get();
                }
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
        $betArray["win_amount"] = 0;
        $betArray["result"] = 0;
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
                var_dump("Switch Default");
                break;
        }
    }
    public function getBetInfo($id){
        return DB::table($this->table)
            ->where("id",$id)
            ->first();
    }
    public function updateBet($dataArray,$id){
        unset($dataArray["created_at"]);
        unset($dataArray["updated_at"]);
        if($dataArray["result"] == 0){
            $dataArray["status"] = 0; // pending still
        }else{
            $dataArray["status"] = 1; // graded
            $dataArray["grading_status"] = 1;
        }
        if($dataArray["result"] == 2 || $dataArray["result"] == 3){
            $dataArray["win_amount"] = 0; // win_amount to 0 if lose or aborted
        }
        return DB::table($this->table)
            ->where("id",$id)
            ->update($dataArray);
    }
    public function getBetsThisWeek(){
        date_default_timezone_set('America/Los_Angeles');
    }
    public function getPastBetsBySelectedDate($date){
        date_default_timezone_set('America/Los_Angeles');
        $currentRaceDate = date('mdy',strtotime($date));
        return DB::table($this->table)
            ->orderBy('created_at','desc')
//            ->whereBetween('created_at',[$date . ' 00:00:00',$date . ' 23:59:59'])
            ->where('race_date',$currentRaceDate)
            ->where('status','!=',0)
            ->get();
    }
    public function getPendingBetsBySelectedDate($date){
        date_default_timezone_set('America/Los_Angeles');
        $currentRaceDate = date('mdy',strtotime($date));
        return DB::table($this->table)
            ->orderBy('created_at','desc')
//            ->whereBetween('created_at',[$date . ' 00:00:00',$date . ' 23:59:59'])
            ->where('race_date',$currentRaceDate)
            ->where('status',0)
            ->get();
    }
    public function getPendingBetsHome($date,$id){
        date_default_timezone_set('America/Los_Angeles');
        $currentRaceDate = date('mdy',strtotime($date));
        return DB::table($this->table)
            ->orderBy('created_at','desc')
//            ->whereBetween('created_at',[$date . ' 00:00:00',$date . ' 23:59:59'])
            ->where('race_date',$currentRaceDate)
            ->where('status',0)
            ->where('player_id',$id)
            ->get();
    }
    public function getPastBetsHome($date,$id){
        date_default_timezone_set('America/Los_Angeles');
        $currentRaceDate = date('mdy',strtotime($date));
        return DB::table($this->table)
            ->orderBy('created_at','desc')
//            ->whereBetween('created_at',[$date . ' 00:00:00',$date . ' 23:59:59'])
            ->where('race_date',$currentRaceDate)
            ->where('status','!=',0)
            ->where('player_id',$id)
            ->get();
    }
    public function gradePendingDD($dataArray){
        DB::table($this->table)
            ->where("race_track",$dataArray["track_code"])
            ->where("race_date",$dataArray["race_date"])
            ->where("race_number",$dataArray["race_number"])
            ->where("bet_type","dailydouble")
            ->update([
                "status" => 0,
                "result" => 0,
                "win_amount" => 0
            ]);
    }
    public static function getBetInfoById($id){
        return DB::table("bets")
            ->where("id",$id)
            ->first();
    }
}
