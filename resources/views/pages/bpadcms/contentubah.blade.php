@extends('layouts.masterhome' )

@section('css')
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/portal/public/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ ('/portal/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
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
					<div class="panel panel-default">
						<div class="panel-heading">Tambah Konten</div>
						<form class="form-horizontal" method="POST" action="/portal/cms/form/ubahcontent" data-toggle="validator" enctype="multipart/form-data">
						@csrf   
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<input type="hidden" name="ids" value="{{ $ids }}">
									<input type="hidden" name="idkat" value="{{ $idkat }}"> 

									<div class="form-group">
										<label class="col-md-2 control-label"> Headline? </label>
										<div class="radio-list col-md-8">
											<label class="radio-inline">
												<div class="radio radio-info">
													<input type="radio" name="headline" id="headline1" value="H," data-error="Pilih salah satu" <?php if ($content['tipe'] == 'H,' ): ?> checked <?php endif ?> >
													<label for="headline1">Ya</label> 
												</div>
											</label>
											<label class="radio-inline">
												<div class="radio radio-info">
													<input type="radio" name="headline" id="headline2" value="" <?php if ($content['tipe'] == '' ||  $content['tipe'] == null): ?> checked <?php endif ?> >
													<label for="headline2">Tidak</label>
												</div>
											</label>
											<div class="help-block with-errors"></div>  
										</div>
									</div>

									@if($idkat != 14 && $idkat != 6 && $idkat != 19 && $idkat != 4 && $idkat != 11 && $idkat != 23)
									<div class="form-group">
										<label for="subkat" class="col-md-2 control-label"><span style="color: red">*</span> Subkategori </label>
										<div class="col-md-8">
											<select class="form-control" name="subkat" id="subkat" required>
												@foreach($subkats as $subkat)
													<option value="{{ $subkat['subkat'] }}" <?php if ($subkat['subkat'] == $content['subkat'] ): ?> selected <?php endif ?> > {{ $subkat['subkat'] }} </option>
												@endforeach
											</select>
										</div>
									</div>
									@endif

									<div class="form-group">
										<label for="tanggal" class="col-md-2 control-label"> Waktu </label>
										<div class="col-md-8">
											<input type="text" class="form-control" id="tanggal" name="tanggal" autocomplete="off" data-error="Masukkan tanggal" value="{{ date('d/m/Y H:i:s', strtotime(str_replace('/', '-', $content['tanggal']))) }}">
										</div>
									</div>

									<div class="form-group">
										<label for="judul" class="col-md-2 control-label"><span style="color: red">*</span> Judul </label>
										<div class="col-md-8">
											<input type="text" class="form-control" id="judul" name="judul" autocomplete="off" data-error="Masukkan judul" required value="{{ $content['judul'] }}">
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
											<input type="text" class="form-control" id="url" name="url" autocomplete="off" value="{{ $content['url'] }}">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									@endif

									@if($idkat != 6 && $idkat != 4)
									<div class="form-group">
										<label for="isi1" class="col-md-2 control-label"> Ringkasan </label>
										<div class="col-md-8">
											<textarea class="summernote form-control" rows="15" placeholder="Enter text ..." name="isi1">{!! html_entity_decode($content['isi1']) !!}</textarea>
										</div>
									</div>
									@endif

									@if($idkat != 6 && $idkat != 5 && $idkat != 19 && $idkat != 4 && $idkat != 11)
									<div class="form-group">
										<label for="isi2" class="col-md-2 control-label"> Isi </label>
										<div class="col-md-8">
											<textarea class="summernote form-control" rows="15" placeholder="Enter text ..." name="isi2">{!! html_entity_decode($content['isi2']) !!}</textarea>
										</div>
									</div>
									@endif
									
									<div class="form-group">
										<label for="editor" class="col-md-2 control-label"> Editor </label>
										<div class="col-md-8">
											<input disabled type="text" class="form-control" id="editor" name="editor" autocomplete="off" value="{{ $content['editor'] }}">
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-2 control-label"> Suspend? </label>
										<div class="radio-list col-md-8">
											<label class="radio-inline">
												<div class="radio radio-info">
													<input type="radio" name="suspend" id="suspend1" value="Y" data-error="Pilih salah satu" <?php if ($content['suspend'] == 'Y' ): ?> checked <?php endif ?> >
													<label for="suspend1">Ya</label> 
												</div>
											</label>
											<label class="radio-inline">
												<div class="radio radio-info">
													<input type="radio" name="suspend" id="suspend2" value="N" <?php if ($content['suspend'] == ''): ?> checked <?php endif ?> >
													<label for="suspend2">Tidak</label>
												</div>
											</label>
											<div class="help-block with-errors"></div>  
										</div>
									</div>
									@if($content['appr'] == 'N')
									<input type="hidden" name="appr" value="Y">
									@else 
									<input type="hidden" name="appr" value="N">
									@endif
								</div>
								<div class="panel-footer">
									<input type="submit" name="btnSimpan" class="btn btn-info pull-right m-r-10" value="Simpan">
									
									@if($content['appr'] == 'N')
									<input type="submit" name="btnAppr" class="btn btn-success pull-right m-r-10" value="Setuju">
									@else
									<input type="submit" name="btnAppr" class="btn btn-danger pull-right m-r-10" value="Batal Setuju">
									@endif
									<button type="button" class="btn btn-default pull-right m-r-10" onclick="goBack()">Kembali</button>
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
		});
	</script>
@endsection