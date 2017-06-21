<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Tracks extends Model
{
    protected $table = "tracks";
    public function getAllTracks($date){
        return DB::table($this->table)
            ->where('date',$date)
            ->get();
    }
}
