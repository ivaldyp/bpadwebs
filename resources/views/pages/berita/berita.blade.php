@extends('layouts.master')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12 text-center">
			<!-- <h1 class="title"><span style="background: linear-gradient(to right, #8C0606 0%, #FF0000 50%, #8C0606 100%); -webkit-background-clip: text;-webkit-text-fill-color: transparent; font-size: 64px">BERITA BPAD</span></h1> -->
			<h1 class="title" style="font-family: 'Century Gothic'; font-size: 64px"><span style="color: #006cb8; font-weight: bold">BERITA</span> TERKINI</h1>
		</div>
	</div>
</div>
<!-- SECTION -->
<div class="section">
	<!-- container -->
	<div class="container">
		<!-- row -->
		<!-- <div class="row">
			<div class="col-md-12" style="padding: 20px">
				<form class="form-horizontal" action="" method="get">
					<div class="form-group">
						<label class="control-label col-md-2" style="text-align: left;">Cari Berita</label>
						<div class="col-md-6">
							<i class="fa fa-search"></i><input type="text" class="input" name="" placeholder="cari">
						</div>
					</div>
				</form>
			</div>
		</div> -->
		<div class="row">
			<!-- MAIN -->
			<main id="main" class="col-md-9">
				<div class="row">
					<!-- article -->

					<?php $count = 1 ?>
					@foreach($berita_list as $berita)

						<?php 
							$fullpath = "http://bpad.jakarta.go.id/images/cms/1.20.512/1/file/" . $berita['tfile']; 
							$originalDate = explode(" ", $berita['tanggal']);
							$newTime = explode(":", $originalDate[1]);
							$newDate = date("d F Y", strtotime($originalDate[0]));
						?>

						<div class="col-md-6">
							<div class="article">
								<div class="berita" style="border-radius: 10px">
									<a href="{{ url('/content/berita/' . $berita['ids']) }}">
										<img src="{{ $fullpath }}" alt="">
									</a>
								</div>
								<div class="article-content">
									<h3 class="article-title"><a href="{{ url('/content/berita/' . $berita['ids']) }}">{{ $berita['judul'] }}</a></h3>
									<ul style="list-style: none; padding: 0;" class="article-meta">
										<i class="fa fa-user" style="color: #006cd8"></i> oleh {{ $berita['editor'] }}
										<span class="pull-right"><i class="fa fa-calendar"></i> {{ $newDate }} <br></span>
										<!-- <i class="fa fa-eye"></i> {{ $berita['thits'] }} views  -->
									</ul>
									<!-- <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p> -->
								</div>
							</div>
						</div>

						@if ($count%2 == 0)

							<div class="clearfix visible-md visible-lg"></div>

						@endif
						
						<?php $count++; ?>

					@endforeach

					<!-- /article -->

				</div>
			</main>
			<!-- /MAIN -->

			<!-- ASIDE -->
			<aside id="aside" class="col-md-3">
				<!-- recent widget -->
				<div class="widget">
					<h3 class="widget-title">Berita Terbaru</h3>

					@foreach($aside_recent as $aside)

						<?php 
							$asidePath = "http://bpad.jakarta.go.id/images/cms/1.20.512/1/file/" . $aside['tfile']; 
							$originalDate = explode(" ", $aside['tanggal']);
							$asideDate = date("d F Y", strtotime($originalDate[0]));
						?>

						<!-- single post -->
						<div class="widget-post">
							<a href="{{ url('/content/berita/' . $aside['ids']) }}">
								<div class="widget-img">
									<img src="{{ $asidePath }}" alt="">
								</div>
								<div class="widget-content">
									{{ $aside['judul'] }}
								</div>
							</a>
							<ul style="list-style: none; padding: 0;" class="article-meta">
								<li>{{ $asideDate }}</li>
								<li>{{ $aside['thits'] }} views</li>
							</ul>
						</div>
						<!-- /single post -->

					@endforeach
				
				</div>
				<!-- /recent widget -->

				<hr>

				<!-- top view widget -->
				<div class="widget">
					<h3 class="widget-title">Paling Banyak Dilihat</h3>

					@foreach($aside_top_view as $aside)

						<?php 
							$asidePath = "http://bpad.jakarta.go.id/images/cms/1.20.512/1/file/" . $aside['tfile']; 
							$originalDate = explode(" ", $aside['tanggal']);
							$asideDate = date("d F Y", strtotime($originalDate[0]));
						?>

						<!-- single post -->
						<div class="widget-post">
							<a href="{{ url('/content/berita/' . $aside['ids']) }}">
								<div class="widget-img">
									<img src="{{ $asidePath }}" alt="">
								</div>
								<div class="widget-content">
									{{ $aside['judul'] }}
								</div>
							</a>
							<ul style="list-style: none; padding: 0;" class="article-meta">
								<li>{{ $asideDate }}</li>
								<li>{{ $aside['thits'] }} views</li>
							</ul>
						</div>
						<!-- /single post -->

					@endforeach
				
				</div>
				<!-- /top view widget -->
			</aside>
			<!-- /ASIDE -->
		</div>
		<!-- /row -->
		{{ $berita_list->links() }}
	</div>
	<!-- /container -->
</div>
<!-- /SECTION -->

@endsection