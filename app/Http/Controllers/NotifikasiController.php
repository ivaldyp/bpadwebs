<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Contenttb as Content_tb;
use App\Emp_notif;

class NotifikasiController extends Controller
{
    public function cek($jenis, $id)
    {
    	Emp_notif::where('ids', $id)
		->update([
			'rd' => 'Y',
		]);

		$jenis = strtoupper($jenis);

		if ($jenis == 'KONTEN') {
			return redirect('/cms/content?suspnow=Y');
		} elseif ($jenis == 'KONTENAPPR') {
			return redirect('/cms/content');
		} elseif ($jenis == 'PROFIL') {
			return redirect('/profil/pegawai');
		} else {
			return redirect('/home');
		}
	}
}
