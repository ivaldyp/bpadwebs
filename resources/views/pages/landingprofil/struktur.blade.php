@extends('layouts.master')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12 text-center">
			<!-- <h1 class="title"><span style="background: linear-gradient(to right, #8C0606 0%, #FF0000 50%, #8C0606 100%); -webkit-background-clip: text;-webkit-text-fill-color: transparent; font-size: 64px">PROFIL BPAD</span></h1> -->
			<h1 class="title" style="font-family: 'Century Gothic'; font-size: 64px"><span style="color: #006cb8; font-weight: bold">STRUKTUR</span> ORGANISASI</h1>
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
                        <h2 class="article-title">Struktur Organisasi - BPAD</h2>   
			            <!-- <img id="img-overlay" src="{{ ('/portal/public/img/profil/organisasi.png') }}" style="width: 100%"> -->
			            <!-- <div id="overlay"></div> -->
			            <span class='zoom' id='ex2'>
							<!-- <svgs>       
								<image href="https://mdn.mozillademos.org/files/6457/mdn_logo_only_color.png" height="200" width="200"/>
							</svg> -->
							<img src="{{ ('/portal/public/img/profil/organisasi.png') }}" width='100%' alt='Struktur Organisasi BPAD'/>
						</span>
			            <br><br>          
                        <h2 class="article-title">Struktur Organisasi - Suku Badan</h2>
			            <!-- <img id="img-overlay" src="{{ ('/portal/public/img/profil/organisasi.png') }}" style="width: 100%"> -->
			            <!-- <div id="overlay"></div> -->
			            <span class='zoom' id='ex1'>
							<img src="{{ ('/portal/public/img/profil/organisasi_suban.png') }}" width='100%' alt='Struktur Organisasi BPAD'/>
						</span>
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