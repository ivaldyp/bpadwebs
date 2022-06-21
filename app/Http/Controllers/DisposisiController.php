<?php

namespace App\Http\Controllers;

require 'vendor/autoload.php';

use GuzzleHttp\Client;

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
use App\Emp_dik;
use App\Emp_gol;
use App\Emp_jab;
use App\Fr_disposisi;
use App\Glo_dik;
use App\Glo_disposisi_kode;
use App\Glo_disposisi_penanganan;
use App\Glo_org_golongan;
use App\Glo_org_jabatan;
use App\Glo_org_kedemp;
use App\Glo_org_lokasi;
use App\Glo_org_statusemp;
use App\Glo_org_unitkerja;
use App\Sec_access;
use App\Sec_menu;

session_start();

class DisposisiController extends Controller
{
	use SessionCheckTraits;
	use SessionCheckNotif;

	public function __construct()
	{
		$this->middleware('auth');
		set_time_limit(600);
	}

	public function checksession() {
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
	}

	public function log(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}

		$dispmaster = DB::select( DB::raw("SELECT TOP (100) [ids]
												  ,[sts]
												  ,[uname]
												  ,[tgl]
												  ,[ip]
												  ,[logbuat]
												  ,[kd_skpd]
												  ,[kd_unit]
												  ,[no_form]
												  ,[kd_surat]
												  ,[status_surat]
												  ,[idtop]
												  ,[tgl_masuk]
												  ,[usr_input]
												  ,[tgl_input]
												  ,[no_index]
												  ,[kode_disposisi]
												  ,[perihal]
												  ,[tgl_surat]
												  ,[no_surat]
												  ,[asal_surat]
												  ,[kepada_surat]
												  ,[sifat1_surat]
												  ,[sifat2_surat]
												  ,[ket_lain]
												  ,[nm_file]
												  ,[kepada]
												  ,[noid]
												  ,[penanganan]
												  ,[catatan]
												  ,[from_user]
												  ,[from_pm]
												  ,[to_user]
												  ,[to_pm]
												  ,[rd]
												  ,[usr_rd]
												  ,[tgl_rd]
												  ,[selesai]
												  ,[child]
												  ,[penanganan_final]
												  ,[catatan_final]
												  FROM [bpaddtfake].[dbo].[fr_disposisi]
												  where no_form like '$request->form'
												  and sts = 1
												  and (status_surat = 'd' or status_surat = 's') 
												  order by no_form desc, ids asc"))[0];
		$dispmaster = json_decode(json_encode($dispmaster), true);

		$treedisp = '<tr>
						<td>
							<span class="fa fa-book"></span> <span>'.$dispmaster['no_form'].' ['.date('d-M-Y',strtotime($dispmaster['tgl'])).']</span> <br>
							<span class="text-muted">Kode: '.$dispmaster['kode_disposisi'].'</span> | <span class="text-muted"> Nomor: '.$dispmaster['no_surat'].'</span><br>

						</td>
					</tr>';

		$treedisp .= $this->display_disposisi($dispmaster['no_form'], $dispmaster['ids']);

		return view('pages.bpaddisposisi.log')
				->with('dispmaster', $dispmaster)
				->with('treedisp', $treedisp);
	}

	public function display_disposisi($no_form, $idtop, $level = 0)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}

		// $query = Fr_disposisi::
		// 			leftJoin('bpaddtfake.dbo.emp_data as emp1', 'emp1.id_emp', '=', 'bpaddtfake.dbo.fr_disposisi.to_pm')
		// 			->where('no_form', $no_form)
		// 			->where('idtop', $idtop)
		// 			->orderBy('ids')
		// 			->get();

		$query = DB::select( DB::raw("SELECT *, bpaddtfake.dbo.fr_disposisi.tgl as disptgl
					from bpaddtfake.dbo.fr_disposisi
					left join bpaddtfake.dbo.emp_data on bpaddtfake.dbo.emp_data.id_emp = bpaddtfake.dbo.fr_disposisi.to_pm
					where no_form = '$no_form'
					and idtop = '$idtop'
					and bpaddtfake.dbo.fr_disposisi.sts = 1
					order by ids
					") );
		$query = json_decode(json_encode($query), true);

		$result = '';

		if (count($query) > 0) {
			foreach ($query as $log) {
				$padding = ($level * 20);

				$result .= '<tr >
								<td style="padding-left:'.$padding.'px; padding-top:10px">
									<i class="fa fa-user"></i> <span>'.$log['nrk_emp'].' '.ucwords(strtolower($log['nm_emp'])).' ['.date('d-M-Y',strtotime($log['disptgl'])).']</span> 
									'.(($log['child'] == 0 && $log['rd'] == 'S') ? "<i data-toggle='tooltip' title='Sudah ditindaklanjut!' class='fa fa-check' style='color: blue'></i>" : '').'
									'.(($log['child'] == 0 && $log['rd'] != 'S') ? "<i data-toggle='tooltip' title='Belum ditindaklanjut!' class='fa fa-close' style='color: red'></i>" : '').'
									<br> 
									<span class="text-muted"> Penanganan: <b>'. ($log['penanganan'] ? $log['penanganan'] : '-' ) .'</b> 
																				'.($log['catatan'] ? '('.$log['catatan'].')' : '' ).'</span>
									<br>
								</td>
							</tr>';

				if ($log['child'] == 1) {
					$result .= $this->display_disposisi($no_form, $log['ids'], $level+1);
				}
			}
		}
		return $result;
	}
	// ---------ADMIN----------- //

	public function formdisposisi(Request $request)
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
			$signnow = "<=";
		}

