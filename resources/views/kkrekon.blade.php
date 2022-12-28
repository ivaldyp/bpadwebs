<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

	<title>BPAD DKI Jakarta</title>
	<link rel="shortcut icon" type="image/x-icon" href="{{ ('/portal/img/photo/bpad-logo-00.png') }}" />

	<!-- Google font -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:300,400%7CSource+Sans+Pro:700" rel="stylesheet">

	<!-- Bootstrap -->
	<link type="text/css" rel="stylesheet" href="{{ ('/portal/css/bootstrap.min.css') }}" />

	<!-- Owl Carousel -->
	<link type="text/css" rel="stylesheet" href="{{ ('/portal/css/owl.carousel.css') }}" />
	<link type="text/css" rel="stylesheet" href="{{ ('/portal/css/owl.theme.default.css') }}" />

	<!-- Font Awesome Icon -->
	<link rel="stylesheet" href="{{ ('/portal/css/font-awesome.min.css') }}" />

	<!-- Custom stlylesheet -->
	<link type="text/css" rel="stylesheet" href="{{ ('/portal/css/style.css') }}" />

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
						<a  href="{{ url('/') }}"><img src="{{ ('/portal/img/photo/bpad-logo-04b.png') }}" alt="logo" height="85"></a>
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
				<h2 class="article-title">Kertas Kerja Rekon SMT 1 2020</h2><br>
				<ul>
					<li><a href="{{ url('/kkrekon/provinsi') }}"><h3>Provinsi</h3></a></li>
					<li><a href="{{ url('/kkrekon/pusat') }}"><h3>Jakarta Pusat</h3></a></li>
					<li><a href="{{ url('/kkrekon/utara') }}"><h3>Jakarta utara</h3></a></li>
					<li><a href="{{ url('/kkrekon/barat') }}"><h3>Jakarta barat</h3></a></li>
					<li><a href="{{ url('/kkrekon/selatan') }}"><h3>Jakarta selatan</h3></a></li>
					<li><a href="{{ url('/kkrekon/timur') }}"><h3>Jakarta timur</h3></a></li>
					<li><a href="{{ url('/kkrekon/seribu') }}"><h3>Pulau seribu</h3></a></li>
				</ul>

			</div>
			<!-- /row -->

			<hr>
		
		</div>
		<!-- /container -->
	</footer>
	<!-- /FOOTER -->

	<!-- jQuery Plugins -->
	<script src="{{ ('/portal/js/jquery.min.js') }}"></script>
	<script src="{{ ('/portal/js/bootstrap.min.js') }}"></script>
	<script src="{{ ('/portal/js/owl.carousel.min.js') }}"></script>
	<script src="{{ ('/portal/js/jquery.stellar.min.js') }}"></script>
	<script src="{{ ('/portal/js/main.js') }}"></script>

</body>

</html>
