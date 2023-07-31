<?php

namespace App\Http\Controllers;

require '../vendor/autoload.php';

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
use App\Emp_files;
use App\Emp_skp;
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
	use SessionCheckNotif;

	public function __construct()
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		$this->middleware('auth');
	}

	public function printdrh(Request $request)
	{
		if (is_null($request->emp)) {
			$id_emp = Auth::user()->id_emp;
		} else {
            $id_emp = $request->emp;
		}
		
		$emp_data = Emp_data::
						where('id_emp', $id_emp)
						->where('sts', 1)
						->first();

		$emp_dik = Emp_dik::with('dik')
						->where('noid', $id_emp)
						->where('sts', 1)
						->orderBy('th_sek', 'desc')
						->get();

		// $emp_gol = Emp_gol::with('gol')
		// 				->where('noid', $id_emp)
		// 				->where('sts', 1)
		// 				->orderBy('tmt_gol', 'desc')
		// 				->get();

		$emp_gol = Emp_gol::
						join('bpaddtfake.dbo.glo_org_golongan', 'bpaddtfake.dbo.glo_org_golongan.gol', '=', 'bpaddtfake.dbo.emp_gol.idgol')
						->where('bpaddtfake.dbo.emp_gol.noid', $id_emp)
						->where('bpaddtfake.dbo.emp_gol.sts', 1)
						->orderBy('bpaddtfake.dbo.emp_gol.tmt_gol', 'desc')
						->get();

		$emp_jab = Emp_jab::with('jabatan')
						->with('lokasi')
						->with('unit')
						->where('noid', $id_emp)
						->where('sts', 1)
						->orderBy('tmt_jab', 'desc')
						->get();

		$emp_non = Emp_non::where('sts', 1)
						->where('noid', $id_emp)
						->orderBy('tgl_non', 'desc')
						->get();

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
		return $pdf->download($id_emp.'_'.strtoupper($emp_data['nm_emp']).'_'.'DRH.pdf');
	}

	public function pegawai(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		// $this->checksession(); //$this->checkSessionTime();
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

		$emp_skp = Emp_skp::
					where('sts', 1)
					->where('noid', Auth::user()->id_emp)
					->orderBy('skp_tgl', 'desc')
					->get();		

		$emp_huk = Emp_huk::
					where('sts', 1)
					->where('noid', Auth::user()->id_emp)
					->orderBy('tgl_sk', 'desc')
					->get();

		$emp_files = Emp_files::
					where('sts', 1)
					->where('noid', Auth::user()->id_emp)
					->orderBy('file_nama', 'asc')
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
				->with('emp_skp', $emp_skp)
				->with('emp_huk', $emp_huk)
				->with('emp_files', $emp_files)
				->with('statuses', $statuses)
				->with('pendidikans', $pendidikans)
				->with('golongans', $golongans)
				->with('jabatans', $jabatans)
				->with('lokasis', $lokasis)
				->with('kedudukans', $kedudukans)
				->with('units', $units)
				->with('keluargas', $keluargas)
				->with('hukumans', $hukumans)
				->with('notifs', $notifs);
	}

	public function formupdateidpegawai(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		// $this->checksession(); //$this->checkSessionTime();

		$id_emp = $request->id_emp;
		$filefoto = '';
		$filecpns = '';
		$filepns = '';
		$karpeg = '';

		$cekangkapenting = Emp_data::where('id_emp', $request->id_emp)->first(['nik_emp', 'nrk_emp', 'nip_emp']);

        if(empty($request->nm_emp)) {
            return redirect('/profil/pegawai')->with('message', 'Nama Tidak Boleh Kosong!');
        }

		if ($request->nip_emp && $request->nip_emp != '' && $request->nip_emp != $cekangkapenting['nip_emp']) {
			$ceknip = Emp_data::
						where('nip_emp', $request->nip_emp)
						->where('sts', '1')
						->count();
			if ($ceknip > 0) {
				return redirect('/profil/pegawai')->with('message', 'NIP sudah tersimpan di database');
			}
		}
		if (strlen($request->nip_emp) > 21 && strlen($request->nip_emp) != 0) {
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
		if (strlen($request->nrk_emp) > 9 && strlen($request->nrk_emp) != 0) {
			return redirect('/profil/pegawai')->with('message', 'NRK harus terdiri dari 6 digit');
		}

		// if ($request->nik_emp && $request->nik_emp != '' && $request->nik_emp != $cekangkapenting['nik_emp']) {
		// 	$ceknrk = Emp_data::
		// 				where('nik_emp', $request->nik_emp)
		// 				->where('sts', '1')
		// 				->count();
		// 	if ($ceknrk > 0) {
		// 		return redirect('/profil/pegawai')->with('message', 'NIK KTP sudah tersimpan di database');
		// 	}
		// }	
		if (strlen($request->nik_emp) != 16 && strlen($request->nik_emp) != 0) {
			return redirect('/profil/pegawai')->with('message', 'NIK KTP harus terdiri dari 16 digit');
		}

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		if (isset($request->filefoto)) {
			$file = $request->filefoto;

			if ($file->getSize() > 500000) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file foto pegawai terlalu besar (Maksimal 500KB)');     
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

		if (isset($request->fileskcpns)) {
			$file = $request->fileskcpns;

			if ($file->getSize() > 500000) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file SK CPNS terlalu besar (Maksimal 500KB)');     
			}

			if (strtolower($file->getClientOriginalExtension()) != "png" && strtolower($file->getClientOriginalExtension()) != "jpg" && strtolower($file->getClientOriginalExtension()) != "jpeg" && strtolower($file->getClientOriginalExtension()) != "pdf") {
				return redirect('/profil/pegawai')->with('message', 'File yang diunggah harus berbentuk JPG / JPEG / PNG / PDF');     
			}

			$filecpns .= "cpns_" . $id_emp . ".". $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefileimg');
			$tujuan_upload .= "\\" . $id_emp . "\\skcpns\\";

			if (file_exists($tujuan_upload . $filecpns )) {
				unlink($tujuan_upload . $filecpns);
			}

			$file->move($tujuan_upload, $filecpns);
		}

		if (isset($request->fileskpns)) {
			$file = $request->fileskpns;

			if ($file->getSize() > 500000) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file SK PNS terlalu besar (Maksimal 500KB)');     
			}

			if (strtolower($file->getClientOriginalExtension()) != "png" && strtolower($file->getClientOriginalExtension()) != "jpg" && strtolower($file->getClientOriginalExtension()) != "jpeg" && strtolower($file->getClientOriginalExtension()) != "pdf") {
				return redirect('/profil/pegawai')->with('message', 'File yang diunggah harus berbentuk JPG / JPEG / PNG / PDF');     
			}

			$filepns .= "pns_" . $id_emp . ".". $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefileimg');
			$tujuan_upload .= "\\" . $id_emp . "\\skpns\\";

			if (file_exists($tujuan_upload . $filepns )) {
				unlink($tujuan_upload . $filepns);
			}

			$file->move($tujuan_upload, $filepns);
		}

		if (isset($request->karpeg)) {
			$file = $request->karpeg;

			if ($file->getSize() > 500000) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file Kartu Pegawai terlalu besar (Maksimal 500KB)');     
			}

			if (strtolower($file->getClientOriginalExtension()) != "png" && strtolower($file->getClientOriginalExtension()) != "jpg" && strtolower($file->getClientOriginalExtension()) != "jpeg" && strtolower($file->getClientOriginalExtension()) != "pdf") {
				return redirect('/profil/pegawai')->with('message', 'File yang diunggah harus berbentuk JPG / JPEG / PNG / PDF');     
			}

			$karpeg .= "karpeg_" . $id_emp . ".". $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefileimg');
			$tujuan_upload .= "\\" . $id_emp . "\\karpeg\\";

			if (file_exists($tujuan_upload . $karpeg )) {
				unlink($tujuan_upload . $karpeg);
			}

			$file->move($tujuan_upload, $karpeg);
		}
			
		if (!(isset($filefoto))) {
			$filefoto = '';
		}

		if (!(isset($filecpns))) {
			$filecpns = '';
		}

		if (!(isset($filepns))) {
			$filepns = '';
		}

		if (!(isset($karpeg))) {
			$karpeg = '';
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

        $pegawai_id_update = [
            'tgl_join' => (isset($request->tgl_join) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_join))) : null),
            'status_emp' => $request->status_emp,
            // 'nip_emp' => ($request->nip_emp ? $request->nip_emp : ''),
            // 'nrk_emp' => ($request->nrk_emp ? $request->nrk_emp : ''),
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
            'updated_at'    => date('Y-m-d H:i:s'),
            'updated_by'    => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
            'tmt_sk_cpns' => ($request->tmt_sk_cpns ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_cpns))) : NULL),
            'tmt_sk_pns' => ($request->tmt_sk_pns ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_pns))) : NULL),
        ];

		Emp_data::where('id_emp', $id_emp)->update($pegawai_id_update);
		Emp_data_11::where('id_emp', $id_emp)->update($pegawai_id_update);

		if ($filefoto != '') {
            $filefoto_update = [
				'foto' => $filefoto,
			];
			Emp_data::where('id_emp', $id_emp)->update($filefoto_update);
			Emp_data_11::where('id_emp', $id_emp)->update($filefoto_update);
		}

		if ($filecpns != '') {
            $filecpns_update = [
				'sk_cpns' => $filecpns,
			];
			Emp_data::where('id_emp', $id_emp)->update($filecpns_update);
			Emp_data_11::where('id_emp', $id_emp)->update($filecpns_update);
		}

		if ($filepns != '') {
            $filepns_update = [
				'sk_pns' => $filepns,
			];
			Emp_data::where('id_emp', $id_emp)->update($filepns_update);
			Emp_data_11::where('id_emp', $id_emp)->update($filepns_update);
		}

		if ($karpeg != '') {
            $karpeg_update = [
				'karpeg' => $karpeg,
			];
			Emp_data::where('id_emp', $id_emp)->update($karpeg_update);
			Emp_data_11::where('id_emp', $id_emp)->update($karpeg_update);
		}

		return redirect('/profil/pegawai')
					->with('message', 'Pegawai '.$request->nm_emp.' berhasil diubah. Apabila terdapat kesalahan data, mohon hapus cache dan refresh atau melakukan login ulang')
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
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

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
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

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
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();
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
		if(count($_SESSION) == 0) {
			return redirect('home');
		}//$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];
		$fileijazah = '';

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		if (isset($request->fileijazah)) {
			$file = $request->fileijazah;

			if ($file->getSize() > 555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file ijazah terlalu besar (Maksimal 500KB)');     
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
		Emp_dik_11::insert($insert_emp_dik);

		return redirect('/profil/pegawai')
					->with('message', 'Data pendidikan pegawai berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdatedikpegawai (Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}//$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];
		$fileijazah = '';

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		if (isset($request->fileijazah)) {
			$file = $request->fileijazah;

			if ($file->getSize() > 555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file ijazah terlalu besar (Maksimal 500KB)');     
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

        $pegawai_dik_update = [
            'iddik' => $request->iddik,
            'prog_sek' => ($request->prog_sek ? $request->prog_sek : ''),
            'nm_sek' => ($request->nm_sek ? $request->nm_sek : ''),
            'no_sek' => ($request->no_sek ? $request->no_sek : ''),
            'th_sek' => ($request->th_sek ? $request->th_sek : ''),
            'gelar_dpn_sek' => ($request->gelar_dpn_sek ? $request->gelar_dpn_sek : ''),
            'gelar_blk_sek' => ($request->gelar_blk_sek ? $request->gelar_blk_sek : ''),
            'ijz_cpns' => $request->ijz_cpns,
            'gambar' => $fileijazah,
        ];

		Emp_dik::where('noid', $id_emp)
			->where('ids', $request->ids)
			->update($pegawai_dik_update);
		Emp_dik_11::where('noid', $id_emp)
			->where('ids', $request->ids)
			->update($pegawai_dik_update);

		if ($fileijazah != '') {
            $fileijazah_update = [
				'tampilnew' => 1,
				'gambar' => $fileijazah,
			];
			Emp_dik::where('noid', $id_emp)
			->where('ids', $request->ids)
			->update($fileijazah_update);
			Emp_dik_11::where('noid', $id_emp)
			->where('ids', $request->ids)
			->update($fileijazah_update);
		}

		return redirect('/profil/pegawai')
					->with('message', 'Data pendidikan pegawai berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletedikpegawai(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}//$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];

        $cekcountdik = Emp_dik::where('noid', $id_emp)->where('sts', 1)->count();
		if ($cekcountdik == 1) {
			return redirect('/profil/pegawai')->with('message', 'Tidak dapat menghapus habis data pendidikan pegawai');
		}

		Emp_dik::where('noid', $id_emp)
		->where('ids', $request->ids)
		->update([
			'sts' => 0,
		]);
		Emp_dik_11::where('noid', $id_emp)
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
		if(count($_SESSION) == 0) {
			return redirect('home');
		}//$this->checkSessionTime();

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

			if ($file->getSize() > 555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file ijazah terlalu besar (Maksimal 500KB)');     
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
		if(count($_SESSION) == 0) {
			return redirect('home');
		}//$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];
		$filenon = '';

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		if (isset($request->filenon)) {
			$file = $request->filenon;

			if ($file->getSize() > 555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file ijazah terlalu besar (Maksimal 500KB)');     
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
			->where('ids', $request->ids)
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
		if(count($_SESSION) == 0) {
			return redirect('home');
		}//$this->checkSessionTime();

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
		if(count($_SESSION) == 0) {
			return redirect('home');
		}//$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];
		$filegol = '';

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		if (isset($request->filegol)) {
			$file = $request->filegol;

			if ($file->getSize() > 555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file terlalu besar (Maksimal 500KB)');     
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
		Emp_gol_11::insert($insert_emp_gol);

		return redirect('/profil/pegawai')
					->with('message', 'Data golongan pegawai berhasil ditambah. Buat golongan baru lalu hapus yang lama.')
					->with('msg_num', 1);
	}

	public function formupdategolpegawai (Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}//$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];
		$filegol = '';

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		if (isset($request->filegol)) {
			$file = $request->filegol;

			if ($file->getSize() > 555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file terlalu besar (Maksimal 500KB)');     
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

        $pegawai_gol_update = [
            'tmt_gol' => (isset($request->tmt_gol) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_gol))) : null),
            'tmt_sk_gol' => (isset($request->tmt_sk_gol) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_gol))) : null),
            'no_sk_gol' => ($request->no_sk_gol ? $request->no_sk_gol : ''),
            'idgol' => $request->idgol,
            'jns_kp' => $request->jns_kp,
            'mk_thn' => ($request->mk_thn ? $request->mk_thn : 0),
            'mk_bln' => ($request->mk_bln ? $request->mk_bln : 0),
        ];

		Emp_gol::where('noid', $id_emp)
			->where('ids', $request->ids)
			->update($pegawai_gol_update);
		Emp_gol_11::where('noid', $id_emp)
			->where('ids', $request->ids)
			->update($pegawai_gol_update);

		if ($filegol != '') {
            $filegol_update = [
				'tampilnew' => 1,
				'gambar' => $filegol,
			];
			Emp_gol::where('noid', $id_emp)
			->where('ids', $request->ids)
			->update($filegol_update);
			Emp_gol_11::where('noid', $id_emp)
			->where('ids', $request->ids)
			->update($filegol_update);
		}

		return redirect('/profil/pegawai')
					->with('message', 'Data golongan pegawai berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletegolpegawai(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}//$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];

		$cekcountgol = Emp_gol::where('noid', $id_emp)->where('sts', 1)->count();
		if ($cekcountgol == 1) {
			return redirect('/profil/pegawai')->with('message', 'Tidak dapat menghapus habis golongan pegawai. Buat jabatan baru lalu hapus yang lama.');
		}

		Emp_gol::where('noid', $id_emp)
		->where('ids', $request->ids)
		->update([
			'sts' => 0,
		]);
		Emp_gol_11::where('noid', $id_emp)
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
		if(count($_SESSION) == 0) {
			return redirect('home');
		}//$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];
		$filejab = '';

        if(empty($request->idjab)) {
            $idjab = NULL;
        }
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
		// 		return redirect('/profil/pegawai')->with('message', 'Jabatan yang dipilih telah terisi. Silahkan pilih jabatan lain.');
		// 	}
		// }

		// $jabatan = explode("||", $request->jabatan);
		// $jns_jab = $jabatan[0];
		// $idjab = $jabatan[1];

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		if (isset($request->filejab)) {
			$file = $request->filejab;

			if ($file->getSize() > 555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file terlalu besar (Maksimal 500KB)');     
			}

			if (strtolower($file->getClientOriginalExtension()) != "png" && strtolower($file->getClientOriginalExtension()) != "jpg" && strtolower($file->getClientOriginalExtension()) != "jpeg" && strtolower($file->getClientOriginalExtension()) != "pdf") {
				return redirect('/profil/pegawai')->with('message', 'File yang diunggah harus berbentuk PDF / JPG / JPEG / PNG');     
			}

			// $filejab .= str_replace(" ", "", str_replace("/","",strtolower($request->idjab))) . "_" . $request->idunit . "_" . $id_emp . ".". $file->getClientOriginalExtension();
            // $filejab .= str_replace("::","_",$request->idunit) . "_" . $id_emp . ".". $file->getClientOriginalExtension();
            $filejab .= date('dmYHis') . "_" . $id_emp . ".". $file->getClientOriginalExtension();

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
				'noid' => $request->noid,
				'tmt_jab' => (isset($request->tmt_jab) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_jab))) : null),
				'idskpd' => '1.20.512',
				'idunit' => $idunit,
				'idlok' => $idlok['kd_lok'],
				'tmt_sk_jab' => (isset($request->tmt_sk_jab) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_jab))) : null),
				'no_sk_jab' => ($request->no_sk_jab ? $request->no_sk_jab : ''),
				'jns_jab' => $request->jns_jab,
				'idjab' => $idjab,
				'eselon' => $request->eselon,
				'gambar' => $filejab,
				'nmunit' => $nmunit,
			];

		Emp_jab::insert($insert_emp_jab);
		Emp_jab_11::insert($insert_emp_jab);

		return redirect('/profil/pegawai')
					->with('message', 'Data Unit Kerja pegawai berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdatejabpegawai(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}//$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];
		$filejab = '';

        if(empty($request->idjab)) {
            $idjab = NULL;
        }

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		if (isset($request->filejab)) {
			$file = $request->filejab;

			if ($file->getSize() > 555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file terlalu besar (Maksimal 500KB)');     
			}

			if (strtolower($file->getClientOriginalExtension()) != "png" && strtolower($file->getClientOriginalExtension()) != "jpg" && strtolower($file->getClientOriginalExtension()) != "jpeg" && strtolower($file->getClientOriginalExtension()) != "pdf") {
				return redirect('/profil/pegawai')->with('message', 'File yang diunggah harus berbentuk PDF / JPG / JPEG / PNG');     
			}

			// $filejab .= str_replace(" ", "", str_replace("/","",strtolower($request->idjab))) . "_" . $request->idunit . "_" . $id_emp . ".". $file->getClientOriginalExtension();
            // $filejab .= str_replace("::","_",$request->idunit) . "_" . $id_emp . ".". $file->getClientOriginalExtension();
            $filejab .= date('dmYHis') . "_" . $id_emp . ".". $file->getClientOriginalExtension();

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

		// $splitidunit = explode("::", $request->idunit);
		// $idunit = $splitidunit[0];
		// $nmunit = $splitidunit[1];

        $pegawai_jab_update = [
            'tmt_jab' => (isset($request->tmt_jab) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_jab))) : null),
            // 'idunit' => $idunit,
            'idlok' => $idlok['kd_lok'],
            'tmt_sk_jab' => (isset($request->tmt_sk_jab) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_jab))) : null),
            'no_sk_jab' => ($request->no_sk_jab ? $request->no_sk_jab : ''),
            // 'jns_jab' => $request->jns_jab,
            'idjab' => $idjab,
            'eselon' => $request->eselon,
            // 'nmunit' => $nmunit,  oouioui0oio
            // 'tampilnew' => 1,
        ];

		Emp_jab::where('noid', $request->noid)
			->where('ids', $request->ids)
			->update($pegawai_jab_update);
		Emp_jab_11::where('noid', $request->noid)
			->where('ids', $request->ids)
			->update($pegawai_jab_update);

		if ($filejab != '') {
            $filejab_update = [
				'gambar' => $filejab,
			];
			Emp_jab::where('noid', $id_emp)
			->where('ids', $request->ids)
			->update($filejab_update);
			Emp_jab_11::where('noid', $id_emp)
			->where('ids', $request->ids)
			->update($filejab_update);
		}

		return redirect('/profil/pegawai')
					->with('message', 'Data Unit Kerja pegawai berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletejabpegawai(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}//$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];

		$cekcountjab = Emp_jab::where('noid', $id_emp)->where('sts', 1)->count();
		if ($cekcountjab == 1) {
			return redirect('/profil/pegawai')->with('message', 'Tidak dapat menghapus habis Riwayat Unit Kerja pegawai');
		}

		Emp_jab::where('noid', $id_emp)
		->where('ids', $request->ids)
		->update([
			'sts' => 0,
		]);
		Emp_jab_11::where('noid', $id_emp)
		->where('ids', $request->ids)
		->update([
			'sts' => 0,
		]);

		return redirect('/profil/pegawai')
					->with('message', 'Data Unit Kerja pegawai berhasil dihapus')
					->with('msg_num', 1);
	}

	// -------SKP-------------------------------------------------------------------- //
	// -------SKP-------------------------------------------------------------------- //
	// -------SKP-------------------------------------------------------------------- //
	// -------SKP-------------------------------------------------------------------- //
	// -------SKP-------------------------------------------------------------------- //
	// -------SKP-------------------------------------------------------------------- //
