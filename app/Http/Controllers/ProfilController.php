<?php

namespace App\Http\Controllers;

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PDF;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Traits\SessionCheckTraits;

use App\Emp_data;
use App\Emp_dik;
use App\Emp_gol;
use App\Emp_jab;
use App\Emp_non;
use App\Emp_kel;
use App\Emp_huk;
use App\Fr_disposisi;
use App\Glo_dik;
use App\Glo_disposisi_kode;
use App\Glo_disposisi_penanganan;
use App\Glo_huk;
use App\Glo_kel;
use App\Glo_org_golongan;
use App\Glo_org_jabatan;
use App\Glo_org_kedemp;
use App\Glo_org_lokasi;
use App\Glo_org_statusemp;
use App\glo_org_unitkerja;
use App\Sec_access;
use App\Sec_menu;

session_start();

class ProfilController extends Controller
{
	use SessionCheckTraits;

	public function __construct()
	{
		$this->middleware('auth');
	}

	public function printdrh(Request $request)
	{
		$emp_data = Emp_data::
						where('id_emp', Auth::user()->id_emp)
						->where('sts', 1)
						->first();

		$emp_dik = Emp_dik::with('dik')
						->where('noid', Auth::user()->id_emp)
						->where('sts', 1)
						->orderBy('th_sek', 'desc')
						->get();

		// $emp_gol = Emp_gol::with('gol')
		// 				->where('noid', Auth::user()->id_emp)
		// 				->where('sts', 1)
		// 				->orderBy('tmt_gol', 'desc')
		// 				->get();

		$emp_gol = Emp_gol::
						join('bpaddtfake.dbo.glo_org_golongan', 'bpaddtfake.dbo.glo_org_golongan.gol', '=', 'bpaddtfake.dbo.emp_gol.idgol')
						->where('bpaddtfake.dbo.emp_gol.noid', Auth::user()->id_emp)
						->where('bpaddtfake.dbo.emp_gol.sts', 1)
						->orderBy('bpaddtfake.dbo.emp_gol.tmt_gol', 'desc')
						->get();

		$emp_jab = Emp_jab::with('jabatan')
						->with('lokasi')
						->with('unit')
						->where('noid', Auth::user()->id_emp)
						->where('sts', 1)
						->orderBy('tmt_jab', 'desc')
						->get();

		$emp_non = Emp_non::where('sts', 1)
						->where('noid', Auth::user()->id_emp)
						->orderBy('tgl_non', 'desc')
						->get();

		$emp_kel = Emp_kel::
					join('bpaddtfake.dbo.glo_kel', 'bpaddtfake.dbo.glo_kel.kel', '=', 'bpaddtfake.dbo.emp_kel.jns_kel')
					->where('bpaddtfake.dbo.emp_kel.noid', Auth::user()->id_emp)
					->where('bpaddtfake.dbo.emp_kel.sts', 1)
					->orderBy('urut', 'asc')
					->get();

		$emp_huk = Emp_huk::
					where('sts', 1)
					->where('noid', Auth::user()->id_emp)
					->orderBy('tgl_sk', 'desc')
					->get();

		$pdf = PDF::setPaper('a4', 'portrait');
		$pdf->loadView('pages.bpadprofil.previewprofil', 
						[
							'emp_data' => $emp_data,
							'emp_dik' => $emp_dik,
							'emp_gol' => $emp_gol,
							'emp_jab' => $emp_jab,
							'emp_non' => $emp_non,
							'emp_kel' => $emp_kel,
							'emp_huk' => $emp_huk,
						]);
		// return $pdf->stream('preview.pdf');
		return $pdf->download(Auth::user()->id_emp.'_DAFTAR RIWAYAT HIDUP.pdf');
	}

	public function pegawai(Request $request)
	{
		$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);



		// $accessid = $this->checkAccess($_SESSION['user_data']['idgroup'], 37);
		// $accessdik = $this->checkAccess($_SESSION['user_data']['idgroup'], 65);
		// $accessgol = $this->checkAccess($_SESSION['user_data']['idgroup'], 71);
		// $accessjab = $this->checkAccess($_SESSION['user_data']['idgroup'], 72);

		$emp_data = Emp_data::
						where('id_emp', Auth::user()->id_emp)
						->where('sts', 1)
						->get();

		$emp_dik = Emp_dik::with('dik')
						->where('noid', Auth::user()->id_emp)
						->where('sts', 1)
						->orderBy('th_sek', 'desc')
						->get();

		$emp_gol = Emp_gol::with('gol')
						->where('noid', Auth::user()->id_emp)
						->where('sts', 1)
						->orderBy('tmt_gol', 'desc')
						->get();

		$emp_jab = Emp_jab::with('jabatan')
						->with('lokasi')
						->with('unit')
						->where('noid', Auth::user()->id_emp)
						->where('sts', 1)
						->orderBy('tmt_jab', 'desc')
						->get();

		$emp_non = Emp_non::where('sts', 1)
						->where('noid', Auth::user()->id_emp)
						->orderBy('tgl_non', 'desc')
						->get();

		$emp_kel = Emp_kel::
					join('bpaddtfake.dbo.glo_kel', 'bpaddtfake.dbo.glo_kel.kel', '=', 'bpaddtfake.dbo.emp_kel.jns_kel')
					->where('bpaddtfake.dbo.emp_kel.noid', Auth::user()->id_emp)
					->where('bpaddtfake.dbo.emp_kel.sts', 1)
					->orderBy('urut', 'asc')
					->get();

		$emp_huk = Emp_huk::
					where('sts', 1)
					->where('noid', Auth::user()->id_emp)
					->orderBy('tgl_sk', 'desc')
					->get();

		$statuses = Glo_org_statusemp::get();
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

		return view('pages.bpadprofil.pegawai')
				->with('id_emp', Auth::user()->id_emp)
				->with('emp_data', $emp_data[0])
				->with('emp_dik', $emp_dik)
				->with('emp_gol', $emp_gol)
				->with('emp_jab', $emp_jab)
				->with('emp_non', $emp_non)
				->with('emp_kel', $emp_kel)
				->with('emp_huk', $emp_huk)
				->with('statuses', $statuses)
				->with('pendidikans', $pendidikans)
				->with('golongans', $golongans)
				->with('jabatans', $jabatans)
				->with('lokasis', $lokasis)
				->with('kedudukans', $kedudukans)
				->with('units', $units)
				->with('keluargas', $keluargas)
				->with('hukumans', $hukumans);
	}

	public function formupdateidpegawai(Request $request)
	{
		$this->checkSessionTime();

		$id_emp = $request->id_emp;
		$filefoto = '';

		$cekangkapenting = Emp_data::where('id_emp', $request->id_emp)->first(['nik_emp', 'nrk_emp', 'nip_emp']);

		if ($request->nip_emp && $request->nip_emp != '' && $request->nip_emp != $cekangkapenting['nip_emp']) {
			$ceknip = Emp_data::
						where('nip_emp', $request->nip_emp)
						->where('sts', '1')
						->count();
			if ($ceknip > 0) {
				return redirect('/profil/pegawai')->with('message', 'NIP sudah tersimpan di database');
			}
		}
		if (strlen($request->nip_emp) != 18 && strlen($request->nip_emp) != 0) {
			return redirect('/profil/pegawai')->with('message', 'NIP harus terdiri dari 18 digit');
		}
			
		if ($request->nrk_emp && $request->nrk_emp != '' && $request->nrk_emp != $cekangkapenting['nrk_emp']) {
			$ceknrk = Emp_data::
						where('nrk_emp', $request->nrk_emp)
						->where('sts', '1')
						->count();
			if ($ceknrk > 0) {
				return redirect('/profil/pegawai')->with('message', 'NRK sudah tersimpan di database');
			}
		}
		if (strlen($request->nrk_emp) != 6 && strlen($request->nrk_emp) != 0) {
			return redirect('/profil/pegawai')->with('message', 'NRK harus terdiri dari 6 digit');
		}

		if ($request->nik_emp && $request->nik_emp != '' && $request->nik_emp != $cekangkapenting['nik_emp']) {
			$ceknrk = Emp_data::
						where('nik_emp', $request->nik_emp)
						->where('sts', '1')
						->count();
			if ($ceknrk > 0) {
				return redirect('/profil/pegawai')->with('message', 'NIK KTP sudah tersimpan di database');
			}
		}	
		if (strlen($request->nik_emp) != 16 && strlen($request->nik_emp) != 0) {
			return redirect('/profil/pegawai')->with('message', 'NIK KTP harus terdiri dari 16 digit');
		}

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		if (isset($request->filefoto)) {
			$file = $request->filefoto;

			if ($file->getSize() > 2222222) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file foto pegawai terlalu besar (Maksimal 2MB)');     
			}

			if (strtolower($file->getClientOriginalExtension()) != "png" && strtolower($file->getClientOriginalExtension()) != "jpg" && strtolower($file->getClientOriginalExtension()) != "jpeg") {
				return redirect('/profil/pegawai')->with('message', 'File yang diunggah harus berbentuk JPG / JPEG / PNG');     
			}

			$filefoto .= $id_emp . ".". $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefileimg');
			$tujuan_upload .= "\\" . $id_emp . "\\profil";

			// List of name of files inside 
			// specified folder 
			$allfiles = glob($tujuan_upload.'/*');  
			   
			// Deleting all the files in the list 
			foreach($allfiles as $all) { 
			   
			    if(is_file($all))  
			    
			        // Delete the given file 
			        unlink($all);  
			} 

			$file->move($tujuan_upload, $filefoto);
		}
			
		if (!(isset($filefoto))) {
			$filefoto = '';
		}

		if (isset($request->tgl_join)) {
			$tgl_join = date('Y-m-d',strtotime($request->tgl_join));
		} else {
			$tgl_join = '';
		}

		if (isset($request->tgl_lahir)) {
			$tgl_lahir = date('Y-m-d',strtotime($request->tgl_lahir));
		} else {
			$tgl_lahir = '';
		}

		Emp_data::where('id_emp', $id_emp)
			->update([
				'tgl_join' => (isset($request->tgl_join) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_join))) : null),
				'status_emp' => $request->status_emp,
				'nip_emp' => ($request->nip_emp ? $request->nip_emp : ''),
				'nrk_emp' => ($request->nrk_emp ? $request->nrk_emp : ''),
				'nm_emp' => ($request->nm_emp ? $request->nm_emp : ''),
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
			]);

		if ($filefoto != '') {
			Emp_data::where('id_emp', $id_emp)
			->update([
				// 'tampilnew' => 1,
				'foto' => $filefoto,
			]);
		}

		return redirect('/profil/pegawai')
					->with('message', 'Pegawai '.$request->nm_emp.' berhasil diubah. Apabila terdapat kesalahan data, mohon melakukan login ulang')
					->with('msg_num', 1);
	}

	// ------KELUARGA--------------------------------------------------------------------- //
	// ------KELUARGA--------------------------------------------------------------------- //
	// ------KELUARGA--------------------------------------------------------------------- //
	// ------KELUARGA--------------------------------------------------------------------- //
	// ------KELUARGA--------------------------------------------------------------------- //
	// ------KELUARGA--------------------------------------------------------------------- //
