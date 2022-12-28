@extends('layouts.masterhome')

@section('css')
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/portal/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<!-- Menu CSS -->
	<link href="{{ ('/portal/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
	<!-- animation CSS -->
	<link href="{{ ('/portal/ample/css/animate.css') }}" rel="stylesheet">
	<!-- Custom CSS -->
	<link href="{{ ('/portal/ample/css/style.css') }}" rel="stylesheet">
	<!-- color CSS -->
	<link href="{{ ('/portal/ample/css/colors/purple-dark.css') }}" id="theme" rel="stylesheet">
	<!-- Date picker plugins css -->
	<link href="{{ ('/portal/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
	<!-- page CSS -->
	<link href="{{ ('/portal/ample/plugins/bower_components/custom-select/custom-select.css') }}" rel="stylesheet" type="text/css" />

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
			<div class="row">
				<div class="col-md-12">
					
						<div class="panel panel-info">
							<div class="panel-heading"> Data Pegawai {{ ucwords(strtolower($emp_data['nm_emp'])) }} </div>
							<div class="panel-wrapper collapse in" aria-expanded="true">
								<div class="panel-body">
									<div class="sttabs tabs-style-underline">
									<nav>
										<ul>
											<li><a href="#section-underline-1" class=""><span>Identitas</span></a></li>
											<li><a href="#section-underline-2" class=""><span>Keluarga</span></a></li>
											<li><a href="#section-underline-3" class=""><span>Pendidikan</span></a></li>
											<li><a href="#section-underline-4" class=""><span>Golongan</span></a></li>
											<li><a href="#section-underline-5" class=""><span>Unit Kerja</span></a></li>
											<li><a href="#section-underline-6" class=""><span>Hukuman Disiplin</span></a></li>
											<li><a href="#section-underline-7" class=""><span>Berkas Lainnya</span></a></li>
											<li><a href="#section-underline-8" class=""><span>Status</span></a></li>
										</ul>
									</nav>
									<div class="content-wrap">
										<section id="section-underline-1">
                                        <h2><b>IDENTITAS</b></h2>
										
                                        <form class="form-horizontal">
										@csrf
											<div class="col-md-12">
												<input type="hidden" name="id_emp" value="{{ $id_emp }}">

												<div class="form-group">
													<label class="col-md-2 control-label"> ID </label>
													<div class="col-md-8">
                                                        <p class="form-control-static"> {{ $emp_data['id_emp'] }} </p>
													</div>
												</div>

												<div class="form-group">
													<label for="tgl_join" class="col-md-2 control-label"> TMT di BPAD <span style="color: red; font-size: 20px;"> *</span></label>
													<div class="col-md-8">
                                                        <p class="form-control-static"> {{ date('d/m/Y', strtotime($emp_data['tgl_join'])) }} </p>
													</div>
												</div>

												<div class="form-group">
													<label for="status_emp" class="col-md-2 control-label"> Status Pegawai </label>
													<div class="col-md-8">
                                                        <p class="form-control-static"> {{ $emp_data['status_emp'] }} </p>
													</div>
												</div>

                                                <div class="form-group">
													<label class="col-md-2 control-label"> SK CPNS </label>
													<div class="col-md-2">
														<?php if ($emp_data['sk_cpns'] && $emp_data['sk_cpns'] != '') : ?>
                                                            <a target="_blank" href="{{ config('app.openfileimg') }}/{{ $emp_data['id_emp'] }}/skcpns/{{ $emp_data['sk_cpns'] }}"><p>[Unduh SK CPNS]</p></a>
                                                        <?php else : ?>
                                                            -
                                                        <?php endif ?>
													</div>
                                                    <label class="col-md-2 control-label"> Tanggal SK CPNS </label>
													<div class="col-md-4">
                                                        <p class="form-control-static"> {{ $emp_data['tmt_sk_cpns'] ? date('d-M-Y',strtotime($emp_data['tmt_sk_cpns'])) : '-' }}</p>
                                                    </div>
												</div>
                                                
                                                <div class="form-group">
													<label class="col-md-2 control-label"> SK PNS </label>
													<div class="col-md-2">
														<?php if ($emp_data['sk_pns'] && $emp_data['sk_pns'] != '') : ?>
                                                            <a target="_blank" href="{{ config('app.openfileimg') }}/{{ $emp_data['id_emp'] }}/skcpns/{{ $emp_data['sk_pns'] }}"><p>[Unduh SK PNS]</p></a>
                                                        <?php else : ?>
                                                            -
                                                        <?php endif ?>
													</div>
                                                    <label class="col-md-2 control-label"> Tanggal SK PNS </label>
													<div class="col-md-4">
                                                        <p class="form-control-static"> {{ $emp_data['tmt_sk_pns'] ? date('d-M-Y',strtotime($emp_data['tmt_sk_pns'])) : '-' }}</p>
													</div>
												</div>

												<div class="form-group">
													<label for="nip_emp" class="col-md-2 control-label"> NIP </label>
													<div class="col-md-8">
                                                        <p class="form-control-static"> {{ $emp_data['nip_emp'] ?? '-' }} </p>
													</div>
												</div>

												<div class="form-group">
													<label for="nrk_emp" class="col-md-2 control-label"> NRK </label>
													<div class="col-md-8">
                                                        <p class="form-control-static"> {{ $emp_data['nrk_emp'] ?? '-' }} </p>
													</div>
												</div>
												
												<div class="form-group">
													<label for="nm_emp" class="col-md-2 control-label"> Nama <span style="color: red; font-size: 20px;"> *</span></label>
													<div class="col-md-8">
                                                        <p class="form-control-static"> {{ $emp_data['nm_emp'] }} </p>
													</div>
												</div>

												<div class="form-group">
													<label for="nik_emp" class="col-md-2 control-label"> NIK KTP </label>
													<div class="col-md-8">
                                                        <p class="form-control-static"> {{ $emp_data['nik_emp'] ?? '-' }} </p>
													</div>
												</div>

												<div class="form-group">
													<label for="gelar" class="col-md-2 control-label"> Gelar Depan </label>
													<div class="col-md-2">
                                                        <p class="form-control-static"> {{ $emp_data['gelar_dpn'] ?? '-' }} </p>
													</div>
                                                    <label for="gelar" class="col-md-2 control-label"> Gelar Blk </label>
													<div class="col-md-4">
                                                        <p class="form-control-static"> {{ $emp_data['gelar_blk'] ?? '-' }} </p>
													</div>
												</div>

												<div class="form-group">
													<label class="col-md-2 control-label"> Jenis Kelamin </label>
													<div class="radio-list col-md-8">
														<p class="form-control-static"> {{ $emp_data['jnkel_emp'] == 'L' ? 'Laki-laki' : 'Perempuan' }} </p> 
													</div>
												</div>

												<div class="form-group">
													<label class="col-md-2 control-label"> Tempat / Tgl Lahir </label>
													<div class="col-md-4">
                                                        <p class="form-control-static"> {{ $emp_data['tempat_lahir'] ?? '-' }}, {{ $emp_data['tgl_lahir'] ? date('d/m/Y', strtotime($emp_data['tgl_lahir'])) : '' }} </p>
													</div>
												</div>

												<div class="form-group">
													<label for="idagama" class="col-md-2 control-label"> Agama </label>
													<div class="col-md-8">
                                                        <p class="form-control-static"> 
                                                            @if($emp_data['idagama'] == 'A')
                                                                Islam
                                                            @elseif($emp_data['idagama'] == 'B')
                                                                Katolik
                                                            @elseif($emp_data['idagama'] == 'C')
                                                                Protestan
                                                            @elseif($emp_data['idagama'] == 'D')
                                                                Budha
                                                            @elseif($emp_data['idagama'] == 'E')    
                                                                Hindu
                                                            @elseif($emp_data['idagama'] == 'F')
                                                                Lainnya
                                                            @elseif($emp_data['idagama'] == 'G')
                                                                Konghucu
                                                            @endif 
                                                        </p>
													</div>
												</div>

												<div class="form-group">
													<label for="alamat_emp" class="col-md-2 control-label"> Alamat </label>
													<div class="col-md-8">
                                                        <p class="form-control-static"> {{ $emp_data['alamat_emp'] ?? '-' }} </p>
													</div>
												</div>

												<div class="form-group">
													<label for="tlp_emp" class="col-md-2 control-label"> Telepon / HP </label>
													<div class="col-md-8">
                                                        <p class="form-control-static"> {{ $emp_data['tlp_emp'] ?? '-' }} </p>
													</div>
												</div>

												<div class="form-group">
													<label for="email_emp" class="col-md-2 control-label"> Email </label>
													<div class="col-md-8">
                                                        <p class="form-control-static"> {{ $emp_data['email_emp'] ?? '-' }} </p>
													</div>
												</div>

												<div class="form-group">
													<label for="status_nikah" class="col-md-2 control-label"> Status Nikah </label>
													<div class="col-md-8">
                                                        <p class="form-control-static"> {{ $emp_data['status_nikah'] ?? '-' }} </p>
													</div>
												</div>

												<div class="form-group">
													<label for="gol_darah" class="col-md-2 control-label"> Golongan Darah </label>
													<div class="col-md-8">
                                                        <p class="form-control-static"> {{ $emp_data['gol_darah'] ?? '-' }} </p>
													</div>
												</div>

												<div class="form-group">
													<label for="nm_bank" class="col-md-2 control-label"> Nama Bank </label>
													<div class="col-md-8">
                                                        <p class="form-control-static"> {{ $emp_data['nm_bank'] ?? '-' }} </p>
													</div>
												</div>

												<div class="form-group">
													<label for="cb_bank" class="col-md-2 control-label"> Cabang Bank </label>
													<div class="col-md-8">
                                                        <p class="form-control-static"> {{ $emp_data['cb_bank'] ?? '-' }} </p>
													</div>
												</div>

												<div class="form-group">
													<label for="an_bank" class="col-md-2 control-label"> Nama Rekening </label>
													<div class="col-md-8">
                                                        <p class="form-control-static"> {{ $emp_data['an_bank'] ?? '-' }} </p>
													</div>
												</div>

												<div class="form-group">
													<label for="nr_bank" class="col-md-2 control-label"> Nomor Rekening </label>
													<div class="col-md-8">
                                                        <p class="form-control-static"> {{ $emp_data['nr_bank'] ?? '-' }} </p>
													</div>
												</div>

												<div class="form-group">
													<label for="no_taspen" class="col-md-2 control-label"> Nomor Taspen </label>
													<div class="col-md-8">
                                                        <p class="form-control-static"> {{ $emp_data['no_taspen'] ?? '-' }} </p>
													</div>
												</div>

												<div class="form-group">
													<label for="npwp" class="col-md-2 control-label"> NPWP </label>
													<div class="col-md-8">
                                                        <p class="form-control-static"> {{ $emp_data['npwp'] ?? '-' }} </p>
													</div>
												</div>

												<div class="form-group">
													<label for="no_askes" class="col-md-2 control-label"> Nomor Askes </label>
													<div class="col-md-8">
                                                        <p class="form-control-static"> {{ $emp_data['no_askes'] ?? '-' }} </p>
													</div>
												</div>

												<div class="form-group">
													<label for="no_jamsos" class="col-md-2 control-label"> Nomor BPJS </label>
													<div class="col-md-8">
                                                        <p class="form-control-static"> {{ $emp_data['no_jamsos'] ?? '-' }} </p>
													</div>
												</div>
											</div>
                                            
											<a href="/portal/kepegawaian/data pegawai"><button type="button" class="m-b-20 m-t-10 btn btn-default pull-right m-r-10"> Kembali </button></a>	
										
											</form>
										</section>
										<section id="section-underline-2">
                                            <h2><b>KELUARGA</b></h2>

											@if(count($emp_kel) > 0)
											<div class="table-responsive">
												<table class="table table-hover table-bordered">
													<thead>
														<tr>
															<th>No</th>
															<th>Keluarga</th>
															<th>NIK</th>
															<th>Tgl Lahir</th>
														</tr>
													</thead>
													<tbody>
														@foreach($emp_kel as $key => $kel)
														<tr>
															<td>{{ $key+1 }}</td>
															<td>
																<strong>{{ strtoupper($kel['jns_kel']) }}</strong>
																<br>{{ ucwords(strtolower($kel['nm_kel'])) }}
															</td>
															<td>{{ $kel['nik_kel'] ?? '-' }}</td>
															<td>{{ date('d-M-Y',strtotime($kel['tgl_kel'])) ?? '-' }}</td>
														</tr>
														@endforeach
													</tbody>
												</table>
											</div>
											@endif
											<a href="/portal/kepegawaian/data pegawai"><button type="button" class="btn btn-default pull-right m-b-20 m-t-10"> Kembali </button></a>
										</section>
										<section id="section-underline-3">
											<div class="white-box">
												<h2><b>PENDIDIKAN FORMAL</b></h2>

												@if(count($emp_dik) > 0)
												<div class="table-responsive">
													<table class="table table-hover table-bordered">
														<thead>
															<tr>
																<th>No</th>
																<th>Pendidikan</th>
																<th>Program Studi</th>
																<th>Data Ijazah</th>
                                                                <th>File Ijazah</th>
															</tr>
														</thead>
														<tbody>
															@foreach($emp_dik as $key => $dik)
															<tr>
																<td>{{ $key+1 }}</td>
																<td>{{ $dik['iddik'] }}</td>
																<td>{{ $dik['nm_sek'] }}<br>
																	<span class="text-muted">{{$dik['prog_sek']}}</span>
																</td>
																<td>{{ $dik['no_sek'] }}<br>
																	<span class="text-muted">{{$dik['th_sek']}}</span>
																</td>
                                                                <td>
                                                                    <?php if ($dik['gambar'] && $dik['gambar'] != '') : ?> 
                                                                        <a target="_blank" href="{{ config('app.openfileimg') }}/{{ $id_emp }}/dik/{{ $dik['gambar'] }}">[File Ijazah]
                                                                        </a>
                                                                    <?php else : ?>
                                                                        [Tidak ada File Ijazah]
                                                                    <?php endif ?>
                                                                    <?php if ($dik['appr'] == '1') : ?> 
                                                                        <i class="fa fa-check" style="color: #2ECC40;" data-toggle="tooltip" title="Sudah Di Approve"></i>
                                                                    <?php else : ?>
                                                                        <i class="fa fa-close" style="color: #FF4136;" data-toggle="tooltip" title="Belum di approve, {{ $dik ? $dik['alasan'] : '' }}"></i>
                                                                    <?php endif ?>
                                                                </td>
															</tr>
															@endforeach
														</tbody>
													</table>
												</div>
												@endif
											</div>
											<div class="white-box">
												<h2><b>PENDIDIKAN NON-FORMAL</b></h2>

												@if(count($emp_non) > 0)
												<div class="table-responsive">
													<table class="table table-hover table-bordered">
														<thead>
															<tr>
																<th>No</th>
																<th>Nama Kegiatan</th>
																<th>Penyelenggara</th>
																<th>Nomor & Tahun</th>
																<th>File</th>
															</tr>
														</thead>
														<tbody>
															@foreach($emp_non as $key => $non)
															<tr>
																<td>{{ $key+1 }}</td>
																<td>{{ $non['nm_non'] ?? '-' }}</td>
																<td>{{ $$non['penye_non'] ?? '-' }}</td>
																<td>
																	<strong>No. {{ $non['sert_non'] }}</strong>
																	<br>Th. {{ $non['thn_non'] ?? '-' }}
																</td>
																<td>
																	<?php if ($non['gambar'] && $non['gambar'] != '') : ?> 
																		<br><a target="_blank" href="{{ config('app.openfileimg') }}/{{ $id_emp }}/non/{{ $non['gambar'] }}">[File]</a>
																	<?php else : ?>
																		<br>[Tidak ada file]
																	<?php endif ?>
																</td>
																
															</tr>
															@endforeach
														</tbody>
													</table>
												</div>
												@endif
											</div>
	
											<a href="/portal/kepegawaian/data pegawai"><button type="button" class="btn btn-default pull-right m-b-20 m-t-10"> Kembali </button></a>	
										</section>
										
										<section id="section-underline-4">
                                            <h2><b>GOLONGAN</b></h2>
											@if(count($emp_gol) > 0)
											<div class="table-responsive">
												<table class="table table-hover table-bordered">
													<thead>
														<tr>
															<th>No</th>
															<th>TMT</th>
															<th>No SK</th>
															<th>Golongan</th>
															<th>File</th>
														</tr>
													</thead>
													<tbody>
														@foreach($emp_gol as $key => $gol)
														<tr>
															<td>{{ $key+1 }}</td>
															<td>{{ date('d/M/Y', strtotime(str_replace('/', '-', $gol['tmt_gol']))) }}</td>
															<td>{{ $gol['no_sk_gol'] }}</td>
															<td>{{ $gol['idgol'] }} - {{ $gol['nm_pangkat'] }}</td>
															<td>
																<?php if ($gol['gambar'] && $gol['gambar'] != '') : ?> 
																	<a target="_blank" href="{{ config('app.openfileimg') }}/{{ $id_emp }}/gol/{{ $gol['gambar'] }}">[File SK]
																	</a>
																<?php else : ?>
																	[Tidak ada SK Gol]
																<?php endif ?>
																<?php if ($gol['appr'] == '1') : ?> 
																	<i class="fa fa-check" style="color: #2ECC40;" data-toggle="tooltip" title="Sudah Di Approve"></i>
																<?php else : ?>
																	<i class="fa fa-close" style="color: #FF4136;" data-toggle="tooltip" title="Belum di approve, {{ $gol ? $gol['alasan'] : '' }}"></i>
																<?php endif ?>
															</td>
														</tr>
														@endforeach
													</tbody>
												</table>
											</div>
											@endif
											<a href="/portal/kepegawaian/data pegawai"><button type="button" class="btn btn-default pull-right m-b-20 m-t-10"> Kembali </button></a>
										</section>
										
										<section id="section-underline-5">
											<h2><b>UNIT KERJA</b></h2>

											@if(count($emp_jab) > 0)
											<div class="table-responsive">
												<table class="table table-hover table-bordered">
													<thead>
														<tr>
															<th>No</th>
															<th>TMT</th>
															<th>Unit</th>
															{{-- <th>Jabatan</th> --}}
															<th>File</th>
														</tr>
													</thead>
													<tbody>
														@foreach($emp_jab as $key => $jab)
														<tr>
															<td>{{ $key+1 }}</td>
															<td>{{ date('d/M/Y', strtotime(str_replace('/', '-', $jab['tmt_jab']))) }}</td>
															<td>
																{{ $jab['nmunit'] }}<br>
																<span class="text-muted">{{ $jab['lokasi']['nm_lok'] }}</span>
															</td>
															{{-- <td>
																{{ $jab['jns_jab'] }}<br>
																<span class="text-muted">{{ $jab['idjab'] }}</span >
															</td> --}}
															<td>
																<span>{{ $jab['no_sk_jab'] }}</span><br>
																<?php if ($jab['gambar'] && $jab['gambar'] != '') : ?> 
																	<a target="_blank" href="{{ config('app.openfileimg') }}/{{ $id_emp }}/jab/{{ $jab['gambar'] }}">[File SK]</a>
																<?php else : ?>
																	[Tidak ada SK Jab]
																<?php endif ?>
																<?php if ($jab['appr'] == '1') : ?> 
																	<i class="fa fa-check" style="color: #2ECC40;" data-toggle="tooltip" title="Sudah Di Approve"></i>
																<?php else : ?>
																	<i class="fa fa-close" style="color: #FF4136;" data-toggle="tooltip" title="Belum di approve, {{ $jab != '' && !(is_null($jab)) ?  $jab['alasan'] : '' }}"></i>
																<?php endif ?>
															</td>
														</tr>
														@endforeach
													</tbody>
												</table>
											</div>
											@endif
											<a href="/portal/kepegawaian/data pegawai"><button type="button" class="btn btn-default pull-right m-b-20 m-t-10"> Kembali </button></a>
										</section>
										
										<section id="section-underline-6">
                                            <h2><b>HUKUMAN DISIPLIN</b></h2>

											@if(count($emp_huk) > 0)
											<div class="table-responsive">
												<table class="table table-hover table-bordered">
													<thead>
														<tr>
															<th>No</th>
															<th>Jenis</th>
															<th>Durasi</th>
															<th>Nomor & Tanggal Surat</th>
															<th>File</th>
														</tr>
													</thead>
													<tbody>
														@foreach($emp_huk as $key => $huk)
														<tr>
															<td>{{ $key+1 }}</td>
															<td>Hukuman {{ ucwords(strtolower($huk['jns_huk'])) }}</td>
															<td>{{ date('d/M/Y',strtotime($huk['tgl_mulai'])) }} - {{ date('d/M/Y',strtotime($huk['tgl_akhir'])) }}</td>
															<td>
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
															<td>
																<?php if ($huk['gambar']) : ?> 
																	<a target="_blank" href="{{ config('app.openfileimg') }}/{{ $id_emp }}/huk/{{ $huk['gambar'] }}">[Unduh File]</a>
																<?php else : ?>
																	[File tidak tersedia]
																<?php endif ?>
																<?php if ($huk['appr'] == '1') : ?> 
																	<i class="fa fa-check" style="color: #2ECC40;" data-toggle="tooltip" title="Sudah Di Approve"></i>
																<?php else : ?>
																	<i class="fa fa-close" style="color: #FF4136;" data-toggle="tooltip" title="Belum di approve, {{ $huk != '' && !(is_null($huk)) ?  $huk['alasan'] : '' }}"></i>
																<?php endif ?>
															</td>
														</tr>
														@endforeach
													</tbody>
												</table>
											</div>
											@endif
											<a href="/portal/kepegawaian/data pegawai"><button type="button" class="btn btn-default pull-right m-b-20 m-t-10"> Kembali </button></a>
										</section>

                                        <section id="section-underline-7">
                                            <h2><b>BERKAS LAINNYA</b></h2>

											@if(count($emp_files) > 0)
											<div class="table-responsive">
												<table class="table table-hover table-bordered">
													<thead>
														<tr>
															<th>No</th>
															<th>Tgl Input</th>
															<th>Nama Berkas</th>
															<th>Nomor Berkas</th>
															<th>Tahun Berkas</th>
															<th>File</th>
														</tr>
													</thead>
													<tbody>
														@foreach($emp_files as $key => $file)
														<tr>
															<td>{{ $key+1 }}</td>
															<td>{{ date('d/M/Y',strtotime($file['tgl'])) }}</td>
															<td>{{ $file['file_nama'] }}</td>
															<td>{{ $file['file_nomor'] ?? '-' }}</td>
															<td>{{ $file['file_tahun'] ?? '-' }}</td>
															<td>
                                                                <?php if ($file['file_save']) : ?> 
                                                                    <a target="_blank" href="{{ config('app.openfileimg') }}/{{ $id_emp }}/files/{{ $file['file_save'] }}">[Unduh File]</a>
                                                                <?php else : ?>
                                                                    [File tidak tersedia]
                                                                <?php endif ?>
                                                            </td>
														</tr>
														@endforeach
													</tbody>
												</table>
											</div>
											@endif
											<a href="/portal/kepegawaian/data pegawai"><button type="button" class="btn btn-default pull-right m-b-20 m-t-10"> Kembali </button></a>
										</section>

										<section id="section-underline-8">
                                            <h2><b>STATUS PEGAWAI</b></h2>
											
                                            <form class="form-horizontal">
											@csrf
												<div class="col-md-12">
													<input type="hidden" name="id_emp" value="{{ $id_emp }}">

													<div class="form-group">
														<label for="ked_emp" class="col-md-2 control-label"> Status </label>
														<div class="col-md-8">
                                                            <p class="form-control-static"> {{ $emp_data['ked_emp'] ?? '-' }} </p>
														</div>
													</div>

													<div class="form-group">
														<label for="tgl_end" class="col-md-2 control-label"> Tanggal </label>
														<div class="col-md-8">
                                                            <p class="form-control-static"> {{ date('d/m/Y') }} </p>
														</div>
													</div>
												</div>
                                                
												<a href="/portal/kepegawaian/data pegawai"><button type="button" class="m-b-20 m-t-10 btn btn-default pull-right m-r-10"> Kembali </button></a>	
											</form>
										</section>
								</div>
							</div>
						</div>
	
						<div class="panel panel-info">
							<div class="panel-heading">  
								
							</div>
						</div>
					
				</div>
			</div>

			<div id="modal-approve" class="modal fade modal-approval" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/portal/kepegawaian/form/approvepegawai" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Approve File</b></h4>
							</div>
							<div class="modal-body">
								<!-- <h4>Approve file? </h4> -->
								<div class="form-group">
									<label class="col-md-2 control-label"> Approve? </label>
									<div class="radio-list col-md-8">
										<label class="radio-inline">
											<div class="radio radio-info">
												<input type="radio" name="appr" id="appr1" value="1" data-error="Pilih salah satu" required checked>
												<label for="appr1">Ya</label> 
											</div>
										</label>
										<label class="radio-inline">
											<div class="radio radio-info">
												<input type="radio" name="appr" id="appr2" value="0">
												<label for="appr2">Tidak</label>
											</div>
										</label>
										<div class="help-block with-errors"></div>  
									</div>
								</div>
								<div class="form-group">
									<label for="alasan" class="col-md-2 control-label"> Alasan </label>
									<div class="col-md-8">
										<textarea class="form-control" name="alasan" id="alasan"></textarea>
									</div>
								</div>
								<input type="hidden" name="ids" id="modal_approve_ids">
								<input type="hidden" name="formtipe" id="modal_approve_formtipe">
								<input type="hidden" name="id_emp" id="modal_approve_id_emp">
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-info pull-right">Simpan</button>
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
	<script src="{{ ('/portal/ample/js/cbpFWTabs.js') }}"></script>
	<script type="text/javascript">
		(function () {
				[].slice.call(document.querySelectorAll('.sttabs')).forEach(function (el) {
				new CBPFWTabs(el);
			});
		})();
	</script>
	<script src="{{ ('/portal/ample/js/custom.min.js') }}"></script>
	<script src="{{ ('/portal/ample/js/validator.js') }}"></script>
	<script src="{{ ('/portal/ample/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>
	<!-- Date Picker Plugin JavaScript -->
	<script src="{{ ('/portal/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
	<script>

		$(".select2").select2();

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
        
        jQuery('.datepicker-autoclose-def').datepicker({
			autoclose: true
			, todayHighlight: false
			, format: 'dd/mm/yyyy'
		});

		jQuery('#datepicker-autoclose').datepicker({
			autoclose: true
			, todayHighlight: false
			, format: 'dd/mm/yyyy'
		});

		jQuery('#datepicker-autoclose2').datepicker({
			autoclose: true
			, todayHighlight: false
			, format: 'dd/mm/yyyy'
		});

		jQuery('#datepicker-autoclose3').datepicker({
			autoclose: true
			, todayHighlight: false
			, format: 'dd/mm/yyyy'
		});

		jQuery('#datepicker-autoclose4').datepicker({
			autoclose: true
			, todayHighlight: false
			, format: 'dd/mm/yyyy'
		});

		jQuery('#datepicker-autoclose5').datepicker({
			autoclose: true
			, todayHighlight: false
			, format: 'dd/mm/yyyy'
		});

		jQuery('#datepicker-autoclose6').datepicker({
			autoclose: true
			, todayHighlight: false
			, format: 'dd/mm/yyyy'
		});

		jQuery('#datepicker-autoclose7').datepicker({
			autoclose: true
			, todayHighlight: false
			, format: 'dd/mm/yyyy'
		});

		jQuery('#datepicker-autoclose8').datepicker({
			autoclose: true
			, todayHighlight: false
			, format: 'dd/mm/yyyy'
		});

		jQuery('#datepicker-autoclose9').datepicker({
			autoclose: true
			, todayHighlight: false
			, format: 'dd/mm/yyyy'
		});

		jQuery('#datepicker-autoclose10').datepicker({
			autoclose: true
			, todayHighlight: false
			, format: 'dd/mm/yyyy'
		});

		jQuery('#datepicker-autoclose11').datepicker({
			autoclose: true
			, todayHighlight: false
			, format: 'dd/mm/yyyy'
		});

		jQuery('#datepicker-autoclose12').datepicker({
			autoclose: true
			, todayHighlight: false
			, format: 'dd/mm/yyyy'
		});

		$('.btn-approve').on('click', function () {
			var $el = $(this);
			$("#modal_approve_ids").val($el.data('appr_ids'));
			$("#modal_approve_formtipe").val($el.data('appr_tipe'));
			$("#modal_approve_id_emp").val($el.data('appr_id_emp'));
		});

	</script>

	
@endsection