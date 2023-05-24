@extends('layouts.masterhome')

@section('css')
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/portal/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ ('/portal/ample/plugins/bower_components/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
	<!-- Menu CSS -->
	<link href="{{ ('/portal/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
    <!-- Clock Picker css -->
    <link href="{{ ('/portal/ample/plugins/bower_components/clockpicker/dist/jquery-clockpicker.min.css') }}" rel="stylesheet">
    <!-- Date picker plugins css -->
    <link href="{{ ('/portal/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
	<!-- animation CSS -->
	<link href="{{ ('/portal/ample/css/animate.css') }}" rel="stylesheet">
	<!-- Custom CSS -->
	<link href="{{ ('/portal/ample/css/style.css') }}" rel="stylesheet">
	<!-- color CSS -->
	<link href="{{ ('/portal/ample/css/colors/purple-dark.css') }}" id="theme" rel="stylesheet">
    <!-- page CSS -->
	<link href="{{ ('/portal/ample/plugins/bower_components/custom-select/custom-select.css') }}" rel="stylesheet" type="text/css" />
    <!-- Alerts CSS -->
    <link href="{{ ('/portal/ample/plugins/bower_components/sweetalert/sweetalert.css') }}" rel="stylesheet" type="text/css">

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
						<div class="panel-heading">Agenda Kepala Badan BPAD</div>
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
                                @if ($access['zadd'] == 'y')
                                <div class="row " style="margin-bottom: 10px">
									<div class="col-md-2">
										<button class="btn btn-info" style="margin-bottom: 10px" data-toggle="modal" data-target="#modal-insert">Tambah</button>
									</div>
								</div>
                                @endif
								<div class="row">
									<div class="table-responsive">
										<table class="myTable table table-hover table-striped table-compact">
											<thead>
												<tr>
													<th>No</th>
													<th>Waktu</th>
													<th>Kegiatan</th>
													<th>No Surat</th>
													<th>Asal Surat</th>
													<th>Unit Tujuan</th>
													<th>Lokasi</th>
													<th>Ket</th>
                                                    @if($access['zupd'] == 'y' || $access['zdel'] == 'y')
													<th>Action</th>
                                                    @endif
												</tr>
											</thead>
											<tbody>
                                                @foreach($events as $key => $event)
                                                <tr>
                                                    <td class="ver-align-mid">{{ $key+1 }}</td>
                                                    <td class="ver-align-mid">
                                                        {{ $event['datetime'] ? date('d-M-Y', strtotime($event['datetime'])) : '-'  }}
                                                        <br>
                                                        {{ $event['datetime'] ? date('H:i', strtotime($event['datetime'])) : '-'  }}
                                                    </td>
                                                    <td class="ver-align-mid">{{ $event['event_name'] }}</td>
                                                    <td class="ver-align-mid">{{ $event['event_number'] }}</td>
                                                    <td class="ver-align-mid">{{ $event['event_from'] }}</td>
                                                    <td class="ver-align-mid">
                                                        @php 
                                                            $arr_unit = explode("::", $event['nm_unit']);
                                                        @endphp
                                                        @if(count($arr_unit) > 1)
                                                            <ol style="padding-left: 20px;">
                                                            @foreach($arr_unit as $unit)
                                                                <li>{{ $unit }}</li>
                                                            @endforeach
                                                            </ol>
                                                        @elseif(count($arr_unit) == 1)
                                                            {{ $arr_unit[0] }}
                                                        @endif 
                                                    </td>
                                                    <td class="ver-align-mid">{{ $event['location'] }}</td>
                                                    <td class="ver-align-mid">{{ $event['info'] }}</td>
                                                    @if($access['zupd'] == 'y' || $access['zdel'] == 'y')
                                                    <td class="ver-align-mid">
                                                        <form method="POST" action="/portal/internal/form/hapus-agenda-kaban">
                                                            @csrf
                                                            <input type="hidden" name="ids" value="{{ $event['ids'] }}">
                                                            <button type="button" class="sa-warning btn btn-danger"><i class="fa fa-trash"></i> Delete</button>
                                                        </form>
                                                    </td>
                                                    @endif
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
        <div class="modal fade" id="modal-insert">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="POST" action="{{ url('/internal/form/tambah-agenda-kaban') }}" class="form-horizontal" data-toggle="validator">
                    @csrf
                        <div class="modal-header">
                            <h4 class="modal-title"><b>Tambah Agenda Kepala Badan BPAD</b></h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="modal_insert_date" class="col-lg-2 control-label"> Tgl & Waktu <span style="color: red">*</span></label>
                                <div class="col-lg-5">
                                    <input autocomplete="off" name="date" type="text" class="form-control datepicker-autoclose" placeholder="dd/mm/yyyy" required>
                                </div>
                                <div class="col-lg-5">
                                    <div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true">
                                        <input autocomplete="off" name="time" type="text" class="form-control" placeholder="hh:mm" required> <span class="input-group-addon"> <span class="glyphicon glyphicon-time"></span> </span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="modal_insert_event_name" class="col-lg-2 control-label"> Kegiatan <span style="color: red">*</span></label>
                                <div class="col-lg-10">
                                    <input type="text" name="event_name" id="modal_insert_event_name" class="form-control" autocomplete="off" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="modal_insert_event_number" class="col-lg-2 control-label"> No Surat <span style="color: red">*</span></label>
                                <div class="col-lg-10">
                                    <input type="text" name="event_number" id="modal_insert_event_number" class="form-control" autocomplete="off" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="modal_insert_event_from" class="col-lg-2 control-label"> Asal Surat <span style="color: red">*</span></label>
                                <div class="col-lg-10">
                                    <input type="text" name="event_from" id="modal_insert_event_from" class="form-control" autocomplete="off" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="id_unit" class="col-md-2 control-label">Unit Tujuan <span style="color: red">*</span> </label>
                                <div class="col-lg-10">
                                    <select class=" select2 select2-multiple" multiple="multiple" name="id_unit[]" id="id_unit" required data-error="Pilih salah satu" autocomplete="off">
                                        @foreach($units as $unit)
                                            <option value="{{ $unit['kd_unit'] }}"> {{ $unit['kd_unit'] }} - {{ $unit['notes'] }}
                                                @if(strlen($unit['kd_unit'] > 4) && substr($unit['kd_unit'], 4, 2) == '51')
                                                    <span style="font-weight: bold;">[JAKARTA PUSAT]</span>
                                                @elseif(strlen($unit['kd_unit'] > 4) && substr($unit['kd_unit'], 4, 2) == '52')
                                                    <span style="font-weight: bold;">[JAKARTA UTARA]</span>
                                                @elseif(strlen($unit['kd_unit'] > 4) && substr($unit['kd_unit'], 4, 2) == '53')
                                                    <span style="font-weight: bold;">[JAKARTA BARAT]</span>
                                                @elseif(strlen($unit['kd_unit'] > 4) && substr($unit['kd_unit'], 4, 2) == '54')
                                                    <span style="font-weight: bold;">[JAKARTA SELATAN]</span>
                                                @elseif(strlen($unit['kd_unit'] > 4) && substr($unit['kd_unit'], 4, 2) == '55')
                                                    <span style="font-weight: bold;">[JAKARTA TIMUR]</span>
                                                @elseif(strlen($unit['kd_unit'] > 4) && substr($unit['kd_unit'], 4, 2) == '56')
                                                    <span style="font-weight: bold;">[PULAU SERIBU]</span>
                                                @elseif(strlen($unit['kd_unit'] > 2) && substr($unit['kd_unit'], 4, 2) == '06')
                                                    <span style="font-weight: bold;">[PPBD]</span>
                                                @elseif(strlen($unit['kd_unit'] > 2) && substr($unit['kd_unit'], 4, 2) == '07')
                                                    <span style="font-weight: bold;">[PUSDATIN ASET]</span>
                                                @elseif(strlen($unit['kd_unit'] > 2) && substr($unit['kd_unit'], 4, 2) == '08')
                                                    <span style="font-weight: bold;">[UPMA]</span>
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="help-block with-errors"></div>  
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="modal_insert_location" class="col-lg-2 control-label"> Lokasi <span style="color: red">*</span></label>
                                <div class="col-lg-10">
                                    <input type="text" name="location" id="modal_insert_location" class="form-control" autocomplete="off" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="modal_insert_info" class="col-lg-2 control-label"> Keterangan </label>
                                <div class="col-lg-10">
                                    <textarea class="form-control" id="modal_insert_info" name="info" autocomplete="off"></textarea>  
                                </div>
                            </div>
                            
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success pull-right">Simpan</button>
                            <button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
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
    <!-- Clock Plugin JavaScript -->
    <script src="{{ ('/portal/ample/plugins/bower_components/clockpicker/dist/jquery-clockpicker.min.js') }}"></script>
    <!-- Date Picker Plugin JavaScript -->
    <script src="{{ ('/portal/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <!-- Sweet-Alert  -->
    <script src="{{ ('/portal/ample/plugins/bower_components/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ ('/portal/ample/plugins/bower_components/sweetalert/jquery.sweet-alert.custom.js') }}"></script>
	<!-- Custom Theme JavaScript -->
	<script src="{{ ('/portal/ample/js/custom.min.js') }}"></script>
	<script src="{{ ('/portal/ample/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ ('/portal/ample/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>

	<script>
		$(function () {
            $(".select2").select2({
				allowClear: true,
			});

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

			$('.myTable').DataTable();
		});
	</script>
@endsection