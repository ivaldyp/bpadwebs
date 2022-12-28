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
				<div class="col-md-1"></div>
				<div class="col-md-10">
					<!-- <div class="white-box"> -->
					<div class="panel panel-info">
                        <div class="panel-heading">Produk BPAD</div>
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
												<th>name</th>
												<th>href</th>
												<th>img</th>
												@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
												<th>Action</th>
												@endif
											</tr>
										</thead>
										<tbody>
										@foreach($produks as $key => $produk)
											<tr>
												<td>{{ $produk['ids'] }}</td>
												<td>{{ $produk['name'] }}</td>
												<td>{{ $produk['href'] }}</td>
												<td><img src="{{ $produk['source'] }}" alt="{{ $produk['name'] }}" width="100" ></td>
												
												@if ($access['zupd'] == 'y' || $access['zdel'] == 'y')
												<td>
													@if($access['zupd'] == 'y')
														<button type="button" class="btn btn-info btn-update" data-toggle="modal" data-target="#modal-update" data-ids="{{ $produk['ids'] }}" data-name="{{ $produk['name'] }}" data-href="{{ $produk['href'] }}" data-source="{{ $produk['source'] }}"><i class="fa fa-edit"></i></button>
													@endif
													@if($access['zdel'] == 'y')
														<button type="button" class="btn btn-danger btn-delete" data-toggle="modal" data-target="#modal-delete" data-ids="{{ $produk['ids'] }}" data-name="{{ $produk['name'] }}" data-href="{{ $produk['href'] }}" data-source="{{ $produk['source'] }}"><i class="fa fa-trash"></i></button>
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
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<form method="POST" action="/portal/cms/form/tambahproduk" class="form-horizontal" data-toggle="validator" enctype="multipart/form-data">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Tambah Produk</b></h4>
							</div>
							<div class="modal-body">
								<div class="form-group">
									<label for="name" class="col-md-2 control-label"><span style="color: red">*</span> Nama </label>
									<div class="col-md-8">
										<input type="text" name="name" id="modal_insert_name" class="form-control" data-error="Masukkan nama produk" autocomplete="off" required>
										<div class="help-block with-errors"></div>
									</div>
								</div>
								<div class="form-group">
									<label for="href" class="col-md-2 control-label"><span style="color: red">*</span> URL </label>
									<div class="col-md-8">
										<input type="text" name="href" id="modal_insert_href" class="form-control" data-error="Masukkan link" autocomplete="off" required>
										<div class="help-block with-errors"></div>
									</div>
								</div>
								<div class="form-group">
									<label for="imgstatic" class="col-lg-2 control-label"> Upload Foto <br> <span style="font-size: 10px">Hanya berupa .JPG, .JPEG, dan .PNG</span> </label>
									<div class="col-lg-8">
										<input type="file" class="form-control" id="imgstatic" name="imgstatic">
										<span>* Ukuran gambar 500 x 500</span>
									</div>
								</div>
								<div class="form-group">
									<label for="imgactive" class="col-lg-2 control-label"> Upload GIF <br> <span style="font-size: 10px">Hanya berupa .GIF</span> </label>
									<div class="col-lg-8">
										<input type="file" class="form-control" id="imgactive" name="imgactive">
										<span>* Ukuran gambar 500 x 500</span>
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
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<form method="POST" action="/portal/cms/form/ubahproduk" class="form-horizontal" data-toggle="validator" enctype="multipart/form-data">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Ubah produk</b></h4>
							</div>
							<div class="modal-body">
								<input type="hidden" name="ids" id="modal_update_ids">

								<div class="form-group">
									<label for="name" class="col-md-2 control-label"><span style="color: red">*</span> Nama </label>
									<div class="col-md-8">
										<input type="text" name="name" id="modal_update_name" class="form-control" data-error="Masukkan nama produk" autocomplete="off" required>
										<div class="help-block with-errors"></div>
									</div>
								</div>
								<div class="form-group">
									<label for="href" class="col-md-2 control-label"><span style="color: red">*</span> URL </label>
									<div class="col-md-8">
										<input type="text" name="href" id="modal_update_href" class="form-control" data-error="Masukkan link" autocomplete="off" required>
										<div class="help-block with-errors"></div>
									</div>
								</div>
								<div class="form-group">
									<label for="imgstatic" class="col-lg-2 control-label"> Upload Foto <br> <span style="font-size: 10px">Hanya berupa .JPG, .JPEG, dan .PNG</span> </label>
									<div class="col-lg-8">
										<input type="file" class="form-control" id="imgstatic" name="imgstatic">
									</div>
								</div>
								<div class="form-group">
									<label for="imgactive" class="col-lg-2 control-label"> Upload GIF <br> <span style="font-size: 10px">Hanya berupa .GIF</span> </label>
									<div class="col-lg-8">
										<input type="file" class="form-control" id="imgactive" name="imgactive">
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
						<form method="POST" action="/portal/cms/form/hapusproduk" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Hapus Produk</b></h4>
							</div>
							<div class="modal-body">
								<h4 id="label_delete"></h4>
								<input type="hidden" name="ids" id="modal_delete_ids" value="">
								<input type="hidden" name="name" id="modal_delete_name" value="">
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
				$("#modal_update_name").val($el.data('name'));
				$("#modal_update_href").val($el.data('href'));
			});

			$('.btn-delete').on('click', function () {
				var $el = $(this);

				$("#label_delete").append('Apakah anda yakin ingin menghapus produk <b>' + $el.data('name') + '</b>?');
				$("#modal_delete_ids").val($el.data('ids'));
				$("#modal_delete_name").val($el.data('name'));
			});

			$("#modal-delete").on("hidden.bs.modal", function () {
				$("#label_delete").empty();
			});

			$('#myTable').DataTable();
		});
	</script>
@endsection