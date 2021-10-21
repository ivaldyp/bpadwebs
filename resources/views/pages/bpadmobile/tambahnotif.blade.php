@extends('layouts.masterhome')

@section('css')
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/portal/public/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<!-- Menu CSS -->
	<link href="{{ ('/portal/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
	<!-- animation CSS -->
	<link href="{{ ('/portal/public/ample/css/animate.css') }}" rel="stylesheet">
	<!-- Custom CSS -->
	<link href="{{ ('/portal/public/ample/css/style.css') }}" rel="stylesheet">
	<!-- color CSS -->
	<link href="{{ ('/portal/public/ample/css/colors/purple-dark.css') }}" id="theme" rel="stylesheet">
	<!-- Date picker plugins css -->
	<link href="{{ ('/portal/public/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
	<!-- page CSS -->
	<link href="{{ ('/portal/public/ample/plugins/bower_components/custom-select/custom-select.css') }}" rel="stylesheet" type="text/css" />


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
					<form class="form-horizontal" method="POST" action="/portal/mobile/form/tambahnotif" data-toggle="validator" enctype="multipart/form-data">
						@csrf
						<div class="panel panel-info">
							<div class="panel-heading"> Aset Jakarta - Tambah Notifikasi </div>
							<div class="panel-wrapper collapse in" aria-expanded="true">
								<div class="panel-body">

									<div class="form-group">
										<label for="tipe" class="col-md-2 control-label"> Tipe </label>
										<div class="col-md-8">
											<select class="form-control" name="tipe" id="tipe">
												@foreach($tipes as $tipe)
													<option value="{{ $tipe['ids'] }}"> {{$tipe['nm_tipe']}} </option>
												@endforeach
											</select>
										</div>
									</div>

									<div class="form-group">
										<label for="judul" class="col-md-2 control-label"> Judul<span style="color: red;">*</span> </label>
										<div class="col-md-8">
											<input autocomplete="off" type="text" name="judul" class="form-control" id="judul" required>
										</div>
									</div>

									<div class="form-group">
										<label for="isi" class="col-md-2 control-label"> Isi<span style="color: red;">*</span> </label>
										<div class="col-md-8">
											<input autocomplete="off" type="text" name="isi" class="form-control" id="isi" required>
										</div>
									</div>

									<div class="form-group">
										<label for="tujuan" class="col-md-2 control-label"> Tujuan </label>
										<div class="col-md-4">
											<select class="form-control" name="tujuan" id="tujuan">
												<option id="tujuan_all" value="1">SEMUA</option>
												<option id="tujuan_device" value="0">DEVICE</option>
											</select>
										</div>
										<div class="col-md-4" id="select-device-col">
											<select class="select2 select2-multiple" multiple="multiple" name="devices[]" id="device">
												<option value="ID1">ID1</option>
												<option value="ID2">ID2</option>
												<option value="ID3">ID3</option>
											</select>
										</div>
									</div>

									<div class="form-group">
										<label for="url" class="col-md-2 control-label"> URL </label>
										<div class="col-md-8">
											<input autocomplete="off" type="text" name="url" class="form-control" id="url">
										</div>
									</div>

									<div class="form-group">
                                        <label for="img" class="col-md-2 control-label"> File <br> </label>
                                        <div class="col-md-8">
                                            <input type="file" class="form-control" id="img" name="img" accept="image/png, image/gif, image/jpeg, image/jpg">
                                        </div>
                                    </div>

								</div>
							</div>
							<div class="panel-footer">
                                <button type="submit" class="btn btn-success pull-right">Simpan</button>
                                <button type="button" class="btn btn-default pull-right m-r-10" onclick="goBack()">Kembali</button>
                                <div class="clearfix"></div>
                            </div>
						</div>
					</form>
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
	<script src="{{ ('/portal/public/ample/js/validator.js') }}"></script>
	<script src="{{ ('/portal/public/ample/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>
	<!-- Date Picker Plugin JavaScript -->
	<script src="{{ ('/portal/public/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

	<script>
		$("#select-device-col").hide();
	
		function goBack() {
		  	window.history.back();
		}

		if($("#tujuan").children(":selected").val() == 0) {
			$("#select-device-col").show();
		}

		$("#tujuan").change(function() {
			var tujuan_id = $(this).children(":selected").val();
			if(tujuan_id == 0) {
				$("#select-device-col").show();
			} else {
				$("#select-device-col").hide();
			}
		});
		
		$(function () {
			$(".select2").select2();

			jQuery('#datepicker-autoclose').datepicker({
				autoclose: true
				, todayHighlight: true
				, format: 'dd/mm/yyyy'
			});

			jQuery('#datepicker-autoclose2').datepicker({
				autoclose: true
				, todayHighlight: true
				, format: 'dd/mm/yyyy'
			});
		});
	</script>
@endsection