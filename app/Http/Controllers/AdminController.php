<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Theme;
class AdminController extends Controller
{
    public function dashboard(){
        $theme = Theme::uses('admin')->layout('layout')->setTitle('Admin');
        return $theme->of('admin/dashboard')->render();
    }
}
