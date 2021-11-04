@extends('layouts.masterhome')

@section('css')
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/portal/public/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ ('/portal/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
	<!-- Menu CSS -->
	<link href="{{ ('/portal/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
	<!-- animation CSS -->
	<link href="{{ ('/portal/public/ample/css/animate.css') }}" rel="stylesheet">
	<!-- Custom CSS -->
	<link href="{{ ('/portal/public/ample/css/style.css') }}" rel="stylesheet">
	<!-- color CSS -->
	<link href="{{ ('/portal/public/ample/css/colors/purple-dark.css') }}" id="theme" rel="stylesheet">

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
@endsection

<!-- /////////////////////////////////////////////////////////////// -->

@section('content')
	<div id="page-wrapper">
		<div class="container-fluid">
			<div class="row bg-title">
				<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
					<h4 class="page-title"><?php 
												$link = explode("/", url()->full());    
												echo str_replace('%20', ' ', ucwords(explode("?", $link[4])[0]));
											?> </h4> </div>
				<div class="col-lg-9 col-sm-8 col-md-8 col-xs-12"> 
					<ol class="breadcrumb">
						<li>{{config('app.name')}}</li>
						<?php 
							if (count($link) == 5) {
								?> 
									<li class="active"> {{ str_replace('%20', ' ', ucwords(explode("?", $link[4])[0])) }} </li>
								<?php
							} elseif (count($link) > 5) {
								?> 
									<li class="active"> {{ str_replace('%20', ' ', ucwords(explode("?", $link[4])[0])) }} </li>
									<li class="active"> {{ str_replace('%20', ' ', ucwords(explode("?", $link[5])[0])) }} </li>
								<?php
							} 
						?>
					</ol>
				</div>
				<!-- /.col-lg-12 -->
			</div>
			<div class="row">
				<div class="col-sm-12">
					@if(Session::has('message'))
						<div class="alert <?php if(Session::get('msg_num') == 1) { ?>alert-success<?php } else { ?>alert-danger<?php } ?> alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="color: white;">&times;</button>{{ Session::get('message') }}</div>
					@endif
				</div>
			</div>
			<div class="row ">
				<div class="col-md-12">
					<!-- <div class="white-box"> -->
					<div class="panel panel-info">
						<div class="panel-heading">List Form Kehadiran</div>
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
								<div class="row " style="margin-bottom: 10px">
									<div class="col-md-1">
										<a href="/portal/internal/kehadiran tambah"><button class="btn btn-info" style="margin-bottom: 10px">Tambah</button></a> 
									</div>
								</div>
								<div class="row">
									<div class="table-responsive">
										<table class="myTable table table-hover">
											<thead>
												<tr>
                                                    <th>No Form</th>
													<th>Kegiatan</th>
													<th>Tanggal</th>
													<th>Peserta</th>
                                                    <th>Lihat Respon</th>
                                                    <th>Copy URL</th>
													<th class="col-md-1">Action</th>
												</tr>
											</thead>
											<tbody>
												@foreach($kehadirans as $key => $hadir)
												<tr>
													<td style="vertical-align: middle;">{{ $hadir['no_form'] }}</td>
													<td style="vertical-align: middle;">{{ $hadir['judul'] }}<br>
                                                        <span class="text-muted">
                                                            {{ $hadir['deskripsi'] }}
                                                        </span>
                                                    </td>
													<td style="vertical-align: middle;">
														{{ date('d/M/Y', strtotime($hadir['tgl_mulai'])) }} - {{ date('d/M/Y', strtotime($hadir['tgl_end'])) }}
													</td>
													<td style="vertical-align: middle;">{{ $hadir['nm_tujuan'] }}</td>
													<td style="vertical-align: middle;">
														<!-- Respon -->
														<a href="/portal/form/{{ $hadir['no_form'] }}/lihat" target="_blank">
														<button style="background-color: transparent; border: none; padding-left: 0px">
															<i class="fa fa-eye" style="color: #2cabe3;"></i> Lihat
														</button></a><br>
														<a href="/portal/form/{{ $hadir['no_form'] }}/excel" target="_blank">
														<button style="background-color: transparent; border: none; padding-left: 0px">
															<i class="fa fa-file-excel-o" style="color: forestgreen;"></i> Excel
														</button></a>
                                                    </td>
                                                    <td style="vertical-align: middle;">
														<!-- URL -->
														<button class="copyBtn" style="background-color: transparent; border: none; padding-left: 0px" 
															data-clipboard-text="{{ $_SERVER['SERVER_NAME'] }}/portal/form/{{$hadir['no_form']}}/
																{{str_replace(' ', '_', preg_replace('/[^\da-z ]/i', '', $hadir['judul'])) }}">
															<i class="fa fa-share-alt"></i> Share Link
														</button>
                                                    </td>
													<td style="vertical-align: middle;" class="col-md-1">
														<form method="GET" action="/portal/internal/kehadiran ubah">
															@csrf
															<input type="hidden" name="no_form" value="{{ $hadir['no_form'] }}">
															<button type="submit" class="btn btn-info btn-outline btn-circle m-r-5 btn-update"><i class="ti-pencil-alt"></i></button>
															<button type="button" class="btn btn-danger btn-outline btn-circle m-r-5 btn-delete" data-toggle="modal" data-target="#modal-delete" data-ids="{{ $hadir['ids'] }}" data-no_form="{{ $hadir['no_form'] }}"><i class="fa fa-trash"></i></button>
														</form>
													</td>
												</tr>
												@endforeach
											</tbody>
										</table>
									</div>
								</div>
								
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="modal-delete" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/portal/internal/form/hapuskehadiran" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Hapus Form Kepegawaian</b></h4>
							</div>
							<div class="modal-body">
								<h4 id="label_delete"></h4>
								<input type="hidden" name="ids" id="modal_delete_ids" value="">
								<input type="hidden" name="no_form" id="modal_delete_no_form" value="">
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-danger pull-right">Hapus</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

<!-- /////////////////////////////////////////////////////////////// -->

@section('js')
	<script src="{{ ('/portal/public/ample/plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
	<!-- Bootstrap Core JavaScript -->
	<script src="{{ ('/portal/public/ample/bootstrap/dist/js/bootstrap.min.js') }}"></script>
	<!-- Menu Plugin JavaScript -->
	<script src="{{ ('/portal/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js') }}"></script>
	<!--slimscroll JavaScript -->
	<script src="{{ ('/portal/public/ample/js/jquery.slimscroll.js') }}"></script>
	<!--Wave Effects -->
	<script src="{{ ('/portal/public/ample/js/waves.js') }}"></script>
	<!-- Custom Theme JavaScript -->
	<script src="{{ ('/portal/public/ample/js/custom.min.js') }}"></script>
	<script src="{{ ('/portal/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
	<script src="{{ ('/portal/public/js/jquery.copy-to-clipboard.js') }}"></script>

	<script type="text/javascript">
		$('.copyBtn').click(function(){
		  	$(this).CopyToClipboard();
		  	alert("Link Berhasil Di Salin");
		});
	</script>

	<script>
		$(function () {

			$('.btn-delete').on('click', function () {
				var $el = $(this);

				$("#label_delete").append('Apakah anda yakin ingin menghapus Form Kehadiran ini?');
				console.log($el.data('ids'));
				$("#modal_delete_ids").val($el.data('ids'));
				$("#modal_delete_no_form").val($el.data('no_form'));
			});

			$("#modal-delete").on("hidden.bs.modal", function () {
				$("#label_delete").empty();
			});

			$('.myTable').DataTable({
				"order": [],
			});
		});
	</script>
@endsection