<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Traits\SessionCheckTraits;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use App\Agenda_tb;
use App\Berita_tb;
use App\Glo_arsip_kategori;
use App\Glo_tujuan_kehadiran;
use App\Help;
use App\Internal_arsip;
use App\Internal_info;
use App\Internal_kehadiran;
use App\Internal_responsehadir;
use App\Sec_menu;

class FormController extends Controller
{
	public function openform($id, $judul)
	{
		date_default_timezone_set('Asia/Jakarta');
		$nowtime = date('Y-m-d');
		
		$form = Internal_kehadiran::
					join('bpaddtfake.dbo.glo_tujuan_kehadiran', 'bpaddtfake.dbo.glo_tujuan_kehadiran.ids', '=', 'bpaddtfake.dbo.internal_kehadiran.tujuan_id')
					->where('bpaddtfake.dbo.internal_kehadiran.sts', '1')
					->where('no_form', $id)
					->orderBy('tgl_mulai', 'desc')
					->first();

		if($nowtime > $form['tgl_end']) {
			$flaglewat = 1;
		} else {
			$flaglewat = 0;
		}

		$ref_form = Glo_tujuan_kehadiran::
					where('ids', $form['tujuan_id'])
					->first();

		$query = ($ref_form['ket_tujuan'] ?? '');

		$emps = DB::select( DB::raw("  
					SELECT id_emp, nrk_emp, nip_emp, nm_emp, a.idgroup_aset as idgroup, tgl_lahir, jnkel_emp, tgl_join, status_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit, d.nm_lok as nm_lok, d.kd_lok as kd_lok  from bpaddtfake.dbo.emp_data as a
					CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
					CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
					,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
					$query
					and ked_emp = 'aktif'
					order by tbunit.kd_unit, a.nm_emp"));
		$emps = json_decode(json_encode($emps), true);

		return view('pages.bpadkehadiran.openform')
				->with('form', $form)
				->with('emps', $emps)
				->with('flaglewat', $flaglewat);
	}
	
	public function simpanform(Request $request)
	{
		var_dump($request->all());
		die;

		$filefoto = '';

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		if (isset($request->fotohadir)) {

            $data = $request->fotohadir;
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);

            $filefoto .= $request->absentgl . "_" . $request->absenjenis . ".png";
            
			$tujuan_upload = config('app.savefilekehadiran');
			$tujuan_upload .= "\\" . $request->absenid . "\\";
            $tujuan_upload .= "\\" . $filefoto;

            if (!is_dir(config('app.savefilekehadiran') . "\\" . $request->absenid)) {
                // dir doesn'kt exist, make it
                mkdir(config('app.savefilekehadiran') . "\\" . $request->absenid);
            }

            file_put_contents($tujuan_upload, $data);   
		}
			
		if (!(isset($filefoto))) {
			$filefoto = '';
		}

		date_default_timezone_set('Asia/Jakarta');
		$insert = [
				'tgl'       	=> date('Y-m-d H:i:s'),	
				'no_form'      	=> $request->form,
				'id_emp'     	=> $request->id_emp,
				'hadir'       	=> $request->tampil,
			];
		Internal_responsehadir::insert($insert);

		$nextpage = "/form/" . $request->form . "/thanks";
		return redirect($nextpage);
	}

	public function openthanksform($id)
	{
		$form = Internal_kehadiran::
			join('bpaddtfake.dbo.glo_tujuan_kehadiran', 'bpaddtfake.dbo.glo_tujuan_kehadiran.ids', '=', 'bpaddtfake.dbo.internal_kehadiran.tujuan_id')
			->where('bpaddtfake.dbo.internal_kehadiran.sts', '1')
			->where('no_form', $id)
			->orderBy('tgl_mulai', 'desc')
			->first();

		return view('pages.bpadkehadiran.openthanksform')
				->with('form', $form);
	}

	public function openresponseform($id)
	{
		$form = Internal_kehadiran::
			join('bpaddtfake.dbo.glo_tujuan_kehadiran', 'bpaddtfake.dbo.glo_tujuan_kehadiran.ids', '=', 'bpaddtfake.dbo.internal_kehadiran.tujuan_id')
			->where('bpaddtfake.dbo.internal_kehadiran.sts', '1')
			->where('no_form', $id)
			->orderBy('tgl_mulai', 'desc')
			->first();

		$no_form = $form['no_form'];

		$ref_form = Glo_tujuan_kehadiran::
			where('ids', $form['tujuan_id'])
			->first();

		$query = ($ref_form['ket_tujuan'] ?? '');

		$emps = DB::select( DB::raw("  
				SELECT distinct(a.id_emp), a.nip_emp, a.nrk_emp, a.nm_emp, res.hadir as sts, tbunit.kd_unit, tbunit.nm_unit, totalhadir,
				CASE WHEN res.hadir = 1
					THEN 'HADIR'
					ELSE 'TIDAK HADIR'
				END as hadir
				from (select count(id_emp) as totalhadir from bpaddtfake.dbo.internal_responsehadir where hadir = '1' and no_form = '$no_form') counthadir, bpaddtfake.dbo.emp_data a
				join bpaddtfake.dbo.emp_jab tbjab on tbjab.ids = (SELECT TOP 1 ids FROM bpaddtfake.dbo.emp_jab WHERE emp_jab.noid = a.id_emp and emp_jab.sts='1' ORDER BY tmt_jab DESC)
				join bpaddtfake.dbo.glo_org_unitkerja tbunit on tbunit.kd_unit = (SELECT TOP 1 idunit FROM bpaddtfake.dbo.glo_org_unitkerja where tbunit.kd_unit = tbjab.idunit)
				left join bpaddtfake.dbo.internal_responsehadir res on a.id_emp = res.id_emp and res.no_form = '$no_form'
				where a.ked_emp = 'AKTIF'
				and a.sts = 1
				and a.id_emp = tbjab.noid
				and tbjab.sts = 1
				$query
				order by tbunit.kd_unit, nm_emp
				"));
		$emps = json_decode(json_encode($emps), true);	

		return view('pages.bpadkehadiran.openresponse')
				->with('form', $form)
				->with('ref_form', $ref_form)
				->with('emps', $emps);
	}
}
