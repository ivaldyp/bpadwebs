<?php

namespace App\Http\Controllers;

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Traits\SessionCheckTraits;
use App\Traits\SessionCheckNotif;

use App\Emp_data;
use App\Models11\Emp_data as Emp_data_11;
use App\Emp_dik;
use App\Models11\Emp_dik as Emp_dik_11;
use App\Emp_gol;
use App\Models11\Emp_gol as Emp_gol_11;
use App\Emp_jab;
use App\Models11\Emp_jab as Emp_jab_11;
use App\Emp_non;
use App\Emp_kel;
use App\Emp_huk;
use App\Emp_notif;
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
use App\Glo_org_statusemp;
use App\glo_org_unitkerja;
use App\Kinerja_data;
use App\Kinerja_detail;
use App\Sec_access;
use App\Sec_menu;
use App\V_disposisi;
use App\V_kinerja;

session_start();

class KepegawaianController extends Controller
{
	use SessionCheckTraits;
	use SessionCheckNotif;

	public function __construct()
	{
		$this->middleware('auth');
		set_time_limit(300);
	}

	// helpers aja
	private function isProduction()
	{
		return env('DB_HOST') == '10.15.38.76';
	}

	public function checksession() {
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
	}

	// ------------------ DATA PEGAWAI ------------------ //

	

	public function pegawaiall(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		unset($_SESSION['notifs']);
		if (Auth::user()->usname) {
			$notifs = $this->checknotif(Auth::user()->usname);
		} else {
			$notifs = $this->checknotif(Auth::user()->id_emp);
		}
		$_SESSION['notifs'] = $notifs;

		$units = Glo_org_unitkerja::orderBy('kd_unit')->get();

		if (is_null($request->kednow)) {
			$kednow = 'AKTIF';
		} else {
			$kednow = $request->kednow;
		}

		if (is_null($request->unit)) {
			if (Auth::user()->id_emp) {
				$idunit = $_SESSION['user_data']['idunit'];
			} else {
				$idunit = '01';
			}
		} else {
			$idunit = $request->unit;
		}

		if($kednow == 'AKTIF') {
			$sts = 'and a.sts = 1';
		} else {
			$sts = '';
		}

		$employees = DB::select( DB::raw("  
			SELECT *,
            CASE
                WHEN (status_emp not like 'NON PNS') 
                    THEN CONCAT(DATEDIFF(month, tmt_jab, GETDATE()) / 12, ' Tahun ', DATEDIFF(month, tmt_jab, GETDATE()) % 12, ' Bulan' )
            END as masa_unit
			from bpaddtfake.dbo.emp_data a
			join bpaddtfake.dbo.emp_jab tbjab on tbjab.ids = (SELECT TOP 1 ids FROM bpaddtfake.dbo.emp_jab WHERE emp_jab.noid = a.id_emp and emp_jab.sts='1' ORDER BY tmt_jab DESC)
			join bpaddtfake.dbo.glo_org_unitkerja tbunit on tbunit.kd_unit = (SELECT TOP 1 idunit FROM bpaddtfake.dbo.glo_org_unitkerja where tbunit.kd_unit = tbjab.idunit)
			join bpaddtfake.dbo.glo_org_lokasi tblok on tblok.kd_lok = tbjab.idlok
			where a.ked_emp = '$kednow'
			$sts 
			and a.id_emp = tbjab.noid
			and tbjab.sts = 1
			and tbunit.kd_unit like '$idunit%'
			order by idunit, nm_emp") );
		$employees = json_decode(json_encode($employees), true);
		
		$kedudukans = Glo_org_kedemp::get();

		return view('pages.bpadkepegawaian.pegawai')
				->with('access', $access)
				->with('kednow', $kednow)
				->with('idunit', $idunit)
				->with('employees', $employees)
				->with('units', $units)
				->with('kedudukans', $kedudukans)
				->with('notifs', $notifs);
		
	}

	public function pegawaitambah()
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		$id_emp = explode(".", Emp_data::max('id_emp'));

		$statuses = Glo_org_statusemp::get();

		$idgroups = Sec_access::
					distinct('idgroup')
					->where('zfor', '2,')
					->orderBy('idgroup')
					->get('idgroup');

		$pendidikans = Glo_dik::
						orderBy('urut')
						->get();

		$golongans = Glo_org_golongan::
					orderBy('gol', 'desc')
					->get();

		$jabatans = Glo_org_jabatan::
					orderBy('jabatan')
					->get();

		$lokasis = Glo_org_lokasi::
					orderBy('kd_lok')
					->get();

		$kedudukans = Glo_org_kedemp::get();

		$units = glo_org_unitkerja::orderBy('kd_unit', 'asc')->get();

		return view('pages.bpadkepegawaian.pegawaitambah')
				->with('id_emp', $id_emp)
				->with('statuses', $statuses)
				->with('idgroups', $idgroups)
				->with('pendidikans', $pendidikans)
				->with('golongans', $golongans)
				->with('jabatans', $jabatans)
				->with('lokasis', $lokasis)
				->with('kedudukans', $kedudukans)
				->with('units', $units);
	}

	public function pegawaiubah(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		$id_emp = $request->id_emp;

		$emp_data = Emp_data::
						where('id_emp', $id_emp)
						->first();

		$emp_dik = Emp_dik::
						where('noid', $id_emp)
						->where('sts', 1)
						->orderBy('th_sek', 'desc')
						->get();

		$emp_gol = Emp_gol::
						join('bpaddtfake.dbo.glo_org_golongan', 'bpaddtfake.dbo.glo_org_golongan.gol', '=', 'bpaddtfake.dbo.emp_gol.idgol')
						->where('bpaddtfake.dbo.emp_gol.noid', $id_emp)
						->where('bpaddtfake.dbo.emp_gol.sts', 1)
						->orderBy('bpaddtfake.dbo.emp_gol.tmt_gol', 'desc')
						->get();

		// $emp_gol = Emp_gol::
		// 				where('noid', $id_emp)
		// 				->where('sts', 1)
		// 				->orderBy('tmt_gol', 'desc')
		// 				->get();

		$emp_jab = Emp_jab::
						with('lokasi')
						->with('unit')
						->where('noid', $id_emp)
						->where('sts', 1)
						->orderBy('tmt_jab', 'desc')
						->get();

		$emp_non = Emp_non::where('sts', 1)
						->where('noid', $id_emp)
						->orderBy('tgl_non', 'desc')
						->get();

		// var_dump($emp_non);
		// die();

		$emp_kel = Emp_kel::
					join('bpaddtfake.dbo.glo_kel', 'bpaddtfake.dbo.glo_kel.kel', '=', 'bpaddtfake.dbo.emp_kel.jns_kel')
					->where('bpaddtfake.dbo.emp_kel.noid', $id_emp)
					->where('bpaddtfake.dbo.emp_kel.sts', 1)
					->orderBy('urut', 'asc')
					->get();

		$emp_huk = Emp_huk::
					where('sts', 1)
					->where('noid', $id_emp)
					->orderBy('tgl_sk', 'desc')
					->get();


		$statuses = Glo_org_statusemp::get();

		$idgroups = Sec_access::
					distinct('idgroup')
					// ->where('zfor', '2,')
					->orderBy('idgroup')
					->get('idgroup');

		$pendidikans = Glo_dik::
						orderBy('urut')
						->get();

		$golongans = Glo_org_golongan::
					orderBy('gol')
					->get();

		$jabatans = Glo_org_jabatan::
					orderBy('jabatan')
					->get();

		$lokasis = Glo_org_lokasi::
					orderBy('kd_lok')
					->get();

		$kedudukans = Glo_org_kedemp::get();

		$units = glo_org_unitkerja::orderBy('kd_unit', 'asc')->get();

		$keluargas = Glo_kel::orderBy('urut')->get();

		$hukumans = Glo_huk::orderBy('urut_huk')->get();

		return view('pages.bpadkepegawaian.pegawaiubah')
				->with('id_emp', $id_emp)
				->with('emp_data', $emp_data)
				->with('emp_dik', $emp_dik)
				->with('emp_gol', $emp_gol)
				->with('emp_jab', $emp_jab)
				->with('emp_non', $emp_non)
				->with('emp_kel', $emp_kel)
				->with('emp_huk', $emp_huk)
				->with('statuses', $statuses)
				->with('idgroups', $idgroups)
				->with('pendidikans', $pendidikans)
				->with('golongans', $golongans)
				->with('jabatans', $jabatans)
				->with('lokasis', $lokasis)
				->with('kedudukans', $kedudukans)
				->with('units', $units)
				->with('keluargas', $keluargas)
				->with('hukumans', $hukumans);
	}

	public function formapprovepegawai(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}

		$namespace = '\\App\\'; 
		$namemodel = "Emp_".$request->formtipe;

		$model = $namespace . $namemodel; 

		$query = $model::
				where('noid', $request->id_emp)
				->where('ids', $request->ids)
				->first();

		if ($query['gambar'] && $request->appr == 0) {
			$file = $query['gambar'];

			$tujuan_upload = config('app.savefileimg');
			$tujuan_upload .= "\\" . $request->id_emp . "\\". $request->formtipe ."\\";

			if (file_exists($tujuan_upload . $file )) {
				unlink($tujuan_upload . $file);
			}

			$model::
				where('noid', $request->id_emp)
				->where('ids', $request->ids)
				->update([
					'gambar' => null,
					'appr' => $request->appr,
					'alasan' => $request->alasan,
				]);
		} else {
			$model::
				where('noid', $request->id_emp)
				->where('ids', $request->ids)
				->update([
					'appr' => $request->appr,
					'alasan' => $request->alasan,
				]);
		}

		if (strtolower($request->formtipe) == 'jab') {
			$formtipe = 'Jabatan';
		} elseif (strtolower($request->formtipe) == 'gol') {
			$formtipe = 'Golongan';
		}

		date_default_timezone_set('Asia/Jakarta');
		if ($request->appr == '0') {
			$notifapprcontent = [
					'sts'       => 1,
					'tgl'       => date('Y-m-d H:i:s'),
					'id_emp'	=> $request->id_emp,
					'jns_notif'	=> 'PROFIL',
					'message1'	=> 'Sertifikat '.ucwords(strtolower($request->formtipe)).' anda telah ditolak',
					'message2'	=> $request->alasan,
					'rd'		=> 'N',
				];
			Emp_notif::insert($notifapprcontent);
		}
		
		return redirect('/kepegawaian/ubah%20pegawai?id_emp='.$request->id_emp)
					->with('message', 'Approval file pegawai berhasil')
					->with('msg_num', 1);
	}

