<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Traits\SessionCheckTraits;

use App\Emp_data;
use App\Sec_access;
use App\Sec_menu;
use App\Sec_logins;

session_start();

class SecurityController extends Controller
{
	use SessionCheckTraits;

	// // // GRUP USER // // // 

	public function display_roles($query, $idgroup, $access, $parent, $level = 0)
    {
        $query = Sec_menu::
                join('sec_access', 'sec_access.idtop', '=', 'sec_menu.ids')
                ->where('sec_menu.tipe', 'l')
                ->whereRaw('LEN(sec_menu.urut) = 1')
                ->where('sec_access.idgroup', $idgroup)
                // ->where('sec_access.zviw', 'y')
                ->where('sec_menu.sao', $parent)
                ->orderByRaw('CONVERT(INT, sec_menu.sao)')
                ->orderBy('sec_menu.urut')
                ->get();

        $result = '';

        if (count($query) > 0) {
            foreach ($query as $menu) {
            	$padding = ($level * 20) + 8;
                $result .= '<tr>
                				<td class="col-md-1">'.$level.'</td>
		        				<td style="padding-left:'.$padding.'px; '.(($level == 0) ? 'font-weight: bold;"' : '').'">('.$menu['ids'].') '.$menu['desk'].'</td>
		        				<td>'.(($menu['zviw'] == 'y')? '<i style="color:green;" class="fa fa-check"></i>' : '<i style="color:red;" class="fa fa-times"></i>').'</td>
		        				<td>'.(($menu['zadd'] == 'y')? '<i style="color:green;" class="fa fa-check"></i>' : '<i style="color:red;" class="fa fa-times"></i>').'</td>
		        				<td>'.(($menu['zupd'] == 'y')? '<i style="color:green;" class="fa fa-check"></i>' : '<i style="color:red;" class="fa fa-times"></i>').'</td>
		        				<td>'.(($menu['zdel'] == 'y')? '<i style="color:green;" class="fa fa-check"></i>' : '<i style="color:red;" class="fa fa-times"></i>').'</td>
		        				<td>'.(($menu['zapr'] == 'y')? '<i style="color:green;" class="fa fa-check"></i>' : '<i style="color:red;" class="fa fa-times"></i>').'</td>
		        				<td>'.$menu['zket'].'</td>
		        				'.(($access['zupd'] == 'y' || $access['zdel'] == 'y') ? 

		        				'<td>
		        					'.(($access['zupd'] == 'y') ? 
			        					'<button type="button" class="btn btn-info btn-update" data-toggle="modal" data-target="#modal-update" data-ids="'.$menu['ids'].'" data-idgroup="'.$menu['idgroup'].'" data-zviw="'.$menu['zviw'].'" data-zadd="'.$menu['zadd'].'" data-zupd="'.$menu['zupd'].'" data-zdel="'.$menu['zdel'].'" data-zapr="'.$menu['zapr'].'" data-zket="'.$menu['zket'].'"><i class="fa fa-edit"></i></button>'
		        					: '').'
		        				</td>'

		        				: '' ).'
		        				
		        			</tr>';

                if ($menu['child'] == 1) {
                    $result .= $this->display_roles($query, $idgroup, $access, $menu['ids'], $level+1);
                }
            }
        }
        return $result;
    }

	public function grupall()
	{
		$this->checkSessionTime();
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], 4);

		$groups = Sec_access::
					distinct('idgroup')
					->orderBy('idgroup')
					->get('idgroup');

		return view('pages.bpadsecurity.grupuser')
				->with('access', $access)
				->with('groups', $groups);
	}

	public function grupubah(Request $request)
	{
		$this->checkSessionTime();
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], 4);

		$groups = Sec_access::
					distinct('idgroup')
					->get('idgroup');

		$all_menu = [];

        $menus = $this->display_roles($all_menu, $request->name, $access, 0);


       	$pagename = $request->name;

		return view('pages.bpadsecurity.ubahgrup')
				->with('access', $access)
				->with('pagename', $pagename)
				->with('menus', $menus)
				->with('groups', $groups);
	}

	public function forminsertgrup(Request $request)
	{
		$this->checkSessionTime();
		// $access = $this->checkAccess($_SESSION['user_data']['idgroup'], 4);

		$namecheck = Sec_access::
						where('idgroup', $request->idgroup)
						->get();

		if (count($namecheck) > 0) {
			return redirect('/security/group user')
					->with('message', 'Grup user '.$request->idgroup.' sudah ada di database. Harap ganti nama.')
					->with('msg_num', 2);
		}

		$query = Sec_access::
					where('idgroup', 'SUPERUSER')
					->orderBy('idtop')
					->get();

		$result = array();
		foreach ($query as $key => $data) {
			array_push($result, [
				'idgroup' => strtoupper($request->idgroup),
				'idtop' => $data['idtop'],
			]);
		}

		if (Sec_access::insert($result)) {
			return redirect('/security/group user')
					->with('message', 'Grup user '.$request->idgroup.' berhasil ditambah')
					->with('msg_num', 1);
		} else {
			return redirect('/security/group user')
					->with('message', 'Grup user '.$request->idgroup.' gagal ditambah')
					->with('msg_num', 2);
		}	
	}

	public function formupdategrup(Request $request)
	{
		$this->checkSessionTime();
		// $access = $this->checkAccess($_SESSION['user_data']['idgroup'], 4);

		!(isset($request->zviw)) ? $zviw = '' : $zviw = 'y';
		!(isset($request->zadd)) ? $zadd = '' : $zadd = 'y';
		!(isset($request->zupd)) ? $zupd = '' : $zupd = 'y';
		!(isset($request->zdel)) ? $zdel = '' : $zdel = 'y';
		!(isset($request->zapr)) ? $zapr = '' : $zapr = 'y';

		$query = Sec_access::
					where('idtop', $request->idtop)
					->where('idgroup', $request->idgroup)
					->update([
						'zviw' => $zviw,
						'zadd' => $zadd,
						'zupd' => $zupd,
						'zdel' => $zdel,
						'zapr' => $zapr,
					]);

		return redirect('/security/group user/ubah?name='.$request->idgroup)
				->with('message', 'Grup user '.$request->idgroup.' berhasil diubah')
				->with('msg_num', 1);
	}

	public function formdeletegrup(Request $request)
	{
		$this->checkSessionTime();
		// $access = $this->checkAccess($_SESSION['user_data']['idgroup'], 4);

		$cari1 = Sec_logins::
					where('idgroup', $request->idgroup)
					->count();

		$cari2 = Emp_data::
					where('idgroup', $request->idgroup)
					->count();

		if ($cari1 > 0 || $cari2 > 0) {
			return redirect('/security/group user')
				->with('message', 'Grup user '.$request->idgroup.' tidak dapat dihapus karena terdapat user yang merupakan grup user tersebut')
				->with('msg_num', 2);
		}

		$query = Sec_access::
					where('idgroup', $request->idgroup)
					->delete();

		return redirect('/security/group user')
				->with('message', 'Grup user '.$request->idgroup.' berhasil dihapus')
				->with('msg_num', 1);
	}

	// ----------------GRUP USER----------------- //

	// ------------------------------------------ //

	// ---------------TAMBAH USER---------------- // 

	public function tambahuser()
	{
		$this->checkSessionTime();
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], 5);

		$idgroup = Sec_access::
					distinct('idgroup')
					->orderBy('idgroup', 'asc')
					->get('idgroup');

		return view('pages.bpadsecurity.tambahuser')
				->with('access', $access)
				->with('idgroup', $idgroup);
	}

	public function forminsertuser(Request $request)
	{
		$this->checkSessionTime();
		// $access = $this->checkAccess($_SESSION['user_data']['idgroup'], 5);

		$data = [
				'usname' 		=> $request->username,
				'passmd5' 		=> md5($request->password),
				'idgroup' 		=> $request->idgroup,
				'nama_user'		 => $request->name,
				'deskripsi_user' => $request->deskripsi_user,
				'email_user'	=> $request->email_user,
				'createdate'	=> date("Y-m-d H:i:s")
			];

		if (Sec_logins::insert($data)) {
			return redirect('/security/manage user')
					->with('message', 'User '.$request->username.' berhasil ditambah')
					->with('msg_num', 1);
		} else {
			return redirect('/security/manage user')
					->with('message', 'User '.$request->username.' gagal ditambah')
					->with('msg_num', 2);
		}	
	}

	// ---------------TAMBAH USER---------------- // 

	// ------------------------------------------ //

	// ---------------MANAGE USER---------------- // 

	public function manageuser()
	{
		$this->checkSessionTime();
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], 6);

		$users = Sec_logins::
					orderBy('idgroup')
					->orderBy('usname')
					->get();	

		$idgroup = Sec_access::
					distinct('idgroup')
					->orderBy('idgroup', 'asc')
					->get('idgroup');

		return view('pages.bpadsecurity.manageuser')
				->with('access', $access)
				->with('idgroup', $idgroup)
				->with('users', $users);
	}

	public function formupdateuser(Request $request)
	{
		$this->checkSessionTime();
		// $access = $this->checkAccess($_SESSION['user_data']['idgroup'], 6);

		$query = Sec_logins::
					where('ids', $request->ids)
					->update([
						'usname' => $request->usname,
						'nama_user' => $request->nama_user,
						'deskripsi_user' => $request->deskripsi_user,
						'email_user' => $request->email_user,
						'idgroup' => $request->idgroup,
					]);

		return redirect('/security/manage user')
					->with('message', 'User '.$request->usname.' berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeleteuser(Request $request)
	{
		$this->checkSessionTime();
		// $access = $this->checkAccess($_SESSION['user_data']['idgroup'], 6);

		$query = Sec_logins::
					where('usname', $request->usname)
					->delete();

		return redirect('/security/manage user')
					->with('message', 'User '.$request->usname.' berhasil dihapus')
					->with('msg_num', 1);
	}

	// ---------------MANAGE USER---------------- // 

	// ------------------------------------------ //

	// ---------------MANAGE SKPD---------------- // 

	// ---------------MANAGE SKPD---------------- // 
}