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
				<div class="col-md-2"></div>
				<div class="col-md-8">
					<!-- <div class="white-box"> -->
					<div class="panel panel-default">
                        <div class="panel-heading">Ruang</div>
                    	<div class="panel-wrapper collapse in">
                            <div class="panel-body">
								@if ($access['zadd'] == 'y')
								<button class="btn btn-info btn-insert" style="margin-bottom: 10px" data-toggle="modal" data-target="#modal-insert">Tambah</button>
								@endif
								<div class="table-responsive">
									<table id="myTable" class="table table-hover">
										<thead>
											<tr>
												<th>No</th>
												<th>Nama</th>
												<th>Unit</th>
												<th>Lokasi</th>
												@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
												<th>Action</th>
												@endif
											</tr>
										</thead>
										<tbody>
										@foreach($ruangs as $key => $ruang)
											<tr>
												<td>{{ $ruang['ids'] }}</td>
												<td>{{ ucwords(strtolower($ruang['nm_ruang'])) }}<br><span class="text-muted">Maks {{ $ruang['jumlah'] ? ucwords(strtolower($ruang['jumlah'])) : 0 }} orang</span></td>
												<td>{{ ucwords(strtolower($ruang['unit'])) }}</td>
												<td>{{ ucwords(strtolower($ruang['lokasi'])) }}<br><span class="text-muted">Lantai {{ ucwords(strtolower($ruang['lantai'])) }}</span></td>
												@if ($access['zupd'] == 'y' || $access['zdel'] == 'y')
												<td>
													@if($access['zupd'] == 'y')
														<button type="button" class="btn btn-info btn-update" data-toggle="modal" data-target="#modal-update" data-ids="{{ $ruang['ids'] }}" data-nm_ruang="{{ $ruang['nm_ruang'] }}" data-kd_unit="{{ $ruang['kd_unit'] }}" data-unit="{{ $ruang['unit'] }}" data-kd_lokasi="{{ $ruang['kd_lokasi'] }}" data-lokasi="{{ $ruang['lokasi'] }}" data-lantai="{{ $ruang['lantai'] }}" data-jumlah="{{ $ruang['jumlah'] }}"><i class="fa fa-edit"></i></button>
													@endif
													@if($access['zdel'] == 'y')
														<button type="button" class="btn btn-danger btn-delete" data-toggle="modal" data-target="#modal-delete" data-ids="{{ $ruang['ids'] }}" data-nm_ruang="{{ $ruang['nm_ruang'] }}"><i class="fa fa-trash"></i></button>
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
					<!-- </div> -->
				</div>
			</div>
			<div id="modal-insert" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/portal/booking/form/tambahruang" class="form-horizontal" data-toggle="validator">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Tambah Ruang</b></h4>
							</div>
							<div class="modal-body">
								<div class="form-group">
									<label for="nm_ruang" class="col-md-2 control-label"><span style="color: red">*</span> Nama Ruang </label>
									<div class="col-md-8">
										<input type="text" name="nm_ruang" id="modal_insert_nm_ruang" class="form-control" data-error="Masukkan nama ruang" autocomplete="off" required>
										<div class="help-block with-errors"></div>
									</div>
								</div>
								<div class="form-group">
									<label for="unit" class="col-md-2 control-label"> Unit </label>
									<div class="col-md-8">
										<select class="form-control select2" name="unit" id="unit">
											@foreach($units as $unit)
												<option value="{{ $unit['kd_unit'] }}::{{ $unit['nm_unit'] }}" > {{ $unit['kd_unit'] }} - {{ $unit['nm_unit'] }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="lokasi" class="col-md-2 control-label"> Lokasi </label>
									<div class="col-md-8">
										<select class="form-control select2" name="lokasi" id="lokasi">
											@foreach($lokasis as $lokasi)
												<option value="{{ $lokasi['kd_lok'] }}::{{ $lokasi['nm_lok'] }}" > {{ $lokasi['kd_lok'] }} - {{ $lokasi['nm_lok'] }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="lantai" class="col-md-2 control-label"> Lantai </label>
									<div class="col-md-4">
										<input type="text" name="lantai" id="modal_insert_lantai" class="form-control" autocomplete="off">
									</div>
								</div>
								<div class="form-group">
									<label for="jumlah" class="col-md-2 control-label"> Kapasitas </label>
									<div class="col-md-4">
										<input type="text" name="jumlah" id="modal_insert_jumlah" class="form-control" autocomplete="off">
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-success pull-right">Simpan</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div id="modal-update" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/portal/booking/form/ubahruang" class="form-horizontal" data-toggle="validator">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Ubah Kategori</b></h4>
							</div>
							<div class="modal-body">
								<input type="hidden" name="ids" id="modal_update_ids">
								<div class="form-group">
									<label for="nm_ruang" class="col-md-2 control-label"><span style="color: red">*</span> Nama Ruang </label>
									<div class="col-md-8">
										<input type="text" name="nm_ruang" id="modal_update_nm_ruang" class="form-control" data-error="Masukkan nama ruang" autocomplete="off" required>
										<div class="help-block with-errors"></div>
									</div>
								</div>
								<div class="form-group">
									<label for="unit" class="col-md-2 control-label"> Unit </label>
									<div class="col-md-8">
										<select class="form-control select2" name="unit" id="modal_update_unit">
											@foreach($units as $unit)
												<option value="{{ $unit['kd_unit'] }}::{{ $unit['nm_unit'] }}" > {{ $unit['kd_unit'] }} - {{ $unit['nm_unit'] }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="lokasi" class="col-md-2 control-label"> Lokasi </label>
									<div class="col-md-8">
										<select class="form-control select2" name="lokasi" id="modal_update_lokasi">
											@foreach($lokasis as $lokasi)
												<option value="{{ $lokasi['kd_lok'] }}::{{ $lokasi['nm_lok'] }}" > {{ $lokasi['kd_lok'] }} - {{ $lokasi['nm_lok'] }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="lantai" class="col-md-2 control-label"> Lantai </label>
									<div class="col-md-4">
										<input type="text" name="lantai" id="modal_update_lantai" class="form-control" autocomplete="off">
									</div>
								</div>
								<div class="form-group">
									<label for="jumlah" class="col-md-2 control-label"> Kapasitas </label>
									<div class="col-md-4">
										<input type="text" name="jumlah" id="modal_update_jumlah" class="form-control" autocomplete="off">
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-success pull-right">Simpan</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div id="modal-delete" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/portal/booking/form/hapusruang" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Hapus Ruang</b></h4>
							</div>
							<div class="modal-body">
								<h4 id="label_delete"></h4>
								<input type="hidden" name="ids" id="modal_delete_ids" value="">
								<input type="hidden" name="nm_ruang" id="modal_delete_nm_ruang" value="">
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
	<script src="{{ ('/portal/ample/js/validator.js') }}"></script>


	<script>
		$(function () {

			$('.btn-update').on('click', function () {
				var $el = $(this);

				$("#modal_update_ids").val($el.data('ids'));
				$("#modal_update_nm_ruang").val($el.data('nm_ruang'));
				$("#modal_update_lantai").val($el.data('lantai'));
				$("#modal_update_lokasi").val($el.data('kd_lokasi') + "::" + $el.data('lokasi'));
				$("#modal_update_unit").val($el.data('kd_unit') + "::" + $el.data('unit'));
				$("#modal_update_nm_ruang").val($el.data('nm_ruang'));
				$("#modal_update_jumlah").val($el.data('jumlah'));
			});

			$('.btn-delete').on('click', function () {
				var $el = $(this);

				$("#label_delete").append('Apakah anda yakin ingin menghapus ruang <b>' + $el.data('nm_ruang') + '</b>?');
				$("#modal_delete_ids").val($el.data('ids'));
				$("#modal_delete_nm_ruang").val($el.data('nm_ruang'));
			});

			$("#modal-delete").on("hidden.bs.modal", function () {
				$("#label_delete").empty();
			});

			$('#myTable').DataTable();
		});
	</script>
@endsection