<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Traits\SessionCheckTraits;

use App\GLo_org_unitkerja;
use App\Notulen;
use App\Sec_menu;

session_start();

class NotulenController extends Controller
{
	use SessionCheckTraits;

	public function __construct()
	{
		$this->middleware('auth');
		set_time_limit(300);
	}

    public function tambahnotulen(Request $request)
	{
		$this->checkSessionTime();

		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		$idunit = $_SESSION['user_data']['idunit'];

		// if (Auth::user()->id_emp) {
		// 	if (strlen($idunit) == 10) {
		// 		$idunit = substr($idunit, 0, 8);
		// 	} elseif (strlen($idunit) == 8) {
		// 		$idunit = $idunit;
		// 	} else {
		// 		$idunit = '';
		// 	}
		// } else {
		// 	$idunit = '';
		// }

		$units = GLo_org_unitkerja::
					whereRaw('LEN(kd_unit) = 8 or LEN(kd_unit) = 6')
					->orderBy('kd_unit')
					->get();
		
		return view('pages.bpadnotulen.tambah')
				->with('access', $access)
				->with('idunit', $idunit)
				->with('units', $units);
	}

	public function forminsertnotulen(Request $request)
	{
		date_default_timezone_set('Asia/Jakarta');

		if ($request->btnDraft) {
			$status = 'd';
		} else {
			$status = 's';
		}

		$insertnotulen = [
			'sts' => 1,
			'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
			'tgl'       => date('Y-m-d H:i:s'),
			'createdby'	=> $request->createdby,
			'createdate' => date('Y-m-d H:i:s'),
			'status_notulen' => $status,
			'id_emp' => $request->id_emp,
			'nm_emp' => $request->nm_emp,
			'unit_emp' => $request->unit_emp,
			'not_dasar' => $request->not_dasar ?? '',
			'not_tempat' => $request->not_tempat,
			'not_tanggal' => date('Y-m-d',strtotime(str_replace('/', '-', $request->not_tanggal))),
			'not_mulai' => $request->not_mulai,
			'not_selesai' => $request->not_selesai,
			'not_acara' => $request->not_acara,
			'not_pimpinan' => $request->not_pimpinan,
			'not_undangan' => htmlentities($request->not_undangan ?? ''),
			'not_tidakhadir' => htmlentities($request->not_tidakhadir ?? ''),
			'not_latar' => htmlentities($request->not_latar ?? ''),
			'not_agenda' => htmlentities($request->not_agenda ?? ''),
			'not_pembahasan' => htmlentities($request->not_pembahasan ?? ''),
			'not_catatan' => htmlentities($request->not_catatan ?? ''),
			'not_kesimpulan' => htmlentities($request->not_kesimpulan ?? ''),
			'not_disppimpinan' => htmlentities($request->not_disppimpinan ?? ''),
			// 'nm_file' => $file,
		];

		Notulen::insert($insertnotulen);

		return redirect('/notulen/mynotulen')
				->with('message', 'Notulen berhasil dibuat')
				->with('msg_num', 1);
	}

	public function notulenall(Request $request)
	{
		$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		if ($request->yearnow) {
			$yearnow = (int)$request->yearnow;
		} else {
			$yearnow = (int)date('Y');
		}

		if ($request->monthnow) {
			$monthnow = (int)$request->monthnow;
		} else {
			$monthnow = (int)date('m');
		}

		if ($request->signnow) {
			$signnow = $request->signnow;
		} else {
			$signnow = "=";
		}

		if ($request->searchnow) {
			$qsearchnow = "and (not_acara like '%".$request->searchnow."%' ')";
		} else {
			$qsearchnow = "";
		}

		if (is_null($request->unit)) {
			if (Auth::user()->id_emp) {
				if (strlen($_SESSION['user_data']['idunit']) > 8) {
					$idunit = substr($_SESSION['user_data']['idunit'], 0, 8);
				} else {
					$idunit = $_SESSION['user_data']['idunit'];
				}
			} else {
				$idunit = '01';
			}
		} else {
			$idunit = $request->unit;
		}

		$qunit = "and (idunit)";

		$notulens = DB::select( DB::raw("
				  	SELECT [ids]
					      ,[sts]
					      ,[uname]
					      ,[tgl]
					      ,[createdby]
					      ,[createdate]
					      ,[status_notulen]
					      ,[id_emp]
					      ,[nm_emp]
					      ,[unit_emp]
					      ,[not_dasar]
					      ,[not_tempat]
					      ,[not_tanggal]
					      ,[not_mulai]
					      ,[not_selesai]
					      ,[not_acara]
					      ,[not_pimpinan]
					      ,[not_undangan]
					      ,[not_tidakhadir]
					      ,[not_latar]
					      ,[not_agenda]
					      ,[not_pembahasan]
					      ,[not_catatan]
					      ,[not_kesimpulan]
					      ,[not_disppimpinan]
					      ,[nm_file]
					  FROM [bpaddtfake].[dbo].[notulen]
					  where MONTH(not_tanggal) $signnow $monthnow
					  and YEAR(not_tanggal) = $yearnow
					  $qsearchnow
					  $qunit
					  order by not_tanggal desc
				"));
		$notulens = json_decode(json_encode($notulens), true);

		$units = GLo_org_unitkerja::
					whereRaw('LEN(kd_unit) <= 8')
					->orderBy('kd_unit')
					->get();

		return view('pages.bpadnotulen.notulen')
				->with('access', $access)
				->with('yearnow', $yearnow)
				->with('monthnow', $monthnow)
				->with('signnow', $signnow)
				->with('searchnow', $request->searchnow)
				->with('units', $units)
				->with('idunit', $idunit)
				->with('notulens', $notulens);
	}
}
