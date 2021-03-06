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
use App\Help;
use App\Sec_menu;

session_start();

class InternalController extends Controller
{
	use SessionCheckTraits;

	public function __construct()
	{
		$this->middleware('auth');
	}

	// ========== <AGENDA> ========== //
    
    public function agenda()
    {
    	$this->checkSessionTime();
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
    	$this->checkSessionTime();

		return view('pages.bpadinternal.agendatambah');
    }

    public function agendaubah(Request $request)
    {
    	$this->checkSessionTime();

		$agenda = Agenda_tb::
					where('ids', $request->ids)
					->first();

		return view('pages.bpadinternal.agendaubah')
				->with('ids', $request->ids)
				->with('agenda', $agenda);
    }

    public function formappragenda(Request $request)
    {
    	$this->checkSessionTime();

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
    	$this->checkSessionTime();

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
    	$this->checkSessionTime();

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
    	$this->checkSessionTime();

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
    	$this->checkSessionTime();
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
    	$this->checkSessionTime();

		return view('pages.bpadinternal.beritatambah');
    }

    public function beritaubah(Request $request)
    {
    	$this->checkSessionTime();

		$berita = Berita_tb::
					where('ids', $request->ids)
					->first();

		return view('pages.bpadinternal.beritaubah')
				->with('ids', $request->ids)
				->with('berita', $berita);
    }

    public function formapprberita(Request $request)
    {
    	$this->checkSessionTime();

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
    	$this->checkSessionTime();

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
    	$this->checkSessionTime();

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
    	$this->checkSessionTime();

		Berita_tb::
				where('ids', $request->ids)
				->delete();

		return redirect('/internal/berita')
					->with('message', 'Berita berhasil dihapus')
					->with('msg_num', 1);
    }

    // ========== </BERITA> ========== //

    // ========== <SARAN> ========== //
    
    public function saran()
    {
    	$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		$sarans = Help::limit(100)
					->orderBy('tanggal', 'desc')
					->get();

		return view('pages.bpadinternal.saran')
				->with('access', $access)
				->with('sarans', $sarans);
    }

    public function formapprsaran(Request $request)
    {
    	$this->checkSessionTime();

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
}
