<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Pem_carousel;

session_start();

class PemanfaatanController extends Controller
{
    public function carouselall(Request $request) 
    {
        $imgs = Pem_carousel::
                where('sts', 1)
                ->orderBy('appr', 'desc')
                ->orderBy('urut', 'asc')
                ->get();

        return view('pages.bpadpemanfaatan.carouselall')
                ->with('imgs', $imgs);
    }

    public function tambahcarousel(Request $request)
    {
        return view('pages.bpadpemanfaatan.carouseltambah');
    }

    public function forminsertcarousel(Request $request)
    {
		date_default_timezone_set('Asia/Jakarta');

        $filefoto = '';

		if (isset($request->image)) {
			$file = $request->image;

            if ($file->getSize() > 2222222) {
				return redirect()->back()->withInput()->with('message', 'Ukuran file terlalu besar (Maksimal 2MB)');     
			}

			$filefoto .= "pem_img_". date('YmdHis') ."." . $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefilepemanfaatancarousel');
			// $tujuan_upload .= "\\" . $request->form;

			$file->move($tujuan_upload, $filefoto);
		}
			
		if (!(isset($filefoto))) {
			$filefoto = '';
		}

		$insert = [
				'sts'       	=> '1',	
				'tgl'       	=> date('Y-m-d H:i:s'),	
				'usname'        => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),	
				'judul'       	=> $request->judul,	
				'url'       	=> ($request->url ?? ''),
                'image'         => $filefoto,
                'appr'          => '0',
                'urut'          => ($request->urut ?? '9'),
			];
        Pem_carousel::insert($insert);

		return redirect('/pemanfaatan/images')
				->with('message', 'Gambar berhasil disimpan')
				->with('msg_num', 1);
    }

    public function formapprovecarousel(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        if($request->appr == 1){
            $appr = 0;
        } else {
            $appr = 1;
        }

		Pem_carousel::where('ids', $request->ids)
						->update([
							'appr' => $appr,
						]);

		return redirect('/pemanfaatan/images')
				->with('message', 'Approval gambar berhasil')
				->with('msg_num', 1);
    }

    public function formdeletecarousel(Request $request)
    {
        Pem_carousel::where('ids', $request->ids)
						->update([
							'sts' => 0,
						]);

		return redirect('/pemanfaatan/images')
				->with('message', 'Approval gambar berhasil')
				->with('msg_num', 1);
    }
}
