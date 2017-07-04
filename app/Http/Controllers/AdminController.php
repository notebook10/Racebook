<?php

namespace App\Http\Controllers;

use App\Bets;
use App\Tracks;
use Illuminate\Http\Request;
use Theme;
class AdminController extends Controller
{
    public function dashboard(){
        $theme = Theme::uses('admin')->layout('layout')->setTitle('Admin');
        return $theme->of('admin/dashboard')->render();
    }
    public function tracks(){
        date_default_timezone_set('America/Los_Angeles');
        $betsModel = new Tracks();
        $tracks = $betsModel->getAllTracks(date('mdy',time()));
        $dataArray = [
            'tracks' => $tracks
        ];
        $theme = Theme::uses('admin')->layout('layout')->setTitle('Admin');
        return $theme->of('admin/tracks', $dataArray)->render();
    }
}
