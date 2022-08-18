<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models76\Mobile_absen_ref;
use App\Models76\Ref_jenis_absen;
use App\Models76\Ref_subjenis_absen;
use App\Models76\Ref_subsubjenis_absen;
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
        whereRaw('LEN(kd_unit) <= 6')
        ->orderBy('kd_unit')
        ->get();

        if($request->idunit) {
            $unitnow = implode(",", $request->idunit);
            $queryunitnow = "AND c.kd_bidang IN ($unitnow)";
        } else {
            $unitnow = 'null';
            $queryunitnow = '';
        }

        if($unitnow != NULL) {
            $unitnow = explode(",", $unitnow);
        }

        $getrekapabsen = 
        DB::connection('server76')->select( 
            DB::raw(
                "
                SELECT 
                        pegawaiall.kd_bidang, 
                        pegawaiall.nm_unit, 
                        pegawaiall.total as total_pegawai, 
                        pegawaiwajib.total as total_wajibapel, 
                        pegawaiizin.total as total_izin, 
                        (pegawaiwajib.total - pegawaiizin.total) as total_wajib_absen,
                        pegawaihadir.total as total_hadir
                    FROM (
                        SELECT
                            unit.nm_unit,
                            res_all.kd_bidang,
                            res_all.total
                        FROM (
                            SELECT
                                c.kd_bidang,
                                count(a.id_emp) as total
                            FROM
                                bpaddtfake.dbo.[emp_data] a
                                JOIN bpaddtfake.dbo.[emp_jab] b ON b.ids = ( SELECT TOP 1 ids FROM bpaddtfake.dbo.emp_jab WHERE emp_jab.noid = a.id_emp AND emp_jab.sts= '1' ORDER BY tmt_jab DESC )
                                JOIN bpaddtfake.dbo.[glo_org_unitkerja] c ON c.kd_unit = ( SELECT TOP 1 idunit FROM bpaddtfake.dbo.glo_org_unitkerja WHERE b.idunit like c.kd_unit + '%'  ) 
                            WHERE
                                a.ked_emp = 'AKTIF' 
                                AND a.sts = 1 
                                AND b.sts = 1 
                                AND a.id_emp = b.noid 
                                $queryunitnow
                                
                            GROUP BY 
                                c.kd_bidang
                        ) res_all
                        JOIN bpaddtfake.dbo.glo_org_unitkerja unit ON res_all.kd_bidang = unit.kd_unit
                    ) pegawaiall
                    JOIN (
                        SELECT
                            unit.nm_unit,
                            res_tdk_wajib_absen.kd_bidang,
                            res_tdk_wajib_absen.total
                        FROM (
                            SELECT
                                c.kd_bidang,
                                count(a.id_emp) as total
                            FROM
                                bpaddtfake.dbo.[emp_data] a
                                JOIN bpaddtfake.dbo.[emp_jab] b ON b.ids = ( SELECT TOP 1 ids FROM bpaddtfake.dbo.emp_jab WHERE emp_jab.noid = a.id_emp AND emp_jab.sts= '1' ORDER BY tmt_jab DESC )
                                JOIN bpaddtfake.dbo.[glo_org_unitkerja] c ON c.kd_unit = ( SELECT TOP 1 idunit FROM bpaddtfake.dbo.glo_org_unitkerja WHERE b.idunit like c.kd_unit + '%'  ) 
                            WHERE
                                a.ked_emp = 'AKTIF' 
                                AND a.sts = 1 
                                AND b.sts = 1 
                                AND a.id_emp = b.noid 
                                AND a.is_tidak_wajib_apel is null
                            GROUP BY 
                                c.kd_bidang
                        ) res_tdk_wajib_absen
                        JOIN bpaddtfake.dbo.glo_org_unitkerja unit ON res_tdk_wajib_absen.kd_bidang = unit.kd_unit
                    ) pegawaiwajib 
                        ON pegawaiall.kd_bidang = pegawaiwajib.kd_bidang
                    JOIN (
                        SELECT
                            unit.nm_unit,
                            res_izin.kd_bidang,
                            res_izin.total
                        FROM (
                            SELECT
                                c.kd_bidang,
                                count(absen.hadir) as total
                            FROM
                                bpaddtfake.dbo.[emp_data] a
                                JOIN bpaddtfake.dbo.[emp_jab] b ON b.ids = ( SELECT TOP 1 ids FROM bpaddtfake.dbo.emp_jab WHERE emp_jab.noid = a.id_emp AND emp_jab.sts= '1' ORDER BY tmt_jab DESC )
                                JOIN bpaddtfake.dbo.[glo_org_unitkerja] c ON c.kd_unit = ( SELECT TOP 1 idunit FROM bpaddtfake.dbo.glo_org_unitkerja WHERE b.idunit like c.kd_unit + '%'  ) 
                                LEFT JOIN bpaddtfake.dbo.mobile_absen absen ON a.id_emp = absen.id_emp AND absen.kegiatan = '$text' AND absen.hadir = 2
                            WHERE	
                                a.ked_emp = 'AKTIF' 
                                AND a.sts = 1 
                                AND b.sts = 1 
                                AND a.id_emp = b.noid 
                            GROUP BY 
                                c.kd_bidang
                        ) res_izin
                        JOIN bpaddtfake.dbo.glo_org_unitkerja unit ON res_izin.kd_bidang = unit.kd_unit
                    ) pegawaiizin 
                        ON pegawaiall.kd_bidang = pegawaiizin.kd_bidang
                    JOIN (
                        SELECT
                            unit.nm_unit,
                            res_yang_hadir.kd_bidang,
                            res_yang_hadir.total
                        FROM (
                            SELECT
                                c.kd_bidang,
                                count(absen.hadir) as total
                            FROM
                                bpaddtfake.dbo.[emp_data] a
                                JOIN bpaddtfake.dbo.[emp_jab] b ON b.ids = ( SELECT TOP 1 ids FROM bpaddtfake.dbo.emp_jab WHERE emp_jab.noid = a.id_emp AND emp_jab.sts= '1' ORDER BY tmt_jab DESC )
                                JOIN bpaddtfake.dbo.[glo_org_unitkerja] c ON c.kd_unit = ( SELECT TOP 1 idunit FROM bpaddtfake.dbo.glo_org_unitkerja WHERE b.idunit like c.kd_unit + '%'  ) 
                                LEFT JOIN bpaddtfake.dbo.[mobile_absen] absen ON a.id_emp = absen.id_emp 
                                    AND absen.kegiatan = '$text' 
                                    AND a.is_tidak_wajib_apel is null
                                    AND absen.hadir = 1
                            WHERE
                                a.ked_emp = 'AKTIF' 
                                AND a.sts = 1 
                                AND b.sts = 1 
                                AND a.id_emp = b.noid 
                            GROUP BY 
                                c.kd_bidang
                        ) res_yang_hadir
                        JOIN bpaddtfake.dbo.glo_org_unitkerja unit ON res_yang_hadir.kd_bidang = unit.kd_unit
                    ) pegawaihadir 
                        ON pegawaiall.kd_bidang = pegawaihadir.kd_bidang
                    ORDER BY pegawaiall.kd_bidang
                " 
            )
        );
        $getrekapabsen = json_decode(json_encode($getrekapabsen), true);

        return view('pages.bpadhidden.qrabsenrekap')
        ->with('getref', $getref)
        ->with('bidangs', $bidangs)
        ->with('unitnow', $unitnow)
        ->with('getrekapabsen', $getrekapabsen);
    }

    public function getpegawaitidakabsen(Request $request)
    {
        $idunit = $request->idunit;
        $qr = $request->qr;

        $getref = Mobile_absen_ref::where('longtext', 'LIKE', $qr . '%')->first();
        $splitref = explode($getref['salt'], $getref['longtext']);

        $text = $splitref[0];
        $end_date = $getref['end_datetime'];

        if($request->idunit) {
            $idunit = str_replace("%2C", ",", $idunit);
            $queryunitnow = "AND tbunit.kd_bidang IN ($idunit)";
        } else {
            $queryunitnow = '';
        }

		$query = 
        DB::connection('server76')->select( 
            DB::raw("
                SELECT
                    a.id_emp,
                    a.nip_emp,
                    a.nrk_emp,
                    a.nm_emp,
                    res.hadir AS sts,
                    tbunit.kd_unit,
                    tbunit.nm_unit,
                    tbunit.nm_bidang, 
                    totalhadir,
                    a.is_tidak_wajib_apel,
                    res.datetime,
                    refsub.nm_sub_absen,
                    refsubsub.nm_subsub_absen,
                CASE
                        
                        WHEN res.hadir = 1 THEN
                    CASE
                            
                            WHEN res.datetime > '$end_date' THEN
                            'TERLAMBAT' ELSE 'HADIR' 
                        END ELSE 'TIDAK HADIR' 
                    END AS kehadiran
                FROM
                    ( SELECT COUNT ( DISTINCT ( id_emp ) ) AS totalhadir FROM bpaddtfake.dbo.mobile_absen WHERE hadir = '1' AND kegiatan = '$text' ) counthadir,
                    bpaddtfake.dbo.emp_data a
                    JOIN bpaddtfake.dbo.emp_jab tbjab ON tbjab.ids = ( SELECT TOP 1 ids FROM bpaddtfake.dbo.emp_jab WHERE emp_jab.noid = a.id_emp AND emp_jab.sts= '1' ORDER BY tmt_jab DESC )
                    JOIN bpaddtfake.dbo.glo_org_unitkerja tbunit ON tbunit.kd_unit = ( SELECT TOP 1 idunit FROM bpaddtfake.dbo.glo_org_unitkerja WHERE tbunit.kd_unit = tbjab.idunit )
                    LEFT JOIN bpaddtfake.dbo.mobile_absen res ON a.id_emp = res.id_emp 
                    AND res.kegiatan = '$text'
                    LEFT JOIN bpaddtfake.dbo.ref_subjenis_absen refsub ON res.subjenis = refsub.id_sub_absen
                    LEFT JOIN bpaddtfake.dbo.ref_subsubjenis_absen refsubsub ON res.subsubjenis = refsubsub.id_subsub_absen 
                WHERE
                    a.ked_emp = 'AKTIF' 
                    AND a.sts = 1 
                    AND a.id_emp = tbjab.noid 
                    AND tbjab.sts = 1 	
                    AND res.hadir is null
                    AND a.is_tidak_wajib_apel is null
                    $queryunitnow
                ORDER BY
                    tbunit.kd_unit,
                    nm_emp	
					"));
		$query = json_decode(json_encode($query), true);

		return $query;
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
        SELECT 
            a.id_emp, 
            a.nip_emp, 
            a.nrk_emp, 
            a.nm_emp, 
            res.hadir as sts, 
            tbunit.kd_unit, 
            tbunit.nm_unit, 
            tbunit.nm_bidang, 
            totalhadir, 
            a.is_tidak_wajib_apel, 
            res.datetime, 
            refsub.nm_sub_absen, 
            refsubsub.nm_subsub_absen, 
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
                THEN 'TIDAK WAJIB APEL'
                ELSE 'WAJIB APEL'
        END as tidak_wajib_apel
        from (select count(distinct(id_emp)) as totalhadir from bpaddtfake.dbo.mobile_absen where hadir = '1' and kegiatan = '$text') counthadir, bpaddtfake.dbo.emp_data a
        join bpaddtfake.dbo.emp_jab tbjab on tbjab.ids = (SELECT TOP 1 ids FROM bpaddtfake.dbo.emp_jab WHERE emp_jab.noid = a.id_emp and emp_jab.sts='1' ORDER BY tmt_jab DESC)
        join bpaddtfake.dbo.glo_org_unitkerja tbunit on tbunit.kd_unit = (SELECT TOP 1 idunit FROM bpaddtfake.dbo.glo_org_unitkerja where tbunit.kd_unit = tbjab.idunit)
        left join bpaddtfake.dbo.mobile_absen res on a.id_emp = res.id_emp and res.kegiatan = '$text'
        left join bpaddtfake.dbo.ref_subjenis_absen refsub on res.subjenis = refsub.id_sub_absen
        left join bpaddtfake.dbo.ref_subsubjenis_absen refsubsub on res.subsubjenis = refsubsub.id_subsub_absen
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