// -------SKP-------------------------------------------------------------------- //
	// -------SKP-------------------------------------------------------------------- //
	// -------SKP-------------------------------------------------------------------- //
// -------SKP-------------------------------------------------------------------- //
	// -------SKP-------------------------------------------------------------------- //
	// -------SKP-------------------------------------------------------------------- //
// -------SKP-------------------------------------------------------------------- //
	// -------SKP-------------------------------------------------------------------- //
	// -------SKP-------------------------------------------------------------------- //
	// -------SKP-------------------------------------------------------------------- //
	// -------SKP-------------------------------------------------------------------- //
	// -------SKP-------------------------------------------------------------------- //
// -------SKP-------------------------------------------------------------------- //
	// -------SKP-------------------------------------------------------------------- //
	// -------SKP-------------------------------------------------------------------- //
// -------SKP-------------------------------------------------------------------- //
	// -------SKP-------------------------------------------------------------------- //
	// -------SKP-------------------------------------------------------------------- //
// -------SKP-------------------------------------------------------------------- //
	// -------SKP-------------------------------------------------------------------- //
	// -------SKP-------------------------------------------------------------------- //


	public function forminsertskppegawai (Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}

		$id_emp = $_SESSION['user_data']['id_emp'];
		$fileskp = '';

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
		if(count($_SESSION) == 0) {
			return redirect('home');
		}//$this->checkSessionTime();

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

			if ($file->getSize() > 555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file terlalu besar (Maksimal 500KB)');     
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
		if(count($_SESSION) == 0) {
			return redirect('home');
		}//$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];
		$filehuk = '';

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		if (isset($request->filehuk)) {
			$file = $request->filehuk;

			if ($file->getSize() > 555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file terlalu besar (Maksimal 500KB)');     
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
		if(count($_SESSION) == 0) {
			return redirect('home');
		}//$this->checkSessionTime();

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

	public function forminsertfilespegawai (Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}//$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];
		$filefiles = '';

		$insert_emp_files = [
				// BERKAS LAIN
				'sts'       => 1,
				'tgl'       => date('Y-m-d H:i:s'),
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'noid'      => $id_emp,
				'file_nama'   => $request->file_nama,
				'file_nomor'   => $request->file_nomor,
				'file_tahun'   => $request->file_tahun,
			];

		$nowid = Emp_files::insertGetId($insert_emp_files);

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		if (isset($request->filefiles)) {
			
			$file = $request->filefiles;

			if ($file->getSize() > 555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file terlalu besar (Maksimal 500KB)');     
			}

			if (strtolower($file->getClientOriginalExtension()) != "png" && strtolower($file->getClientOriginalExtension()) != "jpg" && strtolower($file->getClientOriginalExtension()) != "jpeg" && strtolower($file->getClientOriginalExtension()) != "pdf") {
				return redirect('/profil/pegawai')->with('message', 'File yang diunggah harus berbentuk PDF / JPG / JPEG / PNG');     
			}

			$filefiles .= $nowid . "_" . $id_emp . ".". $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefileimg');
			$tujuan_upload .= "\\" . $id_emp . "\\files\\";

			if (file_exists($tujuan_upload . $filefiles )) {
				unlink($tujuan_upload . $filefiles);
			}

			$file->move($tujuan_upload, $filefiles);
		}
			
		if (!(isset($filefiles))) {
			$filefiles = '';
		}

		if ($filefiles != '') {
			Emp_files::where('noid', $id_emp)
			->where('ids', $nowid)
			->update([
				'file_save' => $filefiles,
			]);
		}
		

		return redirect('/profil/pegawai')
					->with('message', 'Data Berkas Lainnya berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdatefilespegawai (Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}//$this->checkSessionTime();

        $id_emp = $_SESSION['user_data']['id_emp'];
		$filefiles = '';

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		if (isset($request->filefiles)) {
			$file = $request->filefiles;

			if ($file->getSize() > 1111111) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file terlalu besar (Maksimal 1MB)');     
			}

			if (strtolower($file->getClientOriginalExtension()) != "png" && strtolower($file->getClientOriginalExtension()) != "jpg" && strtolower($file->getClientOriginalExtension()) != "jpeg" && strtolower($file->getClientOriginalExtension()) != "pdf") {
				return redirect('/profil/pegawai')->with('message', 'File yang diunggah harus berbentuk PDF / JPG / JPEG / PNG');     
			}

			$filefiles .= $request->ids . "_" . $id_emp . ".". $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefileimg');
			$tujuan_upload .= "\\" . $id_emp . "\\files\\";

			if (file_exists($tujuan_upload . $filefiles )) {
				unlink($tujuan_upload . $filefiles);
			}

			$file->move($tujuan_upload, $filefiles);
		}
			
		if (!(isset($filefiles))) {
			$filefiles = '';
		}

		if ($filefiles != '') {
			Emp_files::where('noid', $id_emp)
			->where('ids', $request->ids)
			->update([
				'file_save' => $filefiles,
			]);
		}

		Emp_files::where('noid', $id_emp)
			->where('ids', $request->ids)
			->update([
				'file_nama'   => $request->file_nama,
				'file_nomor'   => $request->file_nomor,
				'file_tahun'   => $request->file_tahun,
			]);

		return redirect('/profil/pegawai')
					->with('message', 'Data hukuman disiplin berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletefilespegawai(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}//$this->checkSessionTime();

		$id_emp = $_SESSION['user_data']['id_emp'];

		Emp_files::where('noid', $id_emp)
		->where('ids', $request->ids)
		->update([
			'sts' => 0,
		]);

		return redirect('/profil/pegawai')
					->with('message', 'Data Berkas Lainnya berhasil dihapus')
					->with('msg_num', 1);
	}
}
