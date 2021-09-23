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
					<form class="form-horizontal" method="POST" action="/portal/disposisi/form/tambahdisposisi" data-toggle="validator" enctype="multipart/form-data">
					@csrf
						<div class="panel panel-info">
							<div class="panel-heading"> Disposisi </div>
							<div class="panel-wrapper collapse in" aria-expanded="true">
								<div class="panel-body">
									<div class="col-md-6">

										<div class="form-group">
											<label class="col-md-2 control-label"> No Form </label>
											<div class="col-md-8">
												<?php 
													$newnoform = explode(".", $maxnoform); 
													$randomletter = substr(str_shuffle("123456789ABCDEFGHIJKLMNPQRSTUVWXYZ"), 0, 6);
													$randomletter .= substr(($newnoform[3]), -3);
												?>
												<input autocomplete="off" type="text" class="form-control" value="{{ $maxnoform }}" disabled>
												<input autocomplete="off" type="hidden" name="newnoform" class="form-control" id="newnoform" value="{{ $maxnoform }}">
												<!-- <p>{{ $newnoform[0] . '.' . $newnoform[1] . '.' . $newnoform[2] . '.' . ($newnoform[3]+1) }}</p> -->
											</div>
										</div>

										<div class="form-group">
											<label class="col-md-2 control-label"> Kode Surat </label>
											<div class="col-md-8">
												<input type="text" class="form-control" value="{{ $randomletter }}" disabled="">
												<input type="hidden" class="form-control" name="kd_surat" value="{{ $randomletter }}">
											</div>
										</div>

										<div class="form-group">
											<label for="kd_unit" class="col-md-2 control-label"> Unit </label>
											<div class="col-md-8">
												<input type="text" class="form-control" value="[{{ $unitkerjas[0]['kd_unit'] }}] - [{{ $unitkerjas[0]['nm_unit'] }}] " disabled="">
												<input type="hidden" class="form-control" name="kd_unit" value="{{ $unitkerjas[0]['kd_unit'] }}">
												<!-- <select class="form-control select2" name="kd_unit" id="kd_unit">
													@foreach($unitkerjas as $unit)
														<option value="{{ $unit['kd_unit'] }}"> [{{ $unit['kd_unit'] }}] - [{{ $unit['nm_unit'] }}] </option>
													@endforeach
												</select> -->
											</div>
										</div>

										<div class="form-group">
											<label for="catatan_final" class="col-md-2 control-label"> Tipe<span style="color: red">*</span> </label>
											<div class="col-md-8">
												<select class="form-control" name="catatan_final" id="catatan_final" required="">
													<option value="<?php echo NULL; ?>" selected>-</option>
													<option value="Surat"> Surat </option>
													<option value="Undangan"> Undangan </option>
												</select>
											</div>
										</div>

										<div class="form-group">
											<label for="tgl_masuk" class="col-md-2 control-label"> Tgl Masuk<span style="color: red">*</span> </label>
											<div class="col-md-8">
												<input type="text" name="tgl_masuk" class="form-control" id="datepicker-autoclose" autocomplete="off" placeholder="dd/mm/yyyy" required="" data-error="Isi tanggal masuk surat" value="{{ date('d/m/Y') }}">
												<div class="help-block with-errors"></div>
											</div>
										</div>

										<div class="form-group">
											<label for="no_index" class="col-md-2 control-label"> No Index </label>
											<div class="col-md-8">
												<input autocomplete="off" type="text" name="no_index" class="form-control" id="no_index">
											</div>
										</div>

										<div class="form-group">
											<label for="kode_disposisi" class="col-md-2 control-label"> Kode Disposisi </label>
											<div class="col-md-8">
												<select class="form-control select2" name="kode_disposisi" id="kode_disposisi">
													@foreach($kddispos as $kddispo)
														<option value="{{ $kddispo['kd_jnssurat'] }}"> [{{ $kddispo['kd_jnssurat'] }}] - [{{ $kddispo['nm_jnssurat'] }}] </option>
													@endforeach
												</select>
											</div>
										</div>

										<div class="form-group">
											<label for="gelar" class="col-md-2 control-label"> Nomor & Tgl Surat </label>
											<div class="col-md-4">
												<input autocomplete="off" type="text" name="no_surat" class="form-control" id="no_surat" placeholder="Nomor">
											</div>
											<div class="col-md-4">
												<input type="text" name="tgl_surat" class="form-control" id="datepicker-autoclose2" autocomplete="off" placeholder="dd/mm/yyyy" value="{{ date('d/m/Y') }}">
											</div>
										</div>

										<div class="form-group">
											<label for="perihal" class="col-md-2 control-label"> Perihal<span style="color: red">*</span> </label>
											<div class="col-md-8">
												<input type="text" name="perihal" class="form-control" id="perihal" required="" data-error="Isi perihal surat" onkeypress="return event.keyCode!=13">
												<div class="help-block with-errors"></div>
											</div>
										</div>

										<div class="form-group">
											<label for="asal_surat" class="col-md-2 control-label"> Dari </label>
											<div class="col-md-8">
												<input type="text" name="asal_surat" class="form-control" id="asal_surat" onkeypress="return event.keyCode!=13">
											</div>
										</div>

										<div class="form-group">
											<label for="kepada_surat" class="col-md-2 control-label"> Kepada </label>
											<div class="col-md-8">
												<input type="text" name="kepada_surat" class="form-control" id="kepada_surat" onkeypress="return event.keyCode!=13">
											</div>
										</div>

										<div class="form-group">
											<label class="col-md-2 control-label"> Sifat Surat </label>
											<div class="col-md-4">
												<select class="form-control" name="sifat1_surat" id="sifat1_surat">
													<option value="<?php echo NULL; ?>" selected> </option>
													<option value="Rahasia"> Rahasia </option>
													<option value="Penting"> Penting </option>
													<option value="Biasa"> Biasa </option>
												</select>
											</div>
											<div class="col-md-4">
												<select class="form-control" name="sifat2_surat" id="sifat2_surat">
													<option value="<?php echo NULL; ?>" selected> </option>
													<option value="Kilat"> Kilat </option>
													<option value="Hari Ini"> Hari Ini </option>
													<option value="Sangat Segera"> Sangat Segera </option>
													<option value="Segera"> Segera </option>
													<option value="Biasa"> Biasa </option>
												</select>
											</div>
										</div>

										<div class="form-group">
											<label for="ket_lain" class="col-md-2 control-label"> Keterangan </label>
											<div class="col-md-8">
												<textarea name="ket_lain" class="form-control" rows="3"></textarea>
											</div>
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-group">
											<label class="col-md-2 control-label"> Disposisi Ke  </label>
											<div class="col-md-8">
												<select class="select2 m-b-10 select2-multiple" multiple="multiple" name="jabatans[]" id="jabatans">
													@foreach($jabatans as $key => $jabatan)
														<option <?php if ($key == 0): ?> selected <?php endif ?> value="{{ $jabatan['kd_unit'] }}"> {{ $jabatan['nm_unit'] }} </option>
													@endforeach
												</select>
											</div>
										</div>

										@if(isset($stafs))
										<div class="form-group">
											<label for="nip_emp" class="col-md-2 control-label"> Staf </label>
											<div class="col-md-8">
												<select class="select2 m-b-10 select2-multiple" multiple="multiple" name="stafs[]" id="stafs" required>
													@foreach($stafs as $staf)
														<option value="{{ $staf['id_emp'] }}"> 
															{{ ucwords(strtolower($staf['nm_emp'])) }}
															<?php if ($staf['nrk_emp']): ?>
																- [{{ $staf['nrk_emp'] }}]
															<?php endif ?>
														</option>
													@endforeach
												</select>
											</div>
										</div>
										@endif
										
										<div class="form-group">
											<label for="penanganan" class="col-md-2 control-label"> Penanganan </label>
											<div class="col-md-8">
												<select class="select2 form-control" name="penanganan" id="penanganan">
													<option value="{{NULL}}">-</option>
													@foreach($penanganans as $penanganan)
														<option value="{{ $penanganan['nm_penanganan'] }}"> {{ $penanganan['nm_penanganan'] }} </option>
													@endforeach
												</select>
											</div>
										</div>

										<div class="form-group">
											<label for="catatan" class="col-md-2 control-label"> Catatan </label>
											<div class="col-md-8">
												<textarea name="catatan" class="form-control" rows="3"></textarea>
											</div>
										</div>

										<div class="form-group">
	                                        <label for="nm_file" class="col-lg-2 control-label"> File <br> </label>
	                                        <div class="col-lg-8">
	                                            <input type="file" class="form-control" id="nm_file" name="nm_file[]" multiple>
	                                        </div>
	                                    </div>
									</div>
									<!-- <div class="sttabs tabs-style-underline">
										<nav>
											<ul>
												<li><a href="#section-underline-1" class="sticon ti-book"><span>Identitas</span></a></li>
												<li><a href="#section-underline-2" class="sticon ti-camera"><span>Pendidikan</span></a></li>
											</ul>
										</nav>
										<div class="content-wrap">
											<section id="section-underline-1">
											</section>
											<section id="section-underline-2">
											</section>
										</div>
									</div> -->
									
								</div>
							</div>
							<div class="panel-footer">
                                {{-- <!-- <button type="submit" class="btn btn-success pull-right">Simpan</button> -->  --}}
                                {{-- <input type="submit" name="btnKirim" class="btn btn-info pull-right m-r-10" value="Kirim"> --}}
                                <input type="submit" name="btnDraft" class="btn btn-warning pull-right m-r-10" value="Draft">
                                {{-- <!-- <button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Kembali</button> -->  --}}
                                <a href="/portal/disposisi/formdisposisi"><button type="button" class="btn btn-default pull-right m-r-10" onclick="goBack()">Kembali</button></a>
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
	<!-- Custom Theme JavaScript -->
	<script src="{{ ('/portal/public/ample/js/cbpFWTabs.js') }}"></script>
	<script type="text/javascript">
		(function () {
				[].slice.call(document.querySelectorAll('.sttabs')).forEach(function (el) {
				new CBPFWTabs(el);
			});
		})();
	</script>
	<script src="{{ ('/portal/public/ample/js/custom.min.js') }}"></script>
	<script src="{{ ('/portal/public/ample/js/validator.js') }}"></script>
	<script src="{{ ('/portal/public/ample/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>
	<!-- Date Picker Plugin JavaScript -->
	<script src="{{ ('/portal/public/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

	<script>
		function goBack() {
		  window.history.back();
		}
		$(function () {
			$(".select2").select2({
				allowClear: true,
			});

			// $("#newnoform").on('change', function(){

			// 	var varnoform = $("#newnoform").val();

			// 	$.ajax({ 
			// 	type: "GET", 
			// 	url: "/portal/profil/ceknoform",
			// 	data: { noform : varnoform },
			// 	dataType: "JSON",
			// 	}).done(function( data ) { 
			// 		console.log(data[0].total);
			// 		if (data[0].total > 0) {
			// 			alert("Nomor surat sudah terpakai");
			// 		}
			// 	}); 
			// });

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