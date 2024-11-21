<?php
$db = db_connect();
$b1 = (int)date('m');
$t1 = (int)date('Y');
$b2 = (int)date('m');
$t2 = (int)date('Y');
if ((int)$b1 == 1) {
   $b2 = 12;
   $t2--;
} else {
   $b2--;
}
$j1 = $db->query("select ifnull(count(*), 0) as jumlah from pustaka")->getRowArray()['jumlah'];
$j2 = $db->query("select ifnull(count(*), 0) as jumlah from anggota")->getRowArray()['jumlah'];
$p31 = $db->query("select ifnull(count(*), 0) as jumlah from kunjungan where month(waktu) = '" . $b1 . "' and year(waktu) = '" . $t1 . "'")->getRowArray()['jumlah'];
$p32 = $db->query("select ifnull(count(*), 0) as jumlah from kunjungan where month(waktu) = '" . $b2 . "' and year(waktu) = '" . $t2 . "'")->getRowArray()['jumlah'];
$p3x = 100;
if (abs($p31 - $p32) > 0) {
   $selisih = abs($p31 - $p32);
   if ($p31 == 0 || $p32 == 0) {
      $p3x = 100;
   } else {
      $p3x = $selisih / $p32 * 100;
   }
}
$p41 = $db->query("select ifnull(count(*), 0) as jumlah from transaksi where jenis = 'keluar' and month(waktu) = '" . $b1 . "' and year(waktu) = '" . $t1 . "' and kodeanggota > 0")->getRowArray()['jumlah'];
$p42 = $db->query("select ifnull(count(*), 0) as jumlah from transaksi where jenis = 'keluar' and month(waktu) = '" . $b2 . "' and year(waktu) = '" . $t2 . "' and kodeanggota > 0")->getRowArray()['jumlah'];
$p4x = 100;
if (abs($p41 - $p42) > 0) {
   $selisih = abs($p41 - $p42);
   if ($p41 == 0 || $p42 == 0) {
      $p4x = 100;
   } else {
      $p4x = $selisih / $p42 * 100;
   }
}
$tpinjam = $db->query("select ifnull(sum(jumlah),0) as jumlah from detailtransaksi join transaksi on detailtransaksi.kodetransaksi = transaksi.kodetransaksi where month(transaksi.waktu) = '" . $b1 . "' and year(transaksi.waktu) = '" . $t1 . "' and detailtransaksi.status = '7'")->getRowArray()['jumlah'];
$pustaka = $db->query("select detailtransaksi.kodepustaka, ifnull(sum(jumlah),0) as jumlah from detailtransaksi join transaksi on detailtransaksi.kodetransaksi = transaksi.kodetransaksi where month(transaksi.waktu) = '12' and year(transaksi.waktu) = '2023' and detailtransaksi.status = '7' group by detailtransaksi.kodepustaka order by jumlah desc limit 4")->getResultArray();
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<?php echo view('admin/part_head') ?>

