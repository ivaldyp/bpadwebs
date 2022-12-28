<!DOCTYPE html>
<html lang="en">

<head>
	@include('layouts.komponen.head')
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" type="image/png" sizes="16x16" href="{{ ('/portal/img/photo/bpad-logo-05.png') }}">

	<title>BPAD</title>
	@yield('css')
	<style type="text/css">
		@media screen and (max-width: 768px) {
			.mailbox {
				/*margin-left: 50px;*/
			}
		}
		.mailbox {
			/*margin-left: 50px;*/
		}
	</style>
</head>

<body class="fix-header">
	<!-- ============================================================== -->
	<!-- Preloader -->
	<!-- ============================================================== -->
	<div class="preloader">
		<svg class="circular" viewBox="25 25 50 50">
			<circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
		</svg>
	</div>

	<!-- ============================================================== -->
	<!-- Wrapper -->
	<!-- ============================================================== -->
	<div id="wrapper">
		<!-- ============================================================== -->
		<!-- Topbar header - style you can find in pages.scss -->
		<!-- ============================================================== -->
		<nav class="navbar navbar-default navbar-static-top m-b-0">
			<div class="navbar-header">
				<div class="top-left-part">
					<!-- Logo -->
					<a class="logo" href="/portal/home">
						<span class="hidden-sm hidden-md hidden-lg"><img width="50%" src="/portal/img/photo/bpad-logo-05.png"></span>
						<span class="hidden-xs"><img width="20%" src="/portal/img/photo/bpad-logo-000.png32"><strong>BPAD</strong>
						</span>
					</a>
				</div>
				<!-- /Logo -->
				<!-- Search input and Toggle icon -->
				<ul class="nav navbar-top-links navbar-right pull-right">
					@include('layouts.komponen.profilepic')
				</ul>
				<ul class="nav navbar-top-links navbar-left pull-right">
					<li><a href="javascript:void(0)" class="open-close waves-effect waves-light visible-xs"><i class="ti-close ti-menu"></i></a></li>
					@include('layouts.komponen.notification')
				</ul>
				<ul class="nav navbar-top-links navbar-left pull-right">
					<li class="dropdown">
						<a class="dropdown-toggle waves-effect waves-light" data-toggle="dropdown" href="#"> <i class="mdi mdi-bell"></i>
							@if(count($_SESSION['notifs']) > 0)
							<div class="notify"> <span class="heartbit"></span> <span class="point"></span> </div>
							@endif
						</a>
						<ul class="dropdown-menu mailbox animated bounceInDown" style="margin-left: -100px;">
							@if(count($_SESSION['notifs']) > 0)
							{{-- <li>
								<div class="drop-title">You have 4 new messages</div>
							</li> --}}
							<li>
								<div class="message-center" style="word-wrap: normal;">
									@foreach($_SESSION['notifs'] as $key => $notif)
									<a href="/portal/notifikasi/cek/{{ $notif['jns_notif'] }}/{{ $notif['ids'] }}" style="color: black">
										<div class="mail-contnet" data-toggle="modal" data-target="#modal-notif">
											<h5 style="margin-bottom: 5px;"><strong>{{ strtoupper($notif['jns_notif']) }}</strong></h5> 
											<span class="">
												{!! $notif['message1'] !!} 
												@if($notif['message2'] != '')
													karena {!! $notif['message2'] !!}
												@endif
											</span>
											<span class="time">{{ date('d/M/Y - H:i', strtotime(str_replace('/', '-', $notif['tgl']))) }}</span> 
										</div>
									</a>
									
									@endforeach
								</div>
							</li>
							@else 
							<li>
								<div class="drop-title">You have 0 new notifications</div>
							</li>
							@endif
							<li>
								<a class="text-center" href="/portal/notifikasi/"> <strong>Lihat Semua Notifikasi</strong> <i class="fa fa-angle-right"></i> </a>
							</li>
						</ul>
						<!-- /.dropdown-messages -->
					</li>
				</ul>
			</div>
			<!-- /.navbar-header -->
			<!-- /.navbar-top-links -->
			<!-- /.navbar-static-side -->
		</nav>
		<!-- End Top Navigation -->
		<!-- ============================================================== -->
		<!-- Left Sidebar - style you can find in sidebar.scss  -->
		<!-- ============================================================== -->
		<div class="navbar-default sidebar" role="navigation">
			<div class="sidebar-nav slimscrollsidebar">
				<div class="sidebar-head">
					<h3><span class="fa-fw open-close"><i class="ti-menu hidden-xs"></i><i class="ti-close visible-xs"></i></span> <span class="hide-menu">Menu</span></h3> 
				</div>
				{!! $_SESSION['menus'] !!}
			</div>
		</div>
		<!-- ============================================================== -->
		<!-- End Left Sidebar -->
		<!-- ============================================================== -->
		<!-- ============================================================== -->
		<!-- Page Content -->
		<!-- ============================================================== -->
		
		@yield('content')
		<div id="modal-password" class="modal fade" role="dialog" >
		<div class="modal-dialog">
			<div class="modal-content">
				<form method="POST" action="/portal/home/password" class="form-horizontal">
				@csrf
					<div class="modal-header">
						<h4 class="modal-title"><b>Ubah Password</b></h4>
					</div>
					<div class="modal-body">
						<h4>Masukkan password baru  </h4>

						<div class="form-group col-md-12">
							<label for="idunit" class="col-md-2 control-label"> Password </label>
							<div class="col-md-8">
								<input autocomplete="off" type="text" name="passmd5" class="form-control" required>
							</div>
						</div>

						<div class="clearfix"></div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-danger pull-right">Simpan</button>
						<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
					</div>
				</form>
			</div>
		</div>
	</div>
		<!-- ============================================================== -->
		<!-- End Page Content -->
		<!-- ============================================================== -->
	</div>
	<!-- /#wrapper -->

	<!-- jQuery -->
	@yield('js')

</body>

</html>