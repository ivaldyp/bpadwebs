<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Mob_pushnotif;
use App\Glo_mob_notiftipe;

session_start();

class MobileController extends Controller
{
    public function notifall(Request $request)
    {
        $notifs = Mob_pushnotif::
					join('glo_mob_notiftipe', 'glo_mob_notiftipe.ids', '=', 'mob_pushnotif.tipe')	
					->where('mob_pushnotif.sts', 1)
					->get();

        return view('pages.bpadmobile.notifall')
                ->with('notifs', $notifs);
    }
    
    public function tambahnotif(Request $request)
    {
		$notif_tipe = Glo_mob_notiftipe::orderBy('ids')->get();
        return view('pages.bpadmobile.tambahnotif')
				->with('tipes', $notif_tipe);
    }

    public function forminsertnotif(Request $request)
    {
		date_default_timezone_set('Asia/Jakarta');
		
		if($request->tujuan == 0) {
			$tujuan = '::';
			foreach($request->devices as $key => $item) {
				if($key != 0) {
					$tujuan .= "::";
				}
				
				$tujuan .= $item;
			}
		} else {
			$tujuan = 1;
		}

        $filefoto = '';

		if (isset($request->img)) {
			$file = $request->img;

			$filefoto .= "m_notif_". date('YmdHis') ."." . $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefilemobilenotif');
			// $tujuan_upload .= "\\" . $request->form;

			$file->move($tujuan_upload, $filefoto);
		}
			
		if (!(isset($filefoto))) {
			$filefoto = '';
		}

		$insert = [
				'sts'       	=> '1',	
				'tgl'       	=> date('Y-m-d H:i:s'),	
				'uname'         => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),	
				'judul'       	=> $request->judul,	
				'isi'       	=> $request->isi,	
				'url'       	=> ($request->url ?? ''),
                'img'           => $filefoto,
                'appr'          => '0',
                'tipe'          => $request->tipe,
				'tujuan'		=> $tujuan,
			];
        Mob_pushnotif::insert($insert);

		return redirect('/mobile/notif')
				->with('message', 'Notifikasi baru berhasil dibuat')
				->with('msg_num', 1);
    }

	public function formapprovenotif(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

		Mob_pushnotif::where('ids', $request->ids)
						->update([
							'appr' => 1,
						]);

		return redirect('/mobile/notif')
				->with('message', 'Approval Notifikasi berhasil')
				->with('msg_num', 1);
    }

	public function formdeletenotif(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

		Mob_pushnotif::where('ids', $request->ids)
						->update([
							'sts' => 0,
						]);

		return redirect('/mobile/notif')
				->with('message', 'Notifikasi berhasil dihapus')
				->with('msg_num', 1);
    }
}
