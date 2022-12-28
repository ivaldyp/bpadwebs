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
	<!-- Date picker plugins css -->
	<link href="{{ ('/portal/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
	<!-- page CSS -->
	<link href="{{ ('/portal/ample/plugins/bower_components/custom-select/custom-select.css') }}" rel="stylesheet" type="text/css" />


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
					<form class="form-horizontal" method="POST" action="/portal/disposisi/form/lihatdisposisi" data-toggle="validator" enctype="multipart/form-data">
					@csrf
						<div class="panel panel-info">
							<div class="panel-heading"> Disposisi </div>
							<div class="panel-wrapper collapse in" aria-expanded="true">
								<div class="panel-body">
									<div class="col-md-12">

										<h3>Nomor Form: {{ $dispmaster['no_form'] }}</h3>

										<div class="panel-group" id="exampleAccordionDefault" aria-multiselectable="true" role="tablist">
											<div class="panel">
												<div class="panel-heading" style="background-color: #edf1f5" id="exampleHeadingDefaultOne" role="tab"> <a class="panel-title collapsed" data-toggle="collapse" href="#exampleCollapseDefaultOne" data-parent="#exampleAccordionDefault" aria-expanded="true" aria-controls="exampleCollapseDefaultOne"> {{ strtolower($dispmaster['catatan_final']) == 'undangan' ? 'Undangan' : 'Surat' }} </a> </div>
												<div class="panel-collapse collapse" id="exampleCollapseDefaultOne" aria-labelledby="exampleHeadingDefaultOne" role="tabpanel">
													<div class="table-responsive">
														<table class="table table-hover">
															<tr>
																<td class="col-md-6 p-l-30"><h4>No Form</h4></td>
																<td class="col-md-6" style="vertical-align: middle;">
																<h4 class="text-muted">{{ $dispmaster['no_form'] }}</h4></td>
															</tr>
															<tr>
																<td class="col-md-6 p-l-30"><h4>Kode Surat</h4></td>
																<td class="col-md-6" style="vertical-align: middle;">
																<h4 class="text-muted">{{ $dispmaster['kd_surat'] }}</h4></td>
																
															</tr>
															<tr>
																<td class="col-md-6 p-l-30"><h4>Unit</h4></td>
																<td class="col-md-6" style="vertical-align: middle;">
																<h4 class="text-muted">
																	@if($unitkerjas['kd_unit'])
																		[{{ $unitkerjas['kd_unit'] }}]
																	@endif

																	@if($unitkerjas['kd_unit'] && $unitkerjas['nm_unit'])
																		-
																	@endif

																	@if($unitkerjas['nm_unit'])
																		[{{ $unitkerjas['nm_unit'] }}]
																	@endif
																</h4></td>
															</tr>
															<tr>
																<td class="col-md-6 p-l-30"><h4>Tgl Masuk</h4></td>
																<td class="col-md-6" style="vertical-align: middle;">
																<h4 class="text-muted">{{ date('d/m/Y',strtotime($dispmaster['tgl_masuk'])) }}</h4></td>
															</tr>
															<tr>
																<td class="col-md-6 p-l-30"><h4> No Index</h4></td>
																<td class="col-md-6" style="vertical-align: middle;">
																<h4 class="text-muted">{{ $dispmaster['no_index'] }}</h4></td>
															</tr>
															<tr>
																<td class="col-md-6 p-l-30"><h4> Kode Disposisi</h4></td>
																<td class="col-md-6" style="vertical-align: middle;">
																<h4 class="text-muted">
																	@if($kddispos['kd_jnssurat'])
																		[{{ $kddispos['kd_jnssurat'] }}]
																	@endif

																	@if($kddispos['kd_jnssurat'] && $kddispos['nm_jnssurat'])
																		-
																	@endif

																	@if($kddispos['nm_jnssurat'])
																		[{{ $kddispos['nm_jnssurat'] }}]
																	@endif
																</h4></td>
															</tr>
															<tr>
																<td class="col-md-6 p-l-30"><h4> Nomor & Tgl Surat</h4></td>
																<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">
																	@if($dispmaster['no_surat'])
																		[{{ $dispmaster['no_surat'] }}]
																	@endif

																	@if($dispmaster['no_surat'] && $dispmaster['tgl_surat'])
																		-
																	@endif

																	@if($dispmaster['tgl_surat'])
																		[{{ $dispmaster['tgl_surat'] }}]
																	@endif
																</h4></td>
															</tr>
															<tr>
																<td class="col-md-6 p-l-30"><h4> Perihal</h4></td>
																<td class="col-md-6" style="vertical-align: middle;">
																<h4 class="text-muted">{{ $dispmaster['perihal'] }}</h4></td>
															</tr>
															<tr>
																<td class="col-md-6 p-l-30"><h4> Dari</h4></td>
																<td class="col-md-6" style="vertical-align: middle;">
																<h4 class="text-muted">{{ $dispmaster['asal_surat'] }}</h4></td>
															</tr>
															<tr>
																<td class="col-md-6 p-l-30"><h4> Kepada</h4></td>
																<td class="col-md-6" style="vertical-align: middle;">
																<h4 class="text-muted">{{ $dispmaster['kepada_surat'] }}</h4></td>
															</tr>
															<tr>
																<td class="col-md-6 p-l-30"><h4> Sifat Surat</h4></td>
																<td class="col-md-6" style="vertical-align: middle;">
																<h4 class="text-muted">
																	<span class="label label-info">{{ $dispmaster['sifat1_surat'] }}</span>
																	 - 
																	<span class="label label-warning">{{ $dispmaster['sifat2_surat'] }}</span>
																</h4></td>
															</tr>
															<tr>
																<td class="col-md-6 p-l-30"><h4> Keterangan</h4></td>
																<td class="col-md-6" style="vertical-align: middle;">
																<h4 class="text-muted">{{ $dispmaster['ket_lain'] }}</h4></td>
															</tr>
														</table>
													</div>
												</div>
											</div>
											<div class="panel">
												<div class="panel-heading" style="background-color: #edf1f5" id="exampleHeadingDefaultThree" role="tab"> <a class="panel-title collapsed" data-toggle="collapse" href="#exampleCollapseDefaultThree" data-parent="#exampleAccordionDefault" aria-expanded="false" aria-controls="exampleCollapseDefaultThree"> File </a> </div>
												<div class="panel-collapse collapse" id="exampleCollapseDefaultThree" aria-labelledby="exampleHeadingDefaultThree" role="tabpanel">
													<div class="table-responsive">
														<table class="table table-hover">
															<tr>
																<td class="col-md-6 p-l-30"><h4>File</h4></td>
																<td class="col-md-6 data-input">
																	<input type="file" class="form-control" id="nm_file" name="nm_file[]" multiple>
																	<br>
																	<?php 
																		$splitfile = explode("::", $dispmaster['nm_file']);
																		if ($dispmaster['nm_file'] != '') {
																			foreach ($splitfile as $key => $file) { 
																				$namafolder = '/' . date('Y',strtotime($dispmaster['tglmaster'])) . '/' . $dispmaster['no_form'];
																				?>
																				<i class="fa fa-download"></i> <a target="_blank" href="{{ config('app.openfiledisposisi') }}{{$namafolder}}/{{ $file }}">{{ $file }}</a> <a href="javascript:void(0)"><i data-toggle="tooltip" title="Hapus?" class="fa fa-close delete-file" data-nm="{{$file}}" data-ids="{{$dispmaster['ids']}}" data-noform="{{ $dispmaster['no_form']}}" style="color: red"></i></a>
																				<br>
																			<?php }
																		}	 
																	?>
																</td>
															</tr>
														</table>
													</div>
												</div>
											</div>
											<div class="panel">
												<div class="panel-heading" style="background-color: #edf1f5" id="exampleHeadingDefaultTwo" role="tab"> <a class="panel-title collapsed" data-toggle="collapse" href="#exampleCollapseDefaultTwo" data-parent="#exampleAccordionDefault" aria-expanded="false" aria-controls="exampleCollapseDefaultTwo"> Tindak Lanjut </a> </div>
												<div class="panel-collapse collapse" id="exampleCollapseDefaultTwo" aria-labelledby="exampleHeadingDefaultTwo" role="tabpanel">
													<div class="table-responsive">
														<table class="table table-hover">
															@if($jabatans != 0)
															<tr>
																<td class="col-md-6 p-l-30"><h4>Disposisi ke</h4><br>
																	<span class="text-muted">*Refresh halaman apabila yang dicari tidak ditemukan</span>
																</td>
																<td class="col-md-6 data-input">
																	<select class="select2 m-b-10 select2-multiple" multiple="multiple" name="jabatans[]" id="jabatans">
																		{{-- @foreach($jabatans as $key => $jabatan)
																			<option value="{{ $jabatan['jabatan'] }}"> {{ $jabatan['jabatan'] }} </option>
																		@endforeach --}}
																		@foreach($jabatans as $key => $jabatan)
																			<option value="{{ $jabatan['kd_unit'] }}"> {{ ucwords(strtolower($jabatan['nm_unit'])) }} 
																				@if ( strlen($jabatan['kd_unit'] > 2) && substr($jabatan['kd_unit'], 4, 2) == '51' )
																					- <b>JAKARTA PUSAT</b>
																				@elseif ( strlen($jabatan['kd_unit'] > 2) && substr($jabatan['kd_unit'], 4, 2) == '52' )
																					- <b>JAKARTA UTARA</b>
																				@elseif ( strlen($jabatan['kd_unit'] > 2) && substr($jabatan['kd_unit'], 4, 2) == '53' )
																					- <b>JAKARTA BARAT</b>
																				@elseif ( strlen($jabatan['kd_unit'] > 2) && substr($jabatan['kd_unit'], 4, 2) == '54' )
																					- <b>JAKARTA SELATAN</b>
																				@elseif ( strlen($jabatan['kd_unit'] > 2) && substr($jabatan['kd_unit'], 4, 2) == '55' )
																					- <b>JAKARTA TIMUR</b>
																				@elseif ( strlen($jabatan['kd_unit'] > 2) && substr($jabatan['kd_unit'], 4, 2) == '56' )
																					- <b>KEPULAUAN SERIBU</b>
																				@elseif ( strlen($jabatan['kd_unit'] > 2) && substr($jabatan['kd_unit'], 4, 2) == '06' )
																					- <b>PPBD</b>
																				@elseif ( strlen($jabatan['kd_unit'] > 2) && substr($jabatan['kd_unit'], 4, 2) == '07' )
																					- <b>PUSDATIN ASET</b>
																				@elseif ( strlen($jabatan['kd_unit'] > 2) && substr($jabatan['kd_unit'], 4, 2) == '08' )
																					- <b>UPMA</b>
																				@endif
																			</option>
																		@endforeach 
																	</select>
																</td>
															</tr>
															@endif

															@if($stafs != 0)
															<tr>
																<td class="col-md-6 p-l-30"><h4>Staf</h4><br>
																	<span class="text-muted">*Refresh halaman apabila yang dicari tidak ditemukan</span>
																</td>
																<td class="col-md-6 data-input">
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
																</td>
															</tr>
															@endif

															<tr>
																<td class="col-md-6 p-l-30"><h4>Penanganan</h4></td>
																<td class="col-md-6 data-input">
																	<select class="select2 form-control" name="penanganan" id="penanganan">
																		@foreach($penanganans as $penanganan)
																			<option <?php if ($dispmaster['penanganan'] == $penanganan['nm_penanganan']): ?> selected <?php endif ?> value="{{ $penanganan['nm_penanganan'] }}"> {{ $penanganan['nm_penanganan'] }} </option>
																		@endforeach
																	</select>
																</td>
															</tr>

															<tr>
																<td class="col-md-6 p-l-30"><h4>Catatan</h4></td>
																<td class="col-md-6 data-input">
																	<textarea name="catatan" class="form-control" rows="3">{{ $dispmaster['catatan'] }}</textarea>
																</td>
															</tr>
														</table>
													</div>
												</div>
											</div>
											<div class="panel">
												<div class="panel-heading" style="background-color: #edf1f5" id="exampleHeadingDefaultFour" role="tab"> <a class="panel-title collapsed" data-toggle="collapse" href="#exampleCollapseDefaultFour" data-parent="#exampleAccordionDefault" aria-expanded="false" aria-controls="exampleCollapseDefaultFour"> Log </a> </div>
												<div class="panel-collapse collapse" id="exampleCollapseDefaultFour" aria-labelledby="exampleHeadingDefaultFour" role="tabpanel">
													<div class="table-responsive p-l-30">
														<table class="table">
															<tbody>
																{!! $treedisp !!}
															</tbody>
														</table>
													</div>
															
												</div>
											</div>
										</div>

										<input type="hidden" name="ids" value="{{ $dispmaster['ids'] }}">
										<input type="hidden" name="idmaster" value="{{ $dispmaster['idmaster'] }}">
										<input type="hidden" name="kd_unit" value="{{ $dispmaster['kd_unit'] }}">
										<input type="hidden" name="tgl_masuk" value="{{ $dispmaster['tgl_masuk'] }}">
										<input type="hidden" name="no_form" value="{{ $dispmaster['no_form'] }}">
										<input type="hidden" name="kepada" value="{{ $dispmaster['kepada'] }}" id="kepada">
										<input type="hidden" name="noid" value="{{ $dispmaster['noid'] }}" id="noid">
										<input type="hidden" name="tujuanidunit" value="{{ $tujuan['idunit'] }}" id="tujuanidunit">
										<input type="hidden" name="tujuanidemp" value="{{ $tujuan['id_emp'] }}" id="tujuanidemp">
										<input type="hidden" name="tglmaster" value="{{ $dispmaster['tglmaster'] }}">
										<input type="hidden" name="nm_file_master" value="{{ $dispmaster['nm_file'] }}">
										<input type="hidden" name="from_pm_new" value="{{ $dispmaster['to_pm'] }}">

									</div>

									<div class="col-md-6">
													

										<hr>

										
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
								<?php 
									if (Session::get('signdate')) {
										$splitsigndate = explode("::", Session::get('signdate'));
										$signdate = Session::get('signdate');
									} elseif ($signdate) {
										$splitsigndate = explode("::", $signdate);
										$signdate = $signdate;
									}
									$yearnow = $splitsigndate[0];
									$signnow = $splitsigndate[1];
									$monthnow = $splitsigndate[2];
								?>
								<!-- <button type="submit" class="btn btn-success pull-right">Simpan</button> -->
								@if($tipe <> "sent")
								@if(strtolower($dispmaster['rd']) == 'd' || strtolower($dispmaster['rd']) == 'n' || strtolower($dispmaster['rd']) == 'y')
								<input type="hidden" name="signdate" value="{{$signdate}}">
								<input type="submit" name="btnKirim" class="btn btn-info pull-right m-r-10" value="Kirim">
								
								<input type="submit" name="btnDraft" class="btn btn-warning pull-right m-r-10" value="Draft">
								@endif
								@endif
								<!-- <button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Kembali</button> -->
								<a href="/portal/disposisi/disposisi?yearnow={{$yearnow}}&signnow={{$signnow}}&monthnow={{$monthnow}}"><button type="button" class="btn btn-default pull-right m-r-10" onclick="goBack()">Kembali</button></a>
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
	<!-- Custom Theme JavaScript -->
	<script src="{{ ('/portal/ample/js/cbpFWTabs.js') }}"></script>
	<script type="text/javascript">
		(function () {
				[].slice.call(document.querySelectorAll('.sttabs')).forEach(function (el) {
				new CBPFWTabs(el);
			});
		})();
	</script>
	<script src="{{ ('/portal/ample/js/custom.min.js') }}"></script>
	<script src="{{ ('/portal/ample/js/validator.js') }}"></script>
	<script src="{{ ('/portal/ample/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>
	<!-- Date Picker Plugin JavaScript -->
	<script src="{{ ('/portal/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

	<script>
		function goBack() {
		  window.history.back();
		}
		$(function () {
			$(".select2").select2({
				allowClear: true,
			});

			var kepada = $("#kepada").val();
			$('#jabatans').select2('val', kepada.split("::"));

			var noid = $("#noid").val();
			$('#stafs').select2('val', noid.split("::"));

			var tujuanidunit = $("#tujuanidunit").val();
			var tujuanidemp = $("#tujuanidemp").val();
			console.log(tujuanidunit);
			console.log(tujuanidemp);
			if (tujuanidunit.length == 10) {
				$('#stafs').select2('val', tujuanidemp);
			}

			$('.delete-file').on('click', function () {
				var $el = $(this);
				if(confirm("Are you sure you want to delete "+$el.data('nm')+"?")){

					var ids = $el.data('ids');
					var nm_file = $el.data('nm');
					var no_form = $el.data('noform');

					$.ajax({ 
					type: "GET", 
					url: "/portal/disposisi/hapusfiledisposisi",
					data: { nm_file : nm_file, ids : ids, no_form : no_form },
					dataType: "JSON",
					}).done(function( data ) { 
						console.log(data);
						if (data == 0) {
							alert("File berhasil dihapus");
							location.reload();
						} else {
							alert("File tidak ditemukan");
						}
						
					}); 

				}


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