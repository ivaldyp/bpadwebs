<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Traits\SessionCheckTraits;

use App\glo_org_unitkerja;
use App\Sec_access;
use App\Sec_menu;

session_start();

class KepegawaianSetupController extends Controller
{
    use SessionCheckTraits;

	public function __construct()
	{
		$this->middleware('auth');
		set_time_limit(300);
	}

	public function unitall(Request $request)
	{
		$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		$units = glo_org_unitkerja::
						where('sts', 1)
						->orderByRaw('coalesce(kd_unit, sao), sao, kd_unit')
						->get();

		return view('pages.bpadkepegawaiansetup.unit')
				->with('access', $access)
				->with('units', $units);
	}

	public function forminsertunit(Request $request)
	{
		$cekunit = glo_org_unitkerja::
						where('kd_unit', $request->kd_unit)
						->orWhere('nm_unit', strtoupper($request->nm_unit))
						->orWhere('notes', strtoupper($request->notes))
						->first();

		if ($cekunit['kd_unit'] == $request->kd_unit) {
			return redirect('/kepegawaian/setup/unit')
					->with('message', 'unit dengan kode '.strtoupper($request->kd_unit).' sudah ada di dalam database')
					->with('msg_num', 0);
		}

		if ($cekunit['nm_unit'] == strtoupper($request->nm_unit)) {
			return redirect('/kepegawaian/setup/unit')
					->with('message', 'unit dengan nama '.strtoupper($request->nm_unit).' sudah ada di dalam database')
					->with('msg_num', 0);
		}

		if ($cekunit['notes'] == strtoupper($request->notes)) {
			return redirect('/kepegawaian/setup/unit')
					->with('message', 'unit dengan notes '.strtoupper($request->notes).' sudah ada di dalam database')
					->with('msg_num', 0);
		}

		$unitpertama = glo_org_unitkerja::
						where('kd_unit', '01')
						->first();

		$tgl_unit = $unitpertama['tgl_unit'];

		$insert = [
				'sts'       => 1,
				'uname'		=> Auth::user()->usname,
				'tgl'		=> date('Y-m-d H:i:s'),
				'ip'		=> '',
				'logbuat'	=> '',
				'kd_skpd'	=> '',
				'kd_unit'	=> $request->kd_unit,
				'nm_unit'   => strtoupper($request->nm_unit),
				'cp_unit'   => '',
				'notes'   	=> strtoupper($request->notes),
				'child'   	=> 0,
				'sao'   	=> $request->sao == 0 ? '' : $request->sao ,
				'tgl_unit'  => $tgl_unit,
			];

		glo_org_unitkerja::insert($insert);

		return redirect('/kepegawaian/setup/unit')
					->with('message', 'unit '.strtoupper($request->nm_unit).' berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdateunit(Request $request)
	{
		$cekunit = glo_org_unitkerja::
						where('kd_unit', $request->kd_unit)
						->orWhere('nm_unit', strtoupper($request->nm_unit))
						->orWhere('notes', strtoupper($request->notes))
						->first();

		if ($cekunit['nm_unit'] == strtoupper($request->nm_unit)) {
			return redirect('/kepegawaian/setup/unit')
					->with('message', 'unit dengan nama '.strtoupper($request->nm_unit).' sudah ada di dalam database')
					->with('msg_num', 0);
		}

		if ($cekunit['notes'] == strtoupper($request->notes)) {
			return redirect('/kepegawaian/setup/unit')
					->with('message', 'unit dengan notes '.strtoupper($request->notes).' sudah ada di dalam database')
					->with('msg_num', 0);
		}

		glo_org_unitkerja::where('kd_unit', $request->kd_unit)
					->update([
						'kd_unit' => $request->kd_unit,
						'nm_unit' => strtoupper($request->nm_unit),
						'notes' => strtoupper($request->notes),
						'tgl' => date('Y-m-d H:i:s'),
					]);

		return redirect('/kepegawaian/setup/unit')
					->with('message', 'Unit '.strtoupper($request->nm_unit).' berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeleteunit(Request $request)
	{
		glo_org_unitkerja::
			where('kd_unit', 'like', $request['kd_unit'] . '%')
			->delete();

		return redirect('/kepegawaian/setup/unit')
					->with('message', 'Unit '.strtoupper($request->nm_unit).' (beserta childnya) berhasil dihapus')
					->with('msg_num', 1);
	}
}
