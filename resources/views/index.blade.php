@extends('layouts.master')

@section('content')

	@if(isset($lelang) && $lelang['sts'] == 1)
	<div id="ads" class="bottom pull-right">
		<button class="btn btnads" id="adsbutton">X</button>
		<a href="{{ url('/content/lelang') }}">
			<img src="{{ ('/portal/img/photo/lelang-bpad.jpeg') }}">
			<h1 class="text-center" id="lelang-text">LELANG</h1>
			<!-- <a href="#">Home</a> -->
		</a>
	</div>
	@endif
			

	<!-- <div id="about"></div> -->
	<div class="row">
		<div class="col-md-12">
			<!-- <div class="section-title text-center"> -->
				<!-- <h1 class="title"><span style="background: linear-gradient(90deg, #1FA2FF 0%, #12D8FA 25%, #A6FFCF 50%, #12D8FA 75%, #1FA2FF 100%); -webkit-background-clip: text;-webkit-text-fill-color: transparent; font-size: 64px; font-weight: bold;">BERITA TERKINI</span></h1> -->
				
				<!-- <h1 class="title"><span style="background: linear-gradient(90deg, #0052D4 0%, #65C7F7 30%, #65C7F7 60%, #0052D4 100%); -webkit-background-clip: text;-webkit-text-fill-color: transparent; font-size: 64px; font-weight: bold;">BERITA TERKINI</span></h1> -->

				<!-- <div style="text-align: center;">
					<img height="200" src="{{ ('/portal/img/photo/bpad-logo-04b.png') }}">
				</div> -->

				<!-- <h1 class=" chrome">BERITA</h1> -->
				<!-- <h3 class=" dreams">TERKINI</h3> -->
				<!-- <p class="sub-title">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p> -->
			<!-- </div> -->
		</div>
	</div>
	<div id="testimonial" class="section" style="height: 780px;">
		<!-- background section -->
		<div class="section-bg asyncImage" style="background-image: url('{{ ('/portal/img/photo/background-00b.jpg')}}');">
		</div>
		<!-- /background section -->

		<!-- container -->
		<div class="container">
			<!-- row -->
			<div class="row">
				<!-- Testimonial owl -->
				<div class="col-md-12" style="top: 30px">
					<div id="testimonial-owl" class="owl-carousel owl-theme">
						<!-- testimonial -->
						@foreach($hot_content as $hot)

							<?php
								// if (file_exists(config('app.openfileimgberita') . $hot['tfile'])) {
								// 	$fullpath = config('app.openfileimgberita') . $hot['tfile'];
								// } else {
								// 	$fullpath = 'http://bpad.jakarta.go.id/images/cms/1.20.512/1/file/' . $hot['tfile'];
								// }

								if (file_exists(config('app.openfileimgberita') . $hot['tfile']) && $hot['tfile'] && $hot['tfile'] != '') {
									$fullpath = config('app.openfileimgberitafull') . $hot['tfile'];
								} elseif (file_exists('http://bpad.jakarta.go.id/images/cms/1.20.512/1/file/' . $hot['tfile'])) {
									$fullpath = 'http://bpad.jakarta.go.id/images/cms/1.20.512/1/file/' . $hot['tfile'];
								} else {
									$fullpath = config('app.openfileimgcontentdefault');
								}
							?>

							<div class="testimonial col-md-12 hot-div">
								<div class="col-md-6"><img class="hot-img" src="{{ $fullpath }}" alt=""></div>
								
								<div class="col-md-6">
									<h3 class="hot-title">{{ $hot['judul'] }}</h3><br>
									<div class="hot-text" style="color: black">
										{!! html_entity_decode($hot['isi1']) !!}
									</div>
									<hr>
									<a href="{{ url('/content/berita/' . $hot['ids']. '/' . 
									str_replace(' ', '_', preg_replace('/[^\da-z ]/i', '', $hot['judul']))
									) }}" class="primary-button pull-right">Lihat Berita</a>
								</div>
							</div>

						@endforeach

					</div>
				</div>
				<!-- /Testimonial owl -->
			</div>
			<!-- /Row -->
		</div>
		<!-- /container -->

	</div>
	<!-- /TESTIMONIAL -->
	<hr>
	<!-- EVENTS -->
	<div id="events" class="section">
		<!-- container -->
		<div class="container">
			<!-- row -->
			<div class="row">
				<!-- section title -->
				<div class="col-md-12">
					<div class="section-title">
						<h2 class="title" style="font-family: 'Century Gothic';"><span style="color: #006cb8; font-weight: bold">BERITA</span> TERKINI</h2>
						<!-- <p class="sub-title">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p> -->
					</div>
				</div>
				<!-- /section title -->

				<?php $count = 1 ?>
				@foreach($normal_content as $normal)

					<?php 
						if (file_exists(config('app.openfileimgberita') . $normal['tfile']) && $normal['tfile'] && $normal['tfile'] != '') {
							$fullpath = config('app.openfileimgberitafull') . $normal['tfile'];
						} elseif(file_exists('http://bpad.jakarta.go.id/images/cms/1.20.512/1/file/' . $normal['tfile'])) {
							$fullpath = 'http://bpad.jakarta.go.id/images/cms/1.20.512/1/file/' . $normal['tfile'];
						} else {
							$fullpath = config('app.openfileimgcontentdefault');
						}

						$originalDate = explode(" ", $normal['tanggal']);
						$newTime = explode(":", $originalDate[1]);
						$newDate = date("l, d F Y", strtotime($originalDate[0]));
					?>

					<div class="col-md-6">
						<div class="event">
							<div class="event-img">
								<a href="{{ url('/content/berita/' . $normal['ids']. '/' . 
								str_replace(' ', '_', preg_replace('/[^\da-z ]/i', '', $normal['judul']))
								) }}">
									<img src="{{ $fullpath }}" alt="">
								</a>
							</div>
							<div class="event-content">
								<h4><a href="{{ url('/content/berita/' . $normal['ids']) }}">{{ $normal['judul'] }}</a></h4>
								<ul style="list-style: none; padding: 0;" class="event-meta">
									<li><i class="fa fa-clock-o"></i> {{ $newDate }} </li>
									<li><i class="fa fa-user"></i> {{ ucwords(strtolower($normal['editor'])) }} </li>
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

			</div>
			<!-- /row -->
			<div class="text-center">
				<a href="{{ url('/content/berita/') }}" class="primary-button pull-right">Lihat Semua</a>
			</div>
		</div>
		<!-- /container -->
	</div>
	<!-- /EVENTS -->

	<hr>

	<!-- BLOG -->
	<div id="blog" class="section">
		<!-- container -->
		<div class="container">
			<!-- row -->
			<div class="row">
				<!-- section title -->
				<div class="col-md-12">
					<div class="section-title">
						<h2 class="title" style="font-family: 'Century Gothic';"><span style="color: #006cb8; font-weight: bold;">GALERI</span> FOTO</h2>
						<!-- <p class="sub-title">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p> -->
					</div>
				</div>
				<!-- /section title -->

				<!-- blog -->
				<!-- <div class="col-md-4">
					<div class="article">
						<div class="article-img">
							<a href="single-blog.html">
								<img src="./img/post-3.jpg" alt="">
							</a>
						</div>
						<div class="article-content">
							<h3 class="article-title"><a href="single-blog.html">Possit nostro aeterno eu vis, ut cum quem reque</a></h3>
							<ul class="article-meta">
								<li>12 November 2018</li>
								<li>By John doe</li>
								<li>0 Comments</li>
							</ul>
							<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
						</div>
					</div>
				</div> -->
				<!-- /blog -->
				<?php $count = 1 ?>
				@foreach($photo_content as $photo)
					<?php
						if (file_exists(config('app.openfileimggambar') . $photo['tfile']) && $photo['tfile'] && $photo['tfile'] != '') {
							$fullpath = config('app.openfileimggambarfull') . $photo['tfile'];
						} elseif(file_exists('http://bpad.jakarta.go.id/images/cms/1.20.512/5/file/' . $photo['tfile'])) {
							$fullpath = 'http://bpad.jakarta.go.id/images/cms/1.20.512/5/file/' . $photo['tfile'];
						} else {
							$fullpath = config('app.openfileimgcontentdefault');
						}
					?>

					<div class="col-md-6">
						<div class="article">
							<div class="article-img">
								<a href="JavaScript:void(0);">
									<span class="text-test" style="">
										<img style="width: 50px" src="{{ ('/portal/img/photo/bpad-logo-00.png32') }}">
										<hr>
										<h4 style="color: white">{{ $photo['judul'] }}</h4>
									</span>
									<img src="{{ $fullpath }}" alt="" >
								</a>
							</div>
							<!-- <div class="article-content">
								<h3 class="article-title"><a href="single-blog.html">Possit nostro aeterno eu vis, ut cum quem reque</a></h3>
								<ul class="article-meta">
									<li>12 November 2018</li>
									<li>By John doe</li>
									<li>0 Comments</li>
								</ul>
								<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
							</div> -->
						</div>
					</div>

					@if ($count%2 == 0)

						<div class="clearfix visible-md visible-lg"></div>

					@endif
					
					<?php $count++; ?>	

				@endforeach

			</div>
			<!-- /row -->
			<div class="text-center">
				<a href="{{ url('/content/foto/') }}" class="primary-button">Lihat Semua</a>
			</div>
		</div>
		<!-- /container -->
	</div>
	<!-- /BLOG -->

	<hr>

	<!-- container -->
	<div class="container">
		<!-- row -->
		<div class="row">
			<!-- section title -->
			<div class="col-md-12">
				<div class="section-title">
					<h2 class="title" style="font-family: 'Century Gothic';"><span style="color: #006cb8; font-weight: bold;">INFO</span>GRAFIK</h2>
					<!-- <p class="sub-title">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p> -->
				</div>
			</div>
			<!-- /section title -->
		</div>
		<!-- /row -->
	</div>
	<!-- /container -->
	<div id="home-owl" class="owl-carousel owl-theme">

		@if($infos == null)
			<!-- home item -->
			<div class="home-item"  style="overflow: hidden; height: 500px">
				<!-- section background -->
				<div class="section-bg" style="background-image: url('{{ config('app.openfileimgcontentdefault') }}'); background-size: contain; background-repeat: no-repeat;"></div>
				<!-- /section background -->
			</div>
			<!-- /home item -->
		@else
			@foreach($infos as $info)

			<?php
				// $fullpath = config('app.openfileimginfografik') . $info['tfile'];

				if ($info['tfile'] && $info['tfile'] != '') {
					$fullpath = config('app.openfileimginfografik') . $info['tfile'];
				} else {
					$fullpath = config('app.openfileimgcontentdefault');
				}
			?>

			<!-- home item -->
			<div class="home-item"  style="overflow: hidden; height: 500px">
				<!-- section background -->
				<div class="section-bg" style="background-image: url('{{ $fullpath }}'); background-size: contain; background-repeat: no-repeat;"></div>
				<!-- /section background -->
			</div>
			<!-- /home item -->
			@endforeach
		@endif
		
			
	</div>
	<!-- /HOME OWL -->

	<hr>

    {{-- @include('pages.landingcomponent.produk') --}}
    {{-- @include('pages.landingcomponent.medsos') --}}

	<script type="text/javascript">
		(() => {
		'use strict';
		// Page is loaded
		const objects = document.getElementsByClassName('asyncImage');
		Array.from(objects).map((item) => {
			// Start loading image
			const img = new Image();
			img.src = item.dataset.src;
			// Once image is loaded replace the src of the HTML element
			img.onload = () => {
			item.classList.remove('asyncImage');
			return item.nodeName === 'IMG' ? 
				item.src = item.dataset.src :        
				item.style.backgroundImage = `url(${item.dataset.src})`;
			};
		});
		})();
	</script>

@endsection