// ------KELUARGA--------------------------------------------------------------------- //
	// ------KELUARGA--------------------------------------------------------------------- //
	// ------KELUARGA--------------------------------------------------------------------- //
// ------KELUARGA--------------------------------------------------------------------- //
	// ------KELUARGA--------------------------------------------------------------------- //
	// ------KELUARGA--------------------------------------------------------------------- //
// ------KELUARGA--------------------------------------------------------------------- //
	// ------KELUARGA--------------------------------------------------------------------- //
	// ------KELUARGA--------------------------------------------------------------------- //
	// ------KELUARGA--------------------------------------------------------------------- //
	// ------KELUARGA--------------------------------------------------------------------- //
	// ------KELUARGA--------------------------------------------------------------------- //
// ------KELUARGA--------------------------------------------------------------------- //
	// ------KELUARGA--------------------------------------------------------------------- //
	// ------KELUARGA--------------------------------------------------------------------- //
// ------KELUARGA--------------------------------------------------------------------- //
	// ------KELUARGA--------------------------------------------------------------------- //
	// ------KELUARGA--------------------------------------------------------------------- //
// ------KELUARGA--------------------------------------------------------------------- //
	// ------KELUARGA--------------------------------------------------------------------- //
	// ------KELUARGA--------------------------------------------------------------------- //

	public function forminsertkelpegawai (Request $request)
	{
		$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];

		$insert_emp_kel = [
				// PENDIDIKAN
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'noid' => $id_emp,
				'jns_kel' => $request->jns_kel,
				'nm_kel' => $request->nm_kel,
				'nik_kel' => ($request->nik_kel ?? ''),
				'tgl_kel' => ($request->tgl_kel ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_kel))) : ''),
			];

		Emp_kel::insert($insert_emp_kel);

		return redirect('/profil/pegawai')
					->with('message', 'Data keluarga pegawai berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdatekelpegawai (Request $request)
	{
		$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];

		Emp_kel::where('noid', $id_emp)
			->where('ids', $request->ids)
			->update([
				'sts' => 1,
				'jns_kel' => $request->jns_kel,
				'nm_kel' => $request->nm_kel,
				'nik_kel' => ($request->nik_kel ?? ''),
				'tgl_kel' => ($request->tgl_kel ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_kel))) : ''),
			]);

		return redirect('/profil/pegawai')
					->with('message', 'Data keluarga pegawai berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletekelpegawai(Request $request)
	{
		$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];

		Emp_kel::where('noid', $id_emp)
		->where('ids', $request->ids)
		->update([
			'sts' => 0,
		]);

		return redirect('/profil/pegawai')
					->with('message', 'Data keluarga pegawai berhasil dihapus')
					->with('msg_num', 1);
	}

	// ----------PENDIDIKAN--------------------------------------------------------------- //
	// ----------PENDIDIKAN--------------------------------------------------------------- //
	// ----------PENDIDIKAN--------------------------------------------------------------- //
	// ----------PENDIDIKAN--------------------------------------------------------------- //
	// ----------PENDIDIKAN--------------------------------------------------------------- //
	// ----------PENDIDIKAN--------------------------------------------------------------- //
// ----------PENDIDIKAN--------------------------------------------------------------- //
	// ----------PENDIDIKAN--------------------------------------------------------------- //
	// ----------PENDIDIKAN--------------------------------------------------------------- //
// ----------PENDIDIKAN--------------------------------------------------------------- //
	// ----------PENDIDIKAN--------------------------------------------------------------- //
	// ----------PENDIDIKAN--------------------------------------------------------------- //
// ----------PENDIDIKAN--------------------------------------------------------------- //
	// ----------PENDIDIKAN--------------------------------------------------------------- //
	// ----------PENDIDIKAN--------------------------------------------------------------- //
	// ----------PENDIDIKAN--------------------------------------------------------------- //
	// ----------PENDIDIKAN--------------------------------------------------------------- //
	// ----------PENDIDIKAN--------------------------------------------------------------- //
// ----------PENDIDIKAN--------------------------------------------------------------- //
	// ----------PENDIDIKAN--------------------------------------------------------------- //
	// ----------PENDIDIKAN--------------------------------------------------------------- //
// ----------PENDIDIKAN--------------------------------------------------------------- //
	// ----------PENDIDIKAN--------------------------------------------------------------- //
	// ----------PENDIDIKAN--------------------------------------------------------------- //
// ----------PENDIDIKAN--------------------------------------------------------------- //
	// ----------PENDIDIKAN--------------------------------------------------------------- //
	// ----------PENDIDIKAN--------------------------------------------------------------- //

	public function forminsertdikpegawai (Request $request)
	{
		$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];
		$fileijazah = '';

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		if (isset($request->fileijazah)) {
			$file = $request->fileijazah;

			if ($file->getSize() > 5555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file ijazah terlalu besar (Maksimal 5MB)');     
			}

			if (strtolower($file->getClientOriginalExtension()) != "png" && strtolower($file->getClientOriginalExtension()) != "jpg" && strtolower($file->getClientOriginalExtension()) != "jpeg" && strtolower($file->getClientOriginalExtension()) != "pdf") {
				return redirect('/profil/pegawai')->with('message', 'File yang diunggah harus berbentuk PDF / JPG / JPEG / PNG');     
			}

			$fileijazah .= $request->iddik . "_" . $id_emp . ".". $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefileimg');
			$tujuan_upload .= "\\" . $id_emp . "\\dik\\";

			if (file_exists($tujuan_upload . $fileijazah )) {
				unlink($tujuan_upload . $fileijazah);
			}

			$file->move($tujuan_upload, $fileijazah);
		}
			
		if (!(isset($fileijazah))) {
			$fileijazah = '';
		}

		$insert_emp_dik = [
				// PENDIDIKAN
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'noid' => $id_emp,
				'iddik' => $request->iddik,
				'prog_sek' => ($request->prog_sek ? $request->prog_sek : ''),
				'nm_sek' => ($request->nm_sek ? $request->nm_sek : ''),
				'no_sek' => ($request->no_sek ? $request->no_sek : ''),
				'th_sek' => ($request->th_sek ? $request->th_sek : ''),
				'gelar_dpn_sek' => ($request->gelar_dpn_sek ? $request->gelar_dpn_sek : ''),
				'gelar_blk_sek' => ($request->gelar_blk_sek ? $request->gelar_blk_sek : ''),
				'ijz_cpns' => $request->ijz_cpns,
				'gambar' => $fileijazah,
				'tampilnew' => 1,
			];

		Emp_dik::insert($insert_emp_dik);

		return redirect('/profil/pegawai')
					->with('message', 'Data pendidikan pegawai berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdatedikpegawai (Request $request)
	{
		$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];
		$fileijazah = '';

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		if (isset($request->fileijazah)) {
			$file = $request->fileijazah;

			if ($file->getSize() > 5555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file ijazah terlalu besar (Maksimal 5MB)');     
			}

			if (strtolower($file->getClientOriginalExtension()) != "png" && strtolower($file->getClientOriginalExtension()) != "jpg" && strtolower($file->getClientOriginalExtension()) != "jpeg" && strtolower($file->getClientOriginalExtension()) != "pdf") {
				return redirect('/profil/pegawai')->with('message', 'File yang diunggah harus berbentuk PDF / JPG / JPEG / PNG');     
			}

			$fileijazah .= $request->iddik . "_" . $id_emp . ".". $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefileimg');
			$tujuan_upload .= "\\" . $id_emp . "\\dik\\";

			if (file_exists($tujuan_upload . $fileijazah )) {
				unlink($tujuan_upload . $fileijazah);
			}

			$file->move($tujuan_upload, $fileijazah);
		}
			
		if (!(isset($fileijazah))) {
			$fileijazah = '';
		}

		Emp_dik::where('noid', $id_emp)
			->where('ids', $request->ids)
			->update([
				'iddik' => $request->iddik,
				'prog_sek' => ($request->prog_sek ? $request->prog_sek : ''),
				'nm_sek' => ($request->nm_sek ? $request->nm_sek : ''),
				'no_sek' => ($request->no_sek ? $request->no_sek : ''),
				'th_sek' => ($request->th_sek ? $request->th_sek : ''),
				'gelar_dpn_sek' => ($request->gelar_dpn_sek ? $request->gelar_dpn_sek : ''),
				'gelar_blk_sek' => ($request->gelar_blk_sek ? $request->gelar_blk_sek : ''),
				'ijz_cpns' => $request->ijz_cpns,
				'gambar' => $fileijazah,
			]);

		if ($fileijazah != '') {
			Emp_dik::where('noid', $id_emp)
			->where('ids', $request->ids)
			->update([
				'tampilnew' => 1,
				'gambar' => $fileijazah,
			]);
		}

		return redirect('/profil/pegawai')
					->with('message', 'Data pendidikan pegawai berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletedikpegawai(Request $request)
	{
		$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];

		Emp_dik::where('noid', $id_emp)
		->where('ids', $request->ids)
		->update([
			'sts' => 0,
		]);

		return redirect('/profil/pegawai')
					->with('message', 'Data pendidikan pegawai berhasil dihapus')
					->with('msg_num', 1);
	}

	// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
	// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
	// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
	// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
	// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
	// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
	// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
	// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
	// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
	// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
	// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
	// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
	// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
	// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
	// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
	// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
	// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
	// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
	// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
	// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //
	// ----------PENDIDIKAN NON FORMAL--------------------------------------------------------------------- //

	public function forminsertnonpegawai (Request $request)
	{
		$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];
		$filenon = '';

		$insert_emp_non = [
				// PENDIDIKAN
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'noid' => $id_emp,
				'nm_non' => $request->nm_non,
				'penye_non' => $request->penye_non,
				'thn_non' => ($request->thn_non ? $request->thn_non : ''),
				'durasi_non' => ($request->durasi_non ? $request->durasi_non : '0'),
				'sert_non' => ($request->sert_non ? $request->sert_non : ''),
				'tgl_non' => ($request->tgl_non ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_non))) : ''),
			];

		$nowid = Emp_non::insertGetId($insert_emp_non);

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		if (isset($request->filenon)) {
			$file = $request->filenon;

			if ($file->getSize() > 5555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file ijazah terlalu besar (Maksimal 5MB)');     
			}

			if (strtolower($file->getClientOriginalExtension()) != "png" && strtolower($file->getClientOriginalExtension()) != "jpg" && strtolower($file->getClientOriginalExtension()) != "jpeg" && strtolower($file->getClientOriginalExtension()) != "pdf") {
				return redirect('/profil/pegawai')->with('message', 'File yang diunggah harus berbentuk PDF / JPG / JPEG / PNG');     
			}

			$filenon .= $nowid . "_" . $id_emp . ".". $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefileimg');
			$tujuan_upload .= "\\" . $id_emp . "\\non\\";

			if (file_exists($tujuan_upload . $filenon )) {
				unlink($tujuan_upload . $filenon);
			}

			$file->move($tujuan_upload, $filenon);
		}
			
		if (!(isset($filenon))) {
			$filenon = '';
		}

		if ($filenon != '') {
			Emp_non::where('noid', $id_emp)
			->where('ids', $nowid)
			->update([
				'gambar' => $filenon,
			]);
		}
		

		return redirect('/profil/pegawai')
					->with('message', 'Data pendidikan non formal pegawai berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdatenonpegawai (Request $request)
	{
		$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];
		$filenon = '';

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		if (isset($request->filenon)) {
			$file = $request->filenon;

			if ($file->getSize() > 5555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file ijazah terlalu besar (Maksimal 5MB)');     
			}

			if (strtolower($file->getClientOriginalExtension()) != "png" && strtolower($file->getClientOriginalExtension()) != "jpg" && strtolower($file->getClientOriginalExtension()) != "jpeg" && strtolower($file->getClientOriginalExtension()) != "pdf") {
				return redirect('/profil/pegawai')->with('message', 'File yang diunggah harus berbentuk PDF / JPG / JPEG / PNG');     
			}

			$filenon .= $request->ids . "_" . $id_emp . ".". $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefileimg');
			$tujuan_upload .= "\\" . $id_emp . "\\non\\";

			if (file_exists($tujuan_upload . $filenon )) {
				unlink($tujuan_upload . $filenon);
			}

			$file->move($tujuan_upload, $filenon);
		}
			
		if (!(isset($filenon))) {
			$filenon = '';
		}

		if ($filenon != '') {
			Emp_non::where('noid', $id_emp)
			->where('ids', $nowid)
			->update([
				'gambar' => $filenon,
			]);
		}

		Emp_non::where('noid', $id_emp)
			->where('ids', $request->ids)
			->update([
				'sts' => 1,
				'nm_non' => $request->nm_non,
				'penye_non' => $request->penye_non,
				'thn_non' => ($request->thn_non ? $request->thn_non : ''),
				'durasi_non' => ($request->durasi_non ? $request->durasi_non : '0'),
				'sert_non' => ($request->sert_non ? $request->sert_non : ''),
				'tgl_non' => ($request->tgl_non ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_non))) : ''),
			]);

		return redirect('/profil/pegawai')
					->with('message', 'Data pendidikan non formal berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletenonpegawai(Request $request)
	{
		$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];

		Emp_non::where('noid', $id_emp)
		->where('ids', $request->ids)
		->update([
			'sts' => 0,
		]);

		return redirect('/profil/pegawai')
					->with('message', 'Data pendidikan non formal pegawai berhasil dihapus')
					->with('msg_num', 1);
	}

	// -------GOLONGAN---------------------------------------------------------------------- //
	// -------GOLONGAN---------------------------------------------------------------------- //
	// -------GOLONGAN---------------------------------------------------------------------- //
	// -------GOLONGAN---------------------------------------------------------------------- //
	// -------GOLONGAN---------------------------------------------------------------------- //
	// -------GOLONGAN---------------------------------------------------------------------- //
