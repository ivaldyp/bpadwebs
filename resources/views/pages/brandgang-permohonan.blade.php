@extends('layouts.master')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12 text-center">
			<!-- <h1 class="title"><span style="background: linear-gradient(to right, #8C0606 0%, #FF0000 50%, #8C0606 100%); -webkit-background-clip: text;-webkit-text-fill-color: transparent; font-size: 64px">PROFIL BPAD</span></h1> -->
			<h1 class="title" style="font-family: 'Century Gothic'; font-size: 64px; padding-top: 50px;"><span style="color: #006cb8; font-weight: bold">PERMOHONAN</span> BRANDGANG</h1>
		</div>
	</div>
</div>
<!-- SECTION -->
<div class="section">
	<!-- container -->
	<div class="container">
		<!-- row -->
		<div class="row">
			<!-- MAIN -->
			<main id="main" class="col-md-12">
				<!-- article -->
				<div class="article">
					<!-- article content -->
					<div class="article-content">
			            <p style="text-align: justify; text-justify: inter-word;">
                            Tanah Brandgang adalah tanah berupa gang dengan lebar rata-rata 2 meter yang dipergunakan sebagai prasarana umum. Dahulunya tanah brandgang berfungsi sebagai akses alat pemadam kebakaran apabila terjadi kebakaran sehingga memudahkan tim pemadam kebakaran dalam menjangkau rumah-rumah warga.
                            <br><br>
                            Ketentuan pemanfaatan bekas tanah brandgang yang tidak berfungsi lagi sebagai tanah brandgang diatur dalam <b>Peraturan Gubernur Nomor 38 Tahun 2013</b> tentang Perubahan Atas Keputusan Gubernur Nomor 125 Tahun 2002 tentang Ketentuan Pemanfaatan Bekas Tanah Brandgang Yang Tidak Berfungsi Lagi Sebagai Tanah Brandgang.
                            <br><br>
                            Tahapan Pemindahtanganan Bekas Tanah Brandgang, adalah sebagai berikut:
                            <br><br>
                            <img src="{{ asset('img/brandgang/permohonan-brandgang.png') }}" width="100%">
                            <br><br>
                            Tahapan Permohonan dan Monitoring Pemindahtanganan Bekas Tanah Brandgang juga dapat dilihat melalui video dibawah ini:
                            <br><br>
                            <div class="col-md-12" style="display: flex;
                            justify-content:space-between;">
                                <div class="col-md-6">
                                    <iframe width="100%" height="350" src="https://www.youtube.com/embed/fJXS0WTZ0Ao" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                </div>
                                <div class="col-md-6">
                                    <iframe width="100%" height="350" src="https://www.youtube.com/embed/E59s3W_BH5M" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                </div>
                            </div>
                            <br><br>
                            Form permohonan brandgang dapat mulai diisi melalui tombol dibawah ini
                        </p>
			            <br>

			            <a href="https://aset.jakarta.go.id/brandgang/index.aspx?id=permohonan/" target="_blank"><button class="text-center btn btn-info btn-block btn-lg">Isi Form Permohonan Brandgang</button></a>

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