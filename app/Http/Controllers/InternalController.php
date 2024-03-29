<?php

namespace App\Http\Controllers;

// require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Traits\SessionCheckTraits;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use App\Agenda_tb;
use App\Berita_tb;
use App\Emp_data;
use App\Glo_arsip_kategori;
use App\Glo_tujuan_kehadiran;
use App\Glo_profile_skpd;
use App\Glo_org_unitkerja;
use App\Help;
use App\Internal_arsip;
use App\Internal_info;
use App\Internal_kehadiran;
use App\Internal_responsehadir;
use App\Sec_menu;

use App\Models11\Dta_kaban_event;
use App\Models11\Dta_kaban_event_qr;
use App\Models11\Dta_kaban_event_save;
use App\Models11\Dta_kaban_event_staging;

session_start();

class InternalController extends Controller
{
	use SessionCheckTraits;

	public function __construct()
	{
		$this->middleware('auth');
	}

	public function checksession() {
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
	}

	// ========== <AGENDA> ========== //
	
	public function agenda()
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		$agendas = Agenda_tb::limit(200)
					->orderBy('ids', 'desc')
					->get();

		return view('pages.bpadinternal.agenda')
				->with('access', $access)
				->with('agendas', $agendas);
	}

	public function agendatambah()
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		return view('pages.bpadinternal.agendatambah');
	}

	public function agendaubah(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		$agenda = Agenda_tb::
					where('ids', $request->ids)
					->first();

		return view('pages.bpadinternal.agendaubah')
				->with('ids', $request->ids)
				->with('agenda', $agenda);
	}

	public function formappragenda(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		Agenda_tb::where('ids', $request->ids)
			->update([
				'appr' => $request->appr,
			]);

		if ($request->appr == 'Y') {
			$message = 'Berhasil menyetujui agenda';
		} else {
			$message = 'Berhasil membatalkan persetujuan agenda';
		}

		return redirect('/internal/agenda')
				->with('message', $message)
				->with('msg_num', 1);
	}

	public function forminsertagenda(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		$fileagenda = '';

		if (isset($request->dfile)) {
			$file = $request->dfile;

			if ($file->getSize() > 5500000) {
				return redirect('/internal/agenda tambah')->with('message', 'Ukuran file terlalu besar (Maksimal 5MB)');     
			} 

			$fileagenda .= $file->getClientOriginalName();

			$tujuan_upload = config('app.savefileagenda');
			$file->move($tujuan_upload, $fileagenda);
		}
			
		if (!(isset($fileagenda))) {
			$fileagenda = '';
		}

		$inputipe = '';
		if ($request->tipe) {
			foreach ($request->tipe as $tipe) {
				$inputipe .= $tipe . ',';
			}
		}

		$insertagenda = [
			'sts' => 1,
			'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
			'tgl'       => date('Y-m-d H:i:s'),
			'ip'        => '',
			'logbuat'   => '',
			'kd_skpd' => '1.20.512',
			'dtanggal' => date('Y-m-d H:i:s',strtotime(str_replace('/', '-', $request->dtanggal))),
			'ddesk' => ($request->ddesk ? $request->ddesk : ''),
			'tipe' => $inputipe,
			'dfile' => $fileagenda,
			'an' => $request->an,
			'appr' => 'N',
			'usrinput' => $request->usrinput,
			'thits' => 0,
		];

		Agenda_tb::insert($insertagenda);

		return redirect('/internal/agenda')
				->with('message', 'Agenda baru berhasil dibuat')
				->with('msg_num', 1);
	}

	public function formupdateagenda(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		$fileagenda = '';

		if (isset($request->dfile)) {
			$file = $request->dfile;

			if ($file->getSize() > 5500000) {
				return redirect('/internal/agenda tambah')->with('message', 'Ukuran file terlalu besar (Maksimal 5MB)');     
			} 

			$fileagenda .= $file->getClientOriginalName();

			$tujuan_upload = config('app.savefileagenda');
			$file->move($tujuan_upload, $fileagenda);
		}
			
		if (!(isset($fileagenda))) {
			$fileagenda = '';
		}

		$inputipe = '';
		if ($request->tipe) {
			foreach ($request->tipe as $tipe) {
				$inputipe .= $tipe . ',';
			}
		}

		Agenda_tb::where('ids', $request->ids)
					->update([
						'dtanggal' => date('Y-m-d H:i:s',strtotime(str_replace('/', '-', $request->dtanggal))),
						'ddesk' => ($request->ddesk ? $request->ddesk : ''),
						'tipe' => $inputipe,
					]);

		if($fileagenda != '') {
			Agenda_tb::where('ids', $request->ids)
			->update([
				'dfile' => $fileagenda,
			]);
		}

		return redirect('/internal/agenda')
				->with('message', 'Agenda berhasil diubah')
				->with('msg_num', 1);
	}

	public function formdeleteagenda(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		Agenda_tb::
				where('ids', $request->ids)
				->delete();

		$filepath = '';
		$filepath .= config('app.savefileagenda');
		$filepath .= '/' . $request->dfile;

		if ($request->dfile) {
			unlink($filepath);
		}

		return redirect('/internal/agenda')
					->with('message', 'Agenda berhasil dihapus')
					->with('msg_num', 1);
	}

	// ========== </AGENDA> ========== //

	// ========== <BERITA> ========== //

	public function berita()
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		$beritas = Berita_tb::limit(200)
					->orderBy('ids', 'desc')
					->get();

		return view('pages.bpadinternal.berita')
				->with('access', $access)
				->with('beritas', $beritas);
	}

	public function beritatambah()
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		return view('pages.bpadinternal.beritatambah');
	}

	public function beritaubah(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		$berita = Berita_tb::
					where('ids', $request->ids)
					->first();

		return view('pages.bpadinternal.beritaubah')
				->with('ids', $request->ids)
				->with('berita', $berita);
	}

	public function formapprberita(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		Berita_tb::where('ids', $request->ids)
			->update([
				'appr' => $request->appr,
			]);

		if ($request->appr == 'Y') {
			$message = 'Berhasil menyetujui berita';
		} else {
			$message = 'Berhasil membatalkan persetujuan berita';
		}

		return redirect('/internal/berita')
				->with('message', $message)
				->with('msg_num', 1);
	}

	public function forminsertberita(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		if (is_null($request->isi)) {
			$isi = '';
		} else {
			$isi = $request->isi;
		}

		$insertberita = [
			'sts' => 1,
			'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
			'tgl'       => date('Y-m-d H:i:s'),
			'ip'        => '',
			'logbuat'   => '',
			'kd_skpd' => '1.20.512',
			'tanggal' => date('Y-m-d H:i:s',strtotime(str_replace('/', '-', $request->tanggal))),
			'an' => $request->an,
			'isi' => htmlentities($isi),
			'tipe' => $request->tipe,
			'appr' => 'N',
			'usrinput' => $request->usrinput,
		];

		Berita_tb::insert($insertberita);

		return redirect('/internal/berita')
				->with('message', 'Berita baru berhasil dibuat')
				->with('msg_num', 1);
	}

	public function formupdateberita(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		Berita_tb::where('ids', $request->ids)
					->update([
						'tanggal' => date('Y-m-d H:i:s',strtotime(str_replace('/', '-', $request->tanggal))),
						'isi' => htmlentities($request->isi),
						'tipe' => $request->tipe,
					]);

		return redirect('/internal/berita')
				->with('message', 'Berita berhasil diubah')
				->with('msg_num', 1);
	}

	public function formdeleteberita(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		Berita_tb::
				where('ids', $request->ids)
				->delete();

		return redirect('/internal/berita')
					->with('message', 'Berita berhasil dihapus')
					->with('msg_num', 1);
	}

	// ========== </BERITA> ========== //

	// ========== <SARAN> ========== //
	
	public function saran(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		if ($request->yearnow) {
			$yearnow = (int)$request->yearnow;
		} else {
			$yearnow = (int)date('Y');
		}

		$sarans = Help::
					whereRaw('YEAR(tanggal) = '.$yearnow)
					->orderBy('tanggal', 'desc')
					->get();

		return view('pages.bpadinternal.saran')
				->with('access', $access)
				->with('sarans', $sarans)
				->with('yearnow', $yearnow);
	}

	public function formapprsaran(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		if ($request->read == 1) {
			$read = 0;
		} else {
			$read = 1;
		}

		Help::where('ids', $request->ids)
					->update([
						'read' => $read,
					]);

		return redirect('/internal/saran')
				->with('message', 'Status berhasil diubah')
				->with('msg_num', 1);
	}

	public function formmailsaran(Request $request)
	{
		$subject = 'Reply';
		$body = 'Pengirim: ' . $request->sender . '<br><br>';
		$body = $body . $request->body;

		// var_dump($request->sender);
		// var_dump($request->body);
		// // Import PHPMailer classes into the global namespace
		// // These must be at the top of your script, not inside a function

		// // Load Composer's autoloader
		// // require '../vendor/autoload.php';

		// // Instantiation and passing `true` enables exceptions
		$mail = new PHPMailer(true);

		try {
			//Server settings
			$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
			$mail->isSMTP();                                            // Send using SMTP
			$mail->Host       = 'tls://smtp.gmail.com';                    // Set the SMTP server to send through
			$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
			$mail->Username   = 'bpad.masukan@gmail.com';                     // SMTP username
			$mail->Password   = 'bpad_dia';                               // SMTP password
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
			$mail->Port       = 587;
			// $mail->SMTPSecure = 'ssl';         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
			// $mail->Port       = '465';                                    // TCP port to connect to

			//Recipients
			$mail->setFrom('vivaaldy@gmail.com', 'Pengunjung BPAD');
			// $mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
			$mail->addAddress('vivaaldy@gmail.com');               // Name is optional
			// $mail->addReplyTo('info@example.com', 'Information');
			// $mail->addCC('cc@example.com');
			// $mail->addBCC('bcc@example.com');

			// Attachments
			// $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
			// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

			// Content
			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = $subject;
			$mail->Body    = $body;
			// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

			$mail->send();
			return redirect()->action('InternalController@saran');
			// echo 'Message has been sent';
		} catch (Exception $e) {
			echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}

		return redirect('/internal/saran')
				->with('message', 'saran berhasil dibuat')
				->with('msg_num', 1);
	}

	// ========== </SARAN> ========== //

	// ========== <INFO KEPEGAWAIAN> ========== //

	public function infoall(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		if ($request->searchnow) {
			$qsearchnow = $request->searchnow;
		} else {
			$qsearchnow = '';
		}

		$infos = Internal_info::
				where('info_judul', 'like', '%'.$qsearchnow.'%')
				->where('sts', '1')
				->orderBy('tgl', 'desc')
				->orderBy('tgl_mulai', 'desc')		
				->orderBy('tgl_akhir', 'desc')
				->orderBy('info_judul', 'asc')
				->limit(1000)
				->get();

		return view('pages.bpadinternal.info')
				->with('infos', $infos);
	}

	public function infotambah()
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		return view('pages.bpadinternal.infotambah');
	}

	public function infoubah(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		$infos = Internal_info::
					where('ids', $request->ids)
					->first();

		return view('pages.bpadinternal.infoubah')
				->with('ids', $request->ids)
				->with('infos', $infos);
	}

	public function forminsertinfo(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		$fileinfo = '';

		date_default_timezone_set('Asia/Jakarta');
		$insert_info = [
				// PENDIDIKAN
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'info_judul' => $request->info_judul,
				'tgl_mulai' => ($request->tgl_mulai ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_mulai))) : ''),
				'tgl_akhir' => ($request->tgl_akhir ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_akhir))) : ''),
				'info_tampil' => $request->info_tampil,
			];

		$nowid = Internal_info::insertGetId($insert_info);

		if (isset($request->fileinfo)) {
			
			$file = $request->fileinfo;

			if ($file->getSize() > 5555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file terlalu besar (Maksimal 5MB)');     
			}

			if (strtolower($file->getClientOriginalExtension()) != "png" && strtolower($file->getClientOriginalExtension()) != "jpg" && strtolower($file->getClientOriginalExtension()) != "jpeg" && strtolower($file->getClientOriginalExtension()) != "pdf") {
				return redirect('/profil/pegawai')->with('message', 'File yang diunggah harus berbentuk PDF / JPG / JPEG / PNG');     
			}

			$fileinfo .= $nowid . "_info.". $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefileinfo');
			$tujuan_upload .= "\\" . $nowid . "\\";

			if (file_exists($tujuan_upload . $fileinfo )) {
				unlink($tujuan_upload . $fileinfo);
			}

			$file->move($tujuan_upload, $fileinfo);
		}
			
		if (!(isset($fileinfo))) {
			$fileinfo = '';
		}

		if ($fileinfo != '') {
			Internal_info::where('ids', $nowid)
			->update([
				'info_file' => $fileinfo,
			]);
		}
		
		return redirect('/internal/info')
					->with('message', 'Info Kepegawaian baru berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdateinfo(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		Internal_info::where('ids', $request->ids)
					->update([
						'info_judul' => $request->info_judul,
						'tgl_mulai' => ($request->tgl_mulai ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_mulai))) : ''),
						'tgl_akhir' => ($request->tgl_akhir ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_akhir))) : ''),
						'info_tampil' => $request->info_tampil,
					]);

		$fileinfo = '';

		if (isset($request->fileinfo)) {
			
			$file = $request->fileinfo;

			if ($file->getSize() > 5555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file terlalu besar (Maksimal 5MB)');     
			}

			if (strtolower($file->getClientOriginalExtension()) != "png" && strtolower($file->getClientOriginalExtension()) != "jpg" && strtolower($file->getClientOriginalExtension()) != "jpeg" && strtolower($file->getClientOriginalExtension()) != "pdf") {
				return redirect('/profil/pegawai')->with('message', 'File yang diunggah harus berbentuk PDF / JPG / JPEG / PNG');     
			}

			$fileinfo .= $request->ids . "_info.". $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefileinfo');
			$tujuan_upload .= "\\" . $request->ids . "\\";

			if (file_exists($tujuan_upload . $fileinfo )) {
				unlink($tujuan_upload . $fileinfo);
			}

			$file->move($tujuan_upload, $fileinfo);
		}
			
		if (!(isset($fileinfo))) {
			$fileinfo = '';
		}

		if ($fileinfo != '') {
			Internal_info::where('ids', $request->ids)
			->update([
				'info_file' => $fileinfo,
			]);
		}

		return redirect('/internal/info')
				->with('message', 'Info berhasil diubah')
				->with('msg_num', 1);
	}

	public function formdeleteinfo(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		Internal_info::where('ids', $request->ids)
					->update([
						'sts' => 0,
					]);

		return redirect('/internal/info')
					->with('message', 'Info Kepegawaian berhasil dihapus')
					->with('msg_num', 1);
	}

	// ========== </INFO KEPEGAWAIAN> ========== //

	// ========== <ARSIP KEPEGAWAIAN> ========== //

	public function arsipall(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		if ($request->searchnow) {
			$qsearchnow = $request->searchnow;
		} else {
			$qsearchnow = '';
		}

		$arsips = Internal_arsip::
				where('arsip_judul', 'like', '%'.$qsearchnow.'%')
				->where('sts', '1')
				->orderBy('tgl', 'desc')
				->orderBy('arsip_judul', 'asc')
				->limit(1000)
				->get();

		return view('pages.bpadinternal.arsip')
				->with('arsips', $arsips);
	}

	public function arsiptambah()
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		$kats = Glo_arsip_kategori::
				where('sts', 1)
				->orderBy('singkatan')
				->get();

		return view('pages.bpadinternal.arsiptambah')
				->with('kats', $kats);
	}

	public function arsipubah(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		$arsips = Internal_arsip::
					where('ids', $request->ids)
					->first();

		$kats = Glo_arsip_kategori::
				where('sts', 1)
				->orderBy('singkatan')
				->get();

		return view('pages.bpadinternal.arsipubah')
				->with('ids', $request->ids)
				->with('arsips', $arsips)
				->with('kats', $kats);
	}

	public function forminsertarsip(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		$filearsip = '';

		date_default_timezone_set('Asia/Jakarta');
		$insert_arsip = [
				// PENDIDIKAN
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'arsip_judul' => $request->arsip_judul,
				'arsip_detail' => $request->arsip_detail,
				'arsip_kat' => $request->arsip_kat,
				'arsip_tampil' => $request->arsip_tampil,
			];

		$nowid = Internal_arsip::insertGetId($insert_arsip);

		if (isset($request->filearsip)) {
			
			$file = $request->filearsip;

			if ($file->getSize() > 5555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file terlalu besar (Maksimal 5MB)');     
			}

			if (strtolower($file->getClientOriginalExtension()) != "png" && strtolower($file->getClientOriginalExtension()) != "jpg" && strtolower($file->getClientOriginalExtension()) != "jpeg" && strtolower($file->getClientOriginalExtension()) != "pdf") {
				return redirect('/profil/pegawai')->with('message', 'File yang diunggah harus berbentuk PDF / JPG / JPEG / PNG');     
			}

			$filearsip .= $nowid . "_arsip.". $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefilearsip');
			$tujuan_upload .= "\\" . $nowid . "\\";

			if (file_exists($tujuan_upload . $filearsip )) {
				unlink($tujuan_upload . $filearsip);
			}

			$file->move($tujuan_upload, $filearsip);
		}
			
		if (!(isset($filearsip))) {
			$filearsip = '';
		}

		if ($filearsip != '') {
			Internal_arsip::where('ids', $nowid)
			->update([
				'arsip_file' => $filearsip,
			]);
		}
		
		return redirect('/internal/arsip')
					->with('message', 'Arsip baru berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdatearsip(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		Internal_arsip::where('ids', $request->ids)
					->update([
						'arsip_judul' => $request->arsip_judul,
						'arsip_detail' => $request->arsip_detail,
						'arsip_kat' => $request->arsip_kat,
						'arsip_tampil' => $request->arsip_tampil,
					]);

		$filearsip = '';

		if (isset($request->filearsip)) {
			
			$file = $request->filearsip;

			if ($file->getSize() > 5555555) {
				return redirect('/profil/pegawai')->with('message', 'Ukuran file terlalu besar (Maksimal 5MB)');     
			}

			if (strtolower($file->getClientOriginalExtension()) != "png" && strtolower($file->getClientOriginalExtension()) != "jpg" && strtolower($file->getClientOriginalExtension()) != "jpeg" && strtolower($file->getClientOriginalExtension()) != "pdf") {
				return redirect('/profil/pegawai')->with('message', 'File yang diunggah harus berbentuk PDF / JPG / JPEG / PNG');     
			}

			$filearsip .= $request->ids . "_arsip.". $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefilearsip');
			$tujuan_upload .= "\\" . $request->ids . "\\";

			if (file_exists($tujuan_upload . $filearsip )) {
				unlink($tujuan_upload . $filearsip);
			}

			$file->move($tujuan_upload, $filearsip);
		}
			
		if (!(isset($filearsip))) {
			$filearsip = '';
		}

		if ($filearsip != '') {
			Internal_arsip::where('ids', $request->ids)
			->update([
				'arsip_file' => $filearsip,
			]);
		}

		return redirect('/internal/arsip')
				->with('message', 'Arsip berhasil diubah')
				->with('msg_num', 1);
	}

	public function formdeletearsip(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		Internal_arsip::where('ids', $request->ids)
					->update([
						'sts' => 0,
					]);

		return redirect('/internal/arsip')
					->with('message', 'Arsip berhasil dihapus')
					->with('msg_num', 1);
	}

	// ========== </ARSIP KEPEGAWAIAN> ========== //

	// ========== <FORM KEHADIRAN> ========== //

	public function kehadiranall(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		if ($request->searchnow) {
			$qsearchnow = $request->searchnow;
		} else {
			$qsearchnow = '';
		}

		$kehadirans = Internal_kehadiran::
					join('bpaddtfake.dbo.glo_tujuan_kehadiran', 'bpaddtfake.dbo.glo_tujuan_kehadiran.ids', '=', 'bpaddtfake.dbo.internal_kehadiran.tujuan_id')
					->where('bpaddtfake.dbo.internal_kehadiran.sts', '1')
					->where('judul', 'like', '%'.$qsearchnow.'%')
					->orderBy('tgl_mulai', 'desc')
					->get(['judul', 'deskripsi', 'tgl_mulai', 'tgl_end', 'tujuan_id', 'nm_tujuan', 'ket_tujuan', 'no_form', 'bpaddtfake.dbo.internal_kehadiran.ids']);

		return view('pages.bpadkehadiran.kehadiranlist')
				->with('kehadirans', $kehadirans);
	}

	public function kehadirantambah(Request $request)
	{
		if(count($_SESSION) == 0) {
			return redirect('home');
		}
		//$this->checkSessionTime();

		$maxnoform = DB::select( DB::raw("SELECT max(no_form) as maks
										  FROM [bpaddtfake].[dbo].[internal_kehadiran]
										  --where sts = 1
										  ") );
		$maxnoform = json_decode(json_encode($maxnoform), true);

		if (is_null($maxnoform[0]['maks'])) {
			$maxnoform = '1.20.512.10'.'100001';
		} else {
			$splitmaxform = explode(".", $maxnoform[0]['maks']);
			$maxnoform = $splitmaxform[0] . '.' . $splitmaxform[1] . '.' . $splitmaxform[2] . '.10' . substr(($splitmaxform[3]+1), -6);
		}

		$kats = Glo_tujuan_kehadiran::
				where('sts', '<>', 0)
				->orderBy('ids')
				->get();

		return view('pages.bpadkehadiran.kehadirantambah')
				->with('maxnoform', $maxnoform)
				->with('kats', $kats);
	}

	public function forminsertkehadiran(Request $request)
	{
		$ceknoform = Internal_kehadiran::where('no_form', $request->no_form)
									->where('sts', 1)
									->count();

		if ($ceknoform != 0) {
			$maxnoform = DB::select( DB::raw("SELECT max(no_form) as maks
										  FROM [bpaddtfake].[dbo].[internal_kehadiran]
										  where sts = 1") );
			$maxnoform = json_decode(json_encode($maxnoform), true);
			if (is_null($maxnoform)) {
			$maxnoform = '1.20.512.10100001';
			} else {
				$splitmaxform = explode(".", $maxnoform[0]['maks']);
				$maxnoform = $splitmaxform[0] . '.' . $splitmaxform[1] . '.' . $splitmaxform[2] . '.10' . substr(($splitmaxform[3]+1), -6);
			}
		} else {
			$maxnoform = $request->no_form;
			$splitmaxform = explode(".", $maxnoform);
			$maxnoform = $splitmaxform[0] . '.' . $splitmaxform[1] . '.' . $splitmaxform[2] . '.10' . substr(($splitmaxform[3]), -6);
		}

		date_default_timezone_set('Asia/Jakarta');
		$insert = [
				'sts' 			=> 1,
				'uname'     	=> (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       	=> date('Y-m-d H:i:s'),	
				'judul'       	=> $request->judul,
				'deskripsi'     => $request->deskripsi,
				'no_form'       => $maxnoform,
				'tgl_mulai'     => ($request->tgl_mulai ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_mulai))) : ''),
				'tgl_end'   	=> ($request->tgl_end ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_end))) : ''),	
				'tujuan_id'     => $request->tujuan_id,
				'tampil'   		=> $request->tampil,
				'allow_foto'	=> $request->allow_foto,
			];
		Internal_kehadiran::insert($insert);

		return redirect('/internal/kehadiran')
					->with('message', 'Form baru berhasil ditambah')
					->with('msg_num', 1);
	}

	public function kehadiranubah(Request $request)
	{

	}

	public function formdeletekehadiran(Request $request)
	{
		Internal_kehadiran::where('ids', $request->ids)
					->update([
						'sts' => 0,
					]);

		Internal_responsehadir::
					where('no_form', $request->no_form)
					->delete();

		return redirect('/internal/kehadiran')
					->with('message', 'Form berhasil dihapus')
					->with('msg_num', 1);
	}

	public function openform($id, $judul)
	{
		date_default_timezone_set('Asia/Jakarta');
		$nowtime = date('Y-m-d');
		
		$form = Internal_kehadiran::
					join('bpaddtfake.dbo.glo_tujuan_kehadiran', 'bpaddtfake.dbo.glo_tujuan_kehadiran.ids', '=', 'bpaddtfake.dbo.internal_kehadiran.tujuan_id')
					->where('bpaddtfake.dbo.internal_kehadiran.sts', '1')
					->where('no_form', $id)
					->orderBy('tgl_mulai', 'desc')
					->first();

		if($nowtime > $form['tgl_end']) {
			$flaglewat = 1;
		} else {
			$flaglewat = 0;
		}

		$ref_form = Glo_tujuan_kehadiran::
					where('ids', $form['tujuan_id'])
					->first();

		$query = ($ref_form['ket_tujuan'] ?? '');

		$emps = DB::select( DB::raw("  
					SELECT id_emp, nrk_emp, nip_emp, nm_emp, a.idgroup_aset as idgroup, tgl_lahir, jnkel_emp, tgl_join, status_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit, d.nm_lok as nm_lok, d.kd_lok as kd_lok  from bpaddtfake.dbo.emp_data as a
					CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
					CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
					,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
					$query
					and ked_emp = 'aktif'
					order by tbunit.kd_unit, a.nm_emp"));
		$emps = json_decode(json_encode($emps), true);

		return view('pages.bpadkehadiran.openform')
				->with('form', $form)
				->with('emps', $emps)
				->with('flaglewat', $flaglewat);
	}
	
	public function simpanform(Request $request)
	{
		date_default_timezone_set('Asia/Jakarta');
		$insert = [
				'tgl'       	=> date('Y-m-d H:i:s'),	
				'no_form'      	=> $request->form,
				'id_emp'     	=> $request->id_emp,
				'hadir'       	=> $request->tampil,
			];
		Internal_responsehadir::insert($insert);

		$nextpage = "/form/" . $request->form . "/thanks";
		return redirect($nextpage);
	}

	public function openthanksform($id)
	{
		$form = Internal_kehadiran::
			join('bpaddtfake.dbo.glo_tujuan_kehadiran', 'bpaddtfake.dbo.glo_tujuan_kehadiran.ids', '=', 'bpaddtfake.dbo.internal_kehadiran.tujuan_id')
			->where('bpaddtfake.dbo.internal_kehadiran.sts', '1')
			->where('no_form', $id)
			->orderBy('tgl_mulai', 'desc')
			->first();

		return view('pages.bpadkehadiran.openthanksform')
				->with('form', $form);
	}

	public function openresponseform($id)
	{
		$form = Internal_kehadiran::
			join('bpaddtfake.dbo.glo_tujuan_kehadiran', 'bpaddtfake.dbo.glo_tujuan_kehadiran.ids', '=', 'bpaddtfake.dbo.internal_kehadiran.tujuan_id')
			->where('bpaddtfake.dbo.internal_kehadiran.sts', '1')
			->where('no_form', $id)
			->orderBy('tgl_mulai', 'desc')
			->first();

		$no_form = $form['no_form'];

		$ref_form = Glo_tujuan_kehadiran::
			where('ids', $form['tujuan_id'])
			->first();

		$query = ($ref_form['ket_tujuan'] ?? '');

		if($form['sts'] == 2) {
			$total = Glo_profile_skpd::count();
			$emps = DB::select( DB::raw("
			select opd.kolok, opd.kolokdagri, opd.nalok, res.nama, res.nip, res.nrk, res.telp, res.email, res.stat_emp, totalhadir, totalorang,
			CASE WHEN res.hadir = 1
				THEN 'HADIR'
				ELSE 'TIDAK HADIR'
			END as hadir
			from (select count(distinct(id_emp)) as totalhadir, count(id_emp) as totalorang from bpaddtfake.dbo.internal_responsehadir where hadir = '1' and no_form = '$no_form') counthadir, bpaddtfake.dbo.glo_profile_skpd opd
			left join bpaddtfake.dbo.internal_responsehadir res on opd.kolok = res.id_emp and res.no_form = '$no_form'
			order by opd.kolok
			"));
			$emps = json_decode(json_encode($emps), true);	
		} else {
			$total = Emp_data::count();
			$emps = DB::select( DB::raw("  
			SELECT max(res.tgl) as tgl, a.id_emp, a.nip_emp, a.nrk_emp, a.nm_emp, res.hadir as sts, tbunit.kd_unit, tbunit.nm_unit, totalhadir, max(res.foto) as foto, res.ket_tdk_hadir as ket_tdk_hadir,
			CASE WHEN res.hadir = 1
				THEN 'HADIR'
				ELSE 'TIDAK HADIR'
			END as hadir
			from (select count(distinct(id_emp)) as totalhadir from bpaddtfake.dbo.internal_responsehadir where hadir = '1' and no_form = '$no_form') counthadir, bpaddtfake.dbo.emp_data a
			join bpaddtfake.dbo.emp_jab tbjab on tbjab.ids = (SELECT TOP 1 ids FROM bpaddtfake.dbo.emp_jab WHERE emp_jab.noid = a.id_emp and emp_jab.sts='1' ORDER BY tmt_jab DESC)
			join bpaddtfake.dbo.glo_org_unitkerja tbunit on tbunit.kd_unit = (SELECT TOP 1 idunit FROM bpaddtfake.dbo.glo_org_unitkerja where tbunit.kd_unit = tbjab.idunit)
			left join bpaddtfake.dbo.internal_responsehadir res on a.id_emp = res.id_emp and res.no_form = '$no_form'
			where a.ked_emp = 'AKTIF'
			and a.sts = 1
			and a.id_emp = tbjab.noid
			and tbjab.sts = 1
			$query
			group by a.id_emp, a.nip_emp, a.nrk_emp, a.nm_emp, res.hadir, tbunit.kd_unit, tbunit.nm_unit, totalhadir, res.ket_tdk_hadir
			order by tbunit.kd_unit, nm_emp
					"));
			$emps = json_decode(json_encode($emps), true);	
		}

		return view('pages.bpadkehadiran.openresponse')
				->with('form', $form)
				->with('ref_form', $ref_form)
				->with('emps', $emps)
				->with('total', $total);
	}

	public function openexcelform($id)
	{
		$form = Internal_kehadiran::
			join('bpaddtfake.dbo.glo_tujuan_kehadiran', 'bpaddtfake.dbo.glo_tujuan_kehadiran.ids', '=', 'bpaddtfake.dbo.internal_kehadiran.tujuan_id')
			->where('bpaddtfake.dbo.internal_kehadiran.sts', '1')
			->where('no_form', $id)
			->orderBy('tgl_mulai', 'desc')
			->first();

		$no_form = $form['no_form'];

		$ref_form = Glo_tujuan_kehadiran::
			where('ids', $form['tujuan_id'])
			->first();

		$query = ($ref_form['ket_tujuan'] ?? '');

		if($form['sts'] == 2) {
			$total = Glo_profile_skpd::count();
			$emps = DB::select( DB::raw("
			select opd.kolok, opd.kolokdagri, opd.nalok, res.nama, res.nip, res.nrk, res.telp, res.email, res.stat_emp, totalhadir, totalorang,
			CASE WHEN res.hadir = 1
				THEN 'HADIR'
				ELSE 'TIDAK HADIR'
			END as hadir
			from (select count(distinct(id_emp)) as totalhadir, count(id_emp) as totalorang from bpaddtfake.dbo.internal_responsehadir where hadir = '1' and no_form = '$no_form') counthadir, bpaddtfake.dbo.glo_profile_skpd opd
			left join bpaddtfake.dbo.internal_responsehadir res on opd.kolok = res.id_emp and res.no_form = '$no_form'
			order by opd.kolok
			"));
			$emps = json_decode(json_encode($emps), true);	
		} else {
			$total = Emp_data::count();
			$emps = DB::select( DB::raw("  
			SELECT max(res.tgl) as tgl, a.id_emp, a.nip_emp, a.nrk_emp, a.nm_emp, res.hadir as sts, tbunit.kd_unit, tbunit.nm_unit, totalhadir, max(res.foto) as foto, res.ket_tdk_hadir as ket_tdk_hadir,
			CASE WHEN res.hadir = 1
				THEN 'HADIR'
				ELSE 'TIDAK HADIR'
			END as hadir
			from (select count(distinct(id_emp)) as totalhadir from bpaddtfake.dbo.internal_responsehadir where hadir = '1' and no_form = '$no_form') counthadir, bpaddtfake.dbo.emp_data a
			join bpaddtfake.dbo.emp_jab tbjab on tbjab.ids = (SELECT TOP 1 ids FROM bpaddtfake.dbo.emp_jab WHERE emp_jab.noid = a.id_emp and emp_jab.sts='1' ORDER BY tmt_jab DESC)
			join bpaddtfake.dbo.glo_org_unitkerja tbunit on tbunit.kd_unit = (SELECT TOP 1 idunit FROM bpaddtfake.dbo.glo_org_unitkerja where tbunit.kd_unit = tbjab.idunit)
			left join bpaddtfake.dbo.internal_responsehadir res on a.id_emp = res.id_emp and res.no_form = '$no_form'
			where a.ked_emp = 'AKTIF'
			and a.sts = 1
			and a.id_emp = tbjab.noid
			and tbjab.sts = 1
			$query
			group by a.id_emp, a.nip_emp, a.nrk_emp, a.nm_emp, res.hadir, tbunit.kd_unit, tbunit.nm_unit, totalhadir, res.ket_tdk_hadir
			order by tbunit.kd_unit, nm_emp
					"));
			$emps = json_decode(json_encode($emps), true);	
		}

		date_default_timezone_set('Asia/Jakarta');

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->mergeCells('A2:G2');
		$sheet->mergeCells('A3:G3');
		$sheet->setCellValue('A2', 'RESPON '.strtoupper($form['judul']));
		$sheet->setCellValue('A3', date('d/M/Y', strtotime($form['tgl_mulai'])) . '-' . date('d/M/Y', strtotime($form['tgl_end'])));
		$sheet->getStyle('a2:a3')->getFont()->setBold( true );
		$sheet->getStyle('a2:a3')->getAlignment()->setHorizontal('center');

		if($form['sts'] == 2) {	
			$styleArray = [
				'font' => [
					'size' => 12,
					'name' => 'Trebuchet MS',
				]
			];
			$sheet->getStyle('A1:J5')->applyFromArray($styleArray);
			$sheet->setCellValue('A5', 'NO');
			$sheet->setCellValue('B5', 'KOLOK SIERA');
			$sheet->setCellValue('C5', 'KOLOK DAGRI');
			$sheet->setCellValue('D5', 'OPD');
			$sheet->setCellValue('E5', 'NAMA');
			$sheet->setCellValue('F5', 'NRK');
			$sheet->setCellValue('G5', 'NIP');
			$sheet->setCellValue('H5', 'TELP');
			$sheet->setCellValue('I5', 'EMAIL');
			$sheet->setCellValue('J5', 'KEHADIRAN');
			$colorArrayhead = [
				'fill' => [
					'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
					'startColor' => [
						'rgb' => 'F79646',
					],
				],
			];
			$sheet->getStyle('A5:J5')->applyFromArray($colorArrayhead);
			$sheet->getStyle('A5:J5')->getFont()->setBold( true );
			$sheet->getStyle('A5:J5')->getAlignment()->setHorizontal('center');
	
			$colorArrayV1 = [
				'fill' => [
					'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
					'startColor' => [
						'rgb' => 'FDE9D9',
					],
				],
			];
	
			$nowrow = 6;
			$rowstart = $nowrow - 1;
			foreach ($emps as $key => $emp) {
				if ($key%2 == 0) {
					$sheet->getStyle('A'.$nowrow.':J'.$nowrow)->applyFromArray($colorArrayV1);
				}
	
				$sheet->setCellValue('A'.$nowrow, $key+1);
				$sheet->setCellValue('B'.$nowrow, '\''.$emp['kolok']);
				$sheet->setCellValue('C'.$nowrow, '\''.$emp['kolokdagri']);
				$sheet->setCellValue('D'.$nowrow, strtoupper($emp['nalok']));
				$sheet->setCellValue('E'.$nowrow, ($emp['nama'] && $emp['nama']!='' ? strtoupper($emp['nama']) : '-'));
				$sheet->setCellValue('F'.$nowrow, ($emp['nrk'] && $emp['nrk']!='' ? '\''.$emp['nrk'] : '-') );
				$sheet->setCellValue('G'.$nowrow, ($emp['nip'] && $emp['nip']!='' ? '\''.$emp['nip'] : '-') );
				$sheet->setCellValue('H'.$nowrow, ($emp['telp'] && $emp['telp']!='' ? '\''.$emp['telp'] : '-') );
				$sheet->setCellValue('I'.$nowrow, ($emp['email'] && $emp['email']!='' ? $emp['email'] : '-'));
				$sheet->setCellValue('J'.$nowrow, $emp['hadir']);
	
				$nowrow++;
			}
		} else if($form['sts'] == 1) {
			$styleArray = [
				'font' => [
					'size' => 12,
					'name' => 'Trebuchet MS',
				]
			];
			$sheet->getStyle('A1:H5')->applyFromArray($styleArray);
			$sheet->setCellValue('A5', 'NO');
			$sheet->setCellValue('B5', 'NIP');
			$sheet->setCellValue('C5', 'NRK');
			$sheet->setCellValue('D5', 'NAMA');
			$sheet->setCellValue('E5', 'UNIT');
			$sheet->setCellValue('F5', 'KEHADIRAN');
			$sheet->setCellValue('G5', 'KETERANGAN');
			$sheet->setCellValue('H5', 'TIMESTAMP');

			$colorArrayhead = [
				'fill' => [
					'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
					'startColor' => [
						'rgb' => 'F79646',
					],
				],
			];
			$sheet->getStyle('A5:H5')->applyFromArray($colorArrayhead);
			$sheet->getStyle('A5:H5')->getFont()->setBold( true );
			$sheet->getStyle('A5:H5')->getAlignment()->setHorizontal('center');
	
			$colorArrayV1 = [
				'fill' => [
					'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
					'startColor' => [
						'rgb' => 'FDE9D9',
					],
				],
			];

			$nowrow = 6;
			$rowstart = $nowrow - 1;
			foreach ($emps as $key => $emp) {
				if ($key%2 == 0) {
					$sheet->getStyle('A'.$nowrow.':H'.$nowrow)->applyFromArray($colorArrayV1);
				}
	
				$sheet->setCellValue('A'.$nowrow, $key+1);
				$sheet->setCellValue('B'.$nowrow, ($emp['nip_emp'] && $emp['nip_emp']!='' ? '\''.$emp['nip_emp'] : '-') );
				$sheet->setCellValue('C'.$nowrow, ($emp['nrk_emp'] && $emp['nrk_emp']!='' ? '\''.$emp['nrk_emp'] : '-') );
				$sheet->setCellValue('D'.$nowrow, ($emp['nm_emp'] && $emp['nm_emp']!='' ? strtoupper($emp['nm_emp']) : '-'));
				$sheet->setCellValue('E'.$nowrow, strtoupper($emp['nm_unit']));
				$sheet->setCellValue('F'.$nowrow, $emp['hadir']);
				$sheet->setCellValue('G'.$nowrow, $emp['ket_tdk_hadir']);
				$sheet->setCellValue('H'.$nowrow, $emp['tgl']);

				if (strlen($emp['kd_unit']) < 10) {
					$sheet->getStyle('A'.$nowrow.':H'.$nowrow)->getFont()->setBold( true );
				}
	
				$nowrow++;
			}
		}

		$filename = date('dmy').'_Respon.xlsx';

		// Redirect output to a client's web browser (Xlsx)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		 
		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.

		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');

		
	}

	// ========== </FORM KEHADIRAN> ========== //

    // ========== <AGENDA KABAN> ========== //
    
    public function agendakabanall(Request $request)
    {
        if(count($_SESSION) == 0) {
			return redirect('home');
		}
        //$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

        if ($request->yearnow) {
			$yearnow = (int)$request->yearnow;
		} else {
			$yearnow = (int)date('Y');
		}

        if ($request->unitnow) {
			$unitnow = $request->unitnow;
		} else {
			$unitnow = '01';
		}

		// if ($request->monthnow) {
		// 	$monthnow = (int)$request->monthnow;
		// } else {
		// 	$monthnow = (int)date('m');
		// }

        $distinctyear = DB::connection('server12')->table('bpadmobile.dbo.glo_periode_rekon')
                    ->selectRaw('DISTINCT(tahun)')
                    ->orderBy('tahun', 'desc')
                    ->get();    

        $units = Glo_org_unitkerja::
                    where('sts', 1)
                    ->whereRaw('LEN(kd_unit) = 6')
                    ->orderBy('kd_unit')
                    ->get();

        $today = date('Y-m-d');
        
        $pegawais = DB::connection('server12')->select( DB::raw("
		select id_emp, nm_emp, nrk_emp, nip_emp, tbunit.kd_unit, tbunit.nm_unit, tbunit.nm_bidang
		from bpaddtfake.dbo.emp_data a
		join bpaddtfake.dbo.emp_jab tbjab on tbjab.ids = (SELECT TOP 1 ids FROM bpaddtfake.dbo.emp_jab WHERE emp_jab.noid = a.id_emp and emp_jab.sts='1' ORDER BY tmt_jab DESC)
		join bpaddtfake.dbo.glo_org_unitkerja tbunit on tbunit.kd_unit = (SELECT TOP 1 idunit FROM bpaddtfake.dbo.glo_org_unitkerja where tbunit.kd_unit = tbjab.idunit)
		where a.ked_emp = 'AKTIF'
		and a.sts = 1
		and a.id_emp = tbjab.noid
		and tbjab.sts = 1
		and tbunit.sao like '01%'
		order by nm_emp") );
		$pegawais = json_decode(json_encode($pegawais), true);

        $events_today = DB::connection('server12')->table('bpadmobile.dbo.dta_kaban_event AS agenda')->select([
                            'agenda.*',
                            'qr.*',
                        ])
                        ->LeftJoin('bpadmobile.dbo.dta_kaban_event_qr AS qr', 'agenda.ids', '=', 'qr.id_agenda')
                        ->where('agenda.sts', 1)
                        ->whereDate('agenda.datetime', "=", $today)
                        ->whereRaw('YEAR(agenda.datetime) = '.$yearnow)
                        ->orderBy('agenda.datetime', 'desc');
        
        $events_besok = DB::connection('server12')->table('bpadmobile.dbo.dta_kaban_event AS agenda')->select([
                            'agenda.*',
                            'qr.*',
                        ])
                        ->LeftJoin('bpadmobile.dbo.dta_kaban_event_qr AS qr', 'agenda.ids', '=', 'qr.id_agenda')
                        ->where('agenda.sts', 1)
                        ->whereDate('agenda.datetime', ">", $today)
                        ->whereRaw('YEAR(agenda.datetime) = '.$yearnow)
                        ->orderBy('agenda.datetime', 'asc');

        $events_kemarin = DB::connection('server12')->table('bpadmobile.dbo.dta_kaban_event AS agenda')->select([
                            'agenda.*',
                            'qr.*',
                        ])
                        ->LeftJoin('bpadmobile.dbo.dta_kaban_event_qr AS qr', 'agenda.ids', '=', 'qr.id_agenda')
                        ->where('agenda.sts', 1)
                        ->whereDate('agenda.datetime', "<", $today)
                        ->whereRaw('YEAR(agenda.datetime) = '.$yearnow)
                        ->orderBy('agenda.datetime', 'desc');
                        

        if($request->unitnow) {
            $events_today = $events_today->where('agenda.id_unit', 'like', '%'.$unitnow.'%');
            $events_besok = $events_besok->where('agenda.id_unit', 'like', '%'.$unitnow.'%');
            $events_kemarin = $events_kemarin->where('agenda.id_unit', 'like', '%'.$unitnow.'%');
        }

        $events_today = $events_today->get();
        $events_today = json_decode(json_encode($events_today), true);
        $events_besok = $events_besok->get();
        $events_besok = json_decode(json_encode($events_besok), true);
        $events_kemarin = $events_kemarin->get();
        $events_kemarin = json_decode(json_encode($events_kemarin), true);

        return view('pages.bpadinternal.kaban-event')
        ->with('access', $access)
        ->with('units', $units)
	    ->with('yearnow', $yearnow)
	    ->with('unitnow', $unitnow)
	    ->with('distinctyear', $distinctyear)
        ->with('events_today', $events_today)
        ->with('events_besok', $events_besok)
        ->with('events_kemarin', $events_kemarin);
    }

    public function getagendakaban(Request $request)
    {
        $event = DB::connection('server12')->table('bpadmobile.dbo.dta_kaban_event AS agenda')->select([
                            'agenda.*',
                            'qr.*',
                        ])
                        ->LeftJoin('bpadmobile.dbo.dta_kaban_event_qr AS qr', 'agenda.ids', '=', 'qr.id_agenda')
                        ->where('agenda.sts', 1)
                        ->where('agenda.ids', $request->ids)
                        ->first();
        $event = json_decode(json_encode($event), true);

        return $event;
    }

    public function forminsertagendakaban(Request $request)
    {
        if(count($_SESSION) == 0) {
			return redirect('home');
		}

        $event_idunit = "";
        $event_nmunit = "";
        foreach($request->id_unit as $key => $idunit) {
            $result = Glo_org_unitkerja::where('kd_unit', $idunit)->where('sts', 1)->first();
            $event_idunit .= $result['kd_unit'];
            $event_nmunit .= $result['nm_unit'];
            if (!($key === array_key_last($request->id_unit))) {
                $event_idunit .= "::";
                $event_nmunit .= "::";
            }
        }
        
        $datetime = date('Y-m-d', strtotime(str_replace('/', '-', $request->date))) . " " . date('H:i', strtotime($request->time));

		$insertagendakaban = [
			'sts'           => 1,
			'input_date'    => date('Y-m-d H:i:s'),
			'datetime'      => $datetime,
			'event_name'    => $request->event_name,
			'event_number'  => $request->event_number,
			'event_from'    => $request->event_from,
			'id_unit'       => $event_idunit,
			'nm_unit'       => $event_nmunit,
			'location'      => $request->location,
			'info'          => $request->info,
		];

		Dta_kaban_event::insert($insertagendakaban);

		return redirect('/internal/agenda-kaban')
				->with('message', 'Agenda baru berhasil dibuat')
				->with('msg_num', 1);
    }

    public function formdeleteagendakaban(Request $request)
    {
        if(count($_SESSION) == 0) {
			return redirect('home');
		}

        Dta_kaban_event::
        where('ids', $request->ids)
        ->update([
            'sts' => 0,
        ]);

        Dta_kaban_event_qr::
        where('id_agenda', $request->ids)
        ->delete();

        return redirect('/internal/agenda-kaban')
				->with('message', 'Agenda tersebut berhasil dihapus')
				->with('msg_num', 1);
    }

    public function formgenerateagendakaban(Request $request)
    {
        if(count($_SESSION) == 0) {
			return redirect('home');
		}

        $getagenda = Dta_kaban_event::where('ids', $request->ids)->first();

        $seed = str_split('abcdefghijklmnopqrstuvwxyz'
                        .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                        .'0123456789!@#$%^&*()'); // and any other characters
                shuffle($seed); // probably optional since array_is randomized; this may be redundant
                $rand = '';
                foreach (array_rand($seed, 16) as $k) $rand .= $seed[$k];

        $longtext = date('m', strtotime($getagenda['datetime'])) . date('d', strtotime($getagenda['datetime'])) . $rand . "absenapelbpad" . date('Y', strtotime($getagenda['datetime']));
        
        $insertqragenda = [
			'longtext'          => $longtext,
			'nama_kegiatan'     => $getagenda['event_name'],
			'start_datetime'    => $getagenda['datetime'],
			'end_datetime'      => date('Y-m-d', strtotime($getagenda['datetime'])) . " 23:59",
			'createdate'        => date('Y-m-d H:i:s'),
			'active'            => 1,
			'sts'            => 1,
			'id_agenda'         => $getagenda['ids'],
		];

        Dta_kaban_event_qr::insert($insertqragenda);

        return redirect('/internal/agenda-kaban')
				->with('message', 'QR Code untuk agenda tersebut berhasil dibuat')
				->with('msg_num', 1);
    }

    public function exportexcelagendabpad (Request $request)
    {
        if(count($_SESSION) == 0) {
			return redirect('home');
		}
        $longtext = $request->longtext;
        $getref = Dta_kaban_event_qr::where('longtext', 'LIKE', $longtext . '%')->first();
        $tableClass = env('APP_ENV') == 'local' ? Dta_kaban_event_staging::class : Dta_kaban_event_save::class;
        $getrekapabsen = $tableClass::where('kegiatan', $longtext)->get();

        return view('pages.bpadinternal.kaban-event-excel')
                ->with('getref', $getref)
                ->with('getrekapabsen', $getrekapabsen);
    }

    // ========== </AGENDA KABAN> ========== //
}