// -------GOLONGAN---------------------------------------------------------------------- //
	// -------GOLONGAN---------------------------------------------------------------------- //
	// -------GOLONGAN---------------------------------------------------------------------- //
// -------GOLONGAN---------------------------------------------------------------------- //
	// -------GOLONGAN---------------------------------------------------------------------- //
	// -------GOLONGAN---------------------------------------------------------------------- //
// -------GOLONGAN---------------------------------------------------------------------- //
	// -------GOLONGAN---------------------------------------------------------------------- //
	// -------GOLONGAN---------------------------------------------------------------------- //
	// -------GOLONGAN---------------------------------------------------------------------- //
	// -------GOLONGAN---------------------------------------------------------------------- //
	// -------GOLONGAN---------------------------------------------------------------------- //
// -------GOLONGAN---------------------------------------------------------------------- //
	// -------GOLONGAN---------------------------------------------------------------------- //
	// -------GOLONGAN---------------------------------------------------------------------- //
// -------GOLONGAN---------------------------------------------------------------------- //
	// -------GOLONGAN---------------------------------------------------------------------- //
	// -------GOLONGAN---------------------------------------------------------------------- //
// -------GOLONGAN---------------------------------------------------------------------- //
	// -------GOLONGAN---------------------------------------------------------------------- //
	// -------GOLONGAN---------------------------------------------------------------------- //

	public function forminsertgolpegawai (Request $request)
	{
		$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];
		$filegol = '';

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		if (isset($request->filegol)) {
			$file = $request->filegol;

			if ($file->getSize() > 5555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file terlalu besar (Maksimal 5MB)');     
			}

			if (strtolower($file->getClientOriginalExtension()) != "png" && strtolower($file->getClientOriginalExtension()) != "jpg" && strtolower($file->getClientOriginalExtension()) != "jpeg" && strtolower($file->getClientOriginalExtension()) != "pdf") {
				return redirect('/profil/pegawai')->with('message', 'File yang diunggah harus berbentuk PDF / JPG / JPEG / PNG');     
			}

			$filegol .= str_replace("/","",$request->idgol) . "_" . $id_emp . ".". $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefileimg');
			$tujuan_upload .= "\\" . $id_emp . "\\gol\\";

			if (file_exists($tujuan_upload . $filegol )) {
				unlink($tujuan_upload . $filegol);
			}

			$file->move($tujuan_upload, $filegol);
		}
			
		if (!(isset($filegol))) {
			$filegol = '';
		}

		$insert_emp_gol = [
				// GOLONGAN
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'noid' => $request->noid,
				'tmt_gol' => (isset($request->tmt_gol) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_gol))) : null),
				'tmt_sk_gol' => (isset($request->tmt_sk_gol) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_gol))) : null),
				'no_sk_gol' => ($request->no_sk_gol ? $request->no_sk_gol : ''),
				'idgol' => $request->idgol,
				'jns_kp' => $request->jns_kp,
				'mk_thn' => ($request->mk_thn ? $request->mk_thn : 0),
				'mk_bln' => ($request->mk_bln ? $request->mk_bln : 0),
				'gambar' => $filegol,
				'tampilnew' => 1,
			];

		Emp_gol::insert($insert_emp_gol);

		return redirect('/profil/pegawai')
					->with('message', 'Data golongan pegawai berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdategolpegawai (Request $request)
	{
		$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];
		$filegol = '';

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		if (isset($request->filegol)) {
			$file = $request->filegol;

			if ($file->getSize() > 5555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file terlalu besar (Maksimal 5MB)');     
			}

			if (strtolower($file->getClientOriginalExtension()) != "png" && strtolower($file->getClientOriginalExtension()) != "jpg" && strtolower($file->getClientOriginalExtension()) != "jpeg" && strtolower($file->getClientOriginalExtension()) != "pdf") {
				return redirect('/profil/pegawai')->with('message', 'File yang diunggah harus berbentuk PDF / JPG / JPEG / PNG');     
			}

			$filegol .= str_replace("/","",$request->idgol) . "_" . $id_emp . ".". $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefileimg');
			$tujuan_upload .= "\\" . $id_emp . "\\gol\\";

			if (file_exists($tujuan_upload . $filegol )) {
				unlink($tujuan_upload . $filegol);
			}

			$file->move($tujuan_upload, $filegol);
		}
			
		if (!(isset($filegol))) {
			$filegol = '';
		}

		Emp_gol::where('noid', $id_emp)
			->where('ids', $request->ids)
			->update([
				'tmt_gol' => (isset($request->tmt_gol) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_gol))) : null),
				'tmt_sk_gol' => (isset($request->tmt_sk_gol) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_gol))) : null),
				'no_sk_gol' => ($request->no_sk_gol ? $request->no_sk_gol : ''),
				'idgol' => $request->idgol,
				'jns_kp' => $request->jns_kp,
				'mk_thn' => ($request->mk_thn ? $request->mk_thn : 0),
				'mk_bln' => ($request->mk_bln ? $request->mk_bln : 0),
			]);

		if ($filegol != '') {
			Emp_gol::where('noid', $id_emp)
			->where('ids', $request->ids)
			->update([
				'tampilnew' => 1,
				'gambar' => $filegol,
			]);
		}

		return redirect('/profil/pegawai')
					->with('message', 'Data golongan pegawai berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletegolpegawai(Request $request)
	{
		$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];

		$cekcountgol = Emp_gol::where('noid', $id_emp)->where('sts', 1)->count();
		if ($cekcountgol == 1) {
			return redirect('/profil/pegawai')->with('message', 'Tidak dapat menghapus habis golongan pegawai');
		}

		Emp_gol::where('noid', $id_emp)
		->where('ids', $request->ids)
		->update([
			'sts' => 0,
		]);

		return redirect('/profil/pegawai')
					->with('message', 'Data golongan pegawai berhasil dihapus')
					->with('msg_num', 1);
	}

	// -------JABATAN-------------------------------------------------------------------- //
	// -------JABATAN-------------------------------------------------------------------- //
	// -------JABATAN-------------------------------------------------------------------- //
	// -------JABATAN-------------------------------------------------------------------- //
	// -------JABATAN-------------------------------------------------------------------- //
	// -------JABATAN-------------------------------------------------------------------- //
