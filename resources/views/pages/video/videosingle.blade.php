@extends('layouts.master')

@section('content')

<?php 
	$originalDate = explode(" ", $video['tanggal']);
	$newTime = explode(":", $originalDate[1]);
	$newDate = date("d F Y", strtotime($originalDate[0]));
?>

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
					<!-- article title -->
					<h2 class="article-title ">{{ $video['judul'] }}</h2><br>
					<!-- /article title -->
					<hr>
					<!-- article meta -->
					<ul style="list-style: none; padding: 0;" class="article-meta">
						<i class="fa fa-user"></i> oleh {{ $video['editor'] }}, {{ $newDate }}
						<span class="pull-right">
							<i class="fa fa-eye"></i> {{ $video['thits'] }} views
						</span>
					</ul>
					<!-- /article meta -->	
					<hr>

					<!-- article content -->
					<div class="article-content">
						<div>
							{!! str_replace(".com/watch",".com/embed",$video['isi2']) !!}
						</div>
					</div><br>
					<!-- /article content -->
					<a href="{{ url('/content/video') }}"><strong><i class="fa fa-arrow-left"></i> Kembali ke halaman video</strong></a>
				</div>					
			</main>
			<!-- /MAIN -->

			<!-- ASIDE -->
			<aside id="aside" class="col-md-3">

				<hr>

				<!-- top view widget -->
				<div class="widget">
					<h3 class="widget-title">Paling Banyak Dilihat</h3>

					@foreach($aside_top_view as $aside)

						<?php 
							$originalDate = explode(" ", $aside['tanggal']);
							$asideDate = date("d F Y", strtotime($originalDate[0]));
						?>

						<!-- single post -->
						<div class="widget-post">
							<a href="{{ url('/content/video/' . $aside['ids']) }}">
							
								<div>
									<i class="fa fa-caret-right"></i> {{ $aside['judul'] }}
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
	</div>
	<!-- /container -->
</div>
<!-- /SECTION -->

@endsection