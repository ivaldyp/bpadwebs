<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models76\App_faq;

use App\Traits\SessionCheckTraits;
use App\Traits\SessionCheckNotif;

session_start();

class FaqController extends Controller
{
    use SessionCheckTraits;
	use SessionCheckNotif;
    
    // public function __construct()
	// {
	// 	$this->middleware('auth');
	// }

    public function index($app)
    {
        $faqs = App_faq::where('app_name', $app)->orderBy('questions')->get();

        $applications = App_faq::distinct('app_name')->orderBy('app_name')->get('app_name');

        return view('pages.bpaddtfaq.faq')
				->with('faqs', $faqs)
                ->with('applications', $applications)
                ->with('app', $app);
    }

    public function setup(Request $request)
    {
        if(count($_SESSION) == 0) {
			return redirect('home');
		}
        
        $apps = App_faq::distinct('app_name')->orderBy('app_name')->get('app_name');
        $appnow = $request->appnow ?? $apps[0]['app_name'];

        if($appnow) {
            $faqs = App_faq::where('app_name', $appnow)->orderBy('questions')->get();
        } else {
            $faqs = App_faq::where('app_name', $apps[0]['app_name'])->orderBy('questions')->get();
        }

        return view('pages.bpaddtfaq.setup')
                ->with('faqs', $faqs)
                ->with('apps', $apps)
                ->with('appnow', $appnow);
    }

    public function insert(Request $request)
    {
        if(count($_SESSION) == 0) {
			return redirect('home');
		}

        $insertfaq = [
			'app_name' => $request->appname,
            'questions' => $request->questions,
            'answers' => htmlentities($request->answers),
		];

		App_faq::insert($insertfaq);

		return redirect('/faq/setup?appnow='.$request->appname)
				->with('message', 'Berhasil menambahkan FAQ baru tentang aplikasi '.$request->appname)
				->with('msg_num', 1);
    }

    public function update(Request $request)
    {
        if(count($_SESSION) == 0) {
			return redirect('home');
		}
        
        $ids = $request->ids;
        $appnow = $request->appnow;
        $questions = $request->questions;
        $answers = $request->answers;

        App_faq::where('ids', $ids)
        ->update([
            'questions' => $questions,
            'answers' => $answers,
        ]);

		return redirect('/faq/setup?appnow='.$appnow)
				->with('message', "Berhasil mengubah FAQ baru tentang aplikasi ".$request->appnow)
				->with('msg_num', 1);
    }

    public function delete(Request $request)
    {
        if(count($_SESSION) == 0) {
			return redirect('home');
		}

        App_faq::
        where('ids', $request->ids)
        ->delete();

        // return redirect('/faq/setup?appnow='.$request->appnow)
		// 		->with('message', "Berhasil menghapus FAQ aplikasi ".$request->appnow." dengan IDS: ".$request->ids)
		// 		->with('msg_num', 1);

        return 1;
    }
}
