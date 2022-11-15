<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    
	<title>BPAD Provinsi DKI Jakarta</title>
	<meta name="keywords" content="HTML5 Theme" />
	<meta name="description" content="BPAD PORTAL PAGE">
	<meta name="author" content="Pusdatin BPAD">

	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

	<title>BPAD DKI Jakarta</title>
	<link rel="shortcut icon" type="image/x-icon" href="{{ ('/portal/public/img/photo/bpad-logo-00.png') }}" />

	<!-- Google font -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:300,400%7CSource+Sans+Pro:700" rel="stylesheet">

	<!-- Bootstrap -->
	<link type="text/css" rel="stylesheet" href="{{ ('/portal/public/css/bootstrap.min.css') }}" />

	<!-- Owl Carousel -->
	<link type="text/css" rel="stylesheet" href="{{ ('/portal/public/css/owl.carousel.css') }}" />
	<link type="text/css" rel="stylesheet" href="{{ ('/portal/public/css/owl.theme.default.css') }}" />

	<!-- Font Awesome Icon -->
	<link rel="stylesheet" href="{{ ('/portal/public/css/font-awesome.min.css') }}" />

	<!-- Custom stlylesheet -->
	<link type="text/css" rel="stylesheet" href="{{ ('/portal/public/css/style.css') }}" />

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

</head>

