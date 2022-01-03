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
						<div class="panel-heading">Pemanfaatan - Carousel</div>
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
								<div class="row " style="margin-bottom: 10px">
									<div class="col-md-1">
										<a href="/portal/pemanfaatan/tambah carousel"><button class="btn btn-info" style="margin-bottom: 10px">Tambah </button></a> 
									</div>
								</div>
								<div class="row">
									<div class="table-responsive">
										<table class="myTable table table-hover">
											<thead>
												<tr>
													<th>No</th>
													<th>Judul</th>
													<th>URL</th>
													<th>Image</th>
													<th>Urut</th>
													<th>Stat</th>
                                                    <th>Approve</th>
                                                    <th>Delete</th>
												</tr>
											</thead>
											<tbody>
												@foreach($imgs as $key => $img)
												<tr>
													<td>{{ $key + 1 }}</td>
													<td>{{ $img['judul'] }}</td>
													<td>{{ $img['url'] }}</td>
                                                    <td>
														<a href="{{ config('app.openfilepemanfaatancarousel') }}/{{ $img['image'] }}" target="_blank">{{ $img['image'] }}</a>
													</td>
                                                    <td>{{ $img['urut'] }}</td>
                                                    <td>
														@if($img['appr'] == '0')
															<i class="fa fa-close" style="color: red;"></i> Belum Disetujui
														@else
															<i class="fa fa-check" style="color: green;"></i> Sudah Disetujui
														@endif
													</td>
                                                    
													<td>
														<form method="POST" action="/portal/pemanfaatan/form/approvecarousel">
															@csrf
															<input type="hidden" value="{{ $img['ids'] }}" name="ids">
                                                            <input type="hidden" value="{{ $img['appr'] }}" name="appr">
															<button type="submit" class="btn btn-info btn-outline btn-circle m-r-5 btn-approve">
                                                                @if($img['appr'] == 0)
                                                                <i class="ti-check"></i>
                                                                @else
                                                                <i class="ti-close"></i>
                                                                @endif
                                                            </button>
														</form>
													</td>
													<td>
														<form method="POST" id="form-delete-{{ $img['ids'] }}" action="/portal/pemanfaatan/form/hapuscarousel">
															@csrf
															<input type="hidden" value="{{ $img['ids'] }}" name="ids">
															<button type="button" class="btn btn-danger btn-outline btn-circle m-r-5 btn-delete"><i class="ti-trash"></i></button>
														</form>
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
	<!-- Custom Theme JavaScript -->
	<script src="{{ ('/portal/public/ample/js/custom.min.js') }}"></script>
	<script src="{{ ('/portal/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>

	<script>
		$(function () {

			$('.btn-approve').on('click', function () {
				if (confirm("Apa anda yakin melakukan approval pada gambar tersebut?")) { 
					$(this).closest("form").submit();
				}
			});

			$('.btn-delete').on('click', function () {
				if (confirm("Apa anda yakin menghapus gambar tersebut?")) { 
					$(this).closest("form").submit();
				}
			});

			// $('.btn-delete').on('click', function () {
			// 	var $el = $(this);

			// 	$("#label_delete").append('Apakah anda yakin ingin menghapus surat dengan nomor form <b>' + $el.data('noform') + '</b>?');
			// 	$("#modal_delete_ids").val($el.data('ids'));
			// 	$("#modal_delete_noform").val($el.data('noform'));
			// 	$("#modal_delete_nmfile").val($el.data('nmfile'));
			// });

			// $("#modal-delete").on("hidden.bs.modal", function () {
			// 	$("#label_delete").empty();
			// });

			$('.myTable').DataTable();
		});
	</script>
@endsection