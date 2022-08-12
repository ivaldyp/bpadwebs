<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models76\Mobile_absen_ref;
use App\Models76\Views\Get_rekap_absen;
use App\Models11\Emp_data;
use App\Glo_org_unitkerja;
use App\Models76\Views\Get_rekap_absen as ViewsGet_rekap_absen;

use App\Traits\SessionCheckTraits;
use App\Traits\SessionCheckNotif;

session_start();

class PublicController extends Controller
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

    public function qrabsenrekap(Request $request)
    {
        $qr = $request->qr;

        $getref = Mobile_absen_ref::where('longtext', 'LIKE', $qr . '%')->first();
        $splitref = explode($getref['salt'], $getref['longtext']);

        $text = $splitref[0];
        $end_date = $getref['end_datetime'];
        $start_date = $getref['start_datetime'];

        $bidangs = 
        Glo_org_unitkerja::
        whereRaw('LEN(kd_unit) = 6')
        ->get();

        // $getrekapabsen = Get_rekap_absen::orderBy('kd_bidang')->get();
        // $getrekapabsen = DB::connection('server76')->select('exec proc_get_absen_rekap ('.$longtext.')');
        $getrekapabsen = 
        DB::connection('server76')->select( 
            DB::raw(
              "exec bpaddtfake.dbo.proc_get_absen_rekap @Kegiatan = '$text'", 
            )
        );
        $getrekapabsen = json_decode(json_encode($getrekapabsen), true);

        return view('pages.bpadhidden.qrabsenrekap')
        ->with('getref', $getref)
        ->with('getrekapabsen', $getrekapabsen);
    }

    public function qrabsendetail(Request $request)
    {
        $qr = $request->qr;

        $getref = Mobile_absen_ref::where('longtext', 'LIKE', $qr . '%')->first();
        $splitref = explode($getref['salt'], $getref['longtext']);

        $text = $splitref[0];
        $end_date = $getref['end_datetime'];
        $start_date = $getref['start_datetime'];

        $units = 
        Glo_org_unitkerja::
        whereRaw('LEN(kd_unit) = 6')
        ->orderBy('kd_unit')
        ->get();

        if (is_null($request->unit)) {
			$idunit = '01';
		} else {
			$idunit = $request->unit;
		}

        if($request->unit) {
            $queryunit = "AND tbunit.kd_unit like '$request->unit%' ";
        } else {
            $queryunit = '';
        }

        $totalemps = Emp_data::count();
        $emps = DB::connection('server76')->select( DB::raw("
        SELECT a.id_emp, a.nip_emp, a.nrk_emp, a.nm_emp, res.hadir as sts, tbunit.kd_unit, tbunit.nm_unit, totalhadir, a.is_tidak_wajib_apel, res.datetime,
        CASE 
        WHEN res.hadir = 1
            THEN 
                CASE 
                WHEN res.datetime > '$end_date' THEN 'TERLAMBAT'
                ELSE 'HADIR' 
                END
            ELSE 'TIDAK HADIR'
        END as kehadiran,
        CASE WHEN a.is_tidak_wajib_apel = 1
            THEN 'TIDAK'
            ELSE 'WAJIB'
        END as tidak_wajib_apel
        from (select count(distinct(id_emp)) as totalhadir from bpaddtfake.dbo.mobile_absen where hadir = '1' and kegiatan = '$text') counthadir, bpaddtfake.dbo.emp_data a
        join bpaddtfake.dbo.emp_jab tbjab on tbjab.ids = (SELECT TOP 1 ids FROM bpaddtfake.dbo.emp_jab WHERE emp_jab.noid = a.id_emp and emp_jab.sts='1' ORDER BY tmt_jab DESC)
        join bpaddtfake.dbo.glo_org_unitkerja tbunit on tbunit.kd_unit = (SELECT TOP 1 idunit FROM bpaddtfake.dbo.glo_org_unitkerja where tbunit.kd_unit = tbjab.idunit)
        left join bpaddtfake.dbo.mobile_absen res on a.id_emp = res.id_emp and res.kegiatan = '$text'
        where a.ked_emp = 'AKTIF'
        and a.sts = 1
        and a.id_emp = tbjab.noid
        and tbjab.sts = 1
        $queryunit
        order by tbunit.kd_unit, nm_emp
                "));
        $emps = json_decode(json_encode($emps), true);
        
        return view('pages.bpadhidden.qrabsendetail')
                ->with('qr', $qr)
                ->with('getref', $getref)
                ->with('emps', $emps)
                ->with('units', $units)
                ->with('idunit', $idunit)
                ->with('totalemps', $totalemps);
    }
}
