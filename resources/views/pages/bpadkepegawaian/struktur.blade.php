@extends('layouts.masterhome')

@section('css')
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/portal/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<!-- Menu CSS -->
	<link href="{{ ('/portal/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
	<!-- animation CSS -->
	<link href="{{ ('/portal/ample/css/animate.css') }}" rel="stylesheet">
	<!-- Custom CSS -->
	<link href="{{ ('/portal/ample/css/style.css') }}" rel="stylesheet">
	<!-- color CSS -->
	<link href="{{ ('/portal/ample/css/colors/purple-dark.css') }}" id="theme" rel="stylesheet">

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
												echo str_replace('%20', ' ', ucwords(explode("?", $link[4])[0]));
											?> </h4> </div>
				<div class="col-lg-9 col-sm-8 col-md-8 col-xs-12"> 
					<ol class="breadcrumb">
						<li>{{config('app.name')}}</li>
						<?php 
							if (count($link) == 5) {
								?> 
									<li class="active"> {{ str_replace('%20', ' ', ucwords(explode("?", $link[4])[0])) }} </li>
								<?php
							} elseif (count($link) > 5) {
								?> 
									<li class="active"> {{ str_replace('%20', ' ', ucwords(explode("?", $link[4])[0])) }} </li>
									<li class="active"> {{ str_replace('%20', ' ', ucwords(explode("?", $link[5])[0])) }} </li>
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
				<div class="col-md-12">
					<!-- <div class="white-box"> -->
					<div class="panel panel-default">
						<div class="panel-heading">Struktur Organisasi</div>
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
								<input type="hidden" id="empstruc" value="{{ json_encode($employees) }}">
								<div class="panel-body">
									<div style="width:100%; height:700px;" id="orgchart"/>
								</div>
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

<!-- /////////////////////////////////////////////////////////////// -->

@section('js')
	<script src="{{ ('/portal/ample/plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
	<!-- Bootstrap Core JavaScript -->
	<script src="{{ ('/portal/ample/bootstrap/dist/js/bootstrap.min.js') }}"></script>
	<!-- Menu Plugin JavaScript -->
	<script src="{{ ('/portal/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js') }}"></script>
	<!--slimscroll JavaScript -->
	<script src="{{ ('/portal/ample/js/jquery.slimscroll.js') }}"></script>
	<!--Wave Effects -->
	<script src="{{ ('/portal/ample/js/waves.js') }}"></script>
	<!-- Custom Theme JavaScript -->
	<script src="{{ ('/portal/ample/js/custom.min.js') }}"></script>
	<script src="{{ ('/portal/js/jquery.zoom.js') }}"></script>
	<script src="{{ ('/portal/js/jquery.zoom.min.js') }}"></script>
	<!-- OrgChart -->
	<script src="{{ ('/portal/js/orgchart.js') }}"></script>

	<script>
		var empstruc = $("#empstruc").val();
		empstruc = JSON.parse(empstruc);

		var myArray = [];
		var imageExists = false;

		$.each( empstruc, function( key, value ) {

			// var imgloc = '{{ config('app.openfileimg') }}' + '/' +  value['id_emp'] + '/profil/' + value['foto'] ;
			
			// $.ajax({
			// 	url: imgloc,
			// 	type:'HEAD',
			// 	error: function()
			// 	{
			// 		imageExists = true;
			// 	},
			// 	success: function()
			// 	{
			// 		imageExists = false;
			// 	}
			// });

			// console.log(imageExists);

			var ceksao = empstruc.filter(function (data) { return data.idunit == value['sao'] });
			if (key == 0) {
				myArray.push({
					id: value['idunit'],
					name: value['nm_emp'],
					title: value['nm_unit'],
					img: '{{ config('app.openfileimgdefault') }}',
				});
			} else {
				if (ceksao != '') {
					myArray.push({
						id: value['idunit'],
						pid: value['sao'],
						name: value['nm_emp'],
						title: value['nm_unit'],
						img: '{{ config('app.openfileimgdefault') }}',
					});
				}
			}	
		});

		var chart = new OrgChart(document.getElementById("orgchart"), {
			template: "rony",
			nodeBinding: {
				field_0: "name",
				field_1: "title",
				img_0: "img",
			},
			collapse: {
				level: 2
			},
			layout: OrgChart.mixed,
			// orientation: OrgChart.orientation.left,
			nodes: myArray
		});
	</script>

	<script type="text/javascript">
		$(document).ready(function(){
			// $('#ex1').zoom({
			//   	magnify: 0.2,
			// });
			// $('#ex2').zoom({
			//   	magnify: 0.2,
			// });
		});
	</script>
@endsection