// -------JABATAN-------------------------------------------------------------------- //
	// -------JABATAN-------------------------------------------------------------------- //
	// -------JABATAN-------------------------------------------------------------------- //
// -------JABATAN-------------------------------------------------------------------- //
	// -------JABATAN-------------------------------------------------------------------- //
	// -------JABATAN-------------------------------------------------------------------- //
// -------JABATAN-------------------------------------------------------------------- //
	// -------JABATAN-------------------------------------------------------------------- //
	// -------JABATAN-------------------------------------------------------------------- //
	// -------JABATAN-------------------------------------------------------------------- //
	// -------JABATAN-------------------------------------------------------------------- //
	// -------JABATAN-------------------------------------------------------------------- //
// -------JABATAN-------------------------------------------------------------------- //
	// -------JABATAN-------------------------------------------------------------------- //
	// -------JABATAN-------------------------------------------------------------------- //
// -------JABATAN-------------------------------------------------------------------- //
	// -------JABATAN-------------------------------------------------------------------- //
	// -------JABATAN-------------------------------------------------------------------- //
// -------JABATAN-------------------------------------------------------------------- //
	// -------JABATAN-------------------------------------------------------------------- //
	// -------JABATAN-------------------------------------------------------------------- //

	public function forminsertjabpegawai (Request $request)
	{
		$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];
		$filejab = '';

		// $jabatan = explode("||", $request->jabatan);
		// $jns_jab = $jabatan[0];
		// $idjab = $jabatan[1];

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		if (isset($request->filejab)) {
			$file = $request->filejab;

			if ($file->getSize() > 5555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file terlalu besar (Maksimal 5MB)');     
			}

			if (strtolower($file->getClientOriginalExtension()) != "png" && strtolower($file->getClientOriginalExtension()) != "jpg" && strtolower($file->getClientOriginalExtension()) != "jpeg" && strtolower($file->getClientOriginalExtension()) != "pdf") {
				return redirect('/profil/pegawai')->with('message', 'File yang diunggah harus berbentuk PDF / JPG / JPEG / PNG');     
			}

			$filejab .= str_replace(" ", "", str_replace("/","",strtolower($request->idjab))) . "_" . $request->idunit . "_" . $id_emp . ".". $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefileimg');
			$tujuan_upload .= "\\" . $id_emp . "\\jab\\";

			if (file_exists($tujuan_upload . $filejab )) {
				unlink($tujuan_upload . $filejab);
			}

			$file->move($tujuan_upload, $filejab);
		}
			
		if (!(isset($filejab))) {
			$filejab = '';
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
				'idunit' => $request->idunit,
				'idlok' => $request->idlok,
				'tmt_sk_jab' => (isset($request->tmt_sk_jab) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_jab))) : null),
				'no_sk_jab' => ($request->no_sk_jab ? $request->no_sk_jab : ''),
				'jns_jab' => $request->jns_jab,
				'idjab' => $request->idjab,
				'eselon' => $request->eselon,
				'gambar' => $filejab,
			];

		Emp_jab::insert($insert_emp_jab);

		return redirect('/profil/pegawai')
					->with('message', 'Data jabatan pegawai berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdatejabpegawai(Request $request)
	{
		$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];
		$filejab = '';

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		if (isset($request->filejab)) {
			$file = $request->filejab;

			if ($file->getSize() > 5555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file terlalu besar (Maksimal 5MB)');     
			}

			if (strtolower($file->getClientOriginalExtension()) != "png" && strtolower($file->getClientOriginalExtension()) != "jpg" && strtolower($file->getClientOriginalExtension()) != "jpeg" && strtolower($file->getClientOriginalExtension()) != "pdf") {
				return redirect('/profil/pegawai')->with('message', 'File yang diunggah harus berbentuk PDF / JPG / JPEG / PNG');     
			}

			$filejab .= str_replace(" ", "", str_replace("/","",strtolower($request->idjab))) . "_" . $request->idunit . "_" . $id_emp . ".". $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefileimg');
			$tujuan_upload .= "\\" . $id_emp . "\\jab\\";

			if (file_exists($tujuan_upload . $filejab )) {
				unlink($tujuan_upload . $filejab);
			}

			$file->move($tujuan_upload, $filejab);
		}
			
		if (!(isset($filejab))) {
			$filejab = '';
		}

		Emp_jab::where('noid', $request->noid)
			->where('ids', $request->ids)
			->update([
				'tmt_jab' => (isset($request->tmt_jab) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_jab))) : null),
				'idunit' => $request->idunit,
				'idlok' => $request->idlok,
				'tmt_sk_jab' => (isset($request->tmt_sk_jab) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_jab))) : null),
				'no_sk_jab' => ($request->no_sk_jab ? $request->no_sk_jab : ''),
				'jns_jab' => $request->jns_jab,
				'idjab' => $request->idjab,
				'eselon' => $request->eselon,
				// 'tampilnew' => 1,
			]);

		if ($filejab != '') {
			Emp_jab::where('noid', $id_emp)
			->where('ids', $request->ids)
			->update([
				'gambar' => $filejab,
			]);
		}

		return redirect('/profil/pegawai')
					->with('message', 'Data jabatan pegawai berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletejabpegawai(Request $request)
	{
		$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];

		$cekcountjab = Emp_jab::where('noid', $id_emp)->where('sts', 1)->count();
		if ($cekcountjab == 1) {
			return redirect('/profil/pegawai')->with('message', 'Tidak dapat menghapus habis jabatan pegawai');
		}

		Emp_jab::where('noid', $id_emp)
		->where('ids', $request->ids)
		->update([
			'sts' => 0,
		]);

		return redirect('/profil/pegawai')
					->with('message', 'Data jabatan pegawai berhasil dihapus')
					->with('msg_num', 1);
	}

	// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
	// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
	// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
	// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
	// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
	// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
	// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
	// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
	// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
	// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
	// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
	// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
	// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
	// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
	// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
	// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
	// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
	// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
	// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
	// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //
	// -------HUKUMAN DISIPLIN-------------------------------------------------------------------- //

	public function forminserthukpegawai (Request $request)
	{
		$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];
		$filehuk = '';

		$insert_emp_huk = [
				// PENDIDIKAN
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'noid' => $id_emp,
				'jns_huk' => $request->jns_huk,
				'tgl_mulai' => ($request->tgl_mulai ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_mulai))) : ''),
				'tgl_akhir' => ($request->tgl_akhir ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_akhir))) : ''),
				'no_sk' => $request->no_sk ?? '-',
				'tgl_sk' => ($request->tgl_sk ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_sk))) : ''),
			];

		$nowid = Emp_huk::insertGetId($insert_emp_huk);

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		if (isset($request->filehuk)) {
			
			$file = $request->filehuk;

			if ($file->getSize() > 5555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file terlalu besar (Maksimal 5MB)');     
			}

			if (strtolower($file->getClientOriginalExtension()) != "png" && strtolower($file->getClientOriginalExtension()) != "jpg" && strtolower($file->getClientOriginalExtension()) != "jpeg" && strtolower($file->getClientOriginalExtension()) != "pdf") {
				return redirect('/profil/pegawai')->with('message', 'File yang diunggah harus berbentuk PDF / JPG / JPEG / PNG');     
			}

			$filehuk .= $nowid . "_" . $id_emp . ".". $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefileimg');
			$tujuan_upload .= "\\" . $id_emp . "\\huk\\";

			if (file_exists($tujuan_upload . $filehuk )) {
				unlink($tujuan_upload . $filehuk);
			}

			$file->move($tujuan_upload, $filehuk);
		}
			
		if (!(isset($filehuk))) {
			$filehuk = '';
		}

		if ($filehuk != '') {
			Emp_huk::where('noid', $id_emp)
			->where('ids', $nowid)
			->update([
				'gambar' => $filehuk,
			]);
		}
		

		return redirect('/profil/pegawai')
					->with('message', 'Data hukuman disiplin pegawai berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdatehukpegawai (Request $request)
	{
		$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];
		$filehuk = '';

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		if (isset($request->filehuk)) {
			$file = $request->filehuk;

			if ($file->getSize() > 5555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file terlalu besar (Maksimal 5MB)');     
			}

			if (strtolower($file->getClientOriginalExtension()) != "png" && strtolower($file->getClientOriginalExtension()) != "jpg" && strtolower($file->getClientOriginalExtension()) != "jpeg" && strtolower($file->getClientOriginalExtension()) != "pdf") {
				return redirect('/profil/pegawai')->with('message', 'File yang diunggah harus berbentuk PDF / JPG / JPEG / PNG');     
			}

			$filehuk .= $request->ids . "_" . $id_emp . ".". $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefileimg');
			$tujuan_upload .= "\\" . $id_emp . "\\huk\\";

			if (file_exists($tujuan_upload . $filehuk )) {
				unlink($tujuan_upload . $filehuk);
			}

			$file->move($tujuan_upload, $filehuk);
		}
			
		if (!(isset($filehuk))) {
			$filehuk = '';
		}

		if ($filehuk != '') {
			Emp_huk::where('noid', $id_emp)
			->where('ids', $request->ids)
			->update([
				'gambar' => $filehuk,
			]);
		}

		Emp_huk::where('noid', $id_emp)
			->where('ids', $request->ids)
			->update([
				'jns_huk' => $request->jns_huk,
				'tgl_mulai' => ($request->tgl_mulai ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_mulai))) : ''),
				'tgl_akhir' => ($request->tgl_akhir ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_akhir))) : ''),
				'no_sk' => $request->no_sk ?? '-',
				'tgl_sk' => ($request->tgl_sk ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_sk))) : ''),
			]);

		return redirect('/profil/pegawai')
					->with('message', 'Data hukuman disiplin berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletehukpegawai(Request $request)
	{
		$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];

		Emp_huk::where('noid', $id_emp)
		->where('ids', $request->ids)
		->update([
			'sts' => 0,
		]);

		return redirect('/profil/pegawai')
					->with('message', 'Data hukuman disiplin pegawai berhasil dihapus')
					->with('msg_num', 1);
	}

	// ------------------------------------------------------------------------------- //
	// ------------------------------------------------------------------------------- //
	// ------------------------------------------------------------------------------- //
	// ------------------------------------------------------------------------------- //
	// ------------------------------------------------------------------------------- //
	// ------------------------------------------------------------------------------- //
