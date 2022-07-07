@extends('layouts.masterhome' )

@section('css')
    <!-- Bootstrap Core CSS -->
    <link href="{{ ('/portal/public/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ ('/portal/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <!-- Menu CSS -->
    <link href="{{ ('/portal/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
    <!-- animation CSS -->
    <link href="{{ ('/portal/public/ample/css/animate.css') }}" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ ('/portal/public/ample/css/style.css') }}" rel="stylesheet">
    <!-- color CSS -->
    <link href="{{ ('/portal/public/ample/css/colors/purple-dark.css') }}" id="theme" rel="stylesheet">
    <!-- page CSS -->
	<link href="{{ ('/portal/public/ample/plugins/bower_components/custom-select/custom-select.css') }}" rel="stylesheet" type="text/css" />

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
                                                echo ucwords($link[4]);
                                            ?> </h4> </div>
                <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12"> 
                    <ol class="breadcrumb">
                        <li>{{config('app.name')}}</li>
                        <?php 
                            $link = explode("/", url()->full());
                            if (count($link) == 5) {
                                ?> 
                                    <li class="active"> {{ ucwords($link[4]) }} </li>
                                <?php
                            } elseif (count($link) == 6) {
                                ?> 
                                    <li class="active"> {{ ucwords($link[4]) }} </li>
                                    <li class="active"> {{ str_replace('%20', ' ', ucwords($link[5])) }} </li>
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
            <div class="row ">
                <div class="col-md-10 col-md-offset-1">
                    <!-- <div class="white-box"> -->
                    <div class="panel panel-default">
                        <div class="panel-heading">FAQ TABLE</div>
                        <div class="panel-wrapper collapse in">
                            <div class="panel-body">
                                <div class="row">
                                    <button class="btn btn-info btn-tambah" style="margin-bottom: 10px" data-toggle="modal" data-target="#modal-insert" data-appnow="{{ $appnow }}">Tambah</button>
                                </div>
                                <div class="row" style="margin-bottom: 10px">
									<form method="GET" action="/portal/faq/setup">
										<div class="row">
											<div class=" col-md-4">
												<select class="form-control select2" name="appnow" id="appnow" onchange="this.form.submit()">
													@foreach($apps as $app)
                                                    <option <?php if ($appnow == $app['app_name']): ?> selected <?php endif ?> value="{{ $app['app_name'] }}">{{ $app['app_name'] }}</option>
                                                    @endforeach
												</select>
											</div>
											<button type="submit" class="btn btn-primary">Cari</button>
										</div>
										<hr>
									</form>
								</div>
                                <div class="table-responsive">
                                    <table id="myTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>IDS</th>
                                                <th>Questions</th> 
                                                <th>Answers</th>
                                                <th>Edit</th>
                                                <th>Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($faqs as $key => $faq)
                                            <tr>
                                                <form method="POST" action="/portal/faq/update" class="form-horizontal">
                                                @csrf
                                                    <td style="vertical-align: middle;">{{ $key+1 }}</td>
                                                    <td style="vertical-align: middle;">{{ $faq['ids'] }}</td>
                                                    <td style="vertical-align: middle;">
                                                        <textarea rows="5" class="form-control" name="questions">{!! $faq['questions'] !!}</textarea>
                                                    </td>
                                                    <td style="vertical-align: middle;">
                                                        <textarea rows="5" class="form-control" name="answers">{!! $faq['answers'] !!}</textarea>
                                                    </td>
                                                    <td style="vertical-align: middle;">
                                                        <input type="hidden" value="{{ $faq['ids'] }}" name="ids">
                                                        <input type="hidden" value="{{ $appnow }}" name="appnow" id="appnow">
                                                        <button class="btn btn-success" type="submit"><i class="fa fa-check"></i></button>
                                                    </td>
                                                    <td style="vertical-align: middle;">
                                                        {{-- <a href="/portal/faq/delete?ids={{$faq['ids']}}&appnow={{$appnow}}"><button class="btn btn-danger btn-delete-faq" type="button"><i class="fa fa-trash"></i></button></a> --}}
                                                        <button class="btn btn-danger btn-delete-faq" type="button" data-ids="{{ $faq['ids'] }}" data-appnow="{{ $appnow }}"><i class="fa fa-trash"></i></button>
                                                        <!-- <button class="btn btn-danger"><i class="fa fa-trash"></i></button> -->
                                                    </td>
                                                </form>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- </div> -->
                    <div id="modal-insert" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="/portal/faq/insert" class="form-horizontal">
                                @csrf
                                    <div class="modal-header">
                                        <h4 class="modal-title"><b>Tambah FAQ</b></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="insert-appname" class="col-md-2 control-label"> Aplikasi </label>
                                            <div class="col-md-10">
                                                <strong><p class="form-control-static" id="insert-appname"></p></strong>
                                                <input type="hidden" name="appname" id="appname">
                                            </div>
                                        </div>
                                        <div class="form-group">
											<label for="insert-questions" class="col-md-2 control-label"> Questions </label>
											<div class="col-md-10">
												<textarea required id="insert-questions" class="form-control" rows="5" placeholder="Enter text ..." name="questions"></textarea>
											</div>
										</div>
                                        <div class="form-group">
											<label for="insert-answers" class="col-md-2 control-label"> Answers </label>
											<div class="col-md-10">
												<textarea required id="insert-answers" class="form-control" rows="5" placeholder="Enter text ..." name="answers"></textarea>
											</div>
										</div>
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
            
        </div>
        <!-- /.container-fluid -->
        <footer class="footer text-center"> 
            <span>&copy; Copyright <?php echo date('Y'); ?> BPAD DKI Jakarta.</span></span></a>
        </footer>
    </div>
@endsection

<!-- /////////////////////////////////////////////////////////////// -->

@section('js')
    <!-- jQuery -->
    <script src="{{ ('/portal/public/ample/plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="{{ ('/portal/public/ample/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- Menu Plugin JavaScript -->
    <script src="{{ ('/portal/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js') }}"></script>
    <!--slimscroll JavaScript -->
    <script src="{{ ('/portal/public/ample/js/jquery.slimscroll.js') }}"></script>
    <!--Wave Effects -->
    <script src="{{ ('/portal/public/ample/js/waves.js') }}"></script>
    <!-- Custom Theme JavaScript -->
    <script src="{{ ('/portal/public/ample/js/custom.min.js') }}"></script>
    <script src="{{ ('/portal/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ ('/portal/public/ample/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            
            $('.btn-tambah').on('click', function () {
                var $el = $(this);

                $('#insert-appname').text($el.data('appnow'));
                $('#appname').val($el.data('appnow'));
            });

            $(".select2").select2();

            $('.btn-delete-faq').on('click', function () {
				var $el = $(this);
                var ids = $el.data('ids');
                var appnow = $el.data('appnow');

				if(confirm("Apa anda yakin ingin menghapus FAQ dengan ID: "+ids+"?")){
					if (confirm("FAQ yang telah terhapus, tidak dapat dikembalikan, apa anda yakin?")) {
						$.ajax({ 
						type: "GET", 
						url: "/portal/faq/delete",
						data: { ids : ids, appnow : appnow },
						dataType: "JSON",
						}).done(function( data ) { 
							if (data == 1) {
								alert("FAQ berhasil dihapus 😁");
								location.reload();
							} else {
								alert("Tidak dapat menghapus FAQ");
								location.reload();
							}
							
						}); 
					}
				}
			});

            $("#modal-delete").on("hidden.bs.modal", function () {
                $("#label_delete").empty();
            });

            $('#myTable').DataTable();
        });
    </script>
@endsection