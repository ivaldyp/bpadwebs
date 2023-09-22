<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Contenttb as Content_tb;
use App\Glo_kategori;
use App\Help;
use App\Produk_aset;
use App\Setup_tb;
use App\Fr_disposisi;
use App\Glo_org_unitkerja;

session_start();

class LandingController extends Controller
{
	public function feedback(Request $request)
	{    

		$link = explode("portal", $_SERVER['HTTP_REFERER']);
		$insert_help = [
				// GOLONGAN
				'tanggal' => date('Y-m-d H:i:s'),
				'isi' => $request->isi,
				'sender' => $request->sender,
				'read' => 0,
			];

		Help::insert($insert_help);

		return redirect($link[1]);

		// $subject = 'Saran dan Masukan';
		// $body = 'Pengirim: ' . $request->sender . '<br><br>';
		// $body = $body . $request->body;

		// var_dump($request->sender);
		// var_dump($request->body);
		// // Import PHPMailer classes into the global namespace
		// // These must be at the top of your script, not inside a function

		// // Load Composer's autoloader
		// // require '../vendor/autoload.php';

		// // Instantiation and passing `true` enables exceptions
		// $mail = new PHPMailer(true);

		// try {
		// 	//Server settings
		// 	$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
		// 	$mail->isSMTP();                                            // Send using SMTP
		// 	$mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
		// 	$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
		// 	$mail->Username   = 'bpad.masukan@gmail.com';                     // SMTP username
		// 	$mail->Password   = 'bpad_dia';                               // SMTP password
		// 	$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
		// 	$mail->Port       = 587;
		// 	// $mail->SMTPSecure = 'ssl';         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
		// 	// $mail->Port       = '465';                                    // TCP port to connect to

		// 	//Recipients
		// 	$mail->setFrom('info@example.com', 'Pengunjung BPAD');
		// 	// $mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
		// 	$mail->addAddress('Asetbpad@gmail.com');               // Name is optional
		// 	// $mail->addReplyTo('info@example.com', 'Information');
		// 	// $mail->addCC('cc@example.com');
		// 	// $mail->addBCC('bcc@example.com');

		// 	// Attachments
		// 	// $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
		// 	// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

		// 	// Content
		// 	$mail->isHTML(true);                                  // Set email format to HTML
		// 	$mail->Subject = $subject;
		// 	$mail->Body    = $body;
		// 	// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

		// 	$mail->send();
		// 	return redirect()->action('HomeController@index');
		// 	// echo 'Message has been sent';
		// } catch (Exception $e) {
		// 	echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		// }
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function kkrekon(Request $request)
	{
		return view('kkrekon');
	}

	public function kertaskerja(Request $request)
	{
		$segment = collect(request()->segments())->last();
		return view('kertaskerja')
				->with('area', $segment);
	}

	public function index()
	{
		// if (PHP_SESSION_ACTIVE) {
		//     session_destroy();
		// }

		$info_id = Glo_kategori::
						where('kode_kat', 'INF')
						->where('sts', 1)
						->first();

		$infos = Content_tb::
					where('idkat', $info_id['ids'])
					->where('appr', 'Y')
					->where('sts', 1)
					->orderBy('tanggal', 'desc')
					->take(5)
					->get();

		$lelang_id = Glo_kategori::
						where('kode_kat', 'LEL')
						->where('sts', 1)
						->first();

		$lelang = Content_tb::
					where('idkat', $lelang_id['ids'])
					->where('appr', 'Y')
					->where('tipe', 'H,')
					->where('sts', 1)
					->orderBy('tanggal', 'desc')
					->first();

		$hot_content = Content_tb::
					where('idkat', 1)
					->where('appr', 'Y')
					->where('suspend', '')
					->where('sts', 1)
					->where('tipe', 'H,')
					->orderBy('tanggal', 'desc')
					->take(4)
					->get();

		$excludeid = "(";
		for ($i=0; $i < count($hot_content); $i++) { 
		 	$excludeid .= $hot_content[$i]['ids'];
			if ($i != (count($hot_content) - 1)){
				$excludeid .= ",";
			}
		} 
		$excludeid .= ")";

		$normal_content = DB::select( DB::raw("  
					SELECT TOP (4) * 
					From bpadcmsfake.dbo.Content_tb
					where idkat = 1
					and appr = 'Y'
					and suspend = ''
					and sts = 1
					and ids not in $excludeid
					order by tanggal desc") );
		$normal_content = json_decode(json_encode($normal_content), true);

		$photo_content = Content_tb::
					where('idkat', 5)
					->where('appr', 'Y')
					->where('suspend', '')
					->where('sts', 1)
					->orderBy('tgl', 'desc')
					->take(4)
					->get();

		$produk_content = Produk_aset::
						orderBy('ids', 'asc')
						->get();

		return view('index')
				->with('hot_content', $hot_content)
				->with('normal_content', $normal_content)
				->with('photo_content', $photo_content)
				->with('produk_content', $produk_content)
				->with('lelang', $lelang)
				->with('infos', $infos);
	}

	public function logout()
	{
		unset($_SESSION['user_data']);
		Auth::logout();
		return redirect('/');
	}

	public function Ceksurat(Request $request)
	{
		$idsurat = $request->ceksurat;
		$query = null;
		$treedisp = null;
		if(isset($idsurat)){
			$query = DB::select( DB::raw("SELECT *, bpaddtfake.dbo.fr_disposisi.tgl as disptgl
											from bpaddtfake.dbo.fr_disposisi
											join bpaddtfake.dbo.glo_disposisi_kode on bpaddtfake.dbo.glo_disposisi_kode.kd_jnssurat = bpaddtfake.dbo.fr_disposisi.kode_disposisi
											where (kd_surat like '$idsurat' or no_form like '$idsurat') and bpaddtfake.dbo.fr_disposisi.sts = 1
											order by ids") );
			$query = json_decode(json_encode($query), true);

			if(count($query) >= 1){
				$treedisp = '<tr>
								<td>
									<i class="fa fa-book"></i> <span>'.$query[0]['no_form'].' ['.date('d-M-Y', strtotime($query[0]['disptgl'])).']</span> <br>
									<span class="text-muted">Kode: '.$query[0]['kode_disposisi'].'</span> | <span class="text-muted"> Nomor: '.$query[0]['no_surat'].'</span><br>
								</td>
							</tr>';

				$treedisp .= $this->display_disposisi($query[0]['no_form'], $query[0]['ids']);
			}
			
		}
		
		return view('ceksurat')
				->with('idsurat', $idsurat)
				->with('treedisp', $treedisp)
				->with('query', $query);
	}

	public function display_disposisi($no_form, $idtop, $level = 0)
	{
		// $query = Fr_disposisi::
		// 			leftJoin('bpaddtfake.dbo.emp_data as emp1', 'emp1.id_emp', '=', 'bpaddtfake.dbo.fr_disposisi.to_pm')
		// 			->where('no_form', $no_form)
		// 			->where('idtop', $idtop)
		// 			->orderBy('ids')
		// 			->get();

		$query = DB::select( DB::raw("SELECT * , bpaddtfake.dbo.fr_disposisi.tgl as disptgl
					from bpaddtfake.dbo.fr_disposisi
					left join bpaddtfake.dbo.emp_data on bpaddtfake.dbo.emp_data.id_emp = bpaddtfake.dbo.fr_disposisi.to_pm
					where no_form = '$no_form' and idtop = '$idtop'
					and bpaddtfake.dbo.fr_disposisi.sts = 1
					order by ids
					") );
		$query = json_decode(json_encode($query), true);

		$result = '';

		if (count($query) > 0) {
			foreach ($query as $log) {
				$padding = ($level * 20);
				$result .= '<tr >
								<td style="padding-left:'.$padding.'px; padding-top:10px">
									<i class="fa fa-user"></i> <span>'.$log['nrk_emp'].' '.ucwords(strtolower($log['nm_emp'])).' ['.date('d-M-Y', strtotime($log['disptgl'])).']</span> 
									'.(($log['child'] == 0 && $log['rd'] == 'S') ? "<i data-toggle='tooltip' title='Sudah ditindaklanjut!' class='fa fa-check' style='color: blue'></i>" : '').'
									'.(($log['child'] == 0 && $log['rd'] != 'S') ? "<i data-toggle='tooltip' title='Belum ditindaklanjut!' class='fa fa-close' style='color: red'></i>" : '').'
									<br> 
									<span class="text-muted"> Penanganan: <b>'. ($log['penanganan_final'] ? $log['penanganan_final'] : ($log['penanganan_final'] ? $log['penanganan_final'] : ($log['penanganan'] ? $log['penanganan'] : '-' ) )) .'</b></span>
									<br>
								</td>
							</tr>';

				if ($log['child'] == 1) {
					$result .= $this->display_disposisi($no_form, $log['ids'], $level+1);
				}
			}
		}
		return $result;
	}

    public function visimisi(Request $request)
    {
        return view('pages.landingprofil.visimisi');
    }

    public function tupoksi(Request $request)
    {
        return view('pages.landingprofil.tupoksi');
    }

    public function struktur(Request $request)
    {
        return view('pages.landingprofil.struktur');
    }

    public function profilpejabat(Request $request)
    {
        $es2 = DB::select( DB::raw("  
        SELECT id_emp, foto, nm_emp, tbjab.idunit, tbunit.child, tbunit.kd_unit, tbunit.notes, tbunit.sao, tbunit.nm_unit, tbunit.nm_bidang, a.foto_pejabat from bpaddtfake.dbo.emp_data as a
        CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
        CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
        ,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
        and idunit like '01%' AND LEN(idunit) = 2 AND ked_emp = 'AKTIF'
        ORDER BY idunit ASC, idjab ASC") )[0];
        $es2 = json_decode(json_encode($es2), true);

        $es3 = DB::select( DB::raw("  
        SELECT id_emp, foto, nm_emp, tbjab.idunit, tbunit.child, tbunit.kd_unit, tbunit.notes, tbunit.sao, tbunit.nm_unit, tbunit.nm_bidang, a.foto_pejabat from bpaddtfake.dbo.emp_data as a
        CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
        CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
        ,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
        and idunit like '01%' AND LEN(idunit) = 6 AND ked_emp = 'AKTIF'
        ORDER BY idunit ASC, idjab ASC") );
        $es3 = json_decode(json_encode($es3), true);

        $es4 = DB::select( DB::raw("  
        SELECT id_emp, foto, nm_emp, tbjab.idunit, tbunit.child, tbunit.kd_unit, tbunit.notes, tbunit.sao, tbunit.nm_unit, tbunit.nm_bidang, a.foto_pejabat from bpaddtfake.dbo.emp_data as a
        CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
        CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
        ,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
        and idunit like '01%' AND LEN(idunit) = 8 AND ked_emp = 'AKTIF'
        ORDER BY idunit ASC, idjab ASC") );
        $es4 = json_decode(json_encode($es4), true);

        return view('pages.landingprofil.profilpejabat')
                ->with('es2', $es2)
                ->with('es3', $es3)
                ->with('es4', $es4);
    }

    public function profil(Request $request)
    {
        return view('pages.landingprofil.profil');
    }

    public function gethistorypejabat(Request $request)
    {
        $kd_unit = $request->kd_unit;

        $result = DB::select( DB::raw("  
        SELECT id_emp, foto, nm_emp, tbjab.idunit, tbunit.child, tbunit.kd_unit, tbunit.notes, tbunit.sao, tbunit.nm_unit, tbunit.nm_bidang, tbunit.unit_history, a.sejarah_pejabat
        from bpaddtfake.dbo.emp_data as a
        CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
        CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
        ,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
        and a.id_emp like '$kd_unit' AND tbunit.sts = '1' AND ked_emp = 'AKTIF'
        ORDER BY idunit ASC, idjab ASC") )[0];
        $result = json_decode(json_encode($result), true);

        return $result;
    }
}