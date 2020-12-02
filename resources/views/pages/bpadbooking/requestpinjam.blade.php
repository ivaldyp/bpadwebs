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
	<!-- Date picker plugins css -->
	<link href="{{ ('/portal/public/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />

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
					<div class="panel panel-default">
						<div class="panel-heading">Permintaan Pinjaman</div>
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
								<div class="row" style="margin-bottom: 10px">
									<form method="GET" action="/portal/booking/request">
										<div class=" col-md-2">
											<?php date_default_timezone_set('Asia/Jakarta'); ?>
											<select class="form-control" name="yearnow" id="yearnow" onchange="this.form.submit()">
												<option <?php if ($yearnow == (int)date('Y')): ?> selected <?php endif ?> value="{{ (int)date('Y') }}">{{ (int)date('Y') }}</option>
												<option <?php if ($yearnow == (int)date('Y') - 1): ?> selected <?php endif ?> value="{{ (int)date('Y') - 1 }}">{{ (int)date('Y') - 1 }}</option>
												<option <?php if ($yearnow == (int)date('Y') - 2): ?> selected <?php endif ?> value="{{ (int)date('Y') - 2 }}">{{ (int)date('Y') - 2 }}</option>
												<option <?php if ($yearnow == (int)date('Y') - 3): ?> selected <?php endif ?> value="{{ (int)date('Y') - 3 }}">{{ (int)date('Y') - 3 }}</option>
												<option <?php if ($yearnow == (int)date('Y') - 4): ?> selected <?php endif ?> value="{{ (int)date('Y') - 4 }}">{{ (int)date('Y') - 4 }}</option>
											</select>
										</div>
										<div class=" col-md-2">
											<select class="form-control" name="signnow" id="signnow" onchange="this.form.submit()">
												<option <?php if ($signnow == "="): ?> selected <?php endif ?> value="=">=</option>
												<option <?php if ($signnow == ">="): ?> selected <?php endif ?> value=">=">>=</option>
												<option <?php if ($signnow == "<="): ?> selected <?php endif ?> value="<="><=</option>
											</select>
										</div>
										<div class=" col-md-2">
											<select class="form-control" name="monthnow" id="monthnow" onchange="this.form.submit()">
												@php
												$months = 1
												@endphp

												@for($i=$months; $i<=12; $i++)
													@php
														$dateObj   = DateTime::createFromFormat('!m', $i);
														$monthname = $dateObj->format('F');
													@endphp
													<option <?php if ($monthnow == $i): ?> selected <?php endif ?> value="{{ $i }}">{{ $monthname }}</option>
												@endfor
											</select>
										</div>
										<div class=" col-md-2">
											<input type="text" name="searchnow" class="form-control" placeholder="Cari" value="{{ $searchnow }}" autocomplete="off">
										</div>
										<button type="submit" class="btn btn-primary">Cari</button>
									</form>
								</div>
								<ul class="nav customtab nav-tabs" role="tablist">
									<li role="presentation" class="active"><a href="#yes" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true"><span class="visible-xs"><i class="ti-home"></i></span><span class="hidden-xs"> Disetujui</span></a></li>
									<li role="presentation" class=""><a href="#wait" aria-controls="surat" role="tab" data-toggle="tab" aria-expanded="true"><span class="visible-xs"><i class="ti-home"></i></span><span class="hidden-xs"> Menunggu Konfirmasi</span></a></li>
									<li role="presentation" class=""><a href="#no" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs"> Tidak Disetujui</span></a></li>
								</ul>
								<div class="tab-content">
									<div role="tabpanel" class="tab-pane fade active in" id="yes">
										<div class="table-responsive" style="overflow: visible;">
											<table id="myTable" class="table table-hover table-striped" style="z-index: 99999;">
												<thead>
													<tr>
														<th>No</th>
														<th>Tanggal</th>
														<th>Peminjam</th>
														<th>Tujuan</th>
														<th>Ruang</th>
														<th>File</th>
														@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
														<th>Action</th>
														@endif
													</tr>
												</thead>
												<tbody>
													<?php foreach ($bookingyes as $key => $yes) {
														$thisnm = $yes['nm_emp'];
														$thisunit = $yes['nmunit_emp'];
														$thistujuan = $yes['tujuan'];
														$thispeserta = $yes['peserta'];
														$thisruang = $yes['nm_ruang'];
														$thisidruang = $yes['ruang'];
														$thistgl = date('d-M-Y',strtotime($yes['tgl_pinjam']));
														$thismulai = $yes['jam_mulai'];
														$thisselesai = $yes['jam_selesai'];
														$thisfile = $yes['nm_file'];
														$thisstatus = $yes['status'];
														$thisalasan = $yes['alasan_tolak'];
														$thisusrinput = $yes['appr_usr'];
														$thistglinput = $yes['appr_time'];
														?>

														<tr>
															<td>{{$key+1}}</td>
															<td>{{ $thistgl }}<br><span class="text-muted">{{ date('H:i',strtotime($yes['jam_mulai'])) }} - {{ date('H:i',strtotime($yes['jam_selesai'])) }}</span></td>
															<td>{{ $thisnm }}<br><span class="text-muted">{{ $thisunit }}</span></td>
															<td>{{ $thistujuan }}</td>
															<td>{{ $thisruang }}<br><span class="text-muted">{{ $thispeserta != '' ? $thispeserta : 0 }} peserta</span></td>
															<td>
																<?php $namafolder = $thisidruang . date('H',strtotime($thismulai)) . date('dmY',strtotime($thistgl)); ?>
																<a target="_blank" href="{{ config('app.openfilebooking') }}/{{$namafolder}}/{{ $thisfile }}">{{ $thisfile }}</a>
															</td>
															
															<td style="vertical-align: middle;">
																
																<form method="POST" action="/portal/booking/lihat">
																	@csrf
																	<input type="hidden" name="ids" value="{{ $yes['ids'] }}">
																	<input type="hidden" name="hal" value="request">
																	<button type="submit" class="btn btn-info btn-outline btn-circle m-r-5"><i class="ti-eye"></i></button>
																	<button type="button" class="btn btn-danger btn-delete btn-outline btn-circle m-r-5" data-toggle="modal" data-target="#modal-delete" data-ids="{{ $yes['ids'] }}" data-hal="request"><i class="ti-trash"></i></button>
																</form>
															</td>
														</tr>
													
														<div class="clearfix"></div>
													<?php } ?>
												</tbody>
											</table>
										</div>
									</div>
									<div role="tabpanel" class="tab-pane fade" id="wait">
										<div class="table-responsive" style="overflow: visible;">
											<table id="myTable2" class="table table-hover table-striped">
												<thead>
													<tr>
														<th>No</th>
														<th>Tanggal</th>
														<th>Peminjam</th>
														<th>Tujuan</th>
														<th>Ruang</th>
														<th>File</th>
														@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
														<th>Action</th>
														@endif
														<th>Approve</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach ($bookingwait as $key => $wait) {
														$thisnm = $wait['nm_emp'];
														$thisunit = $wait['nmunit_emp'];
														$thistujuan = $wait['tujuan'];
														$thispeserta = $wait['peserta'];
														$thisruang = $wait['nm_ruang'];
														$thisidruang = $wait['ruang'];
														$thistgl = date('d-M-Y',strtotime($wait['tgl_pinjam']));
														$thismulai = $wait['jam_mulai'];
														$thisselesai = $wait['jam_selesai'];
														$thisfile = $wait['nm_file'];
														$thisstatus = $wait['status'];
														$thisalasan = $wait['alasan_tolak'];
														$thisusrinput = $wait['appr_usr'];
														$thistglinput = $wait['appr_time'];
														?>

														<tr>
															<td>{{$key+1}}</td>
															<td>{{ $thistgl }}<br><span class="text-muted">{{ date('H:i',strtotime($wait['jam_mulai'])) }} - {{ date('H:i',strtotime($wait['jam_selesai'])) }}</span></td>
															<td>{{ $thisnm }}<br><span class="text-muted">{{ $thisunit }}</span></td>
															<td>{{ $thistujuan }}</td>
															<td>{{ $thisruang }}<br><span class="text-muted">{{ $thispeserta != '' ? $thispeserta : 0 }} peserta</span></td>
															<td>
																<?php $namafolder = $thisidruang . date('H',strtotime($thismulai)) . date('dmY',strtotime($thistgl)); ?>
																<a target="_blank" href="{{ config('app.openfilebooking') }}/{{$namafolder}}/{{ $thisfile }}">{{ $thisfile }}</a>
															</td>
															
															<td style="vertical-align: middle;">
																
																<form method="POST" action="/portal/booking/ubah pinjam">
																	@csrf
																	@if ($access['zupd'] == 'y')
																	<input type="hidden" name="ids" value="{{ $wait['ids'] }}">
																	<input type="hidden" name="hal" value="request">
																	<button type="submit" class="btn btn-info btn-outline btn-circle m-r-5 btn-update"><i class="ti-pencil-alt"></i></button>
																	@endif
																	@if ($access['zdel'] == 'y' )
																	<button type="button" class="btn btn-danger btn-delete btn-outline btn-circle m-r-5" data-toggle="modal" data-target="#modal-delete" data-ids="{{ $wait['ids'] }}" data-hal="request"><i class="ti-trash"></i></button>
																	@endif
																</form>
																
															</td>
															<td style="vertical-align: middle;"> 

																<button type="button" class="btn btn-success btn-approve btn-outline btn-circle m-r-5" data-ids="{{ $wait['ids'] }}" data-hal="request"><i class="ti-check"></i></button>
															</td>
														</tr>
													
														<div class="clearfix"></div>
													<?php } ?>
												</tbody>
											</table>
										</div>
									</div>
									<div role="tabpanel" class="tab-pane fade" id="no">
										<div class="table-responsive" style="overflow: visible;">
											<table id="myTable3" class="table table-hover table-striped">
												<thead>
													<tr>
														<th>No</th>
														<th>Tanggal</th>
														<th>Peminjam</th>
														<th>Tujuan</th>
														<th>Ruang</th>
														<th>File</th>
														<th>Action</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach ($bookingno as $key => $no) {
														$thisnm = $no['nm_emp'];
														$thisunit = $no['nmunit_emp'];
														$thistujuan = $no['tujuan'];
														$thispeserta = $no['peserta'];
														$thisruang = $no['nm_ruang'];
														$thisidruang = $no['ruang'];
														$thistgl = date('d-M-Y',strtotime($no['tgl_pinjam']));
														$thismulai = $no['jam_mulai'];
														$thisselesai = $no['jam_selesai'];
														$thisfile = $no['nm_file'];
														$thisstatus = $no['status'];
														$thisalasan = $no['alasan_tolak'];
														$thisusrinput = $no['appr_usr'];
														$thistglinput = $no['appr_time'];
														?>

														<tr>
															<td>{{$key+1}}</td>
															<td>{{ $thistgl }}<br><span class="text-muted">{{ date('H:i',strtotime($no['jam_mulai'])) }} - {{ date('H:i',strtotime($no['jam_selesai'])) }}</span></td>
															<td>{{ $thisnm }}<br><span class="text-muted">{{ $thisunit }}</span></td>
															<td>{{ $thistujuan }}</td>
															<td>{{ $thisruang }}<br><span class="text-muted">{{ $thispeserta != '' ? $thispeserta : 0 }} peserta</span></td>
															<td>
																<?php $namafolder = $thisidruang . date('H',strtotime($thismulai)) . date('dmY',strtotime($thistgl)); ?>
																<a target="_blank" href="{{ config('app.openfilebooking') }}/{{$namafolder}}/{{ $thisfile }}">{{ $thisfile }}</a>
															</td>
															<td>
																<form method="POST" action="/portal/booking/lihat">
																	@csrf
																	<input type="hidden" name="ids" value="{{ $no['ids'] }}">
																	<input type="hidden" name="hal" value="request">
																	<button type="submit" class="btn btn-info btn-outline btn-circle m-r-5"><i class="ti-eye"></i></button>
																</form>
																
															</td>
															
														</tr>
													
														<div class="clearfix"></div>
													<?php } ?>
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
			<div id="modal-delete" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/portal/booking/form/hapuspinjam" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Hapus Pinjaman</b></h4>
							</div>
							<div class="modal-body">
								<h4 id="label_delete"></h4>
								<input type="hidden" name="ids" value="" id="modal_delete_ids">
								<input type="hidden" name="hal" value="" id="modal_delete_hal">
								
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-danger pull-right">Hapus</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>

			<div id="modal-approve" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/portal/booking/form/approvepinjam" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Approve Pinjaman</b></h4>
							</div>
							<div class="modal-body">
								<input type="hidden" name="ids" id="modal_approve_ids">
								<input type="hidden" name="hal" id="modal_approve_hal">
								
								<div class="form-group">
									<label class="col-md-2 control-label"> Approve? </label>
									<div class="radio-list col-md-8">
										<label class="radio-inline">
											<div class="radio radio-info">
												<input type="radio" name="status" id="status1" value="S" data-error="Pilih salah satu" required="">
												<label for="status1">Ya</label> 
											</div>
										</label>
										<label class="radio-inline">
											<div class="radio radio-info">
												<input type="radio" name="status" id="status2" value="N" checked>
												<label for="status2">Tidak</label>
											</div>
										</label>
										<div class="help-block with-errors"></div>  
									</div>
								</div>

								<div class="form-group">
									<label for="nmkat" class="col-md-2 control-label"><span style="color: red">*</span> Kategori </label>
									<div class="col-md-8">
										<input type="text" name="nmkat" id="modal_insert_nmkat" class="form-control" data-error="Masukkan nama kategori" autocomplete="off" required>
										<div class="help-block with-errors"></div>
									</div>
								</div>
								
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-danger pull-right">Hapus</button>
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
	<script src="{{ ('/portal/public/ample/js/validator.js') }}"></script>
	<script src="{{ ('/portal/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
	<!-- Date Picker Plugin JavaScript -->
	<script src="{{ ('/portal/public/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

	<script>
		$(function () {

			$('.btn-delete').on('click', function () {
				var $el = $(this);

				$("#label_delete").append('Apakah anda yakin ingin menghapus pinjaman ini?');
				$("#modal_delete_ids").val($el.data('ids'));
				$("#modal_delete_hal").val($el.data('hal'));
			});

			$("#modal-delete").on("hidden.bs.modal", function () {
				$("#label_delete").empty();
			});


			$('.btn-approve').on('click', function () {
				var $el = $(this);

				$("#modal_approve_ids").val($el.data('ids'));
				$("#modal_approve_hal").val($el.data('hal'));
			});

			// $("#modal-approve").on("hidden.bs.modal", function () {
			// 	$("#label_delete").empty();
			// });


			// $('.btn-delete-sent').on('click', function () {
			// 	var $el = $(this);
			// 	if(confirm("Menghapus disposisi yang sudah berjalan akan menghapus seluruh disposisi dengan nomor form yang sama, lanjutkan?")){
			// 		if (confirm("Apa anda yakin menghapus form dengan nomor "+$el.data('no_form')+" ?")) {
			// 			var ids = $el.data('ids');
			// 			var no_form = $el.data('no_form');

			// 			$.ajax({ 
			// 			type: "GET", 
			// 			url: "/portal/disposisi/form/hapusdisposisi",
			// 			data: { ids : ids, no_form : no_form },
			// 			dataType: "JSON",
			// 			}).done(function( data ) { 
			// 				if (data == 0) {
			// 					alert("Disposisi berhasil dihapus");
			// 					location.reload();
			// 				} else {
			// 					alert("Tidak dapat menghapus");
			// 					location.reload();
			// 				}
							
			// 			}); 
			// 		}
			// 	}
			// });

			// $('.btn-delete-draft').on('click', function () {
			// 	var $el = $(this);
			// 	if (confirm("Apa anda yakin menghapus form dengan nomor "+$el.data('no_form')+" ?")) {
			// 		var ids = $el.data('ids');
			// 		var no_form = $el.data('no_form');

			// 		$.ajax({ 
			// 		type: "GET", 
			// 		url: "/portal/disposisi/form/hapusdisposisi",
			// 		data: { ids : ids, no_form : no_form },
			// 		dataType: "JSON",
			// 		}).done(function( data ) { 
			// 			if (data == 0) {
			// 				alert("Disposisi berhasil dihapus");
			// 				location.reload();
			// 			} else {
			// 				alert("Tidak dapat menghapus");
			// 				location.reload();
			// 			}
						
			// 		}); 
			// 	}
			// });

			$('#myTable').DataTable({
				"ordering" : false,
				"searching": false,
			});

			$('#myTable2').DataTable({
				"ordering" : false,
				"searching": false,
			});

			$('#myTable3').DataTable({
				"ordering" : false,
				"searching": false,
			});
		});
	</script>
@endsection