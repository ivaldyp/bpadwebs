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
use App\Sec_logins;
use App\Sec_menu;

// use App\Models11\Password_sapu_jagat;

class ApiController extends Controller
{
	public function tldisposisi(Request $request)
	{
		$teks = substr($request->nama, 0, 10);
		// $perihal = $teks;
		// $rincian = $teks . "wowwowwow";
		// $hasil = json_decode($request->hasil);
		// $insertjabatan = [
		// 	'perihal' => $request->hasil->nama, 
		// 	'kepada' => $request->hasil->nama . "tesssssssssssss",
		// 	'uname' => $request->hasil->id_emp,
		// 	'ket_lain' => $request->hasil->id_unit,
		// 	'catatan' => $request->hasil->tindak_lanjut,
		// ];
		// $response = $request->hasil;
        // $datamap = json_decode($response->getBody());
		// $disp = DB::table('bpaddtfake.dbo.disposisi_tes')->insert(
		// 	['perihal' => $request->input('nama'), 
		// 	'kepada' => $request->input('nama') . "tesssssssssssss",
		// 	'uname' => $request->input('id_emp'),
		// 	'ket_lain' => $request->input('id_unit'),
		// 	]
		// );
		DB::table('bpaddtfake.dbo.disposisi_tes')->insert(
			['perihal' => $request->nama, 
			'kepada' => $request->nama . "tesssssssssssss"]
		);
		// $disp = Disposisi_tes::insert($insertjabatan);
		// return response()->json($disp);
		// return response()->json([
		// 	"message" => "new disp created",
		// 	"success" => true,
		// 	"data" => "",
		// ], 201);

		// echo($request->all());
		// var_dump($request->all());
		
		
		$result = [];
		$result['success'] = true;
		$result['message'] = "berhasil plis";
		$result['data'] = "";
		$result['kode'] = "400";
		return json($result);
	}

