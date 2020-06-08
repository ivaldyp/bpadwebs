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
use App\Content_tb;
use App\Glo_kategori;
use App\Help;
use App\Produk_aset;
use App\Setup_tb;

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
		// // require 'vendor/autoload.php';

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
	public function index()
	{
		// if (PHP_SESSION_ACTIVE) {
		//     session_destroy();
		// }

		$info_id = Glo_kategori::
						where('nmkat', 'infografik')
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
						where('nmkat', 'lelang')
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
					and sts = 1
					and ids not in $excludeid
					order by tanggal desc") );
		$normal_content = json_decode(json_encode($normal_content), true);

		$photo_content = Content_tb::
					where('idkat', 5)
					->where('appr', 'Y')
					->where('sts', 1)
					->orderBy('tanggal', 'desc')
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

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//
	}
}