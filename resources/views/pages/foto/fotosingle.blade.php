<!DOCTYPE html>
<html>
<head>
	<title>BPAD DKI Jakarta</title>
	<link rel="shortcut icon" type="image/x-icon" href="{{ ('/portal/img/photo/bpad-logo-00.png') }}" />

	<style type="text/css">
		img {
			max-width: 100%;
			max-height: 100%;
			width: auto;
			margin: auto;
		}
	</style>
</head>
<body style="background-color: black;">
	<?php 
		// if (file_exists(config('app.openfileimggambar') . $foto['tfile'])) {
		// 	$fullpath = config('app.openfileimggambarfull') . $foto['tfile'];
		// } else {
		// 	$fullpath = 'http://bpad.jakarta.go.id/images/cms/1.20.512/5/file/' . $foto['tfile'];
		// }

		if (file_exists(config('app.openfileimggambar') . $foto['tfile']) && $foto['tfile'] && $foto['tfile'] != '') {
			$fullpath = config('app.openfileimggambarfull') . $foto['tfile'];
		} elseif(file_exists('http://bpad.jakarta.go.id/images/cms/1.20.512/5/file/' . $foto['tfile'])) {
			$fullpath = 'http://bpad.jakarta.go.id/images/cms/1.20.512/5/file/' . $foto['tfile'];
		} else {
			$fullpath = config('app.openfileimgcontentdefault');
		}
		
		$originalDate = explode(" ", $foto['tanggal']);
		$newTime = explode(":", $originalDate[1]);
		$newDate = date("d F Y", strtotime($originalDate[0]));

	?>
	<div style="padding: 20px;">
		<img src="{{ $fullpath }}" >	
	</div>
</body>
</html>