<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Form Kehadiran</title>
        <link
            rel="shortcut icon"
            type="image/x-icon"
            href="{{ '/portal/public/img/photo/bpad-logo-00.png' }}"
        />

        <link
            rel="stylesheet"
            href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
        />

        
        <!-- page CSS -->
        <link href="{{ ('/portal/public/ample/plugins/bower_components/custom-select/custom-select.css') }}" rel="stylesheet" type="text/css" />
    </head>

    <body style="background-color: #f7f7f7">
        <div class="container">
            <div
                class="row"
                style="align-items: center; display: flex; justify-content: center;"
            >
                <div class="navbar-brand">
                    <a href="{{ url('/') }}"
                        ><img
                            src="/portal/public/img/photo/bpad-logo-04b.png32"
                            alt="logo"
                            height="100"
                    /></a>
                </div>
            </div>
            <div class="row" id="">
                <div class="col-lg-2"></div>
                <div class="col-lg-8">
                    <div class="row">
                        <div
                            class="card "
                            style="
                                width: 100%;
                                margin-top: 20px;
                                margin-bottom: 10px;
                            "
                        >
                            <div class="card-header"></div>
                            <div class="card-body" style="padding: 0px;">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <h2 class="card-title" style="font-weight: bold;">{{ $form['judul'] }}</h2>
                                        <span class="text-muted">
                                            <h5>{{ $form['deskripsi'] }}</h5>
                                        </span>
                                    </li>
                                    
                                </ul>
                            </div>
                        </div>
                    </div>

                    @if($flaglewat == 1)

                    <div class="row">
                        <div
                            class="card col-xs-12 col-sm-12"
                            style="
                                width: 100%;
                                margin-bottom: 10px;
                            "
                        >
                            <div class="card-body" style="padding: 10px;">
                                <div class="form-group">
                                    <p style="font-size: 30px; text-align:center; color:red;">Maaf, form ini sudah tidak dapat diisi</p>
                                </div>
                            </div>

                        </div>
                    </div>

                    @else

                    <form method="POST" action="/portal/form/simpanform" data-toggle="validator" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div
                                class="card col-xs-12 col-sm-12"
                                style="
                                    width: 100%;
                                    margin-bottom: 10px;
                                    padding: 10px;
                                "
                            >
                                @if($form['sts'] == 2)
                                <div class="card-body">
                                    <div class="form-group">
										<label for="id_emp" class="col-md-12 control-label"><h4>Organisasi Perangkat Daerah<span style="color: red">*</span></h4> </label>
										<div class="col-md-12">
											<select class="form-control select2" name="id_emp" id="id_emp" required="" data-error="Pilih salah satu">
												<option value="<?php echo NULL; ?>">-- PILIH OPD --</option>
												@foreach($emps as $emp)
													<option <?php if(old('kolok') == $emp['kolok']): ?> selected <?php endif; ?> value="{{ $emp['kolok'] }}">[{{ $emp['kolokdagri'] }}] - {{ $emp['nalok'] }} </option>
												@endforeach
											</select>
											<div class="help-block with-errors"></div>  
										</div>
									</div>

                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="col-md-12 control-label"><h4> Status Pegawai </h4> </label>
                                            <div class="radio-list col-md-4">
                                                <label class="radio-inline">
                                                    <div class="radio radio-info">
                                                        <input type="radio" name="stat_emp" id="stat_emp1" value="1" data-error="Pilih salah satu" required="" checked="">
                                                        <label for="stat_emp1">PNS</label> 
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="radio-list col-md-4">
                                                <label class="radio-inline">
                                                    <div class="radio radio-info">
                                                        <input type="radio" name="stat_emp" id="stat_emp2" value="0" >
                                                        <label for="stat_emp2">NON-PNS</label>
                                                    </div>
                                                </label>
                                                <div class="help-block with-errors"></div>  
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
										<label for="nama" class="col-md-12 control-label"><h4>Nama<span style="color: red">*</span></h4> </label>
										<div class="col-md-12">
											<input type="text" autocomplete="off" class="form-control" required name="nama" data-error="Nama harus diisi">
											<div class="help-block with-errors"></div>  
										</div>
									</div>

                                    <div class="form-group">
										<label for="nrk" class="col-md-12 control-label"><h4>NRK</h4> </label>
										<div class="col-md-12">
											<input type="text" autocomplete="off" class="form-control" name="nrk">
											<div class="help-block with-errors"></div>  
										</div>
									</div>

                                    <div class="form-group">
										<label for="nip" class="col-md-12 control-label"><h4>NIP</h4> </label>
										<div class="col-md-12">
											<input type="text" autocomplete="off" class="form-control" name="nip">
											<div class="help-block with-errors"></div>  
										</div>
									</div>

                                    <div class="form-group">
										<label for="telp" class="col-md-12 control-label"><h4>Nomor Telp (Terintegrasi ke Whatsapp)<span style="color: red">*</span></h4> </label>
										<div class="col-md-12">
											<input type="text" autocomplete="off" class="form-control" required name="telp" data-error="Telp harus diisi">
											<div class="help-block with-errors"></div>  
										</div>
									</div>

                                    <div class="form-group">
										<label for="email" class="col-md-12 control-label"><h4>Alamat Email<span style="color: red">*</span></h4> </label>
										<div class="col-md-12">
											<input type="email" autocomplete="off" class="form-control" required name="email" data-error="Email harus diisi">
											<div class="help-block with-errors"></div>  
										</div>
									</div>
                                </div>
                                @else
                                <div class="card-body">
                                    <div class="form-group">
										<label for="id_emp" class="col-md-12 control-label"><h4>Pegawai<span style="color: red">*</span></h4> </label>
										<div class="col-md-12">
											<select class="form-control select2" name="id_emp" id="id_emp" required="" data-error="Pilih salah satu">
												<option value="<?php echo NULL; ?>">-- PILIH PEGAWAI --</option>
												@foreach($emps as $emp)
													<option <?php if(old('id_emp') == $emp['id_emp']): ?> selected <?php endif; ?> value="{{ $emp['id_emp'] }}">[{{ $emp['nrk_emp'] }}] - {{ $emp['nm_emp'] }} </option>
												@endforeach
											</select>
											<div class="help-block with-errors"></div>  
										</div>
									</div>
                                </div>
                                @endif

                                <div class="card-body">
                                    <div class="form-group">
										<label class="col-md-12 control-label"><h4> Apakah anda mengikuti kegiatan ini? </h4> </label>
										<div class="radio-list col-md-2">
											<label class="radio-inline">
												<div class="radio radio-info">
													<input type="radio" name="tampil" id="tampil1" value="1" data-error="Pilih salah satu" required="" checked="">
													<label for="tampil1">Ya</label> 
												</div>
											</label>
                                        </div>
                                        <div class="radio-list col-md-2">
											<label class="radio-inline">
												<div class="radio radio-info">
													<input type="radio" name="tampil" id="tampil2" value="0" >
													<label for="tampil2">Tidak</label>
												</div>
											</label>
											<div class="help-block with-errors"></div>  
										</div>
									</div>
                                </div>

                                @if($form['allow_foto'] != '0')
                                <div class="card-body">
                                    <div class="form-group">
										<label for="id_emp" class="col-md-12 control-label"><h4>Foto</h4><span class="text-muted">JPG, JPEG, GIF, PNG</span> </label>
										<div class="col-md-12">
                                            <input type="file" class="form-control" id="fotohadir" name="fotohadir" accept="image/png, image/gif, image/jpeg, image/jpg">
											<div class="help-block with-errors"></div>  
										</div>
									</div>
                                </div>
                                @endif
                                
                            </div>
                            <input type="hidden" name="form" value="{{ $form['no_form'] }}">
                        </div>
                        <div class="row" style="justify-content: right; display: flex; margin-bottom: 100px;">
                            <button class="btn btn-success" style="font-size: 20px;">SIMPAN</button>
                        </div>
                    </form>

                    @endif

                </div>
            </div>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

        
    	<script src="{{ ('/portal/public/ample/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>
        <script>
            $(function () {

                $(".select2").select2();

            });
        </script>
    </body>
</html>
