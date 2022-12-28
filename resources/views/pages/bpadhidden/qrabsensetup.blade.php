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
						<div class="panel-heading">Setup QRAbsen BPAD</div>
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
													<th>Createdate</th>
													<th>Kegiatan</th>
													<th>longtext & URL</th>
													<th>start & end</th>
													<th>Laporan</th>
													<th>QR</th>
                                                    @if($access['zdel'] == 'y')
                                                    <th>Excel</th>
                                                    @endif
                                                    @if($access['zupd'] == 'y')
													<th>Set Pegawai</th>
													<th>Action</th>
                                                    @endif
												</tr>
											</thead>
											<tbody>
                                                @foreach($refs as $key => $ref)
                                                <tr>
                                                    <td class="ver-align-mid">{{ $key+1 }}</td>
                                                    <td class="ver-align-mid">{{ $ref['createdate'] }}</td>
                                                    <td class="ver-align-mid">{{ $ref['nama_kegiatan'] }}</td>
                                                    <td class="ver-align-mid">{{ $ref['longtext'] }}<br><span class="text-muted">{{ $ref['url'] }}</span></td>
                                                    <td class="ver-align-mid">
                                                        {{ $ref['start_datetime'] ? date('d-M-Y H:i', strtotime($ref['start_datetime'])) : '-'  }}
                                                        <br>
                                                        {{ $ref['end_datetime'] ? date('d-M-Y H:i', strtotime($ref['end_datetime'])) : '-' }}
                                                    </td>
                                                    <td class="ver-align-mid">
                                                        <a href="{{ url('/qrabsen/rekap?qr=') }}{{ $ref['longtext'] }}"><button type="submit" class="btn btn-primary">Rekap</button></a>
                                                        <a href="{{ url('/qrabsen/detail?qr=') }}{{ $ref['longtext'] }}"><button type="submit" class="btn btn-success">Detail</button></a>
                                                    </td>
                                                    <td class="ver-align-mid hor-align-mid"></td>
                                                    @if($access['zdel'] == 'y')
                                                    <td class="ver-align-mid hor-align-mid">
                                                        <a href="{{ url('/qrabsen/excelraw?qr=') }}{{ $ref['longtext'] }}"><button type="submit" class="btn">Excel RAW</button></a>
                                                    </td>
                                                    @endif
                                                    @if($access['zupd'] == 'y')
                                                    <td class="ver-align-mid">
                                                        <a href="{{ url('/qrabsen/setpegawai?qr=') }}{{ $ref['longtext'] }}"><button class="btn btn-warning"><i class="fa fa-key"></i> Set</button></a>
                                                    </td>
                                                    <td class="ver-align-mid hor-align-mid"></td>
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
                    <form method="POST" action="{{ url('/qrabsen/form/tambahabsen') }}" class="form-horizontal" data-toggle="validator">
                    @csrf
                        <div class="modal-header">
                            <h4 class="modal-title"><b>Tambah Kegiatan Baru</b></h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="modal_insert_nama_kegiatan" class="col-lg-2 control-label"> Kegiatan </label>
                                <div class="col-lg-8">
                                    <input type="text" name="nama_kegiatan" id="modal_insert_nama_kegiatan" class="form-control" autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="modal_insert_longtext" class="col-lg-2 control-label"> LongText </label>
                                <div class="col-lg-8">
                                    <input type="text" name="longtext" id="modal_insert_longtext" class="form-control" autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="modal_insert_salt" class="col-lg-2 control-label"> salt </label>
                                <div class="col-lg-8">
                                    <input type="text" name="salt" id="modal_insert_salt" class="form-control" autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="modal_insert_url" class="col-lg-2 control-label"> URL </label>
                                <div class="col-lg-8">
                                    <input type="text" name="url" id="modal_insert_url" class="form-control" autocomplete="off">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="modal_insert_start_datetime" class="col-lg-2 control-label"> Tgl & Waktu Mulai </label>
                                <div class="col-lg-4">
                                    <input autocomplete="off" name="start_date" type="text" class="form-control datepicker-autoclose" placeholder="dd/mm/yyyy" required>
                                </div>
                                <div class="col-lg-4">
                                    <div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true">
                                        <input autocomplete="off" name="start_time" type="text" class="form-control" placeholder="hh:mm"> <span class="input-group-addon"> <span class="glyphicon glyphicon-time"></span> </span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="modal_insert_end_datetime" class="col-lg-2 control-label"> Tgl & Waktu Akhir </label>
                                <div class="col-lg-4">
                                    <input autocomplete="off" name="end_date" type="text" class="form-control datepicker-autoclose" placeholder="dd/mm/yyyy" required>
                                </div>
                                <div class="col-lg-4">
                                    <div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true">
                                        <input autocomplete="off" name="end_time" type="text" class="form-control" placeholder="hh:mm"> <span class="input-group-addon"> <span class="glyphicon glyphicon-time"></span> </span>
                                    </div>
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
	<!-- Custom Theme JavaScript -->
	<script src="{{ ('/portal/ample/js/custom.min.js') }}"></script>
	<script src="{{ ('/portal/ample/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>

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

			$('.myTable').DataTable();
		});
	</script>
@endsection