@extends('layouts.masterhome')

@section('css')
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/bpadwebs/public/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ ('/bpadwebs/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
	<!-- Menu CSS -->
	<link href="{{ ('/bpadwebs/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
	<link rel="stylesheet" href="{{ ('/bpadwebs/public/ample/plugins/bower_components/html5-editor/bootstrap-wysihtml5.css') }}" />
	<!-- animation CSS -->
	<link href="{{ ('/bpadwebs/public/ample/css/animate.css') }}" rel="stylesheet">
	<!-- Custom CSS -->
	<link href="{{ ('/bpadwebs/public/ample/css/style.css') }}" rel="stylesheet">
	<!-- color CSS -->
	<link href="{{ ('/bpadwebs/public/ample/css/colors/blue-dark.css') }}" id="theme" rel="stylesheet">

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
												echo ucwords($link[4]);
											?> </h4> </div>
				<div class="col-lg-9 col-sm-8 col-md-8 col-xs-12"> 
					<ol class="breadcrumb">
						<li>{{config('app.name')}}</li>
						<?php 
							if (count($link) == 5) {
								?> 
									<li class="active"> {{ ucwords($link[4]) }} </li>
								<?php
							} elseif (count($link) > 5) {
								?> 
									<li class="active"> {{ ucwords($link[4]) }} </li>
									<li class="active"> {{ ucwords($link[5]) }} </li>
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
                        <div class="panel-heading">Konten</div>
                    	<div class="panel-wrapper collapse in">
                            <div class="panel-body">
                            	<div class="row " style="margin-bottom: 10px">
                            		<div class="col-md-1">
                            			@if ($access['zadd'] == 'y')
                            			<label for="suspnow" class="control-label">  </label>
				                      	<button class="btn btn-info btn-href-tambah" type="button" data-toggle="modal" data-target="#modal-insert">Tambah</button>
									  	@endif
                            		</div>
                            		<div class="col-md-6">
                            			<form method="GET" action="/bpadwebs/cms/content">
					                      	<div class=" col-md-3">
					                      		<label for="katnow" class="control-label"> Tipe </label>
					                        	<select class="form-control" name="katnow" id="katnow" required>
					                          	<?php foreach ($kategoris as $key => $kategori) { ?>
					                            	<option value="{{ $kategori['ids'] }}" 
					                              	<?php 
					                                	if ($katnow == $kategori['ids']) {
						                                 	echo "selected";
						                                }
					                              	?>
					                            	>{{ $kategori['nmkat'] }}</option>
					                          	<?php } ?>
					                        	</select>
				                      		</div>
				                      		<div class=" col-md-3">
				                      			<label for="suspnow" class="control-label"> Suspend </label>
					                        	<select class="form-control" name="suspnow" id="suspnow">
					                          	
					                            	<option value="N" <?php if ($suspnow == 'N') { echo "selected"; } ?> >Tidak</option>
					                            	<option value="Y" <?php if ($suspnow == 'Y') { echo "selected"; } ?> >Ya</option>
					                          	
					                        	</select>
				                      		</div>
				                      		
				                      		<button type="submit" class="btn btn-primary">Cari</button>
						                </form>
                            		</div>
                            		
                            		
                            	</div>
								<div class="row">
									<div class="table-responsive">
										<table class="myTable table table-hover">
											<thead>
												<tr>
													<th>No</th>
													<th>Suspend</th>
													<th>Tanggal</th>
													<th>Kategori</th>
													<th>Judul</th>
													<th>Editor</th>
													<th>Approved</th>
													@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
													<th class="col-md-1">Aksi</th>
													@endif
												</tr>
											</thead>
											<tbody>
												@foreach($contents as $key => $content)
												<tr>
													<td>{{ $key + 1 }}</td>
													<td>{!! ($content['sts']) == 0 ? '<i style="color:green;" class="fa fa-check"></i>' : '<i style="color:red;" class="fa fa-times"></i>' !!}</td>
													<td>
														{{ date('d/M/Y', strtotime(str_replace('/', '-', $content['tanggal']))) }}
														<br>
														<span class="text-muted">{{ date('H:i:s', strtotime($content['tanggal'])) }}</span>
													</td>
													<td>{{ $content['subkat'] }}</td>
													<td>{{ $content['judul'] }}</td>
													<td>{{ $content['editor'] }}</td>
													<td>
														{!! ($content['appr']) == 'Y' ? 
															'<i style="color:green;" class="fa fa-check"></i><br><span style="color: white;">1</span>' : 
															'<i style="color:red;" class="fa fa-times"></i><br><span style="color: white;">0</span>' !!}
														</td>
													@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
														<td>
															@if($access['zupd'] == 'y')
																<button type="button" class="btn btn-info btn-update" data-toggle="modal" data-target="#modal-update" data-ids="{{ $content['ids'] }}" data-sts = "{{ $content['sts'] }}" data-idkat = "{{ $content['idkat'] }}" data-subkat = "{{ $content['subkat'] }}" data-tanggal = "{{ $content['tanggal'] }}" data-tglinput = "{{ $content['tglinput'] }}" data-judul = "{{ $content['judul'] }}" data-isi1 = "{{ $content['isi1'] }}" data-isi2 = "{{ $content['isi2'] }}" data-editor = "{{ $content['editor'] }}" data-thits = "{{ $content['thits'] }}" data-tfile = "{{ $content['tfile'] }}" data-kd_cms = "{{ $content['kd_cms'] }}" data-appr = "{{ $content['appr'] }}" data-usrinput = "{{ $content['usrinput'] }}" data-contentnew = "{{ $content['contentnew'] }}"><i class="fa fa-edit"></i></button>
															@endif
															@if($access['zdel'] == 'y')
																<button type="button" class="btn btn-danger btn-delete" data-toggle="modal" data-target="#modal-delete" data-ids="{{ $content['ids'] }}" data-judul="{{ $content['judul'] }}" data-idkat="{{ $content['idkat'] }}"><i class="fa fa-trash"></i></button>
															@endif
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
            <div id="modal-insert" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="GET" action="/bpadwebs/cms/tambah content" class="form-horizontal" data-toggle="validator">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Pilih Kategori</b></h4>
							</div>
							<div class="modal-body">
								<div class="form-group">
                                    <label for="kat" class="col-md-2 control-label"><span style="color: red">*</span> Tipe </label>
                                    <div class="col-md-8">
                                        <select class="form-control select2" name="kat" id="kat" required>
                                            @foreach($kategoris as $kategori)
                                            	<option value="{{ $kategori['ids'] }}">{{ $kategori['nmkat'] }}</option>
                                            @endforeach
                                        </select>
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
			<div id="modal-update" class="modal fade" role="dialog">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<form class="form-horizontal" method="POST" action="/bpadwebs/cms/form/ubahcontent" data-toggle="validator">
                        @csrf   
                            <div class="modal-header">
								<h4 class="modal-title"><b>Ubah Konten</b></h4>
							</div>
							<div class="modal-body">
                            	<input type="hidden" id="modal_update_ids" name="ids" >
                                <input type="hidden" id="modal_update_idkat" name="idkat" >

                                <div class="form-group">
                                    <label for="modal_update_subkat" class="col-md-2 control-label"><span style="color: red">*</span> Subkategori </label>
                                    <div class="col-md-8">
                                        <select class="form-control" name="subkat" id="modal_update_subkat" required>
                                            @foreach($subkats as $subkat)
                                                <option value="{{ $subkat['subkat'] }}"> {{ $subkat['subkat'] }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="modal_update_tanggal" class="col-md-2 control-label"> Waktu </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="modal_update_tanggal" name="tanggal" autocomplete="off" data-error="Masukkan tanggal" value="{{ now('Asia/Jakarta') }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="modal_update_judul" class="col-md-2 control-label"><span style="color: red">*</span> Judul </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="modal_update_judul" name="judul" autocomplete="off" data-error="Masukkan judul" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <!-- <div class="form-group">
                                    <label for="modal_update_tfile" class="col-lg-2 control-label"><span style="color: red">*</span> Upload Foto <br> <span style="font-size: 10px">Hanya berupa PDF, JPG, JPEG, dan PNG</span> </label>
                                    <div class="col-lg-8">
                                        <input type="file" class="form-control" id="modal_update_tfile" name="tfile" required>
                                    </div>
                                </div> -->
                                <div id="cekidkat">
                                	<div class="form-group">
                                        <label for="modal_update_isi1" class="col-md-2 control-label"> Ringkasan </label>
                                        <div class="col-md-8">
                                            <textarea class="textarea_editor form-control" id="modal_update_isi1" rows="15" placeholder="Enter text ..." name="isi1"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="modal_update_isi2" class="col-md-2 control-label"> Isi </label>
                                        <div class="col-md-8">
                                            <textarea class="textarea_editor2 form-control" id="modal_update_isi2" rows="15" placeholder="Enter text ..." name="isi2"></textarea>
                                        </div>
                                    </div>
                                </div>
                                    
                                <div class="form-group">
                                    <label for="editor" class="col-md-2 control-label"> Original Creator </label>
                                    <div class="col-md-8">
                                        <input disabled type="text" class="form-control" id="modal_update_editor" name="editor" autocomplete="off">
                                        <input type="hidden" class="form-control" id="modal_update_editor_hid" name="editor" autocomplete="off" >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label"> Suspend? </label>
                                    <div class="radio-list col-md-8">
                                        <label class="radio-inline">
                                            <div class="radio radio-info">
                                                <input type="radio" name="sts" id="modal_update_sts1" value="0" data-error="Pilih salah satu">
                                                <label for="modal_update_sts1">Ya</label> 
                                            </div>
                                        </label>
                                        <label class="radio-inline">
                                            <div class="radio radio-info">
                                                <input type="radio" name="sts" id="modal_update_sts2" value="1">
                                                <label for="modal_update_sts2">Tidak</label>
                                            </div>
                                        </label>
                                        <div class="help-block with-errors"></div>  
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
								<button type="submit" class="btn btn-info pull-right">Simpan</button>
								<a id="modal_update_href"><button id="btn_update_href" type="button" class="btn btn-success btn-appr pull-right" style="margin-right: 10px">Setuju</button></a>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
                        </form>
                        <form>
                        	
                        </form>
					</div>
				</div>
			</div>
			<div id="modal-delete" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/bpadwebs/cms/form/hapuscontent" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Hapus Kategori</b></h4>
							</div>
							<div class="modal-body">
								<h4 id="label_delete"></h4>
								<input type="hidden" name="ids" id="modal_delete_ids" value="">
								<input type="hidden" name="idkat" id="modal_delete_idkat" value="">
								<input type="hidden" name="judul" id="modal_delete_judul" value="">
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
	<script src="{{ ('/bpadwebs/public/ample/plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
	<!-- Bootstrap Core JavaScript -->
	<script src="{{ ('/bpadwebs/public/ample/bootstrap/dist/js/bootstrap.min.js') }}"></script>
	<!-- Menu Plugin JavaScript -->
	<script src="{{ ('/bpadwebs/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js') }}"></script>
	<!--slimscroll JavaScript -->
	<script src="{{ ('/bpadwebs/public/ample/js/jquery.slimscroll.js') }}"></script>
	<!--Wave Effects -->
	<script src="{{ ('/bpadwebs/public/ample/js/waves.js') }}"></script>
	<!-- Custom Theme JavaScript -->
	<script src="{{ ('/bpadwebs/public/ample/js/custom.min.js') }}"></script>
	<script src="{{ ('/bpadwebs/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
	<script src="{{ ('/bpadwebs/public/ample/js/validator.js') }}"></script>
	<!-- wysuhtml5 Plugin JavaScript -->
    <script src="{{ ('/bpadwebs/public/ample/plugins/bower_components/html5-editor/wysihtml5-0.3.0.js') }}"></script>
    <script src="{{ ('/bpadwebs/public/ample/plugins/bower_components/html5-editor/bootstrap-wysihtml5.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.textarea_editor').wysihtml5();
            $('.textarea_editor2').wysihtml5();
        });
    </script>


	<script>
		$(function () {

			$('.btn-update').on('click', function () {
				var $el = $(this);

				if ($el.data('idkat') != 1) {
					$("#cekidkat").hide();
				} else {
					$("#cekidkat").show();
				}

				if ($el.data('sts') == 0) {
					$("#modal_update_sts1").attr('checked', true);
				} else {
					$("#modal_update_sts2").attr('checked', true);
				}

				$("#modal_update_ids").val($el.data('ids'));
				$("#modal_update_idkat").val($el.data('idkat'));
				$("#modal_update_subkat").val($el.data('subkat'));
				$("#modal_update_waktu").val($el.data('waktu'));
				$("#modal_update_editor").val($el.data('editor'));
				$("#modal_update_editor_hid").val($el.data('editor'));
				$("#modal_update_judul").val($el.data('judul'));
				$("#modal_update_isi1").data("wysihtml5").editor.setValue($el.data('isi1'));
				$("#modal_update_isi2").data("wysihtml5").editor.setValue($el.data('isi2'));

				$('.textarea_editor').contents().find('.wysihtml5-editor').html($el.data('isi1'));
				$('.textarea_editor2').contents().find('.wysihtml5-editor').html($el.data('isi2'));

				var ids = $el.data('ids');
				var idkat = $el.data('idkat');
				var appr = $el.data('appr');
				var judul = $el.data('judul');
				
				if (appr == 'Y') {
					$("#btn_update_href").html('Batal Setuju');
				} else if (appr == 'N') {
					$("#btn_update_href").html('Setuju');
				}
				$("#modal_update_href").attr("href", "/bpadwebs/cms/form/apprcontent?ids=" + ids + "&idkat=" + idkat + "&appr=" + appr + "&judul=" + judul );

			});

			$('.btn-delete').on('click', function () {
				var $el = $(this);

				$("#label_delete").append('Apakah anda yakin ingin menghapus kategori <b>' + $el.data('judul') + '</b>?');
				$("#modal_delete_ids").val($el.data('ids'));
				$("#modal_delete_judul").val($el.data('judul'));
				$("#modal_delete_idkat").val($el.data('idkat'));
			});

			$("#modal-delete").on("hidden.bs.modal", function () {
				$("#label_delete").empty();
			});

			$('.myTable').DataTable();
		});
	</script>
@endsection