@extends('layouts.master')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12 text-center">
			<!-- <h1 class="title"><span style="background: linear-gradient(to right, #8C0606 0%, #FF0000 50%, #8C0606 100%); -webkit-background-clip: text;-webkit-text-fill-color: transparent; font-size: 64px">PROFIL BPAD</span></h1> -->
			<h1 class="title" style="font-family: 'Century Gothic'; font-size: 50px; margin-top: 50px;"><span style="color: #006cb8; font-weight: bold">INFORMASI</span> PUBLIK</h1>
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
			<main id="main" class="col-lg-8 col-lg-offset-2">
				<!-- article -->
				<div class="article">
					<!-- article content -->
					<div class="article-content">
			            
                        <!-- 1 -->
                        <button class="accordion"><span style="font-weight: bold;">Profil PPID</span></button>
                        <div class="panel">
                            <p><a target="_blank" href="https://bpad.jakarta.go.id/portal/ppid/profil">Halaman Profil PPID</a></p>
                        </div>

                        <!-- 2 -->
                        <button class="accordion"><span style="font-weight: bold;">Visi Misi</span></button>
                        <div class="panel">
                            <p><a target="_blank" href="https://bpad.jakarta.go.id/portal/profil/visimisi">Halaman Visi & Misi BPAD</a></p>
                        </div>

                        <!-- 3 -->
                        <button class="accordion"><span style="font-weight: bold;">Tugas & Fungsi</span></button>
                        <div class="panel">
                            <p><a target="_blank" href="https://bpad.jakarta.go.id/portal/profil/tupoksi">Halaman Tugas & Fungsi BPAD</a></p>
                        </div>

                        <!-- 4 -->
                        <button class="accordion"><span style="font-weight: bold;">Struktur Organisasi</span></button>
                        <div class="panel">
                            <p><a target="_blank" href="https://bpad.jakarta.go.id/portal/profil/struktur">Halaman Struktur Organisasi BPAD</a></p>
                        </div>
                        
                        <!-- 5 -->
                        <button class="accordion"><span style="font-weight: bold;">Profil Pejabat</span></button>
                        <div class="panel">
                            <p><a target="_blank" href="https://bpad.jakarta.go.id/portal/profil/profilpejabat">Halaman Profil Pejabat BPAD</a></p>
                        </div>

                        <!-- 6 -->
                        <button class="accordion"><span style="font-weight: bold;">Daftar Pegawai BPAD</span></button>
                        <div class="panel">
                            <p><a target="_blank" href="https://drive.google.com/file/d/1LG7lHx2BER_3-L8kknXBTFczkF_tPrGN/view?usp=share_link">Unduh Daftar Pegawai BPAD</a></p>
                        </div>

                        <!-- 7 -->
                        <button class="accordion"><span style="font-weight: bold;">Program dan Kegiatan BPAD</span></button>
                        <div class="panel">
                            <p><a target="_blank" href="https://publik.bapedadki.net/ringkasan_skpd/981/home">Lihat Program dan Kegiatan BPAD</a></p>
                        </div>

                        <!-- 9 -->
                        <button class="accordion"><span style="font-weight: bold;">Laporan Akuntabilitas Kinerja</span></button>
                        <div class="panel">
                            <p><a target="_blank" href="https://ppid.jakarta.go.id/laporan-kinerja-instansi-pemerintah">Lihat Laporan Akuntabilitas Kinerja</a></p>
                        </div>

                        <!-- 10 -->
                        <button class="accordion"><span style="font-weight: bold;">Laporan Keuangan BPAD</span></button>
                        <div class="panel">
                            <ul>
                                <li><a target="_blank" href="https://drive.google.com/file/d/1AQb2L-eSnTztauITrReqx2XPY7rz-Bv6/view?usp=sharing">Laporan Keuangan BPAD Tahun 2022</a></li>
                                <li><a target="_blank" href="https://drive.google.com/file/d/1Cs02OOps4bXc14JBLmmm08gmrlGnCHYx/view?usp=share_link">Laporan Keuangan BPAD Tahun 2021</a></li>
                            </ul>
                        </div>

                        <!-- 11 -->
                        <button class="accordion"><span style="font-weight: bold;">Rencana Kerja dan Anggaran</span></button>
                        <div class="panel">
                            <p><a target="_blank" href="https://publik.bapedadki.net/ringkasan_skpd/981/home">Tahun Anggaran 2023</a></p>
                        </div>
                        
                        <!-- 32 -->
                        <button class="accordion"><span style="font-weight: bold;">Surat Keputusan Penetapan PPID BPAD</span></button>
                        <div class="panel">
                            <ul>
                                <li><a target="_blank" href="https://bpad.jakarta.go.id/portal/publicfile/produkhukum/SK%20PPID%20Tahun%202023.pdf">Unduh SK PPID BPAD Tahun 2023</a></li>
                                <li><a target="_blank" href="https://bpad.jakarta.go.id/portal/publicfile/produkhukum/SK%20PPID%20Tahun%202022.pdf">Unduh SK PPID BPAD Tahun 2022</a></li>
                            </ul>
                        </div>

                        <!-- 51 -->
                        <button class="accordion"><span style="font-weight: bold;">Media Sosial BPAD</span></button>
                        <div class="panel">
                            <ul>
                                <li><a target="_blank" href="https://twitter.com/BPAD_Jakarta">Twitter BPAD</a></li>
                                <li><a target="_blank" href="https://www.instagram.com/bpad_jakarta/">Instagram BPAD</a></li>
                                <li><a target="_blank" href="https://www.youtube.com/@asetbpad">Youtube BPAD</a></li>
                            </ul>
                        </div>

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
<script>
    var acc = document.getElementsByClassName("accordion");
    var i;
    
    for (i = 0; i < acc.length; i++) {
      acc[i].addEventListener("click", function() {
        this.classList.toggle("active");
        var panel = this.nextElementSibling;
        if (panel.style.maxHeight) {
          panel.style.maxHeight = null;
        } else {
          panel.style.maxHeight = panel.scrollHeight + "px";
        } 
      });
    }
</script>
@endsection