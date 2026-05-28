<?php

namespace App\Http\Controllers;

class FrontController extends Controller
{
    public function welcome()
    {
        return view('welcome');
    }

    public function home()
    {
        return view('home');
    }
}