<body>
   <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
      <?php echo view('admin/part_sidebar') ?>
      <div class="page-wrapper">
         <div class="page-breadcrumb">
            <div class="row">
               <div class="col-7 align-self-center">
                  <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Welcome, Administrator!</h3>
                  <div class="d-flex align-items-center">
                     <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                           <li class="breadcrumb-item"><a href="index.html">Data System Dashboard</a>
                           </li>
                        </ol>
                     </nav>
                  </div>
               </div>
            </div>
         </div>
         <div class="container-fluid">
            <div class="card-group">
               <div class="card border-right" style="border-color: #c6fcae;">
                  <div class="card-body" style="background-color: #c6fcae;">
                     <div class="d-flex d-lg-flex d-md-block align-items-center">
                        <div>
                           <h2 class="text-dark mb-1 w-100 text-truncate font-weight-medium"><?php echo number_format($j1) ?></h2>
                           <h6 class="font-weight-normal mb-0 w-100 text-truncate" style="color: #000000; opacity: 1;">Library Data</h6>
                        </div>
                        <div class="ml-auto mt-md-3 mt-lg-0">
                           <span style="color: #000000; opacity: 1;"><i data-feather="book"></i></span>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="card border-right" style="border-color: #bfedab;">
                  <div class="card-body" style="background-color: #bfedab;">
                     <div class="d-flex d-lg-flex d-md-block align-items-center">
                        <div>
                           <h2 class="text-dark mb-1 w-100 text-truncate font-weight-medium"><?php echo number_format($j2) ?></h2>
                           <h6 class="font-weight-normal mb-0 w-100 text-truncate" style="color: #000000; opacity: 1;">Member Data</h6>
                        </div>
                        <div class="ml-auto mt-md-3 mt-lg-0">
                           <span style="color: #000000; opacity: 1;"><i data-feather="users"></i></span>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="card border-right" style="border-color: #b2f57f;">
                  <div class="card-body" style="background-color: #b2f57f;">
                     <div class="d-flex d-lg-flex d-md-block align-items-center">
                        <div>
                           <div class="d-inline-flex align-items-center">
                              <h2 class="text-dark mb-1 font-weight-medium"><?php echo number_format($p31) ?></h2>
                              <?php if ($p31 == $p32) { ?>
                                 <span class="badge bg-primary font-12 text-white font-weight-medium badge-pill ml-2 d-lg-block d-md-none" title="Consistent with last month">Stable</span>
                              <?php } else if ($p31 < $p32) { ?>
                                 <span class="badge bg-danger font-12 text-white font-weight-medium badge-pill ml-2 d-lg-block d-md-none" title="Down from last mont">-<?php echo number_format($p3x) . '%' ?></span>
                              <?php } else { ?>
                                 <span class="badge bg-success font-12 text-white font-weight-medium badge-pill ml-2 d-lg-block d-md-none" title="Higher than last month">+<?php echo number_format($p3x) . '%' ?></span>
                              <?php } ?>
                           </div>
                           <h6 class="font-weight-normal mb-0 w-100 text-truncate" style="color: #000000; opacity: 1;">Visits This Month</h6>
                        </div>
                        <div class="ml-auto mt-md-3 mt-lg-0">
                           <span style="color: #000000; opacity: 1;"><i data-feather="activity"></i></span>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="card border-right" style="border-color: #70de40;">
                  <div class="card-body" style="background-color: #70de40;">
                     <div class="d-flex d-lg-flex d-md-block align-items-center">
                        <div>
                           <div class="d-inline-flex align-items-center">
                              <h2 class="text-dark mb-1 font-weight-medium"><?php echo number_format($p41) ?></h2>
                              <?php if ($p41 == $p42) { ?>
                                 <span class="badge bg-primary font-12 text-white font-weight-medium badge-pill ml-2 d-lg-block d-md-none" title="Consistent with last month">Stable</span>
                              <?php } else if ($p41 < $p42) { ?>
                                 <span class="badge bg-danger font-12 text-white font-weight-medium badge-pill ml-2 d-lg-block d-md-none" title="Down from last mont">-<?php echo number_format($p4x) . '%' ?></span>
                              <?php } else { ?>
                                 <span class="badge bg-success font-12 text-white font-weight-medium badge-pill ml-2 d-lg-block d-md-none" title="Higher than last month">+<?php echo number_format($p4x) . '%' ?></span>
                              <?php } ?>
                           </div>
                           <h6 class="font-weight-normal mb-0 w-100 text-truncate" style="color: #000000; opacity: 1;">Books Borrowed This Month</h6>
                        </div>
                        <div class="ml-auto mt-md-3 mt-lg-0">
                           <span style="color: #000000; opacity: 1;"><i data-feather="repeat"></i></span>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-lg-12 col-md-12">
                  <div class="card">
                     <div class="card-body" style="background-color: rgba(209, 235, 197, 0.7); /* Warna hijau dengan transparansi 0.7 */ border: 1px solid rgba(209, 235, 197, 0.7); /* Warna border dengan transparansi 0.7 */ border-radius: 4px;">
                        <div class="row">
                           <div class="col-lg-3 col-md-12">
                              <div class="card-body">
                                 <h4 class="card-title text-center">Favorite Books</h4>
                                 <canvas id="inicanvas"></canvas>
                                 <ul class="list-style-none mb-0">
                                    <?php
                                    $favorit = $db->query("select detailtransaksi.kodepustaka, ifnull(sum(jumlah),0) as jumlah from detailtransaksi join transaksi on detailtransaksi.kodetransaksi = transaksi.kodetransaksi where year(transaksi.waktu) = '" . date('Y') . "' and detailtransaksi.status = '7' group by detailtransaksi.kodepustaka order by jumlah desc limit 5")->getResultArray();
                                    $sisa = 100;
                                    $pilihanwarna = ['rgba(47,202,129,1)', 'rgba(47,202,129,0.7)', 'rgba(47,202,129,0.5)', 'rgba(47,202,129,0.3)', 'rgba(47,202,129,0.1)'];
                                    $x = 0;
                                    foreach ($favorit as $f) {
                                       $pt = $db->query("select * from pustaka where kodepustaka = '" . $f['kodepustaka'] . "'")->getRowArray();
                                       $jumlah = $db->query("select ifnull(sum(jumlah),0) as jumlah from detailtransaksi join transaksi on detailtransaksi.kodetransaksi = transaksi.kodetransaksi where year(transaksi.waktu) = '" . date('Y') . "' and detailtransaksi.status = '7' and detailtransaksi.kodepustaka = '" . $f['kodepustaka'] . "'")->getRowArray()['jumlah'];
                                    ?>
                                       <li style="font-size: 9pt;">
                                          <i class="fas fa-circle font-10 mr-2" style="color: <?php echo $pilihanwarna[$x++] ?>;"></i>
                                          <span class="text-muted"><?php echo substr($pt['judul'], 0, 27) . "... (" . $pt['tahun'] . ")" ?></span>
                                          <span class="text-dark float-right font-weight-medium"><?php echo number_format($jumlah) . ' eks' ?></span>
                                       </li>
                                    <?php } ?>
                                 </ul>
                              </div>
                           </div>
                           <div class="col-lg-9 col-md-12">
                              <div class="card-body">
                                 <h4 class="card-title text-center">Yearly Visit Graph <?php echo date('Y') ?></h4>
                                 <canvas id="myChart" style="width: 500px;height: 117px"></canvas>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <?php echo view('admin/part_footer') ?>
      </div>
   </div>
   <?php echo view('admin/part_script') ?>
   <?php
   $isiwarna = "";
   $isilabel = "";
   $isijumlah = "";
   $favorit = $db->query("select detailtransaksi.kodepustaka, ifnull(sum(jumlah),0) as jumlah from detailtransaksi join transaksi on detailtransaksi.kodetransaksi = transaksi.kodetransaksi where year(transaksi.waktu) = '" . date('Y') . "' and detailtransaksi.status = '7' group by detailtransaksi.kodepustaka order by jumlah desc limit 5")->getResultArray();
   $daftarwarna = ['rgba(47,202,129,1)', 'rgba(47,202,129,0.7)', 'rgba(47,202,129,0.5)', 'rgba(47,202,129,0.3)', 'rgba(47,202,129,0.1)'];
   $x = 0;
   foreach ($favorit as $f) {
      $pt = $db->query("select * from pustaka where kodepustaka = '" . $f['kodepustaka'] . "'")->getRowArray();
      $jumlah = $db->query("select ifnull(sum(jumlah),0) as jumlah from detailtransaksi join transaksi on detailtransaksi.kodetransaksi = transaksi.kodetransaksi where year(transaksi.waktu) = '" . date('Y') . "' and detailtransaksi.status = '7' and detailtransaksi.kodepustaka = '" . $f['kodepustaka'] . "'")->getRowArray()['jumlah'];
      $isiwarna .= "'" . $daftarwarna[$x++] . "',";
      $isilabel .= "'" . substr($pt['judul'], 0, 27) . "... (" . $pt['tahun'] . ")" . "',";
      $isijumlah .= $jumlah . ",";
   }

   $isiwarna = substr($isiwarna, 0, strlen($isiwarna) - 1);
   $isilabel = substr($isilabel, 0, strlen($isilabel) - 1);
   $isijumlah = substr($isijumlah, 0, strlen($isijumlah) - 1);

   $labelbulan = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
   $isibulan = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
   for ($i = 0; $i < 12; $i++) {
      $bulan = $i + 1;
      $tahun = (int)date('Y');
      $isibulan[$i] = $db->query("select ifnull(count(*),0) as jumlah from kunjungan where month(waktu) = '" . $bulan . "' and year(waktu) = '" . $tahun . "'")->getRowArray()['jumlah'];
   }
   ?>
   <script src="<?php echo base_url('assets/admin/dist/js/pages/chartjs/chartjs.init.js') ?>"></script>
   <script src="<?php echo base_url('assets/admin/libs/chart.js/dist/Chart.min.js') ?>"></script>
   <script>
      <?php

      $labelbulan = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
      $isibulan = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
      for ($i = 0; $i < 12; $i++) {
         $bulan = $i + 1;
         $tahun = (int)date('Y');
         $isibulan[$i] = $db->query("select ifnull(count(*),0) as jumlah from kunjungan where month(waktu) = '" . $bulan . "' and year(waktu) = '" . $tahun . "'")->getRowArray()['jumlah'];
      }
      ?>
      var ctx = document.getElementById("inicanvas").getContext("2d");
      var piechart = new Chart(ctx, {
         type: 'pie',
         data: {
            labels: [<?php echo $isilabel ?>],
            datasets: [{
               data: [<?php echo $isijumlah ?>],
               backgroundColor: [<?php echo $isiwarna ?>]
            }],
         },
         options: {
            legend: {
               display: false
            }
         }
      });
   </script>
   <script>
      var ctx = document.getElementById("myChart").getContext('2d');
      var myChart = new Chart(ctx, {
         type: 'bar',
         data: {
            labels: <?php echo json_encode($labelbulan) ?>,
            datasets: [{
               label: 'Kunjungan',
               data: <?php echo json_encode($isibulan) ?>,
               backgroundColor: 'rgba(0, 255, 170, 0.3)',
               borderColor: 'rgba(0, 255, 170, 1)',
               borderWidth: 1
            }]
         },
         options: {
            scales: {
               yAxes: [{
                  ticks: {
                     beginAtZero: true
                  }
               }],
               xAxes: [{
                  gridLines: {
                     display: false
                  }
               }],
               yAxes: [{
                  gridLines: {
                     display: false
                  }
               }]
            },
            legend: {
               display: false
            }
         }
      });
   </script>
</body>

</html>