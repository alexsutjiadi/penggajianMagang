<?php
if (!isset($_SESSION)) {
	session_start();
}
if (!isset($_SESSION['pathKota'])) {
	header("Location: ../pilihKota.php");
}
include "../src/main.php";
if (isset($_POST['gogo'])) {
	$init = parse_ini_file($_SESSION['pathKota'] . "init.ini");
	$hitungPPH = $init['hitung_pph'];
	if ($hitungPPH == 0) {
		$message = "Hitung PPH Terlebih Dahulu";
		echo "<script type='text/javascript'>alert('$message');</script>";
		header("Refresh:0");
		return;
	}
include('library/tcpdf.php');
$pdf = new TCPDF('L', 'mm', 'F4');

class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        // Title
        $this->Cell(0, 15, '<< DAFTAR GAJI >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }
}

$pdf->AddPage();

$pdf->SetFont('Helvetica', '', 7.4);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetTextColor(0, 0, 0);


$db = dbase_open($_SESSION['pathKota'] . 'GAJI.DBF', 0);
$db2 = dbase_open($_SESSION['pathKota'] . 'GOLONGAN.DBF', 0);
$db3 = dbase_open($_SESSION['pathKota'] . 'PPH.DBF', 0);

$ndata = dbase_numrecords($db);
$ngol = dbase_numrecords($db2);
$pph = dbase_numrecords($db3);
$No_urut = 0;

for ($gol = 1; $gol <= $ngol; $gol++) {
	$print = "";
	$row2 = dbase_get_record_with_names($db2, $gol);
	$kode = $row2['KODE'];
	$namakode = $row2['NAMA'];
	$kode1 = substr($kode, 0, 1);
	$datapertama = true;
	$TotalGajiBruto = 0;
	$TotalTunjJab = 0;
	$TotalTunjKes = 0;
	$TotalGajiNetto = 0;
	$TotalTHR = 0;
	$TotalTHRBruto = 0;
	$TotalKolomExtra = 0;
	$TotalYangditerima = 0;
	$TotalBCA = 0;
	$TotalTunai = 0;
	
	for ($i = 1; $i <= $ndata; $i++) {
		
		$row = dbase_get_record_with_names($db, $i);
		$kodebca = $row['KODE_BANK'];
		$kolomkosong = 0;
		$row3 = dbase_get_record_with_names($db3, $i);
		$pangkat = $row['DEPT'];
		$pangkat1 = substr($pangkat, 0, 1);

		
		if ($kode1 == $pangkat1) {
			$No_urut++;
			$GajiBruto = $row['GAJI_DASAR'] + $row['TUNJ_REG'] + $row['TUNJ_JAB'] + $row['TUNJ_KES'];
			$GajiNetto = $GajiBruto-($row3['JABAT'] + $row3['THT'] + $row['JPK']);
			$KolomExtra = $row['EXTRA_LAIN']-$row['PINJAMAN'];
			$Total = $GajiNetto-$KolomExtra;
			$BrutoTHR = $row['THR']-$row3['PPH_THR'];

			$TotalGajiBruto = (int)$TotalGajiBruto + (int)$GajiBruto;
			$TotalTunjJab = (int)$TotalTunjJab+(int)$row['TUNJ_JAB'];
			$TotalTunjKes = (int)$TotalTunjKes+(int)$row['TUNJ_KES'];
			$TotalGajiNetto = (int)$TotalGajiNetto+(int)$GajiNetto;
			$TotalTHR = (int)$TotalTHR+(int)$row['THR'];
			$TotalTHRBruto = (int)$TotalTHRBruto+(int)$BrutoTHR;
			$TotalKolomExtra = (int)$TotalKolomExtra+(int)$KolomExtra;
			$TotalYangditerima = (int)$TotalYangditerima+(int)$Total;
			$transfer = 0;
			$tunai = 0;
			
			if ($kodebca==1) {
				$transfer = $Total;
			}else{
				$tunai = $Total;
			}
			$TotalBCA = $TotalBCA + $transfer;
			$TotalTunai = $TotalTunai + $tunai;

			$BrutoTHR = rupiah($BrutoTHR);
			$GajiBruto = rupiah($GajiBruto);
			$GajiNetto = rupiah($GajiNetto);
			$KolomExtra = rupiah($KolomExtra);
			$Total = rupiah($Total);
			$row['TUNJ_JAB'] = rupiah($row['TUNJ_JAB']);
			$row['TUNJ_KES'] = rupiah($row['TUNJ_KES']);
			$row['THR'] = rupiah($row['THR']);
			$kolomkosong = rupiah($kolomkosong);
			$transfer = rupiah($transfer);
			$tunai = rupiah($tunai);
			if ($datapertama == true) {
				$datapertama = false;
				$print .= <<<EOD
							<h1>$namakode</h1>
							<table width="100%" border="1">
								<tr>
									<td rowspan="2" width="15">No</td>
									<td rowspan="2" width="35">NIK</td>
									<td rowspan="2" width="100">Nama</td>
									<td rowspan="2" width="35">Pangkat</td>
									<td rowspan="2" width="70">Gaji Bruto</td>
									<td colspan="2" align="center">Tunjangan</td>
									<td rowspan="2" width="70">Gaji Netto</td>
									<td colspan="2" align="center">T.H.R</td>
									<td rowspan="2" width="70">Pend.Lain/ Pinjaman</td>
									<td rowspan="2" width="70">Yang Diterima</td>
									<td rowspan="2" >Transfer BCA</td>
									<td rowspan="2" width="70">Tunai</td>
								</tr>
								<tr>
									<td>Jabatan</td>
									<td>Kesehatan</td>
									<td>Bruto</td>
									<td>Netto</td>
								</tr>
EOD;
				$print .= <<<EOD
					<tr>
						<td>$No_urut</td>
						<td>$row[NIK]</td>
						<td>$row[NAMA]</td>
						<td>$row[PANGKAT]</td>
						<td>$GajiBruto</td>
						<td>$row[TUNJ_JAB]</td>
						<td>$row[TUNJ_KES]</td>
						<td>$GajiNetto</td>
						<td>$row[THR]</td>
						<td>$BrutoTHR</td>
						<td>$KolomExtra</td>
						<td>$Total</td>
						<td>$transfer</td>
						<td>$tunai</td>
					</tr>

EOD;
			} else {
				$print .= <<<EOD
					<tr>
						<td>$No_urut</td>
						<td>$row[NIK]</td>
						<td>$row[NAMA]</td>
						<td>$row[PANGKAT]</td>
						<td>$GajiBruto</td>
						<td>$row[TUNJ_JAB]</td>
						<td>$row[TUNJ_KES]</td>
						<td>$GajiNetto</td>
						<td>$row[THR]</td>
						<td>$BrutoTHR</td>
						<td>$KolomExtra</td>
						<td>$Total</td>
						<td>$transfer</td>
						<td>$tunai</td>
					</tr>
					

EOD;
			
}
		}
		if ($i == $ndata && $datapertama == false) {
			$TotalGajiBruto = rupiah($TotalGajiBruto);
			$TotalTunjJab = rupiah($TotalTunjJab);
			$TotalTunjKes = rupiah($TotalTunjKes);
			$TotalGajiNetto = rupiah($TotalGajiNetto);
			$TotalTHR = rupiah($TotalTHR);
			$TotalTHRBruto = rupiah($TotalTHRBruto);
			$TotalKolomExtra = rupiah($TotalKolomExtra);
			$TotalYangditerima = rupiah($TotalYangditerima);
			$TotalBCA = rupiah($TotalBCA);
			$TotalTunai = rupiah($TotalTunai);
			$print .= <<<EOD
				<tr>
						<td colspan="4" align="center">Total</td>
						<td>$TotalGajiBruto</td>
						<td>$TotalTunjJab</td>
						<td>$TotalTunjKes</td>
						<td>$TotalGajiNetto</td>
						<td>$TotalTHR</td>
						<td>$TotalTHRBruto</td>
						<td>$TotalKolomExtra</td>
						<td>$TotalYangditerima</td>
						<td>$TotalBCA</td>
						<td>$TotalTunai</td>
					</tr>
			</table>
EOD;
		}
	}
	
	$pdf->writeHTML($print, true, false, false, false, '');
}
$pdf->Output();
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>FULL REPORT THR</title>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<!-- Bootstrap CSS CDN -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
	<!-- Our Custom CSS -->

	<!-- Font Awesome JS -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js" integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous"></script>
	<script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous"></script>
	<link rel="stylesheet" type="text/css" href="../src/tampilan.css">
	<link rel="stylesheet" type="text/css" href="../src/View.css">
</head>

<body>
	<?php printSideBar() ?>
	<div id="content">
		<nav class="navbar navbar-expand-lg navbar-light bg-light">
			<div class="container-fluid">
				<a>FULL REPORT THR </a>
			</div>
		</nav>
		<form action="" method="post">
			<h5>Tanggal Cetak :</h5>
			<input type="date" name="mode">
			<div id="optional">
	</div>
			<br>
			<input type="submit" name="gogo" value="SUBMIT">

		</form>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
		<!-- Bootstrap JS -->
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>

		<script type="text/javascript">
			$(document).ready(function() {
				$('#sidebarCollapse').on('click', function() {
					$('#sidebar').toggleClass('active');
					$('#content').toggleClass('active');
				});
				$("input[type=radio][name=mode]").change(function() {
					var mode = parseInt($(this).val());
					if (mode == 2) {
						$("#optional").html("<h5>DEPT: </h5><input type='text' name='opt'>");
					} else if (mode == 3) {
						$("#optional").html("<h5>NIK: </h5><input type='text' name='opt'>");
					} else if (mode == 1) {
						$("#optional").html('');
					}

				});
			});
		</script>
		
</body>

</html>