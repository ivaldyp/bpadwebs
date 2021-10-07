<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Traits\SessionCheckTraits;

use App\Book_ruang;
use App\Book_transact;
use App\Glo_org_lokasi;
use App\GLo_org_unitkerja;
use App\Sec_menu;

session_start();

class BookingController extends Controller
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

	public function manageruang(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		$lokasis = Glo_org_lokasi::
						orderBy('kd_lok')
						->get();

		$units = GLo_org_unitkerja::
						whereRaw('LEN(kd_unit) = 6')
						->orderBy('kd_unit')
						->get();

		$ruangs = Book_ruang::
					where('sts', 1)
					->orderBy('kd_unit')
					->orderBy('nm_ruang')
					->get();
		
		return view('pages.bpadbooking.manageruang')
				->with('access', $access)
				->with('lokasis', $lokasis)
				->with('ruangs', $ruangs)
				->with('units', $units);
	}

	public function forminsertruang(Request $request)
	{

		$splitunit = explode("::", $request->unit);
		$kd_unit = $splitunit[0];
		$unit = $splitunit[1];

		$splitlokasi = explode("::", $request->lokasi);
		$kd_lokasi = $splitlokasi[0];
		$lokasi = $splitlokasi[1];

		$insertruang = [
			'sts' => 1,
			'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
			'tgl'       => date('Y-m-d H:i:s'),
			'nm_ruang' => ($request->nm_ruang ? $request->nm_ruang : ''),
			'kd_lokasi' => $kd_lokasi,
			'lokasi' => $lokasi,
			'kd_unit' => $kd_unit,
			'unit' => $unit,
			'lantai' => ($request->lantai ? $request->lantai : ''),
			'jumlah' => ($request->jumlah ? $request->jumlah : ''),
		];

		Book_ruang::insert($insertruang);

		return redirect('/booking/manageruang')
				->with('message', 'Ruang berhasil dibuat')
				->with('msg_num', 1);

	}

	public function formupdateruang(Request $request)
	{

		$splitunit = explode("::", $request->unit);
		$kd_unit = $splitunit[0];
		$unit = $splitunit[1];

		$splitlokasi = explode("::", $request->lokasi);
		$kd_lokasi = $splitlokasi[0];
		$lokasi = $splitlokasi[1];

		Book_ruang::where('ids', $request->ids)
			->update([
				'nm_ruang' => ($request->nm_ruang ? $request->nm_ruang : ''),
				'kd_lokasi' => $kd_lokasi,
				'lokasi' => $lokasi,
				'kd_unit' => $kd_unit,
				'unit' => $unit,
				'lantai' => ($request->lantai ? $request->lantai : ''),
				'jumlah' => ($request->jumlah ? $request->jumlah : ''),
			]);

		return redirect('/booking/manageruang')
				->with('message', 'Ruang berhasil diubah')
				->with('msg_num', 1);

	}

	public function formdeleteruang(Request $request)
	{

		Book_ruang::where('ids', $request->ids)
			->update([
				'sts' => 0,
			]);

		return redirect('/booking/manageruang')
				->with('message', 'Ruang berhasil dihapus')
				->with('msg_num', 1);
	}

	public function lihatpinjam(Request $request)
	{
		$booking = Book_transact::
					where('ids', $request->ids)
					->first();

		$ruangs = Book_ruang::
					where('sts', 1)
					->where('ids', $booking['ruang'])
					->first();

		$units = GLo_org_unitkerja::
						whereRaw('LEN(kd_unit) = 6')
						->orderBy('kd_unit')
						->get();

		return view('pages.bpadbooking.lihat')
				->with('booking', $booking)
				->with('ruangs', $ruangs)
				->with('units', $units);
	}

	public function formpinjam(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		$ruangs = Book_ruang::
					where('sts', 1)
					->orderBy('kd_unit')
					->orderBy('nm_ruang')
					->get();

		$units = GLo_org_unitkerja::
						whereRaw('LEN(kd_unit) = 6')
						->orderBy('kd_unit')
						->get();
		
		return view('pages.bpadbooking.formpinjam')
				->with('access', $access)
				->with('ruangs', $ruangs)
				->with('units', $units);
	}

	public function forminsertpinjam(Request $request)
	{
		$tgl_pinjam = date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_pinjam)));
		$jam_mulai = $request->time1;
		$jam_selesai = $request->time2;

		// $findbooking = DB::select( DB::raw("
		// 					SELECT ids
		// 					FROM bpaddtfake.dbo.book_transact
		// 					WHERE ruang = '$request->ruang'
		// 					and tgl_pinjam = '$tgl_pinjam'
		// 					and (jam_mulai <= '$jam_mulai' 
		// 					and jam_selesai > '$jam_mulai')
		// 				") );
		// $findbooking = json_decode(json_encode($findbooking), true);

		// if (count($findbooking) > 0) {
		// 	return redirect('/booking/pinjam')
		// 			->with('message', 'Jadwal yang dipilih telah terisi')
		// 			->with('msg_num', 2);
		// } 

		$filebook = '';

		$splitunit = explode("::", $request->unit);
		$kd_unit = $splitunit[0];
		$unit = $splitunit[1];

		if (strpos($_SESSION['user_data']['idunit'], $kd_unit) !== false) {
			$status = 'S';
			$appr_usr = Auth::user()->id_emp;
			$appr_time = date('Y-m-d H:i:s');
		} else {
			$status = 'Y';
			$appr_usr = '';
			$appr_time = null;
		}

		if (isset($request->nm_file)) {
			$file = $request->nm_file;

			if ($file->getSize() > 5500000) {
				return redirect('/booking/pinjam')->with('message', 'Ukuran file terlalu besar (Maksimal 5MB)');     
			} 

			$filebook .= $file->getClientOriginalName();

			$tujuan_upload = config('app.savefilebooking');
			$tujuan_upload .= "\\" . $request->ruang . explode(":", $jam_mulai)[0] . date('dmY',strtotime($tgl_pinjam)); 
			$file->move($tujuan_upload, $filebook);
		}
			
		if (!(isset($request->nm_file))) {
			$filebook = '';
		}

		$insertpinjam = [
			'sts' => 1,
			'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
			'tgl'       => date('Y-m-d H:i:s'),
			'nm_emp'	=> $request->nm_emp,
			'id_emp'	=> $request->id_emp,
			'unit_emp' => $kd_unit,
			'nmunit_emp' => $unit,
			'tujuan'	=> ($request->tujuan ? $request->tujuan : ''),
			'peserta'	=> ($request->peserta ? $request->peserta : ''),
			'ruang'		=> $request->ruang,
			'tgl_pinjam' => date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_pinjam))),
			'jam_mulai'	=> $request->time1,
			'jam_selesai' => $request->time2,
			'nm_file'	=> $filebook,
			'status'	=> $status,
			'appr_time' => $appr_time,
			'appr_usr' 	=> $appr_usr,
		];

		Book_transact::insert($insertpinjam);

		return redirect('/booking/list')
				->with('message', 'Pinjaman baru berhasil dibuat')
				->with('msg_num', 1);
	}

	public function ubahpinjam(Request $request)
	{
		$booking = Book_transact::
					where('ids', $request->ids)
					->first();

		$ruangs = Book_ruang::
					where('sts', 1)
					->get();

		$units = GLo_org_unitkerja::
						whereRaw('LEN(kd_unit) = 6')
						->orderBy('kd_unit')
						->get();

		return view('pages.bpadbooking.formubah')
				->with('booking', $booking)
				->with('ruangs', $ruangs)
				->with('units', $units);
	}

	public function formupdatepinjam(Request $request)
	{
		
	}

	public function formdeletepinjam(Request $request)
	{
		Book_transact::where('ids', $request->ids)
			->update([
				'status' => 'N',
			]);

		return redirect('/booking/'.$request->hal)
				->with('message', 'Pinjaman tersebut berhasil dihapus')
				->with('msg_num', 1);
	}

	public function listpinjam(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

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
			$signnow = "<=";
		}

		if ($request->searchnow) {
			$qsearchnow = "and tujuan = '".$request->searchnow."'";
		} else {
			$qsearchnow = "";
		}

		if (Auth::user()->usname) {
			$qunit = "";
		} else {
			if (strlen($_SESSION['user_data']['idunit']) > 6) {
				$splitunit = substr($_SESSION['user_data']['idunit'], 0, 6);
				$qunit = "and unit_emp like '".$splitunit."'";
			} elseif (strlen($_SESSION['user_data']['idunit']) < 6) {
				$qunit = "and unit_emp like '%".$_SESSION['user_data']['idunit']."%'";
			} else {
				$qunit = "and unit_emp like '".$_SESSION['user_data']['idunit']."'";
			}
		}

		$bookingyes = DB::select( DB::raw("
							SELECT TOP (1000) tr.[ids]
							      ,tr.[sts]
							      ,tr.[uname]
							      ,tr.[tgl]
							      ,[nm_emp]
							      ,[id_emp]
							      ,[unit_emp]
							      ,[nmunit_emp]
							      ,[tujuan]
							      ,[peserta]
							      ,[ruang]
							      ,ruang.nm_ruang
							      ,[tgl_pinjam]
							      ,[jam_mulai]
							      ,[jam_selesai]
							      ,[nm_file]
							      ,[status]
							      ,[alasan_tolak]
							      ,[appr_usr]
							      ,[appr_time]
						  	FROM [bpaddtfake].[dbo].[book_transact] tr
						  	join bpaddtfake.dbo.book_ruang ruang on ruang.ids = tr.ruang
						  	where status = 'S'
						  		$qsearchnow
						  		$qunit
						  		and month(tgl_pinjam) $signnow $monthnow
								and year(tgl_pinjam) = $yearnow
								and tr.sts = 1
							order by tgl_pinjam desc, jam_mulai desc, jam_selesai desc
						") );

		$bookingno = DB::select( DB::raw("
							SELECT TOP (1000) tr.[ids]
							      ,tr.[sts]
							      ,tr.[uname]
							      ,tr.[tgl]
							      ,[nm_emp]
							      ,[id_emp]
							      ,[unit_emp]
							      ,[nmunit_emp]
							      ,[tujuan]
							      ,[peserta]
							      ,[ruang]
							      ,ruang.nm_ruang
							      ,[tgl_pinjam]
							      ,[jam_mulai]
							      ,[jam_selesai]
							      ,[nm_file]
							      ,[status]
							      ,[alasan_tolak]
							      ,[appr_usr]
							      ,[appr_time]
						  	FROM [bpaddtfake].[dbo].[book_transact] tr
						  	join bpaddtfake.dbo.book_ruang ruang on ruang.ids = tr.ruang
						  	where status = 'N'
						  		$qsearchnow
						  		$qunit
						  		and month(tgl_pinjam) $signnow $monthnow
								and year(tgl_pinjam) = $yearnow
								and tr.sts = 1
							order by tgl_pinjam desc, jam_mulai desc, jam_selesai desc
						") );
		$bookingwait = DB::select( DB::raw("
							SELECT TOP (1000) tr.[ids]
							      ,tr.[sts]
							      ,tr.[uname]
							      ,tr.[tgl]
							      ,[nm_emp]
							      ,[id_emp]
							      ,[unit_emp]
							      ,[nmunit_emp]
							      ,[tujuan]
							      ,[peserta]
							      ,[ruang]
							      ,ruang.nm_ruang
							      ,[tgl_pinjam]
							      ,[jam_mulai]
							      ,[jam_selesai]
							      ,[nm_file]
							      ,[status]
							      ,[alasan_tolak]
							      ,[appr_usr]
							      ,[appr_time]
						  	FROM [bpaddtfake].[dbo].[book_transact] tr
						  	join bpaddtfake.dbo.book_ruang ruang on ruang.ids = tr.ruang
						  	where status = 'Y'
						  		$qsearchnow
						  		$qunit
						  		and month(tgl_pinjam) $signnow $monthnow
								and year(tgl_pinjam) = $yearnow
								and tr.sts = 1
							order by tgl_pinjam desc, jam_mulai desc, jam_selesai desc
						") );


		$bookingyes = json_decode(json_encode($bookingyes), true);
		$bookingwait = json_decode(json_encode($bookingwait), true);
		$bookingno = json_decode(json_encode($bookingno), true);

		return view('pages.bpadbooking.listpinjam')
				->with('access', $access)
				->with('bookingwait', $bookingwait)
				->with('bookingyes', $bookingyes)
				->with('bookingno', $bookingno)
				->with('signnow', $signnow)
				->with('searchnow', $request->searchnow)
				->with('monthnow', $monthnow)
				->with('yearnow', $yearnow);
	}

	public function requestpinjam(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

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
			$signnow = "<=";
		}

		if ($request->searchnow) {
			$qsearchnow = "and tujuan = '".$request->searchnow."'";
		} else {
			$qsearchnow = "";
		}

		if (Auth::user()->usname) {
			$qunit = "";
		} else {
			if (strlen($_SESSION['user_data']['idunit']) > 6) {
				$splitunit = substr($_SESSION['user_data']['idunit'], 0, 6);
				$qunit = "and kd_unit like '".$splitunit."'";
			} elseif (strlen($_SESSION['user_data']['idunit']) < 6) {
				$qunit = "and kd_unit like '%".$_SESSION['user_data']['idunit']."%'";
			} else {
				$qunit = "and kd_unit like '".$_SESSION['user_data']['idunit']."'";
			}
		}

		$bookingyes = DB::select( DB::raw("
							SELECT TOP (1000) tr.[ids]
							      ,tr.[sts]
							      ,tr.[uname]
							      ,tr.[tgl]
							      ,[nm_emp]
							      ,[id_emp]
							      ,[unit_emp]
							      ,[nmunit_emp]
							      ,[tujuan]
							      ,[peserta]
							      ,[ruang]
							      ,ruang.nm_ruang
							      ,ruang.kd_unit
							      ,[tgl_pinjam]
							      ,[jam_mulai]
							      ,[jam_selesai]
							      ,[nm_file]
							      ,[status]
							      ,[alasan_tolak]
							      ,[appr_usr]
							      ,[appr_time]
						  	FROM [bpaddtfake].[dbo].[book_transact] tr
						  	join bpaddtfake.dbo.book_ruang ruang on ruang.ids = tr.ruang
						  	where status = 'S'
						  		$qsearchnow
						  		$qunit
						  		and month(tgl_pinjam) $signnow $monthnow
								and year(tgl_pinjam) = $yearnow
								and tr.sts = 1
							order by tgl_pinjam desc, jam_mulai desc, jam_selesai desc
						") );

		$bookingno = DB::select( DB::raw("
							SELECT TOP (1000) tr.[ids]
							      ,tr.[sts]
							      ,tr.[uname]
							      ,tr.[tgl]
							      ,[nm_emp]
							      ,[id_emp]
							      ,[unit_emp]
							      ,[nmunit_emp]
							      ,[tujuan]
							      ,[peserta]
							      ,[ruang]
							      ,ruang.nm_ruang
							      ,ruang.kd_unit
							      ,[tgl_pinjam]
							      ,[jam_mulai]
							      ,[jam_selesai]
							      ,[nm_file]
							      ,[status]
							      ,[alasan_tolak]
							      ,[appr_usr]
							      ,[appr_time]
						  	FROM [bpaddtfake].[dbo].[book_transact] tr
						  	join bpaddtfake.dbo.book_ruang ruang on ruang.ids = tr.ruang
						  	where status = 'N'
						  		$qsearchnow
						  		$qunit
						  		and month(tgl_pinjam) $signnow $monthnow
								and year(tgl_pinjam) = $yearnow
								and tr.sts = 1
							order by tgl_pinjam desc, jam_mulai desc, jam_selesai desc
						") );
		$bookingwait = DB::select( DB::raw("
							SELECT TOP (1000) tr.[ids]
							      ,tr.[sts]
							      ,tr.[uname]
							      ,tr.[tgl]
							      ,[nm_emp]
							      ,[id_emp]
							      ,[unit_emp]
							      ,[nmunit_emp]
							      ,[tujuan]
							      ,[peserta]
							      ,[ruang]
							      ,ruang.nm_ruang
							      ,ruang.kd_unit
							      ,[tgl_pinjam]
							      ,[jam_mulai]
							      ,[jam_selesai]
							      ,[nm_file]
							      ,[status]
							      ,[alasan_tolak]
							      ,[appr_usr]
							      ,[appr_time]
						  	FROM [bpaddtfake].[dbo].[book_transact] tr
						  	join bpaddtfake.dbo.book_ruang ruang on ruang.ids = tr.ruang
						  	where status = 'Y'
						  		$qsearchnow
						  		$qunit
						  		and month(tgl_pinjam) $signnow $monthnow
								and year(tgl_pinjam) = $yearnow
								and tr.sts = 1
							order by tgl_pinjam desc, jam_mulai desc, jam_selesai desc
						") );

		$bookingyes = json_decode(json_encode($bookingyes), true);
		$bookingwait = json_decode(json_encode($bookingwait), true);
		$bookingno = json_decode(json_encode($bookingno), true);

		return view('pages.bpadbooking.requestpinjam')
				->with('access', $access)
				->with('bookingwait', $bookingwait)
				->with('bookingyes', $bookingyes)
				->with('bookingno', $bookingno)
				->with('signnow', $signnow)
				->with('searchnow', $request->searchnow)
				->with('monthnow', $monthnow)
				->with('yearnow', $yearnow);
	}
}
