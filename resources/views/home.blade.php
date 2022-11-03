@extends('layouts.masterhome')

@section('css')
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/portal/public/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<!-- Menu CSS -->
	<link href="{{ ('/portal/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
	<!-- animation CSS -->
	<link href="{{ ('/portal/public/ample/css/animate.css') }}" rel="stylesheet">
	<!-- Custom CSS -->
	<link href="{{ ('/portal/public/ample/css/style.css') }}" rel="stylesheet">
	<!-- color CSS -->
	<link href="{{ ('/portal/public/ample/css/colors/purple-dark.css') }}" id="theme" rel="stylesheet">
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
@endsection

<!-- /////////////////////////////////////////////////////////////// -->

@section('content')
	<style type="text/css">
		#li_portal a.active {
			background:white;
		}
	</style>
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
							$link = explode("/", url()->full());
							if (count($link) == 5) {
								?> 
									<li class="active"> {{ ucwords(explode("?", $link[4])[0]) }} </li>
								<?php
							} elseif (count($link) == 6) {
								?> 
									<li class="active"> {{ ucwords($link[4]) }} </li>
									<li class="active"> {{ ucwords($link[5]) }} </li>
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
					<div class="white-box">
						<div class="row row-in">
							<a href="/portal/kepegawaian/data%20pegawai">
							<div class="col-md-4 col-sm-4 row-in-br">
								<ul class="col-in">
									<li>
										<span class="circle circle-md bg-info"><i class="ti-user"></i></span>
									</li>
									<li class="col-last"><h3 class="counter text-right m-t-15">{{ $countpegawai['total'] }}</h3></li>
									<li class="col-middle">
										<h4>Pegawai</h4>
									</li>
									
								</ul>
							</div>
							</a>
							@if(isset($_SESSION['user_data']['id_emp']))
							<a href="/portal/disposisi/disposisi">
							<div class="col-md-4 col-sm-4 row-in-br">
								<ul class="col-in">
									<li>
										<span class="circle circle-md bg-danger"><i class="ti-email"></i></span>
									</li>
									<li class="col-last"><h3 class="counter text-right m-t-15">{{ $countdisp['total'] }}</h3></li>
									<li class="col-middle">
										<h4>Disposisi</h4>
									</li>
									
								</ul>
							</div>
							</a>
							@endif

							<a href="/portal/cms/content">
							<div class="col-md-4 col-sm-4 row-in-br">
								<ul class="col-in">
									<li>
										<span class="circle circle-md bg-success"><i class="ti-comment"></i></span>
									</li>
									<li class="col-last">
										<h3 class="counter text-right m-t-15">{{ $countcontent['total'] }}</h3>
									</li>
									<li class="col-middle">
										<h4>Konten</h4>
									</li>
								</ul>
							</div>
							</a>
							
							<!-- <div class="col-md-3 col-sm-6">
								<ul class="col-in">
									<li>
										<span class="circle circle-md bg-warning"><i class="fa fa-dollar"></i></span>
									</li>
									<li class="col-last"><h3 class="counter text-right m-t-15">83</h3></li>
									<li class="col-middle">
										<h4>Net Earnings</h4>
									</li>
								</ul>
							</div> --> 
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="row">
						<div class="col-lg-8">
							<div class="panel panel-info">
								<div class="panel-heading">Organisasi 
									<div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a> </div>
								</div>
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<div>
											@if(isset(Auth::user()->usname) || $_SESSION['user_data']['idunit'] == '01')
												<ul id="tree1">

												@foreach($employees as $key => $emp)
													{{-- @if(substr($emp['nm_emp'], 0, 3) != 'Plt')
													@endif --}}
                                                    <li>
                                                    @if(strlen($emp['idunit']) < 10)
                                                    {{ $emp['nm_unit'] }}<br>
                                                    @endif
                                                    <span class="text-muted">{{ ucwords(strtolower($emp['nm_emp'])) }}</span>

                                                    @if(isset($employees[$key+1]))
                                                    @if(strlen($employees[$key+1]['idunit']) < strlen($emp['idunit']) )
                                                    </ul>
                                                    </li>
                                                    @endif
                                                    @endif

                                                    @if(isset($employees[$key+1]))
                                                    @if(strlen($employees[$key+1]['idunit']) > strlen($emp['idunit']) )
                                                    <ul>
                                                    @endif
                                                    @endif
												@endforeach

												</ul>
											@endif

											@if(strlen($_SESSION['user_data']['idunit']) < 10 && strlen($_SESSION['user_data']['idunit']) > 2)
												<ul id="tree1">

												@foreach($employees as $key => $emp)
													{{-- @if(substr($emp['nm_emp'], 0, 3) != 'Plt')
													@endif --}}
                                                    <li>
                                                    @if(strlen($emp['idunit']) < 10)
                                                    {{ $emp['nm_unit'] }}<br>
                                                    @endif
                                                    <span class="text-muted">{{ ucwords(strtolower($emp['nm_emp'])) }}</span>

                                                    @if($emp['child'] == 1)
                                                    <ul>
                                                    @endif

                                                    @if(isset($employees[$key+1]))
                                                    @if(strlen($employees[$key+1]['idunit']) < strlen($emp['idunit']) )
                                                    </ul>
                                                    </li>
                                                    @endif
                                                    @endif
												@endforeach
												</ul>
											@endif

											@if(strlen($_SESSION['user_data']['idunit']) == 10)
												<ul id="tree1">
													<li>{{ $employees[0]['nm_unit'] }}
														<ul>
															@foreach($employees as $key => $emp)
															<li>{{ ucwords(strtolower($emp['nm_emp'])) }}</li>
															@endforeach
														</ul>
													</li>
												</ul>
											@endif
												
										</div>
									</div>
								</div>
							</div>	
						</div>
						<div class="col-md-4">
							<div class="panel panel-info">
								<div class="panel-heading">Info
									<div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a> </div>
								</div>
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<ul class="nav customtab nav-tabs" role="tablist">
											<!-- <li role="presentation" class="active"><a href="#agenda" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true"><span class="visible-xs"><i class="ti-home"></i></span><span class="hidden-xs"> Agenda</span></a></li> -->
											<!-- <li role="presentation" class=""><a href="#berita" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs">Berita</span></a></li> -->
											<li role="presentation" class="active"><a href="#ulangtahun" aria-controls="messages" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-email"></i></span> <span class="hidden-xs">Ulang Tahun</span></a></li>
											<li role="presentation" class=""><a href="#pensiun" aria-controls="settings" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-settings"></i></span> <span class="hidden-xs">Pensiun</span></a></li>
										</ul>
										<div class="tab-content">
											<div role="tabpanel" class="tab-pane fade in" id="agenda">
												@foreach($agendas as $agenda)
												{{ date('d-M-Y', strtotime(str_replace('/', '-', $agenda['dtanggal']))) }} oleh {{ $agenda['an'] }}
												<br>
												{{ $agenda['ddesk'] }}
												<br>
												<a target="_blank" href="{{ config('app.openfileagenda') }}/{{ $agenda['dfile'] }}"><i class="fa fa-download"></i> Download File </a>
												<hr>
												@endforeach
												{{ $agendas->onEachSide(2)->links() }}
												<div class="clearfix"></div>
											</div>
											<div role="tabpanel" class="tab-pane fade in" id="berita">
												@foreach($beritas as $berita)
												<div class="panel-heading panel-default">{{ date('d-M-Y', strtotime(str_replace('/', '-', $berita['tanggal']))) }} oleh {{ $berita['an'] }}</div>
												
												<br>
												{!! html_entity_decode($berita['isi']) !!}
												<br>
												<hr>
												@endforeach
												{{ $beritas->onEachSide(2)->links() }}
												<div class="clearfix"></div>
											</div>
											<div role="tabpanel" class="tab-pane fade active in" id="ulangtahun">
												<h4>Kemarin:</h4>
                                                <span class='text-center' id="loading-ultah-kemarin">
                                                    <h2><i class='fa fa-refresh fa-spin justify-center'></i></h2>
                                                </span>
                                                <ol id="body-ultah-kemarin"></ol>
											
												<hr>

												<h4>Hari Ini:</h4>
												<span class='text-center' id="loading-ultah-today">
                                                    <h2><i class='fa fa-refresh fa-spin justify-center'></i></h2>
                                                </span>
                                                <ol id="body-ultah-today"></ol>

												<hr>

												<h4>Besok:</h4>
												<span class='text-center' id="loading-ultah-besok">
                                                    <h2><i class='fa fa-refresh fa-spin justify-center'></i></h2>
                                                </span>
                                                <ol id="body-ultah-besok"></ol>

												<hr>

											</div>
											<div role="tabpanel" class="tab-pane fade in" id="pensiun">
                                                <button class="fcbtn btn btn-outline btn-success btn-1d btn-excel-pensiun"  type="button" data-toggle="modal" data-target="#modal-tambah-pensiun">Excel</button>
												<h4>Bulan ini:</h4>
												<span class='text-center' id="loading-pensiun-now">
                                                    <h2><i class='fa fa-refresh fa-spin justify-center'></i></h2>
                                                </span>
                                                <ol id="body-pensiun-now"></ol>

												<hr>

												<h4>Bulan depan:</h4>
												<span class='text-center' id="loading-pensiun-min1">
                                                    <h2><i class='fa fa-refresh fa-spin justify-center'></i></h2>
                                                </span>
                                                <ol id="body-pensiun-min1"></ol>

												<hr>


												<h4>2 - 6 Bulan kedepan:</h4>
												<span class='text-center' id="loading-pensiun-min6">
                                                    <h2><i class='fa fa-refresh fa-spin justify-center'></i></h2>
                                                </span>
                                                <ol id="body-pensiun-min6"></ol>

												<hr>

											</div>
											<!-- <div role="tabpanel" class="tab-pane fade in" id="pensiun"></div> -->
										</div>
										
									</div>
								</div>
							</div>	
						</div>
					</div>
				</div>
			</div>
            <div id="modal-tambah-pensiun" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="GET" action="{{ route('kepegawaian.report.excelpensiun') }}" class="form-horizontal" data-toggle="validator">
							<div class="modal-header">
								<h4 class="modal-title"><b>Pilih Tahun Pensiun</b></h4>
							</div>
							<div class="modal-body">
								<div class="form-group">
									<label for="tahun_pensiun" class="col-md-2 control-label"><span style="color: red">*</span> Tahun </label>
									<div class="col-md-8">
										<select class="form-control select2" name="tahun_pensiun" id="tahun_pensiun" required>
                                            @for($i=2017; $i<=2055; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
										</select>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-success pull-right">Submit</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>


		<!-- /.container-fluid -->
		<footer class="footer text-center"> {{ date('Y') }} &copy; Pusdatin BPAD DKI Jakarta </footer>
	</div>
	
@endsection

<!-- /////////////////////////////////////////////////////////////// -->

@section('js')
	<script src="{{ ('/portal/public/ample/plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
	<!-- Bootstrap Core JavaScript -->
	<!-- <script src="https://code.jquery.com/jquery-3.4.1.js"></script> -->
	<script src="{{ ('/portal/public/ample/bootstrap/dist/js/bootstrap.min.js') }}"></script>
	<!-- Menu Plugin JavaScript -->
	<script src="{{ ('/portal/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js') }}"></script>
	<!--slimscroll JavaScript -->
	<script src="{{ ('/portal/public/ample/js/jquery.slimscroll.js') }}"></script>
	<!--Wave Effects -->
	<script src="{{ ('/portal/public/ample/js/waves.js') }}"></script>
	<!-- Custom Theme JavaScript -->
	<script src="{{ ('/portal/public/ample/js/custom.min.js') }}"></script>
	<!--Style Switcher -->
	<script src="{{ ('/portal/public/ample/plugins/bower_components/styleswitcher/jQuery.style.switcher.js') }}"></script>

	<script>
        $(function () {
            $('#body-ultah-kemarin').hide();

            $.ajax({ 
                method: "GET", 
                url: "{{ route('home.ulangtahun') }}",
            }).done(function( result ) {
                $('#loading-ultah-kemarin').hide();
                $('#body-ultah-kemarin').show();

                $.each( result['ultah_yes'], function( key, value ) {
                    $('#body-ultah-kemarin').append(
                        "<li>"+value['nm_emp']+" - "+value['nm_unit']+" <b>"+(value['nm_lok']).toUpperCase()+"</b></li>"
                    );
                });

                // 
                
                $('#loading-ultah-today').hide();
                $('#body-ultah-today').show();

                $.each( result['ultah_now'], function( key, value ) {
                    $('#body-ultah-today').append(
                        "<li>"+value['nm_emp']+" - "+value['nm_unit']+" <b>"+(value['nm_lok']).toUpperCase()+"</b></li>"
                    );
                });

                // 

                $('#loading-ultah-besok').hide();
                $('#body-ultah-besok').show();

                $.each( result['ultah_tom'], function( key, value ) {
                    $('#body-ultah-besok').append(
                        "<li>"+value['nm_emp']+" - "+value['nm_unit']+" <b>"+(value['nm_lok']).toUpperCase()+"</b></li>"
                    );
                });
            }).fail(function( res, exception ) {
                $('#loading-ultah-kemarin').empty();
                $('#loading-ultah-kemarin').append(
                    "<span class='text-center'>"+
                        "<h2>"+res.status+" - "+res.statusText+"</h2>"+
                    "</span>"
                );

                $('#loading-ultah-today').empty();
                $('#loading-ultah-today').append(
                    "<span class='text-center'>"+
                        "<h2>"+res.status+" - "+res.statusText+"</h2>"+
                    "</span>"
                );

                $('#loading-ultah-besok').empty();
                $('#loading-ultah-besok').append(
                    "<span class='text-center'>"+
                        "<h2>"+res.status+" - "+res.statusText+"</h2>"+
                    "</span>"
                );
            });
        });
    </script>

<script>
    $(function () {
        $('#body-pensiun-kemarin').hide();

        $.ajax({ 
            method: "GET", 
            url: "{{ route('home.pensiun') }}",
        }).done(function( result ) {
            $('#loading-pensiun-now').hide();
            $('#body-pensiun-now').show();

            $.each( result['pensiun_now'], function( key, value ) {
                $('#body-pensiun-now').append(
                    "<li>"+value['nm_emp']+" - "+value['nm_unit']+" <b>"+(value['nm_lok']).toUpperCase()+"</b></li>"
                );
            });

            // 
            
            $('#loading-pensiun-min1').hide();
            $('#body-pensiun-min1').show();

            $.each( result['pensiun_min1'], function( key, value ) {
                $('#body-pensiun-min1').append(
                    "<li>"+value['nm_emp']+" - "+value['nm_unit']+" <b>"+(value['nm_lok']).toUpperCase()+"</b></li>"
                );
            });

            // 

            $('#loading-pensiun-min6').hide();
            $('#body-pensiun-min6').show();

            $.each( result['pensiun_min6'], function( key, value ) {
                $('#body-pensiun-min6').append(
                    "<li>"+value['nm_emp']+" - "+value['nm_unit']+" <b>"+(value['nm_lok']).toUpperCase()+"</b></li>"
                );
            });
        }).fail(function( res, exception ) {
            $('#loading-pensiun-now').empty();
            $('#loading-pensiun-now').append(
                "<span class='text-center'>"+
                    "<h2>"+res.status+" - "+res.statusText+"</h2>"+
                "</span>"
            );

            $('#loading-pensiun-min1').empty();
            $('#loading-pensiun-min1').append(
                "<span class='text-center'>"+
                    "<h2>"+res.status+" - "+res.statusText+"</h2>"+
                "</span>"
            );

            $('#loading-pensiun-min6').empty();
            $('#loading-pensiun-min6').append(
                "<span class='text-center'>"+
                    "<h2>"+res.status+" - "+res.statusText+"</h2>"+
                "</span>"
            );
        });
    });
</script>
    
    <script type="text/javascript">
		$.fn.extend({
			treed: function (o) {
			  
			  var openedClass = 'glyphicon-minus-sign';
			  var closedClass = 'glyphicon-plus-sign';
			  
			  if (typeof o != 'undefined'){
				if (typeof o.openedClass != 'undefined'){
				openedClass = o.openedClass;
				}
				if (typeof o.closedClass != 'undefined'){
				closedClass = o.closedClass;
				}
			  };
			  
				//initialize each of the top levels
				var tree = $(this);
				tree.addClass("tree");
				tree.find('li').has("ul").each(function () {
					var branch = $(this); //li with children ul
					branch.prepend("<i class='indicator glyphicon " + closedClass + "'></i>");
					branch.addClass('branch');
					branch.on('click', function (e) {
						if (this == e.target) {
							var icon = $(this).children('i:first');
							icon.toggleClass(openedClass + " " + closedClass);
							$(this).children().children().toggle();
						}
					})
					branch.children().children().toggle();
				});
				//fire event from the dynamically added icon
			  tree.find('.branch .indicator').each(function(){
				$(this).on('click', function () {
					$(this).closest('li').click();
				});
			  });
				//fire event to open branch if the li contains an anchor instead of text
				tree.find('.branch>a').each(function () {
					$(this).on('click', function (e) {
						$(this).closest('li').click();
						e.preventDefault();
					});
				});
				//fire event to open branch if the li contains a button instead of text
				tree.find('.branch>button').each(function () {
					$(this).on('click', function (e) {
						$(this).closest('li').click();
						e.preventDefault();
					});
				});
			}
		});

		//Initialization of treeviews

		$('#tree1').treed();
	</script>
@endsection
