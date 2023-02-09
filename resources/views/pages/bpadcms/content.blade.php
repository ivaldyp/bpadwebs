@extends('layouts.masterhome')

@section('css')
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/portal/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ ('/portal/ample/plugins/bower_components/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
	<!-- Menu CSS -->
	<link href="{{ ('/portal/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
	<link rel="stylesheet" href="{{ ('/portal/ample/plugins/bower_components/html5-editor/bootstrap-wysihtml5.css') }}" />
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
									<li class="active"> {{ ucwords(explode("?", $link[4])[0]) }} </li>
								<?php
							} elseif (count($link) > 5) {
								?> 
									<li class="active"> {{ ucwords(explode("?", $link[4])[0]) }} </li>
									<li class="active"> {{ ucwords(explode("?", $link[5])[0]) }} </li>
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

									<div class="col-md-12">
										<form method="GET" action="/portal/cms/content">
											<div class=" col-md-3">
												<label for="katnow" class="control-label"> Tipe </label>
												<select class="form-control" name="katnow" id="katnow" required onchange="this.form.submit()">
												<?php foreach ($kategoris as $key => $kategori) { ?>
													<option value="{{ $kategori['ids'] }}" 
													<?php 
														if ($katnow == $kategori['ids']) {
															echo "selected";
														}
													?>
													>{{ $kategori['nmkat'] }} ({{ $kategori['total'] }})</option>
												<?php } ?>
												</select>
											</div>
											<div class=" col-md-2">
												<label for="suspnow" class="control-label"> Suspend </label>
												<select class="form-control" name="suspnow" id="suspnow" onchange="this.form.submit()">
												
													<option value="N" <?php if ($suspnow == 'N') { echo "selected"; } ?> >Tidak</option>
													<option value="Y" <?php if ($suspnow == 'Y') { echo "selected"; } ?> >Ya</option>
												
												</select>
											</div>
											<div class=" col-md-2">
												<?php date_default_timezone_set('Asia/Jakarta'); ?>
												<label for="yearnow" class="control-label"> Tahun </label>
												<select class="form-control" name="yearnow" id="yearnow" onchange="this.form.submit()">
													<option <?php if ($yearnow == (int)date('Y')): ?> selected <?php endif ?> value="{{ (int)date('Y') }}">{{ (int)date('Y') }}</option>
													<option <?php if ($yearnow == (int)date('Y') - 1): ?> selected <?php endif ?> value="{{ (int)date('Y') - 1 }}">{{ (int)date('Y') - 1 }}</option>
													<option <?php if ($yearnow == (int)date('Y') - 2): ?> selected <?php endif ?> value="{{ (int)date('Y') - 2 }}">{{ (int)date('Y') - 2 }}</option>
													<option <?php if ($yearnow == (int)date('Y') - 3): ?> selected <?php endif ?> value="{{ (int)date('Y') - 3 }}">{{ (int)date('Y') - 3 }}</option>
													<option <?php if ($yearnow == (int)date('Y') - 4): ?> selected <?php endif ?> value="{{ (int)date('Y') - 4 }}">{{ (int)date('Y') - 4 }}</option>
												</select>
											</div>
											<div class=" col-md-1">
												<label for="signnow" class="control-label"> Sign </label>
												<select class="form-control" name="signnow" id="signnow" onchange="this.form.submit()">
													<option <?php if ($signnow == "="): ?> selected <?php endif ?> value="=">=</option>
													<option <?php if ($signnow == ">="): ?> selected <?php endif ?> value=">=">>=</option>
													<option <?php if ($signnow == "<="): ?> selected <?php endif ?> value="<="><=</option>
												</select>
											</div>
											<div class=" col-md-2">
												<label for="monthnow" class="control-label"> Bulan </label>
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
										</form>

									</div>
									
									
								</div>
								<div class="row" style="margin-bottom: 10px">
									@if ($access['zadd'] == 'y')
									<div class="col-sm-6" style="margin-left: 10px;">
										<button class="btn btn-info btn-href-tambah" type="button" data-toggle="modal" data-target="#modal-insert">Tambah</button>
										<button class="btn btn-danger btn-href-rekap" type="button" data-toggle="modal" data-target="#modal-rekap"><i class="fa fa-file-pdf-o"></i></button>
										<button class="btn btn-success btn-href-excel" type="button" data-toggle="modal" data-target="#modal-excel"><i class="fa fa-file-excel-o"></i></button>
										
									</div>
									@endif

									@if ($flagapprove == 1)
									<div class="col-sm-2">
										
									</div>
									@endif
								</div>
								<div class="row m-t-30">
                                    <blockquote style="color: red;">
                                        Apabila ingin menghapus konten berita / foto, harap menghubungi PIC Humas BPAD<br>
                                        Aditya - 0813-1412-3416
                                    </blockquote>
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
													<th>File</th>
													@if($katnowdetail['nama'] == 'berita' || $katnowdetail['nama'] == 'lelang' )
													<th>Headline</th>
													@endif
													<th>Approved</th>
													<th>Create Date</th>
													@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
													<th class="col-md-1">Action</th>
													@endif
												</tr>
											</thead>
											<tbody>
												@foreach($contents as $key => $content)
												<tr>
													<td>{{ $key + 1 }}</td>
													<td>{!! ($content['suspend']) == 'Y' ? '<i style="color:green;" class="fa fa-check"></i>' : '<i style="color:red;" class="fa fa-times"></i>' !!}</td>
													<td>
														{{ date('d/M/Y', strtotime(str_replace('/', '-', $content['tanggal']))) }}
														<!-- <br>
														<span class="text-muted">{{ date('H:i:s', strtotime($content['tanggal'])) }}</span> -->
													</td>
													<td>{{ $content['subkat'] }}</td>
													<td>{{ $content['judul'] }}</td>
													<td>{{ $content['editor'] }}</td>
													<td>
														@if(strtolower($content['nmkat']) == 'berita')
															<?php if (file_exists(config('app.openfileimgberita') . $content['tfile'])) { ?>
															<a target="_blank" href="{{ config('app.openfileimgberitafull') }}/{{ $content['tfile'] }}"> {{ $content['tfile'] }}</a>
															<?php } ?>
														@elseif(strtolower($content['nmkat']) == 'galeri foto')
															<?php if (file_exists(config('app.openfileimggambar') . $content['tfile'])) { ?>
															<a target="_blank" href="{{ config('app.openfileimggambarfull') }}/{{ $content['tfile'] }}"> {{ $content['tfile'] }}</a>
															<?php } ?>
														@elseif(strtolower($content['nmkat']) == 'lelang')
															<?php if (file_exists(config('app.openfileimglelang') . $content['tfile'])) { ?>
															<a target="_blank" href="{{ config('app.openfileimglelangfull') }}/{{ $content['tfile'] }}"> {{ $content['tfile'] }}</a>
															<?php } ?>
														@elseif(strtolower($content['nmkat']) == 'infografik')
															<?php if (file_exists(config('app.openfileimginfografik') . $content['tfile'])) { ?>
															<a target="_blank" href="{{ config('app.openfileimginfografikfull') }}/{{ $content['tfile'] }}"> {{ $content['tfile'] }}</a>	
															<?php } ?>
														@endif
													</td>
													@if($katnowdetail['nama'] == 'berita' || $katnowdetail['nama'] == 'lelang' )
													<td>
														@if($content['tipe'] == 'H,')
														<i style="color:green;" class="fa fa-check"></i><br><span style="color: white;">1</span>
														@else
														<i style="color:red;" class="fa fa-times"></i><br><span style="color: white;">0</span>
														@endif
													</td>
													@endif
													<td>
														{!! ($content['appr']) == 'Y' ? 
															'<i style="color:green;" class="fa fa-check"></i><br><span style="color: white;">1</span>' : 
															'<i style="color:red;" class="fa fa-times"></i><br><span style="color: white;">0</span>' !!}
													</td>
													<td>
														{{ date('d/M/Y', strtotime(str_replace('/', '-', $content['tgl']))) }}
													</td>
													@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
														<td>
															<form method="POST" action="/portal/cms/ubah content" target="_blank">
																@csrf
																@if($access['zupd'] == 'y')
																	
																	<input type="hidden" name="ids" value="{{ $content['ids'] }}">
																	<input type="hidden" name="idkat" value="{{ $content['idkat'] }}">
																	<button type="submit" class="btn btn-info btn-update"><i class="ti-pencil-alt"></i></button>
																	
																@endif
																@if($access['zdel'] == 'y' && $flagapprove == 1)
																	<button type="button" class="btn btn-danger btn-delete" data-toggle="modal" data-target="#modal-delete" data-ids="{{ $content['ids'] }}" data-judul="{{ $content['judul'] }}" data-idkat="{{ $content['idkat'] }}"><i class="fa fa-trash"></i></button>
																@endif
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
			<div id="modal-rekap" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="GET" action="/portal/cms/rekap content" class="form-horizontal" data-toggle="validator">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Cetak PDF</b></h4>
							</div>
							<div class="modal-body">
								<div class="form-group">
									<label for="kat" class="col-md-2 control-label"> Kategori </label>
									<div class="col-md-8">
										<select class="form-control select2" name="kat" id="kat_rekap" required>
											@foreach($kategoris as $kategori)
												<option <?php if ($kategori['ids'] == $katnow ): ?> selected <?php endif ?> value="{{ $kategori['ids'] }}">{{ $kategori['nmkat'] }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="rekap_bln" class="col-md-2 control-label"> Bulan </label>
									<div class="col-md-4">
										<select class="form-control" name="rekap_bln" id="rekap_bln" required>
											<option value="01::Januari">Januari</option>
											<option value="02::Februari">Februari</option>
											<option value="03::Maret">Maret</option>
											<option value="04::April">April</option>
											<option value="05::Mei">Mei</option>
											<option value="06::Juni">Juni</option>
											<option value="07::Juli">Juli</option>
											<option value="08::Agustus">Agustus</option>
											<option value="09::September">September</option>
											<option value="10::Oktober">Oktober</option>
											<option value="11::November">November</option>
											<option value="12::Desember">Desember</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="rekap_thn" class="col-md-2 control-label"> Tahun </label>
									<div class="col-md-4">
										<select class="form-control select2" name="rekap_thn" id="rekap_thn" required>
											<option value="{{ date('Y') }}">{{ date('Y') }}</option>
											<option value="{{ date('Y')-1 }}">{{ date('Y')-1 }}</option>
											<option value="{{ date('Y')-2 }}">{{ date('Y')-2 }}</option>
											<option value="{{ date('Y')-3 }}">{{ date('Y')-3 }}</option>
											<option value="{{ date('Y')-4 }}">{{ date('Y')-4 }}</option>
										</select>
									</div>
								</div>

								<input type="hidden" name="current_url" value="{{ $_SERVER['SERVER_NAME'] }}">

							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-success pull-right">Simpan</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div id="modal-excel" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="GET" action="/portal/cms/rekap excel" class="form-horizontal" data-toggle="validator">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Cetak Excel</b></h4>
							</div>
							<div class="modal-body">
								<div class="form-group">
									<label for="kat" class="col-md-2 control-label"> Kategori </label>
									<div class="col-md-8">
										<select class="form-control select2" name="kat" id="kat_excel" required>
											@foreach($kategoris as $kategori)
												<option <?php if ($kategori['ids'] == $katnow ): ?> selected <?php endif ?> value="{{ $kategori['ids'] }}">{{ $kategori['nmkat'] }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="rekap_bln" class="col-md-2 control-label"> Bulan </label>
									<div class="col-md-4">
										<select class="form-control" name="rekap_bln" id="rekap_bln_excel" required>
											<option value="01::Januari">Januari</option>
											<option value="02::Februari">Februari</option>
											<option value="03::Maret">Maret</option>
											<option value="04::April">April</option>
											<option value="05::Mei">Mei</option>
											<option value="06::Juni">Juni</option>
											<option value="07::Juli">Juli</option>
											<option value="08::Agustus">Agustus</option>
											<option value="09::September">September</option>
											<option value="10::Oktober">Oktober</option>
											<option value="11::November">November</option>
											<option value="12::Desember">Desember</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="rekap_thn" class="col-md-2 control-label"> Tahun </label>
									<div class="col-md-4">
										<select class="form-control select2" name="rekap_thn" id="rekap_thn_excel" required>
											<option value="{{ date('Y') }}">{{ date('Y') }}</option>
											<option value="{{ date('Y')-1 }}">{{ date('Y')-1 }}</option>
											<option value="{{ date('Y')-2 }}">{{ date('Y')-2 }}</option>
											<option value="{{ date('Y')-3 }}">{{ date('Y')-3 }}</option>
											<option value="{{ date('Y')-4 }}">{{ date('Y')-4 }}</option>
										</select>
									</div>
								</div>

								<input type="hidden" name="current_url" value="{{ $_SERVER['SERVER_NAME'] }}">

							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-success pull-right">Simpan</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>



			<div id="modal-insert" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="GET" action="/portal/cms/tambah content" class="form-horizontal" data-toggle="validator">
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
												<option <?php if ($kategori['ids'] == $katnow ): ?> selected <?php endif ?> value="{{ $kategori['ids'] }}">{{ $kategori['nmkat'] }}</option>
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
						<form class="form-horizontal" method="POST" action="/portal/cms/form/ubahcontent" data-toggle="validator">
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
						<form method="POST" action="/portal/cms/form/hapuscontent" class="form-horizontal">
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
	<script src="{{ ('/portal/ample/plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
	<!-- Bootstrap Core JavaScript -->
	<script src="{{ ('/portal/ample/bootstrap/dist/js/bootstrap.min.js') }}"></script>
	<!-- Menu Plugin JavaScript -->
	<script src="{{ ('/portal/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js') }}"></script>
	<!--slimscroll JavaScript -->
	<script src="{{ ('/portal/ample/js/jquery.slimscroll.js') }}"></script>
	<!--Wave Effects -->
	<script src="{{ ('/portal/ample/js/waves.js') }}"></script>
	<!-- Custom Theme JavaScript -->
	<script src="{{ ('/portal/ample/js/custom.min.js') }}"></script>
	<script src="{{ ('/portal/ample/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
	<script src="{{ ('/portal/ample/js/validator.js') }}"></script>
	<!-- wysuhtml5 Plugin JavaScript -->
	<script src="{{ ('/portal/ample/plugins/bower_components/html5-editor/wysihtml5-0.3.0.js') }}"></script>
	<script src="{{ ('/portal/ample/plugins/bower_components/html5-editor/bootstrap-wysihtml5.js') }}"></script>
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
				$("#modal_update_href").attr("href", "/portal/cms/form/apprcontent?ids=" + ids + "&idkat=" + idkat + "&appr=" + appr + "&judul=" + judul );

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

			$('.myTable').DataTable({
				"ordering": true,
			});
		});
	</script>
@endsection