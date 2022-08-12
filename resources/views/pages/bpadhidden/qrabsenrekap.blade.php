@extends('layouts.masterhome')

@section('css')
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/portal/public/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ ('/portal/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
	<!-- Menu CSS -->
	<link href="{{ ('/portal/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
    <!-- Clock Picker css -->
    <link href="{{ ('/portal/public/ample/plugins/bower_components/clockpicker/dist/jquery-clockpicker.min.css') }}" rel="stylesheet">
    <!-- Date picker plugins css -->
    <link href="{{ ('/portal/public/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
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
    <style>
        .ver-align-mid {
            vertical-align: middle !important;
        }

        .hor-align-mid {
            text-align: center !important;
        }
    </style>
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
			<div class="row ">
				<div class="col-md-12">
					<!-- <div class="white-box"> -->
					<div class="panel panel-default">
						<div class="panel-heading">Rekap QRAbsen BPAD</div>
						<div class="panel-wrapper collapse in">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12 table-responsive">
                                        <h3><strong>{{ $getref['nama_kegiatan'] }}</strong></h3>
                                        <table class="display no-wrap dataTable">
                                            <thead>
                                                <th>No</th>
                                                <th>BIDANG</th>
                                                <th class="hor-align-mid" style="border-right: 1px solid black">TOTAL PEGAWAI</th>
                                                <th class="hor-align-mid">WAJIB APEL</th>
                                                <th class="hor-align-mid" style="border-right: 1px solid black">SAKIT/CUTI/DL/IZIN</th>
                                                <th class="hor-align-mid">WAJIB ABSEN</th>
                                                <th class="hor-align-mid">HADIR</th>
                                                <th class="hor-align-mid">PERSENTASE</th>
                                            </thead>
                                            <tbody>
                                                @foreach($getrekapabsen as $key => $rekap)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $rekap['nm_unit'] }}</td>
                                                    <td class="hor-align-mid" style="border-right: 1px solid black">{{ $rekap['total_pegawai'] }}</td>
                                                    <td class="hor-align-mid">{{ $rekap['total_wajibapel'] }}</td>
                                                    <td class="hor-align-mid" style="border-right: 1px solid black">{{ $rekap['total_izin'] }}</td>
                                                    <td class="hor-align-mid">{{ $rekap['total_wajib_absen'] }}</td>
                                                    <td class="hor-align-mid">{{ $rekap['total_hadir'] }}</td>
                                                    <td class="hor-align-mid" style="font-weight: bold;">
                                                        @if($rekap['total_wajib_absen'] == 0)
                                                            @php
                                                                $rekap['total_wajib_absen'] = 1;
                                                            @endphp
                                                        @endif
                                                        {{ number_format($rekap['total_hadir'] / $rekap['total_wajib_absen'] * 100, 2) }}%
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
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
    <!-- Clock Plugin JavaScript -->
    <script src="{{ ('/portal/public/ample/plugins/bower_components/clockpicker/dist/jquery-clockpicker.min.js') }}"></script>
    <!-- Date Picker Plugin JavaScript -->
    <script src="{{ ('/portal/public/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
	<!-- Custom Theme JavaScript -->
	<script src="{{ ('/portal/public/ample/js/custom.min.js') }}"></script>
	<script src="{{ ('/portal/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>

	<script>
		$(function () {
            jQuery('.datepicker-autoclose').datepicker({
                autoclose: true
				, todayHighlight: true
				, format: 'dd/mm/yyyy'
            });

            $('.clockpicker').clockpicker({
                donetext: 'Done'
            , }).find('input').change(function () {
                console.log(this.value);
            });

			$('.myTable').DataTable({
                "ordering" : false,
                "paging": false,
            });
		});
	</script>
@endsection