<?php

namespace App\Http\Controllers;

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
use App\Help;
use App\Internal_arsip;
use App\Internal_info;
use App\Internal_kehadiran;
use App\Internal_responsehadir;
use App\Sec_menu;

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
		$this->checksession(); //$this->checkSessionTime();
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
		$this->checksession(); //$this->checkSessionTime();

		return view('pages.bpadinternal.agendatambah');
	}

	public function agendaubah(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		$agenda = Agenda_tb::
					where('ids', $request->ids)
					->first();

		return view('pages.bpadinternal.agendaubah')
				->with('ids', $request->ids)
				->with('agenda', $agenda);
	}

	public function formappragenda(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

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
		$this->checksession(); //$this->checkSessionTime();

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
		$this->checksession(); //$this->checkSessionTime();

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
		$this->checksession(); //$this->checkSessionTime();

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
		$this->checksession(); //$this->checkSessionTime();
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
		$this->checksession(); //$this->checkSessionTime();

		return view('pages.bpadinternal.beritatambah');
	}

	public function beritaubah(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		$berita = Berita_tb::
					where('ids', $request->ids)
					->first();

		return view('pages.bpadinternal.beritaubah')
				->with('ids', $request->ids)
				->with('berita', $berita);
	}

	public function formapprberita(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

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
		$this->checksession(); //$this->checkSessionTime();

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
		$this->checksession(); //$this->checkSessionTime();

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
		$this->checksession(); //$this->checkSessionTime();

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
		$this->checksession(); //$this->checkSessionTime();
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
		$this->checksession(); //$this->checkSessionTime();

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
		// // require 'vendor/autoload.php';

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
		$this->checksession(); //$this->checkSessionTime();
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
		$this->checksession(); //$this->checkSessionTime();

		return view('pages.bpadinternal.infotambah');
	}

	public function infoubah(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

		$infos = Internal_info::
					where('ids', $request->ids)
					->first();

		return view('pages.bpadinternal.infoubah')
				->with('ids', $request->ids)
				->with('infos', $infos);
	}

	public function forminsertinfo(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

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
		$this->checksession(); //$this->checkSessionTime();

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
		$this->checksession(); //$this->checkSessionTime();

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
		$this->checksession(); //$this->checkSessionTime();
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
		$this->checksession(); //$this->checkSessionTime();

		$kats = Glo_arsip_kategori::
				where('sts', 1)
				->orderBy('singkatan')
				->get();

		return view('pages.bpadinternal.arsiptambah')
				->with('kats', $kats);
	}

	public function arsipubah(Request $request)
	{
		$this->checksession(); //$this->checkSessionTime();

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
		$this->checksession(); //$this->checkSessionTime();

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
		$this->checksession(); //$this->checkSessionTime();

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
		$this->checksession(); //$this->checkSessionTime();

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
		$this->checksession(); //$this->checkSessionTime();
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
		$this->checksession(); //$this->checkSessionTime();

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
			SELECT max(res.tgl) as tgl, a.id_emp, a.nip_emp, a.nrk_emp, a.nm_emp, res.hadir as sts, tbunit.kd_unit, tbunit.nm_unit, totalhadir, max(res.foto) as foto,
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
			group by a.id_emp, a.nip_emp, a.nrk_emp, a.nm_emp, res.hadir, tbunit.kd_unit, tbunit.nm_unit, totalhadir
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

	// ========== </FORM KEHADIRAN> ========== //
}
