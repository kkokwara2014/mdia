<?php

namespace App\Http\Controllers;

use App\Models\Leader;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function index(){
        return view('frontend.index');
    }

    public function about(){
        $leaders = Leader::query()
            ->with('user')
            ->where('is_published', true)
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view('frontend.about.about', compact('leaders'));
    }
}
