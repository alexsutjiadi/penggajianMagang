<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['pathKota'])) {
    header("Location: ../pilihKota.php");
}


?>
<!DOCTYPE html>
<html>

<head>
    <title>Hitung PPH</title>

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
    <div class="wrapper">
        <!-- Sidebar  -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <button type="button" id="sidebarCollapse" class="btn btn-info">
                    <i class="fas fa-align-left"></i>
                </button>
            </div>
            <ul class="list-unstyled components">
                <!-- <img src="img/rtn.jpg" /> -->
                <li class="active">
                    <a href="#homeSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        Maintain Input MASTER
                    </a>
                    <ul class="collapse list-unstyled" id="homeSubmenu">
                        <li>
                            <a href='inputMaster.php'>Input Master</a>
                        </li>
                        <a href='payrollMasterFile.php'>Manage Master Gaji</a>
                        <li>
                            <a href='alamatDanNpwp.php'>Alamat & N.P.W.P</a>
                        </li>
                        <a href='masterBCA.php'>Master B.C.A</a>
                        <li>
                            <a href='showNamaGolongan.php'>Golongan</a>
                        </li>
                        <a href='inputGajiBaru.php'>Gaji Baru</a>
                        <li>
                            <a href='inputTunjanganJabatan.php'>Input Data Lain</a>
                        </li>
                    </ul>
                </li>
                <li class="active">
                    <a href="#pageSubTHRBONUS" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        THR BONUS
                    </a>
                    <ul class="collapse list-unstyled" id="pageSubTHRBONUS">
                        <li>
                            <a href='inputTHR.php'>Input THR</a>
                        </li>
                        <li>
                            <a href='inputBonus.php'>Input Bonus</a>
                        </li>
                    </ul>
                </li>
                <li class="active">
                    <a href="#pageSubpangkat" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        Manage Pangkat
                    </a>
                    <ul class="collapse list-unstyled" id="pageSubpangkat">
                        <li>
                            <a href='masterPangkatK1.php' class="w3-bar-item w3-button">K1 </a>
                        </li>
                        <li>
                            <a href='masterPangkatK2.php' class="w3-bar-item w3-button">K2 </a>
                        </li>
                        <li>
                            <a href='masterPangkatK3.php' class="w3-bar-item w3-button">K3 </a>
                        </li>
                        <li>
                            <a href='master4Bplus.php' class="w3-bar-item w3-button">4B - TM </a>
                        </li>
                </li>
            </ul>
            <li class="active">
                <a href="../pilihKota.php">Pilih Kota</a>
            </li>
            <li class="active">
                <a href="hitungPPH.php">Hitung PPH</a>
            </li>
        </nav>
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <a>HITUNG PPH</a>
                </div>
            </nav>
            <form method="post" action="../src/cekPangkat.php">
                <input type="submit" value="hitungPPH" name="hitungPPH">
                <input type="submit" value="CLEAAR" name="clear">
            </form>

            <?php
            $db = dbase_open($_SESSION['pathKota'] . 'PPH.DBF', 0);
            $dbgaji = dbase_open($_SESSION['pathKota'] . 'GAJI.DBF', 0);
            $ndata = dbase_numrecords($db);
            if ($ndata != 0) {
                ?>
                <table width="100%" border="1" id="myTable">
                    <tr>
                        <th>NO. DEPT</th>
                        <th>NIK</th>
                        <th>NAMA</th>
                        <th>PTKP</th>
                        <th>PKP</th>
                        <th>PPH21</th>
                        <th>BLN</th>
                    </tr>
                    <?php
                    for ($i = 1; $i <= $ndata; $i++) { ?>
                        <tr>
                            <?php $row = dbase_get_record_with_names($db, $i);
                            $rowgaji = dbase_get_record_with_names($dbgaji, $i); ?>
                            <td>
                                <?php echo $row['DEPT']; ?>
                            </td>
                            <td>
                                <?php echo $row['NIK'] ?>
                            </td>
                            <td>
                                <?php echo $rowgaji['NAMA']; ?>
                            </td>
                            <td>
                                <?php echo $row['PTKP']; ?>
                            </td>
                            <td>
                                <?php echo $row['PKP']; ?>
                            </td>
                            <td>
                                <?php echo $row['PPH_21']; ?>
                            </td>
                            <td>
                                <?php echo $row['YTD_BLN']; ?>
                            </td>
                        </tr>
                    <?php }
                }
                dbase_close($db);
                dbase_close($dbgaji) ?>


            </table>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
            <!-- Bootstrap JS -->
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>

            <script type="text/javascript">
                $(document).ready(function() {
                    $('#sidebarCollapse').on('click', function() {
                        $('#sidebar').toggleClass('active');
                    });
                });
            </script>
</body>

</html>