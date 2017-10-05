<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Scratches extends Model
{
    //
    protected $table = 'scratches';
    public function getScratchesByDate($date){
        $selectedDate = date('mdy',strtotime($date));
        return DB::table($this->table)
            ->where('race_date',$selectedDate)
            ->get();
    }
}
