@extends('layouts.master')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12 text-center">
			<!-- <h1 class="title"><span style="background: linear-gradient(to right, #8C0606 0%, #FF0000 50%, #8C0606 100%); -webkit-background-clip: text;-webkit-text-fill-color: transparent; font-size: 64px">PROFIL BPAD</span></h1> -->
			<h1 class="title" style="font-family: 'Century Gothic'; font-size: 64px; margin-top: 50px;"><span style="color: #006cb8; font-weight: bold">ALUR</span> PERMOHONAN INFORMASI</h1>
		</div>
	</div>
</div>
<!-- SECTION -->
<div class="section">
	<!-- container -->
	<div class="container">
		<!-- row -->
		<div class="row">
			<!-- MAIN -->
			<main id="main" class="col-md-12">
				<!-- article -->
				<div class="article">
					<!-- article content -->
					<div class="article-content">
                        <img src="{{ ('/portal/img/ppid/Alur Permohonan Info PPID.jpg') }}" width='100%' alt='Alur Permohonan PPID' style="margin: auto; display: block;"/>
					</div>
					<!-- /article content -->
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