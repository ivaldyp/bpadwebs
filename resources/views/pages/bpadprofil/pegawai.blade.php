@extends('layouts.masterhome')

@section('css')
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/portal/public/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<!-- Menu CSS -->
	<link href="{{ ('/portal/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
	<!-- xeditable css -->
	<link href="{{ ('/portal/public/ample/plugins/bower_components/x-editable/dist/bootstrap3-editable/css/bootstrap-editable.css') }}" rel="stylesheet" />
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
				<div class="col-md-4">
					<div class="white-box">
						<div class="user-bg bg-default"> 
							<div class="overlay-box">
								<div class="user-content">
									<?php if (file_exists(config('app.savefileimg') . "\\" . $emp_data['id_emp'] . "\\profil\\" . $emp_data['foto'])) : ?>
										<img src="{{ config('app.openfileimg') }}/{{ $emp_data['id_emp'] }}/profil/{{ $emp_data['foto'] }}" style="height: 100%; width: 20%" class="thumb-lg img-circle" alt="img">

									<?php else : ?>
										<img src="{{ config('app.openfileimgdefault') }}" style="height: 100%; width: 30%" class="thumb-lg img-circle" alt="img">
									<?php endif ?> 
								</div>
							</div>
						</div>
						<div class="user-btm-box" style="text-align: center;">
							<h1>
								{{ ucwords(strtolower($emp_data['nm_emp'])) }}
							</h1>
							<h3><strong>
								{{ ucwords(strtolower($emp_jab[0]['unit']['nm_unit'])) }}
							</strong></h3>
							<a href="/portal/profil/printdrh"><button class="btn btn-warning btn-rounded">Cetak Riwayat</button></a>
						</div>
						<form method="POST" action="/portal/profil/form/ubahidpegawai" data-toggle="validator" enctype="multipart/form-data">
						@csrf
						<div class="user-btm-box" style="text-align: center;">
							<div class="col-md-6 text-center row-in-br">
								<p class="text-blue"><i style="font-size: 30px;" class="mdi mdi-phone"></i></p>
								<h5 class="data-show">{{ $emp_data['tlp_emp'] }}</h5> 
								<input class="form-control data-input" type="text" name="tlp_emp" value="{{ $emp_data['tlp_emp'] }}" placeholder="email" autocomplete="off">
							</div>
							<div class="col-md-6 text-center">
								<p class="text-blue"><i style="font-size: 30px;" class="mdi mdi-email-outline"></i></p>
								<h5 class="data-show">{{ $emp_data['email_emp'] }}</h5> 
								<input class="form-control data-input" type="text" name="email_emp" value="{{ $emp_data['email_emp'] }}" placeholder="email" autocomplete="off">
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-8">
					<div class="white-box">
						<ul class="nav customtab nav-tabs" role="tablist">
							<li role="presentation" class="active"><a href="#tabs1" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true"><span class="visible-xs">Id</span><span class="hidden-xs"> Identitas </span></a></li>
							<li role="presentation" class=""><a href="#tabs5" aria-controls="settings" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs">Kel</span> <span class="hidden-xs">Keluarga</span></a></li>
							<li role="presentation" class=""><a href="#tabs2" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs">Dik</span> <span class="hidden-xs"> Pendidikan </span></a></li>
							<li role="presentation" class=""><a href="#tabs3" aria-controls="messages" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs">Gol</i></span> <span class="hidden-xs">Golongan</span></a></li>
							<li role="presentation" class=""><a href="#tabs4" aria-controls="settings" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs">Jab</span> <span class="hidden-xs">Jabatan</span></a></li>
							<li role="presentation" class=""><a href="#tabs6" aria-controls="settings" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs">HukDis</span> <span class="hidden-xs">Hukuman Disiplin</span></a></li>
						</ul>
						<!-- Tab panes -->
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane fade active in" id="tabs1">
									<div class="panel-group" id="exampleAccordionDefault" aria-multiselectable="true" role="tablist">
										<div class="panel">
											<div class="panel-heading" style="background-color: #edf1f5" id="exampleHeadingDefaultOne" role="tab"> <a class="panel-title collapsed" data-toggle="collapse" href="#exampleCollapseDefaultOne" data-parent="#exampleAccordionDefault" aria-expanded="true" aria-controls="exampleCollapseDefaultOne"> Nomor ID </a> </div>
											<div class="panel-collapse collapse in" id="exampleCollapseDefaultOne" aria-labelledby="exampleHeadingDefaultOne" role="tabpanel">
												<div class="table-responsive">
													<table class="table table-hover">
														<tr>
															<td class="col-md-6 p-l-30"><h4>ID</h4></td>
															<td class="col-md-6" style="vertical-align: middle;">
															<h4 class="text-muted">{{ $emp_data['id_emp'] }}</h4></td>
															<input class="form-control" type="hidden" name="id_emp" value="{{ $emp_data['id_emp'] }}">
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>NIP</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">{{ $emp_data['nip_emp'] }}</h4></td>
															<td class="col-md-6 data-input">
																<!-- <input class="form-control uintTextBox" type="text" name="nip_emp" value="{{ $emp_data['nip_emp'] }}" placeholder="NIP" autocomplete="off"> -->
																<input class="form-control" type="text" name="nip_emp" value="{{ $emp_data['nip_emp'] }}" placeholder="NIP" autocomplete="off">
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>NRK</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">{{ $emp_data['nrk_emp'] }}</h4></td>
															<td class="col-md-6 data-input">
																<!-- <input class="form-control uintTextBox" type="text" name="nrk_emp" value="{{ $emp_data['nrk_emp'] }}" placeholder="NRK" autocomplete="off"> -->
																<input class="form-control" type="text" name="nrk_emp" value="{{ $emp_data['nrk_emp'] }}" placeholder="NRK" autocomplete="off">
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>TMT</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">{{ date('d-M-Y',strtotime($emp_data['tgl_join'])) }}</h4></td>
															<td class="col-md-6 data-input">
																<input id="datepicker-autoclose2" class="form-control" type="text" name="tgl_join" value="{{ date('d/m/Y', strtotime($emp_data['tgl_join'])) }}" placeholder="Tanggal Lahir" autocomplete="off">
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>Status</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">{{ $emp_data['status_emp'] }}</h4></td>
															<td class="col-md-6 data-input">
																<select class="form-control" name="status_emp" id="status_emp">
																	@foreach($statuses as $status)
																		<option value="{{ $status['status_emp'] }}"  
																			<?php if ($emp_data['status_emp'] == $status['status_emp']): ?>
																				selected
																			<?php endif ?>
																		> {{ $status['status_emp'] }} </option>
																	@endforeach
																</select>
															</td>
														</tr>
													</table>
												</div>
											</div>
										</div>
										<div class="panel">
											<div class="panel-heading" style="background-color: #edf1f5" id="exampleHeadingDefaultTwo" role="tab"> <a class="panel-title collapsed" data-toggle="collapse" href="#exampleCollapseDefaultTwo" data-parent="#exampleAccordionDefault" aria-expanded="false" aria-controls="exampleCollapseDefaultTwo"> Data Diri </a> </div>
											<div class="panel-collapse collapse" id="exampleCollapseDefaultTwo" aria-labelledby="exampleHeadingDefaultTwo" role="tabpanel">
												<div class="table-responsive">
													<table class="table table-hover">
														<tr>
															<td class="col-md-6 p-l-30"><h4>Nama</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">
																<?php if ($emp_data['gelar_dpn']) : ?>
																	{{ $emp_data['gelar_dpn'] }}
																<?php endif ?>
																<!-- <span class="inline_edit_id" id="inline-nm_emp" data-type="text" data-id="{{ $emp_data['id_emp'] }}" data-title="Enter username">{{ ucwords(strtolower($emp_data['nm_emp'])) }}</span> -->
																{{ ucwords(strtolower($emp_data['nm_emp'])) }}

																<?php if ($emp_data['gelar_blk']) : ?>
																	<!-- <span class="inline_edit_id" id="inline-gelar_blk" data-type="text" data-id="{{ $emp_data['id_emp'] }}" data-title="Enter username">{{ $emp_data['gelar_blk'] }}</span> -->
																	{{ $emp_data['gelar_blk'] }}
																<?php endif ?>
															</h4></td>
															<td class="col-md-6 data-input">
																<div class="col-md-3">
																	<input class="form-control" type="text" name="gelar_dpn" value="{{ $emp_data['gelar_dpn'] }}" placeholder="Depan" autocomplete="off">
																</div>
																<div class="col-md-6">
																	<input class="form-control" type="text" name="nm_emp" value="{{ $emp_data['nm_emp'] }}" placeholder="Nama" autocomplete="off">
																</div>
																<div class="col-md-3">
																	<input class="form-control" type="text" name="gelar_blk" value="{{ $emp_data['gelar_blk'] }}" placeholder="Belakang" autocomplete="off">
																</div>
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>NIK KTP</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">{{ $emp_data['nik_emp'] }}</h4></td>
															<td class="col-md-6 data-input">
																<input class="form-control uintTextBox" type="text" name="nik_emp" value="{{ $emp_data['nik_emp'] }}" placeholder="NIK" autocomplete="off">
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>Jenis Kelamin</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">
																<?php if ($emp_data['jnkel_emp'] == 'L') : ?>
																	Laki-Laki
																<?php else : ?>
																	Perempuan
																<?php endif ?>
															</h4></td>
															<td class="col-md-6 data-input">
																<div class="radio-list col-md-8">
																	<label class="radio-inline">
																		<div class="radio radio-info">
																			<input type="radio" name="jnkel_emp" id="kel1" value="L" data-error="Pilih salah satu" required checked>
																			<label for="kel1">Laki-laki</label> 
																		</div>
																	</label>
																	<label class="radio-inline">
																		<div class="radio radio-info">
																			<input type="radio" name="jnkel_emp" id="kel2" value="P" 
																				<?php if ($emp_data['jnkel_emp'] == "P"): ?>
																					checked
																				<?php endif ?>
																			>
																			<label for="kel2">Perempuan</label>
																		</div>
																	</label>
																	<div class="help-block with-errors"></div>  
																</div>
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>Tempat, Tgl Lahir</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">{{ $emp_data['tempat_lahir'] }}, {{ date('d-M-Y',strtotime($emp_data['tgl_lahir'])) }}</h4></td>
															<td class="col-md-6 data-input">
																<div class="col-md-6">
																	<input class="form-control" type="text" name="tempat_lahir" value="{{ $emp_data['tempat_lahir'] }}" placeholder="Tempat" autocomplete="off">
																</div>
																<div class="col-md-6">
																	<input id="datepicker-autoclose" class="form-control" type="text" name="tgl_lahir" value="{{ date('d/m/Y', strtotime($emp_data['tgl_lahir'])) }}" placeholder="Tanggal Lahir" autocomplete="off">
																</div>
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>Agama</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">
																<?php if ($emp_data['idagama'] == 'A') : ?>
																	Islam
																<?php elseif ($emp_data['idagama'] == 'B') : ?>
																	Katolik
																<?php elseif ($emp_data['idagama'] == 'C') : ?>
																	Protestan
																<?php elseif ($emp_data['idagama'] == 'D') : ?>
																	Budha
																<?php elseif ($emp_data['idagama'] == 'E') : ?>
																	Hindu
																<?php elseif ($emp_data['idagama'] == 'F') : ?>
																	Lainnya
																<?php elseif ($emp_data['idagama'] == 'G') : ?>
																	Konghucu
																<?php endif ?>
															</h4></td>
															<td class="col-md-6 data-input">
																<select class="form-control" name="idagama" id="idagama">
																	<option value="A" <?php if ($emp_data['idagama'] == "A"): ?> selected <?php endif ?> >Islam </option>
																	<option value="B" <?php if ($emp_data['idagama'] == "B"): ?> selected <?php endif ?> > Katolik </option>
																	<option value="C" <?php if ($emp_data['idagama'] == "C"): ?> selected <?php endif ?> > Protestan </option>
																	<option value="D" <?php if ($emp_data['idagama'] == "D"): ?> selected <?php endif ?> > Budha </option>
																	<option value="E" <?php if ($emp_data['idagama'] == "E"): ?> selected <?php endif ?> > Hindu </option>
																	<option value="F" <?php if ($emp_data['idagama'] == "F"): ?> selected <?php endif ?> > Lainnya </option>
																	<option value="G" <?php if ($emp_data['idagama'] == "G"): ?> selected <?php endif ?> > Konghucu </option>
																</select>
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>Alamat</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">{{ $emp_data['alamat_emp'] }}</h4></td>
															<td class="col-md-6 data-input">
																<textarea class="form-control" name="alamat_emp" placeholder="Alamat" autocomplete="off">{{ $emp_data['alamat_emp'] }}</textarea>
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>Status Perkawinan</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">{{ $emp_data['status_nikah'] }}</h4></td>
															<td class="col-md-6 data-input">
																<select class="form-control" name="status_nikah" id="status_nikah">
																	<option value="Belum Kawin" <?php if ($emp_data['status_nikah'] == "Belum Kawin"): ?> selected <?php endif ?> > Belum Kawin </option>
																	<option value="Kawin" <?php if ($emp_data['status_nikah'] == "Kawin"): ?> selected <?php endif ?> > Kawin </option>
																	<option value="Cerai Hidup" <?php if ($emp_data['status_nikah'] == "Cerai Hidup"): ?> selected <?php endif ?> > Cerai Hidup </option>
																	<option value="Cerai Mati" <?php if ($emp_data['status_nikah'] == "Cerai Mati"): ?> selected <?php endif ?> > Cerai Mati </option>
																</select>
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>Golongan Darah</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">{{ $emp_data['gol_darah'] }}</h4></td>
															<td class="col-md-6 data-input">
																<select class="form-control" name="gol_darah" id="gol_darah">
																	<option value="A" <?php if ($emp_data['gol_darah'] == "A"): ?> selected <?php endif ?> > A </option>
																	<option value="B" <?php if ($emp_data['gol_darah'] == "B"): ?> selected <?php endif ?> > B </option>
																	<option value="AB" <?php if ($emp_data['gol_darah'] == "AB"): ?> selected <?php endif ?> > AB </option>
																	<option value="O" <?php if ($emp_data['gol_darah'] == "O"): ?> selected <?php endif ?> > O </option>
																</select>
															</td>
														</tr>
													</table>
												</div>
											</div>
										</div>
										<div class="panel">
											<div class="panel-heading" style="background-color: #edf1f5" id="exampleHeadingDefaultThree" role="tab"> <a class="panel-title collapsed" data-toggle="collapse" href="#exampleCollapseDefaultThree" data-parent="#exampleAccordionDefault" aria-expanded="false" aria-controls="exampleCollapseDefaultThree"> Nomor Penting </a> </div>
											<div class="panel-collapse collapse" id="exampleCollapseDefaultThree" aria-labelledby="exampleHeadingDefaultThree" role="tabpanel">
												<div class="table-responsive">
													<table class="table table-hover">
														<tr>
															<td class="col-md-6 p-l-30"><h4>Bank</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">
																<?php if (($emp_data['nm_bank'] && $emp_data['nm_bank'] != '') || ($emp_data['cb_bank'] && $emp_data['cb_bank'] != '')) : ?>
																	{{ $emp_data['nm_bank'] }} {{ $emp_data['cb_bank'] }}
																<?php else : ?>
																	-
																<?php endif ?>
															</h4></td>
															<td class="col-md-6 data-input">
																<div class="col-md-6">
																	<input class="form-control" type="text" name="nm_bank" value="{{ $emp_data['nm_bank'] }}" placeholder="Nama Bank" autocomplete="off">
																</div>
																<div class="col-md-6">
																	<input class="form-control" type="text" name="cb_bank" value="{{ $emp_data['cb_bank'] }}" placeholder="Cabang Bank" autocomplete="off">
																</div>
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>Nama Rekening</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">
																<?php if ($emp_data['an_bank'] && $emp_data['an_bank'] != '') : ?>
																	{{ $emp_data['an_bank'] }}
																<?php else : ?>
																	-
																<?php endif ?>
															</h4></td>
															<td class="col-md-6 data-input">
																<input class="form-control" type="text" name="an_bank" value="{{ $emp_data['an_bank'] }}" placeholder="Nama Rekening" autocomplete="off">
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>Nomor Rekening</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">
																<?php if ($emp_data['nr_bank'] && $emp_data['nr_bank'] != '') : ?>
																	{{ $emp_data['nr_bank'] }}
																<?php else : ?>
																	-
																<?php endif ?>
															</h4></td>
															<td class="col-md-6 data-input">
																<input class="form-control" type="text" name="nr_bank" value="{{ $emp_data['nr_bank'] }}" placeholder="Nomor Rekening" autocomplete="off">
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>Nomor Taspen</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">
																<?php if ($emp_data['no_taspen'] && $emp_data['no_taspen'] != '') : ?>
																	{{ $emp_data['no_taspen'] }}
																<?php else : ?>
																	-
																<?php endif ?>
															</h4></td>
															<td class="col-md-6 data-input">
																<input class="form-control" type="text" name="no_taspen" value="{{ $emp_data['no_taspen'] }}" placeholder="Nomor Taspen" autocomplete="off">
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>NPWP</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">
																<?php if ($emp_data['npwp'] && $emp_data['npwp'] != '') : ?>
																	{{ $emp_data['npwp'] }}
																<?php else : ?>
																	-
																<?php endif ?>
															</h4></td>
															<td class="col-md-6 data-input">
																<input class="form-control" type="text" name="npwp" value="{{ $emp_data['npwp'] }}" placeholder="NPWP" autocomplete="off">
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>Nomor Askes</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">
																<?php if ($emp_data['no_askes'] && $emp_data['no_askes'] != '') : ?>
																	{{ $emp_data['no_askes'] }}
																<?php else : ?>
																	-
																<?php endif ?>
															</h4></td>
															<td class="col-md-6 data-input">
																<input class="form-control" type="text" name="no_askes" value="{{ $emp_data['no_askes'] }}" placeholder="Nomor Askes" autocomplete="off">
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>BPJS</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">
																<?php if ($emp_data['no_jamsos'] && $emp_data['no_jamsos'] != '') : ?>
																	{{ $emp_data['no_jamsos'] }}
																<?php else : ?>
																	-
																<?php endif ?>
															</h4></td>
															<td class="col-md-6 data-input">
																<input class="form-control" type="text" name="no_jamsos" value="{{ $emp_data['no_jamsos'] }}" placeholder="Nomor Jamsostek" autocomplete="off">
															</td>
														</tr>
													</table>
												</div>
											</div>
										</div>
									</div>
									<div class="data-input">
										<h4>Ubah Foto 
											<br><span class="text-danger" style="font-size: 14px">Hanya berupa JPG, JPEG, dan PNG</span>
											<br><span class="text-danger" style="font-size: 14px">Ukuran foto 3x4</span>
											<br><span class="text-danger" style="font-size: 14px">Size max 2MB</span>
										</h4>
										<input type="file" name="filefoto">
									</div>
									<button class="btn btn-success pull-right data-input" type="submit">Simpan</button>
									<button class="btn btn-info pull-right btn-edit-id m-r-10" type="button">Ubah</button>
									<div class="clearfix"></div>
								</form>
							</div>
							<div role="tabpanel" class="tab-pane fade" id="tabs5">
								<button class="btn btn-info m-b-20 btn-insert-kel" type="button" data-toggle="modal" data-target="#modal-insert-kel">Tambah</button>

								@if(count($emp_kel) > 0)
								<div class="table-responsive">
									<table class="table table-hover manage-u-table">
										<tbody>
											@foreach($emp_kel as $key => $kel)
												<tr>
													<td style="vertical-align: middle;">
														<strong>{{ strtoupper($kel['jns_kel']) }}</strong>
														<br>{{ ucwords(strtolower($kel['nm_kel'])) }}
													</td>

													<td style="vertical-align: middle;">
														<strong>NIK</strong><br>
														<?php if ($kel['nik_kel']) : ?> 
															{{ $kel['nik_kel'] }}
														<?php else : ?>
															-
														<?php endif ?>
													</td>

													<td style="vertical-align: middle;">
														<strong>Tgl Lahir</strong><br>
														<?php if (date('d-M-Y',strtotime($kel['tgl_kel']))) : ?> 
															{{ date('d-M-Y',strtotime($kel['tgl_kel'])) }}
														<?php else : ?>
															-
														<?php endif ?>
													</td>

													<td style="vertical-align: middle;">
														
														<button type="button" class="btn btn-info btn-outline btn-circle m-r-5 btn-update-kel" data-toggle="modal" data-target="#modal-update-kel" 
															data-ids="{{$kel['ids']}}"
															data-noid="{{$kel['noid']}}"
															data-jns_kel="{{$kel['jns_kel']}}"
															data-nm_kel="{{$kel['nm_kel']}}"
															data-nik_kel="{{$kel['nik_kel']}}"
															data-tgl_kel="{{ date('d/m/Y',strtotime($kel['tgl_kel'])) }}"
														><i class="ti-pencil-alt"></i></button>
														<button type="button" class="btn btn-danger btn-delete-kel btn-outline btn-circle m-r-5" data-toggle="modal" data-target="#modal-delete-kel"
															data-ids="{{$kel['ids']}}"
															data-noid="{{$kel['noid']}}"
															data-jns_kel="{{$kel['jns_kel']}}"
														><i class="ti-trash"></i></button>
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
								@endif
								<div class="clearfix"></div>
							</div>
							<div role="tabpanel" class="tab-pane fade" id="tabs2">
								<div class="white-box">
									<h2><b>PENDIDIKAN FORMAL</b></h2>
									
									<button class="btn btn-info m-b-20 btn-insert-dik" type="button" data-toggle="modal" data-target="#modal-insert-dik">Tambah</button>

									@if(count($emp_dik) > 0)
									<div class="table-responsive">
										<table class="table table-hover manage-u-table">
											<tbody>
												@foreach($emp_dik as $key => $dik)
													@if ($dik['iddik'] != 'NA')
													<tr>
														<td>
															<h1>{{ $dik['iddik'] }}</h1>
														</td>

														<td style="vertical-align: middle;">
															<strong>{{ $dik['prog_sek'] }} {{ $dik['th_sek'] }}</strong>
															<br>{{ $dik['nm_sek'] }}
														</td>

														<td style="vertical-align: middle;">
															<?php if ($dik['no_sek']) : ?>
																<strong>No. {{ $dik['no_sek'] }}</strong>
															<?php endif ?>
															
															<?php if ($dik['gambar'] && $dik['gambar'] != '') : ?> 
																<br><a target="_blank" href="{{ config('app.openfileimg') }}/{{ Auth::user()->id_emp }}/dik/{{ $dik['gambar'] }}">[File Ijazah]</a>
															<?php else : ?>
																<br>[Tidak ada ijazah]
															<?php endif ?>
														</td>

														<td style="vertical-align: middle;">
															<button type="button" class="btn btn-info btn-outline btn-circle m-r-5 btn-update-dik" data-toggle="modal" data-target="#modal-update-dik" 
																data-ids="{{$dik['ids']}}"
																data-noid="{{$dik['noid']}}"
																data-iddik="{{$dik['iddik']}}"
																data-prog_sek="{{$dik['prog_sek']}}"
																data-no_sek="{{$dik['no_sek']}}"
																data-th_sek="{{$dik['th_sek']}}"
																data-nm_sek="{{$dik['nm_sek']}}"
																data-gelar_dpn_sek="{{$dik['gelar_dpn_sek']}}"
																data-gelar_blk_sek="{{$dik['gelar_blk_sek']}}"
																data-ijz_cpns="{{$dik['ijz_cpns']}}"
															><i class="ti-pencil-alt"></i></button>
															<button type="button" class="btn btn-danger btn-delete-dik btn-outline btn-circle m-r-5" data-toggle="modal" data-target="#modal-delete-dik"
																data-ids="{{$dik['ids']}}"
																data-noid="{{$dik['noid']}}"
																data-iddik="{{$dik['iddik']}}"
															><i class="ti-trash"></i></button>
														</td>
													</tr>
													@endif
												@endforeach
											</tbody>
										</table>
									</div>
									@endif
									
									<div class="clearfix"></div>
								</div>

								<div class="white-box">
									<h2><b>PENDIDIKAN NON-FORMAL</b></h2>
									<button class="btn btn-info m-b-20 btn-insert-non" type="button" data-toggle="modal" data-target="#modal-insert-non">Tambah</button>

									@if(count($emp_non) > 0)
									<div class="table-responsive">
										<table class="table table-hover manage-u-table">
											<tbody>
												@foreach($emp_non as $key => $non)
												<tr>
													@if (count($emp_non) > 1)
													<td>
														<h1>{{ $key + 1 }}</h1>
													</td>
													@endif
													<td style="vertical-align: middle;">
														<strong>Nama</strong>
														<br>{{ $non['nm_non'] ?? '-' }}
													</td>

													<td style="vertical-align: middle;">
														<strong>Penyelenggara</strong>
														<br>{{ $non['penye_non'] ?? '-' }}
													</td>

													<td style="vertical-align: middle;">
														<strong>No. {{ $non['sert_non'] }}</strong>
														<br>Th. {{ $non['thn_non'] ?? '-' }}
													</td>

													<td style="vertical-align: middle;">
														<strong>File</strong>
														<?php if ($non['gambar'] && $non['gambar'] != '') : ?> 
															<br><a target="_blank" href="{{ config('app.openfileimg') }}/{{ Auth::user()->id_emp }}/non/{{ $non['gambar'] }}">[File]</a>
														<?php else : ?>
															<br>[Tidak ada file]
														<?php endif ?>
													</td>

													<td style="vertical-align: middle;">
														<button type="button" class="btn btn-info btn-outline btn-circle m-r-5 btn-update-non" data-toggle="modal" data-target="#modal-update-non" 
															data-ids="{{$non['ids']}}"
															data-noid="{{$non['noid']}}"
															data-nm_non="{{$non['nm_non']}}"
															data-penye_non="{{$non['penye_non']}}"
															data-thn_non="{{$non['thn_non']}}"
															data-durasi_non="{{$non['durasi_non']}}"
															data-sert_non="{{$non['sert_non']}}"
															data-tgl_non="{{date('d/m/Y',strtotime($non['tgl_non']))}}"
														><i class="ti-pencil-alt"></i></button>
														<button type="button" class="btn btn-danger btn-delete-non btn-outline btn-circle m-r-5" data-toggle="modal" data-target="#modal-delete-non"
															data-ids="{{$non['ids']}}"
															data-noid="{{$non['noid']}}"
															data-nm_non="{{$non['nm_non']}}"
														><i class="ti-trash"></i></button>
													</td>
												</tr>
												@endforeach
											</tbody>
										</table>
									</div>
									@endif
									
									<div class="clearfix"></div>
								</div>
										
							</div>
							<div role="tabpanel" class="tab-pane fade" id="tabs3">
								<button class="btn btn-info m-b-20 btn-insert-gol" type="button" data-toggle="modal" data-target="#modal-insert-gol">Tambah</button>

								@if(count($emp_gol) > 0)
								<div class="table-responsive">
									<table class="table table-hover manage-u-table">
										<tbody>
											@foreach($emp_gol as $key => $gol)
												<tr>
													@if (count($emp_gol) > 1)
													<td>
														<h1>{{ $key + 1 }}</h1>
													</td>
													@endif
													<td style="vertical-align: middle;">
														<strong>{{ $gol['idgol'] }}</strong>
														<br>{{ $gol['gol']['nm_pangkat'] }}
													</td>

													<?php if ($gol['tmt_gol']) : ?>
														<td style="vertical-align: middle;">
															<strong>TMT </strong>
															<br>{{ date('d-M-Y',strtotime($gol['tmt_gol'])) }}
														</td>
													<?php endif ?>

													<td style="vertical-align: middle;">
														<strong>Nomor SK</strong><br>
														<?php if ($gol['no_sk_gol']) : ?> 
															{{ $gol['no_sk_gol'] }}
														<?php else : ?>
															-
														<?php endif ?>
													</td>
													
													<td style="vertical-align: middle;">
													<?php if ($gol['gambar'] && $gol['gambar'] != '') : ?> 
														<strong>File</strong>
														<br><a target="_blank" href="{{ config('app.openfileimg') }}/{{ Auth::user()->id_emp }}/gol/{{ $gol['gambar'] }}">[File SK]</a>
													<?php else : ?>
														<strong>File</strong>
														<br>[Tidak ada SK Gol]
													<?php endif ?>
													<?php if ($gol['appr'] == '1') : ?> 
														<i class="fa fa-check" style="color: #2ECC40;" data-toggle="tooltip" title="Sudah Di Approve"></i>
													<?php else : ?>
														<i class="fa fa-close" style="color: #FF4136;" data-toggle="tooltip" title="Belum di approve, {{ $gol != '' && !(is_null($gol)) ?  $gol['alasan'] : '' }}"></i>
													<?php endif ?>
													</td>

													<td style="vertical-align: middle;">
														
														<button type="button" class="btn btn-info btn-outline btn-circle m-r-5 btn-update-gol" data-toggle="modal" data-target="#modal-update-gol" 
															data-ids="{{$gol['ids']}}"
															data-noid="{{$gol['noid']}}"
															data-tmt_gol="{{ date('d/m/Y',strtotime($gol['tmt_gol'])) }}"
															data-tmt_sk_gol="{{ date('d/m/Y',strtotime($gol['tmt_sk_gol'])) }}"
															data-no_sk_gol="{{$gol['no_sk_gol']}}"
															data-idgol="{{$gol['idgol']}}"
															data-nm_sek="{{$gol['nm_sek']}}"
															data-jns_kp="{{$gol['jns_kp']}}"
															data-mk_thn="{{$gol['mk_thn']}}"
															data-mk_bln="{{$gol['mk_bln']}}"
														><i class="ti-pencil-alt"></i></button>
														<button type="button" class="btn btn-danger btn-delete-gol btn-outline btn-circle m-r-5" data-toggle="modal" data-target="#modal-delete-gol"
															data-ids="{{$gol['ids']}}"
															data-noid="{{$gol['noid']}}"
															data-idgol="{{$gol['idgol']}}"
														><i class="ti-trash"></i></button>
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
								@endif
								<div class="clearfix"></div>
							</div>
							<div role="tabpanel" class="tab-pane fade" id="tabs4">
								<button class="btn btn-info m-b-20 btn-insert-jab" type="button" data-toggle="modal" data-target="#modal-insert-jab">Tambah</button>

								@if(count($emp_jab) > 0)
								<div class="table-responsive">
									<table class="table table-hover manage-u-table">
										<tbody>
											@foreach($emp_jab as $key => $jab)
												<tr>
													@if (count($emp_jab) > 1)
													<td>
														<h1>{{ $key + 1 }}</h1>
													</td>
													@endif
													<td style="vertical-align: middle;">
														<strong>{!! wordwrap(ucwords(strtolower($jab['unit']['nm_unit'])), 30, "<br>\n", TRUE ) !!}</strong>
														<br>{!! wordwrap($jab['idjab'], 50, "<br>\n", TRUE) !!}
													</td>

													<td style="vertical-align: middle;">
														<strong>Lokasi</strong>
														<br>{{ $jab['lokasi']['nm_lok'] }}
													</td>

													<!-- 00000000000000 -->

													<td style="vertical-align: middle;">
														<strong>TMT</strong><br>
														<?php if ($jab['tmt_jab']) : ?>
															{{ date('d-M-Y',strtotime($jab['tmt_jab'])) }}
														<?php else : ?>
															-
														<?php endif ?>
													</td>

													<td style="vertical-align: middle;">
													<?php if ($jab['gambar'] && $jab['gambar'] != '') : ?> 
														<strong>File</strong>
														<br><a target="_blank" href="{{ config('app.openfileimg') }}/{{ Auth::user()->id_emp }}/jab/{{ $jab['gambar'] }}">[File]</a>
													<?php else : ?>
														<strong>File</strong>
														<br>[Tidak ada SK Jab]
													<?php endif ?>
													<?php if ($jab['appr'] == '1') : ?> 
														<i class="fa fa-check" style="color: #2ECC40;" data-toggle="tooltip" title="Sudah Di Approve"></i>
													<?php else : ?>
														<i class="fa fa-close" style="color: #FF4136;" data-toggle="tooltip" title="Belum di approve, {{ $jab != '' && !(is_null($jab)) ?  $jab['alasan'] : '' }}"></i>
													<?php endif ?>
													</td>
													
													

													<td style="vertical-align: middle;">
														<button type="button" class="btn btn-info btn-outline btn-circle m-r-5 btn-update-jab" data-toggle="modal" data-target="#modal-update-jab" 
															data-ids="{{$jab['ids']}}"
															data-noid="{{$jab['noid']}}"
															data-tmt_jab="{{ date('d/m/Y',strtotime($jab['tmt_jab'])) }}"
															data-tmt_sk_jab="{{ date('d/m/Y',strtotime($jab['tmt_sk_jab'])) }}"
															data-no_sk_jab="{{$jab['no_sk_jab']}}"
															data-idjab="{{$jab['idjab']}}"
															data-jns_jab="{{$jab['jns_jab']}}"
															data-idunit="{{$jab['idunit']}}"
															data-idlok="{{$jab['idlok']}}"
															data-eselon="{{$jab['eselon']}}"
														><i class="ti-pencil-alt"></i></button>
														<button type="button" class="btn btn-danger btn-delete-jab btn-outline btn-circle m-r-5" data-toggle="modal" data-target="#modal-delete-jab"
															data-ids="{{$jab['ids']}}"
															data-noid="{{$jab['noid']}}"
															data-idjab="{{$jab['idjab']}}"
															data-nm_unit="{{ucwords(strtolower($jab['unit']['nm_unit']))}}"
														><i class="ti-trash"></i></button>
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
								@endif
								<div class="clearfix"></div>
							</div>
							<div role="tabpanel" class="tab-pane fade" id="tabs6">
								<button class="btn btn-info m-b-20 btn-insert-huk" type="button" data-toggle="modal" data-target="#modal-insert-huk">Tambah</button>

								@if(count($emp_huk) > 0)
								<div class="table-responsive">
									<table class="table table-hover manage-u-table">
										<tbody>
											@foreach($emp_huk as $key => $huk)
												<tr>
													@if (count($emp_huk) > 1)
													<td>
														<h1>{{ $key + 1 }}</h1>
													</td>
													@endif
													<td style="vertical-align: middle;">
														<strong>Hukuman {{ ucwords(strtolower($huk['jns_huk'])) }}</strong>
														<br>{{ date('d/M/Y',strtotime($huk['tgl_mulai'])) }} - {{ date('d/M/Y',strtotime($huk['tgl_akhir'])) }}
													</td>

													<td style="vertical-align: middle;">
														<strong>SK. 
														<?php if ($huk['no_sk']) : ?> 
															{{ $huk['no_sk'] }}
														<?php else : ?>
															-
														<?php endif ?>
														
														</strong><br>Tgl. 
														<?php if ($huk['tgl_sk']) : ?> 
															{{ date('d/M/Y',strtotime($huk['tgl_sk'])) }}
														<?php else : ?>
															-
														<?php endif ?>
													</td>

													<td style="vertical-align: middle;">
														<strong>File</strong><br>
														<?php if ($huk['gambar']) : ?> 
															<a target="_blank" href="{{ config('app.openfileimg') }}/{{ Auth::user()->id_emp }}/huk/{{ $huk['gambar'] }}">[Unduh File]</a>
														<?php else : ?>
															[File tidak tersedia]
														<?php endif ?>
														<?php if ($huk['appr'] == '1') : ?> 
															<i class="fa fa-check" style="color: #2ECC40;" data-toggle="tooltip" title="Sudah Di Approve"></i>
														<?php else : ?>
															<i class="fa fa-close" style="color: #FF4136;" data-toggle="tooltip" title="Belum di approve, {{ $huk != '' && !(is_null($huk)) ?  $huk['alasan'] : '' }}"></i>
														<?php endif ?>
													</td>


													<td style="vertical-align: middle;">
														
														<button type="button" class="btn btn-info btn-outline btn-circle m-r-5 btn-update-huk" data-toggle="modal" data-target="#modal-update-huk" 
															data-ids="{{$huk['ids']}}"
															data-noid="{{$huk['noid']}}"
															data-jns_huk="{{$huk['jns_huk']}}"
															data-tgl_mulai="{{ date('d/m/Y',strtotime($huk['tgl_mulai'])) }}"
															data-tgl_akhir="{{ date('d/m/Y',strtotime($huk['tgl_akhir'])) }}"
															data-no_sk="{{$huk['no_sk']}}"
															data-tgl_sk="{{ date('d/m/Y',strtotime($huk['tgl_sk'])) }}"
														><i class="ti-pencil-alt"></i></button>
														<button type="button" class="btn btn-danger btn-delete-huk btn-outline btn-circle m-r-5" data-toggle="modal" data-target="#modal-delete-huk"
															data-ids="{{$huk['ids']}}"
															data-noid="{{$huk['noid']}}"
														><i class="ti-trash"></i></button>
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
								@endif
								<div class="clearfix"></div>
							</div>
						</div>
					</div>
				</div>
			</div>














			<!-- MODAL KELUARGA -->
			<!-- 
			KELUARGA
			KELUARGA
			KELUARGA
			-->
			<div id="modal-insert-kel" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content modal-lg">
						<form method="POST" action="/portal/profil/form/tambahkelpegawai" class="form-horizontal" enctype="multipart/form-data">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Tambah Data Keluarga</b></h4>
							</div>
							<div class="modal-body">

								<div class="form-group">
									<label for="jns_kel" class="col-md-3 control-label"> Pilih </label>
									<div class="col-md-4">
										<select class="form-control" name="jns_kel" id="modal_insert_kel_jns_kel">
											@foreach($keluargas as $keluarga)
												<option value="{{ $keluarga['kel'] }}"> {{ $keluarga['kel'] }} </option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group">
									<label for="nm_kel" class="col-md-3 control-label"> Nama </label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="nm_kel" class="form-control" id="modal_insert_kel_nm_kel" required="">
									</div>
								</div>

								<div class="form-group">
									<label for="nik_kel" class="col-md-3 control-label"> NIK KTP </label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="nik_kel" class="form-control uintTextBox" id="modal_insert_kel_nik_kel" placeholder="Boleh Dikosongkan">
									</div>
								</div>

								<div class="form-group">
									<label for="tgl_kel" class="col-md-3 control-label"> Tanggal Lahir </label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="tgl_kel" class="form-control datepicker-autoclose-def" id="modal_insert_kel_tgl_kel" placeholder="dd/mm/yyyy" required="">
									</div>
								</div>

							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-info pull-right">Simpan</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div id="modal-update-kel" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content modal-lg">
						<form method="POST" action="/portal/profil/form/ubahkelpegawai" class="form-horizontal" enctype="multipart/form-data">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Ubah Data Keluarga</b></h4>
							</div>
							<div class="modal-body">
								
								<input type="hidden" name="ids" id="modal_update_kel_ids">
								<input type="hidden" name="noid" id="modal_update_kel_noid">

								<div class="form-group">
									<label for="jns_kel" class="col-md-3 control-label"> Pilih </label>
									<div class="col-md-4">
										<select class="form-control" name="jns_kel" id="modal_update_kel_jns_kel">
											@foreach($keluargas as $keluarga)
												<option value="{{ $keluarga['kel'] }}"> {{ $keluarga['kel'] }} </option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group">
									<label for="nm_kel" class="col-md-3 control-label"> Nama </label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="nm_kel" class="form-control" id="modal_update_kel_nm_kel" required="">
									</div>
								</div>

								<div class="form-group">
									<label for="nik_kel" class="col-md-3 control-label"> NIK KTP </label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="nik_kel" class="form-control uintTextBox" id="modal_update_kel_nik_kel" placeholder="Boleh Dikosongkan">
									</div>
								</div>

								<div class="form-group">
									<label for="tgl_kel" class="col-md-3 control-label"> Tanggal Lahir </label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="tgl_kel" class="form-control datepicker-autoclose-def" id="modal_update_kel_tgl_kel" placeholder="dd/mm/yyyy" required="">
									</div>
								</div>

							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-info pull-right">Simpan</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="modal fade modal-delete" id="modal-delete-kel">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/portal/profil/form/hapuskelpegawai" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Hapus Pendidikan</b></h4>
							</div>
							<div class="modal-body">
								<h4 class="label_delete"></h4>
								<input type="hidden" name="ids" id="modal_delete_kel_ids" value="">
								<input type="hidden" name="noid" id="modal_delete_kel_noid" value="">
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-danger pull-right">Hapus</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>


















			<!-- MODAL PENDIDIKAN -->
			<!-- 
			PENDIDIKAN
			PENDIDIKAN
			PENDIDIKAN
			-->
			<div id="modal-insert-dik" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content modal-lg">
						<form method="POST" action="/portal/profil/form/tambahdikpegawai" class="form-horizontal" enctype="multipart/form-data">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Tambah Pendidikan</b></h4>
							</div>
							<div class="modal-body">

								<div class="form-group">
									<label for="iddik" class="col-md-3 control-label"> Pendidikan Terakhir </label>
									<div class="col-md-9">
										<select class="form-control" name="iddik" id="modal_insert_dik_iddik">
											@foreach($pendidikans as $pendidikan)
												@if($pendidikan['urut'] != '0')
												<option value="{{ $pendidikan['dik'] }}"> {{ $pendidikan['nm_dik'] }} </option>
												@endif
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group">
									<label for="prog_sek" class="col-md-3 control-label"> Program Studi </label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="prog_sek" class="form-control" id="modal_insert_dik_prog_sek">
									</div>
								</div>

								<div class="form-group">
									<label for="nm_sek" class="col-md-3 control-label"> Nama Lembaga </label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="nm_sek" class="form-control" id="modal_insert_dik_nm_sek">
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-3 control-label"> Nomor / Tahun Ijazah </label>
									<div class="col-md-6">
										<input autocomplete="off" type="text" name="no_sek" class="form-control" id="modal_insert_dik_no_sek" placeholder="Nomor Ijazah">
									</div>
									<div class="col-md-3">
										<input autocomplete="off" type="text" name="th_sek" class="form-control intLimitTextBox4" id="modal_insert_dik_th_sek" placeholder="Tahun">
									</div>
								</div>

								<div class="form-group">
									<label for="gelar" class="col-md-3 control-label"> Gelar </label>
									<div class="col-md-3">
										<input autocomplete="off" type="text" name="gelar_dpn_sek" class="form-control" id="modal_insert_dik_gelar_dpn_sek" placeholder="Depan">
									</div>
									<div class="col-md-3">
										<input autocomplete="off" type="text" name="gelar_blk_sek" class="form-control" id="modal_insert_dik_gelar_blk_sek" placeholder="Belakang">
									</div>
								</div>

								<div class="form-group">
									<label for="ijz_cpns" class="col-md-3 control-label"> Ijazah </label>
									<div class="col-md-9">
										<select class="form-control" name="ijz_cpns" id="modal_insert_dik_ijz_cpns">
											<option value="Y"> Ada </option>
											<option value="T"> Tidak </option>
										</select>
									</div>
								</div>

								<div class="form-group">
									<label for="fileijazah" class="col-lg-3 control-label"> Upload Ijazah <br> <span style="font-size: 12px; color:red">Size Max 5MB</span> </label>
									<div class="col-lg-9">
										<input type="file" class="form-control" id="modal_insert_dik_fileijazah" name="fileijazah">
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-info pull-right">Simpan</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div id="modal-update-dik" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content modal-lg">
						<form method="POST" action="/portal/profil/form/ubahdikpegawai" class="form-horizontal" enctype="multipart/form-data">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Ubah Pendidikan</b></h4>
							</div>
							<div class="modal-body">
								
								<input type="hidden" name="ids" id="modal_update_dik_ids">
								<input type="hidden" name="noid" id="modal_update_dik_noid">

								<div class="form-group">
									<label for="iddik" class="col-md-3 control-label"> Pendidikan Terakhir </label>
									<div class="col-md-9">
										<select class="form-control" name="iddik" id="modal_update_dik_iddik">
											@foreach($pendidikans as $pendidikan)
												@if($pendidikan['urut'] != '0')
												<option value="{{ $pendidikan['dik'] }}"> {{ $pendidikan['nm_dik'] }} </option>
												@endif
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group">
									<label for="prog_sek" class="col-md-3 control-label"> Program Studi </label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="prog_sek" class="form-control" id="modal_update_dik_prog_sek">
									</div>
								</div>

								<div class="form-group">
									<label for="nm_sek" class="col-md-3 control-label"> Nama Lembaga </label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="nm_sek" class="form-control" id="modal_update_dik_nm_sek">
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-3 control-label"> Nomor / Tahun Ijazah </label>
									<div class="col-md-6">
										<input autocomplete="off" type="text" name="no_sek" class="form-control" id="modal_update_dik_no_sek" placeholder="Nomor Ijazah">
									</div>
									<div class="col-md-3">
										<input autocomplete="off" type="text" name="th_sek" class="form-control intLimitTextBox4" id="modal_update_dik_th_sek" placeholder="Tahun" required="">
									</div>
								</div>

								<div class="form-group">
									<label for="gelar" class="col-md-3 control-label"> Gelar </label>
									<div class="col-md-3">
										<input autocomplete="off" type="text" name="gelar_dpn_sek" class="form-control" id="modal_update_dik_gelar_dpn_sek" placeholder="Depan">
									</div>
									<div class="col-md-3">
										<input autocomplete="off" type="text" name="gelar_blk_sek" class="form-control" id="modal_update_dik_gelar_blk_sek" placeholder="Belakang">
									</div>
								</div>

								<div class="form-group">
									<label for="ijz_cpns" class="col-md-3 control-label"> Ijazah </label>
									<div class="col-md-9">
										<select class="form-control" name="ijz_cpns" id="modal_update_dik_ijz_cpns">
											<option value="Y"> Ada </option>
											<option value="T"> Tidak </option>
										</select>
									</div>
								</div>

								<div class="form-group">
									<label for="fileijazah" class="col-lg-3 control-label"> Upload Ijazah <br> <span style="font-size: 12px; color:red">Size Max 5MB</span> </label>
									<div class="col-lg-9">
										<input type="file" class="form-control" id="modal_update_dik_fileijazah" name="fileijazah">
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-info pull-right">Simpan</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="modal fade modal-delete" id="modal-delete-dik">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/portal/profil/form/hapusdikpegawai" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Hapus Pendidikan</b></h4>
							</div>
							<div class="modal-body">
								<h4 class="label_delete"></h4>
								<input type="hidden" name="ids" id="modal_delete_dik_ids" value="">
								<input type="hidden" name="noid" id="modal_delete_dik_noid" value="">
								<input type="hidden" name="iddik" id="modal_delete_dik_iddik" value="">
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-danger pull-right">Hapus</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>






















			<!-- MODAL NON FORMAL -->
			<!-- 
			NON FORMAL
			NON FORMAL
			NON FORMAL
			-->
			<div id="modal-insert-non" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content modal-lg">
						<form method="POST" action="/portal/profil/form/tambahnonpegawai" class="form-horizontal" enctype="multipart/form-data">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Tambah Pendidikan Non Formal</b></h4>
							</div>
							<div class="modal-body">

								<div class="form-group">
									<label for="nm_non" class="col-md-3 control-label"> Nama Kegiatan <span style="color: red; font-size: 20px;"> *</span></label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="nm_non" class="form-control" id="modal_insert_non_nm_non" required="">
									</div>
								</div>

								<div class="form-group">
									<label for="penye_non" class="col-md-3 control-label"> Penyelenggara <span style="color: red; font-size: 20px;"> *</span></label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="penye_non" class="form-control" id="modal_insert_non_penye_non">
									</div>
								</div>

								<div class="form-group">
									<label for="thn_non" class="col-md-3 control-label"> Tahun </label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="thn_non" class="form-control intLimitTextBox4" id="modal_insert_non_thn_non">
									</div>
								</div>

								<div class="form-group">
									<label for="durasi_non" class="col-md-3 control-label"> Durasi <br><span style="color: red; font-size: 12px;"> Dalam satuan hari</span></label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="durasi_non" class="form-control intLimitTextBox" id="modal_insert_non_durasi_non" value="0">
									</div>
								</div>

								<div class="form-group">
									<label for="sert_non" class="col-md-3 control-label"> Nomor Sertifikat </label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="sert_non" class="form-control" id="modal_insert_non_sert_non">
									</div>
								</div>

								<div class="form-group">
									<label for="tgl_non" class="col-md-3 control-label"> Tanggal Sertifikat </label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="tgl_non" class="form-control datepicker-autoclose-def" id="modal_insert_non_tgl_non" placeholder="dd/mm/yyyy">
									</div>
								</div>


								<div class="form-group">
									<label for="filenon" class="col-lg-3 control-label"> File <br> <span style="font-size: 12px; color:red">Size Max 5MB</span> </label>
									<div class="col-lg-9">
										<input type="file" class="form-control" id="modal_insert_non_filenon" name="filenon">
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-info pull-right">Simpan</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div id="modal-update-non" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content modal-lg">
						<form method="POST" action="/portal/profil/form/ubahnonpegawai" class="form-horizontal" enctype="multipart/form-data">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Ubah Pendidikan Non Formal</b></h4>
							</div>
							<div class="modal-body">

								<input type="hidden" name="ids" id="modal_update_non_ids">
								<input type="hidden" name="noid" id="modal_update_non_noid">

								<div class="form-group">
									<label for="nm_non" class="col-md-3 control-label"> Nama Kegiatan <span style="color: red; font-size: 20px;"> *</span></label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="nm_non" class="form-control" id="modal_update_non_nm_non" required="">
									</div>
								</div>

								<div class="form-group">
									<label for="penye_non" class="col-md-3 control-label"> Penyelenggara <span style="color: red; font-size: 20px;"> *</span></label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="penye_non" class="form-control" id="modal_update_non_penye_non">
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-3 control-label"> Tahun </label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="thn_non" class="form-control" id="modal_update_non_thn_non">
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-3 control-label"> Durasi <br><span style="color: red; font-size: 12px;"> Dalam satuan hari</span></label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="durasi_non" class="form-control" id="modal_update_non_durasi_non">
									</div>
								</div>

								<div class="form-group">
									<label for="sert_non" class="col-md-3 control-label"> Nomor Sertifikat </label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="sert_non" class="form-control" id="modal_update_non_sert_non">
									</div>
								</div>

								<div class="form-group">
									<label for="tgl_non" class="col-md-3 control-label"> Tanggal Sertifikat </label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="tgl_non" class="form-control datepicker-autoclose-def" id="modal_update_non_tgl_non" placeholder="dd/mm/yyyy">
									</div>
								</div>


								<div class="form-group">
									<label for="filenon" class="col-lg-3 control-label"> File <br> <span style="font-size: 12px; color:red">Size Max 5MB</span> </label>
									<div class="col-lg-9">
										<input type="file" class="form-control" id="modal_update_non_filenon" name="filenon">
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-info pull-right">Simpan</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="modal fade modal-delete" id="modal-delete-non">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/portal/profil/form/hapusnonpegawai" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Hapus Pendidikan Non Formal</b></h4>
							</div>
							<div class="modal-body">
								<h4 class="label_delete"></h4>
								<input type="hidden" name="ids" id="modal_delete_non_ids" value="">
								<input type="hidden" name="noid" id="modal_delete_non_noid" value="">
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-danger pull-right">Hapus</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>




















			<!-- MODAL GOLONGAN -->
			<!-- 
			GOLONGAN
			GOLONGAN
			GOLONGAN
			-->
			<div id="modal-insert-gol" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content modal-lg">
						<form method="POST" action="/portal/profil/form/tambahgolpegawai" class="form-horizontal" enctype="multipart/form-data">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Tambah Golongan</b></h4>
							</div>
							<div class="modal-body">

								<input type="hidden" name="noid" value="{{$id_emp}}">

								<div class="form-group">
									<label class="col-md-3 control-label"> TMT Golongan <span style="color: red; font-size: 20px;"> *</span></label>
									<div class="col-md-8">
										<input type="text" name="tmt_gol" class="form-control" id="datepicker-autoclose8" autocomplete="off" placeholder="dd/mm/yyyy" required="">
									</div>
								</div>

								<div class="form-group">
									<label for="no_sk_gol" class="col-md-3 control-label"> Nomor SK </label>
									<div class="col-md-8">
										<input autocomplete="off" type="text" name="no_sk_gol" class="form-control">
									</div>
								</div>

								<div class="form-group">
									<label for="tmt_sk_gol" class="col-md-3 control-label"> Tanggal SK <span style="color: red; font-size: 20px;"> *</span></label>
									<div class="col-md-8">
										<input type="text" name="tmt_sk_gol" class="form-control" id="datepicker-autoclose9" autocomplete="off" placeholder="dd/mm/yyyy" required="">
									</div>
								</div>

								<div class="form-group">
									<label for="idgol" class="col-md-3 control-label"> Golongan </label>
									<div class="col-md-8">
										<select class="form-control select2" name="idgol">
											@foreach($golongans as $golongan)
												<option value="{{ $golongan['gol'] }}" > {{ $golongan['gol'] }} - {{ $golongan['nm_pangkat'] }} </option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group">
									<label for="jns_kp" class="col-md-3 control-label"> Jenis KP <br> <span style="font-size: 10px">KP (Kenaikan Pangkat)</span> </label>
									<div class="col-md-8">
										<select class="form-control" name="jns_kp">
											<option value="Reguler"> Reguler </option>
											<option value="Pilihan"> Pilihan </option>
											<option value="Penghargaan"> Penghargaan </option>
											<option value="Istimewa"> Istimewa </option>
										</select>
									</div>
								</div>

								<!-- <div class="form-group">
									<label class="col-md-3 control-label"> Masa Kerja </label>
									<div class="col-md-3">
										<input autocomplete="off" type="text" name="mk_thn" class="form-control intLimitTextBox" placeholder="Tahun">
									</div>
									<label for="tmt_sk_gol" class="col-md-1 control-label"> Tahun </label>
									<div class="col-md-3">
										<input autocomplete="off" type="text" name="mk_bln" class="form-control intLimitTextBox" placeholder="Bulan">
									</div>
									<label for="tmt_sk_gol" class="col-md-1 control-label"> Bulan </label>
								</div> -->

								<div class="form-group">
									<label for="fileijazah" class="col-lg-3 control-label"> Upload File <br> <span style="font-size: 12px; color:red">Size Max 5MB</span> </label>
									<div class="col-lg-9">
										<input type="file" class="form-control" id="modal_insert_gol_file" name="filegol">
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-info pull-right">Simpan</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div id="modal-update-gol" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content modal-lg">
						<form method="POST" action="/portal/profil/form/ubahgolpegawai" class="form-horizontal" enctype="multipart/form-data">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Ubah Golongan </b></h4>
							</div>
							<div class="modal-body">
								
								<input type="hidden" name="ids" id="modal_update_gol_ids">
								<input type="hidden" name="noid" id="modal_update_gol_noid">

								<div class="form-group col-md-12">
									<label class="col-md-2 control-label"> TMT Golongan <span style="color: red; font-size: 20px;"> *</span></label>
									<div class="col-md-8">
										<input type="text" name="tmt_gol" class="form-control" id="datepicker-autoclose3" autocomplete="off" placeholder="dd/mm/yyyy" required="">
									</div>
								</div>

								<div class="form-group col-md-12">
									<label for="no_sk_gol" class="col-md-2 control-label"> Nomor SK </label>
									<div class="col-md-8">
										<input autocomplete="off" type="text" name="no_sk_gol" class="form-control" id="modal_update_gol_nosk">
									</div>
								</div>

								<div class="form-group col-md-12">
									<label for="tmt_sk_gol" class="col-md-2 control-label"> Tanggal SK <span style="color: red; font-size: 20px;"> *</span></label>
									<div class="col-md-8">
										<input type="text" name="tmt_sk_gol" class="form-control" id="datepicker-autoclose4" autocomplete="off" placeholder="dd/mm/yyyy" required="">
									</div>
								</div>

								<div class="form-group col-md-12">
									<label for="idgol" class="col-md-2 control-label"> Golongan </label>
									<div class="col-md-8">
										<select class="form-control select2" name="idgol" id="modal_update_gol_idgol">
											@foreach($golongans as $golongan)
												<option value="{{ $golongan['gol'] }}" <?php if ($gol['idgol'] == $golongan['gol'] ): ?> selected <?php endif ?> > {{ $golongan['gol'] }} - {{ $golongan['nm_pangkat'] }} </option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group col-md-12">
									<label for="jns_kp" class="col-md-2 control-label"> Jenis KP <br> <span style="font-size: 10px">KP (Kenaikan Pangkat)</span> </label>
									<div class="col-md-8">
										<select class="form-control" name="jns_kp" id="modal_update_gol_jnskp">
											<option <?php if ($gol['jns_kp'] == "Reguler" ): ?> selected <?php endif ?> value="Reguler"> Reguler </option>
											<option <?php if ($gol['jns_kp'] == "Pilihan" ): ?> selected <?php endif ?> value="Pilihan"> Pilihan </option>
											<option <?php if ($gol['jns_kp'] == "Penghargaan" ): ?> selected <?php endif ?> value="Penghargaan"> Penghargaan </option>
											<option <?php if ($gol['jns_kp'] == "Istimewa" ): ?> selected <?php endif ?> value="Istimewa"> Istimewa </option>
										</select>
									</div>
								</div>

								<!-- <div class="form-group col-md-12">
									<label class="col-md-2 control-label"> Masa Kerja </label>
									<div class="col-md-3">
										<input autocomplete="off" type="text" name="mk_thn" class="form-control intLimitTextBox" placeholder="Tahun" id="modal_update_gol_mkthn">
									</div>
									<label for="tmt_sk_gol" class="col-md-1 control-label"> Tahun </label>
									<div class="col-md-3">
										<input autocomplete="off" type="text" name="mk_bln" class="form-control intLimitTextBox" placeholder="Bulan" id="modal_update_gol_mkbln">
									</div>
									<label for="tmt_sk_gol" class="col-md-1 control-label"> Bulan </label>
								</div> -->


								<div class="form-group col-md-12">
									<label for="filegol" class="col-lg-2 control-label"> Upload File <br> <span style="font-size: 12px; color:red">Size Max 5MB</span> </label>
									<div class="col-lg-8">
										<input type="file" class="form-control" id="modal_update_gol_file" name="filegol">
									</div>
								</div>
								
								<div class="clearfix"></div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-info pull-right">Simpan</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="modal fade modal-delete" id="modal-delete-gol">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/portal/profil/form/hapusgolpegawai" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Hapus Golongan</b></h4>
							</div>
							<div class="modal-body">
								<h4 class="label_delete"></h4>
								<input type="hidden" name="ids" id="modal_delete_gol_ids" value="">
								<input type="hidden" name="noid" id="modal_delete_gol_noid" value="">
								<input type="hidden" name="idgol" id="modal_delete_gol_idgol" value="">
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-danger pull-right">Hapus</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>



















			<!-- MODAL JABATAN -->
			<!-- 
			JABATAN
			JABATAN
			JABATAN
			-->
			<div id="modal-insert-jab" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content modal-lg">
						<form method="POST" action="/portal/profil/form/tambahjabpegawai" class="form-horizontal" enctype="multipart/form-data">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Tambah Jabatan</b></h4>
							</div>
							<div class="modal-body">

								<input type="hidden" name="noid" value="{{$id_emp}}">

								<div class="form-group">
									<label for="jns_jab" class="col-md-2 control-label"> Jenis Jabatan </label>
									<div class="col-md-8">
										<select class="form-control select2" name="jns_jab" id="jns_jab">
											<option value="STRUKTURAL" > STRUKTURAL </option>
											<option value="FUNGSIONAL" > FUNGSIONAL </option>
										</select>
									</div>
								</div>

								{{--<div class="form-group">
									<label for="idjab" class="col-md-2 control-label"> Jabatan </label>
									<div class="col-md-8">
										<select class="form-control select2" name="idjab" id="idjab">
											@foreach($jabatans as $jabatan)
												<option value="{{ $jabatan['jabatan'] }}" > {{ $jabatan['jabatan'] }} </option>
											@endforeach
										</select>
									</div>
								</div>--}}

								<div class="form-group">
									<label for="idunit" class="col-md-2 control-label"> Unit Organisasi </label>
									<div class="col-md-8">
										<select class="form-control select2" name="idunit" id="idunit">
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
									</div>
								</div>

								{{-- <div class="form-group">
									<label for="idlok" class="col-md-2 control-label"> Lokasi </label>
									<div class="col-md-8">
										<select class="form-control" name="idlok" id="idlok">
											@foreach($lokasis as $lokasi)
												<option value="{{ $lokasi['kd_lok'] }}"> {{ $lokasi['nm_lok'] }}</option>
											@endforeach
										</select>
									</div>
								</div> --}}

								<div class="form-group">
									<label for="eselon" class="col-md-2 control-label"> Golongan </label>
									<div class="col-md-8">
										<select class="form-control select2" name="eselon" id="eselon">
											@foreach($golongans as $golongan)
												<option value="{{ $golongan['gol'] }}" > {{ $golongan['gol'] }} - {{ $golongan['nm_pangkat'] }} </option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-2 control-label"> TMT Jabatan <span style="color: red; font-size: 20px;"> *</span></label>
									<div class="col-md-8">
										<input type="text" name="tmt_jab" class="form-control" id="datepicker-autoclose10" autocomplete="off" placeholder="dd/mm/yyyy" required="">
									</div>
								</div>

								<div class="form-group">
									<label for="no_sk_jab" class="col-md-2 control-label"> No SK Jabatan </label>
									<div class="col-md-8">
										<input autocomplete="off" type="text" name="no_sk_jab" class="form-control" >
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-2 control-label"> Tanggal SK <span style="color: red; font-size: 20px;"> *</span></label>
									<div class="col-md-8">
										<input type="text" name="tmt_sk_jab" class="form-control" id="datepicker-autoclose11" autocomplete="off" placeholder="dd/mm/yyyy" required="">
									</div>
								</div>

								<div class="form-group">
									<label for="fileijazah" class="col-lg-2 control-label"> Upload File <br> <span style="font-size: 12px; color:red">Size Max 5MB</span> </label>
									<div class="col-lg-8">
										<input type="file" class="form-control" id="modal_insert_jab_file" name="filejab">
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
			<div id="modal-update-jab" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content modal-lg">
						<form method="POST" action="/portal/profil/form/ubahjabpegawai" class="form-horizontal" enctype="multipart/form-data">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Ubah Jabatan </b></h4>
							</div>
							<div class="modal-body">
								
								<input type="hidden" name="ids" id="modal_update_jab_ids">
								<input type="hidden" name="noid" id="modal_update_jab_noid">

								<div class="form-group col-md-12">
									<label for="jns_jab" class="col-md-2 control-label"> Jenis Jabatan </label>
									<div class="col-md-8">
										<select class="form-control" name="jns_jab" id="modal_update_jab_jns_jab">
											<option value="STRUKTURAL">STRUKTURAL</option>
											<option value="FUNGSIONAL">FUNGSIONAL</option>
										</select>
									</div>
								</div>

								{{--<div class="form-group col-md-12">
									<label for="idjab" class="col-md-2 control-label"> Jabatan </label>
									<div class="col-md-8">
										<select class="form-control select2 modal_update_idjab" name="idjab" id="modal_update_jab_idjab">
											@foreach($jabatans as $jabatan)
												<option value="{{ $jabatan['jabatan'] }}"> {{ $jabatan['jabatan'] }} </option>
											@endforeach
										</select>
									</div>
								</div>--}}

								<div class="form-group col-md-12">
									<label for="idunit" class="col-md-2 control-label"> Unit Organisasi </label>
									<div class="col-md-8">
										<select class="form-control select2" name="idunit" id="modal_update_jab_idunit">
											@foreach($units as $unit)
												<option value="{{ $unit['kd_unit'] }}" > {{ $unit['kd_unit'] }} - {{ $unit['notes'] }}

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
									</div>
								</div>

								{{-- <div class="form-group col-md-12">
									<label for="idlok" class="col-md-2 control-label"> Lokasi </label>
									<div class="col-md-8">
										<select class="form-control" name="idlok" id="modal_update_jab_idlok">
											@foreach($lokasis as $lokasi)
												<option value="{{ $lokasi['kd_lok'] }}"> {{ $lokasi['nm_lok'] }}</option>
											@endforeach
										</select>
									</div>
								</div> --}}

								<div class="form-group col-md-12">
									<label for="eselon" class="col-md-2 control-label"> Golongan </label>
									<div class="col-md-8">
										<select class="form-control select2" name="eselon" id="modal_update_jab_eselon">
											@foreach($golongans as $golongan)
												<option value="{{ $golongan['gol'] }}" > {{ $golongan['gol'] }} - {{ $golongan['nm_pangkat'] }} </option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group col-md-12">
									<label class="col-md-2 control-label"> TMT Jabatan <span style="color: red; font-size: 20px;"> *</span></label>
									<div class="col-md-8">
										<input type="text" name="tmt_jab" class="form-control" id="datepicker-autoclose5" autocomplete="off" placeholder="dd/mm/yyyy" required="">
									</div>
								</div>

								<div class="form-group col-md-12">
									<label for="no_sk_jab" class="col-md-2 control-label"> No SK Jabatan </label>
									<div class="col-md-8">
										<input autocomplete="off" type="text" name="no_sk_jab" class="form-control" id="modal_update_jab_no_sk_jab" >
									</div>
								</div>

								<div class="form-group col-md-12">
									<label class="col-md-2 control-label"> Tanggal SK <span style="color: red; font-size: 20px;"> *</span></label>
									<div class="col-md-8">
										<input type="text" name="tmt_sk_jab" class="form-control" id="datepicker-autoclose6" autocomplete="off" placeholder="dd/mm/yyyy" required="">
									</div>
								</div>

								<div class="clearfix"></div>


								<div class="form-group col-md-12">
									<label for="filejab" class="col-lg-2 control-label"> Upload File <br> <span style="font-size: 12px; color:red">Size Max 5MB</span> </label>
									<div class="col-lg-8">
										<input type="file" class="form-control" id="modal_update_jab_file" name="filejab">
									</div>
								</div>
								
								<div class="clearfix"></div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-info pull-right">Simpan</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="modal fade modal-delete" id="modal-delete-jab">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/portal/profil/form/hapusjabpegawai" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Hapus Jabatan</b></h4>
							</div>
							<div class="modal-body">
								<h4 class="label_delete"></h4>
								<input type="hidden" name="ids" id="modal_delete_jab_ids" value="">
								<input type="hidden" name="noid" id="modal_delete_jab_noid" value="">
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-danger pull-right">Hapus</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>

























			<!-- MODAL HUKUMAN DISIPLIN -->
			<!-- 
			HUKUMAN DISIPLIN
			HUKUMAN DISIPLIN
			HUKUMAN DISIPLIN
			-->
			<div id="modal-insert-huk" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content modal-lg">
						<form method="POST" action="/portal/profil/form/tambahhukpegawai" class="form-horizontal" enctype="multipart/form-data">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Tambah Hukuman Disiplin</b></h4>
							</div>
							<div class="modal-body">

								<div class="form-group">
									<label for="jns_huk" class="col-lg-2 control-label"> Pilih </label>
									<div class="col-md-4">
										<select class="form-control" name="jns_huk" id="modal_insert_huk_jns_huk">
											<option value="Ringan"> Ringan </option>
											<option value="Sedang"> Sedang </option>
											<option value="Berat"> Berat </option>
										</select>
									</div>
								</div>

								<div class="form-group">
									<label for="tgl_mulai" class="col-lg-2 control-label"> Tanggal Mulai </label>
									<div class="col-md-4">
										<input autocomplete="off" type="text" name="tgl_mulai" class="form-control datepicker-autoclose-def" id="modal_insert_huk_tgl_mulai" placeholder="dd/mm/yyyy" required="">
									</div>
								</div>

								<div class="form-group">
									<label for="tgl_akhir" class="col-lg-2 control-label"> Tanggal Akhir </label>
									<div class="col-md-4">
										<input autocomplete="off" type="text" name="tgl_akhir" class="form-control datepicker-autoclose-def" id="modal_insert_huk_tgl_akhir" placeholder="dd/mm/yyyy" required="">
									</div>
								</div>

								<div class="form-group">
									<label for="no_sk" class="col-lg-2 control-label"> No SK </label>
									<div class="col-md-8">
										<input autocomplete="off" type="text" name="no_sk" class="form-control" id="modal_insert_kel_no_sk" required="">
									</div>
								</div>

								<div class="form-group">
									<label for="tgl_sk" class="col-lg-2 control-label"> Tanggal SK </label>
									<div class="col-md-4">
										<input autocomplete="off" type="text" name="tgl_sk" class="form-control datepicker-autoclose-def" id="modal_insert_huk_tgl_sk" placeholder="dd/mm/yyyy" required="">
									</div>
								</div>

								<div class="form-group ">
									<label for="filehuk" class="col-lg-2 control-label"> Upload File <br> <span style="font-size: 12px; color:red">Size Max 5MB</span> </label>
									<div class="col-lg-8">
										<input type="file" class="form-control" id="modal_insert_huk_file" name="filehuk">
									</div>
								</div>


							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-info pull-right">Simpan</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div id="modal-update-huk" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content modal-lg">
						<form method="POST" action="/portal/profil/form/ubahhukpegawai" class="form-horizontal" enctype="multipart/form-data">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Ubah Hukuman Disiplin</b></h4>
							</div>
							<div class="modal-body">
								
								<input type="hidden" name="ids" id="modal_update_huk_ids">
								<input type="hidden" name="noid" id="modal_update_huk_noid">

								<div class="form-group">
									<label for="jns_huk" class="col-lg-2 control-label"> Pilih </label>
									<div class="col-md-4">
										<select class="form-control" name="jns_huk" id="modal_update_huk_jns_huk">
											<option value="Ringan"> Ringan </option>
											<option value="Sedang"> Sedang </option>
											<option value="Berat"> Berat </option>
										</select>
									</div>
								</div>

								<div class="form-group">
									<label for="tgl_mulai" class="col-lg-2 control-label"> Tanggal Mulai </label>
									<div class="col-md-4">
										<input autocomplete="off" type="text" name="tgl_mulai" class="form-control datepicker-autoclose-def" id="modal_update_huk_tgl_mulai" placeholder="dd/mm/yyyy" required="">
									</div>
								</div>

								<div class="form-group">
									<label for="tgl_akhir" class="col-lg-2 control-label"> Tanggal Akhir </label>
									<div class="col-md-4">
										<input autocomplete="off" type="text" name="tgl_akhir" class="form-control datepicker-autoclose-def" id="modal_update_huk_tgl_akhir" placeholder="dd/mm/yyyy" required="">
									</div>
								</div>

								<div class="form-group">
									<label for="no_sk" class="col-lg-2 control-label"> No SK </label>
									<div class="col-md-8">
										<input autocomplete="off" type="text" name="no_sk" class="form-control" id="modal_update_huk_no_sk" required="">
									</div>
								</div>

								<div class="form-group">
									<label for="tgl_sk" class="col-lg-2 control-label"> Tanggal SK </label>
									<div class="col-md-4">
										<input autocomplete="off" type="text" name="tgl_sk" class="form-control datepicker-autoclose-def" id="modal_update_huk_tgl_sk" placeholder="dd/mm/yyyy" required="">
									</div>
								</div>

								<div class="form-group ">
									<label for="filehuk" class="col-lg-2 control-label"> Upload File <br> <span style="font-size: 12px; color:red">Size Max 5MB</span> </label>
									<div class="col-lg-8">
										<input type="file" class="form-control" id="modal_update_huk_file" name="filehuk">
									</div>
								</div>

							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-info pull-right">Simpan</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="modal fade modal-delete" id="modal-delete-huk">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/portal/profil/form/hapushukpegawai" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Hapus Hukuman Disiplin</b></h4>
							</div>
							<div class="modal-body">
								<h4 class="label_delete"></h4>
								<input type="hidden" name="ids" id="modal_delete_huk_ids" value="">
								<input type="hidden" name="noid" id="modal_delete_huk_noid" value="">
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
	<script src="{{ ('/portal/public/ample/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>
	<!-- Date Picker Plugin JavaScript -->
	<script src="{{ ('/portal/public/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
	<!-- jQuery x-editable -->
	<script src="{{ ('/portal/public/ample/plugins/bower_components/moment/moment.js') }}"></script>
	<script type="text/javascript" src="{{ ('/portal/public/ample/plugins/bower_components/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min.js') }}"></script>
	<script type="text/javascript">
		$('#inline-nm_emp ').editable({
			type: 'text'
			, name: 'nm_emp'
			, mode: 'inline'
		});

		$('#inline-gelar_blk').editable({
			type: 'text'
			, name: 'gelar_blk'
			, mode: 'inline'
			, url: '/post'
			, success: function(response, newValue) {
				$.ajax({
					type: "POST",
					url: "/portal/post",
					data: { somefield: "Some field value", another: "another", _token: '{{csrf_token()}}' },
					success: function(data){
						alert(data);
					}
				});
			}
		});
	</script>

	<script>
		(function($) {
		  $.fn.inputFilter = function(inputFilter) {
			return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
			  if (inputFilter(this.value)) {
				this.oldValue = this.value;
				this.oldSelectionStart = this.selectionStart;
				this.oldSelectionEnd = this.selectionEnd;
			  } else if (this.hasOwnProperty("oldValue")) {
				this.value = this.oldValue;
				this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
			  } else {
				this.value = "";
			  }
			});
		  };
		}(jQuery));

		$(".intLimitTextBox").inputFilter(function(value) {
			return /^\d*$/.test(value) && (value === "" || parseInt(value) <= 99); 
		});

		$(".intLimitTextBox4").inputFilter(function(value) {
			return /^\d*$/.test(value) && (value === "" || parseInt(value) <= 9999); 
		});

		$(".uintTextBox").inputFilter(function(value) {
 	 		return /^\d*$/.test(value); 
 	 	});

		$(".select2").select2();

		$(function () {

			$(".data-input").hide();

			$('.btn-edit-id').on('click', function () {
				$(this).text(function(i, text){
					return text === "Ubah" ? "Kembali" : "Ubah";
				});
				$(".data-show").toggle();
				$(".data-input").toggle();
			});

			jQuery('.datepicker-autoclose-def').datepicker({
				autoclose: true
				, todayHighlight: false
				, format: 'dd/mm/yyyy'
				, orientation: "auto"
			});

			jQuery('#datepicker-autoclose').datepicker({
				autoclose: true
				, todayHighlight: false
				, format: 'dd/mm/yyyy'
				, orientation: "auto"
			});

			jQuery('#datepicker-autoclose2').datepicker({
				autoclose: true
				, todayHighlight: false
				, format: 'dd/mm/yyyy'
				, orientation: "auto"
			});

			jQuery('#datepicker-autoclose3').datepicker({
				autoclose: true
				, todayHighlight: false
				, format: 'dd/mm/yyyy'
				, orientation: "auto"
			});

			jQuery('#datepicker-autoclose4').datepicker({
				autoclose: true
				, todayHighlight: false
				, format: 'dd/mm/yyyy'
				, orientation: "auto"
			});

			jQuery('#datepicker-autoclose5').datepicker({
				autoclose: true
				, todayHighlight: false
				, format: 'dd/mm/yyyy'
				, orientation: "auto"
			});

			jQuery('#datepicker-autoclose6').datepicker({
				autoclose: true
				, todayHighlight: false
				, format: 'dd/mm/yyyy'
				, orientation: "auto"
			});

			jQuery('#datepicker-autoclose8').datepicker({
				autoclose: true
				, todayHighlight: false
				, format: 'dd/mm/yyyy'
				, orientation: "auto"
			});

			jQuery('#datepicker-autoclose9').datepicker({
				autoclose: true
				, todayHighlight: false
				, format: 'dd/mm/yyyy'
				, orientation: "auto"
			});

			jQuery('#datepicker-autoclose10').datepicker({
				autoclose: true
				, todayHighlight: false
				, format: 'dd/mm/yyyy'
				, orientation: "auto"
			});

			jQuery('#datepicker-autoclose11').datepicker({
				autoclose: true
				, todayHighlight: false
				, format: 'dd/mm/yyyy'
				, orientation: "auto"
			});

			$('.btn-update-kel').on('click', function () {
				var $el = $(this);
				console.log($el.data());
				$("#modal_update_kel_ids").val($el.data('ids'));
				$("#modal_update_kel_noid").val($el.data('noid'));
				$("#modal_update_kel_jns_kel").val($el.data('jns_kel'));
				$("#modal_update_kel_nm_kel").val($el.data('nm_kel'));
				$("#modal_update_kel_nik_kel").val($el.data('nik_kel'));
				$("#modal_update_kel_tgl_kel").val($el.data('tgl_kel'));
			});
			$('.btn-delete-kel').on('click', function () {
				var $el = $(this);
				$(".label_delete").append('Apakah anda yakin ingin menghapus data <b>' + $el.data('jns_kel') + '</b>?');
				$("#modal_delete_kel_ids").val($el.data('ids'));
				$("#modal_delete_kel_noid").val($el.data('noid'));
			});














			$('.btn-update-dik').on('click', function () {
				var $el = $(this);
				$("#modal_update_dik_ids").val($el.data('ids'));
				$("#modal_update_dik_noid").val($el.data('noid'));
				$("#modal_update_dik_iddik").val($el.data('iddik'));
				$("#modal_update_dik_prog_sek").val($el.data('prog_sek'));
				$("#modal_update_dik_no_sek").val($el.data('no_sek'));
				$("#modal_update_dik_th_sek").val($el.data('th_sek'));
				$("#modal_update_dik_nm_sek").val($el.data('nm_sek'));
				$("#modal_update_dik_gelar_blk_sek").val($el.data('gelar_blk_sek'));
				$("#modal_update_dik_gelar_dpn_sek").val($el.data('gelar_dpn_sek'));
				$("#modal_update_dik_ijz_cpns").val($el.data('ijz_cpns'));
			});
			$('.btn-delete-dik').on('click', function () {
				var $el = $(this);
				$(".label_delete").append('Apakah anda yakin ingin menghapus <b>' + $el.data('iddik') + '</b>?');
				$("#modal_delete_dik_ids").val($el.data('ids'));
				$("#modal_delete_dik_noid").val($el.data('noid'));
				$("#modal_delete_dik_iddik").val($el.data('iddik'));
			});














			$('.btn-update-non').on('click', function () {
				var $el = $(this);
				$("#modal_update_non_ids").val($el.data('ids'));
				$("#modal_update_non_noid").val($el.data('noid'));
				$("#modal_update_non_nm_non").val($el.data('nm_non'));
				$("#modal_update_non_penye_non").val($el.data('penye_non'));
				$("#modal_update_non_thn_non").val($el.data('thn_non'));
				$("#modal_update_non_durasi_non").val($el.data('durasi_non'));
				$("#modal_update_non_sert_non").val($el.data('sert_non'));
				$("#modal_update_non_tgl_non").val($el.data('tgl_non'));
			});
			$('.btn-delete-non').on('click', function () {
				var $el = $(this);
				$(".label_delete").append('Apakah anda yakin ingin menghapus kegiatan <b>' + $el.data('nm_non') + '</b>?');
				$("#modal_delete_non_ids").val($el.data('ids'));
				$("#modal_delete_non_noid").val($el.data('noid'));
			});



















			$('.btn-update-gol').on('click', function () {
				var $el = $(this);
				$("#modal_update_gol_ids").val($el.data('ids'));
				$("#modal_update_gol_noid").val($el.data('noid'));
				$("#datepicker-autoclose3").val($el.data('tmt_gol'));
				$("#modal_update_gol_nosk").val($el.data('no_sk_gol'));
				$("#datepicker-autoclose4").val($el.data('tmt_sk_gol'));
				$("#modal_update_gol_idgol").select2("val", $el.data('idgol'));
				$("#modal_update_gol_jnskp").val($el.data('jns_kp'));
				$("#modal_update_gol_mkthn").val($el.data('mk_thn'));
				$("#modal_update_gol_mkbln").val($el.data('mk_bln'));
			});
			$('.btn-delete-gol').on('click', function () {
				var $el = $(this);
				$(".label_delete").append('Apakah anda yakin ingin menghapus <b>' + $el.data('idgol') + '</b>?');
				$("#modal_delete_gol_ids").val($el.data('ids'));
				$("#modal_delete_gol_noid").val($el.data('noid'));
				$("#modal_delete_gol_idgol").val($el.data('idgol'));
			});
















			$('.btn-update-jab').on('click', function () {
				var $el = $(this);
				$("#modal_update_jab_ids").val($el.data('ids'));
				$("#modal_update_jab_noid").val($el.data('noid'));
				$("#modal_update_jab_jns_jab").select2("val", $el.data('jns_jab'));
				// $("#modal_update_jab_idjab").select2("val", $el.data('idjab'));
				$(".modal_update_idjab").select2().select2('val', $el.data('idjab'));;
				$("#modal_update_jab_idunit").select2("val", $el.data('idunit'));
				$("#modal_update_jab_idlok").val($el.data('idlok'));
				$("#modal_update_jab_eselon").select2("val", $el.data('eselon'));
				$("#datepicker-autoclose5").val($el.data('tmt_jab'));
				$("#datepicker-autoclose6").val($el.data('tmt_sk_jab'));
				$("#modal_update_jab_no_sk_jab").val($el.data('no_sk_jab'));
			});
			$('.btn-delete-jab').on('click', function () {
				var $el = $(this);
				$(".label_delete").append('Apakah anda yakin ingin menghapus jabatan <b>' + $el.data('idjab') + '</b> pada unit kerja <b>' + $el.data('nm_unit') + '</b>?');
				$("#modal_delete_jab_ids").val($el.data('ids'));
				$("#modal_delete_jab_noid").val($el.data('noid'));
			});




















			$('.btn-update-huk').on('click', function () {
				var $el = $(this);
				$("#modal_update_huk_ids").val($el.data('ids'));
				$("#modal_update_huk_noid").val($el.data('noid'));
				$("#modal_update_huk_jns_huk").val($el.data('jns_huk'));
				$("#modal_update_huk_tgl_mulai").val($el.data('tgl_mulai'));
				$("#modal_update_huk_tgl_akhir").val($el.data('tgl_akhir'));
				$("#modal_update_huk_no_sk").val($el.data('no_sk'));
				$("#modal_update_huk_tgl_sk").val($el.data('tgl_sk'));
			});
			$('.btn-delete-huk').on('click', function () {
				var $el = $(this);
				$(".label_delete").append('Apakah anda yakin ingin menghapus hukuman disiplin ini?');
				$("#modal_delete_huk_ids").val($el.data('ids'));
				$("#modal_delete_huk_noid").val($el.data('noid'));
			});










			

			$(".modal-delete").on("hidden.bs.modal", function () {
				$(".label_delete").empty();
			});
		});
	</script>
@endsection