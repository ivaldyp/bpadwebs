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
	<!-- Page plugins css -->
	<link href="{{ ('/portal/public/ample/plugins/bower_components/clockpicker/dist/jquery-clockpicker.min.css') }}" rel="stylesheet">
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
				<div class="col-md-12">
					<form class="form-horizontal" method="POST" action="/portal/notulen/form/ubahnotulen" data-toggle="validator" enctype="multipart/form-data">
					@csrf
						<div class="panel panel-info">
							<div class="panel-heading"> Buat Notulen Baru </div>
							<div class="panel-wrapper collapse in" aria-expanded="true">
								<div class="panel-body">

									<div class="form-group">
										<label for="nm_emp" class="col-md-2 control-label"> NIP<span style="color: red">*</span> </label>
										<div class="col-md-8">
											<input autocomplete="off" type="text" name="nip_emp" class="form-control" value="{{ $notulen['nip_emp'] }}" required="">
										</div>
									</div>

									<div class="form-group">
										<label for="nm_emp" class="col-md-2 control-label"> Nama<span style="color: red">*</span> </label>
										<div class="col-md-8">
											<input autocomplete="off" type="text" name="nm_emp" class="form-control" value="{{ $notulen['nm_emp'] }}" required="">
										</div>
									</div>

									<div class="form-group">
										<label for="unit" class="col-md-2 control-label"> Unit<span style="color: red">*</span> </label>
										<div class="col-md-8">
											<select class="form-control select2" name="unit_emp" id="unit">
												@foreach($units as $unit)
													<?php 
														$new_nm = str_replace('KASUBAG', 'SUB BAGIAN', $unit['nm_unit']);
														$new_nm = str_replace('KEPALA SUB', 'SUB', $new_nm);
													?>

													<option <?php if($notulen['unit_emp'] == $unit['kd_unit']): ?> selected <?php endif ?>
															<?php if(strlen($unit['kd_unit']) == 6): ?> disabled <?php endif ?>
															 value="{{ $unit['kd_unit'] }}"> {{ $unit['kd_unit'] }}::{{ $new_nm }} </option>
												@endforeach
											</select>
										</div>
									</div>

									<hr>

									<div class="form-group">
										<label for="not_dasar" class="col-md-2 control-label"> Dasar </label>
										<div class="col-md-8">
											<input autocomplete="off" type="text" name="not_dasar" class="form-control" value="{{ $notulen['not_dasar'] }}">
										</div>
									</div>
									
									<div class="form-group">
										<label for="not_tempat" class="col-md-2 control-label"> Tempat<span style="color: red">*</span> </label>
										<div class="col-md-8">
											<input autocomplete="off" type="text" name="not_tempat" class="form-control" required="" value="{{ $notulen['not_tempat'] }}">
										</div>
									</div>

									<div class="form-group">
										<label for="not_tanggal" class="col-md-2 control-label"> Tanggal </label>
										<div class="col-md-8">
											<?php date_default_timezone_set('Asia/Jakarta'); ?>
											<input type="text" class="form-control datepicker-autoclose" id="not_tanggal" name="not_tanggal" autocomplete="off" value="{{ date('d/m/Y', strtotime($notulen['not_tanggal'])) }}" required="">
										</div>
									</div>

									<div class="form-group">
										<label for="tgl_masuk" class="col-md-2 control-label"> Mulai </label>
										<div class="col-md-3">
											<div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true">
												<input type="text" class="form-control" name="not_mulai" id="not_mulai" value="{{ date('H:i', strtotime($notulen['not_mulai'])) }}"> <span class="input-group-addon"> <span class="glyphicon glyphicon-time"></span> </span>
											</div>
										</div>
										<label for="tgl_masuk" class="col-md-2 control-label"> Selesai </label>
										<div class="col-md-3">
											<div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true">
												<input type="text" class="form-control" name="not_selesai" id="not_selesai" value="{{ date('H:i', strtotime($notulen['not_selesai'])) }}"> <span class="input-group-addon"> <span class="glyphicon glyphicon-time"></span> </span>
											</div>
										</div>
									</div>

									<div class="form-group">
										<label for="not_acara" class="col-md-2 control-label"> Kegiatan<span style="color: red">*</span> </label>
										<div class="col-md-8">
											<input autocomplete="off" type="text" name="not_acara" class="form-control" required="" value="{{ $notulen['not_acara'] }}">
										</div>
									</div>

									<div class="form-group">
										<label for="not_pimpinan" class="col-md-2 control-label"> Pemimpin Rapat<span style="color: red">*</span> </label>
										<div class="col-md-8">
											<input autocomplete="off" type="text" name="not_pimpinan" class="form-control" required="" value="{{ $notulen['not_pimpinan'] }}">
										</div>
									</div>

									<div class="form-group">
										<label for="not_undangan" class="col-md-2 control-label"> Daftar Undangan </label>
										<div class="col-sm-8">
											<textarea class="summernote form-control" rows="5" placeholder="Enter text ..." name="not_undangan">{!! $notulen['not_undangan'] !!}</textarea>
										</div>
									</div>

									<div class="form-group">
										<label for="not_tidakhadir" class="col-md-2 control-label"> Tidak Hadir </label>
										<div class="col-sm-8">
											<textarea class="summernote form-control" rows="5" placeholder="Enter text ..." name="not_tidakhadir">{!! $notulen['not_tidakhadir'] !!}</textarea>
										</div>
									</div>

									<hr>

									<div class="form-group">
										<label for="not_latar" class="col-md-2 control-label"> Latar Belakang </label>
										<div class="col-sm-8">
											<textarea class="summernote form-control" rows="5" placeholder="Enter text ..." name="not_latar">{!! $notulen['not_latar'] !!}</textarea>
										</div>
									</div>

									<div class="form-group">
										<label for="not_agenda" class="col-md-2 control-label"> Poin Agenda Rapat </label>
										<div class="col-sm-8">
											<textarea class="summernote form-control" rows="5" placeholder="Enter text ..." name="not_agenda">{!! $notulen['not_agenda'] !!}</textarea>
										</div>
									</div>

									<div class="form-group">
										<label for="not_pembahasan" class="col-md-2 control-label"> Pembahasan Rapat </label>
										<div class="col-sm-8">
											<textarea class="summernote form-control" rows="5" placeholder="Enter text ..." name="not_pembahasan">{!! $notulen['not_pembahasan'] !!}</textarea>
										</div>
									</div>
									
									<div class="form-group">
										<label for="not_catatan" class="col-md-2 control-label"> Catatan Rapat </label>
										<div class="col-sm-8">
											<textarea class="summernote form-control" rows="5" placeholder="Enter text ..." name="not_catatan">{!! $notulen['not_catatan'] !!}</textarea>
										</div>
									</div>

									<div class="form-group">
										<label for="not_kesimpulan" class="col-md-2 control-label"> Kesimpulan Rapat </label>
										<div class="col-sm-8">
											<textarea class="summernote form-control" rows="5" placeholder="Enter text ..." name="not_kesimpulan">{!! $notulen['not_kesimpulan'] !!}</textarea>
										</div>
									</div>

									<div class="form-group">
										<label for="not_disppimpinan" class="col-md-2 control-label"> Disposisi Pimpinan </label>
										<div class="col-sm-8">
											<textarea class="summernote form-control" rows="5" placeholder="Enter text ..." name="not_disppimpinan">{!! $notulen['notdisppimpinan'] !!}</textarea>
										</div>
									</div>

									<hr>

									<div class="form-group">
                                        <label for="nm_file" class="col-md-2 control-label"> File</label>
                                        <div class="col-md-8">
                                            <input type="file" class="form-control" id="nm_file" name="nm_file">
                                        </div>
                                    </div>

                                    <div class="form-group">
										<label for="createdby" class="col-md-2 control-label"> Created By </label>
										<div class="col-md-8">
											<input disabled="" type="text" class="form-control" value="{{ $notulen['createdby'] }}">
										</div>
									</div>
								</div>
							</div>
							<div class="panel-footer">
                                <input type="submit" name="btnKirim" class="btn btn-info pull-right m-r-10" value="Simpan">
                                <input type="submit" name="btnDraft" class="btn btn-warning pull-right m-r-10" value="Draft">
                                <!-- <button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Kembali</button> -->
                                <button type="button" class="btn btn-default pull-right m-r-10" onclick="goBack()">Kembali</button>
                                <div class="clearfix"></div>
                            </div>
						</div>	
						<div class="panel panel-info">
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
	<script src="{{ ('/portal/public/ample/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>
	<script src="{{ ('/portal/public/ample/plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
	<script>
		jQuery(document).ready(function () {
			$('.summernote').summernote({
				height: 350, // set editor height
				width: 800,
				minHeight: null, // set minimum height of editor
				maxHeight: null, // set maximum height of editor
				focus: false // set focus to editable area after initializing summernote
			});
		});
	</script>
	<!-- Clock Plugin JavaScript -->
	<script src="{{ ('/portal/public/ample/plugins/bower_components/clockpicker/dist/jquery-clockpicker.min.js') }}"></script>
	<!-- Date Picker Plugin JavaScript -->
	<script src="{{ ('/portal/public/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

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