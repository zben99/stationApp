<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RepportController extends Controller
{
     public function index()
    {
        return view('repports.index');
    }
}
