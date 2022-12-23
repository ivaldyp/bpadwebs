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
			<main id="main" class="col-lg-12">
				<!-- article -->
				<div class="article">
					<!-- article content -->
					<div class="article-content">
			            
                        <button class="accordion"><span style="font-weight: bold;">Laporan Keuangan BPAD</span></button>
                        <div class="panel">
                            <ul style="padding: 18px">
                                <li><a target="_blank" href="https://drive.google.com/file/d/1Cs02OOps4bXc14JBLmmm08gmrlGnCHYx/view?usp=share_link">Laporan Keuangan BPAD Tahun 2021</a></li>
                                <li><a target="_blank" href="#">Laporan Keuangan BPAD Tahun 2020</a></li>
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