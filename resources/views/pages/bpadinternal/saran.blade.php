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
						<div class="panel-heading">Bantuan dan Saran</div>
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
								<div class="row">
									<div class="table-responsive">
										<table class="myTable table table-hover table-striped">
											<thead>
												<tr>
													<th>No</th>
													<th class="col-md-2">Tanggal</th>
													<th>Pengirim</th>
													<th>Isi</th>
													<th class="text-center">Dibalas</th>
													<!-- <th>Reply</th> -->
													@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
													<th class="col-md-1 text-center">Action</th>
													@endif
												</tr>
											</thead>
											<tbody>
												@foreach($sarans as $key => $saran)
													@if (filter_var($saran['sender'], FILTER_VALIDATE_EMAIL)) 
													<tr>
														<td>{{ $key + 1 }}</td>
														<td class="col-md-2">
															{{ date('d/m/Y', strtotime(str_replace('/', '-', $saran['tanggal']))) }}
														</td>
														<td>{{ strtolower($saran['sender']) }}</td>
														<td>{{ $saran['isi'] }}</td>
														<!-- <td>
															<button class="btn btn-circle m-r-5 btn-warning btn-reply" data-toggle="modal" data-target="#modal-reply" data-sender="{{ $saran['sender'] }}"><i class="ti-back-left"></i></button>
														</td> -->
														<td class="text-center" >
															{!! 
																($saran['read']) == 1 ? 
																'<i style="color:green;" class="fa fa-check"></i><br><span style="color: white">1</span>' : 
																'<i style="color:red;" class="fa fa-times"></i><br><span style="color: white">0</span>' 
															!!}
														</td>
														@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
															<td class="col-md-1 text-center">
																<form method="POST" action="/portal/internal/form/apprsaran">
																	@csrf
																	<input type="hidden" name="ids" value="{{ $saran['ids'] }}">
																	<input type="hidden" name="read" value="{{ $saran['read'] }}">
																	@if($saran['read'] == 1)
																	<button type="submit" class="btn btn-circle m-r-5 btn-danger"><i class="ti-close"></i></button>
																	@else
																	<button type="submit" class="btn btn-circle m-r-5 btn-success"><i class="ti-check"></i></button>
																	@endif
																</form>
															</td>
														@endif
													</tr>
													@endif
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
			<div class="modal fade" id="modal-reply">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<form method="POST" action="/portal/internal/form/reply" class="form-horizontal" data-toggle="validator">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Balasan</b></h4>
							</div>
							<div class="modal-body">

								<div class="form-group">
									<label for="modal_update_sender" class="col-md-2 control-label"> Kirim Ke </label>
									<div class="col-md-8">
										<input type="text" name="sender" id="modal_update_sender" class="form-control" autocomplete="off">
									</div>
								</div>

								<div class="form-group">
                                    <label for="body" class="col-md-2 control-label"> Body </label>
                                    <div class="col-md-8">
                                        <textarea class="summernote form-control" rows="15" placeholder="Enter text ..." name="body"></textarea>
                                    </div>
                                </div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-success pull-right">Kirim</button>
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

			$('.btn-reply').on('click', function () {
				var $el = $(this);      
				$("#modal_update_sender").val($el.data('sender'));
			});

			$('.myTable').DataTable();
		});
	</script>
@endsection