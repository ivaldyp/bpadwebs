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
					<form class="form-horizontal" method="POST" action="/portal/internal/form/ubahagenda" data-toggle="validator" enctype="multipart/form-data">
					@csrf
						<div class="panel panel-info">
							<div class="panel-heading"> Ubah Agenda </div>
							<div class="panel-wrapper collapse in" aria-expanded="true">
								<div class="panel-body">

									<input type="hidden" name="ids" value="{{ $ids }}">

									<div class="form-group">
										<label for="tgl_masuk" class="col-md-2 control-label"> Waktu Input </label>
										<div class="col-md-8">
											<input type="text" class="form-control" id="dtanggal" name="dtanggal" autocomplete="off" value="{{ date('d/m/Y H:i:s', strtotime(str_replace('/', '-', $agenda['dtanggal']))) }}">
										</div>
									</div>

									<div class="form-group">
										<label for="ddesk" class="col-md-2 control-label"> Deskripsi </label>
										<div class="col-md-8">
											<input autocomplete="off" type="text" name="ddesk" class="form-control" id="ddesk" value="{{ $agenda['ddesk'] }}">
										</div>
									</div>

									<div class="form-group">
										<label for="kode_disposisi" class="col-md-2 control-label"> Editor </label>
										<div class="col-md-8">
											<input autocomplete="off" type="text" class="form-control" disabled value="{{ $agenda['an'] }}">
										</div>
									</div>

									<div class="form-group">
										<label for="kode_disposisi" class="col-md-2 control-label"> Tipe </label>
										
                                        <div class="col-md-4">
	                                        <input type="checkbox" name="tipe[]" id="checkbox_internal" value="Internal" <?php if (substr(strtolower($agenda['tipe']), 0, 5) == 'inter' ): ?> checked <?php endif ?> >
	                                        <label for="checkbox_internal"> Internal </label>
	                                        <br>
	                                        <input type="checkbox" name="tipe[]" id="checkbox_UKPD" value="UKPD" <?php if (substr(strtolower($agenda['tipe']), -5) == 'ukpd,' ): ?> checked <?php endif ?> >
	                                        <label for="checkbox_UKPD"> UKPD </label>
	                                    </div>
									</div>

									<div class="form-group">
                                        <label for="nm_file" class="col-lg-2 control-label"> Unduh File </label>
                                        <div class="col-lg-8">
                                            <p class="form-control-static">
												<a target="_blank" href="{{ config('app.openfileagenda') }}/{{ $agenda['dfile'] }}"><i class="fa fa-download"></i> {{ $agenda['dfile'] }}</a>
											</p>
                                        </div>
                                    </div>

									<div class="form-group">
                                        <label for="dfile" class="col-lg-2 control-label"> File</label>
                                        <div class="col-lg-8">
                                            <input type="file" class="form-control" id="dfile" name="dfile">
                                        </div>
                                    </div>
								</div>
							</div>
							<div class="panel-footer">
                                <button type="submit" class="btn btn-info pull-right">Simpan</button>
                                @if($agenda['appr'] == 'N')
                                <a href="/portal/internal/form/appragenda?ids={{ $agenda['ids'] }}&appr=Y"><button type="button" class="btn btn-success pull-right" style="margin-right: 10px">Setuju</button></a>
                                @else
                                <a href="/portal/internal/form/appragenda?ids={{ $agenda['ids'] }}&appr=N"><button type="button" class="btn btn-danger pull-right" style="margin-right: 10px">Batal Setuju</button></a>
                                @endif
                                <button type="button" class="btn btn-default pull-right m-r-10" onclick="goBack()">Kembali</button>
                                <!-- <button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Kembali</button> -->
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

	<script type="text/javascript">
		function goBack() {
		  window.history.back();
		}
	</script>

@endsection