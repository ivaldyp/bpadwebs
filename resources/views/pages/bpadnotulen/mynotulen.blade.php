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
	<!-- page CSS -->
	<link href="{{ ('/portal/public/ample/plugins/bower_components/custom-select/custom-select.css') }}" rel="stylesheet" type="text/css" />
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
						<div class="panel-heading">Disposisi</div>
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
								<div class="row" style="margin-bottom: 10px">
									<form method="GET" action="/portal/notulen/mynotulen">
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
										<div class=" col-md-1">
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
										<div class=" col-md-3">
											<input type="text" name="searchnow" class="form-control" placeholder="Cari" value="{{ $searchnow }}" autocomplete="off">
										</div>
										<button type="submit" class="btn btn-primary">Cari</button>
									</form>
								</div>
								<ul class="nav customtab nav-tabs" role="tablist">
									<li role="presentation" class="active"><a href="#sent" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true"><span class="visible-xs"><i class="ti-home"></i></span><span class="hidden-xs"> Terikim</span></a></li>
									<li role="presentation" class=""><a href="#draft" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs"> Draft</span></a></li>
								</ul>
								<div class="tab-content">
									<div role="tabpanel" class="tab-pane fade active in" id="sent">
										<div class="table-responsive" style="overflow: visible;">
											<table id="myTable" class="table table-hover table-striped" style="z-index: 99999;">
												<thead>
													<tr>
														<th>No</th>
														<th class="col-sm-1">Penulis</th>
														<th class="col-sm-2">Tanggal</th>
														<th class="col-sm-6">Kegiatan</th>
														@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
														<th>Action</th>
														@endif
													</tr>
												</thead>
												<tbody>
													<?php foreach ($notulensent as $key => $sent) { ?>

														<tr>
															<td>{{ $key + 1 }}</td>
															<td>{{ strtoupper($sent['nm_emp']) }}</td>
															<td>{{ date('d-M-Y', strtotime($sent['not_tanggal'])) }}<br>
																<span class="text-muted">
																	{{ date('H:i', strtotime($sent['not_mulai'])) }} - {{ date('H:i', strtotime($sent['not_selesai'])) }}
																</span>
															</td>
															<td>{{ $sent['not_acara'] }}</td>
															
															@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
																<td class="col-md-1">
																	<form method="POST" action="/portal/notulen/ubah notulen">
																		@csrf
																		@if($access['zupd'] == 'y')
																		
																			<input type="hidden" name="ids" value="{{ $sent['ids'] }}">
																			<button type="submit" class="btn btn-info btn-outline btn-circle m-r-5 btn-update"><i class="ti-pencil-alt"></i></button>
																			
																		@endif
																		@if($access['zdel'] == 'y')
																			<button type="button" class="btn btn-danger btn-outline btn-circle m-r-5 btn-delete" data-toggle="modal" data-target="#modal-delete" data-ids="{{ $sent['ids'] }}"  ><i class="fa fa-trash"></i></button>
																		@endif
																	</form>
																</td>
															@endif
														</tr>
													
														<div class="clearfix"></div>
													<?php } ?>
												</tbody>
											</table>
										</div>
									</div>
									<div role="tabpanel" class="tab-pane fade" id="draft">
										<div class="table-responsive" style="overflow: visible;">
											<table id="myTable2" class="table table-hover table-striped">
												<thead>
													<tr>
														<th>No</th>
														<th>Penulis</th>
														<th>Tanggal</th>
														<th>Kegiatan</th>
														@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
														<th>Action</th>
														@endif
													</tr>
												</thead>
												<tbody>
													<?php foreach ($notulendraft as $key => $sent) { ?>

														<tr>
															<td>{{ $key + 1 }}</td>
															<td class="col-sm-2">{{ strtoupper($sent['nm_emp']) }}</td>
															<td class="col-sm-2">{{ date('d-M-Y', strtotime($sent['not_tanggal'])) }}<br>
																<span class="text-muted">
																	{{ date('H:i', strtotime($sent['not_mulai'])) }} - {{ date('H:i', strtotime($sent['not_selesai'])) }}
																</span>
															</td>
															<td class="col-sm-6">{{ $sent['not_acara'] }}</td>
															
															@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
																<td class="col-md-1">
																	<form method="POST" action="/portal/notulen/ubah notulen">
																		@csrf
																		@if($access['zupd'] == 'y')
																		
																			<input type="hidden" name="ids" value="{{ $sent['ids'] }}">
																			<button type="submit" class="btn btn-info btn-outline btn-circle m-r-5 btn-update"><i class="ti-pencil-alt"></i></button>
																			
																		@endif
																		@if($access['zdel'] == 'y')
																			<button type="button" class="btn btn-danger btn-outline btn-circle m-r-5 btn-delete" data-toggle="modal" data-target="#modal-delete" data-ids="{{ $sent['ids'] }}"  ><i class="fa fa-trash"></i></button>
																		@endif
																	</form>
																</td>
															@endif
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
						<form method="POST" action="/portal/notulen/form/hapusnotulen" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Hapus Notulen</b></h4>
							</div>
							<div class="modal-body">
								<h4 id="label-delete"></h4>
								<input type="hidden" name="ids" value="">
								
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
	<script src="{{ ('/portal/public/ample/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>
	<script src="{{ ('/portal/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
	
	<script>
		$(function () {

			$(".select2").select2();

			$('.btn-delete').on('click', function () {
				var $el = $(this);

				$("#label_delete").append('Apakah anda yakin ingin menghapus notulen tersebut?');
				$("#modal_delete_ids").val($el.data('ids'));
			});

			$("#modal-delete").on("hidden.bs.modal", function () {
				$("#label_delete").empty();
			});

			$('.myTable').DataTable();

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