@extends('layouts.master')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-lg-12 text-center">
			<!-- <h1 class="title"><span style="background: linear-gradient(to right, #8C0606 0%, #FF0000 50%, #8C0606 100%); -webkit-background-clip: text;-webkit-text-fill-color: transparent; font-size: 64px">PROFIL BPAD</span></h1> -->
			<h1 class="title" style="font-family: 'Century Gothic'; font-size: 50px; margin-top: 50px;"><span style="color: #006cb8; font-weight: bold">PERMOHONAN</span> INFORMASI</h1>
		</div>
	</div>
</div>
<!-- SECTION -->
<div class="section">
	<!-- container -->
	<div class="container">
        <div class="row">
            <div class="col-sm-12">
                @if(Session::has('message'))
                    <div class="alert <?php if(Session::get('msg_num') == 1) { ?>alert-success<?php } else { ?>alert-danger<?php } ?> alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="color: white;">&times;</button>{{ Session::get('message') }}</div>
                @endif
            </div>
        </div>
		<!-- row -->
		<div class="row">
			<!-- MAIN -->
			<main id="main" class="col-lg-6 col-lg-offset-3">
				<!-- article -->
				<div class="article">
					<!-- article content -->
					<div class="article-content">
						<form enctype="multipart/form-data" method="POST" action="{{ route('ppid.saveform') }}" data-toggle="validator">
							@csrf 
                                <div class="form-group">
                                    <label for="ppid_nama">Nama</label>
                                    <input required autocomplete="off" type="text" class="form-control" value="{{ old('ppid_nama') }}" name="ppid_nama" id="ppid_nama">
                                </div>
                                <div class="form-group">
                                    <label for="ppid_identitas">Nomor Induk Kependudukan (KTP)</label>
                                    <input required autocomplete="off" type="number" class="form-control" value="{{ old('ppid_identitas') }}" name="ppid_identitas" id="ppid_identitas">
                                </div>
                                <div class="form-group">
                                    <label for="ppid_identitas_file">Unggah File Identitas (KTP) <br><span class="text-muted" style="font-size: 12px;">Filetype PDF/JPG/JPEG/PNG - MaxSize 2MB</span> </label>
                                    <input required type="file" class="form-control" value="{{ old('ppid_identitas_file') }}" name="ppid_identitas_file" id="ppid_identitas_file" accept="application/pdf, image/*">
                                </div>
                                <div class="form-group">
                                    <label for="ppid_email">Email</label>
                                    <input required autocomplete="off" type="email" class="form-control" value="{{ old('ppid_email') }}" name="ppid_email" id="ppid_email">
                                </div>  
                                <div class="form-group">
                                    <label for="ppid_telp">Telepon</label>
                                    <input required autocomplete="off" type="number" class="form-control" value="{{ old('ppid_telp') }}" name="ppid_telp" id="ppid_telp">
                                </div>
                                <div class="form-group">
                                    <label for="ppid_alamat">Alamat</label>
                                    <textarea required autocomplete="off" class="form-control" name="ppid_alamat" id="ppid_alamat" rows="3">{{ old('ppid_alamat') }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="ppid_informasi">Informasi yang Dibutuhkan</label>
                                    <textarea required autocomplete="off" class="form-control" name="ppid_informasi" id="ppid_informasi" rows="3">{{ old('ppid_informasi') }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="ppid_tujuan">Tujuan Penggunaan Informasi</label>
                                    <textarea required autocomplete="off" class="form-control" name="ppid_tujuan" id="ppid_tujuan" rows="3">{{ old('ppid_tujuan') }}</textarea>
                                </div>
                            </div>
							<button type="submit" class="btn-lg  btn-primary pull-right">Kirim</button>
                        </form>
			            <br>
					</div>
					<!-- /article content -->
				</div>					
			</main>
			<!-- /MAIN -->
		</div>
		<!-- /row -->
	</div>
	<!-- /container -->
</div>
<!-- /SECTION -->

@endsection