<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PpidController extends Controller
{
    public function profil(Request $request)
    {
        return view('pages.landingppid.profil');
    }
}
