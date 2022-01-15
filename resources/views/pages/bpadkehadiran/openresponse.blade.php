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
					<div class="panel panel-info">
						<div class="panel-heading">Respon {{ $form['judul'] }}</div>
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <table class="table table-bordered" style="font-weight:bold">
											@if($form['sts'] == 2)
											<tr>
                                                <td>Peserta Hadir</td>
                                                <td>{{ $emps[0]['totalorang'] }}</td>
                                            </tr>
											<tr>
                                                <td>OPD Hadir</td>
                                                <td>{{ $emps[0]['totalhadir'] }}</td>
                                            </tr>
											<tr>
                                                <td>Tidak Hadir</td>
                                                <td>{{ $total - $emps[0]['totalhadir'] }}</td>
                                            </tr>
											@else
                                            <tr>
                                                <td>Hadir</td>
                                                <td>{{ $emps[0]['totalhadir'] }}</td>
                                            </tr>
                                            <tr>
                                                <td>Tidak Hadir</td>
                                                <td>{{ count($emps) - $emps[0]['totalhadir'] }}</td>
                                            </tr>
											@endif
                                        </table>
                                    </div>
                                </div>
								<div class="row m-t-20">
									<div class="col-md-3">
										{{-- <button id="to-excel" class="btn btn-success">Export to Excel</button> --}}
										<a href="/portal/form/{{$form['no_form']}}/excel" target="_blank"><button class="btn btn-success">Export to Excel</button></a>
									</div>
								</div>
								<div class="row m-t-20">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table id="myTable" class=" table table-hover">
												@if($form['sts'] == 2)
												<thead>
                                                    <tr>
                                                        <th>No</th>
														<th>Kolok</th>
														<th>OPD</th>
                                                        <th>Nama</th>
                                                        <th>NIP & NRK</th>
                                                        <th>Telp & Email</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($emps as $key => $emp)
                                                    <tr>
                                                        <td style="vertical-align: middle;">{{ $key + 1 }}</td>
														<td style="vertical-align: middle;">
															{{ $emp['kolok'] }}<br>
															<span class="text-muted">{{ $emp['kolokdagri'] }}</span>
														</td>
                                                        <td style="vertical-align: middle;">{{ $emp['nalok'] }}</td>
                                                        <td style="vertical-align: middle;">{{ $emp['nama'] ?? '-' }}</td>
                                                        <td style="vertical-align: middle;">
															{{ $emp['nrk'] ?? '-' }}<br>
															<span class="text-muted">{{ $emp['nip'] ?? '-' }}</span>
														</td>
                                                        <td style="vertical-align: middle;">
															{{ $emp['telp'] ?? '-' }}<br>
															<span class="text-muted">{{ $emp['email'] ?? '-' }}</span>
														</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
												@else
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>NIP</th>
                                                        <th>NRK</th>
                                                        <th>Nama</th>
                                                        <th>Unit</th>
                                                        <th class="col-md-2">Kehadiran</th>
														<th>Keterangan</th>
														<th>Foto</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($emps as $key => $emp)
                                                    <tr <?php if(strlen($emp['kd_unit']) < 10): ?> style="font-weight:bold;" <?php endif ?>>
                                                        <td style="vertical-align: middle;">{{ $key + 1 }}</td>
                                                        <td style="vertical-align: middle;">{{ ( is_null($emp['nip_emp']) || $emp['nip_emp'] == '' ? '-' : $emp['nip_emp'] ) }}</td>
                                                        <td style="vertical-align: middle;">{{ ( is_null($emp['nrk_emp']) || $emp['nrk_emp'] == '' ? '-' : $emp['nrk_emp'] ) }}</td>
                                                        <td style="vertical-align: middle;">{{ $emp['nm_emp'] }}</td>
                                                        <td style="vertical-align: middle;">{{ $emp['nm_unit'] }}</td>
                                                        <td style="vertical-align: middle;">{{ $emp['hadir'] }}</td>
														<td style="vertical-align: middle;">{{ $emp['ket_tdk_hadir'] }}</td>
														<td style="vertical-align: middle;">{{ $emp['foto'] }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
												@endif
                                            </table>
                                        </div>
                                    </div>
								</div>
							</div>
						</div>
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
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>

	<script src="{{ ('/portal/public/js/jquery.tableToExcel.js') }}"></script>

    <script>
		$(function () {
			$('#to-excel').click(function () {
				$('#myTable').tblToExcel();
			});

			$('#myTable').DataTable({
				"paging": false,
			});
		});
	</script>
@endsection