<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link href="{{ $_SERVER['DOCUMENT_ROOT'] }}/laporanbmd/public/ample/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="{{ $_SERVER['DOCUMENT_ROOT'] }}/laporanbmd/public/ample/css/animate.css" rel="stylesheet">

	<style type="text/css">

		.logoHeader {
		    /*height: 120px;*/
		    /*vertical-align: middle;*/
		    top: 0px;
		    text-align: center; 
		    font-family: Arial, Helvetica, sans-serif;
		}

		.floatLeft { float: left; }

		.floatRight { float: right; }

		table {
			width: 100%
		}

		.tipetop, .tipebot {
			vertical-align: middle;
			align-content: center;
			text-align: center;
			border: 1px solid black;
		}

		.tipetop {
			color: white;
			background-color: #808080;
		}

		.tablelaporan {
			font-size: 12px;
		}

		/*.tablelaporan thead tr th {
			
		}*/

		.tablelaporan thead tr th{
			border: 1px solid #808080;
			vertical-align: middle;
			align-content: center;
			text-align: center;
		}

		.headclrblue{
			background-color: #DCE6F1;
		}
		.headclrgray{
			background-color: #BFBFBF;
		}

		.headclrgray th{
			line-height: 2px;
			font-size: 10px;
		}

	</style>
</head>
<body >
	<header>
		<h3 class="logoHeader"><b>
			REKAP KONTEN {{ strtoupper($kategoris['nmkat']) }}<br>
			PERIODE {{ strtoupper($bln) }} {{ $thn }}											
		</b></h3>
	</header>
	<div class="row">
		<table style="padding-top: 15px" class=" tablelaporan table-bordered">
			<thead>
				<tr class="headclrblue">
					<th class="">No</th>
					<th class="col-md-2">Kegiatan</th>
					<th class="col-md-2">User</th>
					<th>Tanggal</th>
					<th class="col-md-2">Link</th>
					<th class="col-md-1">Attachment</th>
				</tr>
			</thead>
			<tbody>
				@foreach($contents as $key => $content)
				<?php 
					if ($content['idkat'] == 1) {
						$kat = 'berita';
					} else {
						$kat = 'foto';
					}
				?>
				<tr>
					<td style="text-align: center;">{{ $key+1 }}</td>
					<td style="word-wrap: break-word">{{ $content['judul'] }}</td>
					<td style="word-wrap: break-word;">{{ strtoupper($content['nm_emp']) }} - [{{ $content['nm_unit'] }}, {{ strtoupper($content['nm_lok']) }}]</td>
					<td>{{ date('d/M/Y', strtotime(str_replace('/', '-', $content['tanggal']))) }}</td>
					<td style="word-wrap: break-word">https://{{ $url }}/portal/content/{{ $kat }}/{{ $content['ids'] }}</td>
					<td style="word-wrap: break-word">
						{{ ($content['tfile'] && $content['tfile'] != '' ) ? 'https://' . $url . '/portal/public/publicimg/images/cms/1.20.512/' . $content['idkat']. '/file/' . $content['tfile'] : '-' }}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>	
	</div>
	
	<script src="{{ $_SERVER['DOCUMENT_ROOT'] }}/laporanbmd/public/ample/plugins/bower_components/jquery/dist/jquery.min.js"></script>
	<!-- Bootstrap Core JavaScript -->
	<script src="{{ $_SERVER['DOCUMENT_ROOT'] }}/laporanbmd/public/ample/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="{{ $_SERVER['DOCUMENT_ROOT'] }}/laporanbmd/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.js"></script>
	<script>
		$(function () {
			$('.myTable').DataTable({
				"ordering" : false,
				// "searching": false,
				// "bPaginate": false,
				// "bInfo" : false,
				"lengthChange": true,
				// "pageLength": 20,
				// "scrollY": "200px",
			});
		});
	</script>
</body>
</html>