@extends('layouts.master')

@section('content')

<?php 
	if ($berita != null) {
		// if (file_exists(config('app.openfileimgberita') . $berita['tfile'])) {
		// 	$fullpath = config('app.openfileimgberitafull') . $berita['tfile'];
		// } else {
		// 	$fullpath = 'http://bpad.jakarta.go.id/images/cms/1.20.512/1/file/' . $berita['tfile'];
		// }

		if (file_exists(config('app.openfileimglelang') . $berita['tfile']) && $berita['tfile'] && $berita['tfile'] != '') {
			$fullpath = config('app.openfileimglelangfull') . $berita['tfile'];
		} else {
			$fullpath = config('app.openfileimgcontentdefault');
		}
		
		$originalDate = explode(" ", $berita['tanggal']);
		$newTime = explode(":", $originalDate[1]);
		$newDate = date("d F Y", strtotime($originalDate[0]));
	}
?>

<style type="text/css">
	.article-content a {
		color: blue;
		text-decoration: underline;
	}
</style>

<!-- SECTION -->
<div class="section">
	<!-- container -->
	<div class="container">
		<!-- row -->
		<div class="row">
			<!-- MAIN -->
			<main id="main" class="col-md-9">
				<!-- article -->
				<div class="article">
					@if($berita == null)
						<h1>Tidak ada lelang</h1>
					@else 
						<!-- article title -->
						<h2 class="article-title ">{{ $berita['judul'] }}</h2><br>
						<!-- /article title -->
						<hr>
						<!-- article meta -->
						<ul style="list-style: none; padding: 0;" class="article-meta">
							<i class="fa fa-user"></i> oleh {{ $berita['editor'] }}, {{ $newDate }}
							<span class="pull-right">
								<i class="fa fa-eye"></i> {{ $berita['thits'] }} views
							</span>
						</ul>
						<!-- /article meta -->	
						<hr>
						<!-- article img -->
						<div class="article-img">
							<img src="<?php echo $fullpath; ?>" alt="">
						</div>
						<!-- article img -->

						<!-- article content -->
						<div class="article-content">
							<div>
								{!! html_entity_decode($berita['isi2']) !!}
							</div>
						</div>
						<br>
						<!-- /article content -->
					@endif
						
					<a href="{{ url('/') }}"><strong><i class="fa fa-arrow-left"></i> Kembali ke halaman utama</strong></a>
				</div>					
			</main>
			<!-- /MAIN -->
		</div>
		<!-- /row -->
	</div>
	<!-- /container -->
</div>
<!-- /SECTION -->

@endsection