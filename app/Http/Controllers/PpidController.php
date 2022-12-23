<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class PpidController extends Controller
{
    public function profil(Request $request)
    {
        return view('pages.landingppid.profil');
    }

    public function form(Request $request)
    {
        return view('pages.landingppid.form');
    }

    public function informasipublik(Request $request)
    {
        return view('pages.landingppid.informasipublik');
    }

    public function saveform(Request $request)
    {
        var_dump($request->all());
        var_dump($request->ppid_identitas_file->getSize());
        var_dump($request->ppid_identitas_file->getClientOriginalName());
        die;

        return redirect()->back()->withInput();

        $fileppid = '';

		if (isset($request->dfile)) {
			$file = $request->dfile;

			if ($file->getSize() > 5500000) {
				return redirect('/internal/agenda tambah')->with('message', 'Ukuran file terlalu besar (Maksimal 5MB)');     
			} 

			$fileppid .= $file->getClientOriginalName();

			$tujuan_upload = config('app.savefileppid');
			$file->move($tujuan_upload, $fileppid);
		}
			
		if (!(isset($fileppid))) {
			$fileppid = '';
		}
    }
}
