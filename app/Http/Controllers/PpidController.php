<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Internal_ppid_permohonan_informasi;

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
        $ppid_nama = $request->ppid_nama;
        $ppid_identitas = $request->ppid_identitas;
        $ppid_email = $request->ppid_email;
        $ppid_telp = $request->ppid_telp;
        $ppid_alamat = $request->ppid_alamat;
        $ppid_informasi = $request->ppid_informasi;
        $ppid_tujuan = $request->ppid_tujuan;

        $ppid_identitas_file = '';

		// if (isset($request->ppid_identitas_file)) {
		// 	$file = $request->ppid_identitas_file;

		// 	if ($file->getSize() > 2200000) {
		// 		return redirect()->back()->withInput()->with('message', 'Ukuran file terlalu besar (Maksimal 2MB)');     
		// 	} 

		// 	$ppid_identitas_file .= $file->getClientOriginalName();

		// 	$tujuan_upload = config('app.savefileppid');
		// 	$file->move($tujuan_upload, $ppid_identitas_file);
		// }
			
		// if (!(isset($ppid_identitas_file))) {
		// 	$ppid_identitas_file = '';
		// }

        $insertppid = [
			'sts' => 1,
			'tgl'       => date('Y-m-d H:i:s'),
			'ppid_nama' => $ppid_nama,
            'ppid_identitas' => $ppid_identitas,
            'ppid_identitas_file' => $ppid_identitas_file,
            'ppid_email' => $ppid_email,
            'ppid_telp' => $ppid_telp,
            'ppid_alamat' => $ppid_alamat,
            'ppid_informasi' => $ppid_informasi,
            'ppid_tujuan' => $ppid_tujuan,
		];

		Internal_ppid_permohonan_informasi::insert($insertppid);

        return redirect()->back()
                ->with('message', 'Berhasil Mengirimkan Permohonan Informasi');
    }
}
