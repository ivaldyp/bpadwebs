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

class HiddenController extends Controller
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

    public function index(Request $request)
    {
        return view('pages.bpadhidden.index');
    }
}
