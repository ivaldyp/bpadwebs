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

                    <form method="POST" action="/portal/form/simpanform">
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
                            </div>
                            <input type="hidden" name="form" value="{{ $form['no_form'] }}">
                        </div>
                        <div class="row" style="justify-content: right; display: flex;">
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
