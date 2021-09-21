<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use App\Sec_access;

trait SessionCheckTraits
{
	public function checkSessionTime()
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		if (Auth::check() == FALSE) {
			redirect('login')->send()->with([
                        'message' => 'Session was expired. Please try again',
                        'message-type' => 'danger']);
		} 
	}

	public function checkAccess($idgroup, $idtop)
	{
		$access = Sec_access::
					where('idgroup', $idgroup)
					->where('idtop', $idtop)
					->first();

		if ($access['zviw'] == 'y') {
			return $access;
		} else {
			abort(403, 'Unauthorized action.');
		}
	}
}
