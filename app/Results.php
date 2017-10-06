<?php

namespace App;

use App\Http\Controllers\AdminController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DB;
class Results extends Model
{
    protected $table = "results";
    public function insertResult($dataArray, $operation){
        $logsModel = new Logs();
        if($operation == 0){
            // Add
            // Delete b4 add
            DB::table($this->table)
                ->where("track_code",$dataArray["track_code"])
                ->where("race_number",$dataArray["race_number"])
                ->where("race_date",$dataArray["race_date"])
                ->delete();
            // Logs
            $logsModel->saveLog(['user_id' => Auth::id(),'action'=>'Save result' . ' ' . $dataArray["track_code"] . ' ' .$dataArray["race_number"] . ' ' . $dataArray["race_date"]]);
            $result = new Results();
            $result->track_code = $dataArray["track_code"];
            $result->race_number = $dataArray["race_number"];
            $result->race_date = $dataArray["race_date"];
            $result->race_winners = $dataArray["first"] . "," . $dataArray["second"] . "," . $dataArray["third"] . "," . $dataArray["fourth"];
            $result->graded_by = Auth::id();
            $result->save();
            return 1;
        }else if($operation == 1){
            // Update
            $refactoredArray = [
                'race_winners' => $dataArray["first"] . "," . $dataArray["second"] . "," . $dataArray["third"] . "," . $dataArray["fourth"]
            ];
            $firstResultID = DB::table($this->table)
                ->where("track_code", $dataArray["track_code"])
                ->where("race_number",$dataArray["race_number"])
                ->where("race_date", $dataArray["race_date"])
                ->first();
            $firstResultGradedById = $firstResultID->graded_by;
            DB::table($this->table)
                ->where("track_code", $dataArray["track_code"])
                ->where("race_number",$dataArray["race_number"])
                ->where("race_date", $dataArray["race_date"])
                ->delete();
            $logsModel->saveLog(['user_id' => Auth::id(),'action'=>'Update result' . ' ' . $dataArray["track_code"] . ' ' .$dataArray["race_number"] . ' ' . $dataArray["race_date"]]);
            $result = new Results();
            $result->track_code = $dataArray["track_code"];
            $result->race_number = $dataArray["race_number"];
            $result->race_date = $dataArray["race_date"];
            $result->race_winners = $dataArray["first"] . "," . $dataArray["second"] . "," . $dataArray["third"] . "," . $dataArray["fourth"];
            $result->graded_by = Auth::id();
//            $result->graded_by = $firstResultGradedById;
            $result->save();
            return 1;
        }else if($operation == 2){
            // For matching entering results
            $firstResultEntry = DB::table($this->table)
                ->where("track_code", $dataArray["track_code"])
                ->where("race_number",$dataArray["race_number"])
                ->where("race_date", $dataArray["race_date"])
                ->first();
            $firstPayoutEntry = Payout::getPayoutForVerification($dataArray["track_code"],$dataArray["race_number"],$dataArray["race_date"]);
            $firstPayoutEntryArray = json_decode($firstPayoutEntry->content);
            $payoutComparison = AdminController::comparePayout($firstPayoutEntry,$dataArray["payoutArray"]); // Comparison of payout; If there is a 1 then mismatched
            if(in_array(1,$payoutComparison)){
                // TRUE
                DB::table($this->table)
                    ->where("track_code", $dataArray["track_code"])
                    ->where("race_number",$dataArray["race_number"])
                    ->where("race_date", $dataArray["race_date"])
                    ->update(["status" => 0]);
                return 1; // Payouts MISMATCHED
            }else{
                // check if results matched...
                if($dataArray["first"] . "," . $dataArray["second"] . "," . $dataArray["third"] . "," . $dataArray["fourth"] == $firstResultEntry->race_winners){
                    // if matched: update result status to 1(Matched)
                    DB::table($this->table)
                        ->where("track_code", $dataArray["track_code"])
                        ->where("race_number",$dataArray["race_number"])
                        ->where("race_date", $dataArray["race_date"])
                        ->update(["status" => 1]);
                    $logsModel->saveLog(["user_id"=>Auth::id(),"action"=>"Payout & Result Matched: " . $dataArray["track_code"] . " " . $dataArray["race_number"] . " " . $dataArray["race_date"]]);
                    return $firstResultEntry->id;
                }else{
                    DB::table($this->table)
                        ->where("track_code", $dataArray["track_code"])
                        ->where("race_number",$dataArray["race_number"])
                        ->where("race_date", $dataArray["race_date"])
                        ->update(["status" => 0]);
                    $logsModel->saveLog(["user_id"=>Auth::id(),"action"=>"Results Mismatched:" . $dataArray["track_code"] . " " . $dataArray["race_number"] . " " . $dataArray["race_date"] ]);
                    return 1; // Results MISMATCHED
                }
            }
        }
        return DB::getPdo()->lastInsertId();
    }
    public function checkResults($trkCode, $date, $raceNum){
        return DB::table($this->table)
            ->where("track_code",$trkCode)
            ->where("race_date",$date)
            ->where("race_number", $raceNum)
            ->first();
    }
    public function getLatestResult($id){
        return DB::table($this->table)
//            ->orderBy("updated_at","desc")
//            ->orderBy("created_at","desc")
                ->where("id",$id)
            ->get();
    }
    public function getSecondRaceRes($trkCode, $raceNum, $raceDate){
        return DB::table($this->table)
            ->where("track_code",$trkCode)
            ->where("race_date",$raceDate)
            ->where("race_number", $raceNum + 1)
            ->first();
    }
    public function getAllResults(){
        return DB::table($this->table)
            ->get();
    }
    public function getFirstRaceRes($trkCode, $raceNum, $raceDate){
        return DB::table($this->table)
            ->where("track_code",$trkCode)
            ->where("race_date",$raceDate)
            ->where("race_number", $raceNum - 1)
            ->first();
    }
    public function getAllMismatchedResults(){
        return DB::table($this->table)
            ->where("status",0)
            ->get();
    }
}