	public function forminsertpegawai(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		$id_emp = explode(".", Emp_data::max('id_emp'));
		$new_id_emp = $id_emp[0] . "." . $id_emp[1] . "." . $id_emp[2] . "." . ($id_emp[3] + 1);

		if($request->nrk_emp == '' or is_null($request->nrk_emp)){
			$new_nrk = Emp_data::whereRaw('LEN(nrk_emp) = 4')->max('nrk_emp');
			$new_nrk = $new_nrk + 1;
		} else {
			$new_nrk = $request->nrk_emp;
		}

		$filefoto = '';

		$cekangkapenting = Emp_data::where('id_emp', $request->id_emp)
									->where('ked_emp', 'aktif')
									->where('sts', 1)
									->first(['nik_emp', 'nrk_emp', 'nip_emp']);

		if ($request->nip_emp && $request->nip_emp != '' && $request->nip_emp != $cekangkapenting['nip_emp']) {
			$ceknip = Emp_data::
						where('nip_emp', $request->nip_emp)
						->where('ked_emp', 'aktif')
						->where('sts', '1')
						->count();
			if ($ceknip > 0) {
				return redirect('/kepegawaian/tambah%20pegawai')->with('message', 'NIP sudah tersimpan di database');
			}
		}
		if (strlen($request->nip_emp) > 21) {
			return redirect('/kepegawaian/tambah%20pegawai')->with('message', 'NIP harus terdiri dari 18 digit');
		}
			
		if ($request->nrk_emp && $request->nrk_emp != '' && $request->nrk_emp != $cekangkapenting['nrk_emp']) {
			$ceknrk = Emp_data::
						where('nrk_emp', $request->nrk_emp)
						->where('ked_emp', 'aktif')
						->where('sts', '1')
						->count();
			if ($ceknrk > 0) {
				return redirect('/kepegawaian/tambah%20pegawai')->with('message', 'NRK sudah tersimpan di database');
			}
		}
		if (strlen($request->nrk_emp) > 9) {
			return redirect('/kepegawaian/tambah%20pegawai')->with('message', 'NRK harus terdiri dari 6 digit');
		}

		if ($request->nik_emp && $request->nik_emp != '' && $request->nik_emp != $cekangkapenting['nik_emp']) {
			$ceknrk = Emp_data::
						where('nik_emp', $request->nik_emp)
						->where('ked_emp', 'aktif')
						->where('sts', '1')
						->count();
			if ($ceknrk > 0) {
				return redirect('/kepegawaian/tambah%20pegawai')->with('message', 'NIK KTP sudah tersimpan di database');
			}
		}	
		if (strlen($request->nik_emp) > 16) {
			return redirect('/kepegawaian/tambah%20pegawai')->with('message', 'NIK KTP harus terdiri dari 16 digit');
		}

		$insert_emp_data = [
				// IDENTITAS
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'createdate' => date('Y-m-d H:i:s'),
				'id_emp' => $new_id_emp,
				'nip_emp' => ($request->nip_emp ? $request->nip_emp : ''),
				'nrk_emp' => $new_nrk,
				'nm_emp' => ($request->nm_emp ? $request->nm_emp : ''),
				'nik_emp' => ($request->nik_emp ? $request->nik_emp : ''),
				'gelar_dpn' => ($request->gelar_dpn ? $request->gelar_dpn : ''),
				'gelar_blk' => ($request->gelar_blk ? $request->gelar_blk : ''),
				'jnkel_emp' => $request->jnkel_emp,
				'tempat_lahir' => ($request->tempat_lahir ? $request->tempat_lahir : ''),
				'tgl_lahir' => ($request->tgl_lahir ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_lahir))) : NULL),
				'idagama' => $request->idagama,
				'alamat_emp' => ($request->alamat_emp ? $request->alamat_emp : ''),
				'tlp_emp' => ($request->tlp_emp ? $request->tlp_emp : ''),
				'email_emp' => ($request->email_emp ? $request->email_emp : ''),
				'status_emp' => $request->status_emp,
				'ked_emp' => $request->ked_emp,
				'status_nikah' => $request->status_nikah,
				'gol_darah' => $request->gol_darah,
				'nm_bank' => ($request->nm_bank ? $request->nm_bank : ''),
				'cb_bank' => ($request->cb_bank ? $request->cb_bank : ''),
				'an_bank' => ($request->an_bank ? $request->an_bank : ''),
				'nr_bank' => ($request->nr_bank ? $request->nr_bank : ''),
				'no_taspen' => ($request->no_taspen ? $request->no_taspen : ''),
				'npwp' => ($request->npwp ? $request->npwp : ''),
				'no_askes' => ($request->no_askes ? $request->no_askes : ''),
				'no_jamsos' => ($request->no_jamsos ? $request->no_jamsos : ''),
				'tgl_join' => (isset($request->tgl_join) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_join))) : null),
				'tgl_end' => null,
				'reason' => '',
				'idgroup' => $request->idgroup,
				'pass_emp' => '',
				'foto' => $filefoto,
				'lastlogin' => null,
				'lastip' => '',
				'lasttemp' => '',
				'dwinternal' => '',
				'dwaset' => '',
				'ttd' => '',
				'telegram_id' => '',
				'passmd5' => md5($request->passmd5),
				'idgroup_aset' => 'EMPLOYEE',
				// 'tampilnew' => 1,
			];

		$insert_emp_dik = [
				// PENDIDIKAN
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'noid' => $new_id_emp,
				'iddik' => $request->iddik,
				'prog_sek' => ($request->prog_sek ? $request->prog_sek : ''),
				'nm_sek' => ($request->nm_sek ? $request->nm_sek : ''),
				'no_sek' => ($request->no_sek ? $request->no_sek : ''),
				'th_sek' => ($request->th_sek ? $request->th_sek : ''),
				'gelar_dpn_sek' => ($request->gelar_dpn_sek ? $request->gelar_dpn_sek : ''),
				'gelar_blk_sek' => ($request->gelar_blk_sek ? $request->gelar_blk_sek : ''),
				'ijz_cpns' => 'T',
				// 'tampilnew' => 1,
			];

		$insert_emp_gol = [
				// GOLONGAN
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'noid' => $new_id_emp,
				'tmt_gol' => (isset($request->tmt_gol) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_gol))) : date('Y-m-d')),
				'tmt_sk_gol' => (isset($request->tmt_sk_gol) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_gol))) : date('Y-m-d')),
				'no_sk_gol' => ($request->no_sk_gol ? $request->no_sk_gol : ''),
				'idgol' => $request->idgol,
				'jns_kp' => $request->jns_kp,
				'mk_thn' => ($request->mk_thn ? $request->mk_thn : 0),
				'mk_bln' => ($request->mk_bln ? $request->mk_bln : 0),
				// 'tampilnew' => 1,
			];

		// $jabatan = explode("||", $request->jabatan);
		// $jns_jab = $jabatan[0];
		// $idjab = $jabatan[1];

		if (strlen($request->idunit) > 2) {
			$cutidunit = substr($request->idunit, 4, 2);
			if (substr($cutidunit, 0, 1) == '5') {
				$idlok = Glo_org_lokasi::where('new_kd_lok', $cutidunit)->first(['kd_lok']);
			} elseif (substr($cutidunit, 0, 1) == '0') {
				if ($cutidunit == '06') {
					$idlok = Glo_org_lokasi::where('new_kd_lok', $cutidunit)->first(['kd_lok']);
				} else if ($cutidunit == '07') {
					$idlok = Glo_org_lokasi::where('new_kd_lok', $cutidunit)->first(['kd_lok']);
				} else if ($cutidunit == '08') {
					$idlok = Glo_org_lokasi::where('new_kd_lok', $cutidunit)->first(['kd_lok']);
				} else {
					$idlok = Glo_org_lokasi::where('new_kd_lok', '0')->first(['kd_lok']);
				}
			}
		} else {
			$idlok = ['kd_lok' => '00'];
		}

		$splitidunit = explode("::", $request->idunit);
		$idunit = $splitidunit[0];
		$nmunit = $splitidunit[1];

		$insert_emp_jab = [
				// JABATAN
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'noid' => $new_id_emp,
				'tmt_jab' => (isset($request->tmt_jab) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_jab))) : date('Y-m-d')),
				'idskpd' => '1.20.512',
				'idunit' => $idunit,
				'idlok' => $idlok['kd_lok'],
				'tmt_sk_jab' => (isset($request->tmt_sk_jab) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_jab))) : date('Y-m-d')),
				'no_sk_jab' => ($request->no_sk_jab ? $request->no_sk_jab : ''),
				'jns_jab' => $request->jns_jab,
				'idjab' => $request->idjab,
				'eselon' => $request->eselon,
				'nmunit' => $nmunit,
				// 'tampilnew' => 1,
			];

		Emp_data::insert($insert_emp_data);
		Emp_dik::insert($insert_emp_dik);
		Emp_gol::insert($insert_emp_gol);
		Emp_jab::insert($insert_emp_jab);

		if($this->isProduction()) {
			Emp_data_11::insert($insert_emp_data);
			Emp_dik_11::insert($insert_emp_dik);
			Emp_gol_11::insert($insert_emp_gol);
			Emp_jab_11::insert($insert_emp_jab);
		}

		return redirect('/kepegawaian/data pegawai')
					->with('message', 'Pegawai '.$request->nm_emp.' berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdatepegawai(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		$id_emp = $request->id_emp;

		$cekangkapenting = Emp_data::where('id_emp', $request->id_emp)->first(['nik_emp', 'nrk_emp', 'nip_emp']);

		if ($request->nip_emp && $request->nip_emp != '' && $request->nip_emp != $cekangkapenting['nip_emp']) {
			$ceknip = Emp_data::
						where('nip_emp', $request->nip_emp)
						->where('sts', '1')
						->count();
			if ($ceknip > 0) {
				return redirect('/kepegawaian/ubah%20pegawai?id_emp='.$id_emp)->with('message', 'NIP sudah tersimpan di database');
			}
		}
		if (strlen($request->nip_emp) > 21) {
			return redirect('/kepegawaian/ubah%20pegawai?id_emp='.$id_emp)->with('message', 'NIP harus terdiri dari 18 digit');
		}
			
		if ($request->nrk_emp && $request->nrk_emp != '' && $request->nrk_emp != $cekangkapenting['nrk_emp']) {
			$ceknrk = Emp_data::
						where('nrk_emp', $request->nrk_emp)
						->where('sts', '1')
						->count();
			if ($ceknrk > 0) {
				return redirect('/kepegawaian/ubah%20pegawai?id_emp='.$id_emp)->with('message', 'NRK sudah tersimpan di database');
			}
		}
		if (strlen($request->nrk_emp) > 9) {
			return redirect('/kepegawaian/ubah%20pegawai?id_emp='.$id_emp)->with('message', 'NRK harus terdiri dari 6 digit');
		}

		if ($request->nik_emp && $request->nik_emp != '' && $request->nik_emp != $cekangkapenting['nik_emp']) {
			$ceknrk = Emp_data::
						where('nik_emp', $request->nik_emp)
						->where('sts', '1')
						->count();
			if ($ceknrk > 0) {
				return redirect('/kepegawaian/ubah%20pegawai?id_emp='.$id_emp)->with('message', 'NIK KTP sudah tersimpan di database');
			}
		}	
		if (strlen($request->nik_emp) > 16) {
			return redirect('/kepegawaian/ubah%20pegawai?id_emp='.$id_emp)->with('message', 'NIK KTP harus terdiri dari 16 digit');
		}
		
		$filefoto = '';
	
		// mulai insert

        $pegawai_id_update = [
            'tgl_join' => (isset($request->tgl_join) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_join))) : null),
            'status_emp' => $request->status_emp,
            'nip_emp' => ($request->nip_emp ? $request->nip_emp : ''),
            'nrk_emp' => ($request->nrk_emp ? $request->nrk_emp : ''),
            'nm_emp' => ($request->nm_emp ? strtoupper($request->nm_emp) : ''),
            'nik_emp' => ($request->nik_emp ? $request->nik_emp : ''),
            'gelar_dpn' => ($request->gelar_dpn ? $request->gelar_dpn : ''),
            'gelar_blk' => ($request->gelar_blk ? $request->gelar_blk : ''),
            'jnkel_emp' => $request->jnkel_emp,
            'tempat_lahir' => ($request->tempat_lahir ? $request->tempat_lahir : ''),
            'tgl_lahir' => (isset($request->tgl_lahir) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_lahir))) : null),
            'idagama' => $request->idagama,
            'alamat_emp' => ($request->alamat_emp ? $request->alamat_emp : ''),
            'tlp_emp' => ($request->tlp_emp ? $request->tlp_emp : ''),
            'email_emp' => ($request->email_emp ? $request->email_emp : ''),
            'status_nikah' => $request->status_nikah,
            'gol_darah' => $request->gol_darah,
            'nm_bank' => ($request->nm_bank ? $request->nm_bank : ''),
            'cb_bank' => ($request->cb_bank ? $request->cb_bank : ''),
            'an_bank' => ($request->an_bank ? $request->an_bank : ''),
            'nr_bank' => ($request->nr_bank ? $request->nr_bank : ''),
            'no_taspen' => ($request->no_taspen ? $request->no_taspen : ''),
            'npwp' => ($request->npwp ? $request->npwp : ''),
            'no_askes' => ($request->no_askes ? $request->no_askes : ''),
            'no_jamsos' => ($request->no_jamsos ? $request->no_jamsos : ''),
            'idgroup' => $request->idgroup,
            'idgroup_aset' => $request->idgroup_aset,
            'updated_at'    => date('Y-m-d H:i:s'),
            'updated_by'    => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
            'tmt_sk_cpns' => ($request->tmt_sk_cpns ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_cpns))) : NULL),
            'tmt_sk_pns' => ($request->tmt_sk_pns ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_pns))) : NULL),
        ];

		Emp_data::where('id_emp', $id_emp)->update($pegawai_id_update);
		Emp_data_11::where('id_emp', $id_emp)->update($pegawai_id_update);

		return redirect('/kepegawaian/data pegawai')
					->with('message', 'Pegawai '.$request->nm_emp.' berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletepegawai(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		Emp_data::where('id_emp', $request->id_emp)
					->update([
						'sts' => 0,
						'ked_emp' => "HAPUS",
					]);

		Emp_dik::where('noid', $request->id_emp)
					->update([
						'sts' => 0,
					]);

		Emp_gol::where('noid', $request->id_emp)
					->update([
						'sts' => 0,
					]);

		Emp_jab::where('noid', $request->id_emp)
					->update([
						'sts' => 0,
					]);

        if($this->isProduction()) {
            Emp_data_11::where('id_emp', $request->id_emp)
					->update([
						'sts' => 0,
						'ked_emp' => "HAPUS",
					]);

            Emp_dik_11::where('noid', $request->id_emp)
                        ->update([
                            'sts' => 0,
                        ]);

            Emp_gol_11::where('noid', $request->id_emp)
                        ->update([
                            'sts' => 0,
                        ]);

            Emp_jab_11::where('noid', $request->id_emp)
                        ->update([
                            'sts' => 0,
                        ]);
        }
					
		return redirect('/kepegawaian/data pegawai')
					->with('message', 'Pegawai '.$request->nm_emp.' berhasil dihapus')
					->with('msg_num', 1);
	}

	public function formupdatepassuser(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}

		//$this->checkSessionTime();
        $username = $request->id_emp;
        $new_password = $request->passmd5;
        
        $url = 'https://jakaset.jakarta.go.id/api_login/api/auth/update';
        $dataArray      =        array(
            "username"        => $username,
            "new_password"    => $new_password,
        );

        $httpClient = new \GuzzleHttp\Client();
        $response = $httpClient->post($url, [
            'form_params' => $dataArray
        ]);

		Emp_data::
			where('id_emp', $request->id_emp)
			->update([
				'passmd5' => md5($request->passmd5),
                'updated_at' => date('Y-m-d H:i:s'),
				'updated_by' => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
			]);

        Emp_data_11::
			where('id_emp', $request->id_emp)
			->update([
				'passmd5' => md5($request->passmd5),
                'updated_at' => date('Y-m-d H:i:s'),
				'updated_by' => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
			]);

		return redirect()->back()
					->with('message', 'Password '.$request->nm_emp.' berhasil diubah')
					->with('msg_num', 1);
	}

	public function formupdatestatuspegawai(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		if ($request->ked_emp == 'AKTIF') {
			$sts = 1;
			$tgl_end = null;
		} else {
			$sts = 0;
			$tgl_end = (isset($request->tgl_end) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_end))) : null);
		}

        $pegawai_status_update = [
            'tgl_end' => $tgl_end,
            'ked_emp' => $request->ked_emp,
            'sts'	  => $sts,
        ];

		Emp_data::where('id_emp', $request->id_emp)->update($pegawai_status_update);
		Emp_data_11::where('id_emp', $request->id_emp)->update($pegawai_status_update);

		return redirect('/kepegawaian/data pegawai')
					->with('message', 'Status pegawai berhasil diubah')
					->with('msg_num', 1);
	}

	public function forminsertdikpegawai(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		$insert_emp_dik = [
				// PENDIDIKAN
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'noid' => $request->noid,
				'iddik' => $request->iddik,
				'prog_sek' => ($request->prog_sek ? $request->prog_sek : ''),
				'nm_sek' => ($request->nm_sek ? $request->nm_sek : ''),
				'no_sek' => ($request->no_sek ? $request->no_sek : ''),
				'th_sek' => ($request->th_sek ? $request->th_sek : ''),
				'gelar_dpn_sek' => ($request->gelar_dpn_sek ? $request->gelar_dpn_sek : ''),
				'gelar_blk_sek' => ($request->gelar_blk_sek ? $request->gelar_blk_sek : ''),
				'ijz_cpns' => 'T',
				'tampilnew' => 1,
			];

		Emp_dik::insert($insert_emp_dik);
		Emp_dik_11::insert($insert_emp_dik);

		return redirect('/kepegawaian/ubah pegawai?id_emp='.$request->noid)
					->with('message', 'Data pendidikan pegawai berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdatedikpegawai(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

        $pegawai_dik_update = [
            'iddik' => $request->iddik,
            'prog_sek' => ($request->prog_sek ? $request->prog_sek : ''),
            'nm_sek' => ($request->nm_sek ? $request->nm_sek : ''),
            'no_sek' => ($request->no_sek ? $request->no_sek : ''),
            'th_sek' => ($request->th_sek ? $request->th_sek : ''),
            'gelar_dpn_sek' => ($request->gelar_dpn_sek ? $request->gelar_dpn_sek : ''),
            'gelar_blk_sek' => ($request->gelar_blk_sek ? $request->gelar_blk_sek : ''),
        ];

		Emp_dik::where('noid', $request->noid)
			->where('ids', $request->ids)
			->update($pegawai_dik_update);
		Emp_dik_11::where('noid', $request->noid)
			->where('ids', $request->ids)
			->update($pegawai_dik_update);

		return redirect('/kepegawaian/ubah pegawai?id_emp='.$request->noid)
					->with('message', 'Data pendidikan pegawai berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletedikpegawai(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		$cekcountdik = Emp_dik::where('noid', $request->noid)->where('sts', 1)->count();
		if ($cekcountdik == 1) {
			return redirect('/kepegawaian/ubah pegawai?id_emp='.$request->noid)->with('message', 'Tidak dapat menghapus habis data pendidikan pegawai');
		}

		Emp_dik::where('noid', $request->noid)
			->where('ids', $request->ids)
			->update([
				'sts' => 0,
			]);
		Emp_dik_11::where('noid', $request->noid)
			->where('ids', $request->ids)
			->update([
				'sts' => 0,
			]);

		return redirect('/kepegawaian/ubah pegawai?id_emp='.$request->noid)
					->with('message', 'Data pendidikan '.$request->iddik.' berhasil dihapus')
					->with('msg_num', 1);
	}

	//------------------------------------------------------

	public function forminsertgolpegawai(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		$insert_emp_gol = [
				// GOLONGAN
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'noid' => $request->noid,
				'tmt_gol' => (isset($request->tmt_gol) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_gol))) : date('Y-m-d H:i:s')),
				'tmt_sk_gol' => (isset($request->tmt_sk_gol) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_gol))) : date('Y-m-d H:i:s')),
				'no_sk_gol' => ($request->no_sk_gol ? $request->no_sk_gol : ''),
				'idgol' => $request->idgol,
				'jns_kp' => $request->jns_kp,
				'mk_thn' => ($request->mk_thn ? $request->mk_thn : 0),
				'mk_bln' => ($request->mk_bln ? $request->mk_bln : 0),
				// 'tampilnew' => 1,
			];

		Emp_gol::insert($insert_emp_gol);
		Emp_gol_11::insert($insert_emp_gol);

		return redirect('/kepegawaian/ubah pegawai?id_emp='.$request->noid)
					->with('message', 'Data golongan pegawai berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdategolpegawai(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

        $pegawai_gol_update = [
            'tmt_gol' => (isset($request->tmt_gol) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_gol))) : date('Y-m-d H:i:s')),
            'tmt_sk_gol' => (isset($request->tmt_sk_gol) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_gol))) : date('Y-m-d H:i:s')),
            'no_sk_gol' => ($request->no_sk_gol ? $request->no_sk_gol : ''),
            'idgol' => $request->idgol,
            'jns_kp' => $request->jns_kp,
            'mk_thn' => ($request->mk_thn ? $request->mk_thn : 0),
            'mk_bln' => ($request->mk_bln ? $request->mk_bln : 0),
        ];

		Emp_gol::where('noid', $request->noid)
			->where('ids', $request->ids)
			->update($pegawai_gol_update);
		Emp_gol_11::where('noid', $request->noid)
			->where('ids', $request->ids)
			->update($pegawai_gol_update);

		return redirect('/kepegawaian/ubah pegawai?id_emp='.$request->noid)
					->with('message', 'Data golongan pegawai berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletegolpegawai(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		$cekcountgol = Emp_gol::where('noid', $request->noid)->where('sts', 1)->count();
		if ($cekcountgol == 1) {
			return redirect('/kepegawaian/ubah pegawai?id_emp='.$request->noid)->with('message', 'Tidak dapat menghapus habis golongan pegawai. Buat golongan baru lalu hapus yang lama.');
		}

		Emp_gol::where('noid', $request->noid)
			->where('ids', $request->ids)
			->update([
				'sts' => 0,
			]);
		Emp_gol_11::where('noid', $request->noid)
			->where('ids', $request->ids)
			->update([
				'sts' => 0,
			]);

		return redirect('/kepegawaian/ubah pegawai?id_emp='.$request->noid)
					->with('message', 'Data golongan '.$request->idgol.' berhasil dihapus')
					->with('msg_num', 1);
	}

	//--------------------------------------------------

	public function forminsertjabpegawai(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		// $jabatan = explode("||", $request->jabatan);
		// $jns_jab = $jabatan[0];
		// $idjab = $jabatan[1];

		// // ---CEK JABATAN UDAH TERISI--- //
		// if (strlen($request->idunit) < 10) {
		// 	$findidjabatan = DB::select( DB::raw("
		// 			SELECT id_emp,a.uname+'::'+convert(varchar,a.tgl)+'::'+a.ip,createdate,nip_emp,nrk_emp,nm_emp,nrk_emp+'-'+nm_emp as c2,gelar_dpn,gelar_blk,jnkel_emp,tempat_lahir,tgl_lahir,CONVERT(VARCHAR(10), tgl_lahir, 103) AS [DD/MM/YYYY],idagama,alamat_emp,tlp_emp,email_emp,status_emp,ked_emp,status_nikah,gol_darah,nm_bank,cb_bank,an_bank,nr_bank,no_taspen,npwp,no_askes,no_jamsos,tgl_join,CONVERT(VARCHAR(10), tgl_join, 103) AS [DD/MM/YYYY],tgl_end,reason,a.idgroup,pass_emp,foto,ttd,a.telegram_id,a.lastlogin,tbgol.tmt_gol,CONVERT(VARCHAR(10), tbgol.tmt_gol, 103) AS [DD/MM/YYYY],tbgol.tmt_sk_gol,CONVERT(VARCHAR(10), tbgol.tmt_sk_gol, 103) AS [DD/MM/YYYY],tbgol.no_sk_gol,tbgol.idgol,tbgol.jns_kp,tbgol.mk_thn,tbgol.mk_bln,tbgol.gambar,tbgol.nm_pangkat,tbjab.tmt_jab,CONVERT(VARCHAR(10), tbjab.tmt_jab, 103) AS [DD/MM/YYYY],tbjab.idskpd,tbjab.idunit,tbjab.idjab, tbunit.child, tbjab.idlok,tbjab.tmt_sk_jab,CONVERT(VARCHAR(10), tbjab.tmt_sk_jab, 103) AS [DD/MM/YYYY],tbjab.no_sk_jab,tbjab.jns_jab,tbjab.idjab,tbjab.eselon,tbjab.gambar,tbdik.iddik,tbdik.prog_sek,tbdik.no_sek,tbdik.th_sek,tbdik.nm_sek,tbdik.gelar_dpn_sek,tbdik.gelar_blk_sek,tbdik.ijz_cpns,tbdik.gambar,tbdik.nm_dik,b.nm_skpd,c.nm_unit,c.notes,d.nm_lok FROM bpaddtfake.dbo.emp_data as a
		// 				CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM  bpaddtfake.dbo.emp_gol,bpaddtfake.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
		// 				CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
		// 				CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpaddtfake.dbo.emp_dik,bpaddtfake.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
		// 				CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
		// 				,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
		// 				and tbunit.kd_unit like '$request->idunit' and ked_emp = 'aktif'") );
		// 	$findidjabatan = json_decode(json_encode($findidjabatan), true);

		// 	if (count($findidjabatan) > 0 ) {
		// 		return redirect('/kepegawaian/ubah pegawai?id_emp='.$request->noid)->with('message', 'Jabatan yang dipilih telah terisi. Silahkan pilih jabatan lain.');
		// 	}
		// }

		$splitidunit = explode("::", $request->idunit);
		$idunit = $splitidunit[0];
		$nmunit = $splitidunit[1];

		if (strlen($request->idunit) > 2) {
			$cutidunit = substr($request->idunit, 4, 2);
			if (substr($cutidunit, 0, 1) == '5') {
				$idlok = Glo_org_lokasi::where('new_kd_lok', $cutidunit)->first(['kd_lok']);
			} elseif (substr($cutidunit, 0, 1) == '0') {
				if ($cutidunit == '06') {
					$idlok = Glo_org_lokasi::where('new_kd_lok', $cutidunit)->first(['kd_lok']);
				} else if ($cutidunit == '07') {
					$idlok = Glo_org_lokasi::where('new_kd_lok', $cutidunit)->first(['kd_lok']);
				} else if ($cutidunit == '08') {
					$idlok = Glo_org_lokasi::where('new_kd_lok', $cutidunit)->first(['kd_lok']);
				} else {
					$idlok = Glo_org_lokasi::where('new_kd_lok', '0')->first(['kd_lok']);
				}
			}
		} else {
			$idlok = ['kd_lok' => '00'];
		}

		$insert_emp_jab = [
				// JABATAN
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'noid' => $request->noid,
				'tmt_jab' => (isset($request->tmt_jab) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_jab))) : null),
				'idskpd' => '1.20.512',
				'idunit' => $idunit,
				'idlok' => $idlok['kd_lok'],
				'tmt_sk_jab' => (isset($request->tmt_sk_jab) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_jab))) : null),
				'no_sk_jab' => ($request->no_sk_jab ? $request->no_sk_jab : ''),
				'jns_jab' => $request->jns_jab,
				'idjab' => $request->idjab,
				'eselon' => $request->eselon,
				'nmunit' => $nmunit,
				// 'tampilnew' => 1,
			];

		Emp_jab::insert($insert_emp_jab);
		Emp_jab_11::insert($insert_emp_jab);

		return redirect('/kepegawaian/ubah pegawai?id_emp='.$request->noid)
					->with('message', 'Data jabatan pegawai berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdatejabpegawai(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		// $jabatan = explode("||", $request->jabatan);
		// $jns_jab = $jabatan[0];
		// $idjab = $jabatan[1];

		if (strlen($request->idunit) > 2) {
			$cutidunit = substr($request->idunit, 4, 2);
			if (substr($cutidunit, 0, 1) == '5') {
				$idlok = Glo_org_lokasi::where('new_kd_lok', $cutidunit)->first(['kd_lok']);
			} elseif (substr($cutidunit, 0, 1) == '0') {
				if ($cutidunit == '06') {
					$idlok = Glo_org_lokasi::where('new_kd_lok', $cutidunit)->first(['kd_lok']);
				} else if ($cutidunit == '07') {
					$idlok = Glo_org_lokasi::where('new_kd_lok', $cutidunit)->first(['kd_lok']);
				} else if ($cutidunit == '08') {
					$idlok = Glo_org_lokasi::where('new_kd_lok', $cutidunit)->first(['kd_lok']);
				} else {
					$idlok = Glo_org_lokasi::where('new_kd_lok', '0')->first(['kd_lok']);
				}
			}
		} else {
			$idlok = ['kd_lok' => '00'];
		}

		$splitidunit = explode("::", $request->idunit);
		$idunit = $splitidunit[0];
		$nmunit = $splitidunit[1];

        $pegawai_jab_update = [
            'tmt_jab' => (isset($request->tmt_jab) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_jab))) : null),
            'idunit' => $idunit,
            'idlok' => $idlok['kd_lok'],
            'tmt_sk_jab' => (isset($request->tmt_sk_jab) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_jab))) : null),
            'no_sk_jab' => ($request->no_sk_jab ? $request->no_sk_jab : ''),
            'jns_jab' => $request->jns_jab,
            'idjab' => $request->idjab,
            'eselon' => $request->eselon,
            'nmunit' => $nmunit,
            // 'tampilnew' => 1,
        ];

		Emp_jab::where('noid', $request->noid)
			->where('ids', $request->ids)
			->update($pegawai_jab_update);
		Emp_jab_11::where('noid', $request->noid)
			->where('ids', $request->ids)
			->update($pegawai_jab_update);

		return redirect('/kepegawaian/ubah pegawai?id_emp='.$request->noid)
					->with('message', 'Data jabatan pegawai berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletejabpegawai(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		$cekcountjab = Emp_jab::where('noid', $request->noid)->where('sts', 1)->count();
		if ($cekcountjab == 1) {
			return redirect('/kepegawaian/ubah pegawai?id_emp='.$request->noid)->with('message', 'Tidak dapat menghapus habis jabatan pegawai. Buat jabatan baru lalu hapus yang lama.');
		}

		Emp_jab::where('noid', $request->noid)
			->where('ids', $request->ids)
			->update([
				'sts' => 0,
			]);
		Emp_jab_11::where('noid', $request->noid)
			->where('ids', $request->ids)
			->update([
				'sts' => 0,
			]);

		return redirect('/kepegawaian/ubah pegawai?id_emp='.$request->noid)
					->with('message', 'Data jabatan '.$request->idjab.' berhasil dihapus')
					->with('msg_num', 1);
	}

	// ------------------ DATA PEGAWAI ------------------ //

	// --------------- STRUKTUR ORGANISASI --------------- //

	public function strukturorganisasi()
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		$employees = DB::select( DB::raw("  
						SELECT id_emp, nm_emp, foto, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.sao, tbunit.nm_unit from bpaddtfake.dbo.emp_data as a
						CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM  bpaddtfake.dbo.emp_gol,bpaddtfake.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
						CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
						CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpaddtfake.dbo.emp_dik,bpaddtfake.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
						CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
						,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
						and idunit like '01%' AND LEN(idunit) < 10 AND ked_emp = 'AKTIF'
						ORDER BY idunit ASC, idjab ASC") );
		$employees = json_decode(json_encode($employees), true);

		return view('pages.bpadkepegawaian.struktur')
				->with('employees', $employees);
	}

	// --------------- STRUKTUR ORGANISASI --------------- //

	// ---------------- STATUS DISPOSISI ---------------- //

	public function statusdisposisi()
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		$ids = Auth::user()->id_emp;
	
		if ($ids) {
			$data_self = DB::select( DB::raw("  
								SELECT a.id_emp, a.nrk_emp, a.nip_emp, a.nm_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit, tbunit.notes, d.nm_lok, notread.notread, yesread.yesread, lanjut.lanjut from bpaddtfake.dbo.emp_data as a
								CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
								CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
								CROSS APPLY (
									select  count(disp.rd) as 'notread' from bpaddtfake.dbo.fr_disposisi disp
									  where rd = 'N' and sts = 1
									  and disp.to_pm = a.id_emp) notread
								CROSS APPLY (
									select  count(disp.rd) as 'yesread' from bpaddtfake.dbo.fr_disposisi disp
									  where rd = 'Y' and sts = 1
									  and disp.to_pm = a.id_emp) yesread
								CROSS APPLY (
									select  count(disp.rd) as 'lanjut' from bpaddtfake.dbo.fr_disposisi disp
									  where rd = 'S' and sts = 1
									  and disp.to_pm = a.id_emp) lanjut
								,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
								and id_emp like '$ids'
								") )[0];
			$data_self = json_decode(json_encode($data_self), true);
		} else {
			$data_self = DB::select( DB::raw("  SELECT a.id_emp, a.nrk_emp, a.nip_emp, a.nm_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit, tbunit.notes, d.nm_lok, notread.notread, yesread.yesread, lanjut.lanjut from bpaddtfake.dbo.emp_data as a
								CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
								CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
								CROSS APPLY (
									select  count(disp.rd) as 'notread' from bpaddtfake.dbo.fr_disposisi disp
									  where rd = 'N' and sts = 1
									  and disp.to_pm = a.id_emp) notread
								CROSS APPLY (
									select  count(disp.rd) as 'yesread' from bpaddtfake.dbo.fr_disposisi disp
									  where rd = 'Y' and sts = 1
									  and disp.to_pm = a.id_emp) yesread
								CROSS APPLY (
									select  count(disp.rd) as 'lanjut' from bpaddtfake.dbo.fr_disposisi disp
									  where rd = 'S' and sts = 1
									  and disp.to_pm = a.id_emp) lanjut
								,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
								and idunit like '01' and ked_emp = 'aktif'
								") )[0];
			$data_self = json_decode(json_encode($data_self), true);
		}		

		$result = '';

		if (strlen($data_self['idunit']) < 10) {
			$result .= '<strong>';
		}

		$result .= '<tr '.(strlen($data_self['idunit']) < 10 ? 'style="font-weight:bold"' : '' ).'>
						<td>'.$data_self['id_emp'].'</td>
						<td>'.(is_null($data_self['nrk_emp']) || $data_self['nrk_emp'] == '' ? '-' : $data_self['nrk_emp'] ).'</td>
						<td>'.ucwords(strtolower($data_self['nm_emp'])).'</td>
						<td>'.ucwords($data_self['notes']).'</td>
					';	
		$total = $data_self['notread'] + $data_self['yesread'] + $data_self['lanjut'];
		$result .= '	<td '. ($data_self['notread'] > 0 ? 'class="text-danger"' : '') .'>'.$data_self['notread'].'</td>
						<td>'.$data_self['yesread'].'</td>
						<td>'.$data_self['lanjut'].'</td>
						<td><b>'.$total.'</b></td>
					</tr>';

		if (strlen($data_self['idunit']) < 10) {
			$result .= '</strong>';
		}

		$nowunit = $data_self['idunit'];

		$data_stafs = DB::select( DB::raw("  SELECT a.id_emp, a.nrk_emp, a.nip_emp, a.nm_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit, tbunit.notes, d.nm_lok, notread.notread, yesread.yesread, lanjut.lanjut from bpaddtfake.dbo.emp_data as a
							CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
							CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
							CROSS APPLY (
								select  count(disp.rd) as 'notread' from bpaddtfake.dbo.fr_disposisi disp
								  where rd = 'N' and sts = 1
								  and disp.to_pm = a.id_emp) notread
							CROSS APPLY (
								select  count(disp.rd) as 'yesread' from bpaddtfake.dbo.fr_disposisi disp
								  where rd = 'Y' and sts = 1
								  and disp.to_pm = a.id_emp) yesread
							CROSS APPLY (
								select  count(disp.rd) as 'lanjut' from bpaddtfake.dbo.fr_disposisi disp
								  where rd = 'S' and sts = 1
								  and disp.to_pm = a.id_emp) lanjut
							,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
							and tbunit.sao like '$nowunit%' and ked_emp = 'aktif'
							order by idunit asc, nm_emp asc
							") );
		$data_stafs = json_decode(json_encode($data_stafs), true);

		foreach ($data_stafs as $key => $staf) {
			$result .= '<tr '.(strlen($staf['idunit']) < 10 ? 'style="font-weight:bold"' : '' ).'>
							<td>'.$staf['id_emp'].'</td>
							<td>'.(is_null($staf['nrk_emp']) || $staf['nrk_emp'] == '' ? '-' : $staf['nrk_emp'] ).'</td>
							<td>'.ucwords(strtolower($staf['nm_emp'])).'</td>
							<td>'.ucwords($staf['notes']).'</td>
					';	
			$total = $staf['notread'] + $staf['yesread'] + $staf['lanjut'];
			$result .= '	<td '. ($staf['notread'] > 0 ? 'class="text-danger"' : '') .'>'.$staf['notread'].'</td>
							<td>'.$staf['yesread'].'</td>
							<td>'.$staf['lanjut'].'</td>
							<td><b>'.$total.'</b></td>
						</tr>';
		}

		return view('pages.bpadkepegawaian.statusdisposisi')
				->with('access', $access)
				->with('result', $result);

		if (strlen($data_self['idunit']) == 10) {
			// kalo dia staf
			$result = '';

			$result .= '<tr>
							<td>'.(is_null($data_self['nrk_emp']) || $data_self['nrk_emp'] == '' ? '-' : $data_self['nrk_emp'] ).'</td>
							<td>'.ucwords(strtolower($data_self['nm_emp'])).'</td>
							<td>'.ucwords(strtolower($data_self['nm_unit'])).'</td>
						';

			$belum = json_decode(json_encode(DB::select( DB::raw("
						SELECT Count(id_emp) as belum
						FROM bpaddtfake.dbo.v_disposisi
						where id_emp like '".$ids."'
						and rd = 'N'
					"))[0]), true);

			$baca = json_decode(json_encode(DB::select( DB::raw("
						SELECT Count(id_emp) as baca
						FROM bpaddtfake.dbo.v_disposisi
						where id_emp like '".$ids."'
						and rd = 'Y'
					"))[0]), true);

			$balas = json_decode(json_encode(DB::select( DB::raw("
						SELECT Count(id_emp) as balas
						FROM bpaddtfake.dbo.v_disposisi
						where id_emp like '".$ids."'
						and rd = 'S'
					"))[0]), true);

			$total = $belum['belum'] + $baca['baca'] + $balas['balas'];
			
			$result .= '	<td '. ($belum['belum'] > 0 ? 'class="text-danger"' : '') .'>'.$belum['belum'].'</td>
								<td>'.$baca['baca'].'</td>
								<td>'.$balas['balas'].'</td>
								<td><b>'.$total.'</b></td>
							</tr>';

		} elseif (strlen($data_self['idunit']) == 2) {
			// kalo dia kepala badan
			$result = '<tr>
							<td>'.(is_null($data_self['nrk_emp']) || $data_self['nrk_emp'] == '' ? '-' : $data_self['nrk_emp'] ).'</td>
							<td>'.ucwords(strtolower($data_self['nm_emp'])).'</td>
							<td>'.ucwords(strtolower($data_self['nm_unit'])).'</td>
						';

			$belum = json_decode(json_encode(DB::select( DB::raw("
						SELECT Count(id_emp) as belum
						FROM bpaddtfake.dbo.v_disposisi
						where id_emp like '".$data_self['id_emp']."'
						and rd = 'N'
					"))[0]), true);

			$baca = json_decode(json_encode(DB::select( DB::raw("
						SELECT Count(id_emp) as baca
						FROM bpaddtfake.dbo.v_disposisi
						where id_emp like '".$data_self['id_emp']."'
						and rd = 'Y'
					"))[0]), true);

			$balas = json_decode(json_encode(DB::select( DB::raw("
						SELECT Count(id_emp) as balas
						FROM bpaddtfake.dbo.v_disposisi
						where id_emp like '".$data_self['id_emp']."'
						and rd = 'S'
					"))[0]), true);

			$total = $belum['belum'] + $baca['baca'] + $balas['balas'];

			$result .= '	<td '. ($belum['belum'] > 0 ? 'class="text-danger"' : '') .'>'.$belum['belum'].'</td>
								<td>'.$baca['baca'].'</td>
								<td>'.$balas['balas'].'</td>
								<td><b>'.$total.'</b></td>
							</tr>';

			$idunit = $data_self['idunit'];
			$querys = DB::select( DB::raw("  
						SELECT id_emp, nrk_emp, nip_emp, nm_emp, a.idgroup, tgl_lahir, jnkel_emp, tgl_join, status_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit from bpaddtfake.dbo.emp_data as a
						CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
						CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
						,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
						and tbunit.sao like '$idunit%' and ked_emp = 'aktif'
						order by tbunit.kd_unit") );
			$querys = json_decode(json_encode($querys), true);

			foreach ($querys as $key => $query) {
				$result .= '<tr>
								<td>'.(is_null($query['nrk_emp']) || $query['nrk_emp'] == '' ? '-' : $query['nrk_emp'] ).'</td>
								<td>'.ucwords(strtolower($query['nm_emp'])).'</td>
								<td>'.ucwords(strtolower($query['nm_unit'])).'</td>
							';

				$belum = json_decode(json_encode(DB::select( DB::raw("
							SELECT Count(id_emp) as belum
							FROM bpaddtfake.dbo.v_disposisi
							where id_emp like '".$query['id_emp']."'
							and rd = 'N'
						"))[0]), true);

				$baca = json_decode(json_encode(DB::select( DB::raw("
							SELECT Count(id_emp) as baca
							FROM bpaddtfake.dbo.v_disposisi
							where id_emp like '".$query['id_emp']."'
							and rd = 'Y'
						"))[0]), true);

				$balas = json_decode(json_encode(DB::select( DB::raw("
							SELECT Count(id_emp) as balas
							FROM bpaddtfake.dbo.v_disposisi
							where id_emp like '".$query['id_emp']."'
							and rd = 'S'
						"))[0]), true);

				$total = $belum['belum'] + $baca['baca'] + $balas['balas'];
				
				$result .= '	<td '. ($belum['belum'] > 0 ? 'class="text-danger"' : '') .'>'.$belum['belum'].'</td>
								<td>'.$baca['baca'].'</td>
								<td>'.$balas['balas'].'</td>
								<td><b>'.$total.'</b></td>
							</tr>';
			}
		} else {
			// kalo dia atasan biasa
			$result = '<tr>
							<td>'.(is_null($data_self['nrk_emp']) || $data_self['nrk_emp'] == '' ? '-' : $data_self['nrk_emp'] ).'</td>
							<td>'.ucwords(strtolower($data_self['nm_emp'])).'</td>
							<td>'.ucwords(strtolower($data_self['nm_unit'])).'</td>
						';

			$belum = json_decode(json_encode(DB::select( DB::raw("
						SELECT Count(id_emp) as belum
						FROM bpaddtfake.dbo.v_disposisi
						where id_emp like '".$ids."'
						and rd = 'N'
					"))[0]), true);

			$baca = json_decode(json_encode(DB::select( DB::raw("
						SELECT Count(id_emp) as baca
						FROM bpaddtfake.dbo.v_disposisi
						where id_emp like '".$ids."'
						and rd = 'Y'
					"))[0]), true);

			$balas = json_decode(json_encode(DB::select( DB::raw("
						SELECT Count(id_emp) as balas
						FROM bpaddtfake.dbo.v_disposisi
						where id_emp like '".$ids."'
						and rd = 'S'
					"))[0]), true);

			$total = $belum['belum'] + $baca['baca'] + $balas['balas'];

			$result .= '	<td '. ($belum['belum'] > 0 ? 'class="text-danger"' : '') .'>'.$belum['belum'].'</td>
								<td>'.$baca['baca'].'</td>
								<td>'.$balas['balas'].'</td>
								<td><b>'.$total.'</b></td>
							</tr>';

			$idunit = $data_self['idunit'];
			$querys = DB::select( DB::raw("  
						SELECT id_emp, nrk_emp, nip_emp, nm_emp, a.idgroup, tgl_lahir, jnkel_emp, tgl_join, status_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit from bpaddtfake.dbo.emp_data as a
						CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
						CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
						,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
						and tbunit.sao like '$idunit%' and ked_emp = 'aktif'
						order by tbunit.kd_unit") );
			$querys = json_decode(json_encode($querys), true);

			foreach ($querys as $key => $query) {
				$result .= '<tr>
								<td>'.(is_null($query['nrk_emp']) || $query['nrk_emp'] == '' ? '-' : $query['nrk_emp'] ).'</td>
								<td>'.ucwords(strtolower($query['nm_emp'])).'</td>
								<td>'.ucwords(strtolower($query['nm_unit'])).'</td>
							';

				$belum = json_decode(json_encode(DB::select( DB::raw("
							SELECT Count(id_emp) as belum
							FROM bpaddtfake.dbo.v_disposisi
							where id_emp like '".$query['id_emp']."'
							and rd = 'N'
						"))[0]), true);

				$baca = json_decode(json_encode(DB::select( DB::raw("
							SELECT Count(id_emp) as baca
							FROM bpaddtfake.dbo.v_disposisi
							where id_emp like '".$query['id_emp']."'
							and rd = 'Y'
						"))[0]), true);

				$balas = json_decode(json_encode(DB::select( DB::raw("
							SELECT Count(id_emp) as balas
							FROM bpaddtfake.dbo.v_disposisi
							where id_emp like '".$query['id_emp']."'
							and rd = 'S'
						"))[0]), true);

				$total = $belum['belum'] + $baca['baca'] + $balas['balas'];
				
				$result .= '	<td '. ($belum['belum'] > 0 ? 'class="text-danger"' : '') .'>'.$belum['belum'].'</td>
								<td>'.$baca['baca'].'</td>
								<td>'.$balas['balas'].'</td>
								<td><b>'.$total.'</b></td>
							</tr>';
			}
		}	
	}

	public function suratkeluar(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		$units = glo_org_unitkerja::whereRaw('LEN(kd_unit) = 6')
				->orderBy('kd_unit')
				->get();

				
		if (is_null($request->unit)) {
			if (Auth::user()->id_emp) {
				if(strlen($_SESSION['user_data']['idunit']) > 6) {
					$idunit = substr($_SESSION['user_data']['idunit'], 0, 6);
				} else {
					$idunit = $_SESSION['user_data']['idunit'];
				}
			} else {
				$idunit = '01';
			}
		} else {
			$idunit = $request->unit;
		}

		$surats = Fr_suratkeluar::
					where('unit', $idunit)
					->where('sts', 1)
					->orderBy('tgl_input', 'desc')
					->get();

		return view('pages.bpadkepegawaian.suratkeluar')
				->with('access', $access)
				->with('idunit', $idunit)
				->with('units', $units)
				->with('surats', $surats);
	}

	public function suratkeluartambah()
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		if (Auth::user()->id_emp) {
			if(strlen($_SESSION['user_data']['idunit']) > 6) {
				$idunit = substr($_SESSION['user_data']['idunit'], 0, 6);
			} else {
				$idunit = $_SESSION['user_data']['idunit'];
			}
		} else {
			$idunit = '01';
		}

		$units = glo_org_unitkerja::whereRaw('LEN(kd_unit) = 6')
				->orderBy('kd_unit')
				->get();

		$disposisis = Fr_disposisi::
						limit(200)
						->whereNotNull('kode_disposisi')
						->Where('kode_disposisi', '<>', '')
						->orderBy('no_form', 'desc')
						->get();

		$dispkodes = Glo_disposisi_kode::orderBy('kd_jnssurat')->get();

		return view('pages.bpadkepegawaian.suratkeluartambah')
				->with('disposisis', $disposisis)
				->with('dispkodes', $dispkodes)
				->with('idunit', $idunit)
				->with('units', $units);
	}

	public function suratkeluarubah(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		$units = glo_org_unitkerja::whereRaw('LEN(kd_unit) = 6')
				->orderBy('kd_unit')
				->get();

		$surat = Fr_suratkeluar::
					where('ids', $request->ids)
					->first();

		// $disposisis = Fr_disposisi::
		// 				limit(200)
		// 				->whereNotNull('kode_disposisi')
		// 				->Where('kode_disposisi', '<>', '')
		// 				->orderBy('no_form', 'desc')
		// 				->get();

		$dispkodes = Glo_disposisi_kode::orderBy('kd_jnssurat')->get();

		return view('pages.bpadkepegawaian.suratkeluarubah')
				->with('surat', $surat)
				->with('units', $units)
				// ->with('idunit', $idunit)
				// ->with('disposisis', $disposisis)
				->with('dispkodes', $dispkodes);
	}

	public function forminsertsuratkeluar(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();
		$accessid = $this->checkAccess($_SESSION['user_data']['idgroup'], 1375);

		// $maxnoform = Fr_suratkeluar::max('no_form');
		// if (is_null($maxnoform)) {
		// 	$newnoform = '1.20.512.20200001';
		// } else {
		// 	$splitnoform = explode(".", $maxnoform); 
		// 	$newnoform = $splitnoform[0] . "." . $splitnoform[1] . "." . $splitnoform[2] . "." . ($splitnoform[3]+1);
		// }

		$randomletter = substr(str_shuffle("123456789ABCDEFGHIJKLMNPQRSTUVWXYZ"), 0, 9);
		// $randomletter .= substr(($newnoform[3]), -3);

		$filesuratkeluar = '';

		if (isset($request->nm_file)) {
			$file = $request->nm_file;

			if ($file->getSize() > 2222222) {
				return redirect('/kepegawaian/surat keluar tambah')->with('message', 'Ukuran file terlalu besar (Maksimal 2MB)');     
			}

            if ($file->getClientOriginalExtension() != "pdf") {
				return redirect('/kepegawaian/surat keluar tambah')->with('message', 'File yang diunggah harus berbentuk PDF');     
			} 

			$filesuratkeluar .= date('dmYHis') . '_' . $randomletter . '_SURATKELUAR.' . $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefilesuratkeluar');
			$file->move($tujuan_upload, $filesuratkeluar);
		}
			
		if (!(isset($filesuratkeluar))) {
			$filesuratkeluar = '';
		}

		$insertsurat = [
			'sts' => 1,
			'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
			'tgl'       => date('Y-m-d H:i:s'),
			'ip'        => '',
			'logbuat'   => '',
			'kd_skpd' => '1.20.512',
			'kd_unit' => '01',
			'no_form' => $randomletter,
			// 'tgl_terima' => (isset($request->tgl_terima) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_terima))) : null),
			'usr_input' => (isset(Auth::user()->usname) ? Auth::user()->usname : Auth::user()->id_emp),
			'tgl_input' => date('Y-m-d H:i:s'),
			'kode_disposisi' => $request->kode_disposisi,
			'perihal' => ($request->perihal ? $request->perihal : ''),
			'tgl_surat' => ($request->tgl_surat ? $request->tgl_surat : ''),
			'no_surat' => ($request->no_surat ? $request->no_surat : ''),
			'asal_surat' => ($request->asal_surat ? $request->asal_surat : ''),
			'ket_lain' => ($request->ket_lain ? $request->ket_lain : ''),
			'nm_file' => $filesuratkeluar,
			'kepada' => ($request->kepada ? $request->kepada : ''),
			// 'no_form_in' => $request->no_form_in,
			'unit' => $request->unit,
		];

		Fr_suratkeluar::insert($insertsurat);

		return redirect('/kepegawaian/surat keluar')
				->with('message', 'Surat Keluar berhasil dibuat')
				->with('msg_num', 1);
	}

	public function formupdatesuratkeluar(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

        $randomletter = $request->no_form;

		$filesuratkeluar = '';

		if (isset($request->nm_file)) {
			$file = $request->nm_file;

			if ($file->getSize() > 2222222) {
				return redirect('/kepegawaian/surat keluar tambah')->with('message', 'Ukuran file terlalu besar (Maksimal 2MB)');     
			} 

            if ($file->getClientOriginalExtension() != "pdf") {
				return redirect('/kepegawaian/surat keluar tambah')->with('message', 'File yang diunggah harus berbentuk PDF');     
			} 

			$filesuratkeluar .= date('dmYHis') . '_' . $randomletter . '_SURATKELUAR.' . $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefilesuratkeluar');
			$file->move($tujuan_upload, $filesuratkeluar);
		}
			
		if (!(isset($filesuratkeluar))) {
			$filesuratkeluar = '';
		}

		Fr_suratkeluar::where('ids', $request->ids)
						->update([
							// 'tgl_terima' => date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_terima))),
							'kode_disposisi' => $request->kode_disposisi,
							'perihal' => ($request->perihal ? $request->perihal : ''),
							'tgl_surat' => ($request->tgl_surat ? $request->tgl_surat : ''),
							'no_surat' => ($request->no_surat ? $request->no_surat : ''),
							'asal_surat' => ($request->asal_surat ? $request->asal_surat : ''),
							'ket_lain' => ($request->ket_lain ? $request->ket_lain : ''),
							'kepada' => ($request->kepada ? $request->kepada : ''),
							// 'no_form_in' => $request->no_form_in,
							'unit' => $request->unit,

						]);
		
		if ($filesuratkeluar != '') {
			Fr_suratkeluar::where('ids', $request->ids)
			->update([
				'nm_file' => $filesuratkeluar,
			]);
		}

		return redirect('/kepegawaian/surat keluar')
					->with('message', 'Surat Keluar berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletesuratkeluar(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		$nowsurat = Fr_suratkeluar::
					where('ids', $request->ids)
					->first();

		if (Auth::user()->id_emp) {
			if(strlen($_SESSION['user_data']['idunit']) > 6) {
				$idunit = substr($_SESSION['user_data']['idunit'], 0, 6);
			} elseif(strlen($_SESSION['user_data']['idunit']) > 6){
				$idunit = '010101';
			} else {
				$idunit = $_SESSION['user_data']['idunit'];
			}
			
			if($nowsurat['unit'] != $idunit) {
				return redirect('/kepegawaian/surat keluar')->with('message', 'Anda tidak dapat menghapus surat ini');     
			}
		}

		$filepath = '';
		$filepath .= config('app.savefilesuratkeluar');
		$filepath .= '/' . $request->nm_file;

		// Fr_suratkeluar::
		// 		where('ids', $request->ids)
		// 		->delete();

		Fr_suratkeluar::where('ids', $request->ids)
				->update([
					'sts' => 0,
				]);

		// if ($request->nm_file) {
		// 	unlink($filepath);
		// }

		return redirect('/kepegawaian/surat keluar')
					->with('message', 'Surat Keluar berhasil dihapus')
					->with('msg_num', 1);
	}

	// ---------------- SURAT KELUAR ---------------- //

	// -------------------- EKINERJA -------------------- //

	public function entrikinerja(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		$idemp = Auth::user()->id_emp;

        $laporans = V_kinerja::
                    where('idemp', $idemp)
                    ->whereNull('stat')
                    ->orderBy('tgl_trans', 'desc')
                    ->get();

		return view('pages.bpadkepegawaian.kinerjaentri')
				->with('access', $access)
				->with('laporans', $laporans);
	}

	public function kinerjatambah(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		date_default_timezone_set('Asia/Jakarta');

		if ($request->now_tgl_trans) {
			$now_tgl_trans = date('d/m/Y', strtotime(str_replace('/', '-', $request->now_tgl_trans)));
		} else {
			$now_tgl_trans = date('d/m/Y', strtotime(str_replace('/', '-', now('Asia/Jakarta'))));
		}

		if ($request->now_tipe_hadir) {
			$now_tipe_hadir = $request->now_tipe_hadir;
		} else {
			$now_tipe_hadir = 1;
		}

		if ($request->now_jns_hadir) {
			$now_jns_hadir = $request->now_jns_hadir;
		} else {
			$now_jns_hadir = 'Tepat Waktu (8,5 jam/hari)';
		}

		if ($request->now_lainnya) {
			$now_lainnya = $request->now_lainnya;
		} else {
			$now_lainnya = '';
		}

		return view('pages.bpadkepegawaian.kinerjatambah')
				->with('now_tgl_trans', $now_tgl_trans)
				->with('now_tipe_hadir', $now_tipe_hadir)
				->with('now_jns_hadir', $now_jns_hadir)
				->with('now_lainnya', $now_lainnya);
	}

	public function getaktivitas()
	{
		$idemp = Auth::user()->id_emp;
		// $query = DB::select( DB::raw("
		// 			SELECT a.sts as data_sts, a.tgl as data_tgl, a.idemp as data_idemp, a.tgl_trans as data_tgl_trans, tipe_hadir, jns_hadir, lainnya, stat, tipe_hadir_app, jns_hadir_app, catatan_app,
		// 					b.sts as detail_sts, b.tgl as detail_sts, b.idemp as detail_idemp, b.tgl_trans as detail_tgl_trans, time1, time2, uraian, keterangan
		// 			from bpaddtfake.dbo.kinerja_data a
		// 			join bpaddtfake.dbo.kinerja_detail b on b.idemp = a.idemp
		// 			where b.idemp = '$idemp' 
		// 			and a.tgl_trans = b.tgl_trans
		// 			and a.stat is null
		// 			order by b.tgl_trans desc, time1 asc
		// 			"));
		$query = DB::select( DB::raw("
					SELECT *
					from bpaddtfake.dbo.v_kinerja
					where idemp = '$idemp'
					and stat is null
					order by tgl_trans desc, time1, time2
					"));
		$query = json_decode(json_encode($query), true);

		return $query;
	}

	public function formcekjadwalaktivitas(Request $request)
	{
		$idemp = Auth::user()->id_emp;

		$returnthis = 0;

		$cekappr = DB::select( DB::raw("
					  SELECT count(sts) as total
					  from bpaddtfake.dbo.kinerja_data
					  where stat = 1
					  and idemp = '$idemp'
					  and sts = 1
					  and tgl_trans = '$request->tgltrans'
						"))[0];
		$cekappr = json_decode(json_encode($cekappr), true);

		if ($cekappr['total'] > 0) {
			// tandanya jadwal udah di approved
			$returnthis = 2;
		}

		// $total = DB::select( DB::raw("
		// 			SELECT count(sts) as total from bpaddtfake.dbo.kinerja_detail
		// 			where tgl_trans = '$request->tgltrans'
		// 			and idemp = '$idemp'
		// 			and (time1 <= '$request->time1' and time2 >= '$request->time2')
		// 				"))[0];
		// $total = json_decode(json_encode($total), true);

		// if ($total['total'] > 0) {
		// 	// tandanya ada jadwal yg crash
		// 	$returnthis = 1;
		// }

		return $returnthis;

	}

	public function forminsertkinerja(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		$idemp = Auth::user()->id_emp;
		$splittgltrans = explode("/", $request->tgl_trans);
		$tgl_trans = $splittgltrans[2] . "-" . $splittgltrans[1] . "-" . $splittgltrans[0];

		$cekkinerja = DB::select( DB::raw("
						SELECT *
						from bpaddtfake.dbo.kinerja_data
						where idemp = '$idemp'
						and tgl_trans = '$tgl_trans'
						"));
		$cekkinerja = json_decode(json_encode($cekkinerja), true);

		if ($request->tipe_hadir == 2) {
			Kinerja_detail::	
				where('idemp', $idemp)
				->where('tgl_trans', $tgl_trans)
				->delete();
		}

		if (count($cekkinerja) == 0) {
			Kinerja_data::insert([
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'idemp' => Auth::user()->id_emp,
				'tgl_trans' => $tgl_trans,
				'tipe_hadir' => $request->tipe_hadir,
				'jns_hadir' => $request->jns_hadir,
				'lainnya' => ($request->lainnya ? $request->lainnya : ''),
				'stat' => null,
				'tipe_hadir_app' => null,
				'jns_hadir_app' => null,
				'catatan_app' => null,
			]);
		} else {
			Kinerja_data::
				where('idemp', $idemp)
				->where('tgl_trans', $tgl_trans)
				->update([
					'sts' => 1,
					'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
					'tgl'       => date('Y-m-d H:i:s'),
					'ip'        => '',
					'logbuat'   => '',
					'idemp' => Auth::user()->id_emp,
					'tgl_trans' => $tgl_trans,
					'tipe_hadir' => $request->tipe_hadir,
					'jns_hadir' => $request->jns_hadir,
					'lainnya' => ($request->lainnya ? $request->lainnya : ''),
					'stat' => null,
					'tipe_hadir_app' => null,
					'jns_hadir_app' => null,
					'catatan_app' => null,
				]);
		}

		return redirect('/kepegawaian/kinerja tambah');
	}

	public function formdeletekinerja(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		Kinerja_data::	
			where('idemp', $request->idemp)
			->where('tgl_trans', $request->tgl_trans)
			->delete();

		Kinerja_detail::	
			where('idemp', $request->idemp)
			->where('tgl_trans', $request->tgl_trans)
			->delete();

		return redirect('/kepegawaian/entri kinerja')
					->with('message', 'Data kinerja berhasil dihapus')
					->with('msg_num', 1);
	}

	public function forminsertaktivitas(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		
		$idemp = Auth::user()->id_emp;
		$tgl_trans = date("Y-m-d", strtotime(str_replace('/', '-', $request->tgl_trans)));

		$cekaktivitas = DB::select( DB::raw("
						SELECT *
						from bpaddtfake.dbo.kinerja_data
						where idemp = '$idemp'
						and tgl_trans = '$tgl_trans'
						"));
		$cekaktivitas = json_decode(json_encode($cekaktivitas), true);

		if (count($cekaktivitas) == 0) {

			$insertkinerja = [
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'idemp' => Auth::user()->id_emp,
				'tgl_trans' => $tgl_trans,
				'tipe_hadir' => $request->tipe_hadir,
				'jns_hadir' => $request->jns_hadir,
				'lainnya' => ($request->lainnya ? $request->lainnya : ''),
				'stat' => null,
				'tipe_hadir_app' => null,
				'jns_hadir_app' => null,
				'catatan_app' => null,
			];
			Kinerja_data::insert($insertkinerja);
		}

		if ($request->keterangan) {
			$keterangan = $request->keterangan;
		} else {
			$keterangan = '-';
		}

		$insertaktivitas = [
			'sts' => 1,
			'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
			'tgl'       => date('Y-m-d H:i:s'),
			'ip'        => '',
			'logbuat'   => '',
			'idemp' => Auth::user()->id_emp,
			'tgl_trans' => $tgl_trans,
			'time1' => $request->time1,
			'time2' => $request->time2,
			'uraian' => $request->uraian,
			'keterangan' => $keterangan,
		];

		if (Kinerja_detail::insert($insertaktivitas)) {
			// $query = DB::select( DB::raw("
			// 			SELECT a.sts as data_sts, a.tgl as data_tgl, a.idemp as data_idemp, a.tgl_trans as data_tgl_trans, tipe_hadir, jns_hadir, lainnya, stat, tipe_hadir_app, jns_hadir_app, catatan_app,
			// 					b.sts as detail_sts, b.tgl as detail_sts, b.idemp as detail_idemp, b.tgl_trans as detail_tgl_trans, time1, time2, uraian, keterangan
			// 			from bpaddtfake.dbo.kinerja_data a
			// 			join bpaddtfake.dbo.kinerja_detail b on b.idemp = a.idemp
			// 			where b.idemp = '$idemp' 
			// 			and a.tgl_trans = b.tgl_trans
			// 			and a.stat is null
			// 			order by b.tgl_trans desc, time1 asc
			// 			"));
			$query = DB::select( DB::raw("
					SELECT *
					from bpaddtfake.dbo.v_kinerja
					where idemp = '$idemp'
					and stat is null
					order by tgl_trans desc, time1 desc
					"));
			$query = json_decode(json_encode($query), true);

			$body_append = '';
			$now_date = '';
			foreach ($query as $key => $data) {
				if ($data['tipe_hadir'] != 2 && $data['time1'] && $data['time2']) {
					$splittime1 = explode(":", $data['time1']);
					$time1 = $splittime1[0] . ":" . $splittime1[1];

					$splittime2 = explode(":", $data['time2']);
					$time2 = $splittime2[0] . ":" . $splittime2[1];

					$splitdate1 = explode(" ", $data['tgl_trans'])[0];
					$splitdate2 = explode("-", $splitdate1);
					$date = $splitdate2[2] . "-" . $splitdate2[1] . "-" . $splitdate2[0];

					if ($now_date != $data['tgl_trans']) {
						$now_date = $data['tgl_trans'];
						$body_append .= '<tr style="background-color: #f7fafc !important">
											<td colspan="5"><b>TANGGAL: '.$date.'</b></td>
										</tr>';
					}

					$body_append .= '<tr>
										<td>'.$time1.'</td>
										<td>'.$time2.'</td>
										<td>'.$data['uraian'].'</td>
										<td>'.$data['keterangan'].'</td>
										<td>
											<input id="idemp-'.$key.'" type="hidden" value="'.$idemp.'"></input>
											<input id="tgl_trans-'.$key.'" type="hidden" value="'.$data['tgl_trans'].'"></input>
											<input id="time1-'.$key.'" type="hidden" value="'.$data['time1'].'"></input>
											<button type="button" class="btn btn-danger btn-outline btn-circle m-r-5 btn_delete_aktivitas" id="'.$key.'"><i class="fa fa-trash"></i></button></td>
										</td>
									</tr>';
				}
			}
            return redirect('/kepegawaian/kinerja tambah')
                    ->with('message', 'Data kinerja berhasil ditambah. Silahkan buka menu LAPORAN KINERJA untuk melihat kinerja anda secara lengkap')
                    ->with('msg_num', 1);
			// return json_encode($body_append);
		} else {
			// return 0;
            return redirect('/kepegawaian/kinerja tambah')
                    ->with('message', 'Data kinerja gagal tersimpan')
                    ->with('msg_num', 2);
		}
	}

	public function formdeleteaktivitas(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}

		$idemp = $request->idemp;
		$tgl_trans = $request->tgltrans;
		$time1 = $request->time1;
		$uraian = $request->uraian;
		$keterangan = $request->keterangan;

		Kinerja_detail::	
			where('idemp', $idemp)
			->where('tgl_trans', $tgl_trans)
			->where('time1', $time1)
			->where('uraian', $uraian)
			->where('keterangan', $keterangan)
			->delete();

		// $query = DB::select( DB::raw("
		// 			SELECT a.sts as data_sts, a.tgl as data_tgl, a.idemp as data_idemp, a.tgl_trans as data_tgl_trans, tipe_hadir, jns_hadir, lainnya, stat, tipe_hadir_app, jns_hadir_app, catatan_app,
		// 					b.sts as detail_sts, b.tgl as detail_sts, b.idemp as detail_idemp, b.tgl_trans as detail_tgl_trans, time1, time2, uraian, keterangan
		// 			from bpaddtfake.dbo.kinerja_data a
		// 			join bpaddtfake.dbo.kinerja_detail b on b.idemp = a.idemp
		// 			where b.idemp = '$idemp' 
		// 			and a.tgl_trans = b.tgl_trans
		// 			and a.stat is null
		// 			order by b.tgl_trans desc, time1 asc
		// 			"));
		$query = DB::select( DB::raw("
					SELECT *
					from bpaddtfake.dbo.v_kinerja
					where idemp = '$idemp'
					and stat is null
                    order by tgl_trans desc, time1, time2
					"));
		$query = json_decode(json_encode($query), true);

		$body_append = '';
		$now_date = '';
		foreach ($query as $key => $data) {
			if ($data['tipe_hadir'] != 2 && $data['time1'] && $data['time2']) {
				$splittime1 = explode(":", $data['time1']);
				$time1 = $splittime1[0] . ":" . $splittime1[1];

				$splittime2 = explode(":", $data['time2']);
				$time2 = $splittime2[0] . ":" . $splittime2[1];

				$splitdate1 = explode(" ", $data['tgl_trans'])[0];
				$splitdate2 = explode("-", $splitdate1);
				$date = $splitdate2[2] . "-" . $splitdate2[1] . "-" . $splitdate2[0];

				if ($now_date != $data['tgl_trans']) {
					$now_date = $data['tgl_trans'];
					$body_append .= '<tr style="background-color: #f7fafc !important">
										<td colspan="5"><b>TANGGAL: '.$date.'</b></td>
									</tr>';
				}

				$body_append .= '<tr>
									<td>'.$time1.'</td>
									<td>'.$time2.'</td>
									<td>'.$data['uraian'].'</td>
									<td>'.$data['keterangan'].'</td>
									<td>
										<input id="idemp-'.$key.'" type="hidden" value="'.$idemp.'"></input>
										<input id="tgl_trans-'.$key.'" type="hidden" value="'.$data['tgl_trans'].'"></input>
										<input id="time1-'.$key.'" type="hidden" value="'.$data['time1'].'"></input>
										<button type="button" class="btn btn-danger btn-outline btn-circle m-r-5 btn_delete_aktivitas" id="'.$key.'"><i class="fa fa-trash"></i></button></td>
									</td>
								</tr>';
			}
		}
		return json_encode($body_append);
	}

	public function approvekinerja(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();
		// $currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		// $currentpath = explode("?", $currentpath)[0];
		// $thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		// $access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		if ($_SESSION['user_data']['idunit']) {
			$idunit = $_SESSION['user_data']['idunit'];
		} else {
			$idunit = '01';
		}

		$pegawais = DB::select( DB::raw("
					SELECT id_emp, nm_emp, nrk_emp FROM bpaddtfake.dbo.emp_data as a
					CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM  bpaddtfake.dbo.emp_gol,bpaddtfake.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
					CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
					CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpaddtfake.dbo.emp_dik,bpaddtfake.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
					CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
					,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
					and ked_emp = 'aktif' and tgl_end is null and tbunit.sao like '01%'
					order by nm_emp"));
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
					SELECT kinerja_data.*, emp_data.nm_emp					
					from bpaddtfake.dbo.kinerja_data
					join bpaddtfake.dbo.emp_data on emp_data.id_emp = kinerja_data.idemp
					where idemp = '$now_id_emp'
					and stat is null
					and YEAR(tgl_trans) = $now_year
					and MONTH(tgl_trans) = $now_month
					order by tgl_trans desc, nm_emp asc
					"));

		$laporans = json_decode(json_encode($laporans), true);

		return view('pages.bpadkepegawaian.kinerjaapprove')
				// ->with('access', $access)
				->with('laporans', $laporans)
				->with('pegawais', $pegawais)
				->with('now_month', $now_month)
				->with('now_year', $now_year)
				->with('now_id_emp', $now_id_emp);
	}

	public function formapprovekinerja(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		foreach ($request->laporan as $key => $data) {
			$idemp = $request->{'idemp_'.$data};
			Kinerja_data::
				where('idemp', $request->{'idemp_'.$data})
				->where('tgl_trans', $request->{'tgl_trans_'.$data})
				->update([
					'stat' => 1,
					'tipe_hadir_app' => $request->{'tipe_hadir_'.$data},
					'jns_hadir_app' => $request->{'jns_hadir_'.$data},
					'catatan_app' => '',
				]);
		}

		return redirect('/kepegawaian/approve kinerja?now_id_emp='.$idemp)
					->with('message', 'Data kinerja berhasil disetujui')
					->with('msg_num', 1);
	}

	public function getdetailaktivitas(Request $request)
	{
		$idemp = $request->idemp;
		$tgl_trans = $request->tgl_trans;
		// $query = DB::select( DB::raw("
		// 			SELECT a.sts as data_sts, a.tgl as data_tgl, a.idemp as data_idemp, a.tgl_trans as data_tgl_trans, tipe_hadir, jns_hadir, lainnya, stat, tipe_hadir_app, jns_hadir_app, catatan_app,
		// 					b.sts as detail_sts, b.tgl as detail_sts, b.idemp as detail_idemp, b.tgl_trans as detail_tgl_trans, time1, time2, uraian, keterangan
		// 			from bpaddtfake.dbo.kinerja_data a
		// 			join bpaddtfake.dbo.kinerja_detail b on b.idemp = a.idemp
		// 			where b.idemp = '$idemp' 
		// 			and a.tgl_trans = b.tgl_trans
		// 			and a.stat is null
		// 			order by b.tgl_trans desc, time1 asc
		// 			"));
		$query = DB::select( DB::raw("
					SELECT *
					from bpaddtfake.dbo.kinerja_detail
					where idemp = '$idemp'
					and tgl_trans = '$tgl_trans'
					order by time1
					"));
		$query = json_decode(json_encode($query), true);

		return $query;
	}

	public function formapprovekinerjasingle(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		$tgl_trans = date('Y-m-d',strtotime($request->tgl_trans));

		if ($request->catatan_app) {
			$catatan_app = $request->catatan_app;
		} else {
			$catatan_app = '-';
		}

		if ($request->lainnya) {
			Kinerja_data::
			where('idemp', $request->idemp)
			->where('tgl_trans', $tgl_trans)
			->update([
				'stat' => 1,
				'lainnya' => $request->lainnya,
				'tipe_hadir_app' => $request->tipe_hadir,
				'jns_hadir_app' => $request->jns_hadir,
				'catatan_app' => $catatan_app,
			]);
		} else {
			Kinerja_data::
			where('idemp', $request->idemp)
			->where('tgl_trans', $tgl_trans)
			->update([
				'stat' => 1,
				'lainnya' => '',
				'tipe_hadir_app' => $request->tipe_hadir,
				'jns_hadir_app' => $request->jns_hadir,
				'catatan_app' => $catatan_app,
			]);
		}

		return redirect('/kepegawaian/approve kinerja?now_id_emp='.$request->idemp)
					->with('message', 'Data kinerja berhasil disetujui')
					->with('msg_num', 1);
	}

	public function printexcel(Request $request)
	{
		$now_id = $request->id;
		$now_month = $request->month;
		$now_year = $request->year;
		$now_valid = $request->valid;

		$monthName = date('F', mktime(0, 0, 0, $now_month, 10)); // March

		$now_emp = DB::select( DB::raw("  
				SELECT id_emp, nrk_emp, nip_emp, nm_emp, a.idgroup as idgroup, tgl_lahir, jnkel_emp, tgl_join, status_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit, tbunit.sao, tbunit.notes from bpaddtfake.dbo.emp_data as a
				CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
				CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
				,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
				and id_emp like '$now_id' AND ked_emp = 'AKTIF'") )[0];
		$now_emp = json_decode(json_encode($now_emp), true);

		$sao_es4 = $now_emp['sao'];
		$now_es4 = DB::select( DB::raw("  
				SELECT id_emp, nrk_emp, nip_emp, nm_emp, a.idgroup as idgroup, tgl_lahir, jnkel_emp, tgl_join, status_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit, tbunit.sao, tbunit.notes from bpaddtfake.dbo.emp_data as a
				CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
				CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
				,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
				and idunit like '$sao_es4' AND ked_emp = 'AKTIF'") )[0];
		$now_es4 = json_decode(json_encode($now_es4), true);

		$sao_es3 = $now_es4['sao'];
		$now_es3 = DB::select( DB::raw("  
				SELECT id_emp, nrk_emp, nip_emp, nm_emp, a.idgroup as idgroup, tgl_lahir, jnkel_emp, tgl_join, status_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit, tbunit.sao, tbunit.notes from bpaddtfake.dbo.emp_data as a
				CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
				CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
				,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
				and idunit like '$sao_es3' AND ked_emp = 'AKTIF'") )[0];
		$now_es3 = json_decode(json_encode($now_es3), true);

		$laporans = DB::select( DB::raw("
					SELECT *
					from bpaddtfake.dbo.v_kinerja
					where idemp = '$now_id'
					and stat $now_valid
					and YEAR(tgl_trans) = $now_year
					and MONTH(tgl_trans) = $now_month
					ORDER BY tgl_trans, time1, time2
					"));
		$laporans = json_decode(json_encode($laporans), true);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->mergeCells('A1:E1');
		$sheet->setCellValue('A1', 'LAPORAN KINERJA');
		$sheet->getStyle('A1')->getFont()->setBold( true );
		$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

		$sheet->mergeCells('A2:E2');
		$sheet->setCellValue('A2', 'TENAGA AHLI '.$now_emp['nm_unit'].' BADAN PENGELOLAAN ASET DAERAH');
		$sheet->getStyle('A2')->getFont()->setBold( true );
		$sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

		$sheet->mergeCells('A3:E3');
		$sheet->setCellValue('A3', 'BADAN PENGELOLAAN ASET DAERAH (BPAD) PROVINSI DKI JAKARTA '.date('Y'));
		$sheet->getStyle('A3')->getFont()->setBold( true );
		$sheet->getStyle('A3')->getAlignment()->setHorizontal('center');

		$sheet->setCellValue('A5', 'Nama');
		$sheet->setCellValue('A6', 'Tempat Tugas');
		$sheet->setCellValue('A7', 'Periode');

		$sheet->setCellValue('C5', ': '.ucwords(strtolower($now_emp['nm_emp'])));
		$sheet->getStyle('C5')->getFont()->setBold( true );
		$sheet->setCellValue('C6', ': '.ucwords(strtolower($now_emp['nm_unit'])) . ' BPAD Provinsi DKI Jakarta');
		$sheet->setCellValue('C7', ': '.$monthName .' '. $now_year);		
		
		$styleArray = [
			'font' => [
				'size' => 12,
				'name' => 'Trebuchet MS',
			]
		];

		$sheet->getStyle('A1:E7')->applyFromArray($styleArray);

		$sheet->setCellValue('A9', 'TANGGAL');
		$sheet->setCellValue('B9', 'AWAL');
		$sheet->setCellValue('C9', 'AKHIR');
		$sheet->setCellValue('D9', 'URAIAN');
		$sheet->setCellValue('E9', 'KETERANGAN');

		$sheet->getStyle('A9')->getFont()->setBold( true );
		$sheet->getStyle('B9')->getFont()->setBold( true );
		$sheet->getStyle('C9')->getFont()->setBold( true );
		$sheet->getStyle('D9')->getFont()->setBold( true );
		$sheet->getStyle('E9')->getFont()->setBold( true );

		$sheet->getStyle('A9')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('B9')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('C9')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('D9')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('E9')->getAlignment()->setHorizontal('center');

		$nowdate = 0;
		$nowrow = 10;
		$rowstart = $nowrow - 1;
		foreach ($laporans as $key => $laporan) {
			if ($nowdate != $laporan['tgl_trans']) {
				$nowdate = $laporan['tgl_trans'];

				if ($now_valid == "= 1") {
					$jns_hadir = $laporan['jns_hadir_app'];
				} else {
					$jns_hadir = $laporan['jns_hadir'];
				}

				if ($laporan['jns_hadir_app'] == 'Lainnya (sebutkan)' || $laporan['jns_hadir'] == 'Lainnya (sebutkan)') {
					$lainnya = " --- " . $laporan['lainnya'];
				} else {
					$lainnya = "";
				}

				$sheet->mergeCells("A".$nowrow.":E".$nowrow);
				$sheet->setCellValue("A".$nowrow, date('D, d-M-Y',strtotime($laporan['tgl_trans'])) . " --- " . $jns_hadir);
				$sheet->getStyle('A'.$nowrow)->getFont()->setBold( true );

				$nowrow++;
			}

			if ($now_valid == "= 1") {
				if ($laporan['tipe_hadir_app'] != 2) {
					$sheet->setCellValue('A'.$nowrow, date('d-M-Y',strtotime($laporan['tgl_trans'])));
					$sheet->setCellValue('B'.$nowrow, date('H:i',strtotime($laporan['time1'])));
					$sheet->setCellValue('C'.$nowrow, date('H:i',strtotime($laporan['time2'])));
					$sheet->setCellValue('D'.$nowrow, $laporan['uraian']);
					$sheet->setCellValue('E'.$nowrow, $laporan['keterangan']);
					$nowrow++;
				}
			} else {
				if ($laporan['tipe_hadir'] != 2) {
					$sheet->setCellValue('A'.$nowrow, date('d-M-Y',strtotime($laporan['tgl_trans'])));
					$sheet->setCellValue('B'.$nowrow, date('H:i',strtotime($laporan['time1'])));
					$sheet->setCellValue('C'.$nowrow, date('H:i',strtotime($laporan['time2'])));
					$sheet->setCellValue('D'.$nowrow, $laporan['uraian']);
					$sheet->setCellValue('E'.$nowrow, $laporan['keterangan']);
					$nowrow++;
				}
			}
		}

		$rowend = $nowrow - 1;

		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setWidth(8);
		$sheet->getColumnDimension('C')->setWidth(8);
		$sheet->getColumnDimension('D')->setWidth(55);
		$sheet->getColumnDimension('E')->setWidth(30);

		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
			],
		];

		$sheet->getStyle('A'.$rowstart.':E'.$rowend)->applyFromArray($styleArray);

		$nowrow++;
		$sheet->setCellValue('E'.$nowrow, 'Jakarta, _________');

		$nowrow++;
		$rownext = $nowrow + 1;
		$sheet->mergeCells('A'.$nowrow.':C'.$rownext);
		$sheet->setCellValue('A'.$nowrow, strtoupper($now_es4['notes']));
		$sheet->getStyle('A'.$nowrow)->getAlignment()->setWrapText(true);
		$sheet->getStyle('A'.$nowrow)->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A'.$nowrow)->getAlignment()->setVertical('center');

		$nowrow++;
		$sheet->setCellValue('E'.$nowrow, 'TENAGA PENDAMPING');
		$sheet->getStyle('E'.$nowrow)->getAlignment()->setHorizontal('center');

		$nowrow = $nowrow + 4;
		$sheet->setCellValue('D'.$nowrow, 'Mengetahui:');
		$sheet->getStyle('D'.$nowrow)->getAlignment()->setHorizontal('center');

		$nowrow++;
		$sheet->mergeCells('A'.$nowrow.':C'.$nowrow);
		$sheet->setCellValue('A'.$nowrow, strtoupper($now_es4['nm_emp']));
		$sheet->getStyle('A'.$nowrow)->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A'.$nowrow)->getFont()->setBold( true );
		//-----//
		$rownext = $nowrow + 1;
		$sheet->mergeCells('D'.$nowrow.':D'.$rownext);
		$sheet->setCellValue('D'.$nowrow, strtoupper($now_es3['notes']));
		$sheet->getStyle('D'.$nowrow)->getAlignment()->setWrapText(true);
		$sheet->getStyle('D'.$nowrow)->getAlignment()->setHorizontal('center');
		$sheet->getStyle('D'.$nowrow)->getAlignment()->setVertical('center');
		//-----//
		$sheet->setCellValue('E'.$nowrow, strtoupper($now_emp['nm_emp']));
		$sheet->getStyle('E'.$nowrow)->getAlignment()->setHorizontal('center');
		$sheet->getStyle('E'.$nowrow)->getFont()->setBold( true );

		$nowrow++;
		$sheet->setCellValue('A'.$nowrow, 'NIP. '.$now_es4['nip_emp']);
		$sheet->mergeCells('A'.$nowrow.':C'.$nowrow);
		$sheet->getStyle('A'.$nowrow)->getAlignment()->setHorizontal('center');

		$nowrow = $nowrow + 4;
		$sheet->setCellValue('D'.$nowrow, strtoupper($now_es3['nm_emp']));
		$sheet->getStyle('D'.$nowrow)->getAlignment()->setWrapText(true);
		$sheet->getStyle('D'.$nowrow)->getAlignment()->setHorizontal('center');
		$sheet->getStyle('D'.$nowrow)->getAlignment()->setVertical('center');

		$nowrow++;
		$sheet->setCellValue('D'.$nowrow, 'NIP. '.$now_es3['nip_emp']);
		$sheet->getStyle('D'.$nowrow)->getAlignment()->setHorizontal('center');

		$styleArray = [
			'font' => [
				'size' => 12,
				'name' => 'Trebuchet MS',
			]
		];
		$sheet->getStyle('A'.($rowend+1).':E'.$nowrow)->applyFromArray($styleArray);

		$filename = 'EKinerja_'.$now_id.'_'.$monthName.$now_year.'.xlsx';

		// Redirect output to a client's web browser (Xlsx)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		 
		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.
		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
	}

	public function printexcelpegawaiadmin(Request $request)
	{
		$idunit = $request->unit;
		$kednow = $request->ked;
		$esecheck = $request->es;

		if($esecheck == '23') {
			$employees = DB::select( DB::raw("  
						SELECT id_emp, 
                        -- email_emp, 
                        nrk_emp, nip_emp, gelar_dpn, gelar_blk, nm_emp, sk_cpns, sk_pns, karpeg, nik_emp, a.idgroup as idgroup, alamat_emp, status_nikah, gol_darah, idagama, tlp_emp, tempat_lahir, tgl_lahir, jnkel_emp, tgl_join, status_emp, nm_bank, cb_bank, an_bank, nr_bank, no_taspen, npwp, no_askes, no_jamsos, 
						tbjab.idjab, tbjab.idunit, tbjab.tmt_jab, tbjab.no_sk_jab, tbjab.tmt_sk_jab, tbjab.gambar as jabgambar, 
                        CASE
                            WHEN (status_emp not like 'NON PNS') 
                                THEN CONCAT(DATEDIFF(month, tmt_jab, GETDATE()) / 12, ' Tahun ', DATEDIFF(month, tmt_jab, GETDATE()) % 12, ' Bulan' )
                        END as masa_unit,
                        CASE
                            WHEN tmt_sk_cpns is not null 
                                THEN CONCAT(DATEDIFF(year, tmt_sk_cpns, GETDATE()), ' Tahun')
                            ELSE CONCAT(DATEDIFF(year, tgl_join, GETDATE()), ' Tahun')
                        END as masa_kerja,
						tbunit.nm_unit, tbunit.notes, tbunit.child, tbunit.kd_unit, d.nm_lok, 
						tbdik.iddik, tbdik.prog_sek, tbdik.nm_sek, tbdik.th_sek, tbdik.no_sek, tbdik.gambar as dikgambar, 
						tbgol.tmt_gol, tbgol.idgol, tbgol.nm_pangkat, tbgol.no_sk_gol, tbgol.tmt_sk_gol, tbgol.gambar as golgambar
						from bpaddtfake.dbo.emp_data as a
						CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM  bpaddtfake.dbo.emp_gol,bpaddtfake.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
						CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
						CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpaddtfake.dbo.emp_dik,bpaddtfake.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
						CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
						,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
						and LEN(tbunit.kd_unit) <= 6 AND ked_emp = '$kednow'
						order by idunit asc, nm_emp ASC") );
			$employees = json_decode(json_encode($employees), true);
		} else {
			$employees = DB::select( DB::raw("  
						SELECT id_emp, 
                        -- email_emp, 
                        nrk_emp, nip_emp, gelar_dpn, gelar_blk, nm_emp, sk_cpns, sk_pns, karpeg, nik_emp, a.idgroup as idgroup, alamat_emp, status_nikah, gol_darah, idagama, tlp_emp, tempat_lahir, tgl_lahir, jnkel_emp, tgl_join, status_emp, nm_bank, cb_bank, an_bank, nr_bank, no_taspen, npwp, no_askes, no_jamsos, 
						tbjab.idjab, tbjab.idunit, tbjab.tmt_jab, tbjab.no_sk_jab, tbjab.tmt_sk_jab, tbjab.gambar as jabgambar, 
                        CASE
                            WHEN (status_emp not like 'NON PNS') 
                                THEN CONCAT(DATEDIFF(month, tmt_jab, GETDATE()) / 12, ' Tahun ', DATEDIFF(month, tmt_jab, GETDATE()) % 12, ' Bulan' )
                        END as masa_unit,
                        CASE
                            WHEN tmt_sk_cpns is not null 
                                THEN CONCAT(DATEDIFF(year, tmt_sk_cpns, GETDATE()), ' Tahun')
                            ELSE CONCAT(DATEDIFF(year, tgl_join, GETDATE()), ' Tahun')
                        END as masa_kerja,
						tbunit.nm_unit, tbunit.notes, tbunit.child, tbunit.kd_unit, d.nm_lok, 
						tbdik.iddik, tbdik.prog_sek, tbdik.nm_sek, tbdik.th_sek, tbdik.no_sek, tbdik.gambar as dikgambar, 
						tbgol.tmt_gol, tbgol.idgol, tbgol.nm_pangkat, tbgol.no_sk_gol, tbgol.tmt_sk_gol, tbgol.gambar as golgambar
						from bpaddtfake.dbo.emp_data as a
						CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM  bpaddtfake.dbo.emp_gol,bpaddtfake.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
						CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
						CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpaddtfake.dbo.emp_dik,bpaddtfake.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
						CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
						,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
						and idunit like '$idunit%' AND ked_emp = '$kednow'
						order by idunit asc, nm_emp ASC") );
			$employees = json_decode(json_encode($employees), true);
		}

		$alphabet = array( 'a', 'b', 'c', 'd', 'e',
					   'f', 'g', 'h', 'i', 'j',
					   'k', 'l', 'm', 'n', 'o',
					   'p', 'q', 'r', 's', 't',
					   'u', 'v', 'w', 'x', 'y',
					   'z', 'aa', 'ab', 'ac', 'ad', 'ae',
					   'af', 'ag', 'ah', 'ai', 'aj',
					   'ak', 'al', 'am', 'an', 'ao',
					   'ap', 'aq', 'ar', 'as', 'at',
					   'au', 'av', 'aw', 'ax', 'ay',
					   'az', 'ba', 'bb', 'bc', 'bd', 'be',
					   'bf', 'bg', 'bh', 'bi', 'bj',
					   'bk', 'bl', 'bm', 'bn', 'bo',
					   'bp', 'bq', 'br', 'bs', 'bt',
					   'bu', 'bv', 'bw', 'bx', 'by',
					   'bz'
					   );
		$alpnum = 0;

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'DATA PEGAWAI');
		$sheet->getStyle('A1')->getFont()->setBold( true );

		$sheet->setCellValue('A2', 'BADAN PENGELOLAAN ASET DAERAH');
		$sheet->getStyle('A2')->getFont()->setBold( true );

		$sheet->setCellValue('A3', 'PROVINSI DKI JAKARTA '.date('Y'));
		$sheet->getStyle('A3')->getFont()->setBold( true );

		$styleArray = [
			'font' => [
				'size' => 12,
				'name' => 'Trebuchet MS',
			]
		];
		$sheet->getStyle('A1:A5')->applyFromArray($styleArray);

		$sheet->setCellValue($alphabet[$alpnum].'5', 'NO'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'STATUS'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'TOTAL MASA JABATAN'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'ID'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'NIP'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'NRK'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'TMT DI BPAD'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'GLR DEPAN'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'NAMA'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'GLR BELAKANG'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'BIDANG'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'UNIT KERJA'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'LOKASI'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'TEMPAT LAHIR'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'TGL LAHIR'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'ALAMAT'); $alpnum++;
		// $sheet->setCellValue($alphabet[$alpnum].'5', 'EMAIL'); $alpnum++;
        $sheet->setCellValue($alphabet[$alpnum].'5', 'TELP'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'AGAMA'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'JNS KELAMIN'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'STAT PERNIKAHAN'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'GOL DARAH'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'NIK KTP'); $alpnum++;
		$alpnum++;

		$sheet->setCellValue($alphabet[$alpnum].'5', 'SK CPNS'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'SK PNS'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'KARPEG'); $alpnum++;
		$alpnum++;

		$sheet->setCellValue($alphabet[$alpnum].'5', 'NAMA BANK'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'CABANG BANK'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'NAMA REKENING'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'NOMOR REKENING'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'NOMOR TASPEN'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'NPWP'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'NOMOR ASKES'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'NOMOR BPJS'); $alpnum++;
		$alpnum++;

		// $sheet->setCellValue($alphabet[$alpnum].'5', 'DATA KELUARGA'); $alpnum++;
		// $alpnum++;

		$sheet->setCellValue($alphabet[$alpnum].'5', 'PENDIDIKAN TERAKHIR'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'LEMBAGA'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'PRODI'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'TAHUN LULUS'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'NO IJAZAH'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'FILE IJAZAH'); $alpnum++;
		$alpnum++;

		$sheet->setCellValue($alphabet[$alpnum].'5', 'GOLONGAN'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'NAMA GOLONGAN'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'TMT GOLONGAN'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'NOMOR SK GOL'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'TMT SK GOL'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'FILE SK GOL'); $alpnum++;
		$alpnum++;

		$sheet->setCellValue($alphabet[$alpnum].'5', 'UNIT KERJA'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'MASA KERJA PADA UNIT'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'LOKASI'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'TMT JABATAN'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'NOMOR SK JAB'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'TMT SK JAB'); $alpnum++;
		$sheet->setCellValue($alphabet[$alpnum].'5', 'FILE SK JAB');
		$maxalpnum = $alpnum;

		$colorArrayhead = [
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => 'F79646',
				],
			],
		];
		$sheet->getStyle($alphabet[0].'5:'.$alphabet[$maxalpnum].'5')->applyFromArray($colorArrayhead);

		$sheet->getStyle($alphabet[0].'5:'.$alphabet[$maxalpnum].'5')->getFont()->setBold( true );

		$sheet->getStyle($alphabet[0].'5:'.$alphabet[$maxalpnum].'5')->getAlignment()->setHorizontal('center');

		$colorArrayV1 = [
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => 'FDE9D9',
				],
			],
		];

		$colorArrayEmpty = [
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => 'FF0000',
				],
			],
		];

		$nowrow = 6;
		$rowstart = $nowrow - 1;
		$alpnum = 0;
		$bidangnow = '';
		foreach ($employees as $key => $employee) {
			if ($key%2 == 0) {
				$sheet->getStyle($alphabet[0].$nowrow.':'.$alphabet[$maxalpnum].$nowrow)->applyFromArray($colorArrayV1);
			}
			
			if(strlen($employee['kd_unit']) == 6) {
				$bidangnow = $employee['nm_unit'];
			} elseif (strlen($employee['kd_unit']) == 2) {
				$bidangnow = "BADAN PENGELOLAAN ASET DAERAH";
			}

			$sheet->setCellValue($alphabet[$alpnum].$nowrow, $key+1); $alpnum++;
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, $employee['status_emp']); $alpnum++;
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, $employee['masa_kerja']); $alpnum++;
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, $employee['id_emp']); $alpnum++;
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, $employee['nip_emp'] ? '\''.$employee['nip_emp'] : '-' ); $alpnum++;
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, $employee['nrk_emp'] ?? '-' ); 
			$sheet->getStyle($alphabet[$alpnum].$nowrow)->getAlignment()->setHorizontal('right'); $alpnum++;
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, ($employee['tgl_join'] ? date('d-m-Y', strtotime($employee['tgl_join'])) : '-') ); $alpnum++;
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, ($employee['gelar_dpn'] ? $employee['gelar_dpn'] . ' ' : '' )); $alpnum++;
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, strtoupper($employee['nm_emp'])); $alpnum++;
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, ($employee['gelar_blk'] ? ', ' . $employee['gelar_blk'] : '' )); $alpnum++;
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, strtoupper($bidangnow)); $alpnum++;
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, strtoupper($employee['notes'])); $alpnum++;
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, $employee['nm_lok']); $alpnum++;
			if(is_null($employee['tempat_lahir']) || $employee['tempat_lahir'] == '') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, ($employee['tempat_lahir'] ? strtoupper($employee['tempat_lahir']) : '#EMPTY') ); $alpnum++;
			if(is_null($employee['tgl_lahir']) || $employee['tgl_lahir'] == '') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, ($employee['tgl_lahir'] ? date('d-m-Y', strtotime($employee['tgl_lahir'])) : '#EMPTY') ); $alpnum++;
			if(is_null($employee['alamat_emp']) || $employee['alamat_emp'] == '') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, $employee['alamat_emp'] && $employee['alamat_emp'] != '' && $employee['alamat_emp'] != '-' ? $employee['alamat_emp'] : '#EMPTY'); $alpnum++;
			// if(is_null($employee['email_emp']) || $employee['email_emp'] == '') {
			// 	$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			// }
			// $sheet->setCellValue($alphabet[$alpnum].$nowrow, $employee['email_emp'] && $employee['email_emp'] != '' && $employee['email_emp'] != '-' ? $employee['email_emp'] : '#EMPTY'); $alpnum++;
			if(is_null($employee['tlp_emp']) || $employee['tlp_emp'] == '') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, $employee['tlp_emp'] && $employee['tlp_emp'] != '' && $employee['tlp_emp'] != '-' ? $employee['tlp_emp'] : '#EMPTY'); $alpnum++;
			if($employee['idagama'] == 'A') {
				$sheet->setCellValue($alphabet[$alpnum].$nowrow, 'ISLAM'); $alpnum++;
			} elseif ($employee['idagama'] == 'B') {
				$sheet->setCellValue($alphabet[$alpnum].$nowrow, 'KATOLIK'); $alpnum++;
			} elseif ($employee['idagama'] == 'C') {
				$sheet->setCellValue($alphabet[$alpnum].$nowrow, 'PROTESTAN'); $alpnum++;
			} elseif ($employee['idagama'] == 'D') {
				$sheet->setCellValue($alphabet[$alpnum].$nowrow, 'BUDHA'); $alpnum++;
			} elseif ($employee['idagama'] == 'E') {
				$sheet->setCellValue($alphabet[$alpnum].$nowrow, 'HINDU'); $alpnum++;
			} elseif ($employee['idagama'] == 'F') {
				$sheet->setCellValue($alphabet[$alpnum].$nowrow, 'LAINNYA'); $alpnum++;
			} elseif ($employee['idagama'] == 'G') {
				$sheet->setCellValue($alphabet[$alpnum].$nowrow, 'KONGHUCU'); $alpnum++;
			} else {
				$sheet->setCellValue($alphabet[$alpnum].$nowrow, 'LAINNYA'); $alpnum++;
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, $employee['jnkel_emp'] == 'L' ? 'LAKI-LAKI' : 'PEREMPUAN'); $alpnum++;
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, strtoupper($employee['status_nikah'])); $alpnum++;
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, $employee['gol_darah']); $alpnum++;
			if(is_null($employee['nik_emp']) || $employee['nik_emp'] == '') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, ($employee['nik_emp'] ? '\''.$employee['nik_emp'] : '#EMPTY') ); $alpnum++;

			$sheet->getStyle($alphabet[$alpnum].$nowrow.':'.$alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayhead); $alpnum++;

			if((is_null($employee['sk_cpns']) || $employee['sk_cpns'] == '') && $employee['status_emp'] != 'NON PNS') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, ($employee['sk_cpns'] && $employee['sk_cpns'] != '' ? 'ADA' : 'TIDAK ADA' )); $alpnum++;
			if((is_null($employee['sk_pns']) || $employee['sk_pns'] == '') && $employee['status_emp'] != 'NON PNS') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, ($employee['sk_pns'] && $employee['sk_pns'] != '' ? 'ADA' : 'TIDAK ADA' )); $alpnum++;
			if((is_null($employee['karpeg']) || $employee['karpeg'] == '') && $employee['status_emp'] != 'NON PNS') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, ($employee['karpeg'] && $employee['karpeg'] != '' ? 'ADA' : 'TIDAK ADA' )); $alpnum++;

			$sheet->getStyle($alphabet[$alpnum].$nowrow.':'.$alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayhead); $alpnum++;

			if(is_null($employee['nm_bank']) || $employee['nm_bank'] == '') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, ($employee['nm_bank'] && $employee['nm_bank'] != '' ? strtoupper($employee['nm_bank']) : '#EMPTY' )); $alpnum++;
			if(is_null($employee['cb_bank']) || $employee['cb_bank'] == '') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, ($employee['cb_bank'] && $employee['cb_bank'] != '' ? strtoupper($employee['cb_bank']) : '#EMPTY' )); $alpnum++;
			if(is_null($employee['an_bank']) || $employee['an_bank'] == '') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, ($employee['an_bank'] && $employee['an_bank'] != '' ? strtoupper($employee['an_bank']) : '#EMPTY' )); $alpnum++;
			if(is_null($employee['nr_bank']) || $employee['nr_bank'] == '') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, ($employee['nr_bank'] && $employee['nr_bank'] != '' ? strtoupper($employee['nr_bank']) : '#EMPTY' )); $alpnum++;
			if(is_null($employee['no_taspen']) || $employee['no_taspen'] == '') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, ($employee['no_taspen'] && $employee['no_taspen'] != '' ? strtoupper($employee['no_taspen']) : '#EMPTY' )); $alpnum++;
			if(is_null($employee['npwp']) || $employee['npwp'] == '') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, ($employee['npwp'] && $employee['npwp'] != '' ? strtoupper($employee['npwp']) : '#EMPTY' )); $alpnum++;
			if(is_null($employee['no_askes']) || $employee['no_askes'] == '') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, ($employee['no_askes'] && $employee['no_askes'] != '' ? strtoupper($employee['no_askes']) : '#EMPTY' )); $alpnum++;
			if(is_null($employee['no_jamsos']) || $employee['no_jamsos'] == '') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, ($employee['no_jamsos'] && $employee['no_jamsos'] != '' ? strtoupper($employee['no_jamsos']) : '#EMPTY' )); $alpnum++;
			$sheet->getStyle($alphabet[$alpnum].$nowrow.':'.$alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayhead); $alpnum++;


			//DATA KELUARGA
			// if(is_null($employee['idkel'])) {
			//     $sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			// }
			// $sheet->setCellValue($alphabet[$alpnum].$nowrow, is_null($employee['idkel']) ? 'DATA KELUARGA TERISI' : '#EMPTY'); $alpnum++;


			//DATA PENDIDIKAN
			if(is_null($employee['iddik']) || $employee['iddik'] == '' || $employee['iddik'] == 'NA') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, $employee['iddik'] != 'NA' ? $employee['iddik'] : '#EMPTY'); $alpnum++;
			if(is_null($employee['nm_sek']) || $employee['nm_sek'] == '') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, $employee['nm_sek'] ? strtoupper($employee['nm_sek']) : '#EMPTY' ); $alpnum++;
			if(is_null($employee['prog_sek']) || $employee['prog_sek'] == '') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, $employee['prog_sek'] ? strtoupper($employee['prog_sek']) : '#EMPTY' ); $alpnum++;
			if(is_null($employee['th_sek']) || $employee['th_sek'] == '') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, $employee['th_sek'] && $employee['th_sek'] != '' && $employee['th_sek'] != '-' ? $employee['th_sek'] : '#EMPTY'); $alpnum++;
			if(is_null($employee['no_sek']) || $employee['no_sek'] == '') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, $employee['no_sek'] && $employee['no_sek'] != '' && $employee['no_sek'] != '-' ? $employee['no_sek'] : '#EMPTY'); $alpnum++;
			if(is_null($employee['dikgambar']) || $employee['dikgambar'] == '') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, is_null($employee['dikgambar']) || $employee['dikgambar'] == '' ? 'BELUM UPLOAD' : 'SUDAH UPLOAD'); $alpnum++;
			$sheet->getStyle($alphabet[$alpnum].$nowrow.':'.$alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayhead); $alpnum++;


			//DATA GOLONGAN
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, $employee['idgol'] ?? '-' ); $alpnum++;
			if((is_null($employee['nm_pangkat']) || $employee['nm_pangkat'] == '') && $employee['status_emp'] != 'NON PNS') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, $employee['nm_pangkat'] ? strtoupper($employee['nm_pangkat']) : '#EMPTY' ); $alpnum++;
			if((is_null($employee['tmt_gol']) || $employee['tmt_gol'] == '') && $employee['status_emp'] != 'NON PNS') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, ($employee['tmt_gol'] ? date('d-M-Y', strtotime(str_replace('/', '-', $employee['tmt_gol']))) : '#EMPTY') ); $alpnum++;
			if((is_null($employee['no_sk_gol']) || $employee['no_sk_gol'] == '') && $employee['status_emp'] != 'NON PNS') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, ($employee['no_sk_gol'] && $employee['no_sk_gol'] != '' && $employee['no_sk_gol'] != '-' ? strtoupper($employee['no_sk_gol']) : '#EMPTY') ); $alpnum++;
			if((is_null($employee['tmt_sk_gol']) || $employee['tmt_sk_gol'] == '') && $employee['status_emp'] != 'NON PNS') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, ($employee['tmt_sk_gol'] ? date('d-M-Y', strtotime(str_replace('/', '-', $employee['tmt_sk_gol']))) : '#EMPTY') ); $alpnum++;
			if((is_null($employee['golgambar']) || $employee['golgambar'] == '') && $employee['status_emp'] != 'NON PNS') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, is_null($employee['golgambar']) || $employee['golgambar'] == '' ? 'BELUM UPLOAD' : 'SUDAH UPLOAD' ); $alpnum++;
			
            //DATA UNIT KERJA
			$sheet->getStyle($alphabet[$alpnum].$nowrow.':'.$alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayhead); $alpnum++;
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, strtoupper($employee['nm_unit'])); $alpnum++;
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, strtoupper($employee['masa_unit'])); $alpnum++;
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, $employee['nm_lok']); $alpnum++;
			if((is_null($employee['tmt_jab']) || $employee['tmt_jab'] == '') && $employee['status_emp'] != 'NON PNS') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, ($employee['tmt_jab'] ? date('d-M-Y', strtotime(str_replace('/', '-', $employee['tmt_jab']))) : '#EMPTY') ); $alpnum++;
			if((is_null($employee['no_sk_jab']) || $employee['no_sk_jab'] == '') && $employee['status_emp'] != 'NON PNS') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, ($employee['no_sk_jab'] && $employee['no_sk_jab'] != '' && $employee['no_sk_jab'] != '-' ? strtoupper($employee['no_sk_jab']) : '#EMPTY') ); $alpnum++;
			if((is_null($employee['tmt_sk_jab']) || $employee['tmt_sk_jab'] == '') && $employee['status_emp'] != 'NON PNS') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, ($employee['tmt_sk_jab'] ? date('d-M-Y', strtotime(str_replace('/', '-', $employee['tmt_sk_jab']))) : '#EMPTY') ); $alpnum++;
			if((is_null($employee['jabgambar']) || $employee['jabgambar'] == '') && $employee['status_emp'] != 'NON PNS') {
				$sheet->getStyle($alphabet[$alpnum].$nowrow)->applyFromArray($colorArrayEmpty);
			}
			$sheet->setCellValue($alphabet[$alpnum].$nowrow, is_null($employee['jabgambar']) || $employee['jabgambar'] == '' ? 'BELUM UPLOAD' : 'SUDAH UPLOAD' );

			if (strlen($employee['idunit']) < 10) {
				$sheet->getStyle($alphabet[0].$nowrow.':'.$alphabet[$alpnum].$nowrow)->getFont()->setBold( true );
			}

			$nowrow++;
			$alpnum = 0;
		}

		foreach($alphabet as $key => $columnID) {
			if($key > 0) {
				$sheet->getColumnDimension($columnID)
				->setAutoSize(true);
			}
		}
		$sheet->getColumnDimension('A')->setWidth(7);

		
		if($esecheck == '23') {
			$filename = date('dmy').'_PEGAWAI_ESELON_BPAD.xlsx';
		} else {
			$filename = date('dmy').'_PEGAWAI_BPAD.xlsx';
		}

		$rowend = $nowrow - 1;


		// Redirect output to a client's web browser (Xlsx)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		 
		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.

		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
	}

	public function printexcelpegawai(Request $request)
	{
		$idunit = $request->unit;
		$kednow = $request->ked;

		$employees = DB::select( DB::raw("  
					SELECT id_emp, nrk_emp, nip_emp, nm_emp, a.idgroup as idgroup, tgl_lahir, jnkel_emp, tgl_join, status_emp, tbjab.idjab, tbjab.idunit, tbunit.nm_unit, tbunit.notes, tbunit.child, d.nm_lok, 
                    CASE
                        WHEN (status_emp not like 'NON PNS') 
                            THEN CONCAT(DATEDIFF(month, tmt_jab, GETDATE()) / 12, ' Tahun ', DATEDIFF(month, tmt_jab, GETDATE()) % 12, ' Bulan' )
                    END as masa_unit,
                    CASE
                            WHEN tmt_sk_cpns is not null 
                                THEN CONCAT(DATEDIFF(year, tmt_sk_cpns, GETDATE()), ' Tahun')
                            ELSE CONCAT(DATEDIFF(year, tgl_join, GETDATE()), ' Tahun')
                        END as masa_kerja,
                    from bpaddtfake.dbo.emp_data as a
					CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM  bpaddtfake.dbo.emp_gol,bpaddtfake.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
					CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
					CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpaddtfake.dbo.emp_dik,bpaddtfake.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
					CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
					,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
					and idunit like '$idunit%' AND ked_emp = '$kednow'
					order by idunit asc, nm_emp ASC") );
		$employees = json_decode(json_encode($employees), true);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->mergeCells('A1:K1');
		$sheet->setCellValue('A1', 'DATA PEGAWAI');
		$sheet->getStyle('A1')->getFont()->setBold( true );
		$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

		$sheet->mergeCells('A2:K2');
		$sheet->setCellValue('A2', 'BADAN PENGELOLAAN ASET DAERAH');
		$sheet->getStyle('A2')->getFont()->setBold( true );
		$sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

		$sheet->mergeCells('A3:K3');
		$sheet->setCellValue('A3', 'PROVINSI DKI JAKARTA '.date('Y'));
		$sheet->getStyle('A3')->getFont()->setBold( true );
		$sheet->getStyle('A3')->getAlignment()->setHorizontal('center');	

		$styleArray = [
			'font' => [
				'size' => 12,
				'name' => 'Trebuchet MS',
			]
		];
		$sheet->getStyle('A1:K5')->applyFromArray($styleArray);

		$sheet->setCellValue('A5', 'NO');
		$sheet->setCellValue('B5', 'TOTAL MASA KERJA');
		$sheet->setCellValue('C5', 'ID');
		$sheet->setCellValue('D5', 'NIP');
		$sheet->setCellValue('E5', 'NRK');
		$sheet->setCellValue('F5', 'NAMA');
		$sheet->setCellValue('G5', 'UNIT');
		$sheet->setCellValue('H5', 'MASA KERJA PADA UNIT');
		$sheet->setCellValue('I5', 'LOKASI');
		$sheet->setCellValue('J5', 'TGL LAHIR');
		$sheet->setCellValue('K5', 'STATUS');

		$colorArrayhead = [
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => 'F79646',
				],
			],
		];
		$sheet->getStyle('A5:K5')->applyFromArray($colorArrayhead);

		$sheet->getStyle('A5:K5')->getFont()->setBold( true );

		$sheet->getStyle('A5:K5')->getAlignment()->setHorizontal('center');

		$colorArrayV1 = [
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => 'FDE9D9',
				],
			],
		];

		$nowrow = 6;
		$rowstart = $nowrow - 1;
		foreach ($employees as $key => $employee) {
			if ($key%2 == 0) {
				$sheet->getStyle('A'.$nowrow.':K'.$nowrow)->applyFromArray($colorArrayV1);
			}

			$sheet->setCellValue('A'.$nowrow, $key+1);
			$sheet->setCellValue('B'.$nowrow, $employee['masa_kerja']);
			$sheet->setCellValue('C'.$nowrow, $employee['id_emp']);
			$sheet->setCellValue('D'.$nowrow, $employee['nip_emp'] ? '\''.$employee['nip_emp'] : '-' );
			$sheet->setCellValue('E'.$nowrow, $employee['nrk_emp'] ? $employee['nrk_emp'] : '-' );
			$sheet->getStyle('E'.$nowrow)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('F'.$nowrow, strtoupper($employee['nm_emp']));
			$sheet->setCellValue('G'.$nowrow, strtoupper($employee['notes']));
			$sheet->setCellValue('H'.$nowrow, strtoupper($employee['masa_unit']));
			$sheet->setCellValue('I'.$nowrow, $employee['nm_lok']);
			$sheet->setCellValue('J'.$nowrow, date('d-m-Y', strtotime($employee['tgl_lahir'])));
			$sheet->setCellValue('K'.$nowrow, $employee['status_emp']);

			if (strlen($employee['idunit']) < 10) {
				$sheet->getStyle('A'.$nowrow.':K'.$nowrow)->getFont()->setBold( true );
			}

			$nowrow++;
		}

		foreach(range('A','K') as $columnID) {
			$sheet->getColumnDimension($columnID)
				->setAutoSize(true);
		}

		$rowend = $nowrow - 1;

		$filename = date('dmy').'_PEGAWAI_BPAD.xlsx';

		// Redirect output to a client's web browser (Xlsx)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		 
		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.

		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
	}

	public function laporankinerja(Request $request)
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
					SELECT id_emp, nm_emp, nrk_emp FROM bpaddtfake.dbo.emp_data as a
					CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM  bpaddtfake.dbo.emp_gol,bpaddtfake.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
					CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
					CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpaddtfake.dbo.emp_dik,bpaddtfake.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
					CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
					,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
					and ked_emp = 'aktif' and tgl_end is null and tbunit.sao like '01%'
					order by nm_emp"));
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

		if ($request->now_valid) {
			$now_valid = $request->now_valid;
		} else {
			$now_valid = "= 1";
		}

		$laporans = DB::select( DB::raw("
					SELECT *
					from bpaddtfake.dbo.v_kinerja
					where idemp = '$now_id_emp'
					and stat $now_valid
					and YEAR(tgl_trans) = $now_year
					and MONTH(tgl_trans) = $now_month
					ORDER BY tgl_trans, time1, time2
					"));
		$laporans = json_decode(json_encode($laporans), true);

		return view('pages.bpadkepegawaian.kinerjalaporan')
				->with('access', $access)
				->with('pegawais', $pegawais)
				->with('now_id_emp', $now_id_emp)
				->with('now_month', $now_month)
				->with('now_year', $now_year)
				->with('now_valid', $now_valid)
				->with('laporans', $laporans);
	}

	public function formresetkinerja(Request $request)
	{
		Kinerja_data::
			where('idemp', $request->now_id_emp)
			->where('tgl_trans', $request->tgl_trans)
			->update([
				'stat' => null,
			]);

		return redirect('/kepegawaian/laporan%20kinerja?now_id_emp='.$request->now_id_emp.'&now_month='.$request->now_month.'&now_year='.$request->now_year.'&now_valid=%3D+1')->with('message', 'Kinerja tanggal '.date('d-M-Y',strtotime($request->tgl_trans)).' berhasil direset');
	}

	// -------------------- EKINERJA -------------------- //
}