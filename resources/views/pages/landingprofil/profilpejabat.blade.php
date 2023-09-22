@extends('layouts.master')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12 text-center">
			<!-- <h1 class="title"><span style="background: linear-gradient(to right, #8C0606 0%, #FF0000 50%, #8C0606 100%); -webkit-background-clip: text;-webkit-text-fill-color: transparent; font-size: 64px">PROFIL BPAD</span></h1> -->
			<h1 class="title" style="font-family: 'Century Gothic'; font-size: 64px"><span style="color: #006cb8; font-weight: bold">PROFIL</span> PEJABAT</h1>
		</div>
	</div>
</div>
<!-- SECTION -->
<div class="section">
	<!-- container -->
	<div class="container">
		<!-- row ESELON II -->
		<div class="row">
            <!-- section title -->
            <div class="col-md-12">
                <div class="section-title">
                    <h2 class="title" style="font-family: 'Century Gothic';"><span style="color: #006cb8; font-weight: bold;">ESELON</span> II</h2>
                    <!-- <p class="sub-title">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p> -->
                </div>
            </div>
            <!-- section title -->
			<div class="col-md-4 col-md-offset-4">
                <div class="number number-modal" data-toggle="modal" data-target="#modal-es" data-kd_unit="{{ $es2['id_emp'] }}">
                    @if( file_exists(config('app.savefileimg') . "\\" . $es2['id_emp'] . "\\pejabat\\" . $es2['foto_pejabat']) ) 
                    <img src="{{ config('app.openfileimg') }}/{{ $es2['id_emp'] }}/pejabat/{{ $es2['foto_pejabat'] }}" style="width: 75%" alt="img" class="thumb-lg img-circle">
                    @elseif(file_exists(config('app.savefileimg') . "\\" . $es2['id_emp'] . "\\profil\\" . $es2['foto']))
                    <img src="{{ config('app.openfileimg') }}/{{ $es2['id_emp'] }}/profil/{{ $es2['foto'] }}" style="width: 65%" alt="img">
                    @else
                    <img src="{{ config('app.openfileimgdefault') }}" style="width: 65%" alt="img">
                    @endif
                    <!-- <i class="fa fa-smile-o"></i> -->
                    <h4>{{ strtoupper($es2['nm_emp']) }} <br> <span class="text-muted">{{ $es2['notes'] }}</span> </h4>
                    <!-- <span>eDokumen</span> -->
                </div>
            </div>
		</div>
		<!-- /row ESELON II -->

        <hr>

        <!-- row ESELON III -->
		<div class="row">
            <!-- section title -->
            <div class="col-md-12">
                <div class="section-title">
                    <h2 class="title" style="font-family: 'Century Gothic';"><span style="color: #006cb8; font-weight: bold;">ESELON</span> III</h2>
                    <!-- <p class="sub-title">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p> -->
                </div>
            </div>
            <!-- section title -->
            <div class="row"> 
                @foreach($es3 as $data)
                @if( $loop->index % 4 == 0)
                </div>
                <div class="row">
                @endif
                <div class="col-md-3">
                    <div class="number number-modal" data-toggle="modal" data-target="#modal-es" data-kd_unit="{{ $data['id_emp'] }}">
                        @if( file_exists(config('app.savefileimg') . "\\" . $data['id_emp'] . "\\pejabat\\" . $data['foto_pejabat']) ) 
                        <img src="{{ config('app.openfileimg') }}/{{ $data['id_emp'] }}/pejabat/{{ $data['foto_pejabat'] }}" style="width: 75%" alt="img" class="thumb-lg img-circle">
                        @elseif(file_exists(config('app.savefileimg') . "\\" . $data['id_emp'] . "\\profil\\" . $data['foto']))
                        <img src="{{ config('app.openfileimg') }}/{{ $data['id_emp'] }}/profil/{{ $data['foto'] }}" style="width: 65%" alt="img">
                        @else
                        <img src="{{ config('app.openfileimgdefault') }}" style="width: 65%" alt="img">
                        @endif
                        <!-- <i class="fa fa-smile-o"></i> -->
                        <div class="text-left">
                            <h4>{{ strtoupper($data['nm_emp']) }} </h4><br> <span class="text-muted">{{ $data['notes'] }}</span> 
                        </div>
                        <!-- <span>eDokumen</span> -->
                    </div>
                </div>
                @endforeach
            </div>
		</div>
		<!-- /row ESELON III -->

        <hr>

        <!-- row ESELON IV -->
		<div class="row">
            <!-- section title -->
            <div class="col-md-12">
                <div class="section-title">
                    <h2 class="title" style="font-family: 'Century Gothic';"><span style="color: #006cb8; font-weight: bold;">ESELON</span> IV</h2>
                    <!-- <p class="sub-title">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p> -->
                </div>
            </div>
            <!-- section title -->
            @php 
            $bidangnow = $es4[0]['nm_bidang'];
            @endphp

            <div class="row"> 
                <h3>{{ $bidangnow }}</h3>
                @foreach($es4 as $data)

                @if( $data['nm_bidang'] != $bidangnow)
                @php 
                $bidangnow = $data['nm_bidang'];
                @endphp
                </div>
                <div class="row">
                <h3>{{ $bidangnow }}</h3>
                @endif
                
                <div class="col-md-2">
                    <div class="number number-modal" data-toggle="modal" data-target="#modal-es" data-kd_unit="{{ $data['id_emp'] }}"> 
                        @if( file_exists(config('app.savefileimg') . "\\" . $data['id_emp'] . "\\pejabat\\" . $data['foto_pejabat']) ) 
                        <img src="{{ config('app.openfileimg') }}/{{ $data['id_emp'] }}/pejabat/{{ $data['foto_pejabat'] }}" style="width: 75%" alt="img" class="thumb-lg img-circle">
                        @elseif(file_exists(config('app.savefileimg') . "\\" . $data['id_emp'] . "\\profil\\" . $data['foto']))
                        <img src="{{ config('app.openfileimg') }}/{{ $data['id_emp'] }}/profil/{{ $data['foto'] }}" style="width: 65%" alt="img">
                        @else
                        <img src="{{ config('app.openfileimgdefault') }}" style="width: 65%" alt="img">
                        @endif
                        <!-- <i class="fa fa-smile-o"></i> -->
                        <div class="text-left">
                            <h4>{{ strtoupper($data['nm_emp']) }} </h4><br> <span class="text-muted">{{ $data['notes'] }}</span> 
                        </div>
                        <!-- <span>eDokumen</span> -->
                    </div>
                </div>
                @endforeach
            </div>
		</div>
		<!-- /row ESELON IV -->
	</div>
	<!-- /container -->

    <!-- Modal -->
    <div class="modal fade" id="modal-es" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" id="div-header-es">

                </div>
                <div class="modal-body" id="div-body-es">
                    
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /SECTION -->

