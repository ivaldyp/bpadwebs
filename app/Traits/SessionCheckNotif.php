<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use App\Emp_notif;
use App\Sec_access;
use App\Sec_logins;

trait SessionCheckNotif
{
	public function checknotif($idemp)
	{
		// if (Auth::user()->id_emp) {
		// 	$idemp = Auth::user()->id_emp;
		// } else {
		// 	$idemp = Auth::user()->usname;
		// }

		// var_dump($idemp);
		// die;

		$notifs = Emp_notif::
						where('id_emp', $idemp)
						->where('sts', 1)
						->where('rd', 'N')
						->orderBy('tgl', 'desc')
						->take(5)
						->get();
		return $notifs;
	}
}
