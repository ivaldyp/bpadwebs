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
                        <div class="panel-heading">Kategori</div>
                    	<div class="panel-wrapper collapse in">
                            <div class="panel-body">
								@if ($access['zadd'] == 'y')
								<button class="btn btn-info btn-insert" style="margin-bottom: 10px" data-toggle="modal" data-target="#modal-insert">Tambah</button>
								@endif
								<div class="table-responsive">
									<table id="myTable" class="table table-hover">
										<thead>
											<tr>
												<th>ID</th>
												<th>Nama</th>
												<th>Suspend?</th>
												@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
												<th>Action</th>
												@endif
											</tr>
										</thead>
										<tbody>
										@foreach($kategoris as $key => $kategori)
											<tr>
												<td>{{ $kategori['ids'] }}</td>
												<td>{{ ucwords(strtolower($kategori['nmkat'])) }}</td>
												<td>{!! ($kategori['sts']) == 0 ? '<i style="color:green;" class="fa fa-check"></i>' : '<i style="color:red;" class="fa fa-times"></i>' !!}</td>
												@if ($access['zupd'] == 'y' || $access['zdel'] == 'y')
												<td>
													@if($access['zupd'] == 'y')
														<button type="button" class="btn btn-info btn-update" data-toggle="modal" data-target="#modal-update" data-ids="{{ $kategori['ids'] }}" data-nmkat="{{ $kategori['nmkat'] }}" data-sts="{{ $kategori['sts'] }}"><i class="fa fa-edit"></i></button>
													@endif
													@if($access['zdel'] == 'y')
														<button type="button" class="btn btn-danger btn-delete" data-toggle="modal" data-target="#modal-delete" data-ids="{{ $kategori['ids'] }}" data-nmkat="{{ $kategori['nmkat'] }}"><i class="fa fa-trash"></i></button>
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
						<form method="POST" action="/portal/cms/form/tambahkategori" class="form-horizontal" data-toggle="validator">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Tambah Kategori</b></h4>
							</div>
							<div class="modal-body">
								<div class="form-group">
									<label for="nmkat" class="col-md-2 control-label"><span style="color: red">*</span> Kategori </label>
									<div class="col-md-8">
										<input type="text" name="nmkat" id="modal_insert_nmkat" class="form-control" data-error="Masukkan nama kategori" autocomplete="off" required>
										<div class="help-block with-errors"></div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-2 control-label"> Suspend? </label>
									<div class="radio-list col-md-8">
										<label class="radio-inline">
											<div class="radio radio-info">
												<input type="radio" name="sts" id="sts1" value="0" data-error="Pilih salah satu">
												<label for="sts1">Ya</label> 
											</div>
										</label>
										<label class="radio-inline">
											<div class="radio radio-info">
												<input type="radio" name="sts" id="sts2" value="1" checked>
												<label for="sts2">Tidak</label>
											</div>
										</label>
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
			<div id="modal-update" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/portal/cms/form/ubahkategori" class="form-horizontal" data-toggle="validator">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Ubah Kategori</b></h4>
							</div>
							<div class="modal-body">
								<input type="hidden" name="ids" id="modal_update_ids">
								<div class="form-group">
									<label for="nmkat" class="col-md-2 control-label"><span style="color: red">*</span> Kategori </label>
									<div class="col-md-8">
										<input type="text" name="nmkat" id="modal_update_nmkat" class="form-control" data-error="Masukkan nama kategori" autocomplete="off" required>
										<div class="help-block with-errors"></div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-2 control-label"> Suspend? </label>
									<div class="radio-list col-md-8">
										<label class="radio-inline">
											<div class="radio radio-info">
												<input type="radio" name="sts" id="update_sts1" value="0" data-error="Pilih salah satu">
												<label for="update_sts1">Ya</label> 
											</div>
										</label>
										<label class="radio-inline">
											<div class="radio radio-info">
												<input type="radio" name="sts" id="update_sts2" value="1">
												<label for="update_sts2">Tidak</label>
											</div>
										</label>
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
						<form method="POST" action="/portal/cms/form/hapuskategori" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Hapus Kategori</b></h4>
							</div>
							<div class="modal-body">
								<h4 id="label_delete"></h4>
								<input type="hidden" name="ids" id="modal_delete_ids" value="">
								<input type="hidden" name="nmkat" id="modal_delete_nmkat" value="">
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
				$("#modal_update_nmkat").val($el.data('nmkat'));

				if ($el.data('sts') == 0) {
					$("#update_sts1").attr('checked', true);
				} else {
					$("#update_sts2").attr('checked', true);
				}
			});

			$('.btn-delete').on('click', function () {
				var $el = $(this);

				$("#label_delete").append('Apakah anda yakin ingin menghapus kategori <b>' + $el.data('nmkat') + '</b>?');
				$("#modal_delete_ids").val($el.data('ids'));
				$("#modal_delete_nmkat").val($el.data('nmkat'));
			});

			$("#modal-delete").on("hidden.bs.modal", function () {
				$("#label_delete").empty();
			});

			$('#myTable').DataTable();
		});
	</script>
@endsection