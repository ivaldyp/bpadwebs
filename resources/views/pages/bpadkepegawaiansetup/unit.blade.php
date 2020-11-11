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

	<style type="text/css">
		#myTable td {
			vertical-align: middle;
		}
	</style>

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
                        <div class="panel-heading">Unit</div>
                    	<div class="panel-wrapper collapse in">
                            <div class="panel-body">
								@if ($access['zadd'] == 'y')
								<button class="btn btn-info btn-insert" style="margin-bottom: 10px" data-toggle="modal" data-target="#modal-insert" data-kd_unit="0" data-nm_unit="Tidak ada" data-sao="">Tambah</button>
								@endif
								<div class="table-responsive">
									<table id="myTable" class="table table-hover display compact">
										<thead>
											<tr>
												<th>kode</th>
												<th>Nama</th>
												<th>Notes</th>
												<th>Parent</th>
												@if($access['zadd'] == 'y')
												<th>Tambah Anak</th>
												@endif
												@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
												<th>Action</th>
												@endif
											</tr>
										</thead>
										<tbody>
										@foreach($units as $key => $unit)
											<?php 
												$nowpadd = strlen($unit['kd_unit']) * 7;
											?>
											<tr>
												<td style="padding-left: {{$nowpadd}}px;" class="col-md-2">
													@if($unit['child'])
														<b>{{ $unit['kd_unit'] }} <i class="fa fa-arrow-down"></i></b>
													@else
														{{ $unit['kd_unit'] }}
													@endif
													
												</td>
												<td>{{ strtoupper($unit['nm_unit']) }}</td>
												<td>{{ strtoupper($unit['notes']) }}</td>
												<td>{{ $unit['sao'] }}</td>
											
												@if ($access['zadd'] == 'y')
												<td>
													<button type="button" class="btn btn-success btn-insert" data-toggle="modal" data-target="#modal-insert" data-kd_unit="{{ $unit['kd_unit'] }}" data-nm_unit="{{ $unit['nm_unit'] }}" data-sao="{{ $unit['kd_unit'] }}"><i class="fa fa-plus"></i></button>
												</td>
												@endif
												@if ($access['zupd'] == 'y' || $access['zdel'] == 'y')
												<td class="col-md-2">
													@if($access['zupd'] == 'y')
														<button type="button" class="btn btn-info btn-update" data-toggle="modal" data-target="#modal-update" data-kd_unit="{{ $unit['kd_unit'] }}" data-nm_unit="{{ $unit['nm_unit'] }}" data-notes="{{ $unit['notes'] }}" data-sao="{{ $unit['sao'] }}" data-notes="{{ $unit['notes'] }}"><i class="fa fa-edit"></i></button>
													@endif
													@if($access['zdel'] == 'y')
														<button type="button" class="btn btn-danger btn-delete" data-toggle="modal" data-target="#modal-delete" data-kd_unit="{{ $unit['kd_unit'] }}" data-nm_unit="{{ $unit['nm_unit'] }}" ><i class="fa fa-trash"></i></button>
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
						<form method="POST" action="/portal/kepegawaian/form/tambahunit" class="form-horizontal" data-toggle="validator" id="form-insert">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Tambah Unit</b></h4>
							</div>
							<div class="modal-body">
								<div class="form-group">
									<label for="kd_unit" class="col-md-2 control-label"><span style="color: red">*</span> Kode Unit </label>
									<div class="col-md-8">
										<input type="text" name="kd_unit" id="modal_insert_kd_unit" class="form-control" data-error="Masukkan kode unit" autocomplete="off" required>
										<div class="help-block with-errors"></div>
									</div>
								</div>

								<div class="form-group">
									<label for="nm_unit" class="col-md-2 control-label"><span style="color: red">*</span> Nama </label>
									<div class="col-md-8">
										<input type="text" name="nm_unit" id="modal_insert_nm_unit" class="form-control" data-error="Masukkan nama unit" autocomplete="off" required>
										<div class="help-block with-errors"></div>
									</div>
								</div>

								<div class="form-group">
									<label for="notes" class="col-md-2 control-label"><span style="color: red">*</span> Notes </label>
									<div class="col-md-8">
										<input type="text" name="notes" id="modal_insert_notes" class="form-control" data-error="Masukkan notes unit" autocomplete="off" required>
										<div class="help-block with-errors"></div>
									</div>
								</div>

								<div class="form-group">
									<label for="sao" class="col-md-2 control-label"> Parent </label>
									<div class="col-md-8">
										<input type="text" name="sao" id="modal_insert_sao" class="form-control" disabled>
										<input type="hidden" name="sao" id="modal_insert_sao_real" class="form-control">
										<div class="help-block with-errors"></div>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-success pull-right" id="btn-confirm-insert">Simpan</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div id="modal-update" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/portal/kepegawaian/form/ubahunit" class="form-horizontal" data-toggle="validator">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Ubah Unit</b></h4>
							</div>
							<div class="modal-body">
								<input type="hidden" name="ids" id="modal_update_ids">
								<div class="form-group">
									<label for="kd_unit" class="col-md-2 control-label"><span style="color: red">*</span> Kode Unit </label>
									<div class="col-md-8">
										<input type="text" name="kd_unit" id="modal_update_kd_unit" class="form-control" data-error="Masukkan nama kategori" autocomplete="off" required>
										<div class="help-block with-errors"></div>
									</div>
								</div>

								<div class="form-group">
									<label for="nm_unit" class="col-md-2 control-label"><span style="color: red">*</span> Nama </label>
									<div class="col-md-8">
										<input type="text" name="nm_unit" id="modal_update_nm_unit" class="form-control" data-error="Masukkan nama kategori" autocomplete="off" required>
										<div class="help-block with-errors"></div>
									</div>
								</div>

								<div class="form-group">
									<label for="notes" class="col-md-2 control-label"><span style="color: red">*</span> Notes </label>
									<div class="col-md-8">
										<input type="text" name="notes" id="modal_update_notes" class="form-control" data-error="Masukkan nama kategori" autocomplete="off" required>
										<div class="help-block with-errors"></div>
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
						<form method="POST" action="/portal/kepegawaian/form/hapusunit" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Hapus Unit</b></h4>
							</div>
							<div class="modal-body">
								<h4 id="label_delete"></h4>
								<input type="hidden" name="kd_unit" id="modal_delete_kd_unit" value="">
								<input type="hidden" name="nm_unit" id="modal_delete_nm_unit" value="">
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
	<script src="{{ ('/portal/public/ample/js/validator.js') }}"></script>


	<script>
		$(function () {

			$('.btn-insert').on('click', function () {
				var $el = $(this);

				$("#modal_insert_kd_unit").val($el.data('sao'));
				$("#modal_insert_sao").val("("+$el.data('kd_unit')+") - "+$el.data('nm_unit'));
				$("#modal_insert_sao_real").val($el.data('kd_unit'));
			});

			$('.btn-update').on('click', function () {
				var $el = $(this);

				$("#modal_update_kd_unit").val($el.data('kd_unit'));
				$("#modal_update_nm_unit").val($el.data('nm_unit'));
				$("#modal_update_notes").val($el.data('notes'));
				// $("#modal_update_sao").val($el.data('sao'));
			});

			$('.btn-delete').on('click', function () {
				var $el = $(this);

				$("#label_delete").append('Apakah anda yakin ingin menghapus unit <b>' + $el.data('nm_unit') + '</b> beserta seluruh childnya?');
				$("#modal_delete_kd_unit").val($el.data('kd_unit'));
				$("#modal_delete_nm_unit").val($el.data('nm_unit'));
			});

			$("#modal-delete").on("hidden.bs.modal", function () {
				$("#label_delete").empty();
			});

			$('#myTable').DataTable({
				"paging":   false,
		        "ordering": false,
		        // "info":     false,
				// "bSort": false,
				"aaSorting": [],
			});
		});
	</script>
@endsection