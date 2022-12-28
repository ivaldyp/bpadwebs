@extends('layouts.masterhome' )

@section('css')
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/portal/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<!-- summernotes CSS -->
	<link href="{{ ('/portal/ample/plugins/bower_components/summernote/dist/summernote.css') }}" rel="stylesheet" />
	<!-- Menu CSS -->
	<link href="{{ ('/portal/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
	<!-- Page plugins css -->
    <link href="{{ ('/portal/ample/plugins/bower_components/clockpicker/dist/jquery-clockpicker.min.css') }}" rel="stylesheet">
	<!-- Date picker plugins css -->
    <link href="{{ ('/portal/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
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
												echo ucwords($link[4]);
											?> </h4> </div>
				<div class="col-lg-9 col-sm-8 col-md-8 col-xs-12"> 
					<ol class="breadcrumb">
						<li>{{config('app.name')}}</li>
						<?php 
							$link = explode("/", url()->full());
							if (count($link) == 5) {
								?> 
									<li class="active"> {{ ucwords($link[4]) }} </li>
								<?php
							} elseif (count($link) == 6) {
								?> 
									<li class="active"> {{ ucwords($link[4]) }} </li>
									<?php 
										$backlink = explode("?", $link[5]);
									?>
									<li class="active"> {{ str_replace('%20', ' ', ucwords($backlink[0])) }} </li>
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
					<div class="panel panel-info">
						<div class="panel-heading">Tambah Konten</div>
						<form class="form-horizontal" method="POST" action="/portal/cms/form/tambahcontent" data-toggle="validator" enctype="multipart/form-data">
						@csrf   
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<input type="hidden" name="idkat" value="{{ $idkat }}">
									<input type="hidden" name="contentnew" value="1">
									<input type="hidden" name="appr" value="N">
									<input type="hidden" name="usrinput" value="{{ isset(Auth::user()->id_emp) ? Auth::user()->id_emp : Auth::user()->usname }}">
									<input type="hidden" name="kode_kat" value="{{ $kat['kode_kat'] }}">

									@if($kat['kode_kat'] == 'INF')

										<?php 

										date_default_timezone_set('Asia/Jakarta');
										$datetanggal = date('d-m-Y H:i:s');
										?>

										<div class="form-group">
											<label for="tanggal" class="col-md-2 control-label"> Waktu </label>
											<div class="col-md-8">
												<input type="text" class="form-control" id="tanggal" name="tanggal" autocomplete="off" data-error="Masukkan tanggal" value="{{ $datetanggal }}">
											</div>
										</div>

										<div class="form-group">
											<label for="judul" class="col-md-2 control-label"><span style="color: red">*</span> Judul </label>
											<div class="col-md-8">
												<input type="text" class="form-control" id="judul" name="judul" autocomplete="off" data-error="Masukkan judul" required>
												<div class="help-block with-errors"></div>
											</div>
										</div>

										<div class="form-group">
											<label for="url" class="col-md-2 control-label"> URL </label>
											<div class="col-md-8">
												<input type="text" class="form-control" id="url" name="url" autocomplete="off">
												<div class="help-block with-errors"></div>
											</div>
										</div>

										<div class="form-group">
											<label for="tfile" class="col-lg-2 control-label"> Upload Foto <br> <span style="font-size: 10px">Hanya berupa JPG, JPEG, dan PNG</span> </label>
											<div class="col-lg-8">
												<input type="file" class="form-control" id="tfile" name="tfile">
											</div>
										</div>

									@elseif($kat['kode_kat'] == 'VID')

										<div class="form-group">
											<label for="subkat" class="col-md-2 control-label"><span style="color: red">*</span> Subkategori </label>
											<div class="col-md-8">
												<select class="form-control" name="subkat" id="subkat">
													@foreach($subkats as $subkat)
														<option value="{{ $subkat['subkat'] }}"> {{ $subkat['subkat'] }} </option>
													@endforeach
												</select>
											</div>
										</div>

										<div class="form-group">
											<label for="tanggal" class="col-md-2 control-label"> Waktu </label>
											<div class="col-md-8">
												<input type="text" class="form-control" id="tanggal" name="tanggal" autocomplete="off" data-error="Masukkan tanggal" value="{{ date('d/m/Y H:i:s', strtotime(str_replace('/', '-', now('Asia/Jakarta')))) }}">
											</div>
										</div>

										<div class="form-group">
											<label for="judul" class="col-md-2 control-label"><span style="color: red">*</span> Judul </label>
											<div class="col-md-8">
												<input type="text" class="form-control" id="judul" name="judul" autocomplete="off" data-error="Masukkan judul" required>
												<div class="help-block with-errors"></div>
											</div>
										</div>

										<div class="form-group">
											<label for="url" class="col-md-2 control-label"> URL<br><span style="color: red; font-size: 14px">Masukkan kode youtube video ID</span> </label>
											<div class="col-md-8 input-group">
												<span class="input-group-addon" id="basic-addon3">youtube.com/watch?v=</span>
												<input type="text" class="form-control" id="url" name="url" autocomplete="off">
												<div class="help-block with-errors"></div>
											</div>
										</div>

										<div class="form-group">
											<label for="isi2" class="col-md-2 control-label"> Embed </label>
											<div class="col-md-8">
												<textarea class="form-control" id="isi2" name="isi2" autocomplete="off"></textarea>
												<div class="help-block with-errors"></div>
											</div>
										</div>

									@else

										@if($idkat != 14 && $idkat != 6 && $idkat != 19 && $idkat != 4 && $idkat != 11)
										<div class="form-group">
											<label for="subkat" class="col-md-2 control-label"><span style="color: red">*</span> Subkategori </label>
											<div class="col-md-8">
												<select class="form-control" name="subkat" id="subkat">
													@foreach($subkats as $subkat)
														<option value="{{ $subkat['subkat'] }}"> {{ $subkat['subkat'] }} </option>
													@endforeach
												</select>
											</div>
										</div>
										@endif

										<div class="form-group">
											<label for="tanggal" class="col-md-2 control-label"> Waktu </label>
											<div class="col-md-8">
												<input type="text" class="form-control" id="tanggal" name="tanggal" autocomplete="off" data-error="Masukkan tanggal" value="{{ date('d/m/Y H:i:s', strtotime(str_replace('/', '-', now('Asia/Jakarta')))) }}">
											</div>
										</div>

										<div class="form-group">
											<label for="judul" class="col-md-2 control-label"><span style="color: red">*</span> Judul </label>
											<div class="col-md-8">
												<input type="text" class="form-control" id="judul" name="judul" autocomplete="off" data-error="Masukkan judul" required>
												<div class="help-block with-errors"></div>
											</div>
										</div>

										<div class="form-group">
											<label class="col-md-2 control-label"> Jadikan headline? </label>
											<div class="radio-list col-md-8">
												<label class="radio-inline">
													<div class="radio radio-info">
														<input type="radio" name="headline" id="headline1" value="H," data-error="Pilih salah satu">
														<label for="headline1">Ya</label> 
													</div>
												</label>
												<label class="radio-inline">
													<div class="radio radio-info">
														<input type="radio" name="headline" id="headline2" value="" checked>
														<label for="headline2">Tidak</label>
													</div>
												</label>
												<div class="help-block with-errors"></div>  
											</div>
										</div>

										@if($idkat != 14 && $idkat != 6 && $idkat != 19 && $idkat != 4 && $idkat != 11)
										<div class="form-group">
											<label for="tfile" class="col-lg-2 control-label"> Upload Foto <br> <span style="font-size: 10px">Hanya berupa JPG, JPEG, dan PNG</span> </label>
											<div class="col-lg-8">
												<input type="file" class="form-control" id="tfile" name="tfile">
											</div>
										</div>
										@endif

										@if($idkat == 6)
										<div class="form-group">
											<label for="tfiledownload" class="col-lg-2 control-label"> Upload File <br> <span style="font-size: 10px">Berupa .pdf, .xls, .doc, .xlxs, .docx, .zip, .rar, .txt, .csv</span> </label>
											<div class="col-lg-8">
												<input type="file" class="form-control" id="tfiledownload" name="tfiledownload">
											</div>
										</div>
										@endif

										@if($idkat == 4)
										<div class="form-group">
											<label for="url" class="col-md-2 control-label"> URL </label>
											<div class="col-md-8">
												<input type="text" class="form-control" id="url" name="url" autocomplete="off">
												<div class="help-block with-errors"></div>
											</div>
										</div>
										@endif

										@if($idkat != 6 && $idkat != 4)
										<div class="form-group">
											<label for="isi1" class="col-md-2 control-label"> Ringkasan </label>
											<div class="col-md-8">
												<div class="alert alert-warning col-md-11">
													Ringkasan hanya berisi teks 1 paragraf, tidak dapat diisi gambar.
												</div>
												<textarea class="summernote form-control" rows="15" placeholder="Enter text ..." name="isi1"></textarea>
											</div>
										</div>
										@endif

										@if($idkat != 6 && $idkat != 5 && $idkat != 19 && $idkat != 4 && $idkat != 11)
										<div class="form-group">
											<label for="isi2" class="col-md-2 control-label"> Isi </label>
											<div class="col-md-8">
												<textarea class="summernote form-control" rows="15" placeholder="Enter text ..." name="isi2"></textarea>
											</div>
										</div>
										@endif

									@endif

									<div class="form-group">
										<label for="editor" class="col-md-2 control-label"> Editor </label>
										<div class="col-md-8">
											<input disabled type="text" class="form-control" id="editor" name="editor" autocomplete="off" value="{{ isset($_SESSION['user_data']['nm_emp']) ? $_SESSION['user_data']['nm_emp'] : (isset($_SESSION['user_data']['nama_user']) ? $_SESSION['user_data']['nama_user'] : $_SESSION['user_data']['usname']) }}">
											<input type="hidden" class="form-control" id="editor" name="editor" autocomplete="off" value="{{ isset($_SESSION['user_data']['nm_emp']) ? $_SESSION['user_data']['nm_emp'] : (isset($_SESSION['user_data']['nama_user']) ? $_SESSION['user_data']['nama_user'] : $_SESSION['user_data']['usname']) }}">
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-2 control-label"> Suspend? </label>
										<div class="radio-list col-md-8">
											<label class="radio-inline">
												<div class="radio radio-info">
													<input type="radio" name="suspend" id="suspend1" value="Y" data-error="Pilih salah satu">
													<label for="suspend1">Ya</label> 
												</div>
											</label>
											<label class="radio-inline">
												<div class="radio radio-info">
													<input type="radio" name="suspend" id="suspend2" value="" checked>
													<label for="suspend2">Tidak</label>
												</div>
											</label>
											<div class="help-block with-errors"></div>  
										</div>
									</div>

										
								</div>
								<div class="panel-footer">
									<button type="submit" class="btn btn-success pull-right">Simpan</button>
									<!-- <button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Kembali</button> -->
									<a href="{{ url()->previous() }}"><button type="button" class="btn btn-default pull-right m-r-10">Kembali</button></a>		
									<div class="clearfix"></div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('js')
	<!-- jQuery -->
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
	<script src="{{ ('/portal/ample/plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
	<!-- Clock Plugin JavaScript -->
    <script src="{{ ('/portal/ample/plugins/bower_components/clockpicker/dist/jquery-clockpicker.min.js') }}"></script>
    <!-- Date Picker Plugin JavaScript -->
    <script src="{{ ('/portal/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
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

	<script>
		function goBack() {
		  window.history.back();

		}

		$(document).ready(function() {
			
			$( "#idgroup" ).click(function() {
				var idkat = $( this ).val();
				$(".subkat-"+idkat).attr('display', 'none');
				// $('.subkat-'+idkat).hide();
			});

			$('#tfile').bind('change', function() {
  				if (this.files[0].size > 1024000) {
  					alert("File yang dipilih terlalu besar, maksimal 1MB. Silahkan pilih file lain");
  					$('#tfile').val(''); 
  				}
			});

			

		});
	</script>
@endsection