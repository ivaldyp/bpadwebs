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
						<div class="panel-heading">Detail QRAbsen BPAD</div>
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
                                <div class="row p-b-20">
                                    <div class="col-md-6">
                                        <form method="GET" action="{{ url('/qrabsen/detail') }}">
                                            <select class="form-control select2" name="unit" id="unit" required onchange="this.form.submit()">
                                            <?php foreach ($units as $key => $unit) { ?>
                                                <option value="{{ $unit['kd_unit'] }}" 
                                                <?php 
                                                    if ($idunit == $unit['kd_unit']) {
                                                        echo "selected";
                                                    }
                                                ?>
                                                >[{{ $unit['kd_unit'] }}]
                                                    @if ( strlen($unit['kd_unit'] > 2) && substr($unit['kd_unit'], 4, 2) == '51' )
                                                        [JAKARTA PUSAT]
                                                    @elseif ( strlen($unit['kd_unit'] > 2) && substr($unit['kd_unit'], 4, 2) == '52' )
                                                        [JAKARTA UTARA]
                                                    @elseif ( strlen($unit['kd_unit'] > 2) && substr($unit['kd_unit'], 4, 2) == '53' )
                                                        [JAKARTA BARAT]
                                                    @elseif ( strlen($unit['kd_unit'] > 2) && substr($unit['kd_unit'], 4, 2) == '54' )
                                                        [JAKARTA SELATAN]
                                                    @elseif ( strlen($unit['kd_unit'] > 2) && substr($unit['kd_unit'], 4, 2) == '55' )
                                                        [JAKARTA TIMUR]
                                                    @elseif ( strlen($unit['kd_unit'] > 2) && substr($unit['kd_unit'], 4, 2) == '56' )
                                                        [KEPULAUAN SERIBU]
                                                    @elseif ( strlen($unit['kd_unit'] > 2) && substr($unit['kd_unit'], 4, 2) == '06' )
                                                        [PPBD]
                                                    @elseif ( strlen($unit['kd_unit'] > 2) && substr($unit['kd_unit'], 4, 2) == '07' )
                                                        [PUSDATIN ASET]
                                                    @elseif ( strlen($unit['kd_unit'] > 2) && substr($unit['kd_unit'], 4, 2) == '08' )
                                                        [UPMA]
                                                    @endif
                                                        - {{ ($unit['kd_unit'] == '01' ? 'SEMUA' : $unit['notes'])   }}</option>
                                            <?php } ?>
                                            </select>
                                            <input type="hidden" name="qr" value="{{ $qr }}">
                                        </form>
                                    </div>
                                </div>
                                {{-- <div class="row">
                                    <div class="col-md-4">
                                        <table class="table table-bordered" style="font-weight:bold">
                                            <tr>
                                                <td>Hadir</td>
                                                <td>{{ $emps[0]['totalhadir'] }}</td>
                                            </tr>
                                            <tr>
                                                <td>Tidak Hadir</td>
                                                <td>{{ count($emps) - $emps[0]['totalhadir'] }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div> --}}
								<div class="row">
									<div class="table-responsive">
										<table class="myTable table table-hover table-striped table-compact">
											<thead>
												<tr>
													<th>ID emp</th>
													<th>NIP & NRK</th>
													<th>Nama</th>
                                                    <th>Bidang</th>
													<th>Unit Kerja</th>
													<th class="col-md-1">Waktu</th>
													<th class="col-md-1">Status Hadir</th>
													<th class="col-md-1">Wajib Apel</th>
												</tr>
											</thead>
											<tbody>
                                                @php
                                                $bidangnow = '';
                                                @endphp

                                                @foreach($emps as $key => $emp)
                                                
                                                @php 
                                                if(strlen($emp['kd_unit']) == 6) {
                                                    $bidangnow = $emp['nm_unit'];
                                                } elseif (strlen($emp['kd_unit']) == 2) {
                                                    $bidangnow = "KEPALA BADAN PENGELOLAAN ASET DAERAH";
                                                }
                                                @endphp
                                                
                                                <tr 
                                                    @if(strlen($emp['kd_unit']) < 10)
                                                    style="font-weight:bold;"
                                                    @endif
                                                >
                                                    <td class="ver-align-mid">{{ $emp['id_emp'] }}</td>
                                                    <td class="ver-align-mid">
                                                        {{ $emp['nip_emp'] && $emp['nip_emp'] != '-' ? $emp['nip_emp'] : '-' }}
                                                        <br>
                                                        {{ $emp['nrk_emp'] && $emp['nrk_emp'] != '-' ? $emp['nrk_emp'] : '-' }}
                                                    </td>
                                                    <td class="ver-align-mid">{{ strtoupper($emp['nm_emp']) }}</td>
                                                    <td class="ver-align-mid">{{ $bidangnow }}</td>
                                                    <td class="ver-align-mid">{{ $emp['nm_unit'] }}</td>
                                                    <td class="ver-align-mid">
                                                        @if($emp['kd_unit'] == '01' && $emp['sts'] == NULL)
                                                            {{-- @php
                                                            $min_epoch = strtotime($getref['start_datetime']);
                                                            $max_epoch = strtotime($getref['end_datetime']);
                                                        
                                                            $rand_epoch = rand($min_epoch, $max_epoch);
                                                            @endphp  --}}
                                                            @php
                                                                $date = date('H:i:s', strtotime($getref['start_datetime']));
                                                                $newDate = date('d-M-Y H:i:s', strtotime($date. ' +12 minutes'));
                                                            @endphp
                                                        
                                                            {{ $newDate }}
                                                        @else 
                                                            @if($emp['datetime'])
                                                            {{ date('d-M-Y', strtotime($emp['datetime'])) }}
                                                            <br>
                                                            {{ date('H:i:s', strtotime($emp['datetime'])) }}
                                                            @else
                                                            -
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td class="ver-align-mid">
                                                        @if($emp['kd_unit'] == '01' && $emp['sts'] == NULL)
                                                            HADIR
                                                        @else
                                                            {{ $emp['kehadiran'] }}
                                                            <br>
                                                            @if($emp['sts'] == 2)
                                                                @if($emp['nm_sub_absen'])
                                                                    {{ $emp['nm_sub_absen'] }} <br> {{ $emp['nm_subsub_absen'] }}
                                                                @else
                                                                    ALPA
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td class="ver-align-mid">
                                                        @if($emp['nm_sub_absen'])
                                                            TIDAK WAJIB APEL
                                                        @else
                                                            {{ $emp['tidak_wajib_apel'] }}
                                                        @endif
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