// ------------------------------------------------------------------------------- //
	// ------------------------------------------------------------------------------- //
	// ------------------------------------------------------------------------------- //
// ------------------------------------------------------------------------------- //
	// ------------------------------------------------------------------------------- //
	// ------------------------------------------------------------------------------- //
// ------------------------------------------------------------------------------- //
	// ------------------------------------------------------------------------------- //
	// ------------------------------------------------------------------------------- //
	// ------------------------------------------------------------------------------- //
	// ------------------------------------------------------------------------------- //
	// ------------------------------------------------------------------------------- //
// ------------------------------------------------------------------------------- //
	// ------------------------------------------------------------------------------- //
	// ------------------------------------------------------------------------------- //
// ------------------------------------------------------------------------------- //
	// ------------------------------------------------------------------------------- //
	// ------------------------------------------------------------------------------- //
// ------------------------------------------------------------------------------- //
	// ------------------------------------------------------------------------------- //
	// ------------------------------------------------------------------------------- //

	public function disposisi(Request $request)
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

		if ($request->signnow) {
			$signnow = $request->signnow;
		} else {
			$signnow = "=";
		}

		$idgroup = $_SESSION['user_data']['idgroup'];
		if (substr($idgroup, 0, 8) == 'EMPLOYEE' || $idgroup == 'ADMIN DIA' || $idgroup == 'TYPIST') {
			$disposisiinboxs = DB::select( DB::raw("SELECT top 500 disp.*, emp1.nm_emp as from_pm, emp2.nm_emp as to_pm, disp.from_pm as from_id, disp.to_pm as to_id
										  from bpaddtfake.dbo.fr_disposisi disp
										  left join bpaddtfake.dbo.emp_data emp1 on disp.from_pm = emp1.id_emp
										  left join bpaddtfake.dbo.emp_data emp2 on disp.to_pm = emp2.id_emp
										  where no_form in (SELECT distinct(no_form)
										  FROM [bpaddtfake].[dbo].[fr_disposisi]
										  where to_pm = '".$_SESSION['user_data']['id_emp'] ."')
										  and disp.sts = 1
										  and year(tgl_masuk) $signnow $yearnow
										  order by disp.no_form DESC, disp.ids ASC") );

			if (strlen($_SESSION['user_data']['idunit']) == 10) {
				$disposisiends = DB::select( DB::raw("SELECT top 500 disp.*, emp1.nm_emp as from_pm, emp2.nm_emp as to_pm, disp.from_pm as from_id, disp.to_pm as to_id
										  from bpaddtfake.dbo.fr_disposisi disp
										  left join bpaddtfake.dbo.emp_data emp1 on disp.from_pm = emp1.id_emp
										  left join bpaddtfake.dbo.emp_data emp2 on disp.to_pm = emp2.id_emp
										  where no_form in (SELECT distinct(no_form)
										  FROM [bpaddtfake].[dbo].[fr_disposisi]
										  where to_pm = '".$_SESSION['user_data']['id_emp'] ."')
										  and disp.sts = 1
										  and year(tgl_masuk) $signnow $yearnow
										  order by disp.no_form DESC, disp.ids ASC") );
				$disposisisents = 0;
			} else {
				$disposisisents = DB::select( DB::raw("SELECT top 500 disp.*, emp1.nm_emp as from_pm, emp2.nm_emp as to_pm, disp.from_pm as from_id, disp.to_pm as to_id
										  from bpaddtfake.dbo.fr_disposisi disp
										  left join bpaddtfake.dbo.emp_data emp1 on disp.from_pm = emp1.id_emp
										  left join bpaddtfake.dbo.emp_data emp2 on disp.to_pm = emp2.id_emp
										  where no_form in (SELECT distinct(no_form)
										  FROM [bpaddtfake].[dbo].[fr_disposisi]
										  where from_pm = '".$_SESSION['user_data']['id_emp'] ."')
										  and disp.sts = 1
										  and year(tgl_masuk) $signnow $yearnow
										  order by disp.no_form DESC, disp.ids ASC") );
				$disposisiends = 0;
			}

			$isEmployee = 1;
			$disposisiinboxs = json_decode(json_encode($disposisiinboxs), true);
			$disposisisents = json_decode(json_encode($disposisisents), true);
			$disposisiends = json_decode(json_encode($disposisiends), true);
			$disposisis = 0;
			$disposisisdraft = 0;
		} else {
			$disposisis = DB::select( DB::raw("SELECT TOP 500 *
										  from bpaddtfake.dbo.fr_disposisi
										  where (kode_disposisi is not null 
										  and kode_disposisi != '')
										  and sts = 1
										  and year(tgl_masuk) $signnow $yearnow
										  and status_surat = 's'
										  order by no_form DESC") );
			$isEmployee = 0;
			$disposisis = json_decode(json_encode($disposisis), true);
			$disposisiinboxs = 0;
			$disposisisents = 0;
			$disposisiends = 0;

			$disposisisdraft = DB::select( DB::raw("SELECT TOP 500 *
										  from bpaddtfake.dbo.fr_disposisi
										  where (kode_disposisi is not null 
										  and kode_disposisi != '')
										  and sts = 1
										  and status_surat = 'd'
										  order by no_form DESC") );
			$disposisisdraft = json_decode(json_encode($disposisisdraft), true);
		}

		return view('pages.bpadprofil.disposisi')
				->with('access', $access)
				->with('disposisis', $disposisis)
				->with('disposisisdraft', $disposisisdraft)
				->with('disposisiinboxs', $disposisiinboxs)
				->with('disposisisents', $disposisisents)
				->with('disposisiends', $disposisiends)
				->with('signnow', $signnow)
				->with('yearnow', $yearnow)
				->with('isEmployee', $isEmployee);
	}

	public function display_disposisi($no_form, $idtop, $level = 0)
	{
		// $query = Fr_disposisi::
		// 			leftJoin('bpaddtfake.dbo.emp_data as emp1', 'emp1.id_emp', '=', 'bpaddtfake.dbo.fr_disposisi.to_pm')
		// 			->where('no_form', $no_form)
		// 			->where('idtop', $idtop)
		// 			->orderBy('ids')
		// 			->get();

		$query = DB::select( DB::raw("SELECT * 
					from bpaddtfake.dbo.fr_disposisi
					left join bpaddtfake.dbo.emp_data on bpaddtfake.dbo.emp_data.id_emp = bpaddtfake.dbo.fr_disposisi.to_pm
					where no_form = '$no_form'
					and idtop = '$idtop'
					order by ids
					") );
		$query = json_decode(json_encode($query), true);

		$result = '';

		if (count($query) > 0) {
			foreach ($query as $log) {
				$padding = ($level * 20);
				$result .= '<tr >
								<td style="padding-left:'.$padding.'px; padding-top:10px">
									<span class="fa fa-user"></span> <span>'.$log['nrk_emp'].' '.ucwords(strtolower($log['nm_emp'])).'</span> <br> 
									<span class="text-muted"> Penanganan: <b>'. ($log['penanganan_final'] ? $log['penanganan_final'] : ($log['penanganan_final'] ? $log['penanganan_final'] : $log['penanganan'] )) .'</b></span><br>
								</td>
							</tr>';

				if ($log['child'] == 1) {
					$result .= $this->display_disposisi($no_form, $log['ids'], $level+1);
				}
			}
		}
		return $result;
	}

	public function disposisilihat (Request $request)
	{
		$this->checkSessionTime();

		if (Auth::user()->id_emp == $request->to_id) {
			$rd_status = Fr_disposisi::
							where('ids', $request->ids)
							->first(['rd']);

			if ($rd_status['rd'] != 'S') {
				Fr_disposisi::
					where('ids', $request->ids)
					->update([
						'usr_rd' => Auth::user()->id_emp,
						'tgl_rd' => date('Y-m-d'),
						'rd' => 'Y',
					]);
			}
		}

		$opendisposisi = Fr_disposisi::
							join('bpaddtfake.dbo.glo_disposisi_kode', 'bpaddtfake.dbo.glo_disposisi_kode.kd_jnssurat', '=', 'bpaddtfake.dbo.fr_disposisi.kode_disposisi')
							->where('no_form', $request->no_form)
							->orderBy('ids')
							->get();

		if ($request->asal_form == 'sent' && $request->to_id == Auth::user()->id_emp) {
			$id_now = $request->ids;
			$id_child = $request->ids;
		} elseif ($request->asal_form == 'sent' && $request->to_id != Auth::user()->id_emp) {
			$id_now = $request->idtop;
			$id_child = $request->idtop;
		} elseif ($request->asal_form == 'inbox') {
			$id_now = $request->ids;
			$id_child = $request->ids;
		}

		if (isset(Auth::user()->usname)) {
			$id_now = $request->ids;
			$id_child = $request->ids;
		}

		$openpenanganannow = Fr_disposisi::
							where('ids', $id_now)
							->first();

		$openpenangananchild = Fr_disposisi::
							where('idtop', $id_child)
							->first();

		$treedisp = '<tr>
						<td>
							<span class="fa fa-book"></span> <span>'.$opendisposisi[0]['no_form'].'</span> <br>
							<span class="text-muted">Kode: '.$opendisposisi[0]['kode_disposisi'].'</span> | <span class="text-muted"> Nomor: '.$opendisposisi[0]['no_surat'].'</span><br>

						</td>
					</tr>';

		$treedisp .= $this->display_disposisi($request->no_form, $opendisposisi[0]['ids']);

		$kddispos = Glo_disposisi_kode::orderBy('kd_jnssurat')->get();

		if ($_SESSION['user_data']['child'] == 1 || is_null($_SESSION['user_data']['id_emp'])) {

			if (is_null($_SESSION['user_data']['id_emp'])) {
				$idunits = '%';
			} else {
				$idunits = $_SESSION['user_data']['idunit'];
			}

			$stafs = DB::select( DB::raw("
						SELECT id_emp, nrk_emp, nip_emp, nm_emp, tbjab.idjab, tbjab.idunit, tbunit.child from bpaddtfake.dbo.emp_data as a
						CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
						CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
						,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
							and tbunit.sao like '".$idunits."%' and ked_emp = 'aktif' order by nm_emp") );
			$stafs = json_decode(json_encode($stafs), true);
		} else {
			$stafs = 0;
		}

		if (is_null($_SESSION['user_data']['id_emp'])) {
			$jabatans = Glo_org_jabatan::
					where('jabatan',  'like', '%Kepala Badan%')
					->get();
		} else {
			$idunit = $_SESSION['user_data']['idunit'];
			$jabatans = DB::select( DB::raw("
						SELECT id_emp, nm_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit, tbunit.notes from bpaddtfake.dbo.emp_data as a
						CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM  bpaddtfake.dbo.emp_gol,bpaddtfake.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
						CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
						CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpaddtfake.dbo.emp_dik,bpaddtfake.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
						CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
						,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
						and tbunit.sao like '$idunit%' AND LEN(idunit) < 10 AND ked_emp = 'AKTIF'
						ORDER BY idunit ASC, idjab ASC
						"));
			$jabatans = json_decode(json_encode($jabatans), true);
		}

		$penanganans = Glo_disposisi_penanganan::
						orderBy('urut')
						->get();

		$idgroup = $_SESSION['user_data']['idgroup'];
		if (substr($idgroup, 0, 8) == 'EMPLOYEE' || $idgroup == 'ADMIN DIA' || $idgroup == 'TYPIST') {
			$isEmployee = 1;
		} else {
			$isEmployee = 0;
		}	

		return view('pages.bpadprofil.lihatdisposisi')
				->with('opendisposisi', $opendisposisi)
				->with('openpenanganannow', $openpenanganannow)
				->with('openpenangananchild', $openpenangananchild)
				->with('kddispos', $kddispos)
				->with('stafs', $stafs)
				->with('jabatans', $jabatans)
				->with('penanganans', $penanganans)
				->with('isEmployee', $isEmployee)
				->with('ids', $request->ids)
				->with('no_form', $request->no_form)
				->with('idtop', $request->idtop)
				->with('to_id', $request->to_id)
				->with('asal_form', $request->asal_form)
				->with('treedisp', $treedisp);
	}

	public function disposisitambah (Request $request)
	{
		$this->checkSessionTime();

		$maxnoform = Fr_disposisi::max('no_form');
		$kddispos = Glo_disposisi_kode::orderBy('kd_jnssurat')->get();

		if ($_SESSION['user_data']['child'] == 1 || is_null($_SESSION['user_data']['id_emp'])) {

			if (is_null($_SESSION['user_data']['id_emp'])) {
				$idunits = '%';
			} else {
				$idunits = $_SESSION['user_data']['idunit'];
			}

			$stafs = DB::select( DB::raw("
						SELECT id_emp,a.uname+'::'+convert(varchar,a.tgl)+'::'+a.ip,createdate,nip_emp,nrk_emp,nm_emp,nrk_emp+'-'+nm_emp as c2,gelar_dpn,gelar_blk,jnkel_emp,tempat_lahir,tgl_lahir,CONVERT(VARCHAR(10), tgl_lahir, 103) AS [DD/MM/YYYY],idagama,alamat_emp,tlp_emp,email_emp,status_emp,ked_emp,status_nikah,gol_darah,nm_bank,cb_bank,an_bank,nr_bank,no_taspen,npwp,no_askes,no_jamsos,tgl_join,CONVERT(VARCHAR(10), tgl_join, 103) AS [DD/MM/YYYY],tgl_end,reason,a.idgroup,pass_emp,foto,ttd,a.telegram_id,a.lastlogin,tbgol.tmt_gol,CONVERT(VARCHAR(10), tbgol.tmt_gol, 103) AS [DD/MM/YYYY],tbgol.tmt_sk_gol,CONVERT(VARCHAR(10), tbgol.tmt_sk_gol, 103) AS [DD/MM/YYYY],tbgol.no_sk_gol,tbgol.idgol,tbgol.jns_kp,tbgol.mk_thn,tbgol.mk_bln,tbgol.gambar,tbgol.nm_pangkat,tbjab.tmt_jab,CONVERT(VARCHAR(10), tbjab.tmt_jab, 103) AS [DD/MM/YYYY],tbjab.idskpd,tbjab.idunit,tbjab.idjab, tbunit.child, tbjab.idlok,tbjab.tmt_sk_jab,CONVERT(VARCHAR(10), tbjab.tmt_sk_jab, 103) AS [DD/MM/YYYY],tbjab.no_sk_jab,tbjab.jns_jab,tbjab.idjab,tbjab.eselon,tbjab.gambar,tbdik.iddik,tbdik.prog_sek,tbdik.no_sek,tbdik.th_sek,tbdik.nm_sek,tbdik.gelar_dpn_sek,tbdik.gelar_blk_sek,tbdik.ijz_cpns,tbdik.gambar,tbdik.nm_dik,b.nm_skpd,c.nm_unit,c.notes,d.nm_lok FROM bpaddtfake.dbo.emp_data as a
							CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM bpaddtfake.dbo.emp_gol,bpaddtfake.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
							CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
							CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpaddtfake.dbo.emp_dik,bpaddtfake.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
							CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
							,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
							and idunit like '".$idunits."%' and ked_emp = 'aktif' order by nm_emp") );
			$stafs = json_decode(json_encode($stafs), true);
		} else {
			$stafs = 0;
		}

		if (is_null($_SESSION['user_data']['id_emp'])) {
			$jabatans = Glo_org_jabatan::
					where('jabatan',  'like', '%Kepala Badan%')
					->get();
		} else {
			$jabatans = DB::select( DB::raw("
						SELECT *
						from bpaddtfake.dbo.glo_org_jabatan org
						cross apply (select top 1 * from bpaddtfake.dbo.emp_jab as jab where org.jabatan = jab.idjab) resjab 
						where (LEFT(org.jabatan, 6) = 'kepala' or LEFT(jabatan, 5) = 'sekre')
						order by tmt_jab desc, resjab.idjab asc
						"));	
			$jabatans = json_decode(json_encode($jabatans), true);
		}

		$penanganans = Glo_disposisi_penanganan::
						orderBy('urut')
						->get();

		return view('pages.bpadprofil.tambahdisposisi')
				->with('maxnoform', $maxnoform)
				->with('kddispos', $kddispos)
				->with('stafs', $stafs)
				->with('jabatans', $jabatans)
				->with('penanganans', $penanganans);
	}

	public function formviewdisposisi(Request $request)
	{
		$this->checkSessionTime();
		//kalo dia orang TU brarti ngubah form doang

		if (is_null($_SESSION['user_data']['id_emp'])) {

			$file = $request->nm_file;

			if (count($file) <= 1) {
				if (isset($request->nm_file)) {
					$filedispo = 'disp';
					$file = $request->nm_file;

					if ($file->getSize() > 52222222) {
						return redirect('/profil/lihat disposisi')->with('message', 'Ukuran file terlalu besar (Maksimal 2MB)');     
					} 

					$filedispo .= date('dmYHis');
					$filedispo .= ".". $file->getClientOriginalExtension();

					$tujuan_upload = config('app.savefiledisposisi');
					$file->move($tujuan_upload, $filedispo);
				}
			} else {
				$filedispo = '';
				foreach ($file as $key => $data) {
					$filenow = 'disp';

					if ($data->getSize() > 52222222) {
						return redirect('/profil/lihat disposisi')->with('message', 'Ukuran file terlalu besar (Maksimal 2MB)');     
					} 

					$filenow .= $key;
					$filenow .= date('dmYHis');
					$filenow .= ".". $data->getClientOriginalExtension();

					$tujuan_upload = config('app.savefiledisposisi');
					$data->move($tujuan_upload, $filenow);

					if ($key != count($file) - 1) {
						$filedispo .= $filenow . "::";
					} else {
						$filedispo .= $filenow;
					}
				}
			}	
				
			if (!(isset($filedispo))) {
				$filedispo = null;
			}

			// if (!(isset($filetambahan))) {
			// 	$filetambahan = null;
			// }

			if ($request->sifat1_surat == null) {
				$sifat1 = '';
			} else {
				$sifat1 = $request->sifat1_surat;
			}

			if ($request->sifat2_surat == null) {
				$sifat2 = '';
			} else {
				$sifat2 = $request->sifat2_surat;
			}

			if (isset($request->btnDraft)) {
				Fr_disposisi::where('ids', $request->ids)
					->update([
						'tgl_masuk' => date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_masuk))),
						'usr_input' => (isset(Auth::user()->usname) ? Auth::user()->usname : Auth::user()->id_emp),
						'tgl_input' => date('Y-m-d H:i:s'),
						'no_index' => $request->no_index,
						'kode_disposisi' => $request->kode_disposisi,
						'perihal' => $request->perihal,
						'tgl_surat' => $request->tgl_surat,
						'no_surat' => $request->no_surat,
						'asal_surat' => $request->asal_surat,
						'kepada_surat' => $request->kepada_surat,
						'sifat1_surat' => $sifat1,
						'sifat2_surat' => $sifat2,
						'ket_lain' => $request->ket_lain,
						'status_surat' => 'd',
						'no_form' => $request->no_form,
					]);

				if (isset($filedispo)) {
					Fr_disposisi::where('ids', $request->ids)
					->update([
						'nm_file' => $filedispo,
					]);
				}

				return redirect('/profil/disposisi')
						->with('message', 'Disposisi berhasil diubah')
						->with('msg_num', 1);

			} elseif (isset($request->btnKirim)) {
				Fr_disposisi::where('ids', $request->ids)
					->update([
						'tgl_masuk' => date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_masuk))),
						'usr_input' => (isset(Auth::user()->usname) ? Auth::user()->usname : Auth::user()->id_emp),
						'tgl_input' => date('Y-m-d H:i:s'),
						'no_index' => $request->no_index,
						'kode_disposisi' => $request->kode_disposisi,
						'perihal' => $request->perihal,
						'tgl_surat' => $request->tgl_surat,
						'no_surat' => $request->no_surat,
						'asal_surat' => $request->asal_surat,
						'kepada_surat' => $request->kepada_surat,
						'sifat1_surat' => $sifat1,
						'sifat2_surat' => $sifat2,
						'ket_lain' => $request->ket_lain,
						'status_surat' => 's',
						'no_form' => $request->no_form,
					]);

				if (isset($filedispo)) {
					Fr_disposisi::where('ids', $request->ids)
					->update([
						'nm_file' => $filedispo,
					]);
				}

				$findidemp = DB::select( DB::raw("
						SELECT id_emp,a.uname+'::'+convert(varchar,a.tgl)+'::'+a.ip,createdate,nip_emp,nrk_emp,nm_emp,nrk_emp+'-'+nm_emp as c2,gelar_dpn,gelar_blk,jnkel_emp,tempat_lahir,tgl_lahir,CONVERT(VARCHAR(10), tgl_lahir, 103) AS [DD/MM/YYYY],idagama,alamat_emp,tlp_emp,email_emp,status_emp,ked_emp,status_nikah,gol_darah,nm_bank,cb_bank,an_bank,nr_bank,no_taspen,npwp,no_askes,no_jamsos,tgl_join,CONVERT(VARCHAR(10), tgl_join, 103) AS [DD/MM/YYYY],tgl_end,reason,a.idgroup,pass_emp,foto,ttd,a.telegram_id,a.lastlogin,tbgol.tmt_gol,CONVERT(VARCHAR(10), tbgol.tmt_gol, 103) AS [DD/MM/YYYY],tbgol.tmt_sk_gol,CONVERT(VARCHAR(10), tbgol.tmt_sk_gol, 103) AS [DD/MM/YYYY],tbgol.no_sk_gol,tbgol.idgol,tbgol.jns_kp,tbgol.mk_thn,tbgol.mk_bln,tbgol.gambar,tbgol.nm_pangkat,tbjab.tmt_jab,CONVERT(VARCHAR(10), tbjab.tmt_jab, 103) AS [DD/MM/YYYY],tbjab.idskpd,tbjab.idunit,tbjab.idjab, tbunit.child, tbjab.idlok,tbjab.tmt_sk_jab,CONVERT(VARCHAR(10), tbjab.tmt_sk_jab, 103) AS [DD/MM/YYYY],tbjab.no_sk_jab,tbjab.jns_jab,tbjab.idjab,tbjab.eselon,tbjab.gambar,tbdik.iddik,tbdik.prog_sek,tbdik.no_sek,tbdik.th_sek,tbdik.nm_sek,tbdik.gelar_dpn_sek,tbdik.gelar_blk_sek,tbdik.ijz_cpns,tbdik.gambar,tbdik.nm_dik,b.nm_skpd,c.nm_unit,c.notes,d.nm_lok FROM bpaddtfake.dbo.emp_data as a
							CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM  bpaddtfake.dbo.emp_gol,bpaddtfake.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
							CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
							CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpaddtfake.dbo.emp_dik,bpaddtfake.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
							CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
							,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
							and tbunit.kd_unit like '01' and ked_emp = 'aktif' order by nm_emp") )[0];
				$findidemp = json_decode(json_encode($findidemp), true);

				$insertsurat = [
					'sts' => 1,
					'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
					'tgl'       => date('Y-m-d H:i:s'),
					'ip'        => '',
					'logbuat'   => '',
					'kd_skpd'	=> '1.20.512',
					'kd_unit'	=> '01',
					'no_form' => $request->no_form,
					'kd_surat' => '',
					'status_surat' => '',
					'idtop' => $request->ids,
					'tgl_masuk' => (isset($request->tgl_masuk) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_masuk))) : date('Y-m-d')),
					'usr_input' => '',
					'tgl_input' => null,
					'no_index' => '',
					'kode_disposisi' => '',
					'perihal' => '',
					'tgl_surat' => '',
					'no_surat' => '',
					'asal_surat' => '',
					'kepada_surat' => '',
					'sifat1_surat' => '',
					'sifat2_surat' => '',
					'ket_lain' => '',
					'nm_file' => '',
					'kepada' => ($findidemp['idjab'] ? $findidemp['idjab'] : ''),
					'noid' => '',
					'penanganan' => ($request->penanganan ? $request->penanganan : ''),
					'catatan' => ($request->catatan ? $request->catatan : ''),
					'from_user' => '',
					'from_pm' => (isset(Auth::user()->usname) ? Auth::user()->usname : Auth::user()->id_emp),
					'to_user' => '',
					'to_pm' => $findidemp['id_emp'],
					'rd' => 'N',
					'usr_rd' => '',
					'tgl_rd' => null,
					'selesai' => 'Y',
					'child' => 0,
				];
				Fr_disposisi::insert($insertsurat);

				return redirect('/profil/disposisi')
						->with('message', 'Disposisi berhasil diubah')
						->with('msg_num', 1);

			} else {
				Fr_disposisi::where('ids', $request->ids)
					->update([
						'tgl_masuk' => date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_masuk))),
						'usr_input' => (isset(Auth::user()->usname) ? Auth::user()->usname : Auth::user()->id_emp),
						'tgl_input' => date('Y-m-d H:i:s'),
						'no_index' => $request->no_index,
						'kode_disposisi' => $request->kode_disposisi,
						'perihal' => $request->perihal,
						'tgl_surat' => $request->tgl_surat,
						'no_surat' => $request->no_surat,
						'asal_surat' => $request->asal_surat,
						'kepada_surat' => $request->kepada_surat,
						'sifat1_surat' => $sifat1,
						'sifat2_surat' => $sifat2,
						'ket_lain' => $request->ket_lain,
					]);

				// var_dump($request->no_form);
				// die();

				// Fr_disposisi::where('no_form', $request->prevnoform)
				// ->update([
				// 	'no_form' => $request->no_form,
				// ]);

				$update = DB::update( DB::raw("
					UPDATE fr_disposisi
					set	no_form = '$request->no_form'
					where no_form = '$request->prevnoform'					
				") );

				if (isset($filedispo)) {
					Fr_disposisi::where('ids', $request->ids)
					->update([
						'nm_file' => $filedispo,
					]);
				}

				return redirect('/profil/disposisi')
						->with('message', 'Disposisi berhasil diubah')
						->with('msg_num', 1);
			}

			
		} else {
			//kalo dia pegawai brarti lanjutin disposisi
			if (is_null($request->jabatans) && is_null($request->stafs)) {
				Fr_disposisi::where('ids', $request->ids)
				->update([
					'penanganan_final' => $request->penanganan,
					'catatan_final' => $request->catatan,
					'rd' => 'S',
					'usr_input' => Auth::user()->id_emp,
					'tgl_input' => date('Y-m-d H:i:s'),
				]);

				return redirect('/profil/disposisi')
					->with('message', 'Disposisi berhasil dilanjutkan')
					->with('msg_num', 1);
			} else {

				if ($request->cekasal == 'inbox') {
					$idtop = $request->ids;

					Fr_disposisi::where('ids', $request->ids)
					->update([
						'penanganan_final' => $request->penanganan,
						'catatan_final' => $request->catatan,
						'usr_input' => Auth::user()->id_emp,
						'tgl_input' => date('Y-m-d H:i:s'),
						'rd' => 'S',
						'selesai' => '',
						'child' => 1,
					]);
				} elseif ($request->cekasal == 'sent' && $request->cekto_id == Auth::user()->id_emp) {
					$idtop = $request->ids;

					Fr_disposisi::where('ids', $request->ids)
					->update([
						'penanganan_final' => $request->penanganan,
						'catatan_final' => $request->catatan,
						'usr_input' => Auth::user()->id_emp,
						'tgl_input' => date('Y-m-d H:i:s'),
						'rd' => 'S',
						'selesai' => '',
						'child' => 1,
					]);
				} elseif ($request->cekasal == 'sent' && $request->cekto_id != Auth::user()->id_emp) {
					$idtop = $request->cekidtop;

					Fr_disposisi::where('ids', $request->cekidtop)
					->update([
						'penanganan_final' => $request->penanganan,
						'catatan_final' => $request->catatan,
						'usr_input' => Auth::user()->id_emp,
						'tgl_input' => date('Y-m-d H:i:s'),
						'rd' => 'S',
						'selesai' => '',
						'child' => 1,
					]);
				}

				$id_emp_array = [];

				if ($request->jabatans) {

					foreach ($request->jabatans as $jabatan) {
						$kepada = explode("||", $jabatan)[0];
						$to_pm = explode("||", $jabatan)[1];

						$insertsurat = [
							'sts' => 1,
							'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
							'tgl'       => date('Y-m-d H:i:s'),
							'ip'        => '',
							'logbuat'   => '',
							'kd_skpd'	=> '1.20.512',
							'kd_unit'	=> '01',
							'no_form' => $request->prevnoform,
							'kd_surat' => '',
							'status_surat' => '',
							'idtop' => $idtop,
							'tgl_masuk' => (isset($request->tgl_masuk) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_masuk))) : null),
							'usr_input' => '',
							'tgl_input' => null,
							'no_index' => '',
							'kode_disposisi' => '',
							'perihal' => '',
							'tgl_surat' => '',
							'no_surat' => '',
							'asal_surat' => '',
							'kepada_surat' => '',
							'sifat1_surat' => '',
							'sifat2_surat' => '',
							'ket_lain' => '',
							'nm_file' => '',
							'kepada' => $kepada,
							'noid' => '',
							'penanganan' => ($request->penanganan ? $request->penanganan : ''),
							'catatan' => ($request->catatan ? $request->catatan : ''),
							'from_user' => '',
							'from_pm' => (isset(Auth::user()->usname) ? Auth::user()->usname : Auth::user()->id_emp),
							'to_user' => '',
							'to_pm' => $to_pm,
							'rd' => 'N',
							'usr_rd' => '',
							'tgl_rd' => null,
							'selesai' => 'Y',
							'child' => 0,
						];

						array_push($id_emp_array, $to_pm);
						Fr_disposisi::insert($insertsurat);
					}
				}

				if ($request->stafs) {
					foreach ($request->stafs as $staf) {
						if (!(in_array($staf, $id_emp_array))) {
							$insertsurat = [
								'sts' => 1,
								'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
								'tgl'       => date('Y-m-d H:i:s'),
								'ip'        => '',
								'logbuat'   => '',
								'kd_skpd'	=> '1.20.512',
								'kd_unit'	=> '01',
								'no_form' => $request->no_form,
								'kd_surat' => '',
								'status_surat' => '',
								'idtop' => $idtop,
								'tgl_masuk' => (isset($request->tgl_masuk) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_masuk))) : null),
								'usr_input' => '',
								'tgl_input' => null,
								'no_index' => '',
								'kode_disposisi' => '',
								'perihal' => '',
								'tgl_surat' => '',
								'no_surat' => '',
								'asal_surat' => '',
								'kepada_surat' => '',
								'sifat1_surat' => '',
								'sifat2_surat' => '',
								'ket_lain' => '',
								'nm_file' => '',
								'kepada' => '',
								'noid' => '',
								'penanganan' => ($request->penanganan ? $request->penanganan : ''),
								'catatan' => ($request->catatan ? $request->catatan : ''),
								'from_user' => '',
								'from_pm' => (isset(Auth::user()->usname) ? Auth::user()->usname : Auth::user()->id_emp),
								'to_user' => '',
								'to_pm' => $staf,
								'rd' => 'N',
								'usr_rd' => '',
								'tgl_rd' => null,
								'selesai' => 'Y',
								'child' => 0,
							];
							Fr_disposisi::insert($insertsurat);
						}
					}
				}

				return redirect('/profil/disposisi')
					->with('message', 'Disposisi berhasil dilanjutkan')
					->with('msg_num', 1);
			}
		}
	}

	public function forminsertdisposisi(Request $request)
	{
		$this->checkSessionTime();

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		$file = $request->nm_file;

		if (count($file) <= 1) {
			if (isset($file)) {
				$filedispo = 'disp';

				if ($file->getSize() > 52222222) {
					return redirect('/profil/tambah disposisi')->with('message', 'Ukuran file terlalu besar (Maksimal 2MB)');     
				} 

				$filedispo .= date('dmYHis');
				$filedispo .= ".". $file->getClientOriginalExtension();

				$tujuan_upload = config('app.savefiledisposisi');
				$file->move($tujuan_upload, $filedispo);
			}
		} else {
			$filedispo = '';
			foreach ($file as $key => $data) {
				$filenow = 'disp';

				if ($data->getSize() > 52222222) {
					return redirect('/profil/tambah disposisi')->with('message', 'Ukuran file terlalu besar (Maksimal 2MB)');     
				} 

				$filenow .= $key;
				$filenow .= date('dmYHis');
				$filenow .= ".". $data->getClientOriginalExtension();

				$tujuan_upload = config('app.savefiledisposisi');
				$data->move($tujuan_upload, $filenow);

				if ($key != count($file) - 1) {
					$filedispo .= $filenow . "::";
				} else {
					$filedispo .= $filenow;
				}
			}
		}	
			
		if (!(isset($filedispo))) {
			$filedispo = null;
		}

		// $maxnoform = Fr_disposisi::max('no_form');
		// if (is_null($maxnoform)) {
		// 	$maxnoform = '1.20.512.20100001';
		// }
		// $splitnoform = explode(".", $maxnoform); 
		$newnoform = $request->newnoform;

		if (isset($request->btnDraft)) {
			$status_surat = 'd';
		} else {
			$status_surat = 's';
		}

		$insertsuratmaster = [
			'sts' => 1,
			'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
			'tgl'       => date('Y-m-d H:i:s'),
			'ip'        => '',
			'logbuat'   => '',
			'kd_skpd'	=> '1.20.512',
			'kd_unit'	=> '01',
			'no_form' => $newnoform,
			'kd_surat' => '',
			'status_surat' => $status_surat,
			'idtop' => 0,
			'tgl_masuk' => (isset($request->tgl_masuk) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_masuk))) : date('Y-m-d')),
			'usr_input' => (isset(Auth::user()->usname) ? Auth::user()->usname : Auth::user()->id_emp),
			'tgl_input' => date('Y-m-d H:i:s'),
			'no_index' => $request->no_index,
			'kode_disposisi' => $request->kode_disposisi,
			'perihal' => $request->perihal,
			'tgl_surat' => $request->tgl_surat,
			'no_surat' => $request->no_surat,
			'asal_surat' => $request->asal_surat,
			'kepada_surat' => $request->kepada_surat,
			'sifat1_surat' => ($request->sifat1_surat == null ? '' : $request->sifat1_surat ),
			'sifat2_surat' => ($request->sifat2_surat == null ? '' : $request->sifat2_surat ),
			'ket_lain' => $request->ket_lain,
			'nm_file' => $filedispo,
			'kepada' => 'Kepala Badan Pengelola Aset Daerah',
			'noid' => '',
			'penanganan' => '',
			'catatan' => '',
			'from_user' => '',
			'from_pm' => '',
			'to_user' => '',
			'to_pm' => '',
			'rd' => '',
			'usr_rd' => null,
			'tgl_rd' => null,
			'selesai' => '',
			'child' => 0,
		];

		if (Fr_disposisi::insert($insertsuratmaster) && $request->btnKirim) {

			$findidemp = DB::select( DB::raw("
						SELECT id_emp,a.uname+'::'+convert(varchar,a.tgl)+'::'+a.ip,createdate,nip_emp,nrk_emp,nm_emp,nrk_emp+'-'+nm_emp as c2,gelar_dpn,gelar_blk,jnkel_emp,tempat_lahir,tgl_lahir,CONVERT(VARCHAR(10), tgl_lahir, 103) AS [DD/MM/YYYY],idagama,alamat_emp,tlp_emp,email_emp,status_emp,ked_emp,status_nikah,gol_darah,nm_bank,cb_bank,an_bank,nr_bank,no_taspen,npwp,no_askes,no_jamsos,tgl_join,CONVERT(VARCHAR(10), tgl_join, 103) AS [DD/MM/YYYY],tgl_end,reason,a.idgroup,pass_emp,foto,ttd,a.telegram_id,a.lastlogin,tbgol.tmt_gol,CONVERT(VARCHAR(10), tbgol.tmt_gol, 103) AS [DD/MM/YYYY],tbgol.tmt_sk_gol,CONVERT(VARCHAR(10), tbgol.tmt_sk_gol, 103) AS [DD/MM/YYYY],tbgol.no_sk_gol,tbgol.idgol,tbgol.jns_kp,tbgol.mk_thn,tbgol.mk_bln,tbgol.gambar,tbgol.nm_pangkat,tbjab.tmt_jab,CONVERT(VARCHAR(10), tbjab.tmt_jab, 103) AS [DD/MM/YYYY],tbjab.idskpd,tbjab.idunit,tbjab.idjab, tbunit.child, tbjab.idlok,tbjab.tmt_sk_jab,CONVERT(VARCHAR(10), tbjab.tmt_sk_jab, 103) AS [DD/MM/YYYY],tbjab.no_sk_jab,tbjab.jns_jab,tbjab.idjab,tbjab.eselon,tbjab.gambar,tbdik.iddik,tbdik.prog_sek,tbdik.no_sek,tbdik.th_sek,tbdik.nm_sek,tbdik.gelar_dpn_sek,tbdik.gelar_blk_sek,tbdik.ijz_cpns,tbdik.gambar,tbdik.nm_dik,b.nm_skpd,c.nm_unit,c.notes,d.nm_lok FROM bpaddtfake.dbo.emp_data as a
							CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM  bpaddtfake.dbo.emp_gol,bpaddtfake.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
							CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
							CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpaddtfake.dbo.emp_dik,bpaddtfake.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
							CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
							,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
							and tbjab.idjab like '".$request->jabatans[0]."' and ked_emp = 'aktif' order by nm_emp") )[0];
			$findidemp = json_decode(json_encode($findidemp), true);

			$idnew = Fr_disposisi::max('ids');
			$insertsurat = [
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'kd_skpd'	=> '1.20.512',
				'kd_unit'	=> '01',
				'no_form' => $newnoform,
				'kd_surat' => '',
				'status_surat' => '',
				'idtop' => $idnew,
				'tgl_masuk' => (isset($request->tgl_masuk) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_masuk))) : date('Y-m-d')),
				'usr_input' => '',
				'tgl_input' => null,
				'no_index' => '',
				'kode_disposisi' => '',
				'perihal' => '',
				'tgl_surat' => '',
				'no_surat' => '',
				'asal_surat' => '',
				'kepada_surat' => '',
				'sifat1_surat' => '',
				'sifat2_surat' => '',
				'ket_lain' => '',
				'nm_file' => '',
				'kepada' => ($request->jabatans[0] ? $request->jabatans[0] : ''),
				'noid' => '',
				'penanganan' => ($request->penanganan ? $request->penanganan : ''),
				'catatan' => ($request->catatan ? $request->catatan : ''),
				'from_user' => '',
				'from_pm' => (isset(Auth::user()->usname) ? Auth::user()->usname : Auth::user()->id_emp),
				'to_user' => '',
				'to_pm' => $findidemp['id_emp'],
				'rd' => 'N',
				'usr_rd' => '',
				'tgl_rd' => null,
				'selesai' => 'Y',
				'child' => 0,
			];
			Fr_disposisi::insert($insertsurat);
		}

		return redirect('/profil/disposisi')
					->with('message', 'Disposisi berhasil dibuat')
					->with('msg_num', 1);
	}

	public function deleteLoopDisposisi($ids)
	{
		$querys = Fr_disposisi::
				where('idtop', $ids)
				->get(['ids', 'child']);

		foreach ($querys as $key => $query) {
			$this->deleteLoopDisposisi($query['ids']);
			Fr_disposisi::
				where('ids', $query['ids'])
				->delete();
		}

		return 1;
	}

	public function formdeletedisposisi(Request $request)
	{
		$this->checkSessionTime();

		$this->deleteLoopDisposisi($request->ids);

		Fr_disposisi::
				where('ids', $request->ids)
				->delete();

		$countanak = count(Fr_disposisi::where('idtop', $request->idtop)->get());
		if ($countanak == 0) {
			Fr_disposisi::where('ids', $request->idtop)
				->update([
					'rd' => 'Y',
					'selesai' => 'Y',
					'child' => 0,
				]);
		}

		return redirect('/profil/disposisi')
					->with('message', 'Disposisi berhasil dihapus')
					->with('msg_num', 1);
	}

	public function ceknoform(Request $request)
	{
		$no_form = $request->noform;
		$query = DB::select( DB::raw("
					SELECT count(ids) as total
					from bpaddtfake.dbo.fr_disposisi
					where no_form = '$no_form'
					"));
		$query = json_decode(json_encode($query), true);

		return $query;
	}
}
