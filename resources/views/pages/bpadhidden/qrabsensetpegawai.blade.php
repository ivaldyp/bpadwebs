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
				<div class="col-md-10 col-md-offset-1">
					<!-- <div class="white-box"> -->
					<div class="panel panel-default">
                        <form class="form-horizontal" method="POST" action="{{ url('/qrabsen/form/ubahstshadir') }}">
                            @csrf
                            <div class="panel-heading">
                                Set Pegawai QRAbsen - {{ $getref['nama_kegiatan'] }}
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="row">
                                        <input class="hidden" name="longtext" value="{{ $getref['longtext'] }}">
                                    
                                        <div class="form-group">
                                            <label for="id_emp" class="col-md-2 control-label"> Pegawai </label>
                                            <div class="col-md-9">
                                                <select class="form-control select2" name="id_emp" id="id_emp" required>
                                                    <option value="<?php echo NULL; ?>"> -- PILIH PEGAWAI -- </option>
                                                    @foreach($pegawais as $emp)
                                                    <option value="{{ $emp['id_emp'] }}"> [{{ $emp['nrk_emp'] }}] - [{{ $emp['nm_emp'] }}] - [{{ $emp['nm_lok'] }}, {{ $emp['nm_unit'] }}] </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="jenis_absen" class="col-md-2 control-label"> Kehadiran </label>
                                            <div class="col-md-3">
                                                <select class="form-control" name="jenis_absen" id="jenis_absen">
                                                    @foreach($ref_absens as $ref)
                                                        <option value="{{ $ref['id_ref_absen'] }}"> {{ $ref['nm_ref_absen'] }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <select class="form-control" name="subjenis_absen" id="subjenis_absen">
                                                    @foreach($ref_sub_absens as $refsub)
                                                        <option value="{{ $refsub['nm_sub_absen'] }}"> {{ $refsub['nm_sub_absen'] }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <select class="form-control" name="subsubjenis_absen_select" id="subsubjenis_absen_select">
                                                    @php
                                                        $nowsubjenis = $ref_subsub_absens[0]['nm_sub_absen']
                                                    @endphp
                                                    <optgroup label="{{ $nowsubjenis }}">
                                                    @foreach($ref_subsub_absens as $key => $refsubsub)
                                                        @if($refsubsub['nm_sub_absen'] != $nowsubjenis)
                                                        @php $nowsubjenis = $refsubsub['nm_sub_absen']  @endphp
                                                        </optgroup>
                                                        <optgroup label="{{ $nowsubjenis }}">
                                                        @endif
                                                            <option value="{{ $refsubsub['nm_subsub_absen'] }}"> {{ $refsubsub['nm_subsub_absen'] }} </option>
                                                    @endforeach
                                                    </optgroup>
                                                </select>
                                                <input type="text" class="form-control" name="subsubjenis_absen_text" id="subsubjenis_absen_text">
                                            </div>

                                        </div>

                                        
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <button type="submit" class="btn btn-success pull-right">Simpan</button>
                                <!-- <button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Kembali</button> -->
                                <a href="{{ url('/qrabsen/setup') }}"><button type="button" class="btn btn-default pull-right m-r-10" onclick="goBack()">Kembali</button></a>
                                <div class="clearfix"></div>
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
    <script src="{{ ('/portal/ample/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>

	<script>
		$(function () {
            $(".select2").select2();
            $( "#subsubjenis_absen_text" ).hide();

            $('#jenis_absen').on('change', function() { 
                if(this.value == 1) {
                    $( "#subjenis_absen" ).prop( "disabled", true );
                    $( "#subsubjenis_absen_text" ).prop( "disabled", true );
                    $( "#subsubjenis_absen_select" ).prop( "disabled", true );
                } else if(this.value == 2) {
                    $( "#subjenis_absen" ).prop( "disabled", false );
                    $( "#subsubjenis_absen_text" ).prop( "disabled", false );
                    $( "#subsubjenis_absen_select" ).prop( "disabled", false );
                }
            });

            $('#subjenis_absen').on('change', function() { 
                console.log(this.value);
                if(this.value.toLowerCase() == 'izin') {
                    $( "#subsubjenis_absen_select" ).hide();
                    $( "#subsubjenis_absen_text" ).show();
                } else {
                    $( "#subsubjenis_absen_select" ).show();
                    $( "#subsubjenis_absen_text" ).hide();
                }
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

			$('.myTable').DataTable({
                "ordering" : false,
                "paging": false,
            });
		});
	</script>
@endsection