@extends('layouts.masterhome')

@section('css')
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/portal/public/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ ('/portal/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
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
					<!-- <div class="white-box"> -->
					<div class="panel panel-default">
						<div class="panel-heading text-center">Report</div>
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="white-box text-center">
                                            <h2 class="text-center">Laporan Pegawai Pensiun</h2>
                                            <button class="fcbtn btn btn-outline btn-success btn-1d btn-excel-pensiun m-r-10" type="button" data-toggle="modal" data-target="#modal-excel-pensiun">Excel</button>
                                            {{-- <button class="fcbtn btn btn-outline btn-danger btn-1d btn-excel-pensiun m-r-10">PDF</button>
                                            <button class="fcbtn btn btn-outline btn-warning btn-1d btn-excel-pensiun m-r-10">View</button> --}}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="white-box text-center">
                                            <h2 class="text-center">Laporan Pegawai Naik Golongan</h2>
                                            <button class="fcbtn btn btn-outline btn-success btn-1d btn-excel-naikgol m-r-10" type="button" data-toggle="modal" data-target="#modal-excel-naikgol">Excel</button>
                                            {{-- <button class="fcbtn btn btn-outline btn-danger btn-1d btn-excel-naikgol m-r-10">PDF</button>
                                            <button class="fcbtn btn btn-outline btn-warning btn-1d btn-excel-naikgol m-r-10">View</button> --}}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="white-box text-center">
                                            <h2 class="text-center">Laporan Pegawai Pensiun</h2>
                                            <button class="fcbtn btn btn-outline btn-success btn-1d btn-excel-pensiun m-r-10" type="button" data-toggle="modal" data-target="#modal-excel-pensiun">Excel</button>
                                            {{-- <button class="fcbtn btn btn-outline btn-danger btn-1d btn-excel-pensiun m-r-10">PDF</button>
                                            <button class="fcbtn btn btn-outline btn-warning btn-1d btn-excel-pensiun m-r-10">View</button> --}}
                                        </div>
                                    </div>
                                </div>
							</div>
						</div>
					</div>
				</div>
			</div>
            <div id="modal-excel-pensiun" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="GET" action="{{ route('kepegawaian.report.excelpensiun') }}" class="form-horizontal" data-toggle="validator">
							<div class="modal-header">
								<h4 class="modal-title"><b>Pilih Tahun Pensiun</b></h4>
							</div>
							<div class="modal-body">
								<div class="form-group">
									<label for="tahun_pensiun" class="col-md-2 control-label"><span style="color: red">*</span> Tahun </label>
									<div class="col-md-8">
										<select class="form-control select2" name="tahun_pensiun" id="tahun_pensiun" required>
                                            @for($i=2017; $i<=2055; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
										</select>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-success pull-right">Submit</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
            <div id="modal-excel-naikgol" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="GET" action="{{ route('kepegawaian.report.excelnaikgol') }}" class="form-horizontal" data-toggle="validator">
							<div class="modal-header">
								<h4 class="modal-title"><b>Pilih Tahun Kenaikan Golongan</b></h4>
							</div>
							<div class="modal-body">
								<div class="form-group">
									<label for="tahun_naikgol" class="col-md-2 control-label"><span style="color: red">*</span> Tahun </label>
									<div class="col-md-8">
										<select class="form-control select2" name="tahun_naikgol" id="tahun_naikgol" required>
                                            @for($i=date('Y') - 4; $i<=date('Y') + 4; $i++)
                                            <option @if($i == date('Y')) selected @endif value="{{ $i }}">{{ $i }}</option>
                                            @endfor
										</select>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-success pull-right">Submit</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
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
	<script src="{{ ('/portal/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>

	<script>
		$(function () {
			$('.myTable').DataTable();
		});
	</script>
@endsection