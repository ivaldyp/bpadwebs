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
use Illuminate\Support\Collection;
use App\Traits\SessionCheckTraits;
use App\Traits\SessionCheckNotif;

use App\Contenttb as Content_tb;
use App\Emp_data;
use App\Emp_notif;
use App\Glo_kategori;
use App\Glo_subkategori;
use App\New_icon_produk;
use App\Sec_access;
use App\Sec_menu;
use App\Sec_logins;
use App\Setup_can_approve;

session_start();

class CmsController extends Controller
{
	use SessionCheckTraits;
	use SessionCheckNotif;

	public function __construct()
	{
		$this->middleware('auth');
	}

	public function checksession() {
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
	}

	// ------------------ MENU ------------------ //

	public function display_roles($query, $idgroup, $access, $parent, $level = 0)
	{

		if ($parent == 0) {
			$sao = "(sao = 0 or sao is null)";
		} else {
			$sao = "(sao = ".$parent.")";
		}

		$query = DB::select( DB::raw("
					SELECT *
					FROM bpaddtfake.dbo.sec_menu
					WHERE $sao
					ORDER BY urut, ids
				"));
		$query = json_decode(json_encode($query), true);

		$result = '';

		if (count($query) > 0) {
			foreach ($query as $menu) {
				// if (strlen($menu['url']) > 50) {
				// 	$url = substr($menu['url'], 0, 47);
				// } else {
				// 	$url = $menu['url'];
				// }
				$padding = ($level * 20) + 8;
				$result .= '<tr style="background-color:">
								<td class="col-md-1">'.$level.'</td>
								<td class="col-md-1">'.$menu['ids'].'</td>
								<td style="padding-left:'.$padding.'px; '.(($level == 0) ? 'font-weight: bold;"' : '').'">'.$menu['desk'].' '.(($menu['child'] == 1)? '<i class="fa fa-arrow-down"></i>' : '').'</td>
								<td>'.($menu['zket'] ? $menu['zket'] : '-').'</td>
								<td>'.($menu['iconnew'] ? $menu['iconnew'] : '-').'</td>
								<td style="word-wrap: normal;">'.($menu['urlnew'] ? (strlen($menu['urlnew']) > 30 ? substr($menu['urlnew'],0,27) . " ..." : $menu['urlnew'] ) : '-').'</td>
								<td class="text-center">'.intval($menu['urut']).'</td>
								<td class="text-center">'.(($menu['child'] == 1)? '<i style="color:green;" class="fa fa-check"></i>' : '<i style="color:red;" class="fa fa-times"></i>').'</td>
								<td class="text-center">'.(($menu['tampilnew'] == 1)? '<i style="color:green;" class="fa fa-check"></i>' : '<i style="color:red;" class="fa fa-times"></i>').'</td>
								
								'.(($access['zadd'] == 'y') ? 
									'<td class="text-center"><button type="button" class="btn btn-success btn-insert" data-toggle="modal" data-target="#modal-insert" data-ids="'.$menu['ids'].'" data-desk="'.$menu['desk'].'"><i class="fa fa-plus"></i></button></td>'
								: '' ).'


								'.(($access['zupd'] == 'y' || $access['zdel'] == 'y') ? 
									'<td class="col-md-2">
										'.(($access['zupd'] == 'y') ? 
											'<button type="button" class="btn btn-info btn-update" data-toggle="modal" data-target="#modal-update" data-ids="'.$menu['ids'].'" data-desk="'.$menu['desk'].'" data-child="'.$menu['child'].'" data-iconnew="'.$menu['iconnew'].'" data-urlnew="'.$menu['urlnew'].'" data-urut="'.$menu['urut'].'" data-tampilnew="'.$menu['tampilnew'].'" data-zket="'.$menu['zket'].'"><i class="fa fa-edit"></i></button>
											<a href="/portal/cms/menuakses?menu='.$menu['ids'].'&nama='.$menu['desk'].'"><button type="button" class="btn btn-warning"><i class="fa fa-key"></i></button></a>
											'
										: '').'
										'.(($access['zdel'] == 'y') ? 
											'<button type="button" class="btn btn-danger btn-delete" data-toggle="modal" data-target="#modal-delete" data-ids="'.$menu['ids'].'" data-sao="'.$menu['sao'].'" data-desk="'.$menu['desk'].'"><i class="fa fa-trash"></i></button>'
										: '').'
									</td>'
								: '' ).'
								
							</tr>';

				if ($menu['child'] == 1) {
					$result .= $this->display_roles($query, $idgroup, $access, $menu['ids'], $level+1);
				}
			}
		}
		return $result;
	}

	public function menuall(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		$all_menu = [];

		$menus = $this->display_roles($all_menu, $request->name, $access, 0);
		
		return view('pages.bpadcms.menu')
				->with('access', $access)
				->with('menus', $menus);
	}

	public function forminsertmenu(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		$maxids = Sec_menu::max('ids');
		$urut = intval(Sec_menu::where('sao', $request->sao)
				->max('urut'));

		if ($request->urut) {
			$urut = $request->urut;
		} else {
			if (is_null($urut)) {
				$urut = 1;
			} else {
				$urut = $urut + 1;
			}
		}
		is_null($request->desk) ? $desk = '' : $desk = $request->desk;
		is_null($request->zket) ? $zket = '' : $zket = $request->zket;
		$request->sao == 0 ? $sao = '' : $sao = $request->sao;  

		$insert = [
				'sts'       => 1,
				'uname'     => Auth::user()->usname,
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'suspend'   => '',
				'urut'      => $urut,
				'desk'      => $desk,
				'validat'   => '',
				'isi'       => '',
				'ipserver'  => '',
				'child'     => 0,
				'sao'       => $sao,
				'tipe'      => '',
				'icon'      => '',
				'zfile'     => '',
				'zket'      => $zket,
				'iconnew'   => $request->iconnew,
				'urlnew'    => $request->urlnew,
				'tampilnew' => $request->tampilnew,
			];

		if (Sec_menu::insert($insert) && $sao > 0) {
			$query = Sec_menu::
						where('ids', $sao)
						->update([
							'child' => 1,
						]);
		}

		$idgroups = Sec_access::
					distinct('idgroup')
					->orderBy('idgroup', 'asc')
					->get('idgroup');

		$result = array();
		$thisid = Sec_menu::max('ids');
		foreach ($idgroups as $key => $group) {
			array_push($result, [
				'sts' => 1,
				'uname' => Auth::user()->usname,
				'tgl' => date('Y-m-d H:i:s'),
				'ip' => '',
				'logbuat' => '',
				'idgroup' => $group['idgroup'],
				'idtop' => $thisid,
			]);
		}
		Sec_access::insert($result);

		return redirect('/cms/menu')
					->with('message', 'Menu '.$request->desk.' berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdatemenu(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		Sec_menu::
			where('ids', $request->ids)
			->update([
				'desk'      => $request->desk,
				'zket'      => $request->zket,
				'urut'      => $request->urut,
				'iconnew'   => $request->iconnew,
				'urlnew'    => $request->urlnew,
				'tampilnew' => $request->tampilnew
			]);

		return redirect('/cms/menu')
					->with('message', 'Menu '.$request->desk.' berhasil diubah')
					->with('msg_num', 1);
	}

	public function deleteLoopAccess($ids)
	{
		$childids = Sec_menu::
					where('sao', $ids)
					->get('ids');

		foreach ($childids as $id) {
			$this->deleteLoopAccess($id['ids']);
			Sec_access::
				where('idtop', $id['ids'])
				->delete();
		}

		return 1;
	}

	public function deleteLoopMenu($ids)
	{
		$childids = Sec_menu::
					where('sao', $ids)
					->get('ids');

		foreach ($childids as $id) {
			$this->deleteLoopMenu($id['ids']);
			Sec_menu::
				where('sao', $id['ids'])
				->delete();
		}

		return 1;
	}

	public function formdeletemenu(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		// hapus menu dari tabel access
		$this->deleteLoopAccess($request->ids);

		$deletechildaccess = Sec_access::
								where('idtop', $request->ids)
								->delete();

		// hapus menu dari tabel menu
		$this->deleteLoopMenu($request->ids);

		$delete = Sec_menu::
					where('ids', $request->ids)
					->delete();
		
		// cek if menu masih punya child
		$cekchild = Sec_menu::
					where('sao', $request->sao)
					->count();

		if ($cekchild == 0) {
			$updatechild = Sec_menu::
							where('ids', $request->sao)
							->update([
								'child' => 0,
							]);
		}

		return redirect('/cms/menu')
					->with('message', 'Menu '.$request->desk.' berhasil dihapus')
					->with('msg_num', 1);
	}

	public function menuakses(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		$idtop = $request->menu;
		$desk = $request->nama;
		$accesses = Sec_access::
					where('idtop', $idtop)
					->orderBy('idgroup')
					->get();

		return view('pages.bpadcms.menuakses')
				->with('now_idtop', $idtop)
				->with('now_desk', $desk)
				->with('accesses', $accesses);
	}

	public function formupdateaccess(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		$idtop = $request->idtop;
		$desk = $request->desk;

		Sec_access::where('idtop', $idtop)
					->update([
						'zviw' => null,
						'zadd' => null,
						'zupd' => null,
						'zdel' => null,
					]);

		if ($request->zviw) {
			foreach ($request->zviw as $zviw) {
				Sec_access::
					where('idtop', $idtop)
					->where('idgroup', $zviw)
					->update([
						'zviw' => 'y',
					]);
			}
		}

		if ($request->zadd) {
			foreach ($request->zadd as $zadd) {
				Sec_access::
					where('idtop', $idtop)
					->where('idgroup', $zadd)
					->update([
						'zadd' => 'y',
					]);
			}
		}

		if ($request->zupd) {
			foreach ($request->zupd as $zupd) {
				Sec_access::
					where('idtop', $idtop)
					->where('idgroup', $zupd)
					->update([
						'zupd' => 'y',
					]);
			}
		}

		if ($request->zdel) {
			foreach ($request->zdel as $zdel) {
				Sec_access::
					where('idtop', $idtop)
					->where('idgroup', $zdel)
					->update([
						'zdel' => 'y',
					]);
			}
		}

		return redirect('/cms/menuakses?menu='.$idtop.'&nama='.$desk)
					->with('message', 'Hak akses '.$desk.' berhasil diubah')
					->with('msg_num', 1);
	}

	// ------------------ MENU ------------------ //

	// ------------------------------------------ //

	// ---------------- KATEGORI ---------------- //

	public function kategoriall(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		$kategoris = Glo_kategori::
						orderBy('ids')
						->get();
		
		return view('pages.bpadcms.kategori')
				->with('access', $access)
				->with('kategoris', $kategoris);
	}

	public function forminsertkategori(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		$result = [
				'sts'       => $request->sts,
				'uname'     => Auth::user()->usname,
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'nmkat'     => $request->nmkat,
				'privacy'   => 'C,',
				'inc_sql'   => '',
				'inc_file'  => '',
				'inc_order' => '',
				'iscontent' => '',
			];

		Glo_kategori::insert($result);

		return redirect('/cms/kategori')
					->with('message', 'kategori '.$request->desk.' berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdatekategori(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		Glo_kategori::
			where('ids', $request->ids)
			->update([
				'nmkat' => $request->nmkat,
				'sts'   => $request->sts,
			]);

		return redirect('/cms/kategori')
					->with('message', 'Kategori '.$request->nmkat.' berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletekategori(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		// hapus menu dari tabel kategori
		$delete = Glo_kategori::
					where('ids', $request->ids)
					->delete();

		return redirect('/cms/kategori')
					->with('message', 'Kategori '.$request->nmkat.' berhasil dihapus')
					->with('msg_num', 1);
	}

	// ------------------ KATEGORI ------------------ //

	// ---------------------------------------------- //

	// ---------------- SUB KATEGORI ---------------- //

	public function subkategoriall(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		$subkats = Glo_subkategori::
					join('bpadcmsfake.dbo.glo_kategori', 'bpadcmsfake.dbo.glo_kategori.ids', '=', 'bpadcmsfake.dbo.glo_subkategori.idkat')
					->get();

		$subkatsid =    Glo_subkategori::
						join('bpadcmsfake.dbo.glo_kategori', 'bpadcmsfake.dbo.glo_kategori.ids', '=', 'bpadcmsfake.dbo.glo_subkategori.idkat')
						->distinct('idkat', 'bpadcmsfake.dbo.glo_kategori.nmkat')
						->get(['idkat', 'nmkat']);
		
		return view('pages.bpadcms.subkategori')
				->with('access', $access)
				->with('subkatsid', $subkatsid)
				->with('subkats', $subkats);
	}

	public function forminsertsubkategori(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		$maxurut = Glo_subkategori::max('urut_subkat');
		$maxurut = $maxurut+1;

		$result = [
				'sts'       => $request->sts,
				'uname'     => Auth::user()->usname,
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'idkat'     => '5',
				'subkat'     => $request->subkat,
				'urut_subkat' => $maxurut,
				'kd_cms'    => '1.20.512',
			];

		Glo_subkategori::insert($result);

		return redirect('/cms/subkategori')
					->with('message', 'subkategori '.$request->subkat.' berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdatesubkategori(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		Glo_subkategori::
			where('ids', $request->ids)
			->update([
				'subkat' => $request->subkat,
				'sts'   => $request->sts,
			]);

		return redirect('/cms/subkategori')
					->with('message', 'Subkategori '.$request->subkat.' berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletesubkategori(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		// hapus menu dari tabel kategori
		$delete = Glo_subkategori::
					where('ids', $request->ids)
					->delete();

		return redirect('/cms/subkategori')
					->with('message', 'Subkategori '.$request->subkat.' berhasil dihapus')
					->with('msg_num', 1);
	}

	// ----------------- SUB KATEGORI ------------------ //

	// ---------------------------------------------- //

	// -------------------- CONTENT ---------------- //

	public function contentall(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();
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

		if (!(is_null($request->katnow))) {
			$katnow = $request->katnow;
		} else {
			$katnow = 1;
		}

		if (is_null($request->suspnow) || $request->suspnow == 'N') {
			$suspnow = '';
		} elseif ($request->suspnow == 'Y') {
			$suspnow = 'Y';
		}

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

		// if (is_null($request->apprnow) || $request->apprnow == 1) {
		//     $apprnow = 'Y';
		// } elseif ($request->apprnow == 0) {
		//     $apprnow = 'N';
		// } 

		$kategoris = DB::select( DB::raw("
					SELECT *, (select count (ids) from bpadcmsfake.dbo.content_tb as con where appr = 'N' and sts = 1 and suspend = '$suspnow' and idkat = kat.ids) as total
					FROM bpadcmsfake.dbo.glo_kategori as kat
					WHERE sts = 1
					ORDER BY nmkat
				"));
		$kategoris = json_decode(json_encode($kategoris), true);

		$katnowdetail = DB::select( DB::raw("
					SELECT *, lower(nmkat) as nama
					FROM bpadcmsfake.dbo.glo_kategori kat
					WHERE ids = $katnow
				"))[0];
		$katnowdetail = json_decode(json_encode($katnowdetail), true);

		$subkats = Glo_subkategori::
					get();

		$contents = DB::select( DB::raw("
					SELECT TOP (1000) con.*, lower(kat.nmkat) as nmkat from bpadcmsfake.dbo.content_tb con
					  join bpadcmsfake.dbo.glo_kategori kat on kat.ids = con.idkat
					  where idkat = $katnow
					  and suspend = '$suspnow'
					  and con.sts = 1
					  and month(con.tanggal) $signnow $monthnow
					  and year(con.tanggal) $signnow $yearnow
					  order by con.appr, con.tgl desc 
				"));
		$contents = json_decode(json_encode($contents), true);

		$approve = Setup_can_approve::first();
		$splitappr = explode("::", $approve['can_approve']);

		if ($_SESSION['user_data']['id_emp']) {
			$thisid = $_SESSION['user_data']['id_emp'];
		} else {
			$thisid = $_SESSION['user_data']['usname'];
		} 

		foreach ($splitappr as $key => $data) {
			if ($thisid == $data) {
				$flagapprove = 1;
				break;
			} else {
				$flagapprove = 0;
			}
		}

		return view('pages.bpadcms.content')
				->with('access', $access)
				->with('kategoris', $kategoris)
				->with('subkats', $subkats)
				->with('contents', $contents)
				->with('katnow', $katnow)
				->with('katnowdetail', $katnowdetail)
				->with('suspnow', $suspnow)
				->with('flagapprove', $flagapprove)
				->with('signnow', $signnow)
				->with('monthnow', $monthnow)
				->with('yearnow', $yearnow);
	}

	public function contentexcel(Request $request)
	{
		$kategoris = DB::select( DB::raw("
					SELECT nmkat
					FROM bpadcmsfake.dbo.glo_kategori
					WHERE sts = 1
					AND ids = '$request->kat'
				"))[0];
		$kategoris = json_decode(json_encode($kategoris), true);

		$splitmon = explode("::", $request->rekap_bln);
		$bln = $splitmon[0];

		$contents = DB::select( DB::raw("
					SELECT TOP (1000) con.*, lower(kat.nmkat) as nmkat, nm_emp, nm_unit, emp.nm_lok from bpadcmsfake.dbo.content_tb con
					join bpadcmsfake.dbo.glo_kategori kat on kat.ids = con.idkat
					join (
						SELECT id_emp, nm_emp, tbunit.nm_unit, d.nm_lok FROM bpaddtfake.dbo.emp_data as a
					CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
					CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
					,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
					 and ked_emp = 'aktif' and tgl_end is null
					) emp on con.usrinput = emp.id_emp 
					where con.idkat = '$request->kat'
					and con.suspend = ''
					and con.sts = 1
					and month(con.tanggal) = '$bln'
					and year(con.tanggal) = '$request->rekap_thn'
					order by con.tanggal desc

					-- SELECT TOP (1000) con.*, lower(kat.nmkat) as nmkat from bpadcmsfake.dbo.content_tb con
					--   join bpadcmsfake.dbo.glo_kategori kat on kat.ids = con.idkat
					--   where idkat = '$request->kat'
					--   and suspend = ''
					--   and con.sts = 1
					--   and month(tanggal) = '$bln'
					--   and year(tanggal) = '$request->rekap_thn'
					--   order by con.tgl desc
				"));
		$contents = json_decode(json_encode($contents), true);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'REKAP KONTEN ' . strtoupper($kategoris['nmkat']));
		$sheet->getStyle('A1')->getFont()->setBold( true );

		$sheet->setCellValue('A2', 'PERIODE ' . strtoupper($splitmon[1]) . ' ' . $request->rekap_thn);
		$sheet->getStyle('A2')->getFont()->setBold( true );

		$styleArray = [
		    'font' => [
		        'size' => 12,
		        'name' => 'Trebuchet MS',
		    ]
		];
		$sheet->getStyle('A1:A4')->applyFromArray($styleArray);

		$sheet->setCellValue('A4', 'NO');
		$sheet->setCellValue('B4', 'KEGIATAN');
		$sheet->setCellValue('C4', 'USER');
		$sheet->setCellValue('D4', 'TANGGAL');
		$sheet->setCellValue('E4', 'LINK');
		$sheet->setCellValue('F4', 'ATTACHMENT');

		$colorArrayhead = [
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => 'F79646',
				],
			],
		];
		$sheet->getStyle('A4:F4')->applyFromArray($colorArrayhead);
		$sheet->getStyle('A4:F4')->getFont()->setBold( true );
		$sheet->getStyle('A4:F4')->getAlignment()->setHorizontal('center');

		$colorArrayV1 = [
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => 'FDE9D9',
				],
			],
		];

		$nowrow = 5;
		$rowstart = $nowrow - 1;
		foreach ($contents as $key => $content) {
			if ($content['idkat'] == 1) {
				$kat = 'berita';
			} else {
				$kat = 'foto';
			}

			if ($key%2 == 0) {
				$sheet->getStyle('A'.$nowrow.':F'.$nowrow)->applyFromArray($colorArrayV1);
			}

			$sheet->setCellValue('A'.$nowrow, $key+1);
			$sheet->setCellValue('B'.$nowrow, $content['judul']);
			$sheet->setCellValue('C'.$nowrow, strtoupper($content['nm_emp']) . ' - [' . $content['nm_unit'] . ', '. strtoupper($content['nm_lok']) .']' );
			$sheet->setCellValue('D'.$nowrow, date('d/M/Y', strtotime(str_replace('/', '-', $content['tanggal']))) );
			$sheet->setCellValue('E'.$nowrow, 'https://' . $request->current_url . '/portal/content/' . $kat . '/' . $content['ids'] );
			$sheet->setCellValue('F'.$nowrow, ($content['tfile'] && $content['tfile'] != '' ) ? 'https://' . $request->current_url . '/portal/public/publicimg/images/cms/1.20.512/' . $content['idkat']. '/file/' . $content['tfile'] : '-' );

			$nowrow++;
		}

		$sheet->getColumnDimension('A')->setWidth(7);
		$sheet->getColumnDimension('B')->setWidth(100);
		foreach(range('C','F') as $columnID) {
		    $sheet->getColumnDimension($columnID)
		        ->setAutoSize(true);
		}

		$rowend = $nowrow - 1;

		$filename = 'REKAP_'.strtoupper($kategoris['nmkat']).'_'.strtoupper($splitmon[1]).'_'.$request->rekap_thn.'.xlsx';

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

	public function contentrekap(Request $request)
	{
		$kategoris = DB::select( DB::raw("
					SELECT nmkat
					FROM bpadcmsfake.dbo.glo_kategori
					WHERE sts = 1
					AND ids = '$request->kat'
				"))[0];
		$kategoris = json_decode(json_encode($kategoris), true);

		$splitmon = explode("::", $request->rekap_bln);
		$bln = $splitmon[0];

		$contents = DB::select( DB::raw("
					SELECT TOP (1000) con.*, lower(kat.nmkat) as nmkat, nm_emp, nm_unit, emp.nm_lok from bpadcmsfake.dbo.content_tb con
					join bpadcmsfake.dbo.glo_kategori kat on kat.ids = con.idkat
					join (
						SELECT id_emp, nm_emp, tbunit.nm_unit, d.nm_lok FROM bpaddtfake.dbo.emp_data as a
					CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
					CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
					,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
					 and ked_emp = 'aktif' and tgl_end is null
					) emp on con.usrinput = emp.id_emp 
					where con.idkat = '$request->kat'
					and con.suspend = ''
					and con.sts = 1
					and month(con.tanggal) = '$bln'
					and year(con.tanggal) = '$request->rekap_thn'
					order by con.tanggal desc
				"));
		$contents = json_decode(json_encode($contents), true);

		$pdf = PDF::setPaper('a4', 'landscape');
		$pdf->loadView('pages.bpadcms.contentpreview', 
						[
							'contents' => $contents,
							'kategoris' => $kategoris,
							'bln' => $splitmon[1],
							'thn' => $request->rekap_thn,
							'url' => $request->current_url,
						]);
		// return $pdf->stream('preview.pdf');
		return $pdf->download('REKAP_'.strtoupper($kategoris['nmkat']).'_'.strtoupper($splitmon[1]).$request->rekap_thn.'.pdf');
	}

	public function contenttambah(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		$subkats = Glo_subkategori::
					where('idkat', $request->kat)
					->get();

		$kat = Glo_kategori::
					where('ids', $request->kat)
					->first();

		return view('pages.bpadcms.contenttambah')
				->with('subkats', $subkats)
				->with('kat', $kat)
				->with('idkat', $request->kat);
	}

	public function contentubah(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		$ids = $request->ids;
		$idkat = $request->idkat;

		$subkats = Glo_subkategori::
					where('idkat', $idkat)
					->get();

		$content = DB::select( DB::raw("
					SELECT con.tanggal as tanggalc, con.*, lower(kat.nmkat) as nmkat from bpadcmsfake.dbo.content_tb con
					  join bpadcmsfake.dbo.glo_kategori kat on kat.ids = con.idkat
					  where con.ids = $ids
					  order by con.tanggal desc
				"))[0];
		$content = json_decode(json_encode($content), true);

		$kat = Glo_kategori::
					where('ids', $idkat)
					->first();

		$approve = Setup_can_approve::first();
		$splitappr = explode("::", $approve['can_approve']);

		if ($_SESSION['user_data']['id_emp']) {
			$thisid = $_SESSION['user_data']['id_emp'];
		} else {
			$thisid = $_SESSION['user_data']['usname'];
		}   

		foreach ($splitappr as $key => $data) {
			if ($thisid == $data) {
				$flagapprove = 1;
				break;
			} else {
				$flagapprove = 0;
			}
		}

		return view('pages.bpadcms.contentubah')
				->with('ids', $ids)
				->with('idkat', $idkat)
				->with('kat', $kat)
				->with('subkats', $subkats)
				->with('content', $content)
				->with('flagapprove', $flagapprove)
				->with('signnow', $request->signnow)
				->with('monthnow', $request->monthnow)
				->with('yearnow', $request->yearnow);
	}

	public function forminsertcontent(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		$kat = Glo_kategori::
					where('ids', $request->idkat)
					->first();

		if (isset($request->tfile)) {
			$file = $request->tfile;

			if ($file->getSize() > 1024000) {
				// return redirect('/cms/content?katnow='.$request->idkat)->with('message', 'Ukuran file terlalu besar (Maksimal 1MB)');
				return redirect()->back()->with('message', 'Ukuran file terlalu besar (Maksimal 1MB)');
			} 
			if (strtolower($file->getClientOriginalExtension()) != "png" && strtolower($file->getClientOriginalExtension()) != "jpg" && strtolower($file->getClientOriginalExtension()) != "jpeg") {
				return redirect('/cms/content?katnow='.$request->idkat)->with('message', 'File yang diunggah harus berbentuk JPG / JPEG / PNG');     
			} 

			$file_name = "cms" . preg_replace("/[^0-9]/", "", $request->tanggal);
			$file_name .= $_SESSION['user_data']['nrk_emp'];
			$file_name .= ".". $file->getClientOriginalExtension();

			if ($request->idkat == 1) {
				$tujuan_upload = config('app.savefileimgberita');
			} elseif ($request->idkat == 5) {
				$tujuan_upload = config('app.savefileimggambar');
			}

			if (strtolower($kat['nmkat']) == 'lelang') {
				$tujuan_upload = config('app.savefileimglelang');
			} elseif (strtolower($kat['nmkat']) == 'infografik') {
				$tujuan_upload = config('app.savefileimginfografik');
			} elseif (strtolower($kat['nmkat']) == 'infografik epem') {
				$tujuan_upload = config('app.savefileimginfografikepem');
			}
		
			$file->move($tujuan_upload, $file_name);

			$image_info = getimagesize($tujuan_upload . '\\' . $file_name);
			$width = $image_info[0];
			$height = $image_info[1];
			$height_new = 256 * $height / $width;

			$this->createThumbnail($file_name, 256, 256, $tujuan_upload);
		}

		if (isset($request->tfiledownload)) {
			$file = $request->tfiledownload;

			if ($file->getSize() > 33000000) {
				return redirect('/cms/content?katnow='.$request->idkat)->with('message', 'Ukuran file terlalu besar (Maksimal 5MB)');     
			} 

			$file_name = $file->getClientOriginalName();

			$tujuan_upload = config('app.savefiledocs');
			$file->move($tujuan_upload, $file_name);
		}
			
		if (!(isset($file_name))) {
			$file_name = '';
		}

		if (!(isset($request->subkat))) {
			$subkat = '';
		} else {
			$subkat = $request->subkat;
		}

		if (!(isset($request->url))) {
			$url = '';
		} else {
			$url = $request->url;
		}

		if (!(isset($request->isi1))) {
			$isi1 = '';
		} else {
			$isi1 = $request->isi1;
		}

		if (!(isset($request->isi2))) {
			$isi2 = '';
		} else {
			$isi2 = $request->isi2;
		}

		if ($request->suspend == 'Y') {
			$suspend = 'Y';
		} else {
			$suspend = '';
		}

		if ($request->headline == 'H,' ) {
			$headline = 'H,';
		} else {
			$headline = '';
		}

		if (strtolower($kat['nmkat']) == 'lelang' && $headline == 'H,') {
			Content_tb::where('idkat', $request->idkat)
			->update([
				'tipe' => '',
			]);
		}

		if ($request->kode_kat == 'VID') {
			$isi2 = $isi2;
		} else {
			$isi2 = htmlentities($isi2);
		}
		
		$insert = [
				'sts'       => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'suspend' => $suspend,
				'idkat'     => $request->idkat,
				'subkat'     => $subkat,
				'tipe'      => $headline,
				'tanggal'   => (is_null($request->tanggal) ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s',strtotime(str_replace('/', '-', $request->tanggal))) ),
				'judul'   => $request->judul,
				'isi1'   => htmlentities($isi1),
				'isi2'   => $isi2,
				'tglinput'  => (is_null($request->tanggal) ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s',strtotime(str_replace('/', '-', $request->tanggal))) ),
				'editor'   => $request->editor,
				'link'      => '',
				'thits'   => 0,
				'ipserver' => '',
				'tfile'   => $file_name,
				'likes'   => 0,
				'privacy'   => '',
				'url'       => $url,
				'kd_cms'   => '1.20.512',
				'appr'   => "N",
				'usrinput'   => $request->usrinput,
				'contentnew'   => $request->contentnew,
			];

		Content_tb::insert($insert);

		return redirect('/cms/content?katnow='.$request->idkat)
					->with('message', 'Konten berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdatecontent(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		$kat = Glo_kategori::
					where('ids', $request->idkat)
					->first();

		if (isset($request->tfile)) {
			$file = $request->tfile;

			if ($file->getSize() > 2222222) {
				return redirect('/cms/content?katnow='.$request->idkat)->with('message', 'Ukuran file terlalu besar (Maksimal 2MB)');     
			} 
			if ($file->getClientOriginalExtension() != "png" && $file->getClientOriginalExtension() != "jpg" && $file->getClientOriginalExtension() != "jpeg") {
				return redirect('/cms/content?katnow='.$request->idkat)->with('message', 'File yang diunggah harus berbentuk JPG / JPEG / PNG');     
			} 

			// $file_name = "cms" . preg_replace("/[^0-9]/", "", $request->tanggal);
			$file_name = "cms" . date('dmyHis');
			$file_name .= $_SESSION['user_data']['nrk_emp'];
			$file_name .= ".". $file->getClientOriginalExtension();

			if ($request->idkat == 1) {
				$tujuan_upload = config('app.savefileimgberita');
			} elseif ($request->idkat == 5) {
				$tujuan_upload = config('app.savefileimggambar');
			}

			if (strtolower($kat['nmkat']) == 'lelang') {
				$tujuan_upload = config('app.savefileimglelang');
			} elseif (strtolower($kat['nmkat']) == 'infografik') {
				$tujuan_upload = config('app.savefileimginfografik');
			}

			$file->move($tujuan_upload, $file_name);

			$image_info = getimagesize($tujuan_upload . '\\' . $file_name);
			$width = $image_info[0];
			$height = $image_info[1];
			$height_new = 256 * $height / $width;

			$this->createThumbnail($file_name, 256, 256, $tujuan_upload);
		}

		if (isset($request->tfiledownload)) {
			$file = $request->tfiledownload;

			if ($file->getSize() > 5555000) {
				return redirect('/cms/content?katnow='.$request->idkat)->with('message', 'Ukuran file terlalu besar (Maksimal 5MB)');     
			} 

			$file_name = $file->getClientOriginalName();

			$tujuan_upload = config('app.savefiledocs');
			$file->move($tujuan_upload, $file_name);
		}
			
		if (!(isset($file_name))) {
			$file_name = '';
		}

		if (!(isset($request->subkat))) {
			$subkat = '';
		} else {
			$subkat = $request->subkat;
		}

		if (!(isset($request->url))) {
			$url = '';
		} else {
			$url = $request->url;
		}

		if (!(isset($request->isi1))) {
			$isi1 = '';
		} else {
			$isi1 = $request->isi1;
		}

		if (!(isset($request->isi2))) {
			$isi2 = '';
		} else {
			$isi2 = $request->isi2;
		}

		if ($request->kode_kat == 'VID') {
			$isi2 = $isi2;
		} else {
			$isi2 = htmlentities($isi2);
		}

		if ($request->headline == 'H,' ) {
			$headline = 'H,';
		} else {
			$headline = '';
		}

		if (strtolower($kat['nmkat']) == 'lelang' && $headline == 'H,') {
			Content_tb::where('idkat', $request->idkat)
			->update([
				'tipe' => '',
			]);
		}

		if (isset($request->btnAppr)) {

			if (Auth::user()->usname) {
				$approved_by = Auth::user()->usname;
			} else {
				$approved_by = Auth::user()->id_emp;
			}

			if (strtolower($kat['nmkat']) == 'lelang' && $request->appr == 'Y') {
				Content_tb::where('idkat', $request->idkat)
				->update([
					'appr' => 'N',
					'approved_by' => $approved_by,
				]);
			}

			Content_tb::where('ids', $request->ids)
			->update([
				'appr' => $request->appr,
				'approved_by' => $approved_by,
			]);
		}


		if ($request->suspend == 'Y') {
			$suspend = 'Y';
			$headline = '';
			
			Content_tb::where('ids', $request->ids)
			->update([
				'appr' => 'N',
			]);

			date_default_timezone_set('Asia/Jakarta');
			if ($request->suspnow == '') {
				$notifsuspend = [
					'sts'       => 1,
					'tgl'       => date('Y-m-d H:i:s'),
					'id_emp'	=> $request->usrinput,
					'jns_notif'	=> 'KONTEN',
					'message1'	=> 'Konten anda dengan judul <b>'.$request->judul.'</b> telah di suspend',
					'message2'	=> $request->suspend_teks,
					'rd'		=> 'N',
				];
				Emp_notif::insert($notifsuspend);
			}

		} else {
			$suspend = '';
		}

		date_default_timezone_set('Asia/Jakarta');
		if ($request->appr == 'Y' && $suspend == '') {
			$notifapprcontent = [
					'sts'       => 1,
					'tgl'       => date('Y-m-d H:i:s'),
					'id_emp'	=> $request->usrinput,
					'jns_notif'	=> 'KONTENAPPR',
					'message1'	=> 'Konten anda dengan judul <b>'.$request->judul.'</b> telah disetujui',
					'message2'	=> '',
					'rd'		=> 'N',
				];
			Emp_notif::insert($notifapprcontent);
		}

		Content_tb::
			where('ids', $request->ids)
			->update([
				'subkat'     => $subkat,
				'tipe'      => $headline,
				'tanggal'   => (is_null($request->tanggal) ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s',strtotime(str_replace('/', '-', $request->tanggal))) ),
				'judul'   => $request->judul,
				'isi1'   => htmlentities($request->isi1),
				'isi2'   => $isi2,
				'url'       => $url,
				'suspend' => $suspend,
				'suspend_teks' => $request->suspend_teks,
			]);

		if ($file_name != '') {
			Content_tb::where('ids', $request->ids)
			->update([
				'tfile' => $file_name,
			]);
		}

		if ($request->suspnow == 'Y') {
			$suspnow = 'Y';
		} elseif ($request->suspnow == '') {
			$suspnow = 'N';
		}

		return redirect('/cms/content?katnow='.$request->idkat.'&suspnow='.$suspnow)
					->with('message', 'Konten berhasil diubah')
					->with('msg_num', 1);
	}

	public function formapprcontent(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		Content_tb::
			where('ids', $request->ids)
			->update([
				'appr' => $request->appr,
			]);

		return redirect('/cms/content?katnow='.$request->idkat)
					->with('message', 'Konten berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletecontent(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		// hapus menu dari tabel kategori
		Content_tb::
					where('ids', $request->ids)
					->update([
						'sts' => 0,
					]);

		return redirect('/cms/content?katnow='.$request->idkat)
					->with('message', 'Konten berhasil dihapus')
					->with('msg_num', 1);
	}

	function createThumbnail($image_name, $new_width, $new_height, $uploadDir)
	{
		$path = $uploadDir . '/' . $image_name;

		$mime = getimagesize($path);

		if($mime['mime']=='image/png') { 
			$src_img = imagecreatefrompng($path);
		}
		if($mime['mime']=='image/jpg' || $mime['mime']=='image/jpeg' || $mime['mime']=='image/pjpeg') {
			$src_img = imagecreatefromjpeg($path);
		}   

		$old_x          =   imageSX($src_img);
		$old_y          =   imageSY($src_img);

		if($old_x > $old_y) 
		{
			$thumb_w    =   $new_width;
			$thumb_h    =   $old_y*($new_height/$old_x);
		}

		if($old_x < $old_y) 
		{
			$thumb_w    =   $old_x*($new_width/$old_y);
			$thumb_h    =   $new_height;
		}

		if($old_x == $old_y) 
		{
			$thumb_w    =   $new_width;
			$thumb_h    =   $new_height;
		}

		$dst_img        =   ImageCreateTrueColor($thumb_w,$thumb_h);

		imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 

		$image_name = str_replace(".","-thumb.",$image_name);

		// New save location
		$new_thumb_loc = $uploadDir . '/' . $image_name;

		if($mime['mime']=='image/png') {
			$result = imagepng($dst_img,$new_thumb_loc,8);
		}
		if($mime['mime']=='image/jpg' || $mime['mime']=='image/jpeg' || $mime['mime']=='image/pjpeg') {
			$result = imagejpeg($dst_img,$new_thumb_loc,80);
		}

		imagedestroy($dst_img); 
		imagedestroy($src_img);

		return $result;
	}

	// ---------------- CONTENT ------------------ //

	// ------------------------------------------- //

	// ---------------- PRODUK ------------------- //

	public function produkall(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		$produks = New_icon_produk::
						where('type', 'static')
						->orderBy('ids')
						->get();
		
		return view('pages.bpadcms.produk')
				->with('access', $access)
				->with('produks', $produks);
	}

	public function forminsertproduk(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		$cekexists = New_icon_produk::where('name', $request->name)->count();
		if ($cekexists > 0) {
			return redirect('/cms/produk')->with('message', 'Produk tersebut sudah ada');
		}

		if (isset($request->imgstatic)) {
			$file = $request->imgstatic;

			if ($file->getSize() > 33000000) {
				return redirect('/cms/produk')->with('message', 'Ukuran file terlalu besar (Maksimal 2MB)');     
			} 
			if ($file->getClientOriginalExtension() != "png" && $file->getClientOriginalExtension() != "jpg" && $file->getClientOriginalExtension() != "jpeg") {
				return redirect('/cms/produk')->with('message', 'File yang diunggah harus berbentuk JPG / JPEG / PNG');     
			} 

			$file_name = "ico-" . $request->name;
			$file_name .= ".". $file->getClientOriginalExtension();
			$tujuan_upload = config('app.savefileimgproduk');
			
			$file->move($tujuan_upload, $file_name);
		}

		if (!(isset($file_name))) {
			$pathhref = '';
		} else {
			$pathhref = "/" . explode("/", $_SERVER['REQUEST_URI'])[1];
			$pathhref .= "/public/img/icon-aset/";
			$pathhref .= $file_name;
		}

		$result1 = [
				'href'      => $request->href,
				'name'      => $request->name,
				'source'    => $pathhref,
				'type'      => 'static',
			];
		New_icon_produk::insert($result1);

		if (isset($request->imgactive)) {
			$file = $request->imgactive;

			if ($file->getSize() > 33000000) {
				return redirect('/cms/produk')->with('message', 'Ukuran file terlalu besar (Maksimal 2MB)');     
			} 
			if ($file->getClientOriginalExtension() != "gif") {
				return redirect('/cms/produk')->with('message', 'File yang diunggah harus berbentuk GIF');     
			} 

			$file_name = "ico-" . $request->name;
			$file_name .= ".". $file->getClientOriginalExtension();
			$tujuan_upload = config('app.savefileimgproduk');
			
			$file->move($tujuan_upload, $file_name);
		}

		if (!(isset($file_name))) {
			$pathhref = '';
		} else {
			$pathhref = "/" . explode("/", $_SERVER['REQUEST_URI'])[1];
			$pathhref .= "/public/img/icon-aset/";
			$pathhref .= $file_name;
		}

		$result2 = [
				'href'      => $request->href,
				'name'      => $request->name,
				'source'    => $pathhref,
				'type'      => 'active',
			];
		New_icon_produk::insert($result2);

		return redirect('/cms/produk')
					->with('message', 'produk '.$request->name.' berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdateproduk(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		if (isset($request->imgstatic)) {
			$file = $request->imgstatic;

			if ($file->getSize() > 33000000) {
				return redirect('/cms/produk')->with('message', 'Ukuran file terlalu besar (Maksimal 2MB)');     
			} 
			if ($file->getClientOriginalExtension() != "png" && $file->getClientOriginalExtension() != "jpg" && $file->getClientOriginalExtension() != "jpeg") {
				return redirect('/cms/produk')->with('message', 'File yang diunggah harus berbentuk JPG / JPEG / PNG');     
			} 

			$file_name = "ico-" . $request->name;
			$file_name .= ".". $file->getClientOriginalExtension();
			$tujuan_upload = config('app.savefileimgproduk');
			
			$file->move($tujuan_upload, $file_name);
		}

		if (!(isset($file_name))) {
			$pathhref = '';
		} else {
			$pathhref = "/" . explode("/", $_SERVER['REQUEST_URI'])[1];
			$pathhref .= "/public/img/icon-aset/";
			$pathhref .= $file_name;

			New_icon_produk::
				where('name', $request->name)
				->where('type', 'static')
				->update([
					'source' => $pathhref,
				]);
		}

		/////////////

		if (isset($request->imgactive)) {
			$file = $request->imgactive;

			if ($file->getSize() > 33000000) {
				return redirect('/cms/produk')->with('message', 'Ukuran file terlalu besar (Maksimal 2MB)');     
			} 
			if ($file->getClientOriginalExtension() != "gif") {
				return redirect('/cms/produk')->with('message', 'File yang diunggah harus berbentuk GIF');     
			} 

			$file_name2 = "ico-" . $request->name;
			$file_name2 .= ".". $file->getClientOriginalExtension();
			$tujuan_upload = config('app.savefileimgproduk');
			
			$file->move($tujuan_upload, $file_name2);
		}

		if (!(isset($file_name2))) {
			$pathhref = '';
		} else {
			$pathhref = "/" . explode("/", $_SERVER['REQUEST_URI'])[1];
			$pathhref .= "/public/img/icon-aset/";
			$pathhref .= $file_name2;

			New_icon_produk::
				where('name', $request->name)
				->where('type', 'active')
				->update([
					'source' => $pathhref,
				]);
		}

		//////////

		New_icon_produk::
			where('name', $request->name)
			->update([
				'href'      => $request->href,
				'name'      => $request->name,
			]);

		return redirect('/cms/produk')
					->with('message', 'Produk '.$request->name.' berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeleteproduk(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		// hapus produk

		$delete = New_icon_produk::
					where('name', $request->name)
					->delete();

		return redirect('/cms/produk')
					->with('message', 'Produk '.$request->name.' berhasil dihapus')
					->with('msg_num', 1);
	}

	// ---------------- PRODUK ------------------- //

	// ---------------- APPROVE ------------------- //

	public function approve (Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		$approveds = Setup_can_approve::first();

		$splitapprove = explode("::", $approveds['can_approve']);
		$peopleappr = '';
		foreach ($splitapprove as $key => $split) {
			
			if (substr($split, 0, 8) == '1.20.512') {
				$query = Emp_data::where('id_emp', $split)->first(['nm_emp']);
				$peopleappr .= $query['nm_emp'];
			} else {
				$peopleappr .= $split;
			}
			$peopleappr .= '::';
		}

		$pegawai1 = Emp_data::where('ked_emp', 'AKTIF')->orderBy('nm_emp')->get();

		$pegawai2 = Sec_logins::orderBy('idgroup')->orderBy('usname')->get();

		return view('pages.bpadcms.approve')
				->with('approveds', $peopleappr)
				->with('pegawai1', $pegawai1)
				->with('pegawai2', $pegawai2);
	}

	public function formsaveapprove (Request $request)
	{
		$approve = '';

		if ($request->approve) {
			foreach ($request->approve as $key => $data) {
				$approve .= $data . "::";
			}
		}

		Setup_can_approve::where('can_approve', 'like', '%%')->delete();

		$query = [
				'updated_at'    => date('Y-m-d H:i:s'),
				'can_approve'   => $approve,
			];
		Setup_can_approve::insert($query);

		return redirect('/cms/approve')
					->with('message', 'Pegawai approval berhasil diubah')
					->with('msg_num', 1);
	}

	// ---------------- APPROVE ------------------- //

}

	