<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AbsenController extends Controller
{
    public function login (Request $request)
    {
        return view('pages.bpadabsen.login');
    }

    public function foto (Request $request) 
    {
        if(isset($request->username) && isset($request->password)) {
            $username = $request->username;
            $password = $request->password;

            $findpegawai = DB::select( DB::raw("
            select id_emp, nm_emp, nip_emp, nrk_emp, tbunit.nm_unit, tbunit.kd_unit
            from bpaddtfake.dbo.emp_data a
            join bpaddtfake.dbo.emp_jab tbjab on tbjab.ids = (SELECT TOP 1 ids FROM bpaddtfake.dbo.emp_jab WHERE emp_jab.noid = a.id_emp and emp_jab.sts='1' ORDER BY tmt_jab DESC)
            join bpaddtfake.dbo.glo_org_unitkerja tbunit on tbunit.kd_unit = (SELECT TOP 1 idunit FROM bpaddtfake.dbo.glo_org_unitkerja where tbunit.kd_unit = tbjab.idunit)
            where a.ked_emp = 'AKTIF'
            and a.sts = 1
            and a.id_emp = tbjab.noid
            and tbjab.sts = 1
            and (a.id_emp like '".$username."' or a.nrk_emp like '".$username."' or a.nip_emp like '".$username."')
            order by nm_emp") );
            $findpegawai = json_decode(json_encode($findpegawai), true);

            if(!(isset($findpegawai[0]))) {
                return redirect('/absen/login')
                    ->with('message', 'Username / Password tidak ditemukan')
                    ->with('msg_num', 2)
                    ->withInput($request->input()); 
            }
        } else {
            return redirect('/absen/login')
				->with('message', 'Silahkan login terlebih dahulu')
				->with('msg_num', 2)
                ->withInput($request->input());
        }

        return view('pages.bpadabsen.foto')
            ->with('data', $findpegawai[0]);
    }

    public function simpanfoto(Request $request) 
    {

    }
}