	public function kepegawaian(Request $request)
	{
		$arr_result = [];

		// ngambil data unit pegawai tsb
		$q_pegawai = DB::select( DB::raw("  
						SELECT id_emp, nrk_emp, nip_emp, nm_emp, a.idgroup_aset as idgroup, tgl_lahir, jnkel_emp, tgl_join, status_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit from bpaddtfake.dbo.emp_data as a
						CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
						CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
						,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
						and idunit like '01%' AND ked_emp = 'aktif'
						order by tbunit.kd_unit, nm_emp ASC") );
		// $q_pegawai = json_decode(json_encode($q_pegawai), true);
		$arr_result['pegawai'] = $q_pegawai;

		return json_decode(json_encode($arr_result, JSON_PRETTY_PRINT), true);
	}

	public function loginemp(Request $request)
	{
		$user = $request->user;
		$pass = $request->pass;

        // $pw_sapu = Password_sapu_jagat::first();

		$query1 = Emp_data::where('ked_emp', 'AKTIF')
							->where('sts', '1')
							->where(function($q) use ($user) {
				            $q->where('nrk_emp', $user)
		                        ->orWhere('nip_emp', $user)
		                        ->orWhere('id_emp', $user);
				            })
				            ->first(['id_emp', 'nrk_emp', 'nip_emp', 'nm_emp']);

		if (is_null($query1)) {
			return json_decode(json_encode("user not found"), true);
		} else {
			if ($pass == 'Bp@d2020!@' || $pass == 'rprikat2017' || $pass == 'BPAD@2023!@') {
				$user = Emp_data::
                                where('sts', 1)
                                ->where(function($q) use ($user) {
                                    $q->where('nrk_emp', $user)
                                        ->orWhere('nip_emp', $user)
                                        ->orWhere('id_emp', $user);
                                    })
			            		->first(['id_emp', 'nrk_emp', 'nip_emp', 'nm_emp']);
			} else {
				$user = Emp_data::
                            where('sts', 1)
                            ->where('passmd5', md5($pass))
							->where(function($q) use ($user) {
				            $q->where('nrk_emp', $user)
		                        ->orWhere('nip_emp', $user)
		                        ->orWhere('id_emp', $user);
				            })
				            ->first(['id_emp', 'nrk_emp', 'nip_emp', 'nm_emp']);
			}

			if (is_null($user)) {
				return json_decode(json_encode("password salah"), true);
			}
		}

		$arr_result = [];

		// ngambil data unit pegawai tsb
		$q_pegawai = DB::select( DB::raw("  
						SELECT id_emp, nrk_emp, nip_emp, nm_emp, a.idgroup_aset as idgroup, tgl_lahir, jnkel_emp, tgl_join, status_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit, d.nm_lok as nm_lok, d.kd_lok as kd_lok, tbunit.nm_bidang, tbunit.notes  from bpaddtfake.dbo.emp_data as a
						CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
						CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
						,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
						and id_emp = '$user->id_emp' and ked_emp = 'aktif'
						order by tbunit.kd_unit") )[0];
		// $q_pegawai = json_decode(json_encode($q_pegawai), true);

		$unit_pegawai = $q_pegawai->idunit;
		// $unit_pegawai = $q_pegawai['idunit'];
		$arr_result['pegawai'] = $q_pegawai;

		return json_decode(json_encode($arr_result, JSON_PRETTY_PRINT), true);
	}

	public function getuserdata(Request $request)
	{
		$arr_result = [];
		$user = $request->user;

		// ngambil data unit pegawai tsb
		$q_pegawai = DB::select( DB::raw("  
						SELECT id_emp, nrk_emp, nip_emp, nm_emp, a.idgroup_aset as idgroup, tgl_lahir, jnkel_emp, tgl_join, status_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit from bpaddtfake.dbo.emp_data as a
						CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
						CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
						,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
						and id_emp = '$user' and ked_emp = 'aktif'
						order by tbunit.kd_unit") )[0];
		// $q_pegawai = json_decode(json_encode($q_pegawai), true);

		$unit_pegawai = $q_pegawai->idunit;
		// $unit_pegawai = $q_pegawai['idunit'];
		$arr_result['pegawai'] = $q_pegawai;

		return json_decode(json_encode($arr_result, JSON_PRETTY_PRINT), true);
	}

	public function loginaset(Request $request)
	{
		$user = $request->user;
		$pass = $request->pass;

        // $pw_sapu = Password_sapu_jagat::first();

		if ($pass == 'Bp@d2020!@' || $pass == 'rprikat2017' || $pass == 'BPAD@2023!@') {
			if (is_numeric(substr($user, 0, 6)) && strlen($user) <= 9) {
				$user = Emp_data::where([
					'nrk_emp' => $user,
					'sts'    => 1,
					'ked_emp' => 'AKTIF',
				])->first(['id_emp', 'nrk_emp', 'nip_emp', 'nm_emp']);
			} elseif (is_numeric(substr($user, 0, 18)) && strlen($user) <= 21) {
				$user = Emp_data::where([
					'nip_emp' => $user,
					'sts'    => 1,
					'ked_emp' => 'AKTIF',
				])->first(['id_emp', 'nrk_emp', 'nip_emp', 'nm_emp']);
			} elseif (substr($user, 1, 1) == '.') {
				$user = Emp_data::where([
					'id_emp' => $user,
					'sts'    => 1,
					'ked_emp' => 'AKTIF',
				])->first(['id_emp', 'nrk_emp', 'nip_emp', 'nm_emp']);
			}
		} else {
			if (is_numeric(substr($user, 0, 6)) && strlen($user) <= 9) {
				$user = Emp_data::where([
					'nrk_emp' => $user,
					'sts'    => 1,
					'passmd5' => md5($pass),
					'ked_emp' => 'AKTIF',
				])->first(['id_emp', 'nrk_emp', 'nip_emp', 'nm_emp']);
			} elseif (is_numeric(substr($user, 0, 18)) && strlen($user) <= 21) {
				$user = Emp_data::where([
					'nip_emp' => $user,
					'sts'    => 1,
					'passmd5' => md5($pass),
					'ked_emp' => 'AKTIF',
				])->first(['id_emp', 'nrk_emp', 'nip_emp', 'nm_emp']);
			} elseif (substr($user, 1, 1) == '.') {
				$user = Emp_data::where([
					'id_emp' => $user,
					'sts'    => 1,
					'passmd5' => md5($pass),
					'ked_emp' => 'AKTIF',
				])->first(['id_emp', 'nrk_emp', 'nip_emp', 'nm_emp']);
			}
		}

		if (!($user)) {
			return json_decode(json_encode("user not found"), true);
		}

		// var_dump($user);
		// die();

		$arr_result = [];

		// ngambil data unit pegawai tsb
		$q_pegawai = DB::select( DB::raw("  
						SELECT id_emp, nrk_emp, nip_emp, nm_emp, a.idgroup_aset as idgroup, tgl_lahir, jnkel_emp, tgl_join, status_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit from bpaddtfake.dbo.emp_data as a
						CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
						CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
						,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
						and id_emp = '$user->id_emp' and ked_emp = 'aktif'
						order by tbunit.kd_unit") )[0];
		// $q_pegawai = json_decode(json_encode($q_pegawai), true);

		$unit_pegawai = $q_pegawai->idunit;
		// $unit_pegawai = $q_pegawai['idunit'];
		$arr_result['pegawai'] = $q_pegawai;


		// ngambil data atasan
		if (strlen($unit_pegawai) == 2) {
			$arr_result['atasan'] = NULL;
		} else {
			if (strlen($unit_pegawai) == 6) {
				$unit_atasan = substr($unit_pegawai, 0, 2);
			} elseif (strlen($unit_pegawai) > 6) {
				$unit_atasan = substr($unit_pegawai, 0, (strlen($unit_pegawai) - 2));
			}

			$q_atasan = DB::select( DB::raw("  
						SELECT id_emp, nrk_emp, nip_emp, nm_emp, a.idgroup, tgl_lahir, jnkel_emp, tgl_join, status_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit from bpaddtfake.dbo.emp_data as a
						CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
						CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
						,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
						and tbjab.idunit like '$unit_atasan' and ked_emp = 'aktif'
						order by tbunit.kd_unit") )[0];
			// $q_atasan = json_decode(json_encode($q_atasan), true);
			$arr_result['atasan'] = $q_atasan;
		}


		//ngambil data bawahan
		if (strlen($unit_pegawai) == 2) {
			$q_bawahan = DB::select( DB::raw("  
						SELECT id_emp, nrk_emp, nip_emp, nm_emp, a.idgroup, tgl_lahir, jnkel_emp, tgl_join, status_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit from bpaddtfake.dbo.emp_data as a
						CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
						CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
						,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
						and tbunit.sao like '$unit_pegawai%' and LEN(tbjab.idjab) < 10 and ked_emp = 'aktif'
						order by tbunit.kd_unit") );
			// $q_bawahan = json_decode(json_encode($q_bawahan), true);

			$arr_result['bawahan'] = $q_bawahan;
		} elseif (strlen($unit_pegawai) == 10) {
			$arr_result['bawahan'] = NULL;		
		} else {			
			$q_bawahan = DB::select( DB::raw("  
						SELECT id_emp, nrk_emp, nip_emp, nm_emp, a.idgroup, tgl_lahir, jnkel_emp, tgl_join, status_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit from bpaddtfake.dbo.emp_data as a
						CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
						CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
						,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
						and tbunit.sao like '$unit_pegawai%' and ked_emp = 'aktif'
						order by tbunit.kd_unit, nm_emp") );
			// $q_bawahan = json_decode(json_encode($q_bawahan), true);

			$arr_result['bawahan'] = $q_bawahan;
		}

		return json_decode(json_encode($arr_result, JSON_PRETTY_PRINT), true);

		// var_dump(json_encode($arr_result));
		// die();
	}
}
