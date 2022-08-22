<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models76\Mobile_absen_ref;
use App\Models76\Mobile_absen;
use App\Models76\Ref_jenis_absen;
use App\Models76\Ref_subjenis_absen;
use App\Models76\Ref_subsubjenis_absen;
use App\Emp_data;
use App\Sec_menu;

use App\Traits\SessionCheckTraits;
use App\Traits\SessionCheckNotif;

session_start();

class HiddenController extends Controller
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

    public function index(Request $request)
    {
        return view('pages.bpadhidden.index');
    }

    public function qrabsensetup(Request $request)
    {
        //$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

        $refs = Mobile_absen_ref::orderBy('createdate', 'desc')->get();

        return view('pages.bpadhidden.qrabsensetup')
        ->with('access', $access)
        ->with('refs', $refs);
    }

    public function forminsertqrabsen(Request $request)
    {
        var_dump($request->all());
        // $splitstartdate = explode("/", $request->start_date);
        var_dump( date('Y-m-d', strtotime($request->start_date)) );
        var_dump( date('Y-d-m', strtotime($request->end_date)) );
        var_dump( date('H:i', strtotime($request->start_time)) );
        var_dump( date('H:i', strtotime($request->end_time)) );

        Mobile_absen_ref::insert([
            'start_datetime' => date($request->start_date),
            'end_datetime' => date($request->end_date),
            'longtext' => $request->longtext . "W",
        ]);
        die;

        $nama_kegiatan = $request->getParam('nama_kegiatan', '');
        $salt = $request->getParam('salt', '');
        $url = $request->getParam('url', '');
        $url = $request->getParam('url', '');
        $url = $request->getParam('url', '');

        Mobile_absen_ref::insert([

        ]);
    }

    public function qrabsensetpegawai (Request $request)
    {
        $qr = $request->qr;

        $getref = Mobile_absen_ref::where('longtext', 'LIKE', $qr . '%')->first();
        $splitref = explode($getref['salt'], $getref['longtext']);

        $text = $splitref[0];
        $end_date = $getref['end_datetime'];
        $start_date = $getref['start_datetime'];

        $ref_absens = Ref_jenis_absen::orderBy('id_ref_absen', 'desc')->get();
        $ref_sub_absens = Ref_subjenis_absen::orderBy('id_sub_absen')->get();
        $ref_subsub_absens = 
        Ref_subsubjenis_absen::
        join('bpaddtfake.dbo.ref_subjenis_absen as subjenis', 'bpaddtfake.dbo.ref_subsubjenis_absen.id_sub_absen', 'subjenis.id_sub_absen')
        ->orderBy('id_subsub_absen')
        ->get();

        if(Auth::user()->usname && $_SESSION['user_data']['deskripsi_user']){
            $param = '0101'.$_SESSION['user_data']['deskripsi_user'];
            $pegawais = 
            DB::connection('server76')->select( 
                DB::raw(
                "exec bpaddtfake.dbo.proc_getallpegawai_withfilter_bidang @Idunit = '".$param."'"
                )
            );
        } else {
            $pegawais = 
            DB::connection('server76')->select( 
                DB::raw(
                "exec bpaddtfake.dbo.proc_getallpegawai", 
                )
            );
        }
        $pegawais = json_decode(json_encode($pegawais), true);

        return view('pages.bpadhidden.qrabsensetpegawai')
                ->with('pegawais', $pegawais)
                ->with('getref', $getref)
                ->with('ref_absens', $ref_absens)
                ->with('ref_sub_absens', $ref_sub_absens)
                ->with('ref_subsub_absens', $ref_subsub_absens);
    }

    public function formuptdatestsabsen(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $datenow     = date('YmdHis');
        $datetimenow = date('Y-m-d H:i:s');

        $qr = $request->longtext;
        $getref = Mobile_absen_ref::where('longtext', 'LIKE', $qr . '%')->first();
        $splitref = explode($getref['salt'], $getref['longtext']);
        $text = $splitref[0];

        $cekabsen = Mobile_absen::
        where('kegiatan', $text)
        ->where('id_emp', $request->id_emp)
        ->orderBy('datetime', 'asc')
        ->get();

        //kalo emp tersebut belom ada, maka insert
        if(count($cekabsen) == 0) {
            Mobile_absen::insert([
                'id_emp'            => $request->id_emp,
                'datetime'          => $datetimenow,
                'kegiatan'          => $text,
                'hadir'             => $request->jenis_absen,
                'subjenis'          => $request->subjenis_absen,
                'subsubjenis'       => $request->subsubjenis_absen,
            ]);
        } elseif(count($cekabsen) > 0) {
            if($request->jenis_absen == 1) {
                Mobile_absen::
                where('id_emp', $request->id_emp)
                ->where('kegiatan',  $text)
                ->update([
                    'datetime'          => $datetimenow,
                    'hadir'             => $request->jenis_absen,
                    'subjenis'          => null,
                    'subsubjenis'       => null,
                ]);
            } elseif ($request->jenis_absen == 2) {
                Mobile_absen::
                where('id_emp', $request->id_emp)
                ->where('kegiatan',  $text)
                ->update([
                    'datetime'          => $datetimenow,
                    'hadir'             => $request->jenis_absen,
                    'subjenis'          => $request->subjenis_absen,
                    'subsubjenis'       => $request->subsubjenis_absen,
                ]);
            }
        }

        return redirect('/qrabsen/setpegawai?qr='.$text)
				->with('message', "Berhasil mengubah status absensi pegawai")
				->with('msg_num', 1);
    }
}
