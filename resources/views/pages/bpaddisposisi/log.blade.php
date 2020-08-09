<!-- Bootstrap Core CSS -->
<link href="{{ ('/portal/public/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">

<!-- Custom CSS -->
<link href="{{ ('/portal/public/ample/css/style.css') }}" rel="stylesheet">

<div class="container jumbotron">
	<div class="col-md-6">
		<div class="table-responsive">
			<table class="table">
				<tbody>
					<tr>
						<td><b>No Form</b></td>
						<td>{{ $dispmaster['no_form'] }}</td>
					</tr>
					<tr>
						<td><b>Kode Surat</b></td>
						<td>{{ $dispmaster['kd_surat'] }}</td>
					</tr>
					<tr>
						<td><b>Tipe</b></td>
						<td>{{ $dispmaster['catatan_final'] ?? 'Surat' }}</td>
					</tr>
					<tr>
						<td><b>Tanggal Masuk</b></td>
						<td>{{ date('d-M-Y', strtotime($dispmaster['tgl_masuk'])) }}</td>
					</tr>
					<tr>
						<td><b>No Index</b></td>
						<td>{{ $dispmaster['no_index'] ?? '-' }}</td>
					</tr>
					<tr>
						<td><b>Kode Disposisi</b></td>
						<td>{{ $dispmaster['kode_disposisi'] }}</td>
					</tr>
					<tr>
						<td><b>Nomor & Tgl Surat</b></td>
						<td>{{ $dispmaster['no_surat'] ?? '-' }} <br>
						{{ $dispmaster['tgl_surat'] ?? '-' }}</td>
					</tr>
					<tr>
						<td><b>Perihal</b></td>
						<td>{{ $dispmaster['perihal'] }}</td>
					</tr>
					<tr>
						<td><b>Dari</b></td>
						<td>{{ $dispmaster['asal_surat'] }}</td>
					</tr>
					<tr>
						<td><b>Kepada</b></td>
						<td>{{ $dispmaster['kepada_surat'] }}</td>
					</tr>
					<tr>
						<td><b>Unit</b></td>
						<td>{{ $dispmaster['no_form'] }}</td>
					</tr>
					<tr>
						<td><b>Sifat</b></td>
						<td>{{ $dispmaster['sifat1_surat'] }} {{ $dispmaster['sifat2_surat'] ? ' - '.$dispmaster['sifat2_surat'] : '' }}</td>
					</tr>
					<tr>
						<td><b>Keterangan</b></td>
						<td>{{ $dispmaster['ket_lain'] }}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="col-md-6">
		<h2>LOG</h2>
		<div class="table-responsive">
			<table>
				<tbody>
					{!! $treedisp !!}
				</tbody>
			</table>
		</div>
	</div>
</div>



