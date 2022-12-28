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
						<div class="panel-heading">Surat Keluar</div>
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
								<div class="row " style="margin-bottom: 10px">
									<div class="col-md-1">
										@if ($access['zadd'] == 'y')
										<a href="/portal/kepegawaian/surat keluar tambah"><button class="btn btn-info" style="margin-bottom: 10px">Tambah </button></a> 
										@endif
									</div>
									<form method="GET" action="/portal/kepegawaian/surat keluar">
										<div class=" col-md-6">
											<select class="form-control select2" name="unit" id="unit" required onchange="this.form.submit()">
											  <?php foreach ($units as $key => $unit) { ?>
												<option value="{{ $unit['kd_unit'] }}" 
												  <?php 
													if ($idunit == $unit['kd_unit']) {
														 echo "selected";
													}
												  ?>
												>[{{ $unit['kd_unit'] }}]
													@if ( strlen($unit['kd_unit'] > 2) && substr($unit['kd_unit'], 4, 2) == '51' )
														[JAKARTA PUSAT]
													@elseif ( strlen($unit['kd_unit'] > 2) && substr($unit['kd_unit'], 4, 2) == '52' )
														[JAKARTA UTARA]
													@elseif ( strlen($unit['kd_unit'] > 2) && substr($unit['kd_unit'], 4, 2) == '53' )
														[JAKARTA BARAT]
													@elseif ( strlen($unit['kd_unit'] > 2) && substr($unit['kd_unit'], 4, 2) == '54' )
														[JAKARTA SELATAN]
													@elseif ( strlen($unit['kd_unit'] > 2) && substr($unit['kd_unit'], 4, 2) == '55' )
														[JAKARTA TIMUR]
													@elseif ( strlen($unit['kd_unit'] > 2) && substr($unit['kd_unit'], 4, 2) == '56' )
														[KEPULAUAN SERIBU]
													@elseif ( strlen($unit['kd_unit'] > 2) && substr($unit['kd_unit'], 4, 2) == '06' )
														[PPBD]
													@elseif ( strlen($unit['kd_unit'] > 2) && substr($unit['kd_unit'], 4, 2) == '07' )
														[PUSDATIN ASET]
													@elseif ( strlen($unit['kd_unit'] > 2) && substr($unit['kd_unit'], 4, 2) == '08' )
														[UPMA]
													@endif
													 - {{ ($unit['kd_unit'] == '01' ? 'SEMUA' : $unit['nm_unit'])   }}</option>
											  <?php } ?>
											</select>
										  </div>
									</form>
								</div>
								<div class="row">
									<div class="table-responsive">
										<table class="myTable table table-hover">
											<thead>
												<tr>
													<th>No</th>
													<th>No Form</th>
													<th>Tgl Surat</th>
													<th>No Surat</th>
													<th>Kode</th>
													<th>Perihal</th>
													<th>Dari</th>
													<th>File</th>
													@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
													<th class="col-md-1">Action</th>
													@endif
												</tr>
											</thead>
											<tbody>
												@foreach($surats as $key => $surat)
												<tr>
													<td style="vertical-align: middle;">{{ $key + 1 }}</td>
													<td style="vertical-align: middle;">{{ $surat['no_form'] }}</td>
													<td style="vertical-align: middle;">{{ date('d-M-Y',strtotime(str_replace('/', '-', $surat['tgl_surat']))) }}</td>
													<td style="vertical-align: middle;">
														@if($surat['no_surat'])
														{{ $surat['no_surat'] }}
														@endif
													</td>
													<td style="vertical-align: middle;">{{ $surat['kode_disposisi'] }}</td>
													<td style="vertical-align: middle;">{{ $surat['perihal'] }}</td>
													<td style="vertical-align: middle;">{{ $surat['asal_surat'] }}</td>
													<td style="vertical-align: middle;">
                                                        @if($surat['nm_file'] == '' || is_null($surat['nm_file']))
                                                            -
                                                        @else
                                                            <a target="_blank" href="{{ config('app.openfilesuratkeluar') }}/{{ $surat['nm_file'] }}"><i class="fa fa-download"></i> {{ $surat['nm_file'] }}</a>
                                                        @endif
                                                    </td>
													@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
														<td style="vertical-align: middle;">
															@if($access['zupd'] == 'y')
																<form method="POST" action="/portal/kepegawaian/surat keluar ubah">
																	@csrf
																	<input type="hidden" name="ids" value="{{ $surat['ids'] }}">
																	<input type="hidden" name="no_form" value="{{ $surat['no_form'] }}">
																	<button type="submit" class="btn btn-info btn-outline btn-circle m-r-5 btn-update"><i class="ti-pencil-alt"></i></button>
																	<button type="button" class="btn btn-danger btn-outline btn-circle m-r-5 btn-delete" data-toggle="modal" data-target="#modal-delete" data-ids="{{ $surat['ids'] }}" data-noform="{{ $surat['no_form'] }}" data-nmfile="{{ $surat['nm_file'] }}" ><i class="fa fa-trash"></i></button>
																</form>
															@endif
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
						<form method="POST" action="/portal/kepegawaian/form/hapussuratkeluar" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Hapus Surat Keluar</b></h4>
							</div>
							<div class="modal-body">
								<h4 id="label_delete"></h4>
								<input type="hidden" name="ids" id="modal_delete_ids" value="">
								<input type="hidden" name="no_form" id="modal_delete_noform" value="">
								<input type="hidden" name="nm_file" id="modal_delete_nmfile" value="">
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

				$("#label_delete").append('Apakah anda yakin ingin menghapus surat dengan nomor form <b>' + $el.data('noform') + '</b>?');
				$("#modal_delete_ids").val($el.data('ids'));
				$("#modal_delete_noform").val($el.data('noform'));
				$("#modal_delete_nmfile").val($el.data('nmfile'));
			});

			$("#modal-delete").on("hidden.bs.modal", function () {
				$("#label_delete").empty();
			});

			$('.myTable').DataTable();
		});
	</script>
@endsection