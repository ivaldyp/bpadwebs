<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link href="/portal/public/ample/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="/portal/public/ample/css/animate.css" rel="stylesheet">

	<style type="text/css">

		.logoHeader {
		    /*height: 120px;*/
		    /*vertical-align: middle;*/
		    top: 0px;
		    text-align: center; 
		    font-family: Arial, Helvetica, sans-serif;
		}

		table {
			width: 100%;
			margin-left: 15px;
			border-collapse: collapse;
		}

		.tablelaporan {
			text-transform: uppercase;
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

		tr {

		}

		td, th {
			vertical-align: middle;
			font-family: Arial, Helvetica, sans-serif;
			font-size: 12px;
		}

		td {
			padding-left: 3px;
		}

		table, td, th {
			border: 1px solid black;
		} 

		.section-title {
			font-family: Arial, Helvetica, sans-serif;
			font-size: 14px;
		}

		.wid35 {
			width: 40%;
			border: white;
			vertical-align: top;
		}

		.wid5 {
			width: 5%;
			border: white;
			vertical-align: top;
		}

		.wid60 {
			width: 55%;
			border: white;
			vertical-align: top;
		}

	</style>
</head>
<body >
	<header>
		<!-- <img class="floatLeft" src=" portal}{{ ('/public/img/excel/excel-logo-dki2.png') }}" height="100">-->		
		<h3 class="logoHeader"><b>
			DAFTAR RIWAYAT HIDUP										
		</b></h3>
	</header>
	
	<div class="row" style="padding-left: 70px">
		<label class="section-title"><h4>I. DATA PRIBADI</h4></label>
		<table class="nottable">
			<tbody>
				<tr class="">
					<td class="wid35">a. Nama (Lengkap dengan gelar)</td>
					<td class="wid5"> : </td>
					<td class="wid60">{{ $emp_data['gelar_dpn'] ?? '' }} {{ $emp_data['nm_emp'] }}{{ $emp_data['gelar_blk'] ? (', ' . $emp_data['gelar_blk']) : '' }}</td>
				</tr>
				<tr class="">
					<td class="wid35">b. NIP / NRK</td>
					<td class="wid5"> : </td>
					<td class="wid60">{{ !(is_null($emp_data['nip_emp'])) && $emp_data['nip_emp'] != '' ? $emp_data['nip_emp'] : '-' }} / 
										{{ !(is_null($emp_data['nrk_emp'])) && $emp_data['nrk_emp'] != '' ? $emp_data['nrk_emp'] : '-' }}</td>
				</tr>
				<tr class="">
					<td class="wid35">c. Tempat, Tanggal Lahir</td>
					<td class="wid5"> : </td>
					<td class="wid60">{{ $emp_data['tempat_lahir'] ?? '-' }}, {{ date('d-M-Y',strtotime($emp_data['tgl_lahir'])) }}</td>
				</tr>
				<tr class="">
					<td class="wid35">d. Jenis Kelamin</td>
					<td class="wid5"> : </td>
					<td class="wid60">{{ $emp_data['jnkel_emp'] == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
				</tr>
				<tr class="">
					<td class="wid35">e. Agama</td>
					<td class="wid5"> : </td>
					<td class="wid60">
						<?php if ($emp_data['idagama'] == 'A') : ?>
							Islam
						<?php elseif ($emp_data['idagama'] == 'B') : ?>
							Katolik
						<?php elseif ($emp_data['idagama'] == 'C') : ?>
							Protestan
						<?php elseif ($emp_data['idagama'] == 'D') : ?>
							Budha
						<?php elseif ($emp_data['idagama'] == 'E') : ?>
							Hindu
						<?php elseif ($emp_data['idagama'] == 'F') : ?>
							Lainnya
						<?php elseif ($emp_data['idagama'] == 'G') : ?>
							Konghucu
						<?php endif ?>
					</td>
				</tr>
				<tr class="">
					<td class="wid35">f. Status Perkawinan</td>
					<td class="wid5"> : </td>
					<td class="wid60">{{ $emp_data['status_nikah'] }}</td>
				</tr>
				<tr class="">
					<td class="wid35">g. Alamat</td>
					<td class="wid5"> : </td>
					<td class="wid60">{{ !(is_null($emp_data['alamat_emp'])) && $emp_data['alamat_emp'] != '' ? $emp_data['alamat_emp'] : '-' }}</td>
				</tr>
				<tr class="">
					<td class="wid35">h. Nomor telp</td>
					<td class="wid5"> : </td>
					<td class="wid60">{{ !(is_null($emp_data['tlp_emp'])) && $emp_data['tlp_emp'] != '' ? $emp_data['tlp_emp'] : '-' }}</td>
				</tr>
				<tr class="">
					<td class="wid35">i. Email</td>
					<td class="wid5"> : </td>
					<td class="wid60">{{ !(is_null($emp_data['email_emp'])) && $emp_data['email_emp'] != '' ? $emp_data['email_emp'] : '-' }}</td>
				</tr>
			</tbody>

		</table>	
	</div>

	<div class="row" style="padding-left: 70px">
		<label class="section-title"><h4>II. KELUARGA</h4></label>
		<table class="table tablelaporan table-bordered" style="border: solid 1px black">
			<thead>
				<tr class="headclrblue">
					<th style="width: 5%">No</th>
					<th style="width: 25%">Keluarga</th>
					<th style="width: 25%">Nama</th>
					<th style="width: 25%">NIK</th>
					<th style="width: 25%">Tanggal Lahir</th>
				</tr>
			</thead>
			<tbody>
				@if(count($emp_kel) > 0)
				@foreach($emp_kel as $key => $kel)
				<tr>
					<td style="text-align: center;">{{ $key + 1 }}</td>
					<td>{{ strtoupper($kel['jns_kel']) }}</td>
					<td>{{ ucwords(strtolower($kel['nm_kel'])) }}</td>
					<td>{{ $kel['nik_kel'] != '' && !(is_null($kel['nik_kel'])) ? $kel['nik_kel'] : '-' }}</td>
					<td>{{ date('d-M-Y',strtotime($kel['tgl_kel'])) }}</td>
				</tr>
				@endforeach
				@else
				<tr>
					<td style="text-align: center;" colspan=5> --- Tidak Ada Data --- </td>
				</tr>
				@endif
			</tbody>

		</table>	
	</div>

	<div class="row" style="padding-left: 70px">
		<label class="section-title"><h4>III. RIWAYAT GOLONGAN</h4></label>
		<table class="table tablelaporan table-bordered" style="border: solid 1px black">
			<thead>
				<tr class="headclrblue">
					<th style="width: 5%">No</th>
					<th style="width: 25%">Pangkat / Gol</th>
					<th style="width: 25%">TMT Pangkat</th>
					<th style="width: 25%">Nomor SK</th>
					<th style="width: 25%">Tanggal SK</th>
				</tr>
			</thead>
			<tbody>
				@if(count($emp_gol) > 0)
				@foreach($emp_gol as $key => $gol)
				<tr>
					<td style="text-align: center;">{{ $key + 1 }}</td>
					<td>{{ $gol['nm_pangkat'] }} ({{ $gol['idgol'] }})</td>
					<td>{{ date('d-M-Y',strtotime($gol['tmt_gol'])) }}</td>
					<td>{{ $gol['no_sk_gol'] != '' && !(is_null($gol['no_sk_gol'])) ? $gol['no_sk_gol'] : '-' }}</td>
					<td>{{ date('d-M-Y',strtotime($gol['tmt_sk_gol'])) }}</td>
				</tr>
				@endforeach
				@else
				<tr>
					<td style="text-align: center;" colspan=5> --- Tidak Ada Data --- </td>
				</tr>
				@endif
			</tbody>

		</table>	
	</div>

	<div class="row" style="padding-left: 70px">
		<label class="section-title"><h4>IV. RIWAYAT UNIT KERJA</h4></label>
		<table class="table tablelaporan table-bordered" style="border: solid 1px black">
			<thead>
				<tr class="headclrblue">
					<th style="width: 5%">No</th>
					<!-- <th style="width: 25%">Jabatan</th> -->
					<th style="width: 25%">Unit Kerja</th>
                    <th style="width: 25%">Lokasi</th>
					<th style="width: 25%">Nomor SK</th>
					<th style="width: 25%">Tanggal SK</th>
				</tr>
			</thead>
			<tbody>
				@if(count($emp_jab) > 0)
				@foreach($emp_jab as $key => $jab)
				<tr>
					<td style="text-align: center;">{{ $key + 1 }}</td>
					<!-- <td>{!! wordwrap($jab['idjab'], 50, "<br>\n", TRUE) !!}</td> -->
					<td>{{ ucwords(strtolower($jab['nmunit'])) }}</td>
                    <td>{{ ucwords(strtolower($jab['lokasi']['nm_lok'])) }}</td>
					<td>{{ $jab['no_sk_jab'] != '' && !(is_null($jab['no_sk_jab'])) ? $jab['no_sk_jab'] : '-' }}</td>
					<td>{{ date('d-M-Y',strtotime($jab['tmt_sk_jab'])) }}</td>
				</tr>
				@endforeach
				@else
				<tr>
					<td style="text-align: center;" colspan=5> --- Tidak Ada Data --- </td>
				</tr>
				@endif
			</tbody>

		</table>	
	</div>

	<div class="row" style="padding-left: 70px">
		<label class="section-title"><h4>V. PENDIDIKAN</h4></label>
		<table class="table tablelaporan table-bordered" style="border: solid 1px black">
			<thead>
				<tr class="headclrblue">
					<th style="width: 5%">No</th>
					<th style="width: 15%">Pendidikan</th>
					<th style="width: 25%">Instansi/Lembaga</th>
					<th style="width: 10%">Tahun</th>
					<th style="width: 25%">Jurusan</th>
					<th style="width: 25%">Nomor Ijazah</th>
				</tr>
			</thead>
			<tbody>
				@if(count($emp_dik) > 0)
				@foreach($emp_dik as $key => $dik)
				<tr>
					<td style="text-align: center;">{{ $key + 1 }}</td>
					<td>{{ $dik['iddik'] }}</td>
					<td>{{ $dik['nm_sek'] }} </td>
					<td>{{ $dik['th_sek'] != '' && !(is_null($dik['th_sek'])) ? $dik['th_sek'] : '-' }}</td>
					<td>{{ $dik['prog_sek'] }}</td>
					<td>{{ $dik['no_sek'] != '' && !(is_null($dik['no_sek'])) ? $dik['no_sek'] : '-' }}</td>
				</tr>
				@endforeach
				@else
				<tr>
					<td style="text-align: center;" colspan=6> --- Tidak Ada Data --- </td>
				</tr>
				@endif
			</tbody>

		</table>
	</div>

	<div class="row" style="padding-left: 70px">
		<label class="section-title"><h4>VI. PENDIDIKAN NON FORMAL</h4></label>
		<table class="table tablelaporan table-bordered" style="border: solid 1px black">
			<thead>
				<tr class="headclrblue">
					<th style="width: 5%">No</th>
					<th style="width: 25%">Kegiatan</th>
					<th style="width: 25%">Penyelenggara</th>
					<th style="width: 10%">Tahun SK</th>
					<th style="width: 25%">Nomor SK</th>
				</tr>
			</thead>
			<tbody>
				@if(count($emp_non) > 0)
				@foreach($emp_non as $key => $non)
				<tr>
					<td style="text-align: center;">{{ $key + 1 }}</td>
					<td>{{ $non['nm_non'] ?? '-' }}</td>
					<td>{{ $non['penye_non'] ?? '-' }} </td>
					<td>{{ $non['thn_non'] ?? '-' }}</td>
					<td>{{ $non['sert_non'] != '' && !(is_null($non['sert_non'] )) ? $non['sert_non'] : '-' }}</td>
				</tr>
				@endforeach
				@else
				<tr>
					<td style="text-align: center;" colspan=5> --- Tidak Ada Data --- </td>
				</tr>
				@endif
			</tbody>

		</table>	
	</div>

	<div class="row" style="padding-left: 70px">
		<label class="section-title"><h4>VII. HUKUMAN DISIPLIN</h4></label>
		<table class="table tablelaporan table-bordered" style="border: solid 1px black">
			<thead>
				<tr class="headclrblue">
					<th style="width: 5%">No</th>
					<th style="width: 25%">Jenis</th>
					<th style="width: 25%">Durasi</th>
					<th style="width: 25%">Nomor SK</th>
					<th style="width: 10%">Tanggal SK</th>
				</tr>
			</thead>
			<tbody>
				@if(count($emp_huk) > 0)
				@foreach($emp_huk as $key => $huk)
				<tr>
					<td style="text-align: center;">{{ $key + 1 }}</td>
					<td>{{ $huk['jns_huk'] }}</td>
					<td>{{ date('d/M/Y',strtotime($huk['tgl_mulai'])) }} - {{ date('d/M/Y',strtotime($huk['tgl_akhir'])) }} </td>
					<td>{{ $huk['no_sk'] != '' && !(is_null($huk['no_sk'] )) ? date('d/M/Y',strtotime($huk['no_sk'])) : '-' }}</td>
					<td>{{ $huk['tgl_sk'] != '' && !(is_null($huk['tgl_sk'] )) ? date('d/M/Y',strtotime($huk['tgl_sk'])) : '-' }}</td>
				</tr>
				@endforeach
				@else
				<tr>
					<td style="text-align: center;" colspan=5> --- Tidak Ada Data --- </td>
				</tr>
				@endif
			</tbody>

		</table>	
	</div>
	
	
	

	<script src="/portal/public/ample/plugins/bower_components/jquery/dist/jquery.min.js"></script>
	<!-- Bootstrap Core JavaScript -->
	<script src="/portal/public/ample/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="/portal/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.js"></script>
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