		if ($request->searchnow) {
			$qsearchnow = "and (kd_surat like '%".$request->searchnow."%' 
			or no_form like '%".$request->searchnow."%' 
			or perihal like '%".$request->searchnow."%' 
			or asal_surat like '%".$request->searchnow."%' 
			or no_surat like '%".$request->searchnow."%' 
			or kode_disposisi like '%".$request->searchnow."%' 
			or usr_input like '%".$request->searchnow."%'
			or no_surat + '/' + kode_disposisi like '%".$request->searchnow."%')";
		} else {
			$qsearchnow = "";
		}

		// $tglnow = (int)date('d');
		// $tgllengkap = $yearnow . "-" . $monthnow . "-" . $tglnow;

		$distinctyear = Fr_disposisi::distinct()
						->whereRaw('YEAR(tgl_masuk) > 2016')
						->whereRaw('YEAR(tgl_masuk) <= '.date('Y'))
						->orderBy('year', 'desc')
						->get([DB::raw('YEAR(tgl_masuk) as year')]);

		$dateofmonth = [0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
		$tglnow = $dateofmonth[$monthnow];

		$tgllengkap = $yearnow . "-" . $monthnow . "-" . $tglnow;

		$idgroup = $_SESSION['user_data']['id_emp'];
		
		$disposisiundangans = DB::select( DB::raw("SELECT TOP (1000) [ids]
												,[sts]
												,[uname]
												,[tgl]
												,[ip]
												,[logbuat]
												,[kd_skpd]
												,[kd_unit]
												,[no_form]
												,[kd_surat]
												,[status_surat]
												,[idtop]
												,[tgl_masuk]
												,[usr_input]
												,[tgl_input]
												,[no_index]
												,[kode_disposisi]
												,[perihal]
												,[tgl_surat]
												,[no_surat]
												,[asal_surat]
												,[kepada_surat]
												,[sifat1_surat]
												,[sifat2_surat]
												,[ket_lain]
												,[nm_file]
												,[kepada]
												,[noid]
												,[penanganan]
												,[catatan]
												,[from_user]
												,[from_pm]
												,[to_user]
												,[to_pm]
												,[rd]
												,[usr_rd]
												,[tgl_rd]
												,[selesai]
												,[child]
												,[penanganan_final]
												,[catatan_final]
												FROM [bpaddtfake].[dbo].[fr_disposisi]
												where status_surat like 's'
												$qsearchnow
												and month(tgl_masuk) $signnow $monthnow
												and year(tgl_masuk) = $yearnow
												and sts = 1
												and catatan_final = 'undangan'
												order by tgl_masuk desc, no_form desc"));
		$disposisisurats = DB::select( DB::raw("SELECT TOP (1000) [ids]
												,[sts]
												,[uname]
												,[tgl]
												,[ip]
												,[logbuat]
												,[kd_skpd]
												,[kd_unit]
												,[no_form]
												,[kd_surat]
												,[status_surat]
												,[idtop]
												,[tgl_masuk]
												,[usr_input]
												,[tgl_input]
												,[no_index]
												,[kode_disposisi]
												,[perihal]
												,[tgl_surat]
												,[no_surat]
												,[asal_surat]
												,[kepada_surat]
												,[sifat1_surat]
												,[sifat2_surat]
												,[ket_lain]
												,[nm_file]
												,[kepada]
												,[noid]
												,[penanganan]
												,[catatan]
												,[from_user]
												,[from_pm]
												,[to_user]
												,[to_pm]
												,[rd]
												,[usr_rd]
												,[tgl_rd]
												,[selesai]
												,[child]
												,[penanganan_final]
												,[catatan_final]
												FROM [bpaddtfake].[dbo].[fr_disposisi]
												where status_surat like 's'
												$qsearchnow
												and month(tgl_masuk) $signnow $monthnow
												and year(tgl_masuk) = $yearnow
												and sts = 1
												and ((catatan_final <> 'undangan' and catatan_final <> 'ppid') or catatan_final is null)
												order by tgl_masuk desc, no_form desc"));

		$disposisippids = DB::select( DB::raw("SELECT TOP (1000) [ids]
												,[sts]
												,[uname]
												,[tgl]
												,[ip]
												,[logbuat]
												,[kd_skpd]
												,[kd_unit]
												,[no_form]
												,[kd_surat]
												,[status_surat]
												,[idtop]
												,[tgl_masuk]
												,[usr_input]
												,[tgl_input]
												,[no_index]
												,[kode_disposisi]
												,[perihal]
												,[tgl_surat]
												,[no_surat]
												,[asal_surat]
												,[kepada_surat]
												,[sifat1_surat]
												,[sifat2_surat]
												,[ket_lain]
												,[nm_file]
												,[kepada]
												,[noid]
												,[penanganan]
												,[catatan]
												,[from_user]
												,[from_pm]
												,[to_user]
												,[to_pm]
												,[rd]
												,[usr_rd]
												,[tgl_rd]
												,[selesai]
												,[child]
												,[penanganan_final]
												,[catatan_final]
												FROM [bpaddtfake].[dbo].[fr_disposisi]
												where status_surat like 's'
												$qsearchnow
												and month(tgl_masuk) $signnow $monthnow
												and year(tgl_masuk) = $yearnow
												and sts = 1
												and catatan_final = 'ppid'
												order by tgl_masuk desc, no_form desc"));

		
		$disposisidrafts = DB::select( DB::raw("SELECT TOP (1000) [ids]
												,[sts]
												,[uname]
												,[tgl]
												,[ip]
												,[logbuat]
												,[kd_skpd]
												,[kd_unit]
												,[no_form]
												,[kd_surat]
												,[status_surat]
												,[idtop]
												,[tgl_masuk]
												,[usr_input]
												,[tgl_input]
												,[no_index]
												,[kode_disposisi]
												,[perihal]
												,[tgl_surat]
												,[no_surat]
												,[asal_surat]
												,[kepada_surat]
												,[sifat1_surat]
												,[sifat2_surat]
												,[ket_lain]
												,[nm_file]
												,[kepada]
												,[noid]
												,[penanganan]
												,[catatan]
												,[from_user]
												,[from_pm]
												,[to_user]
												,[to_pm]
												,[rd]
												,[usr_rd]
												,[tgl_rd]
												,[selesai]
												,[child]
												,[penanganan_final]
												,[catatan_final]
												FROM [bpaddtfake].[dbo].[fr_disposisi]
												where status_surat like 'd'
												$qsearchnow
												and month(tgl_masuk) $signnow $monthnow
												and year(tgl_masuk) = $yearnow
												and sts = 1
												order by tgl_masuk desc, no_form desc"));

		$disposisiundangans = json_decode(json_encode($disposisiundangans), true);
		$disposisisurats = json_decode(json_encode($disposisisurats), true);
		$disposisippids = json_decode(json_encode($disposisippids), true);
		$disposisidrafts = json_decode(json_encode($disposisidrafts), true);

		return view('pages.bpaddisposisi.formdisposisi')
				->with('access', $access)
				->with('disposisiundangans', $disposisiundangans)
				->with('disposisisurats', $disposisisurats)
				->with('disposisippids', $disposisippids)
				->with('disposisidrafts', $disposisidrafts)
				->with('distinctyear', $distinctyear)
				->with('signnow', $signnow)
				->with('searchnow', $request->searchnow)
				->with('monthnow', $monthnow)
				->with('yearnow', $yearnow)
				->with('notifs', $notifs);
	}

	public function disposisitambah(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}

		// $this->checksession(); //$this->checkSessionTime();

		// $maxnoform = Fr_disposisi::max('no_form');

		$maxnoform = DB::select( DB::raw("SELECT max(no_form) as maks
										  FROM [bpaddtfake].[dbo].[fr_disposisi]
										  where sts = 1") );
		$maxnoform = json_decode(json_encode($maxnoform), true);

		if (is_null($maxnoform)) {
			$maxnoform = '1.20.512.'.substr(date('Y'), -2).'100001';
		} else {
			$splitmaxform = explode(".", $maxnoform[0]['maks']);
			$maxnoform = $splitmaxform[0] . '.' . $splitmaxform[1] . '.' . $splitmaxform[2] . '.' . substr(date('Y'), -2) . substr(($splitmaxform[3]+1), -6);
		}
		$kddispos = Glo_disposisi_kode::orderBy('kd_jnssurat')->get();

		$unitkerjas = DB::select( DB::raw("SELECT TOP (1000) [sts]
											  ,[uname]
											  ,[tgl]
											  ,[ip]
											  ,[logbuat]
											  ,[kd_skpd]
											  ,[kd_unit]
											  ,[nm_unit]
											  ,[cp_unit]
											  ,[notes]
											  ,[child]
											  ,[sao]
											  ,[tgl_unit]
										  FROM [bpaddtfake].[dbo].[glo_org_unitkerja]
										  order by kd_unit") );
		$unitkerjas = json_decode(json_encode($unitkerjas), true);

		// $stafs = DB::select( DB::raw("
		// 			SELECT id_emp,a.uname+'::'+convert(varchar,a.tgl)+'::'+a.ip,createdate,nip_emp,nrk_emp,nm_emp,nrk_emp+'-'+nm_emp as c2,gelar_dpn,gelar_blk,jnkel_emp,tempat_lahir,tgl_lahir,CONVERT(VARCHAR(10), tgl_lahir, 103) AS [DD/MM/YYYY],idagama,alamat_emp,tlp_emp,email_emp,status_emp,ked_emp,status_nikah,gol_darah,nm_bank,cb_bank,an_bank,nr_bank,no_taspen,npwp,no_askes,no_jamsos,tgl_join,CONVERT(VARCHAR(10), tgl_join, 103) AS [DD/MM/YYYY],tgl_end,reason,a.idgroup,pass_emp,foto,ttd,a.telegram_id,a.lastlogin,tbgol.tmt_gol,CONVERT(VARCHAR(10), tbgol.tmt_gol, 103) AS [DD/MM/YYYY],tbgol.tmt_sk_gol,CONVERT(VARCHAR(10), tbgol.tmt_sk_gol, 103) AS [DD/MM/YYYY],tbgol.no_sk_gol,tbgol.idgol,tbgol.jns_kp,tbgol.mk_thn,tbgol.mk_bln,tbgol.gambar,tbgol.nm_pangkat,tbjab.tmt_jab,CONVERT(VARCHAR(10), tbjab.tmt_jab, 103) AS [DD/MM/YYYY],tbjab.idskpd,tbjab.idunit,tbjab.idjab, tbunit.child, tbjab.idlok,tbjab.tmt_sk_jab,CONVERT(VARCHAR(10), tbjab.tmt_sk_jab, 103) AS [DD/MM/YYYY],tbjab.no_sk_jab,tbjab.jns_jab,tbjab.idjab,tbjab.eselon,tbjab.gambar,tbdik.iddik,tbdik.prog_sek,tbdik.no_sek,tbdik.th_sek,tbdik.nm_sek,tbdik.gelar_dpn_sek,tbdik.gelar_blk_sek,tbdik.ijz_cpns,tbdik.gambar,tbdik.nm_dik,b.nm_skpd,c.nm_unit,c.notes,d.nm_lok FROM bpaddtfake.dbo.emp_data as a
		// 				CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM bpaddtfake.dbo.emp_gol,bpaddtfake.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
		// 				CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
		// 				CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpaddtfake.dbo.emp_dik,bpaddtfake.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
		// 				CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
		// 				,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
		// 				and tbunit.kd_unit like '01%' and ked_emp = 'aktif' order by nm_emp") );
		// $stafs = json_decode(json_encode($stafs), true);

		// $jabatans = DB::select( DB::raw("SELECT [sts]
		// 												  ,[uname]
		// 												  ,[tgl]
		// 												  ,[ip]
		// 												  ,[logbuat]
		// 												  ,[kd_skpd]
		// 												  ,[jns_jab]
		// 												  ,[jabatan]
		// 												  ,[disposisi]
		// 											  FROM [bpaddtfake].[dbo].[glo_org_jabatan]
		// 											  where disposisi = 'Y'
		// 											  and jabatan like '%kepala badan%'
		// 											  order by jabatan asc") );
		// $jabatans = json_decode(json_encode($jabatans), true);

		if(!(is_null($_SESSION['user_data']['deskripsi_user'])) && $_SESSION['user_data']['deskripsi_user'] != '') {
			if(strlen($_SESSION['user_data']['deskripsi_user']) == 2) {
				if ($_SESSION['user_data']['deskripsi_user'] == '51' )
					$kd_unit = '010151';
				elseif ($_SESSION['user_data']['deskripsi_user'] == '52' )
					$kd_unit = '010152';
				elseif ($_SESSION['user_data']['deskripsi_user'] == '53' )
					$kd_unit = '010153';
				elseif ($_SESSION['user_data']['deskripsi_user'] == '54' )
					$kd_unit = '010154';
				elseif ($_SESSION['user_data']['deskripsi_user'] == '55' )
					$kd_unit = '010155';
				elseif ($_SESSION['user_data']['deskripsi_user'] == '56' )
					$kd_unit = '010156';
				elseif ($_SESSION['user_data']['deskripsi_user'] == '06' )
					$kd_unit = '010106';
				elseif ($_SESSION['user_data']['deskripsi_user'] == '07' )
					$kd_unit = '010107';
				elseif ($_SESSION['user_data']['deskripsi_user'] == '08' )
					$kd_unit = '010108';
				else 
					$kd_unit = '01';
			} else {
				$kd_unit = '01';
			}
		}

		$jabatans = DB::select( DB::raw("SELECT [sts]
										      ,[uname]
										      ,[tgl]
										      ,[ip]
										      ,[logbuat]
										      ,[kd_skpd]
										      ,[kd_unit]
										      ,[nm_unit]
										      ,[cp_unit]
										      ,[notes]
										      ,[child]
										      ,[sao]
										      ,[tgl_unit]
										  FROM [bpaddtfake].[dbo].[glo_org_unitkerja]
										  WHERE kd_unit = '$kd_unit'  
										  ORDER BY kd_unit asc, nm_unit asc ") );
		$jabatans = json_decode(json_encode($jabatans), true);

		$penanganans = Glo_disposisi_penanganan::
						orderBy('urut')
						->get();

		return view('pages.bpaddisposisi.disposisitambah')
				->with('maxnoform', $maxnoform)
				->with('kddispos', $kddispos)
				// ->with('stafs', $stafs)
				->with('unitkerjas', $unitkerjas)
				->with('jabatans', $jabatans)
				->with('penanganans', $penanganans);
	}

	public function disposisiubah(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}

		// if (file_exists("C:/xampp/htdocs/portal/public/publicfile/disp/1.20.512.20102228/disp19.pdf" )) {
		$dispmaster = DB::select( DB::raw("SELECT TOP (100) [ids]
												  ,[sts]
												  ,[uname]
												  ,[tgl]
												  ,[ip]
												  ,[logbuat]
												  ,[kd_skpd]
												  ,[kd_unit]
												  ,[no_form]
												  ,[kd_surat]
												  ,[status_surat]
												  ,[idtop]
												  ,[tgl_masuk]
												  ,[usr_input]
												  ,[tgl_input]
												  ,[no_index]
												  ,[kode_disposisi]
												  ,[perihal]
												  ,[tgl_surat]
												  ,[no_surat]
												  ,[asal_surat]
												  ,[kepada_surat]
												  ,[sifat1_surat]
												  ,[sifat2_surat]
												  ,[ket_lain]
												  ,[nm_file]
												  ,[kepada]
												  ,[noid]
												  ,[penanganan]
												  ,[catatan]
												  ,[from_user]
												  ,[from_pm]
												  ,[to_user]
												  ,[to_pm]
												  ,[rd]
												  ,[usr_rd]
												  ,[tgl_rd]
												  ,[selesai]
												  ,[child]
												  ,[penanganan_final]
												  ,[catatan_final]
												  FROM [bpaddtfake].[dbo].[fr_disposisi]
												  where ids like '$request->ids'
												  and sts = 1
												  order by tgl_masuk desc, no_form desc"))[0];
		$dispmaster = json_decode(json_encode($dispmaster), true);

		// $treedisp = '<tr>
		// 				<td>
		// 					<span class="fa fa-book"></span> <span>'.$dispmaster['no_form'].' ['.date('d-M-Y',strtotime($dispmaster['tgl'])).']</span> <br>
		// 					<span class="text-muted">Kode: '.$dispmaster['kode_disposisi'].'</span> | <span class="text-muted"> Nomor: '.$dispmaster['no_surat'].'</span><br>

		// 				</td>
		// 			</tr>';

		// $treedisp .= $this->display_disposisi($request->no_form, $dispmaster['ids']);

		$kddispos = Glo_disposisi_kode::orderBy('kd_jnssurat')->get();

		$unitkerjas = DB::select( DB::raw("SELECT TOP (1000) [sts]
											  ,[uname]
											  ,[tgl]
											  ,[ip]
											  ,[logbuat]
											  ,[kd_skpd]
											  ,[kd_unit]
											  ,[nm_unit]
											  ,[cp_unit]
											  ,[notes]
											  ,[child]
											  ,[sao]
											  ,[tgl_unit]
										  FROM [bpaddtfake].[dbo].[glo_org_unitkerja]
										  order by kd_unit") );
		$unitkerjas = json_decode(json_encode($unitkerjas), true);

		// $stafs = DB::select( DB::raw("
		// 			SELECT id_emp,a.uname+'::'+convert(varchar,a.tgl)+'::'+a.ip,createdate,nip_emp,nrk_emp,nm_emp,nrk_emp+'-'+nm_emp as c2,gelar_dpn,gelar_blk,jnkel_emp,tempat_lahir,tgl_lahir,CONVERT(VARCHAR(10), tgl_lahir, 103) AS [DD/MM/YYYY],idagama,alamat_emp,tlp_emp,email_emp,status_emp,ked_emp,status_nikah,gol_darah,nm_bank,cb_bank,an_bank,nr_bank,no_taspen,npwp,no_askes,no_jamsos,tgl_join,CONVERT(VARCHAR(10), tgl_join, 103) AS [DD/MM/YYYY],tgl_end,reason,a.idgroup,pass_emp,foto,ttd,a.telegram_id,a.lastlogin,tbgol.tmt_gol,CONVERT(VARCHAR(10), tbgol.tmt_gol, 103) AS [DD/MM/YYYY],tbgol.tmt_sk_gol,CONVERT(VARCHAR(10), tbgol.tmt_sk_gol, 103) AS [DD/MM/YYYY],tbgol.no_sk_gol,tbgol.idgol,tbgol.jns_kp,tbgol.mk_thn,tbgol.mk_bln,tbgol.gambar,tbgol.nm_pangkat,tbjab.tmt_jab,CONVERT(VARCHAR(10), tbjab.tmt_jab, 103) AS [DD/MM/YYYY],tbjab.idskpd,tbjab.idunit,tbjab.idjab, tbunit.child, tbjab.idlok,tbjab.tmt_sk_jab,CONVERT(VARCHAR(10), tbjab.tmt_sk_jab, 103) AS [DD/MM/YYYY],tbjab.no_sk_jab,tbjab.jns_jab,tbjab.idjab,tbjab.eselon,tbjab.gambar,tbdik.iddik,tbdik.prog_sek,tbdik.no_sek,tbdik.th_sek,tbdik.nm_sek,tbdik.gelar_dpn_sek,tbdik.gelar_blk_sek,tbdik.ijz_cpns,tbdik.gambar,tbdik.nm_dik,b.nm_skpd,c.nm_unit,c.notes,d.nm_lok FROM bpaddtfake.dbo.emp_data as a
		// 				CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM bpaddtfake.dbo.emp_gol,bpaddtfake.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
		// 				CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
		// 				CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpaddtfake.dbo.emp_dik,bpaddtfake.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
		// 				CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
		// 				,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
		// 				and tbunit.kd_unit like '01%' and ked_emp = 'aktif' order by nm_emp") );
		// $stafs = json_decode(json_encode($stafs), true);

		// $jabatans = DB::select( DB::raw("SELECT TOP (1000) [sts]
		// 												  ,[uname]
		// 												  ,[tgl]
		// 												  ,[ip]
		// 												  ,[logbuat]
		// 												  ,[kd_skpd]
		// 												  ,[jns_jab]
		// 												  ,[jabatan]
		// 												  ,[disposisi]
		// 											  FROM [bpaddtfake].[dbo].[glo_org_jabatan]
		// 											  where disposisi = 'Y'
		// 											  and jabatan like '%kepala badan%'
		// 											  order by jabatan asc") );
		// $jabatans = json_decode(json_encode($jabatans), true);

		if($dispmaster['kepada'] != '' && !(is_null($dispmaster['kepada']))) {
			$kd_unit = $dispmaster['kepada'];
		} else {
			if(!(is_null($_SESSION['user_data']['deskripsi_user'])) && $_SESSION['user_data']['deskripsi_user'] != '') {
				if(strlen($_SESSION['user_data']['deskripsi_user']) == 2) {
					if ($_SESSION['user_data']['deskripsi_user'] == '51' )
						$kd_unit = '010151';
					elseif ($_SESSION['user_data']['deskripsi_user'] == '52' )
						$kd_unit = '010152';
					elseif ($_SESSION['user_data']['deskripsi_user'] == '53' )
						$kd_unit = '010153';
					elseif ($_SESSION['user_data']['deskripsi_user'] == '54' )
						$kd_unit = '010154';
					elseif ($_SESSION['user_data']['deskripsi_user'] == '55' )
						$kd_unit = '010155';
					elseif ($_SESSION['user_data']['deskripsi_user'] == '56' )
						$kd_unit = '010156';
					elseif ($_SESSION['user_data']['deskripsi_user'] == '06' )
						$kd_unit = '010106';
					elseif ($_SESSION['user_data']['deskripsi_user'] == '07' )
						$kd_unit = '010107';
					elseif ($_SESSION['user_data']['deskripsi_user'] == '08' )
						$kd_unit = '010108';
					else 
						$kd_unit = '01';
				} else {
					$kd_unit = '01';
				}
			}
		}

		$jabatans = DB::select( DB::raw("SELECT [sts]
										      ,[uname]
										      ,[tgl]
										      ,[ip]
										      ,[logbuat]
										      ,[kd_skpd]
										      ,[kd_unit]
										      ,[nm_unit]
										      ,[cp_unit]
										      ,[notes]
										      ,[child]
										      ,[sao]
										      ,[tgl_unit]
										  FROM [bpaddtfake].[dbo].[glo_org_unitkerja]
										  WHERE kd_unit = '$kd_unit' 
										  ORDER BY kd_unit asc, nm_unit asc ") );
		$jabatans = json_decode(json_encode($jabatans), true);

		$penanganans = Glo_disposisi_penanganan::
						orderBy('urut')
						->get();

		$treedisp = '<tr>
						<td>
							<span class="fa fa-book"></span> <span>'.$dispmaster['no_form'].' ['.date('d-M-Y',strtotime($dispmaster['tgl'])).']</span> <br>
							<span class="text-muted">Kode: '.$dispmaster['kode_disposisi'].'</span> | <span class="text-muted"> Nomor: '.$dispmaster['no_surat'].'</span><br>

						</td>
					</tr>';

		$treedisp .= $this->display_disposisi($dispmaster['no_form'], $dispmaster['ids']);

		return view('pages.bpaddisposisi.disposisiubah')
				->with('signdate', $request->signdate)
				->with('dispmaster', $dispmaster)
				->with('treedisp', $treedisp)
				->with('kepada', $dispmaster['kepada'])
				->with('kddispos', $kddispos)
				->with('treedisp', $treedisp)
				// ->with('stafs', $stafs)
				->with('unitkerjas', $unitkerjas)
				->with('jabatans', $jabatans)
				->with('penanganans', $penanganans);
	}

	public function disposisihapusfile(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}

		$splittahun = explode(".", $request->no_form)[3];
		$splittahun = substr($splittahun, 0, 2);
		unlink(config('app.savefiledisposisi') . "/20" . $splittahun . "/" . $request->no_form . "/" . $request->nm_file );
		$nmfilebefore = Fr_disposisi::where('ids', $request->ids)->get();
		$nmfilenew = '';
		
		$splitnmfile = explode("::", $nmfilebefore[0]['nm_file']);
		foreach ($splitnmfile as $key => $nm_file) {
			if ($nm_file != $request->nm_file) {
				if ($key != 0 && $nm_file != $request->nm_file) {
					$nmfilenew .= "::";
				}
				$nmfilenew .= $nm_file;
			}
		}

		Fr_disposisi::where('ids', $request->ids)
			->update([
				'nm_file' => $nmfilenew,
			]);

		return 0;
	}

	public function forminsertdisposisi(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}

		// $this->checksession(); //$this->checkSessionTime();

		if (isset($request->jabatans) && isset($request->stafs)) {
			return redirect('/disposisi/tambah disposisi')
					->with('message', 'Tidak boleh memilih jabatan & staf bersamaan')
					->with('msg_num', 2);
		}

		if (is_null($request->jabatans)) {
			return redirect('/disposisi/tambah disposisi')
						->with('message', 'Tujuan disposisi tidak boleh kosong')
						->with('msg_num', 2);
		}

		if (isset($request->btnDraft)) {
			$status_surat = 'd';
			$selesai = 'Y';
			$child = 0;
		} else {
			// if (count($request->jabatans) > 1 || strpos(strtolower($request->jabatans[0]),"kepala badan") === false ) {
			// if (count($request->jabatans) > 1 || $request->jabatans[0] != '01' ) {
			// 	return redirect('/disposisi/tambah disposisi')
			// 			->with('message', 'Hanya boleh memilh Kepala Badan untuk memulai alur disposisi')
			// 			->with('msg_num', 2);
			// }
			$status_surat = 's';
			if (isset($request->jabatans)) {
				$selesai = '';
				$child = 1;
			} elseif (isset($request->stafs)) {
				$selesai = '';
				$child = 1;
			} else {
				$selesai = 'Y';
				$child = 0;
			}
		}

		if ($status_surat == 's' && is_null($request->jabatans) && is_null($request->stafs)) {
			return redirect('/disposisi/tambah disposisi')
					->with('message', 'Harus memilih untuk melanjutkan disposisi')
					->with('msg_num', 2);
		}

		// $ceknoform = Fr_disposisi::where('no_form', $request->newnoform)
		// 							->where('sts', 1)
		// 							->count();
		// if ($ceknoform != 0) {
		// 	$maxnoform = DB::select( DB::raw("SELECT max(no_form) as maks
		// 								  FROM [bpaddtfake].[dbo].[fr_disposisi]
		// 								  where sts = 1") );
		// 	$maxnoform = json_decode(json_encode($maxnoform), true);
		// 	if (is_null($maxnoform)) {
		// 	$maxnoform = '1.20.512.'.substr(date('Y'), -2).'100001';
		// 	} else {
		// 		$splitmaxform = explode(".", $maxnoform[0]['maks']);
		// 		$maxnoform = $splitmaxform[0] . '.' . $splitmaxform[1] . '.' . $splitmaxform[2] . '.' . substr(date('Y'), -2) . substr(($splitmaxform[3]+1), -6);
		// 	}
		// } else {
		// 	$maxnoform = $request->newnoform;
		// 	$splitmaxform = explode(".", $maxnoform);
		// 	$maxnoform = $splitmaxform[0] . '.' . $splitmaxform[1] . '.' . $splitmaxform[2] . '.' . substr(date('Y'), -2) . substr(($splitmaxform[3]), -6);
		// }

		$maxnoform = DB::select( DB::raw("SELECT max(no_form) as maks
										  FROM [bpaddtfake].[dbo].[fr_disposisi]
										  where sts = 1") );
		$maxnoform = json_decode(json_encode($maxnoform), true);
		if (is_null($maxnoform)) {
		$maxnoform = '1.20.512.'.substr(date('Y'), -2).'100001';
		} else {
			$splitmaxform = explode(".", $maxnoform[0]['maks']);
			$maxnoform = $splitmaxform[0] . '.' . $splitmaxform[1] . '.' . $splitmaxform[2] . '.' . substr(date('Y'), -2) . substr(($splitmaxform[3]+1), -6);
		}

		$diryear = (isset($request->tgl_masuk) ? date('Y',strtotime(str_replace('/', '-', $request->tgl_masuk))) : date('Y'));
		if (isset($request->nm_file)) {
			$file = $request->nm_file;
			if (count($file) == 1) {
				$filedispo = 'disp';

				if ($file[0]->getSize() > 52222222) {
					return redirect('/disposisi/tambah disposisi')->with('message', 'Ukuran file terlalu besar');     
				} 

				$filedispo .= ($splitmaxform[3]);
				$filedispo .= ".". $file[0]->getClientOriginalExtension();

				$tujuan_upload = config('app.savefiledisposisi');
				$tujuan_upload .= "\\" . $diryear;
				$tujuan_upload .= "\\" . $maxnoform;
				$file[0]->move($tujuan_upload, $filedispo);
			} else {
				$filedispo = '';
				foreach ($file as $key => $data) {
					$filenow = 'disp';

					if ($data->getSize() > 52222222) {
						return redirect('/disposisi/tambah disposisi')->with('message', 'Ukuran file terlalu besar');     
					} 

					$filenow .= $key;
					$filenow .= ($splitmaxform[3]);
					$filenow .= ".". $data->getClientOriginalExtension();

					$tujuan_upload = config('app.savefiledisposisi');
					$tujuan_upload .= "\\" . $diryear;
					$tujuan_upload .= "\\" . $maxnoform;
					$data->move($tujuan_upload, $filenow);

					if ($key != count($file) - 1) {
						$filedispo .= $filenow . "::";
					} else {
						$filedispo .= $filenow;
					}
				}
			}	
		} else {
			$filedispo = '';
		}

		$kepada = '';
		if (isset($request->jabatans)) {
			for ($i=0; $i < count($request->jabatans); $i++) { 
				$kepada .= $request->jabatans[$i];
				if ($i != (count($request->jabatans) - 1)) {
					$kepada .= "::";
				}
			}
		}

		if($request->catatan_final == 'PPID') {
			$kepada = '010101';
		}

		$insertsuratmaster = [
			'sts' => 1,
			'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
			'tgl'       => date('Y-m-d H:i:s'),
			'ip'        => '',
			'logbuat'   => '',
			'kd_skpd'	=> '1.20.512',
			'kd_unit'	=> $request->kd_unit,
			'no_form' => $maxnoform,
			'kd_surat' => $request->kd_surat,
			'status_surat' => $status_surat,
			'idtop' => 0,
			'tgl_masuk' => (isset($request->tgl_masuk) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_masuk))) : date('Y-m-d')),
			'usr_input' => (isset(Auth::user()->usname) ? Auth::user()->usname : Auth::user()->id_emp),
			'tgl_input' => date('Y-m-d H:i:s'),
			'no_index' => (isset($request->no_index) ? $request->no_index : '' ),
			'kode_disposisi' => $request->kode_disposisi,
			'perihal' => (isset($request->perihal) ? $request->perihal : '' ),
			'tgl_surat' => (isset($request->tgl_surat) ? date('m/d/Y', strtotime(strtr($request->tgl_surat, '/', '-'))) : null ),
			'no_surat' => (isset($request->no_surat) ? $request->no_surat : '' ),
			'asal_surat' => (isset($request->asal_surat) ? $request->asal_surat : '' ),
			'kepada_surat' => (isset($request->kepada_surat) ? $request->kepada_surat : '' ),
			'sifat1_surat' => (isset($request->sifat1_surat) ? $request->sifat1_surat : '' ),
			'sifat2_surat' => (isset($request->sifat2_surat) ? $request->sifat2_surat : '' ),
			'ket_lain' => (isset($request->ket_lain) ? $request->ket_lain : '' ),
			'nm_file' => $filedispo,
			'kepada' => $kepada,
			'noid' => '',
			'penanganan' => (isset($request->penanganan) ? $request->penanganan : '' ),
			'catatan' => (isset($request->catatan) ? $request->catatan : '' ),
			'from_user' => '',
			'from_pm' => '',
			'to_user' => '',
			'to_pm' => '',
			'rd' => '',
			'usr_rd' => null,
			'tgl_rd' => null,
			'selesai' => $selesai,
			'child' => $child,
			'catatan_final' => $request->catatan_final,
		];

		Fr_disposisi::insert($insertsuratmaster);
		$idnew = Fr_disposisi::max('ids');

		if ($request->btnDraft) {
			return redirect('/disposisi/formdisposisi')
					->with('message', 'Disposisi berhasil dibuat')
					->with('msg_num', 1);
		}

		if ($request->btnKirim) {

			if (isset($request->jabatans)) {
				for ($i=0; $i < count($request->jabatans); $i++) { 

					if($request->catatan_final == 'PPID') {
						$jab = '010101';
					} else {
						$jab = $request->jabatans[$i];
					}

					$findidemp = DB::select( DB::raw("
							SELECT id_emp,a.uname+'::'+convert(varchar,a.tgl)+'::'+a.ip,createdate,nip_emp,nrk_emp,nm_emp,nrk_emp+'-'+nm_emp as c2,gelar_dpn,gelar_blk,jnkel_emp,tempat_lahir,tgl_lahir,CONVERT(VARCHAR(10), tgl_lahir, 103) AS [DD/MM/YYYY],idagama,alamat_emp,tlp_emp,email_emp,status_emp,ked_emp,status_nikah,gol_darah,nm_bank,cb_bank,an_bank,nr_bank,no_taspen,npwp,no_askes,no_jamsos,tgl_join,CONVERT(VARCHAR(10), tgl_join, 103) AS [DD/MM/YYYY],tgl_end,reason,a.idgroup,pass_emp,foto,ttd,a.telegram_id,a.lastlogin,tbgol.tmt_gol,CONVERT(VARCHAR(10), tbgol.tmt_gol, 103) AS [DD/MM/YYYY],tbgol.tmt_sk_gol,CONVERT(VARCHAR(10), tbgol.tmt_sk_gol, 103) AS [DD/MM/YYYY],tbgol.no_sk_gol,tbgol.idgol,tbgol.jns_kp,tbgol.mk_thn,tbgol.mk_bln,tbgol.gambar,tbgol.nm_pangkat,tbjab.tmt_jab,CONVERT(VARCHAR(10), tbjab.tmt_jab, 103) AS [DD/MM/YYYY],tbjab.idskpd,tbjab.idunit,tbjab.idjab, tbunit.child, tbjab.idlok,tbjab.tmt_sk_jab,CONVERT(VARCHAR(10), tbjab.tmt_sk_jab, 103) AS [DD/MM/YYYY],tbjab.no_sk_jab,tbjab.jns_jab,tbjab.idjab,tbjab.eselon,tbjab.gambar,tbdik.iddik,tbdik.prog_sek,tbdik.no_sek,tbdik.th_sek,tbdik.nm_sek,tbdik.gelar_dpn_sek,tbdik.gelar_blk_sek,tbdik.ijz_cpns,tbdik.gambar,tbdik.nm_dik,b.nm_skpd,c.nm_unit,c.notes,d.nm_lok FROM bpaddtfake.dbo.emp_data as a
								CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM  bpaddtfake.dbo.emp_gol,bpaddtfake.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
								CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
								CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpaddtfake.dbo.emp_dik,bpaddtfake.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
								CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
								,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
								and tbunit.kd_unit like '".$jab."' and ked_emp = 'aktif'") )[0];
								// and tbjab.idjab like '".$request->jabatans[$i]."' and ked_emp = 'aktif'
					$findidemp = json_decode(json_encode($findidemp), true);

					$insertsurat = [
						'sts' => 1,
						'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
						'tgl'       => date('Y-m-d H:i:s'),
						'ip'        => '',
						'logbuat'   => '',
						'kd_skpd'	=> '1.20.512',
						'kd_unit'	=> $request->kd_unit,
						'no_form' => $maxnoform,
						'kd_surat' => null,
						'status_surat' => null,
						'idtop' => $idnew,
						'tgl_masuk' => (isset($request->tgl_masuk) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_masuk))) : date('Y-m-d')),
						'usr_input' => '',
						'tgl_input' => null,
						'no_index' => '',
						'kode_disposisi' => '',
						'perihal' => '',
						'tgl_surat' => null,
						'no_surat' => '',
						'asal_surat' => '',
						'kepada_surat' => '',
						'sifat1_surat' => '',
						'sifat2_surat' => '',
						'ket_lain' => '',
						'nm_file' => '',
						'kepada' => '',
						'noid' => '',
						'penanganan' => '',
						'catatan' => '',
						'from_user' => (Auth::user()->usname ? 'A' : 'E'),
						'from_pm' => (isset(Auth::user()->usname) ? Auth::user()->usname : Auth::user()->id_emp),
						'to_user' => 'E',
						'to_pm' => $findidemp['id_emp'],
						'rd' => 'N',
						'usr_rd' => null,
						'tgl_rd' => null,
						'selesai' => '',
						'child' => 0,
					];
					Fr_disposisi::insert($insertsurat);

					$getlastinsertid = Fr_disposisi::
										where('sts', 1)
										->where('no_form', $maxnoform)
										->orderBy('ids', 'desc')
										->first();

					// NOTIFIKASI BROADCAST kalau ada DISPOSISI BARU 
					$url = "http://10.15.38.80/mobileaset/notif/send"; //release
					// $url = "http://10.15.38.82/mobileasetstaging/notif/send"; //staging
					
					$client = new Client();
					$res = $client->request('GET', $url, [
						'headers' => [
							'Content-Type' => 'application/x-www-form-urlencoded',
						],
						'form_params' => [
							"id_emp" => $findidemp['id_emp'],
							"title" => "Disposisi",
							"message" => "Anda baru saja mendapatkan disposisi baru!! Segera cek aplikasi anda",
							"data" => [
								"type" => "disposisi",
								"ids" => $getlastinsertid['ids'],
							],
						],
					]);

					sleep(2);
				}
					
			}
		}

		DB::statement("
							WITH cte AS (
							SELECT 
							        ids, 
							        idtop,
							        no_form, 
							        from_pm, 
							        to_pm, 
									penanganan,
									catatan,
							        ROW_NUMBER() OVER (
							            PARTITION BY 
							        idtop,
							        no_form, 
							        from_pm, 
							        to_pm,
									penanganan,
									catatan
							            ORDER BY 
							                ids, 
							        no_form, 
							        from_pm, 
							        to_pm
							        ) row_num
							     FROM 
							        bpaddtfake.dbo.fr_disposisi

									where no_form = '$maxnoform'
							)
							DELETE FROM cte
							WHERE row_num > 1;" );

		return redirect('/disposisi/formdisposisi')
					->with('message', 'Disposisi berhasil dibuat')
					->with('msg_num', 1);
	}

	public function formupdatedisposisi(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		// $this->checksession(); //$this->checkSessionTime();

		if (isset($request->jabatans) && isset($request->stafs)) {
			return redirect('/disposisi/ubah disposisi?ids='.$request->ids)
					->with('message', 'Tidak boleh memilih jabatan & staf bersamaan')
					->with('signdate', $request->signdate)
					->with('msg_num', 2);
		}

		if (is_null($request->jabatans)) {
			return redirect('/disposisi/ubah disposisi?ids='.$request->ids)
						->with('message', 'Tujuan disposisi tidak boleh kosong')
						->with('signdate', $request->signdate)
						->with('msg_num', 2);
		}

		if (isset($request->btnDraft)) {
			$status_surat = 'd';
			$selesai = 'Y';
			$child = 0;
		} else {
			// if (count($request->jabatans) > 1 || strpos(strtolower($request->jabatans[0]),"kepala badan") === false ) {
			// if (count($request->jabatans) > 1 || $request->jabatans[0] != '01' ) {
			// 	return redirect('/disposisi/ubah disposisi?ids='.$request->ids)
			// 			->with('message', 'Hanya boleh memilh Kepala Badan untuk memulai alur disposisi')
			// 			->with('signdate', $request->signdate)
			// 			->with('msg_num', 2);
			// }
			$status_surat = 's';
			if (isset($request->jabatans)) {
				$selesai = '';
				$child = 1;
			} elseif (isset($request->stafs)) {
				$selesai = '';
				$child = 1;
			} else {
				$selesai = 'Y';
				$child = 0;
			}
		}

		if ($status_surat == 's' && is_null($request->jabatans) && is_null($request->stafs)) {
			return redirect('/disposisi/ubah disposisi?ids='.$request->ids)
					->with('message', 'Harus memilih untuk melanjutkan disposisi')
					->with('signdate', $request->signdate)
					->with('msg_num', 2);
		}

		$splitmaxform = explode(".", $request->no_form);

		$nowdisposisi = Fr_disposisi::where('ids', $request->ids)->first();
		$filedispo = $nowdisposisi['nm_file'];

		$diryear = date('Y',strtotime($request->tgl_masuk_master));
		if (isset($request->nm_file)) {
			$file = $request->nm_file;
			if (count($file) == 1) {
				
				if ($file[0]->getSize() > 52222222) {
					return redirect('/disposisi/ubah disposisi?ids='.$request->ids)
						->with('message', 'Ukuran file terlalu besar') 
						->with('signdate', $request->signdate)
						->with('msg_num', 2);
				} 

				if ($filedispo != '') {
					$filedispo .= '::';
				}

				$filenow = 'disp';
				$filenow .= (int) date('HIs');
				$filenow .= ($splitmaxform[3]);
				$filenow .= ".". $file[0]->getClientOriginalExtension();

				$tujuan_upload = config('app.savefiledisposisi');
				$tujuan_upload .= "\\" . $diryear;
				$tujuan_upload .= "\\" . $request->no_form;

				$filedispo .= $filenow;

				$file[0]->move($tujuan_upload, $filenow);
			} else {
				if ($filedispo != '') {
					$filedispo .= '::';
				}

				foreach ($file as $key => $data) {

					if ($data->getSize() > 52222222) {
						return redirect('/disposisi/ubah disposisi?ids='.$request->ids)
							->with('message', 'Ukuran file terlalu besar') 
							->with('signdate', $request->signdate)
							->with('msg_num', 2);   
					} 

					$filenow = 'disp';
					$filenow .= (int) date('HIs') + $key;
					$filenow .= ($splitmaxform[3]);
					$filenow .= ".". $data->getClientOriginalExtension();

					$tujuan_upload = config('app.savefiledisposisi');
					$tujuan_upload .= "\\" . $diryear;
					$tujuan_upload .= "\\" . $request->no_form;
					$data->move($tujuan_upload, $filenow);

					// if ($key != count($file) - 1) {
					// 	$filedispo .= $filenow . "::";
					// } else {
					// 	$filedispo .= $filenow;
					// }
				
					if ($key != 0) {
						$filedispo .= "::";
					} 
					$filedispo .= $filenow;

				}
			}
			Fr_disposisi::where('ids', $request->ids)
			->update([
				'nm_file' => $filedispo,
			]);	
		}

		$kepada = '';
		if (isset($request->jabatans)) {
			for ($i=0; $i < count($request->jabatans); $i++) { 
				$kepada .= $request->jabatans[$i];
				if ($i != (count($request->jabatans) - 1)) {
					$kepada .= "::";
				}
			}
		}

		if($request->catatan_final == 'PPID') {
			$kepada = '010101';
		}

		Fr_disposisi::where('ids', $request->ids)
			->update([
			'kd_unit'	=> $request->kd_unit,
			'status_surat' => $status_surat,
			'tgl_masuk' => (isset($request->tgl_masuk) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_masuk))) : date('Y-m-d')),
			'no_index' => (isset($request->no_index) ? $request->no_index : '' ),
			'kode_disposisi' => $request->kode_disposisi,
			'perihal' => (isset($request->perihal) ? $request->perihal : '' ),
			'tgl_surat' => (isset($request->tgl_surat) ? date('m/d/Y', strtotime(strtr($request->tgl_surat, '/', '-'))) : null ),
			'no_surat' => (isset($request->no_surat) ? $request->no_surat : '' ),
			'asal_surat' => (isset($request->asal_surat) ? $request->asal_surat : '' ),
			'kepada_surat' => (isset($request->kepada_surat) ? $request->kepada_surat : '' ),
			'sifat1_surat' => (isset($request->sifat1_surat) ? $request->sifat1_surat : '' ),
			'sifat2_surat' => (isset($request->sifat2_surat) ? $request->sifat2_surat : '' ),
			'ket_lain' => (isset($request->ket_lain) ? $request->ket_lain : '' ),
			'kepada' => $kepada,
			'penanganan' => (isset($request->penanganan) ? $request->penanganan : '' ),
			'catatan' => (isset($request->catatan) ? $request->catatan : '' ),
			'selesai' => $selesai,
			'child' => $child,
			'catatan_final' => $request->catatan_final,
		]);
		$idnew = $request->ids;

		if ($request->btnDraft) {
			$splitsigndate = explode("::", $request->signdate);
			$yearnow = $splitsigndate[0];
			$signnow = $splitsigndate[1];
			$monthnow = $splitsigndate[2];
			return redirect('/disposisi/formdisposisi?yearnow='.$yearnow.'&signnow='.$signnow.'&monthnow='.$monthnow)
					->with('message', 'Disposisi berhasil diubah')
					->with('msg_num', 1);
		}

		if ($request->btnKirim) {

			if (isset($request->jabatans)) {
				for ($i=0; $i < count($request->jabatans); $i++) { 

					if($request->catatan_final == 'PPID') {
						$jab = '010101';
					} else {
						$jab = $request->jabatans[$i];
					}

					$findidemp = DB::select( DB::raw("
							SELECT id_emp,a.uname+'::'+convert(varchar,a.tgl)+'::'+a.ip,createdate,nip_emp,nrk_emp,nm_emp,nrk_emp+'-'+nm_emp as c2,gelar_dpn,gelar_blk,jnkel_emp,tempat_lahir,tgl_lahir,CONVERT(VARCHAR(10), tgl_lahir, 103) AS [DD/MM/YYYY],idagama,alamat_emp,tlp_emp,email_emp,status_emp,ked_emp,status_nikah,gol_darah,nm_bank,cb_bank,an_bank,nr_bank,no_taspen,npwp,no_askes,no_jamsos,tgl_join,CONVERT(VARCHAR(10), tgl_join, 103) AS [DD/MM/YYYY],tgl_end,reason,a.idgroup,pass_emp,foto,ttd,a.telegram_id,a.lastlogin,tbgol.tmt_gol,CONVERT(VARCHAR(10), tbgol.tmt_gol, 103) AS [DD/MM/YYYY],tbgol.tmt_sk_gol,CONVERT(VARCHAR(10), tbgol.tmt_sk_gol, 103) AS [DD/MM/YYYY],tbgol.no_sk_gol,tbgol.idgol,tbgol.jns_kp,tbgol.mk_thn,tbgol.mk_bln,tbgol.gambar,tbgol.nm_pangkat,tbjab.tmt_jab,CONVERT(VARCHAR(10), tbjab.tmt_jab, 103) AS [DD/MM/YYYY],tbjab.idskpd,tbjab.idunit,tbjab.idjab, tbunit.child, tbjab.idlok,tbjab.tmt_sk_jab,CONVERT(VARCHAR(10), tbjab.tmt_sk_jab, 103) AS [DD/MM/YYYY],tbjab.no_sk_jab,tbjab.jns_jab,tbjab.idjab,tbjab.eselon,tbjab.gambar,tbdik.iddik,tbdik.prog_sek,tbdik.no_sek,tbdik.th_sek,tbdik.nm_sek,tbdik.gelar_dpn_sek,tbdik.gelar_blk_sek,tbdik.ijz_cpns,tbdik.gambar,tbdik.nm_dik,b.nm_skpd,c.nm_unit,c.notes,d.nm_lok FROM bpaddtfake.dbo.emp_data as a
								CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM  bpaddtfake.dbo.emp_gol,bpaddtfake.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
								CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
								CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpaddtfake.dbo.emp_dik,bpaddtfake.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
								CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
								,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
								and tbunit.kd_unit like '".$jab."' and ked_emp = 'aktif'") )[0];
								// and tbjab.idjab like '".$request->jabatans[$i]."' and ked_emp = 'aktif'
					$findidemp = json_decode(json_encode($findidemp), true);

					$insertsurat = [
						'sts' => 1,
						'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
						'tgl'       => date('Y-m-d H:i:s'),
						'ip'        => '',
						'logbuat'   => '',
						'kd_skpd'	=> '1.20.512',
						'kd_unit'	=> $request->kd_unit,
						'no_form' => $request->no_form,
						'kd_surat' => null,
						'status_surat' => null,
						'idtop' => $idnew,
						'tgl_masuk' => (isset($request->tgl_masuk) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_masuk))) : date('Y-m-d')),
						'usr_input' => '',
						'tgl_input' => null,
						'no_index' => '',
						'kode_disposisi' => '',
						'perihal' => '',
						'tgl_surat' => null,
						'no_surat' => '',
						'asal_surat' => '',
						'kepada_surat' => '',
						'sifat1_surat' => '',
						'sifat2_surat' => '',
						'ket_lain' => '',
						'nm_file' => '',
						'kepada' => '',
						'noid' => '',
						'penanganan' => '',
						'catatan' => '',
						'from_user' => (Auth::user()->usname ? 'A' : 'E'),
						'from_pm' => (isset(Auth::user()->usname) ? Auth::user()->usname : Auth::user()->id_emp),
						'to_user' => 'E',
						'to_pm' => $findidemp['id_emp'],
						'rd' => 'N',
						'usr_rd' => null,
						'tgl_rd' => null,
						'selesai' => '',
						'child' => 0,
					];
					Fr_disposisi::insert($insertsurat);

					$getlastinsertid = Fr_disposisi::
										where('sts', 1)
										->where('no_form', $request->no_form)
										->orderBy('ids', 'desc')
										->first();

					// NOTIFIKASI BROADCAST kalau ada DISPOSISI BARU 
					$url = "http://10.15.38.80/mobileaset/notif/send"; //release
					// $url = "http://10.15.38.82/mobileasetstaging/notif/send"; //staging
					
					$client = new Client();
					$res = $client->request('GET', $url, [
						'headers' => [
							'Content-Type' => 'application/x-www-form-urlencoded',
						],
						'form_params' => [
							"id_emp" => $findidemp['id_emp'],
							"title" => "Disposisi",
							"message" => "Anda baru saja mendapatkan disposisi baru!! Segera cek aplikasi anda",
							"data" => [
								"type" => "disposisi",
								"ids" => $getlastinsertid['ids'],
							],
						],
					]);

					sleep(2);
				}
					
			}

			// if (isset($request->stafs)) {
			// 	for ($i=0; $i < count($request->stafs); $i++) { 
			// 		$findidemp = DB::select( DB::raw("
			// 				SELECT id_emp,a.uname+'::'+convert(varchar,a.tgl)+'::'+a.ip,createdate,nip_emp,nrk_emp,nm_emp,nrk_emp+'-'+nm_emp as c2,gelar_dpn,gelar_blk,jnkel_emp,tempat_lahir,tgl_lahir,CONVERT(VARCHAR(10), tgl_lahir, 103) AS [DD/MM/YYYY],idagama,alamat_emp,tlp_emp,email_emp,status_emp,ked_emp,status_nikah,gol_darah,nm_bank,cb_bank,an_bank,nr_bank,no_taspen,npwp,no_askes,no_jamsos,tgl_join,CONVERT(VARCHAR(10), tgl_join, 103) AS [DD/MM/YYYY],tgl_end,reason,a.idgroup,pass_emp,foto,ttd,a.telegram_id,a.lastlogin,tbgol.tmt_gol,CONVERT(VARCHAR(10), tbgol.tmt_gol, 103) AS [DD/MM/YYYY],tbgol.tmt_sk_gol,CONVERT(VARCHAR(10), tbgol.tmt_sk_gol, 103) AS [DD/MM/YYYY],tbgol.no_sk_gol,tbgol.idgol,tbgol.jns_kp,tbgol.mk_thn,tbgol.mk_bln,tbgol.gambar,tbgol.nm_pangkat,tbjab.tmt_jab,CONVERT(VARCHAR(10), tbjab.tmt_jab, 103) AS [DD/MM/YYYY],tbjab.idskpd,tbjab.idunit,tbjab.idjab, tbunit.child, tbjab.idlok,tbjab.tmt_sk_jab,CONVERT(VARCHAR(10), tbjab.tmt_sk_jab, 103) AS [DD/MM/YYYY],tbjab.no_sk_jab,tbjab.jns_jab,tbjab.idjab,tbjab.eselon,tbjab.gambar,tbdik.iddik,tbdik.prog_sek,tbdik.no_sek,tbdik.th_sek,tbdik.nm_sek,tbdik.gelar_dpn_sek,tbdik.gelar_blk_sek,tbdik.ijz_cpns,tbdik.gambar,tbdik.nm_dik,b.nm_skpd,c.nm_unit,c.notes,d.nm_lok FROM bpaddtfake.dbo.emp_data as a
			// 					CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM  bpaddtfake.dbo.emp_gol,bpaddtfake.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
			// 					CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
			// 					CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpaddtfake.dbo.emp_dik,bpaddtfake.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
			// 					CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
			// 					,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
			// 					and id_emp like '".$request->stafs[$i]."' and ked_emp = 'aktif'") )[0];
			// 		$findidemp = json_decode(json_encode($findidemp), true);

			// 		$insertsurat = [
			// 			'sts' => 1,
			// 			'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
			// 			'tgl'       => date('Y-m-d H:i:s'),
			// 			'ip'        => '',
			// 			'logbuat'   => '',
			// 			'kd_skpd'	=> '1.20.512',
			// 			'kd_unit'	=> $request->kd_unit,
			// 			'no_form' => $maxnoform,
			// 			'kd_surat' => null,
			// 			'status_surat' => null,
			// 			'idtop' => $idnew,
			// 			'tgl_masuk' => (isset($request->tgl_masuk) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_masuk))) : date('Y-m-d')),
			// 			'usr_input' => '',
			// 			'tgl_input' => null,
			// 			'no_index' => '',
			// 			'kode_disposisi' => '',
			// 			'perihal' => '',
			// 			'tgl_surat' => null,
			// 			'no_surat' => '',
			// 			'asal_surat' => '',
			// 			'kepada_surat' => '',
			// 			'sifat1_surat' => '',
			// 			'sifat2_surat' => '',
			// 			'ket_lain' => '',
			// 			'nm_file' => '',
			// 			'kepada' => ($request->jabatans[0] ? $request->jabatans[0] : ''),
			// 			'noid' => '',
			// 			'penanganan' => '',
			// 			'catatan' => '',
			// 			'from_user' => (Auth::user()->usname ? 'A' : 'E'),
			// 			'from_pm' => (isset(Auth::user()->usname) ? Auth::user()->usname : Auth::user()->id_emp),
			// 			'to_user' => 'E',
			// 			'to_pm' => $findidemp['id_emp'],
			// 			'rd' => 'N',
			// 			'usr_rd' => null,
			// 			'tgl_rd' => null,
			// 			'selesai' => '',
			// 			'child' => 0,
			// 		];
			// 		Fr_disposisi::insert($insertsurat);
			// 	}
			// }
		}

		$splitsigndate = explode("::", $request->signdate);
		$yearnow = $splitsigndate[0];
		$signnow = $splitsigndate[1];
		$monthnow = $splitsigndate[2];
		return redirect('/disposisi/formdisposisi?yearnow='.$yearnow.'&signnow='.$signnow.'&monthnow='.$monthnow)
					->with('message', 'Disposisi berhasil dikirim')
					->with('msg_num', 1);
	}

	public function formdeletedisposisi(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}

		Fr_disposisi::where('no_form', $request->no_form)
		->update([
			'sts' => 0,
            'delete_date' => date('Y-m-d H:i:s'),
            'delete_user' => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
		]);

		return 0;
	}

	public function formresetdisposisi(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		
		$thisdisp = Fr_disposisi::where('no_form', $request->no_form)->get();
		foreach ($thisdisp as $key => $dis) {
			if ($dis['status_surat'] == 's') {
				Fr_disposisi::where('ids', $dis['ids'])
				->update([
					'status_surat' => 'd',
					'selesai' => 'Y',
					'child' => 0,
				]);
			} else {
				Fr_disposisi::where('ids', $dis['ids'])
				->update([
					'sts' => 0,
				]);
			}
		}

		return 0;
	}

	// ---------/ADMIN----------- //

	// ---------EMPLOYEE----------- //

	public function disposisi(Request $request)
	{
		// $this->checksession(); //$this->checkSessionTime();
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
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
			$signnow = "<=";
		}

		if ($request->searchnow) {
			$qsearchnow = "and (
									m.kd_surat like '%".$request->searchnow."%' or 
									m.no_form like '%".$request->searchnow."%' or 
									m.perihal like '%".$request->searchnow."%' or 
									m.asal_surat like '%".$request->searchnow."%' or 
									m.no_surat like '%".$request->searchnow."%' or 
									m.kode_disposisi like '%".$request->searchnow."%' or 
									m.no_surat + '/' + m.kode_disposisi like '%".$request->searchnow."%')";
		} else {
			$qsearchnow = "";
		}

		$idgroup = $_SESSION['user_data']['id_emp'];
		if (is_null($idgroup)) {
			$qid = '';
		} else {
			$qid = "and d.to_pm = '".$idgroup."'";
		}

		$distinctyear = Fr_disposisi::distinct()
						->whereRaw('YEAR(tgl_masuk) > 2016')
						->whereRaw('YEAR(tgl_masuk) <= '.date('Y'))
						->orderBy('year', 'desc')
						->get([DB::raw('YEAR(tgl_masuk) as year')]);

		$tglnow = (int)date('d');
		$tgllengkap = $yearnow . "-" . $monthnow . "-" . $tglnow;

		$dispinboxundangan = DB::select( DB::raw("SELECT TOP (1000) d.[ids]
												  ,d.[sts]
												  ,m.sts as stsmaster
												  ,d.[uname]
												  ,d.[tgl]
												  ,m.tgl as tglmaster
												  ,d.[ip]
												  ,d.[logbuat]
												  ,d.[kd_skpd]
												  ,d.[kd_unit]
												  ,d.[no_form]
												  ,m.[kd_surat]
												  ,d.[status_surat]
												  ,d.[idtop]
												  ,t.ids as parent
												  ,d.[tgl_masuk]
												  ,d.[usr_input]
												  ,d.[tgl_input]
												  ,m.[no_index]
												  ,m.[kode_disposisi]
												  ,m.[perihal]
												  ,m.[tgl_surat]
												  ,m.[no_surat]
												  ,m.[asal_surat]
												  ,m.[kepada_surat]
												  ,m.[sifat1_surat]
												  ,m.[sifat2_surat]
												  ,d.[ket_lain]
												  ,m.[nm_file]
												  ,t.[kepada]
												  ,d.[noid]
												  ,t.[penanganan]
												  ,t.[catatan]
												  ,d.[from_user]
												  ,d.[from_pm]
												  ,emp1.nm_emp as from_nm
												  ,d.[to_user]
												  ,d.[to_pm]
												  ,emp2.nm_emp as to_nm
												  ,d.[rd]
												  ,d.[usr_rd]
												  ,d.[tgl_rd]
												  ,d.[selesai]
												  ,d.[child]
												  ,m.[catatan_final]
												  FROM [bpaddtfake].[dbo].[fr_disposisi] d
												  left join bpaddtfake.dbo.emp_data as emp1 on emp1.id_emp = d.from_pm
												  left join bpaddtfake.dbo.emp_data as emp2 on emp2.id_emp = d.to_pm
												  left join bpaddtfake.dbo.fr_disposisi as t on t.ids = d.idtop
												  left join bpaddtfake.dbo.fr_disposisi as m on m.no_form = d.no_form and m.idtop = 0
												  where (d.rd like 'Y' or d.rd like 'N')
												  and month(m.tgl_masuk) $signnow $monthnow
												  and year(m.tgl_masuk) = $yearnow
												  and m.sts = 1
												  and d.sts = 1
												  and m.catatan_final = 'undangan'
												  AND d.idtop > 0 AND d.child = 0
												  $qid
												  $qsearchnow
												  order by d.tgl_masuk desc, d.no_form desc, d.ids asc"));
		$dispinboxundangan = json_decode(json_encode($dispinboxundangan), true);

		$dispinboxsurat = DB::select( DB::raw("SELECT TOP (1000) d.[ids]
												  ,d.[sts]
												  ,m.sts as stsmaster
												  ,d.[uname]
												  ,d.[tgl]
												  ,m.tgl as tglmaster
												  ,d.[ip]
												  ,d.[logbuat]
												  ,d.[kd_skpd]
												  ,d.[kd_unit]
												  ,d.[no_form]
												  ,m.[kd_surat]
												  ,d.[status_surat]
												  ,d.[idtop]
												  ,t.ids as parent
												  ,d.[tgl_masuk]
												  ,d.[usr_input]
												  ,d.[tgl_input]
												  ,m.[no_index]
												  ,m.[kode_disposisi]
												  ,m.[perihal]
												  ,m.[tgl_surat]
												  ,m.[no_surat]
												  ,m.[asal_surat]
												  ,m.[kepada_surat]
												  ,m.[sifat1_surat]
												  ,m.[sifat2_surat]
												  ,d.[ket_lain]
												  ,m.[nm_file]
												  ,t.[kepada]
												  ,d.[noid]
												  ,t.[penanganan]
												  ,t.[catatan]
												  ,d.[from_user]
												  ,d.[from_pm]
												  ,emp1.nm_emp as from_nm
												  ,d.[to_user]
												  ,d.[to_pm]
												  ,emp2.nm_emp as to_nm
												  ,d.[rd]
												  ,d.[usr_rd]
												  ,d.[tgl_rd]
												  ,d.[selesai]
												  ,d.[child]
												  ,m.[catatan_final]
												  FROM [bpaddtfake].[dbo].[fr_disposisi] d
												  left join bpaddtfake.dbo.emp_data as emp1 on emp1.id_emp = d.from_pm
												  left join bpaddtfake.dbo.emp_data as emp2 on emp2.id_emp = d.to_pm
												  left join bpaddtfake.dbo.fr_disposisi as t on t.ids = d.idtop
												  left join bpaddtfake.dbo.fr_disposisi as m on m.no_form = d.no_form and m.idtop = 0
												  where (d.rd like 'Y' or d.rd like 'N')
												  and month(m.tgl_masuk) $signnow $monthnow
												  and year(m.tgl_masuk) = $yearnow
												  and m.sts = 1
												  and d.sts = 1
												  and ((m.catatan_final <> 'undangan' and m.catatan_final <> 'ppid') or m.catatan_final is null )
												  AND d.idtop > 0 AND d.child = 0
												  $qid
												  $qsearchnow
												  order by d.tgl_masuk desc, d.no_form desc, d.ids asc"));
		$dispinboxsurat = json_decode(json_encode($dispinboxsurat), true);

		$dispinboxppid = DB::select( DB::raw("SELECT TOP (1000) d.[ids]
												  ,d.[sts]
												  ,m.sts as stsmaster
												  ,d.[uname]
												  ,d.[tgl]
												  ,m.tgl as tglmaster
												  ,d.[ip]
												  ,d.[logbuat]
												  ,d.[kd_skpd]
												  ,d.[kd_unit]
												  ,d.[no_form]
												  ,m.[kd_surat]
												  ,d.[status_surat]
												  ,d.[idtop]
												  ,t.ids as parent
												  ,d.[tgl_masuk]
												  ,d.[usr_input]
												  ,d.[tgl_input]
												  ,m.[no_index]
												  ,m.[kode_disposisi]
												  ,m.[perihal]
												  ,m.[tgl_surat]
												  ,m.[no_surat]
												  ,m.[asal_surat]
												  ,m.[kepada_surat]
												  ,m.[sifat1_surat]
												  ,m.[sifat2_surat]
												  ,d.[ket_lain]
												  ,m.[nm_file]
												  ,t.[kepada]
												  ,d.[noid]
												  ,t.[penanganan]
												  ,t.[catatan]
												  ,d.[from_user]
												  ,d.[from_pm]
												  ,emp1.nm_emp as from_nm
												  ,d.[to_user]
												  ,d.[to_pm]
												  ,emp2.nm_emp as to_nm
												  ,d.[rd]
												  ,d.[usr_rd]
												  ,d.[tgl_rd]
												  ,d.[selesai]
												  ,d.[child]
												  ,m.[catatan_final]
												  FROM [bpaddtfake].[dbo].[fr_disposisi] d
												  left join bpaddtfake.dbo.emp_data as emp1 on emp1.id_emp = d.from_pm
												  left join bpaddtfake.dbo.emp_data as emp2 on emp2.id_emp = d.to_pm
												  left join bpaddtfake.dbo.fr_disposisi as t on t.ids = d.idtop
												  left join bpaddtfake.dbo.fr_disposisi as m on m.no_form = d.no_form and m.idtop = 0
												  where (d.rd like 'Y' or d.rd like 'N')
												  and month(m.tgl_masuk) $signnow $monthnow
												  and year(m.tgl_masuk) = $yearnow
												  and m.sts = 1
												  and d.sts = 1
												  and m.catatan_final = 'ppid'
												  AND d.idtop > 0 AND d.child = 0
												  $qid
												  $qsearchnow
												  order by d.tgl_masuk desc, d.no_form desc, d.ids asc"));
		$dispinboxppid = json_decode(json_encode($dispinboxppid), true);

		$dispdraft = DB::select( DB::raw("SELECT TOP (1000) d.[ids]
												  ,d.[sts]
												  ,m.sts as stsmaster
												  ,d.[uname]
												  ,d.[tgl]
												  ,m.tgl as tglmaster
												  ,d.[ip]
												  ,d.[logbuat]
												  ,d.[kd_skpd]
												  ,d.[kd_unit]
												  ,d.[no_form]
												  ,m.[kd_surat]
												  ,d.[status_surat]
												  ,d.[idtop]
												  ,d.[tgl_masuk]
												  ,d.[usr_input]
												  ,d.[tgl_input]
												  ,m.[no_index]
												  ,m.[kode_disposisi]
												  ,m.[perihal]
												  ,m.[tgl_surat]
												  ,m.[no_surat]
												  ,m.[asal_surat]
												  ,m.[kepada_surat]
												  ,m.[sifat1_surat]
												  ,m.[sifat2_surat]
												  ,d.[ket_lain]
												  ,m.[nm_file]
												  ,d.[kepada]
												  ,d.[noid]
												  ,d.[penanganan]
												  ,d.[catatan]
												  ,d.[from_user]
												  ,d.[from_pm]
												  ,emp1.nm_emp as from_nm
												  ,d.[to_user]
												  ,d.[to_pm]
												  ,emp2.nm_emp as to_nm
												  ,d.[rd]
												  ,d.[usr_rd]
												  ,d.[tgl_rd]
												  ,d.[selesai]
												  ,d.[child]
												  FROM [bpaddtfake].[dbo].[fr_disposisi] d
												  left join bpaddtfake.dbo.emp_data as emp1 on emp1.id_emp = d.from_pm
												  left join bpaddtfake.dbo.emp_data as emp2 on emp2.id_emp = d.to_pm
												  left join bpaddtfake.dbo.fr_disposisi as m on m.no_form = d.no_form and m.idtop = 0
												  where d.rd like 'D'
												  and month(m.tgl_masuk) $signnow $monthnow
												  and year(m.tgl_masuk) = $yearnow
												  and m.sts = 1
												  and d.sts = 1
												  AND d.idtop > 0 AND d.child = 0
												  $qid
												  $qsearchnow
												  order by d.tgl_masuk desc, d.no_form desc"));
		$dispdraft = json_decode(json_encode($dispdraft), true);

		if (strlen($_SESSION['user_data']['idunit']) == 8) {
			$rd = "";
			$qid = "d.from_pm = '".$idgroup."'";
			$or = "or (d.to_pm = '".$idgroup."' and d.selesai = 'Y')";
			// $rd = "(d.rd like 'N' or d.rd like 'Y')";
		} elseif (strlen($_SESSION['user_data']['idunit']) == 10) {
			$qid = "(d.to_pm = '".$idgroup."' and d.selesai = 'Y')";
			$rd = "";
			$or = "";
		} else {
			$rd = "";
			$or = "or (d.to_pm = '".$idgroup."' and d.selesai = 'Y' and d.child = 0)";
			$qid = "d.from_pm = '".$idgroup."'";
		}

		$dispsentundangan = DB::select( DB::raw("SELECT TOP (1000) d.[ids]
												  ,d.[sts]
												  ,m.sts as stsmaster
												  ,d.[uname]
												  ,d.[tgl]
												  ,m.tgl as tglmaster
												  ,d.[ip]
												  ,d.[logbuat]
												  ,d.[kd_skpd]
												  ,d.[kd_unit]
												  ,d.[no_form]
												  ,m.[kd_surat]
												  ,d.[status_surat]
												  ,d.[idtop]
												  ,t.ids as parent
												  ,t.penanganan as penanganantop
												  ,t.catatan as catatantop
												  ,d.[tgl_masuk]
												  ,d.[usr_input]
												  ,d.[tgl_input]
												  ,m.[no_index]
												  ,m.[kode_disposisi]
												  ,m.[perihal]
												  ,m.[tgl_surat]
												  ,m.[no_surat]
												  ,m.[asal_surat]
												  ,m.[kepada_surat]
												  ,m.[sifat1_surat]
												  ,m.[sifat2_surat]
												  ,d.[ket_lain]
												  ,m.[nm_file]
												  ,d.[kepada]
												  ,d.[noid]
												  ,d.[penanganan]
												  ,d.[catatan]
												  ,d.[from_user]
												  ,d.[from_pm]
												  ,emp1.nm_emp as from_nm
												  ,d.[to_user]
												  ,d.[to_pm]
												  ,emp2.nm_emp as to_nm
												  ,d.[rd] as rddisp
												  ,m.rd as rdmaster
												  ,d.[usr_rd]
												  ,d.[tgl_rd]
												  ,d.[selesai]
												  ,d.[child]
												  ,m.[catatan_final]
												  FROM [bpaddtfake].[dbo].[fr_disposisi] d
												  left join bpaddtfake.dbo.emp_data as emp1 on emp1.id_emp = d.from_pm
												  left join bpaddtfake.dbo.emp_data as emp2 on emp2.id_emp = d.to_pm
												  left join bpaddtfake.dbo.fr_disposisi as t on t.ids = d.idtop
												  left join bpaddtfake.dbo.fr_disposisi as m on m.no_form = d.no_form and m.idtop = 0
												  where month(m.tgl_masuk) $signnow $monthnow
												  and year(m.tgl_masuk) = $yearnow
												  and m.sts = 1
												  and d.sts = 1
												  and m.catatan_final = 'undangan'
												  $qsearchnow
												  and (
												  ($rd $qid)
												  $or)
												  order by d.tgl_masuk desc, d.no_form desc, d.ids asc"));
		$dispsentundangan = json_decode(json_encode($dispsentundangan), true);

		$dispsentsurat = DB::select( DB::raw("SELECT TOP (1000) d.[ids]
												  ,d.[sts]
												  ,m.sts as stsmaster
												  ,d.[uname]
												  ,d.[tgl]
												  ,m.tgl as tglmaster
												  ,d.[ip]
												  ,d.[logbuat]
												  ,d.[kd_skpd]
												  ,d.[kd_unit]
												  ,d.[no_form]
												  ,m.[kd_surat]
												  ,d.[status_surat]
												  ,d.[idtop]
												  ,t.ids as parent
												  ,t.penanganan as penanganantop
												  ,t.catatan as catatantop
												  ,d.[tgl_masuk]
												  ,d.[usr_input]
												  ,d.[tgl_input]
												  ,m.[no_index]
												  ,m.[kode_disposisi]
												  ,m.[perihal]
												  ,m.[tgl_surat]
												  ,m.[no_surat]
												  ,m.[asal_surat]
												  ,m.[kepada_surat]
												  ,m.[sifat1_surat]
												  ,m.[sifat2_surat]
												  ,d.[ket_lain]
												  ,m.[nm_file]
												  ,d.[kepada]
												  ,d.[noid]
												  ,d.[penanganan]
												  ,d.[catatan]
												  ,d.[from_user]
												  ,d.[from_pm]
												  ,emp1.nm_emp as from_nm
												  ,d.[to_user]
												  ,d.[to_pm]
												  ,emp2.nm_emp as to_nm
												  ,d.[rd] as rddisp
												  ,m.rd as rdmaster
												  ,d.[usr_rd]
												  ,d.[tgl_rd]
												  ,d.[selesai]
												  ,d.[child]
												  ,m.[catatan_final]
												  FROM [bpaddtfake].[dbo].[fr_disposisi] d
												  left join bpaddtfake.dbo.emp_data as emp1 on emp1.id_emp = d.from_pm
												  left join bpaddtfake.dbo.emp_data as emp2 on emp2.id_emp = d.to_pm
												  left join bpaddtfake.dbo.fr_disposisi as t on t.ids = d.idtop
												  left join bpaddtfake.dbo.fr_disposisi as m on m.no_form = d.no_form and m.idtop = 0
												  where month(m.tgl_masuk) $signnow $monthnow
												  and year(m.tgl_masuk) = $yearnow
												  and m.sts = 1
												  and d.sts = 1
												  and ((m.catatan_final <> 'undangan' and m.catatan_final <> 'ppid') or m.catatan_final is null )
												  $qsearchnow
												  and (
												  ($rd $qid)
												  $or)
												  order by d.tgl_masuk desc, d.no_form desc, d.ids asc"));
		$dispsentsurat = json_decode(json_encode($dispsentsurat), true);

		$dispsentppid = DB::select( DB::raw("SELECT TOP (1000) d.[ids]
												  ,d.[sts]
												  ,m.sts as stsmaster
												  ,d.[uname]
												  ,d.[tgl]
												  ,m.tgl as tglmaster
												  ,d.[ip]
												  ,d.[logbuat]
												  ,d.[kd_skpd]
												  ,d.[kd_unit]
												  ,d.[no_form]
												  ,m.[kd_surat]
												  ,d.[status_surat]
												  ,d.[idtop]
												  ,t.ids as parent
												  ,t.penanganan as penanganantop
												  ,t.catatan as catatantop
												  ,d.[tgl_masuk]
												  ,d.[usr_input]
												  ,d.[tgl_input]
												  ,m.[no_index]
												  ,m.[kode_disposisi]
												  ,m.[perihal]
												  ,m.[tgl_surat]
												  ,m.[no_surat]
												  ,m.[asal_surat]
												  ,m.[kepada_surat]
												  ,m.[sifat1_surat]
												  ,m.[sifat2_surat]
												  ,d.[ket_lain]
												  ,m.[nm_file]
												  ,d.[kepada]
												  ,d.[noid]
												  ,d.[penanganan]
												  ,d.[catatan]
												  ,d.[from_user]
												  ,d.[from_pm]
												  ,emp1.nm_emp as from_nm
												  ,d.[to_user]
												  ,d.[to_pm]
												  ,emp2.nm_emp as to_nm
												  ,d.[rd] as rddisp
												  ,m.rd as rdmaster
												  ,d.[usr_rd]
												  ,d.[tgl_rd]
												  ,d.[selesai]
												  ,d.[child]
												  ,m.[catatan_final]
												  FROM [bpaddtfake].[dbo].[fr_disposisi] d
												  left join bpaddtfake.dbo.emp_data as emp1 on emp1.id_emp = d.from_pm
												  left join bpaddtfake.dbo.emp_data as emp2 on emp2.id_emp = d.to_pm
												  left join bpaddtfake.dbo.fr_disposisi as t on t.ids = d.idtop
												  left join bpaddtfake.dbo.fr_disposisi as m on m.no_form = d.no_form and m.idtop = 0
												  where month(m.tgl_masuk) $signnow $monthnow
												  and year(m.tgl_masuk) = $yearnow
												  and m.sts = 1
												  and d.sts = 1
												  and m.catatan_final = 'ppid'
												  $qsearchnow
												  and (
												  ($rd $qid)
												  $or)
												  order by d.tgl_masuk desc, d.no_form desc, d.ids asc"));
		$dispsentppid = json_decode(json_encode($dispsentppid), true);

		return view('pages.bpaddisposisi.disposisi')
				->with('access', $access)
				->with('dispinboxundangan', $dispinboxundangan)
				->with('dispinboxsurat', $dispinboxsurat)
				->with('dispinboxppid', $dispinboxppid)
				->with('dispsentundangan', $dispsentundangan)
				->with('dispsentsurat', $dispsentsurat)
				->with('dispsentppid', $dispsentppid)
				->with('dispdraft', $dispdraft)
				->with('distinctyear', $distinctyear)
				->with('signnow', $signnow)
				->with('searchnow', $request->searchnow)
				->with('monthnow', $monthnow)
				->with('yearnow', $yearnow)
				->with('notifs', $notifs);
	}

	public function disposisilihat(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}

		$dispmaster = DB::select( DB::raw("SELECT d.[ids]
												  ,m.ids as idmaster
												  ,d.[sts]
												  ,d.[uname]
												  ,d.[tgl]
												  ,m.tgl as tglmaster
												  ,d.[ip]
												  ,d.[logbuat]
												  ,d.[kd_skpd]
												  ,d.[kd_unit]
												  --,d.nm_unit
												  ,d.[no_form]
												  ,m.[kd_surat]
												  ,d.[status_surat]
												  ,d.[idtop]
												  ,d.[tgl_masuk]
												  ,d.[usr_input]
												  ,d.[tgl_input]
												  ,m.[no_index]
												  ,m.[kode_disposisi]
												  --,m.nm_jnssurat
												  ,m.[perihal]
												  ,m.[tgl_surat]
												  ,m.[no_surat]
												  ,m.[asal_surat]
												  ,m.[kepada_surat]
												  ,m.[sifat1_surat]
												  ,m.[sifat2_surat]
												  ,d.[ket_lain]
												  ,m.[nm_file]
												  ,d.[kepada]
												  ,d.[noid]
												  ,d.[penanganan]
												  ,d.[catatan]
												  ,d.[from_user]
												  ,d.[from_pm]
												  ,emp1.nm_emp as from_nm
												  ,d.[to_user]
												  ,d.[to_pm]
												  ,emp2.nm_emp as to_nm
												  ,d.[rd]
												  ,d.[usr_rd]
												  ,d.[tgl_rd]
												  ,d.[selesai]
												  ,d.[child]
												  ,m.[catatan_final]
												  FROM [bpaddtfake].[dbo].[fr_disposisi] d
												  left join bpaddtfake.dbo.emp_data as emp1 on emp1.id_emp = d.from_pm
												  left join bpaddtfake.dbo.emp_data as emp2 on emp2.id_emp = d.to_pm
												  join bpaddtfake.dbo.fr_disposisi as m on m.no_form = d.no_form and m.idtop = 0 and m.sts = 1
												  --join bpaddtfake.dbo.glo_org_unitkerja as unit on unit.kd_unit = d.kd_unit
												  --join bpaddtfake.dbo.Glo_disposisi_kode as kode on kode.kd_jnssurat = m.kode_disposisi 
												  where d.ids = '$request->ids'
												  and d.sts = 1"))[0];
		$dispmaster = json_decode(json_encode($dispmaster), true); 

		$kddispos = Glo_disposisi_kode::where('kd_jnssurat', $dispmaster['kode_disposisi'])->orderBy('kd_jnssurat')->first();
		$unitkerjas = Glo_org_unitkerja::where('kd_unit', $dispmaster['kd_unit'])->orderBy('kd_unit')->first();

		$to_pm = $dispmaster['to_pm'];
		$tujuan = DB::select( DB::raw("SELECT id_emp, tbjab.idunit FROM bpaddtfake.dbo.emp_data as a
			CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
			CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
			,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
			and id_emp like '$to_pm'"))[0];
		$tujuan = json_decode(json_encode($tujuan), true);

		if (strtolower($dispmaster['rd']) == 'n') {
			Fr_disposisi::where('ids', $dispmaster['ids'])
			->update([
				'rd' => 'Y',
				'usr_rd' => ($_SESSION['user_data']['id_emp'] ? $_SESSION['user_data']['id_emp'] : $_SESSION['user_data']['usname']),
				'tgl_rd' => date('Y-m-d'),
			]);
		} else {
			Fr_disposisi::where('ids', $dispmaster['ids'])
			->update([
				'usr_rd' => ($_SESSION['user_data']['id_emp'] ? $_SESSION['user_data']['id_emp'] : $_SESSION['user_data']['usname']),
				'tgl_rd' => date('Y-m-d'),
			]);
		}

		$treedisp = '<tr>
						<td>
							<span class="fa fa-book"></span> <span>'.$dispmaster['no_form'].' ['.date('d-M-Y', strtotime($dispmaster['tglmaster'])).'] </span> <br>
							<span class="text-muted">Kode: '.$dispmaster['kode_disposisi'].'</span> | <span class="text-muted"> Nomor: '.$dispmaster['no_surat'].'</span><br>

						</td>
					</tr>';

		$treedisp .= $this->display_disposisi($dispmaster['no_form'], $dispmaster['idmaster']);

		if (isset($_SESSION['user_data']['id_emp'])) {
			$kd_unit = $_SESSION['user_data']['idunit'];
		} else {
			$kd_unit = "01";
		}

		$stafs = DB::select( DB::raw("
					SELECT id_emp,a.uname+'::'+convert(varchar,a.tgl)+'::'+a.ip,createdate,nip_emp,nrk_emp,nm_emp,nrk_emp+'-'+nm_emp as c2,gelar_dpn,gelar_blk,jnkel_emp,tempat_lahir,tgl_lahir,CONVERT(VARCHAR(10), tgl_lahir, 103) AS [DD/MM/YYYY],idagama,alamat_emp,tlp_emp,email_emp,status_emp,ked_emp,status_nikah,gol_darah,nm_bank,cb_bank,an_bank,nr_bank,no_taspen,npwp,no_askes,no_jamsos,tgl_join,CONVERT(VARCHAR(10), tgl_join, 103) AS [DD/MM/YYYY],tgl_end,reason,a.idgroup,pass_emp,foto,ttd,a.telegram_id,a.lastlogin,tbgol.tmt_gol,CONVERT(VARCHAR(10), tbgol.tmt_gol, 103) AS [DD/MM/YYYY],tbgol.tmt_sk_gol,CONVERT(VARCHAR(10), tbgol.tmt_sk_gol, 103) AS [DD/MM/YYYY],tbgol.no_sk_gol,tbgol.idgol,tbgol.jns_kp,tbgol.mk_thn,tbgol.mk_bln,tbgol.gambar,tbgol.nm_pangkat,tbjab.tmt_jab,CONVERT(VARCHAR(10), tbjab.tmt_jab, 103) AS [DD/MM/YYYY],tbjab.idskpd,tbjab.idunit,tbjab.idjab, tbunit.child, tbjab.idlok,tbjab.tmt_sk_jab,CONVERT(VARCHAR(10), tbjab.tmt_sk_jab, 103) AS [DD/MM/YYYY],tbjab.no_sk_jab,tbjab.jns_jab,tbjab.idjab,tbjab.eselon,tbjab.gambar,tbdik.iddik,tbdik.prog_sek,tbdik.no_sek,tbdik.th_sek,tbdik.nm_sek,tbdik.gelar_dpn_sek,tbdik.gelar_blk_sek,tbdik.ijz_cpns,tbdik.gambar,tbdik.nm_dik,b.nm_skpd,c.nm_unit,c.notes,d.nm_lok FROM bpaddtfake.dbo.emp_data as a
						CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM bpaddtfake.dbo.emp_gol,bpaddtfake.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
						CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
						CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpaddtfake.dbo.emp_dik,bpaddtfake.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
						CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
						,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
						and tbunit.sao like '$kd_unit%' and ked_emp = 'aktif' and LEN(tbunit.kd_unit) = 10 order by nm_emp
						--and tbunit.sao like '01%' and ked_emp = 'aktif' and LEN(tbunit.kd_unit) = 10 order by nm_emp
						") );
		$stafs = json_decode(json_encode($stafs), true);

	// 	QUERY STAFS LEBIH PENDEK
	
	// 	select id_emp, nm_emp
	//   from emp_data a
	//   join emp_jab tbjab on tbjab.ids = (SELECT TOP 1 ids FROM bpaddtfake.dbo.emp_jab WHERE emp_jab.noid = a.id_emp and emp_jab.sts='1' ORDER BY tmt_jab DESC)
	//   join glo_org_unitkerja tbunit on tbunit.kd_unit = (SELECT TOP 1 idunit FROM bpaddtfake.dbo.glo_org_unitkerja where tbunit.kd_unit = tbjab.idunit)
	//   --(SELECT TOP 1 idunit FROM bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab,
	//   --(SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit,
	//   --(SELECT sts, idunit, noid FROM bpaddtfake.dbo.emp_jab WHERE emp_jab.sts='1') tbjab,
	//   --(SELECT * FROM bpaddtfake.dbo.glo_org_unitkerja) tbunit
	//   --where tbunit.sao like '01010501%'
	//   where a.ked_emp = 'AKTIF'
	//   and a.sts = 1
	//   and a.id_emp = tbjab.noid
	//   and tbjab.sts = 1
	//   and tbunit.sao like '$kd_unit%'
	//   order by nm_emp

		if (strlen($_SESSION['user_data']['idunit']) == 10 ) {
			$jabatans = 0;
			$stafs = 0;
		} else {
			// $jabatans = DB::select( DB::raw("SELECT [sts]
			// 									  ,[uname]
			// 									  ,[tgl]
			// 									  ,[ip]
			// 									  ,[logbuat]
			// 									  ,[kd_skpd]
			// 									  ,[jns_jab]
			// 									  ,[jabatan]
			// 									  ,[disposisi]
			// 								  FROM [bpaddtfake].[dbo].[glo_org_jabatan]
			// 								  where disposisi = 'Y'
			// 								  order by jabatan asc") );
			// $jabatans = json_decode(json_encode($jabatans), true);

			$jabatans = DB::select( DB::raw("SELECT [sts]
											      ,[uname]
											      ,[tgl]
											      ,[ip]
											      ,[logbuat]
											      ,[kd_skpd]
											      ,[kd_unit]
											      , CASE WHEN LOWER(SUBSTRING(nm_unit, 0, 3)) != 'ka' AND LOWER(SUBSTRING(nm_unit, 0, 3)) != 'ke'
														THEN 'KEPALA ' + nm_unit
													ELSE nm_unit
													END as nm_unit 
											      ,[cp_unit]
											      ,[notes]
											      ,[child]
											      ,[sao]
											      ,[tgl_unit]
											  FROM [bpaddtfake].[dbo].[glo_org_unitkerja]
											  WHERE LEN(kd_unit) < 10
											  and len(kd_unit) >= LEN($kd_unit)
											  and kd_unit <> '$kd_unit'
											  ORDER BY LEN(kd_unit), kd_unit asc") );
			$jabatans = json_decode(json_encode($jabatans), true);

			if (Auth::user()->id_emp && strlen($_SESSION['user_data']['idunit']) < 8) {
				$stafs = 0;
			}
		}

		$penanganans = Glo_disposisi_penanganan::
						orderBy('urut')
						->get();

		if($request->tipe) {
			$tipe = $request->tipe;
		} else {
			$tipe = "inbox";
		}
		

		return view('pages.bpaddisposisi.disposisilihat')
				->with('signdate', $request->signdate)
				->with('dispmaster', $dispmaster)
				->with('treedisp', $treedisp)
				->with('stafs', $stafs)
				->with('tujuan', $tujuan)
				->with('kddispos', $kddispos)
				->with('unitkerjas', $unitkerjas)
				->with('penanganans', $penanganans)
				->with('jabatans', $jabatans)
				->with('tipe', $tipe);
	}

	public function formlihatdisposisi(Request $request)
	{
		// $this->checksession(); //$this->checkSessionTime();

		if(count($_SESSION) == 0) {
			return redirect('home');
		}

		if (isset($request->jabatans) && isset($request->stafs)) {
			return redirect('/disposisi/lihat disposisi?ids='.$request->ids)
					->with('message', 'Tidak boleh memilih jabatan & staf bersamaan')
					->with('signdate', $request->signdate)
					->with('msg_num', 2);
		}

		if (isset($request->btnDraft)) {
			$rd = 'D';
		} else {
			$rd = 'S';
			if (is_null($request->jabatans) && is_null($request->stafs)) {
				$selesai = 'Y';
				$child = 0;
			} else {
				$selesai = '';
				$child = 1;
			}
		}

		$splitmaxform = explode(".", $request->no_form);

		$filedispo = $request->nm_file_master;

		$diryear = date('Y',strtotime($request->tgl_masuk));
		if (isset($request->nm_file)) {
			$file = $request->nm_file;
			if (count($file) == 1) {
				
				if ($file[0]->getSize() > 52222222) {
					return redirect('/disposisi/lihat disposisi?ids='.$request->ids)
							->with('message', 'Ukuran file terlalu besar')
							->with('signdate', $request->signdate)
							->with('msg_num', 2);    
				} 

				if ($filedispo != '') {
					$filedispo .= '::';
				}

				$filenow = 'disp';
				$filenow .= (int) date('HIs');
				$filenow .= ($splitmaxform[3]);
				$filenow .= ".". $file[0]->getClientOriginalExtension();

				$tujuan_upload = config('app.savefiledisposisi');
				$tujuan_upload .= "\\" . $diryear;
				$tujuan_upload .= "\\" . $request->no_form;

				$filedispo .= $filenow;

				$file[0]->move($tujuan_upload, $filenow);
			} else {
				if ($filedispo != '') {
					$filedispo .= '::';
				}

				foreach ($file as $key => $data) {

					if ($data->getSize() > 52222222) {
						return redirect('/disposisi/lihat disposisi?ids='.$request->ids)
								->with('message', 'Ukuran file terlalu besar')
								->with('signdate', $request->signdate)
								->with('msg_num', 2);      
					} 

					$filenow = 'disp';
					$filenow .= (int) date('HIs') + $key;
					$filenow .= ($splitmaxform[3]);
					$filenow .= ".". $data->getClientOriginalExtension();

					$tujuan_upload = config('app.savefiledisposisi');
					$tujuan_upload .= "\\" . $diryear;
					$tujuan_upload .= "\\" . $request->no_form;
					$data->move($tujuan_upload, $filenow);

					// if ($key != count($file) - 1) {
					// 	$filedispo .= $filenow . "::";
					// } else {
					// 	$filedispo .= $filenow;
					// }
				
					if ($key != 0) {
						$filedispo .= "::";
					} 
					$filedispo .= $filenow;

				}
			}
			Fr_disposisi::where('ids', $request->idmaster)
			->update([
				'nm_file' => $filedispo,
			]);	
		}

		$kepada = '';
		if (isset($request->jabatans)) {
			for ($i=0; $i < count($request->jabatans); $i++) { 
				$kepada .= $request->jabatans[$i];
				if ($i != (count($request->jabatans) - 1)) {
					$kepada .= "::";
				}
			}
		}

		$noid = '';
		if (isset($request->stafs)) {
			if (count($request->stafs) == 1) {
				$noid = $request->stafs[0];
			}
		}

		if (isset($request->btnDraft)) {
			Fr_disposisi::where('ids', $request->ids)
				->update([
				'usr_input' => (Auth::user()->id_emp ? Auth::user()->id_emp : $request->from_pm_new),
				'tgl_input' => date('Y-m-d'),
				'kepada' => $kepada,
				'penanganan' => (isset($request->penanganan) ? $request->penanganan : '' ),
				'catatan' => (isset($request->catatan) ? $request->catatan : '' ),
				'rd' => 'D',
			]);

			$splitsigndate = explode("::", $request->signdate);
			$yearnow = $splitsigndate[0];
			$signnow = $splitsigndate[1];
			$monthnow = $splitsigndate[2];
			return redirect('/disposisi/disposisi?yearnow='.$yearnow.'&signnow='.$signnow.'&monthnow='.$monthnow)
					->with('message', 'Disposisi berhasil diubah')
					->with('msg_num', 1);
		}

		if (isset($request->btnKirim)) {
			Fr_disposisi::where('ids', $request->ids)
				->update([
				'usr_input' => (Auth::user()->id_emp ? Auth::user()->id_emp : $request->from_pm_new),
				'tgl_input' => date('Y-m-d'),
				'kepada' => $kepada,
				'noid' => $noid,
				'penanganan' => (isset($request->penanganan) ? $request->penanganan : '' ),
				'catatan' => (isset($request->catatan) ? $request->catatan : '' ),
				'rd' => 'S',
				'selesai' => $selesai,
				'child' => $child,
			]);

			$arrjabatan = [];
			$arrstaf = [];

			// if (isset($request->jabatans)) {
			// 	$uniqjabatans = array_unique($request->jabatans);
			// 	for ($i=0; $i < count($uniqjabatans); $i++) { 

			// 		$findidjabatan = DB::select( DB::raw("
			// 				SELECT id_emp,a.uname+'::'+convert(varchar,a.tgl)+'::'+a.ip,createdate,nip_emp,nrk_emp,nm_emp,nrk_emp+'-'+nm_emp as c2,gelar_dpn,gelar_blk,jnkel_emp,tempat_lahir,tgl_lahir,CONVERT(VARCHAR(10), tgl_lahir, 103) AS [DD/MM/YYYY],idagama,alamat_emp,tlp_emp,email_emp,status_emp,ked_emp,status_nikah,gol_darah,nm_bank,cb_bank,an_bank,nr_bank,no_taspen,npwp,no_askes,no_jamsos,tgl_join,CONVERT(VARCHAR(10), tgl_join, 103) AS [DD/MM/YYYY],tgl_end,reason,a.idgroup,pass_emp,foto,ttd,a.telegram_id,a.lastlogin,tbgol.tmt_gol,CONVERT(VARCHAR(10), tbgol.tmt_gol, 103) AS [DD/MM/YYYY],tbgol.tmt_sk_gol,CONVERT(VARCHAR(10), tbgol.tmt_sk_gol, 103) AS [DD/MM/YYYY],tbgol.no_sk_gol,tbgol.idgol,tbgol.jns_kp,tbgol.mk_thn,tbgol.mk_bln,tbgol.gambar,tbgol.nm_pangkat,tbjab.tmt_jab,CONVERT(VARCHAR(10), tbjab.tmt_jab, 103) AS [DD/MM/YYYY],tbjab.idskpd,tbjab.idunit,tbjab.idjab, tbunit.child, tbjab.idlok,tbjab.tmt_sk_jab,CONVERT(VARCHAR(10), tbjab.tmt_sk_jab, 103) AS [DD/MM/YYYY],tbjab.no_sk_jab,tbjab.jns_jab,tbjab.idjab,tbjab.eselon,tbjab.gambar,tbdik.iddik,tbdik.prog_sek,tbdik.no_sek,tbdik.th_sek,tbdik.nm_sek,tbdik.gelar_dpn_sek,tbdik.gelar_blk_sek,tbdik.ijz_cpns,tbdik.gambar,tbdik.nm_dik,b.nm_skpd,c.nm_unit,c.notes,d.nm_lok FROM bpaddtfake.dbo.emp_data as a
			// 					CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM  bpaddtfake.dbo.emp_gol,bpaddtfake.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
			// 					CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
			// 					CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpaddtfake.dbo.emp_dik,bpaddtfake.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
			// 					CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
			// 					,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
			// 					and tbjab.idjab like '".$uniqjabatans[$i]."' and ked_emp = 'aktif'") );
			// 		$findidjabatan = json_decode(json_encode($findidjabatan), true);

			// 		if (isset($findidjabatan[0])) {
			// 			$insertjabatan = [
			// 				'sts' => 1,
			// 				'uname'     => (Auth::user()->id_emp ? Auth::user()->id_emp : Auth::user()->usname),
			// 				'tgl'       => date('Y-m-d H:i:s'),
			// 				'ip'        => '',
			// 				'logbuat'   => '',
			// 				'kd_skpd'	=> '1.20.512',
			// 				'kd_unit'	=> $request->kd_unit,
			// 				'no_form' => $request->no_form,
			// 				'kd_surat' => null,
			// 				'status_surat' => null,
			// 				'idtop' => $request->ids,
			// 				'tgl_masuk' => (isset($request->tgl_masuk) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_masuk))) : date('Y-m-d')),
			// 				'usr_input' => '',
			// 				'tgl_input' => null,
			// 				'no_index' => '',
			// 				'kode_disposisi' => '',
			// 				'perihal' => '',
			// 				'tgl_surat' => null,
			// 				'no_surat' => '',
			// 				'asal_surat' => '',
			// 				'kepada_surat' => '',
			// 				'sifat1_surat' => '',
			// 				'sifat2_surat' => '',
			// 				'ket_lain' => '',
			// 				'nm_file' => '',
			// 				'kepada' => '',
			// 				'noid' => '',
			// 				'penanganan' => '',
			// 				'catatan' => '',
			// 				'from_user' => 'E',
			// 				'from_pm' => (Auth::user()->id_emp ? Auth::user()->id_emp : $request->from_pm_new),
			// 				'to_user' => 'E',
			// 				'to_pm' => $findidjabatan[0]['id_emp'],
			// 				'rd' => 'N',
			// 				'usr_rd' => null,
			// 				'tgl_rd' => null,
			// 				'selesai' => '',
			// 				'child' => 0,
			// 			];
			// 			Fr_disposisi::insert($insertjabatan);
			// 		}
			// 	}
			// }

			if (isset($request->jabatans)) {
				$uniqjabatans = array_unique($request->jabatans);
				for ($i=0; $i < count($uniqjabatans); $i++) { 

					$findidjabatan = DB::select( DB::raw("
							SELECT id_emp,a.uname+'::'+convert(varchar,a.tgl)+'::'+a.ip,createdate,nip_emp,nrk_emp,nm_emp,nrk_emp+'-'+nm_emp as c2,gelar_dpn,gelar_blk,jnkel_emp,tempat_lahir,tgl_lahir,CONVERT(VARCHAR(10), tgl_lahir, 103) AS [DD/MM/YYYY],idagama,alamat_emp,tlp_emp,email_emp,status_emp,ked_emp,status_nikah,gol_darah,nm_bank,cb_bank,an_bank,nr_bank,no_taspen,npwp,no_askes,no_jamsos,tgl_join,CONVERT(VARCHAR(10), tgl_join, 103) AS [DD/MM/YYYY],tgl_end,reason,a.idgroup,pass_emp,foto,ttd,a.telegram_id,a.lastlogin,tbgol.tmt_gol,CONVERT(VARCHAR(10), tbgol.tmt_gol, 103) AS [DD/MM/YYYY],tbgol.tmt_sk_gol,CONVERT(VARCHAR(10), tbgol.tmt_sk_gol, 103) AS [DD/MM/YYYY],tbgol.no_sk_gol,tbgol.idgol,tbgol.jns_kp,tbgol.mk_thn,tbgol.mk_bln,tbgol.gambar,tbgol.nm_pangkat,tbjab.tmt_jab,CONVERT(VARCHAR(10), tbjab.tmt_jab, 103) AS [DD/MM/YYYY],tbjab.idskpd,tbjab.idunit,tbjab.idjab, tbunit.child, tbjab.idlok,tbjab.tmt_sk_jab,CONVERT(VARCHAR(10), tbjab.tmt_sk_jab, 103) AS [DD/MM/YYYY],tbjab.no_sk_jab,tbjab.jns_jab,tbjab.idjab,tbjab.eselon,tbjab.gambar,tbdik.iddik,tbdik.prog_sek,tbdik.no_sek,tbdik.th_sek,tbdik.nm_sek,tbdik.gelar_dpn_sek,tbdik.gelar_blk_sek,tbdik.ijz_cpns,tbdik.gambar,tbdik.nm_dik,b.nm_skpd,c.nm_unit,c.notes,d.nm_lok FROM bpaddtfake.dbo.emp_data as a
								CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM  bpaddtfake.dbo.emp_gol,bpaddtfake.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
								CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
								CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpaddtfake.dbo.emp_dik,bpaddtfake.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
								CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
								,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
								and tbunit.kd_unit like '".$uniqjabatans[$i]."' and ked_emp = 'aktif'") );
					$findidjabatan = json_decode(json_encode($findidjabatan), true);

					if (isset($findidjabatan[0])) {
						$insertjabatan = [
							'sts' => 1,
							'uname'     => (Auth::user()->id_emp ? Auth::user()->id_emp : Auth::user()->usname),
							'tgl'       => date('Y-m-d H:i:s'),
							'ip'        => '',
							'logbuat'   => '',
							'kd_skpd'	=> '1.20.512',
							'kd_unit'	=> $request->kd_unit,
							'no_form' => $request->no_form,
							'kd_surat' => null,
							'status_surat' => null,
							'idtop' => $request->ids,
							'tgl_masuk' => (isset($request->tgl_masuk) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_masuk))) : date('Y-m-d')),
							'usr_input' => '',
							'tgl_input' => null,
							'no_index' => '',
							'kode_disposisi' => '',
							'perihal' => '',
							'tgl_surat' => null,
							'no_surat' => '',
							'asal_surat' => '',
							'kepada_surat' => '',
							'sifat1_surat' => '',
							'sifat2_surat' => '',
							'ket_lain' => '',
							'nm_file' => '',
							'kepada' => '',
							'noid' => '',
							'penanganan' => '',
							'catatan' => '',
							'from_user' => 'E',
							'from_pm' => (Auth::user()->id_emp ? Auth::user()->id_emp : $request->from_pm_new),
							'to_user' => 'E',
							'to_pm' => $findidjabatan[0]['id_emp'],
							'rd' => 'N',
							'usr_rd' => null,
							'tgl_rd' => null,
							'selesai' => '',
							'child' => 0,
						];
						Fr_disposisi::insert($insertjabatan);

						$getlastinsertid = Fr_disposisi::
											where('sts', 1)
											->where('no_form', $request->no_form)
											->orderBy('ids', 'desc')
											->first();

						// NOTIFIKASI BROADCAST kalau ada DISPOSISI BARU 
						$url = "http://10.15.38.80/mobileaset/notif/send"; //release
						// $url = "http://10.15.38.82/mobileasetstaging/notif/send"; //staging
						
						$client = new Client();
						$res = $client->request('GET', $url, [
							'headers' => [
								'Content-Type' => 'application/x-www-form-urlencoded',
							],
							'form_params' => [
								"id_emp" => $findidjabatan[0]['id_emp'],
								"title" => "Disposisi",
								"message" => "Anda baru saja mendapatkan disposisi baru!! Segera cek aplikasi anda sekarang.",
								"data" => [
									"type" => "disposisi",
									"ids" => $getlastinsertid['ids'],
								],
							],
						]);

						sleep(2);
					}
				}
			}

			if (isset($request->stafs)) {
				$uniqstafs = array_unique($request->stafs);
				for ($i=0; $i < count($uniqstafs); $i++) { 

					$findidstaf = DB::select( DB::raw("
							SELECT id_emp,a.uname+'::'+convert(varchar,a.tgl)+'::'+a.ip,createdate,nip_emp,nrk_emp,nm_emp,nrk_emp+'-'+nm_emp as c2,gelar_dpn,gelar_blk,jnkel_emp,tempat_lahir,tgl_lahir,CONVERT(VARCHAR(10), tgl_lahir, 103) AS [DD/MM/YYYY],idagama,alamat_emp,tlp_emp,email_emp,status_emp,ked_emp,status_nikah,gol_darah,nm_bank,cb_bank,an_bank,nr_bank,no_taspen,npwp,no_askes,no_jamsos,tgl_join,CONVERT(VARCHAR(10), tgl_join, 103) AS [DD/MM/YYYY],tgl_end,reason,a.idgroup,pass_emp,foto,ttd,a.telegram_id,a.lastlogin,tbgol.tmt_gol,CONVERT(VARCHAR(10), tbgol.tmt_gol, 103) AS [DD/MM/YYYY],tbgol.tmt_sk_gol,CONVERT(VARCHAR(10), tbgol.tmt_sk_gol, 103) AS [DD/MM/YYYY],tbgol.no_sk_gol,tbgol.idgol,tbgol.jns_kp,tbgol.mk_thn,tbgol.mk_bln,tbgol.gambar,tbgol.nm_pangkat,tbjab.tmt_jab,CONVERT(VARCHAR(10), tbjab.tmt_jab, 103) AS [DD/MM/YYYY],tbjab.idskpd,tbjab.idunit,tbjab.idjab, tbunit.child, tbjab.idlok,tbjab.tmt_sk_jab,CONVERT(VARCHAR(10), tbjab.tmt_sk_jab, 103) AS [DD/MM/YYYY],tbjab.no_sk_jab,tbjab.jns_jab,tbjab.idjab,tbjab.eselon,tbjab.gambar,tbdik.iddik,tbdik.prog_sek,tbdik.no_sek,tbdik.th_sek,tbdik.nm_sek,tbdik.gelar_dpn_sek,tbdik.gelar_blk_sek,tbdik.ijz_cpns,tbdik.gambar,tbdik.nm_dik,b.nm_skpd,c.nm_unit,c.notes,d.nm_lok FROM bpaddtfake.dbo.emp_data as a
								CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM  bpaddtfake.dbo.emp_gol,bpaddtfake.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
								CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
								CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpaddtfake.dbo.emp_dik,bpaddtfake.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
								CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
								,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
								and id_emp like '".$uniqstafs[$i]."' and ked_emp = 'aktif'") );
					$findidstaf = json_decode(json_encode($findidstaf), true);

					if (isset($findidstaf[0])) {
						$insertstaf = [
							'sts' => 1,
							'uname'     => (Auth::user()->id_emp ? Auth::user()->id_emp : Auth::user()->usname),
							'tgl'       => date('Y-m-d H:i:s'),
							'ip'        => '',
							'logbuat'   => '',
							'kd_skpd'	=> '1.20.512',
							'kd_unit'	=> $request->kd_unit,
							'no_form' => $request->no_form,
							'kd_surat' => null,
							'status_surat' => null,
							'idtop' => $request->ids,
							'tgl_masuk' => (isset($request->tgl_masuk) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_masuk))) : date('Y-m-d')),
							'usr_input' => '',
							'tgl_input' => null,
							'no_index' => '',
							'kode_disposisi' => '',
							'perihal' => '',
							'tgl_surat' => null,
							'no_surat' => '',
							'asal_surat' => '',
							'kepada_surat' => '',
							'sifat1_surat' => '',
							'sifat2_surat' => '',
							'ket_lain' => '',
							'nm_file' => '',
							'kepada' => '',
							'noid' => '',
							'penanganan' => '',
							'catatan' => '',
							'from_user' => 'E',
							'from_pm' => (Auth::user()->id_emp ? Auth::user()->id_emp : $request->from_pm_new),
							'to_user' => 'E',
							'to_pm' => $findidstaf[0]['id_emp'],
							'rd' => 'N',
							'usr_rd' => null,
							'tgl_rd' => null,
							'selesai' => '',
							'child' => 0,
						];
						Fr_disposisi::insert($insertstaf);

						$getlastinsertid = Fr_disposisi::
											where('sts', 1)
											->where('no_form', $request->no_form)
											->orderBy('ids', 'desc')
											->first();

						// NOTIFIKASI BROADCAST kalau ada DISPOSISI BARU 
						$url = "http://10.15.38.80/mobileaset/notif/send"; //release
						// $url = "http://10.15.38.82/mobileasetstaging/notif/send"; //staging
						
						$client = new Client();
						$res = $client->request('GET', $url, [
							'headers' => [
								'Content-Type' => 'application/x-www-form-urlencoded',
							],
							'form_params' => [
								"id_emp" => $findidstaf[0]['id_emp'],
								"title" => "Disposisi",
								"message" => "Anda baru saja mendapatkan disposisi baru!! Segera cek aplikasi anda",
								"data" => [
									"type" => "disposisi",
									"ids" => $getlastinsertid['ids'],
								],
							],
						]);

						sleep(2);
					}
				}
			}

			$splitsigndate = explode("::", $request->signdate);
			$yearnow = $splitsigndate[0];
			$signnow = $splitsigndate[1];
			$monthnow = $splitsigndate[2];

			DB::statement("
							WITH cte AS (
							SELECT 
							        ids, 
							        idtop,
							        no_form, 
							        from_pm, 
							        to_pm, 
									penanganan,
									catatan,
							        ROW_NUMBER() OVER (
							            PARTITION BY 
							        idtop,
							        no_form, 
							        from_pm, 
							        to_pm,
									penanganan,
									catatan
							            ORDER BY 
							                ids, 
							        no_form, 
							        from_pm, 
							        to_pm
							        ) row_num
							     FROM 
							        bpaddtfake.dbo.fr_disposisi

									where no_form = '$request->no_form'
							)
							DELETE FROM cte
							WHERE row_num > 1;" );

			return redirect('/disposisi/disposisi?yearnow='.$yearnow.'&signnow='.$signnow.'&monthnow='.$monthnow)
					->with('message', 'Disposisi berhasil')
					->with('msg_num', 1);
		}
	}

	public function formdeletedisposisiemp(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}

		Fr_disposisi::where('ids', $request->ids)
		->update([
			'sts' => 0,
		]);

		$idtop = $request->idtop;

		$countchilddisp = Fr_disposisi::
							where('idtop', $idtop)
							->where('sts', 1)
							->count();

		if ($countchilddisp == 0) {
			Fr_disposisi::where('ids', $idtop)
			->update([
				'rd' 		=> 'N',
				'selesai'   => '',
				'child'		=> 0,
			]);
		}

		return 0;
	}

	// ---------/EMPLOYEE----------- //

	public function printexcel(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->mergeCells('A1:J1');
		$sheet->setCellValue('A1', 'STATUS DISPOSISI BPAD');
		$sheet->getStyle('A1')->getFont()->setBold( true );
		$sheet->getStyle('A1')->getAlignment()->setHorizontal('left');

		$styleArray = [
		    'font' => [
		        'size' => 16,
		        'name' => 'Trebuchet MS',
		    ]
		];
		$sheet->getStyle('A1:J1')->applyFromArray($styleArray);

		$sheet->setCellValue('A2', date('d/m/Y H:i', strtotime('+7 hours')));

		$sheet->setCellValue('A3', 'ID');
		$sheet->setCellValue('b3', 'NRK');
		$sheet->setCellValue('c3', 'NAMA');
		$sheet->setCellValue('d3', 'BIDANG');
		$sheet->setCellValue('e3', 'UNIT KERJA');
		$sheet->setCellValue('f3', 'TOTAL SURAT');
		$sheet->setCellValue('g3', 'BELUM DIBACA');
		$sheet->setCellValue('h3', 'HANYA DIBACA');
		$sheet->setCellValue('i3', 'SUDAH DI-TL');
		$sheet->setCellValue('j3', '% TL');

		$sheet->getStyle('A3:J3')->getFont()->setBold( true );
		$sheet->getStyle('A3:J3')->getAlignment()->setHorizontal('center');

		$colorArrayhead = [
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => 'F79646',
				],
			],
		];
		$sheet->getStyle('A3:J3')->applyFromArray($colorArrayhead);

		$nowrow = 4;
		$rowstart = $nowrow - 1;

		$ids = Auth::user()->id_emp;

		if ($ids) {
			$data_self = DB::select( DB::raw("  
								SELECT a.id_emp, a.nrk_emp, a.nip_emp, a.nm_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit, tbunit.notes, tbunit.kd_unit, d.nm_lok, notread.notread, yesread.yesread, lanjut.lanjut from bpaddtfake.dbo.emp_data as a
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
			$data_self = DB::select( DB::raw("  SELECT a.id_emp, a.nrk_emp, a.nip_emp, a.nm_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit, tbunit.notes, tbunit.kd_unit, d.nm_lok, notread.notread, yesread.yesread, lanjut.lanjut from bpaddtfake.dbo.emp_data as a
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

		$total = $data_self['notread'] + $data_self['yesread'] + $data_self['lanjut'];

		$sheet->setCellValue('A'.$nowrow, $data_self['id_emp']);
		$sheet->setCellValue('B'.$nowrow, $data_self['nrk_emp']);
		$sheet->getStyle('B'.$nowrow)->getAlignment()->setHorizontal('right');
		$sheet->setCellValue('c'.$nowrow, $data_self['nm_emp']);
		$sheet->setCellValue('d'.$nowrow, $data_self['nm_unit']);
		$sheet->setCellValue('e'.$nowrow, $data_self['notes']);
		$sheet->setCellValue('f'.$nowrow, $total);
		$sheet->setCellValue('g'.$nowrow, $data_self['notread']);
		$sheet->setCellValue('h'.$nowrow, $data_self['yesread']);
		$sheet->setCellValue('i'.$nowrow, $data_self['lanjut']);
		$sheet->setCellValue('j'.$nowrow, $total != 0 ? number_format((float)($data_self['lanjut']/$total*100), 2, '.', '') . '%' : 0 );

		$sheet->getStyle('f'.$nowrow.':j'.$nowrow)->getNumberFormat()->setFormatCode('#,##0');

		$colorArrayV1 = [
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => 'FDE9D9',
				],
			],
		];
		$sheet->getStyle('a'.$nowrow.':j'.$nowrow)->applyFromArray($colorArrayV1);

		if (strlen($data_self['idunit']) < 10) {
			$sheet->getStyle('a'.$nowrow.':j'.$nowrow)->getFont()->setBold( true );
		}
		$nowrow++;

		$nowunit = $data_self['idunit'];

		$data_stafs = DB::select( DB::raw("  SELECT a.id_emp, a.nrk_emp, a.nip_emp, a.nm_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit, tbunit.kd_unit, tbunit.notes, d.nm_lok, notread.notread, yesread.yesread, lanjut.lanjut from bpaddtfake.dbo.emp_data as a
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

		if ($data_stafs) {
            $bidangnow = '';
			foreach ($data_stafs as $key => $staf) {
                if(strlen($staf['kd_unit']) == 6) {
                    $bidangnow = $staf['nm_unit'];
                } elseif (strlen($staf['kd_unit']) == 2) {
                    $bidangnow = "BADAN PENGELOLAAN ASET DAERAH";
                }

				$total = $staf['notread'] + $staf['yesread'] + $staf['lanjut'];

				$sheet->setCellValue('A'.$nowrow, $staf['id_emp']);
				$sheet->setCellValue('B'.$nowrow, $staf['nrk_emp']);
				$sheet->getStyle('B'.$nowrow)->getAlignment()->setHorizontal('right');
				$sheet->setCellValue('c'.$nowrow, $staf['nm_emp']);
				$sheet->setCellValue('d'.$nowrow, strtoupper($bidangnow));
				$sheet->setCellValue('e'.$nowrow, strtoupper($staf['notes']));
				$sheet->setCellValue('f'.$nowrow, $total);
				$sheet->setCellValue('g'.$nowrow, $staf['notread']);
				$sheet->setCellValue('h'.$nowrow, $staf['yesread']);
				$sheet->setCellValue('i'.$nowrow, $staf['lanjut']);
				$sheet->setCellValue('j'.$nowrow, $total != 0 ? number_format((float)($staf['lanjut']/$total*100), 2, '.', '') . '%' : 0  );
				$sheet->getStyle('f'.$nowrow.':j'.$nowrow)->getNumberFormat()->setFormatCode('#,##0');

				if ($key%2 == 1) {
					$sheet->getStyle('a'.$nowrow.':j'.$nowrow)->applyFromArray($colorArrayV1);
				}
				
				if (strlen($staf['idunit']) < 10) {
					$sheet->getStyle('a'.$nowrow.':j'.$nowrow)->getFont()->setBold( true );
				}
				$nowrow++;
			}
		}

		$sheet->setShowGridlines(false);

		foreach(range('A','J') as $columnID) {
		    $sheet->getColumnDimension($columnID)
		        ->setAutoSize(true);
		}

		$filename = date('dmy').'_STATDISP.xlsx';

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

    public function printexceleselon3(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->mergeCells('A1:J1');
		$sheet->setCellValue('A1', 'STATUS DISPOSISI BPAD');
		$sheet->getStyle('A1')->getFont()->setBold( true );
		$sheet->getStyle('A1')->getAlignment()->setHorizontal('left');

		$styleArray = [
		    'font' => [
		        'size' => 16,
		        'name' => 'Trebuchet MS',
		    ]
		];
		$sheet->getStyle('A1:J1')->applyFromArray($styleArray);

		$sheet->setCellValue('A2', date('d/m/Y H:i', strtotime('+7 hours')));

		$sheet->setCellValue('A3', 'ID');
		$sheet->setCellValue('b3', 'NRK');
		$sheet->setCellValue('c3', 'NAMA');
		$sheet->setCellValue('d3', 'BIDANG');
		$sheet->setCellValue('e3', 'UNIT KERJA');
		$sheet->setCellValue('f3', 'TOTAL SURAT');
		$sheet->setCellValue('g3', 'BELUM DIBACA');
		$sheet->setCellValue('h3', 'HANYA DIBACA');
		$sheet->setCellValue('i3', 'SUDAH DI-TL');
		$sheet->setCellValue('j3', '% TL');

		$sheet->getStyle('A3:J3')->getFont()->setBold( true );
		$sheet->getStyle('A3:J3')->getAlignment()->setHorizontal('center');

		$colorArrayhead = [
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => 'F79646',
				],
			],
		];
		$sheet->getStyle('A3:J3')->applyFromArray($colorArrayhead);

		$nowrow = 4;
		$rowstart = $nowrow - 1;

		// $ids = Auth::user()->id_emp;

		// if ($ids) {
		// 	$data_self = DB::select( DB::raw("  
		// 						SELECT a.id_emp, a.nrk_emp, a.nip_emp, a.nm_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit, tbunit.notes, d.nm_lok, notread.notread, yesread.yesread, lanjut.lanjut from bpaddtfake.dbo.emp_data as a
		// 						CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
		// 						CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
		// 						CROSS APPLY (
		// 							select  count(disp.rd) as 'notread' from bpaddtfake.dbo.fr_disposisi disp
		// 							  where rd = 'N' and sts = 1
		// 							  and disp.to_pm = a.id_emp) notread
		// 						CROSS APPLY (
		// 							select  count(disp.rd) as 'yesread' from bpaddtfake.dbo.fr_disposisi disp
		// 							  where rd = 'Y' and sts = 1
		// 							  and disp.to_pm = a.id_emp) yesread
		// 						CROSS APPLY (
		// 							select  count(disp.rd) as 'lanjut' from bpaddtfake.dbo.fr_disposisi disp
		// 							  where rd = 'S' and sts = 1
		// 							  and disp.to_pm = a.id_emp) lanjut
		// 						,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
		// 						and id_emp like '$ids'
		// 						") )[0];
		// 	$data_self = json_decode(json_encode($data_self), true);
		// } else {
		// 	$data_self = DB::select( DB::raw("  SELECT a.id_emp, a.nrk_emp, a.nip_emp, a.nm_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit, tbunit.notes, d.nm_lok, notread.notread, yesread.yesread, lanjut.lanjut from bpaddtfake.dbo.emp_data as a
		// 						CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
		// 						CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
		// 						CROSS APPLY (
		// 							select  count(disp.rd) as 'notread' from bpaddtfake.dbo.fr_disposisi disp
		// 							  where rd = 'N' and sts = 1
		// 							  and disp.to_pm = a.id_emp) notread
		// 						CROSS APPLY (
		// 							select  count(disp.rd) as 'yesread' from bpaddtfake.dbo.fr_disposisi disp
		// 							  where rd = 'Y' and sts = 1
		// 							  and disp.to_pm = a.id_emp) yesread
		// 						CROSS APPLY (
		// 							select  count(disp.rd) as 'lanjut' from bpaddtfake.dbo.fr_disposisi disp
		// 							  where rd = 'S' and sts = 1
		// 							  and disp.to_pm = a.id_emp) lanjut
		// 						,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
		// 						and idunit like '01' and ked_emp = 'aktif'
		// 						") )[0];
		// 	$data_self = json_decode(json_encode($data_self), true);
		// }		

		// $total = $data_self['notread'] + $data_self['yesread'] + $data_self['lanjut'];

		// $sheet->setCellValue('A'.$nowrow, $data_self['id_emp']);
		// $sheet->setCellValue('B'.$nowrow, $data_self['nrk_emp']);
		// $sheet->getStyle('B'.$nowrow)->getAlignment()->setHorizontal('right');
		// $sheet->setCellValue('c'.$nowrow, $data_self['nm_emp']);
		// $sheet->setCellValue('d'.$nowrow, $data_self['idjab']);
		// $sheet->setCellValue('e'.$nowrow, $data_self['nm_unit']);
		// $sheet->setCellValue('f'.$nowrow, $total);
		// $sheet->setCellValue('g'.$nowrow, $data_self['notread']);
		// $sheet->setCellValue('h'.$nowrow, $data_self['yesread']);
		// $sheet->setCellValue('i'.$nowrow, $data_self['lanjut']);
		// $sheet->setCellValue('j'.$nowrow, $total != 0 ? number_format((float)($data_self['lanjut']/$total*100), 2, '.', '') . '%' : 0 );

		// $sheet->getStyle('f'.$nowrow.':j'.$nowrow)->getNumberFormat()->setFormatCode('#,##0');

		// $colorArrayV1 = [
		// 	'fill' => [
		// 		'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
		// 		'startColor' => [
		// 			'rgb' => 'FDE9D9',
		// 		],
		// 	],
		// ];
		// $sheet->getStyle('a'.$nowrow.':j'.$nowrow)->applyFromArray($colorArrayV1);

		// if (strlen($data_self['idunit']) < 10) {
		// 	$sheet->getStyle('a'.$nowrow.':j'.$nowrow)->getFont()->setBold( true );
		// }
		// $nowrow++;

		// $nowunit = $data_self['idunit'];

		$data_stafs = DB::select( DB::raw("  SELECT a.id_emp, a.nrk_emp, a.nip_emp, a.nm_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.kd_unit, tbunit.nm_unit, tbunit.notes, d.nm_lok, notread.notread, yesread.yesread, lanjut.lanjut from bpaddtfake.dbo.emp_data as a
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

		if ($data_stafs) {
            $bidangnow = '';
			foreach ($data_stafs as $key => $staf) {

                if(strlen($staf['kd_unit']) == 6) {
                    $bidangnow = $staf['nm_unit'];
                } elseif (strlen($staf['kd_unit']) == 2) {
                    $bidangnow = "BADAN PENGELOLAAN ASET DAERAH";
                }

				$total = $staf['notread'] + $staf['yesread'] + $staf['lanjut'];

				$sheet->setCellValue('A'.$nowrow, $staf['id_emp']);
				$sheet->setCellValue('B'.$nowrow, $staf['nrk_emp']);
				$sheet->getStyle('B'.$nowrow)->getAlignment()->setHorizontal('right');
				$sheet->setCellValue('c'.$nowrow, $staf['nm_emp']);
				$sheet->setCellValue('d'.$nowrow, strtoupper($bidangnow));
				$sheet->setCellValue('e'.$nowrow, strtoupper($staf['notes']));
				$sheet->setCellValue('f'.$nowrow, $total);
				$sheet->setCellValue('g'.$nowrow, $staf['notread']);
				$sheet->setCellValue('h'.$nowrow, $staf['yesread']);
				$sheet->setCellValue('i'.$nowrow, $staf['lanjut']);
				$sheet->setCellValue('j'.$nowrow, $total != 0 ? number_format((float)($staf['lanjut']/$total*100), 2, '.', '') . '%' : 0  );
				$sheet->getStyle('f'.$nowrow.':j'.$nowrow)->getNumberFormat()->setFormatCode('#,##0');

				if ($key%2 == 1) {
					$sheet->getStyle('a'.$nowrow.':j'.$nowrow)->applyFromArray($colorArrayV1);
				}
				
				if (strlen($staf['idunit']) < 10) {
					$sheet->getStyle('a'.$nowrow.':j'.$nowrow)->getFont()->setBold( true );
				}
				$nowrow++;
			}
		}

		$sheet->setShowGridlines(false);

		foreach(range('A','J') as $columnID) {
		    $sheet->getColumnDimension($columnID)
		        ->setAutoSize(true);
		}

		$filename = date('dmy').'_STATDISP.xlsx';

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
}
