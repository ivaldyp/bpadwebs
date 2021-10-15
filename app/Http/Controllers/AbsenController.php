<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Kinerja_foto;

class AbsenController extends Controller
{
    public function masuk (Request $request)
    {
        return view('pages.bpadabsen.masuk');
    }

    public function foto (Request $request) 
    {
        if(isset($request->username) && isset($request->password)) {
            $username = $request->username;
            $password = md5($request->password);

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
            and a.passmd5 = '".$password."'
            order by nm_emp") );
            $findpegawai = json_decode(json_encode($findpegawai), true);

            if(!(isset($findpegawai[0]))) {
                return redirect()->back()
                    ->with('message', 'Username / Password tidak ditemukan')
                    ->with('msg_num', 2)
                    ->withInput($request->input()); 
            }
        } else {
            return redirect()->back()
				->with('message', 'Silahkan login terlebih dahulu')
				->with('msg_num', 2)
                ->withInput($request->input());
        }

        return view('pages.bpadabsen.foto2')
            ->with('data', $findpegawai[0]);
    }

    public function cekabsen(Request $request) 
    {
        $id = $request->id;
        $jenis = $request->jenis;
        $tgl = $request->tgl;
        $cekabsen = DB::select( DB::raw("
        select count(sts) as total
        from bpaddtfake.dbo.kinerja_foto
        where absen_id = '".$id."'
        and absen_jenis = '".$jenis."'	
        and absen_tgl = '".$tgl."'") )[0];
        $cekabsen = json_decode(json_encode($cekabsen), true);

        if ($cekabsen['total'] > 0) {
			// tandanya udah ada absen
			return 1;
		} else {
            return 0;
        }
    }

    public function simpan(Request $request) 
    {
        $jam = $request->absenjam;
        if($jam >= 0 && $jam < 5) {
            return redirect()->back()
            ->with('message', 'Tidak dapat menyimpan foto (Absen pagi pukul 05 - 07)')
            ->with('msg_num', 2);
        } else if ($jam >= 5 && $jam < 7) {
            $stat = "Tepat Waktu";
        } else if ($jam >= 7 && $jam < 12) {
            $stat = "Terlambat";
        } else if ($jam >= 12 && $jam < 16) {
            $stat = "Pulang Cepat";
        } else if ($jam >= 16 && $jam < 20) {
            $stat = "Tepat Waktu";
        } else if ($jam >= 20 && $jam < 24) {
            return redirect()->back()
            ->with('message', 'Tidak dapat menyimpan foto (Absen sore pukul 16 - 20)')
            ->with('msg_num', 2);
        } 

        $filefoto = '';

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		if (isset($request->absenimg)) {

            $data = $request->absenimg;
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);

            $filefoto .= $request->absentgl . "_" . $request->absenjenis . ".png";
            
			$tujuan_upload = config('app.savefileabsen');
			$tujuan_upload .= "\\" . $request->absenid;
            $tujuan_upload .= "\\" . $filefoto;

            if (!is_dir(config('app.savefileabsen') . "\\" . $request->absenid)) {
                // dir doesn'kt exist, make it
                mkdir(config('app.savefileabsen') . "\\" . $request->absenid);
            }

            file_put_contents($tujuan_upload, $data);   
		}
			
		if (!(isset($filefoto))) {
			$filefoto = '';
		}

        $insertfoto = [
			'sts'       => 1,
			'uname'     => $request->absenid,
			'tgl'       => date('Y-m-d H:i:s'),
            'ip'        => '',
			'absen_id' => $request->absenid,
			'absen_jenis' => $request->absenjenis,
			'absen_tgl' => $request->absentgl,
			'absen_waktu' => $request->absenwaktu,
			'absen_sts' => $stat,
			'absen_img' => $filefoto,
		];

		Kinerja_foto::insert($insertfoto);
        
        return redirect('esiappe/berhasil');
    }

    public function berhasil()
    {
        return view('pages.bpadabsen.berhasil');
    }
}
