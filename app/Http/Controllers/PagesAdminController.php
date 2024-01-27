<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Helper\ResponseBuilder;
use Illuminate\Support\Facades\Storage; 
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PagesAdminController extends Controller
{
    public function index(Request $request)
    {
        $title = "Pembelajaran"; 
        return view('absensi', compact('title')); 
    } 

    public function scan(Request $request)
    {
        $title = "Scan QrCode";
        return view('scan', compact('title'));
    }

    public function login(Request $request)
    {
        $title = "Login";
        return view('login', compact('title'));
    }
 
}