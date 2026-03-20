<?php

namespace App\Http\Controllers;

use App\Models\Leader;
use App\Models\User;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public $totalmembers;
    public function __construct()
    {
        $this->totalmembers=User::count();
    }

    public function index(){
        $membersCount=$this->totalmembers;
        return view('frontend.index', compact('membersCount') );
    }

    public function about(){
        $membersCount=$this->totalmembers;
        $leaders = Leader::query()
            ->with('user')
            ->where('is_published', true)
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view('frontend.about.about', compact('leaders', 'membersCount'));
    }
}
