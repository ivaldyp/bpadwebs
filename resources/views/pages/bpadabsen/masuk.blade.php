@extends('layouts.master-nameless')

@section('content')
<div class="container">
	<div class="row" style="padding-top:50px">
		<div class="col-md-12 text-center">
			<!-- <h1 class="title"><span style="background: linear-gradient(to right, #8C0606 0%, #FF0000 50%, #8C0606 100%); -webkit-background-clip: text;-webkit-text-fill-color: transparent; font-size: 64px">PROFIL BPAD</span></h1> -->
			<h1 class="title" style="font-family: 'Century Gothic'; font-size: 64px;"><span style="color: #006cb8; font-weight: bold">ABSENSI e-SIAPPE</h1>
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
            <div class="col-md-2"></div>
			<main id="main" class="col-md-8">
				<!-- article -->
				<div class="article">
					<!-- article content -->
					<div class="article-content row">
						<form class="form-horizontal" method="POST" action="/portal/esiappe/foto" data-toggle="validator">
                            @csrf

                            <div class="form-group">
                                <div class="col-md-6">
                                    <input style="align-content: center;" class=" input text-center" type="text" name="username" placeholder="Masukkan username" autocomplete="off" required>
                                </div>
                                <div class="col-md-6">
                                    <input style="align-content: center;" class=" input text-center" type="password" name="password" placeholder="Masukkan password" autocomplete="off" required>
                                </div>
                                <button class="m-t-30 b-t-30 primary-button pull-right" type="submit">Submit</button>
                            </div>
                            
                        </form>
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