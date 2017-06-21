<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function insertuser($dataArray){
        $insert = new User();
        $insert->firstname = $dataArray['firstname'];
        $insert->lastname = $dataArray['lastname'];
        $insert->user_type = $dataArray['user_type'];
        $insert->username = $dataArray['username'];
        $insert->password = bcrypt($dataArray['password']);
        $insert->save();
    }
    public static function checkusertype($id){
        $currentuser = User::where('id',$id)->first();
        if($currentuser->user_type == 1){
            return 'dashboard';
        }else if($currentuser->user_type == 2){
            return 'admin/dashboard';
        }
    }
}
