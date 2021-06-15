<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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

class ApiDisposisiController extends Controller
{
    public function disposisi(Request $request)
	{
		$id_emp = $request->id_emp;

		$employees = DB::select( DB::raw("  
					SELECT id_emp, nrk_emp, nip_emp, nm_emp, a.idgroup as idgroup, tgl_lahir, jnkel_emp, tgl_join, status_emp, tbjab.idjab, tbjab.idunit, tbunit.nm_unit, tbunit.child, d.nm_lok from bpaddtfake.dbo.emp_data as a
					CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM  bpaddtfake.dbo.emp_gol,bpaddtfake.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
					CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
					CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpaddtfake.dbo.emp_dik,bpaddtfake.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
					CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
					,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
					and id_emp like '$id_emp'
					order by idunit asc, nm_emp ASC") )[0];
		$employees = json_decode(json_encode($employees), true);

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

		$idgroup = $id_emp;
		if (is_null($idgroup)) {
			$qid = '';
		} else {
			$qid = "and d.to_pm = '".$idgroup."'";
		}

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
												  and (m.catatan_final <> 'undangan' or m.catatan_final is null )
												  AND d.idtop > 0 AND d.child = 0
												  $qid
												  $qsearchnow
												  order by d.tgl_masuk desc, d.no_form desc, d.ids asc"));
		$dispinboxsurat = json_decode(json_encode($dispinboxsurat), true);

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

		if (strlen($employees['idunit']) == 8) {
			$rd = "";
			$qid = "d.from_pm = '".$idgroup."'";
			$or = "or (d.to_pm = '".$idgroup."' and d.selesai = 'Y')";
			// $rd = "(d.rd like 'N' or d.rd like 'Y')";
		} elseif (strlen($employees['idunit']) == 10) {
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
												  and (m.catatan_final <> 'undangan' or m.catatan_final is null )
												  $qsearchnow
												  and (
												  ($rd $qid)
												  $or)
												  order by d.tgl_masuk desc, d.no_form desc, d.ids asc"));
		$dispsentsurat = json_decode(json_encode($dispsentsurat), true);

		// var_dump($dispsent);
		echo json_encode($dispinboxsurat);
		die();

		return view('pages.bpaddisposisi.disposisi')
				->with('dispinboxundangan', $dispinboxundangan)
				->with('dispinboxsurat', $dispinboxsurat)
				->with('dispsentundangan', $dispsentundangan)
				->with('dispsentsurat', $dispsentsurat)
				->with('dispdraft', $dispdraft)
				->with('signnow', $signnow)
				->with('searchnow', $request->searchnow)
				->with('monthnow', $monthnow)
				->with('yearnow', $yearnow)
				->with('notifs', $notifs);
	}
}
