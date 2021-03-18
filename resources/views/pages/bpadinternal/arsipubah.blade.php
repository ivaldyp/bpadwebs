@extends('layouts.masterhome')

@section('css')
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/portal/public/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<!-- summernotes CSS -->
	<link href="{{ ('/portal/public/ample/plugins/bower_components/summernote/dist/summernote.css') }}" rel="stylesheet" />
	<!-- Menu CSS -->
	<link href="{{ ('/portal/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
	<!-- animation CSS -->
	<link href="{{ ('/portal/public/ample/css/animate.css') }}" rel="stylesheet">
	<!-- Custom CSS -->
	<link href="{{ ('/portal/public/ample/css/style.css') }}" rel="stylesheet">
	<!-- color CSS -->
	<link href="{{ ('/portal/public/ample/css/colors/purple-dark.css') }}" id="theme" rel="stylesheet">
	<!-- page CSS -->
	<link href="{{ ('/portal/public/ample/plugins/bower_components/custom-select/custom-select.css') }}" rel="stylesheet" type="text/css" />
	<!-- Date picker plugins css -->
	<link href="{{ ('/portal/public/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />


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
				<!-- <div class="col-md-1"></div> -->
				<div class="col-md-12">
					<form class="form-horizontal" method="POST" action="/portal/internal/form/ubaharsip" data-toggle="validator" enctype="multipart/form-data">
					@csrf
						<div class="panel panel-info">
							<div class="panel-heading"> Tambah Arsip Kepegawaian </div>
							<div class="panel-wrapper collapse in" aria-expanded="true">
								<div class="panel-body">
									<input type="hidden" name="ids" value="{{ $ids }}">

									<div class="form-group">
										<label for="arsip_judul" class="col-md-2 control-label"> Judul <span style="color: red; font-size: 12px">*</span> </label>
										<div class="col-md-8"> 
											<input type="text" class="form-control" id="arsip_judul" name="arsip_judul" autocomplete="off" required="" data-error="Judul harus diisi" value="{{ $arsips['arsip_judul'] }}">
											<div class="help-block with-errors"></div>  
										</div>
									</div>

									<div class="form-group">
										<label for="arsip_kat" class="col-md-2 control-label">Kategori<span style="color: red">*</span> </label>
										<div class="col-md-8">
											<select class="form-control select2" name="arsip_kat" id="arsip_kat" required="" data-error="Pilih salah satu">
												@foreach($kats as $kat)
													<option <?php if($arsips['arsip_kat'] == $kat['singkatan']) : ?> selected <?php endif ?> value="{{ $kat['singkatan'] }}">[{{ $kat['singkatan'] }}] - {{ $kat['nm_kat'] }}</option>
												@endforeach
											</select>
											<div class="help-block with-errors"></div>  
										</div>
									</div>

									<div class="form-group">
										<label for="arsip_detail" class="col-md-2 control-label"> Detail </label>
										<div class="col-md-8">
											<textarea class="form-control" id="arsip_detail" name="arsip_detail" autocomplete="off">{{ $arsips['arsip_detail'] }}</textarea>  
										</div>
									</div>

									<div class="form-group">
										<label for="filearsip" class="col-lg-2 control-label"> File <br><span style="color: red">File Size Maksimal 5MB</span> </label>
										<div class="col-lg-8">
											<input type="file" class="form-control" id="filearsip" name="filearsip"><br>
                                            <span class="text-muted"><a target="_blank" href="{{ config('app.openfilearsip') }}/{{ $arsips['ids'] }}/{{ $arsips['arsip_file'] }}"><i class="fa fa-download"></i> [Unduh Disini] </a></span>
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-2 control-label"> Tampilkan? </label>
										<div class="radio-list col-md-8">
											<label class="radio-inline">
												<div class="radio radio-info">
													<input type="radio" name="arsip_tampil" id="tampil1" value="1" data-error="Pilih salah satu" required="" checked="">
													<label for="tampil1">Ya</label> 
												</div>
											</label>
											<label class="radio-inline">
												<div class="radio radio-info">
													<input type="radio" name="arsip_tampil" id="tampil2" value="0" >
													<label for="tampil2">Tidak</label>
												</div>
											</label>
											<div class="help-block with-errors"></div>  
										</div>
									</div>

								</div>
							</div>
							<div class="panel-footer">
								<button type="submit" class="btn btn-success pull-right">Simpan</button>
								<!-- <button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Kembali</button> -->
								<a href="{{ url()->previous() }}"><button type="button" class="btn btn-default pull-right m-r-10">Kembali</button></a>
								<div class="clearfix"></div>
							</div>
							<div class="panel-heading">  
								
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
	<script src="{{ ('/portal/public/ample/plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
	<script src="{{ ('/portal/public/ample/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>
	<!-- Date Picker Plugin JavaScript -->
	<script src="{{ ('/portal/public/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
	<script>

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