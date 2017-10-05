<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    protected $table = "logs";
    public function saveLog($dataArray){
        $logs = new Logs();
        $logs->user_id = $dataArray['user_id'];
        $logs->action = $dataArray['action'];
        $logs->save();
        return 0;
    }
}
