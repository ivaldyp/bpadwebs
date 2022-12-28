<?php

namespace App\Http\Controllers;

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use App\Traits\SessionCheckTraits;

use App\Emp_data;
use App\Emp_dik;
use App\Emp_gol;
use App\Emp_jab;
use App\Emp_non;
use App\Emp_kel;
use App\Emp_huk;
use App\Fr_suratkeluar;
use App\Fr_disposisi;
use App\Glo_dik;
use App\Glo_huk;
use App\Glo_kel;
use App\Glo_disposisi_kode;
use App\Glo_org_golongan;
use App\Glo_org_jabatan;
use App\Glo_org_kedemp;
use App\Glo_org_lokasi;
use App\Glo_org_petajab;
use App\Glo_org_statusemp;
use App\glo_org_unitkerja;
use App\Kinerja_data;
use App\Kinerja_detail;
use App\Sec_access;
use App\Sec_menu;
use App\V_disposisi;

session_start();

class Kepegawaian2Controller extends Controller
{
	use SessionCheckTraits;

	public function __construct()
	{
		$this->middleware('auth');
		set_time_limit(300);
	}

	public function checksession() {
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
	}

	public function petajabatan(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();
		$units = glo_org_unitkerja::
						where('sts', 1)
						->orderByRaw('coalesce(kd_unit, sao), sao, kd_unit')
						->get();

		return view('pages.bpadkepegawaian2.petajabatan')
				->with('units', $units);
	}

	public function forminsertjabchild(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();
		$isi_jab = $request->isi_jab;
		(is_null($request->slot_jab) ? $slot_jab = 0 : $slot_jab = $request->slot_jab);
		if ($isi_jab > $slot_jab) {
			return redirect()->back()->with('message', 'Jabatan terisi tidak boleh melebihi slot yang tersedia');
		}

		$cekidexist = Glo_org_petajab::
						where('kd_jab', $request->kd_jab)
						->where('sts', 1)
						->get();

		if (count($cekidexist) > 0) {
			return redirect()->back()->with('message', 'ID terdapat didalam database');
		}

		$insert = [
				'sts'       => 1,
				'uname'		=> Auth::user()->usname,
				'tgl'		=> date('Y-m-d H:i:s'),
				'update_data' => date('Y-m-d H:i:s'),

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
	}

	//------------------------------------------------------
	//----------------------E-SIAPPE------------------------
	//------------------------------------------------------

	public function laporanfoto (Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}

		//$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		if ($_SESSION['user_data']['idunit']) {
			$idunit = $_SESSION['user_data']['idunit'];
		} else {
			$idunit = '01';
		}

		$pegawais = DB::select( DB::raw("
		select id_emp, nm_emp, nrk_emp, nip_emp, tbunit.kd_unit, tbunit.nm_unit
		from bpaddtfake.dbo.emp_data a
		join bpaddtfake.dbo.emp_jab tbjab on tbjab.ids = (SELECT TOP 1 ids FROM bpaddtfake.dbo.emp_jab WHERE emp_jab.noid = a.id_emp and emp_jab.sts='1' ORDER BY tmt_jab DESC)
		join bpaddtfake.dbo.glo_org_unitkerja tbunit on tbunit.kd_unit = (SELECT TOP 1 idunit FROM bpaddtfake.dbo.glo_org_unitkerja where tbunit.kd_unit = tbjab.idunit)
		where a.ked_emp = 'AKTIF'
		and a.sts = 1
		and a.id_emp = tbjab.noid
		and tbjab.sts = 1
		and tbunit.sao like '01%'
		and LEN(tbunit.kd_unit) = 10
		order by tbunit.kd_unit, nm_emp") );
		$pegawais = json_decode(json_encode($pegawais), true);

		if ($request->now_id_emp) {
			//kalo ada input milih pegawai
			$now_id_emp = $request->now_id_emp;
		} else {
			// kalo gada milih pegawai
			if (Auth::user()->usname) {
				// kalo yg login admin -> ambil id_emp pertama
				$now_id_emp = $pegawais[0]['id_emp'];
			} elseif (Auth::user()->id_emp) {
				// kalo yg login pegawai
				if (strlen($_SESSION['user_data']['idunit']) == 10) {
					// set id_emp sekarang = id_emp pegawai yg login
					$now_id_emp = Auth::user()->id_emp;
				} else {
					// set id_emp sekarang = id_emp pertama dari query list pegawai
					$now_id_emp = $pegawais[0]['id_emp'];
				}
			}
		}

		if ($request->now_month) {
			$now_month = (int)$request->now_month;
		} else {
			$now_month = (int)date('m');
		}

		if ($request->now_year) {
			$now_year = (int)$request->now_year;
		} else {
			$now_year = (int)date('Y');
		}

		$laporans = DB::select( DB::raw("
					select DISTINCT foto.absen_id, foto.absen_tgl, 
					a.absen_jenis as jenis_pagi, a.absen_waktu as waktu_pagi, a.absen_sts as sts_pagi, a.absen_img as foto_pagi, 
					b.absen_jenis as jenis_sore, b.absen_waktu as waktu_sore, b.absen_sts as sts_sore, b.absen_img as foto_sore
					from bpaddtfake.dbo.kinerja_foto foto
					left join bpaddtfake.dbo.kinerja_foto a on foto.absen_tgl = a.absen_tgl and a.absen_jenis = 'pagi' and a.absen_id = '$now_id_emp'
					left join bpaddtfake.dbo.kinerja_foto b on foto.absen_tgl = b.absen_tgl and b.absen_jenis = 'sore' and b.absen_id = '$now_id_emp'
					where foto.absen_id = '$now_id_emp'
					and YEAR(foto.absen_tgl) = $now_year
					and MONTH(foto.absen_tgl) = $now_month
					order by foto.absen_tgl
					"));
		$laporans = json_decode(json_encode($laporans), true);
	
		return view('pages.bpadkepegawaian2.esiappelaporan')
				->with('access', $access)
				->with('pegawais', $pegawais)
				->with('now_id_emp', $now_id_emp)
				->with('now_month', $now_month)
				->with('now_year', $now_year)
				->with('laporans', $laporans);
	}
}
