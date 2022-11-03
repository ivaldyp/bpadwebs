<?php

namespace App\Http\Controllers;

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Traits\SessionCheckTraits;

use App\Sec_menu;

session_start();

class KepegawaianReportController extends Controller
{
    use SessionCheckTraits;

    public function index(Request $request)
    {
        $currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

        return view ('pages.bpadkepegawaianreport.index')
                ->with('access', $access);
    }
    
    public function printexcelpensiun(Request $request)
    {
        $tahun = $request->tahun_pensiun;
        $tahun_pegawai = $tahun - 58;

		$employees = DB::select( DB::raw("  
					SELECT id_emp, nrk_emp, nip_emp, nm_emp, a.idgroup as idgroup, tgl_lahir, jnkel_emp, tgl_join, status_emp, tbjab.idjab, tbjab.idunit, tbunit.nm_unit, tbunit.notes, tbunit.child, d.nm_lok from bpaddtfake.dbo.emp_data as a
					CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM  bpaddtfake.dbo.emp_gol,bpaddtfake.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
					CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
					CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpaddtfake.dbo.emp_dik,bpaddtfake.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
					CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
					,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
					AND ked_emp = 'AKTIF' AND YEAR(tgl_lahir) = $tahun_pegawai
					order by idunit asc, nm_emp ASC") );
		$employees = json_decode(json_encode($employees), true);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->mergeCells('A1:I1');
		$sheet->setCellValue('A1', 'DATA PEGAWAI PENSIUN');
		$sheet->getStyle('A1')->getFont()->setBold( true );
		$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

		$sheet->mergeCells('A2:I2');
		$sheet->setCellValue('A2', 'BADAN PENGELOLAAN ASET DAERAH');
		$sheet->getStyle('A2')->getFont()->setBold( true );
		$sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

		$sheet->mergeCells('A3:I3');
		$sheet->setCellValue('A3', 'PROVINSI DKI JAKARTA '.date('Y'));
		$sheet->getStyle('A3')->getFont()->setBold( true );
		$sheet->getStyle('A3')->getAlignment()->setHorizontal('center');	

		$styleArray = [
			'font' => [
				'size' => 12,
				'name' => 'Trebuchet MS',
			]
		];
		$sheet->getStyle('A1:I5')->applyFromArray($styleArray);

		$sheet->setCellValue('A5', 'NO');
		$sheet->setCellValue('B5', 'ID');
		$sheet->setCellValue('C5', 'NIP');
		$sheet->setCellValue('D5', 'NRK');
		$sheet->setCellValue('E5', 'NAMA');
		$sheet->setCellValue('F5', 'UNIT');
		$sheet->setCellValue('G5', 'LOKASI');
		$sheet->setCellValue('H5', 'TGL LAHIR');
		$sheet->setCellValue('I5', 'STATUS');

		$colorArrayhead = [
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => 'F79646',
				],
			],
		];
		$sheet->getStyle('A5:I5')->applyFromArray($colorArrayhead);

		$sheet->getStyle('A5:I5')->getFont()->setBold( true );

		$sheet->getStyle('A5:I5')->getAlignment()->setHorizontal('center');

		$colorArrayV1 = [
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => 'FDE9D9',
				],
			],
		];

		$nowrow = 6;
		$rowstart = $nowrow - 1;
		foreach ($employees as $key => $employee) {
			if ($key%2 == 0) {
				$sheet->getStyle('A'.$nowrow.':I'.$nowrow)->applyFromArray($colorArrayV1);
			}

			$sheet->setCellValue('A'.$nowrow, $key+1);
			$sheet->setCellValue('B'.$nowrow, $employee['id_emp']);
			$sheet->setCellValue('C'.$nowrow, $employee['nip_emp'] ? '\''.$employee['nip_emp'] : '-' );
			$sheet->setCellValue('D'.$nowrow, $employee['nrk_emp'] ? $employee['nrk_emp'] : '-' );
			$sheet->getStyle('D'.$nowrow)->getAlignment()->setHorizontal('right');
			$sheet->setCellValue('E'.$nowrow, strtoupper($employee['nm_emp']));
			$sheet->setCellValue('F'.$nowrow, strtoupper($employee['notes']));
			$sheet->setCellValue('G'.$nowrow, $employee['nm_lok']);
			$sheet->setCellValue('H'.$nowrow, date('d-m-Y', strtotime($employee['tgl_lahir'])));
			$sheet->setCellValue('I'.$nowrow, $employee['status_emp']);

			if (strlen($employee['idunit']) < 10) {
				$sheet->getStyle('A'.$nowrow.':I'.$nowrow)->getFont()->setBold( true );
			}

			$nowrow++;
		}

		foreach(range('A','I') as $columnID) {
			$sheet->getColumnDimension($columnID)
				->setAutoSize(true);
		}

		$rowend = $nowrow - 1;

		$filename = 'PEGAWAI_PENSIUN_BPAD.xlsx';

		// Redirect output to a client's web browser (Xlsx)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		 
		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.

		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
    }
}