<body>
	<!-- HEADER -->
	<header id="home" style="height: 100px">
		<!-- NAVGATION -->
		<nav id="main-navbar" style="top: 20px">
			<div class="container">
				<div class="navbar-header">
					<!-- Logo -->
					<div class="navbar-brand">
						<a  href="{{ url('/') }}"><img src="/portal/public/img/photo/bpad-logo-04b.png32" alt="logo" height="85"></a>
					</div>
					<!-- Logo -->

					<!-- Mobile toggle -->
					<button class="navbar-toggle-btn">
							<i class="fa fa-bars"></i>
						</button>
					<!-- Mobile toggle -->

					<!-- Mobile Search toggle -->
					<button class="search-toggle-btn">
							<i class="fa fa-search"></i>
						</button>
					<!-- Mobile Search toggle -->
				</div>

				<!-- Search -->
				<!-- <div class="navbar-search">
					<button class="search-btn"><i class="fa fa-search"></i></button>
					<div class="search-form">
						<form>
							<input class="input" type="text" name="search" placeholder="Search">
						</form>
					</div>
				</div> -->
				<!-- Search -->

				<!-- Nav menu -->
				<ul class="navbar-menu nav navbar-nav navbar-right">
					<li><a href="{{ url('/') }}">Home</a></li>
					<li class="has-dropdown"><a href="#" target="_blank">Profil</a>
						<ul class="dropdown" style="list-style: none; padding: 0;">
							<li><a href="{{ route('profil.visimisi') }}">Visi Misi</a></li>
							<li><a href="{{ route('profil.tupoksi') }}">Tugas & Fungsi</a></li>
							<li><a href="{{ route('profil.struktur') }}">Struktur Organisasi</a></li>
							<li><a href="{{ route('profil.profilpejabat') }}">Profil Pejabat</a></li>
						</ul>
					</li>
					<li class="has-dropdown"><a href="#" target="_blank">Produk</a>
						<ul class="dropdown" style="list-style: none; padding: 0;">
							<li><a href="http://aset.jakarta.go.id" target="_blank">Internal</a></li>
							<li class="has-dropdown"><a href="#">Publik</a>
								<ul class="dropdown" style="list-style: none; padding: 0;">
									<li><a href="https://bpad.jakarta.go.id/epemanfaatan/" target="_blank">Pemanfaatan</a></li>
									<li><a href="https://bpad.jakarta.go.id/produkhukum/" target="_blank">Produk Hukum</a></li>
								</ul>
							</li>
						</ul>
					</li>
					<li class="has-dropdown"><a href="#">Konten</a>
						<ul class="dropdown" style="list-style: none; padding: 0;">
							<li><a href="{{ url('content/berita') }}">Berita</a></li>
							<li><a href="{{ url('content/foto') }}">Foto</a></li>
							<li><a href="{{ url('content/video') }}">Video</a></li>
							<li><a href="http://simaster.jakarta.go.id/internal.download/index.php?x=Moderator&y=B5B320392109ABC1986FDBF67DB15E71" target="_blank">Download</a></li>
						</ul>
					</li>
					<li class="has-dropdown"><a href="#">Lainnya</a>
						<ul class="dropdown" style="list-style: none; padding: 0;">
							<li><a href="http://bpad.jakarta.go.id/portal/ceksurat" target="_blank">Cek Surat</a></li>
							<li><a href="https://webmail.bpadjakarta.id/" target="_blank">Webmail</a></li>
							<li><a href="https://ppid.jakarta.go.id/" target="_blank">PPID</a></li>
							<li><a href="https://aset.jakarta.go.id/brandgang/index.aspx?id=permohonan/" target="_blank">Permohonan Brandgang</a></li>
							<li><a href="https://aset.jakarta.go.id/brandgang/index.aspx?id=monitoring/" target="_blank">Monitoring Brandgang</a></li>
							<li><a href="{{ url('esiappe/masuk') }}">Absensi Online e-SIAPPE</a></li>
						</ul>
					</li>	
					<li style="background: #006cb8;"><a style="color: white" href="{{ url('login') }}">
						@if(Auth::check())
						Masuk
						@else
						Login
						@endif
					</a></li>
				</ul>

				<!-- Nav menu -->
			</div>
		</nav>
		<!-- /NAVGATION -->
	</header>
	<!-- /HEADER -->

	@yield('content')

	<!-- FOOTER -->
	<footer id="footer" class="section">
		<!-- container -->
		<div class="container">
			<!-- row -->
			<div class="row">
				<!-- footer contact -->
				<div class="col-md-4">
					<div class="footer">
						<div class="footer-logo" style="margin-top: 20px">
							<a href="https://bpad.jakarta.go.id/"><img style="padding: 0 10px;" src="{{ ('/portal/public/img/photo/plusjakartalogo2.png32') }}" alt="" height="35" width="auto"></a>
							<a href="https://jamc.jakarta.go.id/"><img style="padding: 0 10px;" src="{{ ('/portal/public/img/photo/jamc_logo.png') }}" alt="" height="35" width="auto"></a>
						</div>
						<address>
							<span style="font-weight: bold;">Gedung Dinas Teknis</span><br>
							Jl. Abdul Muis No. 66 (Lt. 4)<br>
							Tanah Abang-Jakarta Pusat
						</address>
						<ul class="footer-contact" style="list-style: none; padding: 0;">
							<li><i class="fa fa-phone"></i> (021) 3865745 - (021) 3865745</li>
							<li><i class="fa fa-envelope"></i> bpad@jakarta.go.id</li>
						</ul>
					</div>
				</div>
				<!-- /footer contact -->

				<!-- footer galery -->
				<div class="col-md-4">
					<div class="footer">
						<h3 class="footer-title">Lokasi</h3>
						<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d991.6618577592792!2d106.81810288811926!3d-6.17792776640271!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x19ca3e98a96811ee!2sBPAD%20Provinsi%20DKI%20Jakarta!5e0!3m2!1sen!2sid!4v1591692810287!5m2!1sen!2sid" style="height: 200px" frameborder="0" style="border:0;" allowfullscreen="true" aria-hidden="false" tabindex="0"></iframe>
					</div>
				</div>
				<!-- /footer galery -->

				<!-- footer newsletter -->
				<div class="col-md-4">
					<div class="footer">
						<h3 class="footer-title">Bantuan dan Saran</h3>
						<!-- <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor     ididunt.</p> -->
						<form class="footer-newsletter" action="{{ url('mail') }}" method="post">
							@csrf
							<!-- <input class="input" type="email" placeholder="Enter your email"> -->
							<textarea class="input" placeholder="Ketik saran dan masukkan" name="isi" id="isi" required></textarea>
							<input class="input" type="email" name="sender" placeholder="Masukkan email" autocomplete="off" required id="sender">
							<button class="primary-button" type="submit" id="mail_submit">Kirim</button>
						</form>
						<ul class="footer-social text-center">
							<!-- <li><a href="JavaScript:void(0);"><i class="fa fa-facebook"></i></a></li> -->
							<li><a target="_blank" href="https://twitter.com/BPAD_Jakarta"><i class="fa fa-twitter"></i></a></li>
							<li><a target="_blank" href="https://www.youtube.com/channel/UC_S1y4yWE7nngg66DfG_hxg/"><i class="fa fa-youtube"></i></a></li>
							<li><a target="_blank" href="https://instagram.com/bpad_jakarta"><i class="fa fa-instagram"></i></a></li>
						</ul>
					</div>
				</div>
				<!-- /footer newsletter -->

				<!-- footer galery -->
				<!-- <div class="col-md-4">
					<div class="footer">
						<h3 class="footer-title">Galery</h3>
						<ul class="footer-galery">
							<li><a href="#"><img src="./img/galery-1.jpg" alt=""></a></li>
							<li><a href="#"><img src="./img/galery-2.jpg" alt=""></a></li>
							<li><a href="#"><img src="./img/galery-3.jpg" alt=""></a></li>
							<li><a href="#"><img src="./img/galery-4.jpg" alt=""></a></li>
							<li><a href="#"><img src="./img/galery-5.jpg" alt=""></a></li>
							<li><a href="#"><img src="./img/galery-6.jpg" alt=""></a></li>
						</ul>
					</div>
				</div> -->
				<!-- /footer galery -->

				<!-- footer newsletter -->
				<!-- <div class="col-md-4">
					<div class="footer">
						<h3 class="footer-title">Newsletter</h3>
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt.</p>
						<form class="footer-newsletter">
							<input class="input" type="email" placeholder="Enter your email">
							<button class="primary-button">Subscribe</button>
						</form>
						<ul class="footer-social">
							<li><a href="#"><i class="fa fa-facebook"></i></a></li>
							<li><a href="#"><i class="fa fa-twitter"></i></a></li>
							<li><a href="#"><i class="fa fa-google-plus"></i></a></li>
							<li><a href="#"><i class="fa fa-instagram"></i></a></li>
							<li><a href="#"><i class="fa fa-pinterest"></i></a></li>
						</ul>
					</div>
				</div> -->
				<!-- /footer newsletter -->
			</div>
			<!-- /row -->

			<!-- footer copyright & nav -->
			<div id="footer-bottom" class="row">
				<div class="col-sm-12">
					<div class="col-sm-6">
						<div class="footer-copyright">
							<span>&copy; Copyright BPAD DKI Jakarta.</span><br>
							Powered by <a href="JavaScript:void(0);"><span style="cursor: default;">Pusdatin BPAD Provinsi DKI Jakarta</span></a>
						</div>
					</div>
					<div class="col-sm-6" >
						<div class="footer-copyright pull-right">
						  	<!-- Histats.com  (div with counter) -->
                            <div id="histats_counter"></div>
                            <!-- Histats.com  START  (aync)-->
                            <script type="text/javascript">var _Hasync= _Hasync|| [];
                            _Hasync.push(['Histats.start', '1,3757099,4,202,118,45,00011000']);
                            _Hasync.push(['Histats.fasi', '1']);
                            _Hasync.push(['Histats.track_hits', '']);
                            (function() {
                            var hs = document.createElement('script'); hs.type = 'text/javascript'; hs.async = true;
                            hs.src = ('//s10.histats.com/js15_as.js');
                            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(hs);
                            })();</script>
                            <noscript><a href="/" target="_blank"><img  src="//sstatic1.histats.com/0.gif?3757099&101" alt="" border="0"></a></noscript>
                            <!-- Histats.com  END  -->
							<!-- <img src="{{ ('/portal/public/img/photo/plusjakartalogo2.png') }}" alt="" height="100"> -->
						</div>
					</div>
				</div>
			</div>
			<!-- /footer copyright & nav -->
		</div>
		<!-- /container -->
	</footer>
	<!-- /FOOTER -->

	<!-- jQuery Plugins -->
	<script src="{{ ('/portal/public/js/jquery.min.js') }}"></script>
	<script src="{{ ('/portal/public/js/bootstrap.min.js') }}"></script>
	<script src="{{ ('/portal/public/js/owl.carousel.min.js') }}"></script>
	<script src="{{ ('/portal/public/js/jquery.stellar.min.js') }}"></script>
	<script src="{{ ('/portal/public/js/main.js') }}"></script>

	<!-- <script src='http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script> -->
	<script src="{{ ('/portal/public/js/jquery.zoom.js') }}"></script>
	<script src="{{ ('/portal/public/js/jquery.copy-to-clipboard.js') }}"></script>
	<script type="text/javascript">
		$('.copyBtn').click(function(){
		  	$(this).CopyToClipboard();
		  	alert("Link Berhasil Di Salin");
		});
	</script>
	<script type="text/javascript">
		var main = function(){
			var ads = $('#ads')
			
			$(document).scroll(function(){
				if ( $(this).scrollTop() >= $(window).height() - ads.height() ){
				ads.removeClass('bottom').addClass('top')
				} else {
				ads.removeClass('top').addClass('bottom')
				}
			})
		}
		$(document).ready(main);
	</script>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#adsbutton").click(function(){
				$("#ads").hide();
			});
			$("#mail_submit").click(function(){
				var isi = $("#isi").val();
				var sender = $("#sender").val();

				if (isi != '' && sender != '') {
					alert("Saran berhasil tersimpan");
				}
			});
		});
	</script>
</body>

</html>
