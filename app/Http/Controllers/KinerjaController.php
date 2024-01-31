<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Traits\SessionCheckTraits;
use App\Traits\SessionCheckNotif;

use App\Kinerja_data;
use App\Kinerja_detail;
use App\Sec_menu;
use App\V_kinerja;

session_start();

class KinerjaController extends Controller
{
    use SessionCheckTraits;
	use SessionCheckNotif;

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

    public function entrikinerja(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
        
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

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

        if (Auth::user()->id_emp) {
            $now_id_emp = Auth::user()->id_emp;
            
            $laporans = V_kinerja::
                        where('idemp', $now_id_emp)
                        ->whereYear('tgl_trans', $now_year)
                        ->whereMonth('tgl_trans', $now_month)
                        ->orderBy('tgl_trans')
                        ->orderBy('time1')
                        ->orderBy('time2')
                        ->get();
        } else {
            $now_id_emp = 0;
            $laporans = V_kinerja::
                        where('idemp', 0)
                        ->get();
        }


		return view('pages.bpadkinerja.kinerjaentri')
                ->with('now_month', $now_month)
				->with('now_year', $now_year)
				->with('now_id_emp', $now_id_emp)
				->with('laporans', $laporans)
				->with('access', $access);
	}

    public function forminsertaktivitas(Request $request)
	{
        if(count($_SESSION) == 0) {
			return redirect('home');
		}

        if($request->tipe_hadir== 1) {
            $flagback = 0;
            if ($request->time1 == '00:00' && $request->time2 == '00:00' && $request->uraian == '') {
                $flagback = 1; $msg = "Mohon isi detail kegiatan";
            } else if($request->time1 == '00:00' && $request->time2 == '00:00') { 
                $flagback = 1; $msg = "Mohon isi waktu kegiatan";
            } else if($request->time1 == '00:00') { 
                $flagback = 1; $msg = "Mohon isi waktu mulai kegiatan"; 
            } else if($request->time2 == '00:00') { 
                $flagback = 1; $msg = "Mohon isi waktu berakhir kegiatan";
            } else if($request->uraian == '') { 
                $flagback = 1; $msg = "Mohon isi uraian kegiatan";
            }

            if ($flagback == 1) {
                return redirect()->back()
                ->with('message', $msg)
                ->with('msg_num', 2)
                ->withInput();
            }
        } 

        $id_emp = $request->id_emp;
        $tgl_trans = date("Y-m-d", strtotime(str_replace('/', '-', $request->tgl_trans)));
        $kinerja_detail_id = date('dmYHis') . "_" . random_int(100000, 999999);

        $cekaktivitas = Kinerja_data::
                        where('idemp', $id_emp)
                        ->where('tgl_trans', $tgl_trans)
                        ->get();

        if (count($cekaktivitas) == 0) {
            $insertkinerja = [
                'sts'               => 1,
                'uname'             => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
                'tgl'               => date('Y-m-d H:i:s'),
                'ip'                => '',
                'logbuat'           => '',
                'idemp'             => $id_emp,
                'tgl_trans'         => $tgl_trans,
                'tipe_hadir'        => $request->tipe_hadir,
                'jns_hadir'         => $request->jns_hadir,
                'lainnya'           => ($request->lainnya ? $request->lainnya : ''),
                'stat'              => null,
                'tipe_hadir_app'    => null,
                'jns_hadir_app'     => null,
                'catatan_app'       => null,
            ];
            Kinerja_data::insert($insertkinerja);
        }

        if ($request->keterangan) {
			$keterangan = $request->keterangan;
		} else {
			$keterangan = '-';
		}

		$insertaktivitas = [
			'sts'               => 1,
			'uname'             => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
			'tgl'               => date('Y-m-d H:i:s'),
			'ip'                => '',
			'logbuat'           => '',
			'idemp'             => $id_emp,
			'tgl_trans'         => $tgl_trans,
			'time1'             => $request->time1,
			'time2'             => $request->time2,
			'uraian'            => $request->uraian,
			'keterangan'        => $keterangan,
            'kinerja_detail_id' => $kinerja_detail_id
		];

        if (Kinerja_detail::insert($insertaktivitas)) {
            return redirect('/kepegawaian/entri kinerja')
                    ->with('message', 'Data kinerja berhasil ditambah. Silahkan buka menu LAPORAN KINERJA untuk melihat kinerja anda secara lengkap')
                    ->with('msg_num', 1);
		} else {
            return redirect()->back()->withInput()
                    ->with('message', 'Data kinerja gagal tersimpan')
                    ->with('msg_num', 2);
		}
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

    public function formdeleteaktivitas(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}

		$idemp = $request->idemp;
		$tgl_trans = $request->tgl_trans;
		$time1 = $request->time1;
		$uraian = $request->uraian;
		$keterangan = $request->keterangan;
        $kinerja_detail_id = $request->kinerja_detail_id;

        if (is_null($kinerja_detail_id)) {
            Kinerja_detail::	
                where('idemp', $idemp)
                ->where('tgl_trans', $tgl_trans)
                ->where('time1', $time1)
                ->where('uraian', $uraian)
                ->where('keterangan', $keterangan)
                ->delete();
        } else {
            Kinerja_detail::	
                where('idemp', $idemp)
                ->where('tgl_trans', $tgl_trans)
                ->where('kinerja_detail_id', $kinerja_detail_id)
                ->delete();
        }

        return redirect('/kepegawaian/entri kinerja')
            ->with('message', 'Data aktivitas berhasil dihapus')
            ->with('msg_num', 1);
    }
}
