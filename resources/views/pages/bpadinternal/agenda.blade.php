@extends('layouts.masterhome')

@section('css')
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/portal/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ ('/portal/ample/plugins/bower_components/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
	<!-- Menu CSS -->
	<link href="{{ ('/portal/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
	<!-- animation CSS -->
	<link href="{{ ('/portal/ample/css/animate.css') }}" rel="stylesheet">
	<!-- Custom CSS -->
	<link href="{{ ('/portal/ample/css/style.css') }}" rel="stylesheet">
	<!-- color CSS -->
	<link href="{{ ('/portal/ample/css/colors/purple-dark.css') }}" id="theme" rel="stylesheet">

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
					<div class="panel panel-default">
						<div class="panel-heading">Agenda</div>
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
								<div class="row " style="margin-bottom: 10px">
									<div class="col-md-1">
										@if ($access['zadd'] == 'y')
										<a href="/portal/internal/agenda tambah"><button class="btn btn-info" style="margin-bottom: 10px">Tambah</button></a> 
										@endif
									</div>
								</div>
								<div class="row">
									<div class="table-responsive">
										<table class="myTable table table-hover table-striped">
											<thead>
												<tr>
													<th>No</th>
													<th class="col-md-2">Waktu</th>
													<th>Deskripsi</th>
													<th>File</th>
													<th>Editor</th>
													<th>Untuk</th>
													<th style="text-align: center;">Approved</th>
													@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
													<th class="col-md-1">Action</th>
													@endif
												</tr>
											</thead>
											<tbody>
												@foreach($agendas as $key => $agenda)
												<tr>
													<td>{{ $key + 1 }}</td>
													<td class="col-md-2">
														{{ date('d/m/Y', strtotime(str_replace('/', '-', $agenda['dtanggal']))) }}
														<br>
														<span class="text-muted">{{ date('H:i:s', strtotime($agenda['dtanggal'])) }}</span>
													</td>
													<td>{{ $agenda['ddesk'] }}</td>
													<td><a target="_blank" href="{{ config('app.openfileagenda') }}/{{ $agenda['dfile'] }}"><i class="fa fa-download"></i> {{ $agenda['dfile'] }}</a></td>
													<?php 
														$split = explode(",", $agenda['tipe']);
													?>
													<th>{{ $agenda['an'] }}</th>
													<td>
														@foreach($split as $data)
														<span class="label label-info">{{ $data }}</span>
														<br>
														@endforeach
													</td>
													<td class="text-center">
														{!! 
															($agenda['appr']) == 'Y' ? 
															'<i style="color:green;" class="fa fa-check"></i><br><span style="color: white">1</span>' : 
															'<i style="color:red;" class="fa fa-times"></i><br><span style="color: white">0</span>' 
														!!}
													</td>
													@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
														<td class="col-md-1">
															<form method="POST" action="/portal/internal/agenda ubah">
																@csrf
																@if($access['zupd'] == 'y')
																
																	<input type="hidden" name="ids" value="{{ $agenda['ids'] }}">
																	<button type="submit" class="btn btn-info btn-outline btn-circle m-r-5 btn-update"><i class="ti-pencil-alt"></i></button>
																	
																@endif
																@if($access['zdel'] == 'y')
																	<button type="button" class="btn btn-danger btn-outline btn-circle m-r-5 btn-delete" data-toggle="modal" data-target="#modal-delete" data-ids="{{ $agenda['ids'] }}" data-dfile="{{ $agenda['dfile'] }}" ><i class="fa fa-trash"></i></button>
																@endif
															</form>
														</td>
													@endif
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
						<form method="POST" action="/portal/internal/form/hapusagenda" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Hapus Agenda</b></h4>
							</div>
							<div class="modal-body">
								<h4 id="label_delete"></h4>
								<input type="hidden" name="ids" id="modal_delete_ids" value="">
								<input type="hidden" name="dfile" id="modal_delete_dfile" value="">
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
	<script src="{{ ('/portal/ample/plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
	<!-- Bootstrap Core JavaScript -->
	<script src="{{ ('/portal/ample/bootstrap/dist/js/bootstrap.min.js') }}"></script>
	<!-- Menu Plugin JavaScript -->
	<script src="{{ ('/portal/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js') }}"></script>
	<!--slimscroll JavaScript -->
	<script src="{{ ('/portal/ample/js/jquery.slimscroll.js') }}"></script>
	<!--Wave Effects -->
	<script src="{{ ('/portal/ample/js/waves.js') }}"></script>
	<!-- Custom Theme JavaScript -->
	<script src="{{ ('/portal/ample/js/custom.min.js') }}"></script>
	<script src="{{ ('/portal/ample/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>

	<script>
		$(function () {

			$('.btn-delete').on('click', function () {
				var $el = $(this);

				$("#label_delete").append('Apakah anda yakin ingin menghapus agenda tersebut?');
				$("#modal_delete_ids").val($el.data('ids'));
				$("#modal_delete_dfile").val($el.data('dfile'));
			});

			$("#modal-delete").on("hidden.bs.modal", function () {
				$("#label_delete").empty();
			});

			$('.myTable').DataTable();
		});
	</script>
@endsection