<script>
    $(document).ready(function() {
        $('.number-modal').on('click', function () {
            $(' #div-header-es ').empty();
            $(' #div-body-es ').empty();
            
            $(' #div-body-es ').append(
                "<h2><i class='fa fa-refresh fa-spin justify-center'></i></h2>"
            );

            var $el = $(this);
            var kd_unit = $el.data('kd_unit');
            
            $.ajax({
                method: "GET",
                url: "{{ route('profil.gethistorypejabat') }}",
                data: { 
                    kd_unit : kd_unit,
                },
            }).done(function( data ) {
                $(' #div-header-es ').empty();
                $(' #div-body-es ').empty();
                
                $(' #div-header-es ').append(
                    "<button type='button' class='close' data-dismiss='modal'>&times;</button>"+
                    "<h4 class='modal-title'>"+data.nm_emp+"</h4><br>"+
                    "<span class='text-muted'>"+ data.nm_bidang + " - " + (data.notes).toUpperCase()+"</span>"
                );
                $(' #div-body-es ').append(
                    "<h3>Sejarah Singkat</h3>"+
                    "<p>"+data.sejarah_pejabat+"</p>"
                );
            }).fail(function( res, exception ) {
                $(' #div-header-es ').empty();
                $(' #div-body-es ').empty();

                $(' #div-body-es ').append(
                    "<td colspan='6' class='text-center'>"+
                        "<h2>"+res.status+" - "+res.statusText+"</h2>"+
                    "</td>"
                );
            });
        });
    });
</script>

@endsection