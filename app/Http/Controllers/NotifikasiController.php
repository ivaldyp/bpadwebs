<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Traits\SessionCheckTraits;
use App\Traits\SessionCheckNotif;

use App\Contenttb as Content_tb;
use App\Emp_notif;

session_start();

class NotifikasiController extends Controller
{
	use SessionCheckTraits;
	use SessionCheckNotif;

	public function notifall(Request $request)
	{
		if (Auth::user()->id_emp) {
			$ids = Auth::user()->id_emp;
		} elseif (Auth::user()->usname) {
			$ids = Auth::user()->usname;
		}

		$notifs = Emp_notif::
					where('id_emp', $ids)
					->where('sts', 1)
					->orderBy('tgl', 'desc')
					->get();

		return view('pages.bpadnotif.notifikasi')
				->with('notifs', $notifs);
	}

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
