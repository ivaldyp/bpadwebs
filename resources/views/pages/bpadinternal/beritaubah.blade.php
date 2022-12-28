@extends('layouts.masterhome')

@section('css')
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/portal/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<!-- summernotes CSS -->
	<link href="{{ ('/portal/ample/plugins/bower_components/summernote/dist/summernote.css') }}" rel="stylesheet" />
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
					<form class="form-horizontal" method="POST" action="/portal/internal/form/ubahberita" data-toggle="validator" enctype="multipart/form-data">
					@csrf
						<div class="panel panel-info">
							<div class="panel-heading"> Ubah Berita </div>
							<div class="panel-wrapper collapse in" aria-expanded="true">
								<div class="panel-body">

									<input type="hidden" name="ids" value="{{ $ids }}">

									<div class="form-group">
                                        <label for="tipe" class="col-md-2 control-label"> Kategori </label>
                                        <div class="col-md-8">
                                            <select class="form-control" name="tipe" id="tipe">
                                                <option value="Internal" <?php if ($berita['tipe'] == 'Internal' ): ?> selected <?php endif ?> > Internal </option>
                                                <option value="UKPD" <?php if ($berita['tipe'] == 'UKPD' ): ?> selected <?php endif ?> > UKPD </option>
                                            </select>
                                        </div>
                                    </div>

									<div class="form-group">
										<label for="tanggal" class="col-md-2 control-label"> Waktu Input </label>
										<div class="col-md-8">
											<input type="text" class="form-control" id="tanggal" name="tanggal" autocomplete="off" value="{{ date('d/m/Y H:i:s', strtotime(str_replace('/', '-', $berita['tanggal']))) }}">
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-2 control-label"> Editor </label>
										<div class="col-md-8">
											<input autocomplete="off" type="text" class="form-control" disabled value="{{ $berita['an'] }}">
										</div>
									</div>

									<div class="form-group">
                                        <label for="isi" class="col-md-2 control-label"> Isi </label>
                                        <div class="col-md-8">
                                            <textarea class="summernote form-control" rows="15" placeholder="Enter text ..." name="isi">{!! html_entity_decode($berita['isi']) !!}</textarea>
                                        </div>
                                    </div>

								</div>
							</div>
							<div class="panel-footer">
                                <button type="submit" class="btn btn-info pull-right">Simpan</button>
                                @if($berita['appr'] == 'N')
                                <a href="/portal/internal/form/apprberita?ids={{ $berita['ids'] }}&appr=Y"><button type="button" class="btn btn-success pull-right" style="margin-right: 10px">Setuju</button></a>
                                @else
                                <a href="/portal/internal/form/apprberita?ids={{ $berita['ids'] }}&appr=N"><button type="button" class="btn btn-danger pull-right" style="margin-right: 10px">Batal Setuju</button></a>
                                @endif
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
	<script>
		jQuery(document).ready(function () {
			$('.summernote').summernote({
				height: 350, // set editor height
				width: 900,
				minHeight: null, // set minimum height of editor
				maxHeight: null, // set maximum height of editor
				focus: false // set focus to editable area after initializing summernote
			});
		});

		function goBack() {
		  window.history.back();
		}
	</script>
@endsection