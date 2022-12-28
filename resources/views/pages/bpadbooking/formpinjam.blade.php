@extends('layouts.masterhome')

@section('css')
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/portal/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<!-- Menu CSS -->
	<link href="{{ ('/portal/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
	<!-- animation CSS -->
	<link href="{{ ('/portal/ample/css/animate.css') }}" rel="stylesheet">
	<!-- Custom CSS -->
	<link href="{{ ('/portal/ample/css/style.css') }}" rel="stylesheet">
	<!-- color CSS -->
	<link href="{{ ('/portal/ample/css/colors/purple-dark.css') }}" id="theme" rel="stylesheet">
	<!-- page CSS -->
	<link href="{{ ('/portal/ample/plugins/bower_components/custom-select/custom-select.css') }}" rel="stylesheet" type="text/css" />
	<!-- Page plugins css -->
	<link href="{{ ('/portal/ample/plugins/bower_components/clockpicker/dist/jquery-clockpicker.min.css') }}" rel="stylesheet">
	<!-- Date picker plugins css -->
	<link href="{{ ('/portal/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />


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
					<form class="form-horizontal" method="POST" action="/portal/booking/form/tambahpinjam" data-toggle="validator" enctype="multipart/form-data">
					@csrf
						<div class="panel panel-info">
							<div class="panel-heading"> Buat Pinjaman Baru </div>
							<div class="panel-wrapper collapse in" aria-expanded="true">
								<div class="panel-body">
									<div class="form-group">
										<label for="nm_emp" class="col-md-2 control-label"> Nama Peminjam </label>
										<div class="col-md-8">
											<input autocomplete="off" type="text" name="nm_emp" class="form-control" value="{{ Auth::user()->id_emp ? $_SESSION['user_data']['nm_emp'] : $_SESSION['user_data']['nama_user'] }}" required="">
										</div>
									</div>

									<div class="form-group">
										<label for="id_emp" class="col-md-2 control-label"> ID Peminjam </label>
										<div class="col-md-8">
											<input autocomplete="off" type="text" name="id_emp" class="form-control" value="{{ Auth::user()->id_emp ? $_SESSION['user_data']['id_emp'] : $_SESSION['user_data']['usname'] }}" required="">
										</div>
									</div>

									<div class="form-group">
										<label for="unit" class="col-md-2 control-label"> Unit Peminjam </label>
										<div class="col-md-8">
											<select class="form-control select2" name="unit" id="unit">
												@foreach($units as $unit)
													<option <?php if(strpos($_SESSION['user_data']['idunit'], $unit['kd_unit']) !== false): ?> selected <?php endif ?> value="{{ $unit['kd_unit'] }}::{{ $unit['nm_unit'] }}"> {{ $unit['kd_unit'] }}::{{ $unit['nm_unit'] }} </option>
												@endforeach
											</select>
										</div>
									</div>

									<div class="form-group">
										<label for="ruang" class="col-md-2 control-label"> Ruang </label>
										<div class="col-md-8">
											<select class="form-control select2" name="ruang" id="ruang">
												@foreach($ruangs as $ruang)
													<option value="{{ $ruang['ids'] }}"> [{{ $ruang['nm_ruang'] }}] - [{{ $ruang['lokasi'] }}, Lantai {{ $ruang['lantai'] }}] </option>
												@endforeach
											</select>
										</div>
									</div>

									<div class="form-group">
										<label for="tujuan" class="col-md-2 control-label"> Kegiatan </label>
										<div class="col-md-8">
											<input autocomplete="off" type="text" name="tujuan" id="tujuan" class="form-control" required="">
										</div>
									</div>

									<div class="form-group">
										<label for="peserta" class="col-md-2 control-label"> Jumlah Peserta </label>
										<div class="col-md-3">
											<input autocomplete="off" type="text" name="peserta" id="peserta" class="form-control" placeholder="contoh: 10 / 20 / 30">
										</div>
									</div>

									<div class="form-group">
										<label for="tgl_pinjam" class="col-md-2 control-label"> Tanggal </label>
										<div class="col-md-8">
											<?php date_default_timezone_set('Asia/Jakarta'); ?>
											<input type="text" class="form-control datepicker-autoclose" id="tgl_pinjam" name="tgl_pinjam" autocomplete="off" value="{{ date('d/m/Y') }}" required="">
										</div>
									</div>

									<div class="form-group">
										<label for="tgl_masuk" class="col-md-2 control-label"> Mulai </label>
										<div class="col-md-3">
											<div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true">
												<input type="text" class="form-control" value="00:00" name="time1" id="time1"> <span class="input-group-addon"> <span class="glyphicon glyphicon-time"></span> </span>
											</div>
										</div>
										<label for="tgl_masuk" class="col-md-2 control-label"> Selesai </label>
										<div class="col-md-3">
											<div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true">
												<input type="text" class="form-control" value="00:00" name="time2" id="time2"> <span class="input-group-addon"> <span class="glyphicon glyphicon-time"></span> </span>
											</div>
										</div>
									</div>

									<div class="form-group">
                                        <label for="nm_file" class="col-md-2 control-label"> File </label>
                                        <div class="col-md-8">
                                            <input type="file" class="form-control" id="nm_file" name="nm_file" accept="application/pdf">
											<p class="text-muted">Undangan / Surat Pengantar, MAX 1MB</p>
                                        </div>
                                    </div>

									<div class="form-group">
                                        <label for="catatan" class="col-md-2 control-label"> Catatan </label>
                                        <div class="col-md-8">
											<textarea name="catatan" id="catatan" class="form-control"></textarea>
                                        </div>
                                    </div>
								</div>
							</div>
							<div class="panel-footer">
                                <button type="submit" class="btn btn-success pull-right">Simpan</button>
                                <!-- <button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Kembali</button> -->
                                <button type="button" class="btn btn-default pull-right m-r-10" onclick="goBack()">Kembali</button>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-heading">  
								
							</div>
						</div>	
						<div class="panel panel-info">
							
						</div>
					</form>
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
	<script src="{{ ('/portal/ample/js/validator.js') }}"></script>
	<script src="{{ ('/portal/ample/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>
	<!-- Clock Plugin JavaScript -->
	<script src="{{ ('/portal/ample/plugins/bower_components/clockpicker/dist/jquery-clockpicker.min.js') }}"></script>
	<!-- Date Picker Plugin JavaScript -->
	<script src="{{ ('/portal/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

	<script>
		function goBack() {
		  window.history.back();
		}

		$('.clockpicker').clockpicker({
			donetext: 'Done'
			, }).find('input').change(function () {
		});

		$(function () {
			$(".select2").select2();

			jQuery('.datepicker-autoclose').datepicker({
				autoclose: true
				, todayHighlight: true
				, format: 'dd/mm/yyyy'
			});
		});
	</script>
@endsection