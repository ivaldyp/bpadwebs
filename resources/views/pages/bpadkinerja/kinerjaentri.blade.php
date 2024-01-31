@extends('layouts.masterhome')

@section('css')
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/portal/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ ('/portal/ample/plugins/bower_components/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
	<!-- Menu CSS -->
	<link href="{{ ('/portal/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
    <!-- Page plugins css -->
	<link href="{{ ('/portal/ample/plugins/bower_components/clockpicker/dist/jquery-clockpicker.min.css') }}" rel="stylesheet">
	<!-- Date picker plugins css -->
	<link href="{{ ('/portal/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
	<!-- animation CSS -->
	<link href="{{ ('/portal/ample/css/animate.css') }}" rel="stylesheet">
    <!-- Alerts CSS -->
    <link href="{{ ('/portal/ample/plugins/bower_components/sweetalert/sweetalert.css') }}" rel="stylesheet" type="text/css">
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
                        <div class="panel-heading"> Entri Kinerja </div>
                    	<div class="panel-wrapper collapse in">
                            <div class="panel-body">
                                @if ($access['zadd'] == 'y' && $_SESSION['user_data']['id_emp'] )
                                <div class="row ">
                                    <div class="col-md-12">
                                        <form class="form-horizontal" method="POST" action="/portal/kepegawaian/form/tambahaktivitas" data-toggle="validator" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id_emp" value="{{ Auth::user()->id_emp }}" id="idemp">
                    
                                        <div class="form-group">
                                            <label for="tgl_masuk" class="col-md-2 control-label"> Tanggal </label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control datepicker-autoclose" id="tgl_trans" name="tgl_trans" autocomplete="off" value="{{ old('tgl_trans') ?? date('d/m/Y') }}">
                                            </div>
                                        </div>
    
                                        <div class="form-group">
                                            <label for="tipe" class="col-md-2 control-label"> Kehadiran </label>
                                            <div class="col-md-4">
                                                <select class="form-control" name="tipe_hadir" id="tipe_hadir">
                                                    <option {{ (old('tipe_hadir') == 1 ? "selected":"") }} value="1"> Hadir </option>
                                                    <option {{ (old('tipe_hadir') == 2 ? "selected":"") }} value="2"> Tidak Hadir </option>
                                                    <option {{ (old('tipe_hadir') == 3 ? "selected":"") }} value="3"> DL Full </option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <select class="form-control" name="jns_hadir" id="jns_hadir">
                                                    <optgroup label="Hadir">
                                                        <option {{ (old('jns_hadir') == "Tepat Waktu (8,5 jam/hari)" ? "selected":"") }} class="select_jns_hadir tipe-1" id="1-first" value="Tepat Waktu (8,5 jam/hari)"> Tepat Waktu (8,5 jam/hari) </option>
                                                        <option {{ (old('jns_hadir') == "Dinas Luar Awal" ? "selected":"") }} class="select_jns_hadir tipe-1" value="Dinas Luar Awal"> Dinas Luar Awal </option>
                                                        <option {{ (old('jns_hadir') == "Dinas Luar Akhir" ? "selected":"") }} class="select_jns_hadir tipe-1" value="Dinas Luar Akhir"> Dinas Luar Akhir </option>
                                                        <option {{ (old('jns_hadir') == "Terlambat" ? "selected":"") }} class="select_jns_hadir tipe-1" value="Terlambat"> Terlambat </option>
                                                        <option {{ (old('jns_hadir') == "Pulang Cepat" ? "selected":"") }} class="select_jns_hadir tipe-1" value="Pulang Cepat"> Pulang Cepat </option>
                                                    </optgroup>
                                                    <optgroup label="Tidak Hadir">
                                                        <option {{ (old('jns_hadir') == "Sakit" ? "selected":"") }} class="select_jns_hadir tipe-2" id="2-first" value="Sakit"> Sakit </option>
                                                        <option {{ (old('jns_hadir') == "Izin" ? "selected":"") }} class="select_jns_hadir tipe-2" value="Izin"> Izin </option>
                                                        <option {{ (old('jns_hadir') == "Cuti" ? "selected":"") }} class="select_jns_hadir tipe-2" value="Cuti"> Cuti </option>
                                                        <option {{ (old('jns_hadir') == "Alpa" ? "selected":"") }} class="select_jns_hadir tipe-2" value="Alpa"> Alpa </option>
                                                    </optgroup>
                                                    <optgroup label="DL Full">
                                                        <option {{ (old('jns_hadir') == "Rapat" ? "selected":"") }} class="select_jns_hadir tipe-3" id="3-first" value="Rapat"> Rapat </option>
                                                        <option {{ (old('jns_hadir') == "Peninjauan Lapangan" ? "selected":"") }} class="select_jns_hadir tipe-3" value="Peninjauan Lapangan"> Peninjauan Lapangan </option>
                                                    </optgroup>
                                                    <optgroup label="Lainnya">
                                                        <option {{ (old('jns_hadir') == "Lainnya" ? "selected":"") }} class="select_jns_hadir tipe-2 lainnya" value="Lainnya"> Lainnya (sebutkan) </option>
                                                    </optgroup>
                                                </select>
                                            </div>
                                        </div>
    
                                        <div class="form-group" id="input_lainnya">
                                            <label for="lainnya" class="col-md-2 control-label"> Lainnya </label>
                                            <div class="col-md-8">
                                                <textarea class="form-control" name="lainnya" id="lainnya"></textarea>
                                            </div>
                                        </div>
    
                                        <hr>
    
                                        <div id="form_aktivitas">
                                            <div class="form-group">
                                                <label for="tgl_masuk" class="col-md-2 control-label"> Awal </label>
                                                <div class="col-md-3">
                                                    <div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true">
                                                        <input type="text" class="form-control" value="{{ old('time1', '00:00') }}" name="time1" id="time1"> <span class="input-group-addon"> <span class="glyphicon glyphicon-time"></span> </span>
                                                    </div>
                                                </div>
                                                <label for="tgl_masuk" class="col-md-2 control-label"> Akhir </label>
                                                <div class="col-md-3">
                                                    <div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true">
                                                        <input type="text" class="form-control" value="{{ old('time2', '00:00') }}" name="time2" id="time2"> <span class="input-group-addon"> <span class="glyphicon glyphicon-time"></span> </span>
                                                    </div>
                                                </div>
                                            </div>
    
                                            <div class="form-group" id="input_uraian">
                                                <label for="uraian" class="col-md-2 control-label"> Uraian </label>
                                                <div class="col-md-8">
                                                    <textarea class="form-control" name="uraian" id="uraian">{{ old('uraian') }}</textarea>
                                                </div>
                                            </div>
    
                                            <div class="form-group" id="input_keterangan">
                                                <label for="keterangan" class="col-md-2 control-label"> Keterangan </label>
                                                <div class="col-md-8">
                                                    <textarea class="form-control" name="keterangan" id="keterangan">{{ old('keterangan') }}</textarea>
                                                </div>
                                            </div>
    
                                        </div>
                                        
                                        <div class="col-md-10">	
                                            <button type="submit" class="btn btn-info m-b-20 m-l-20 pull-right" id="">Tambah Aktivitas</button>
                                        </div>
                                      
                                        </form>
                                    </div>
                                </div>	
                                @endif						
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row ">
				<div class="col-md-12">
					<!-- <div class="white-box"> -->
					<div class="panel panel-info">
                        <div class="panel-heading"> Lihat Kinerja </div>
                    	<div class="panel-wrapper collapse in">
                            <div class="panel-body">
                                <div class="row" style="margin-bottom: 10px">
									<form method="GET" action="/portal/kepegawaian/entri kinerja">
										<div class="col-md-2">
											<select class="form-control" name="now_month" id="now_month">
												<option <?php if ($now_month == 1): ?> selected <?php endif ?> value="1">Januari</option>
												<option <?php if ($now_month == 2): ?> selected <?php endif ?> value="2">Februari</option>
												<option <?php if ($now_month == 3): ?> selected <?php endif ?> value="3">Maret</option>
												<option <?php if ($now_month == 4): ?> selected <?php endif ?> value="4">April</option>
												<option <?php if ($now_month == 5): ?> selected <?php endif ?> value="5">Mei</option>
												<option <?php if ($now_month == 6): ?> selected <?php endif ?> value="6">Juni</option>
												<option <?php if ($now_month == 7): ?> selected <?php endif ?> value="7">Juli</option>
												<option <?php if ($now_month == 8): ?> selected <?php endif ?> value="8">Agustus</option>
												<option <?php if ($now_month == 9): ?> selected <?php endif ?> value="9">September</option>
												<option <?php if ($now_month == 10): ?> selected <?php endif ?> value="10">Oktober</option>
												<option <?php if ($now_month == 11): ?> selected <?php endif ?> value="11">November</option>
												<option <?php if ($now_month == 12): ?> selected <?php endif ?> value="12">Desember</option>
											</select>
										</div>
										<div class="col-md-2">
											<?php date_default_timezone_set('Asia/Jakarta'); ?>
											<select class="form-control" name="now_year" id="now_year">
												<option <?php if ($now_year == (int)date('Y')): ?> selected <?php endif ?> value="{{ (int)date('Y') }}">{{ (int)date('Y') }}</option>
												<option <?php if ($now_year == (int)date('Y') - 1): ?> selected <?php endif ?> value="{{ (int)date('Y') - 1 }}">{{ (int)date('Y') - 1 }}</option>
												<option <?php if ($now_year == (int)date('Y') - 2): ?> selected <?php endif ?> value="{{ (int)date('Y') - 2 }}">{{ (int)date('Y') - 2 }}</option>
											</select>
										</div>
                                        <div class="col-md-2">	
                                            <button type="submit" class="btn btn-block btn-info m-b-20 m-l-20 pull-right" id="">Cari</button>
                                        </div>
									</form>
								</div>
                                <div class="row">
									<div class="table-responsive">
										<table class="myTable table table-hover color-table primary-table" >
											<thead>
												<tr>
													<th class="col-md-1">Tanggal</th>
													<th class="col-md-1">Awal</th>
													<th class="col-md-1">Akhir</th>
													<th class="col-md-4">Uraian</th>
													<th class="col-md-4">Keterangan</th>
                                                    <th>Action</th>
												</tr>
											</thead>
                                            @if(count($laporans) == 0)
                                            <tbody>
                                                <tr>
                                                    <td colspan="6">-- Tidak Ada Data --</td>
                                                </tr>
                                            </tbody>
                                            @else
											<tbody>
												@php
												$nowdate = 0
												@endphp

												@foreach($laporans as $laporan)
                                                    @if($nowdate != $laporan['tgl_trans'])
                                                        @php $nowdate = $laporan['tgl_trans'] @endphp
                                                        <tr style="background-color: #f7fafc !important">
                                                            <td colspan="5" style="vertical-align: middle;"><b>
                                                            TANGGAL: {{ date('D, d-M-Y',strtotime($laporan['tgl_trans'])) }} --- 
                                                            {{ $laporan['jns_hadir_app'] ?? $laporan['jns_hadir'] }}

                                                            @if($laporan['jns_hadir_app'] == 'Lainnya (sebutkan)' || $laporan['jns_hadir'] == 'Lainnya (sebutkan)')
                                                            --- {{ $laporan['lainnya'] }}
                                                            @endif

                                                            </b></td>
                                                            <td style="display: none;"></td>
                                                            <td style="display: none;"></td>
                                                            <td style="display: none;"></td>
                                                            <td style="display: none;"></td>
                                                            <td>
                                                                <form method="POST" action="/portal/kepegawaian/form/hapuskinerja">
                                                                    @csrf
                                                                    <input type="hidden" name="idemp" value="{{ $laporan['idemp'] }}">
                                                                    <input type="hidden" name="tgl_trans" value="{{ $laporan['tgl_trans'] }}">
                                                                    <button class="btn btn-block btn-danger sa-warning">Hapus Kinerja</button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endif

                                                    @if($laporan['tipe_hadir_app'] != 2 && $laporan['tipe_hadir'] != 2)
                                                    <tr>
                                                        <td>{{ date('d-M-Y',strtotime($laporan['tgl_trans'])) }}</td>
                                                        <td>{{ date('H:i',strtotime($laporan['time1'])) }}</td>
                                                        <td>{{ date('H:i',strtotime($laporan['time2'])) }}</td>
                                                        <td>{{ $laporan['uraian'] }}</td>
                                                        <td>{{ $laporan['keterangan'] }}</td>
                                                        <td>
                                                            <form method="POST" action="/portal/kepegawaian/form/hapusaktivitas">
                                                                @csrf
                                                                <input type="hidden" name="idemp" value="{{ $laporan['idemp'] }}"> 
                                                                <input type="hidden" name="tgl_trans" value="{{ $laporan['tgl_trans'] }}"> 
                                                                <input type="hidden" name="time1" value="{{ $laporan['time1'] }}"> 
                                                                <input type="hidden" name="uraian" value="{{ $laporan['uraian'] }}"> 
                                                                <input type="hidden" name="keterangan" value="{{ $laporan['keterangan'] }}"> 
                                                                <input type="hidden" name="kinerja_detail_id" value="{{ $laporan['kinerja_detail_id'] }}"> 
                                                                <button class="btn btn-block btn-warning sa-warning">Hapus Aktivitas</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    @endif
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
    <!-- Sweet-Alert  -->
    <script src="{{ ('/portal/ample/plugins/bower_components/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ ('/portal/ample/plugins/bower_components/sweetalert/jquery.sweet-alert.custom.js') }}"></script>
	<!-- Clock Plugin JavaScript -->
	<script src="{{ ('/portal/ample/plugins/bower_components/clockpicker/dist/jquery-clockpicker.min.js') }}"></script>
	<!-- Date Picker Plugin JavaScript -->
	<script src="{{ ('/portal/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
	<!-- Custom Theme JavaScript -->
	<script src="{{ ('/portal/ample/js/custom.min.js') }}"></script>
	<script src="{{ ('/portal/ample/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
    

    <script>
        $('.myTable').DataTable({
            "paging":   false,
            "ordering": false,
            "info":     false,
        });
        
        $('#lainnya').attr('disabled', true);

        $('.clockpicker').clockpicker({
			donetext: 'Done'
			, }).find('input').change(function () {
		});

        jQuery('.datepicker-autoclose').datepicker({
            autoclose: true
            , todayHighlight: true
            , format: 'dd/mm/yyyy'
        });

        $('#jns_hadir').on('change', function() {
			$('#lainnya').val("");
			if($('select[id="jns_hadir"] :selected').hasClass('lainnya')){
				$('#lainnya').attr('disabled', false);
			} else {
				$('#lainnya').attr('disabled', true);
			}
		});

        $('#tipe_hadir').on('change', function() {
			if (this.value == 2) {
				$('#form_aktivitas').hide();
			} else {
				$('#form_aktivitas').show();
			}
		});
    </script>

@endsection