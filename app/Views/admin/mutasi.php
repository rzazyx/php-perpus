<?php
$db = db_connect();
$daftarbulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$daftartahun = date('Y');
$cek = $db->query("select count(*) as jumlah from transaksi")->getRowArray()['jumlah'];
if ($cek > 0) {
   $daftartahun = $db->query("select year(waktu) as tahun from transaksi order by waktu asc limit 1")->getRowArray()['tahun'];
}
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<?php echo view('admin/part_head') ?>

<body>
   <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
      <?php echo view('admin/part_sidebar') ?>
      <div class="page-wrapper">
         <div class="container-fluid">
            <div class="row">
               <div class="col-12">
                  <div class="card">
                     <div class="card-body">
                        <form class="row col-3" action="<?php echo base_url('a/mutasitampil') ?>" method="post" style="float: right;">
                           <div class="input-group">
                              <div class="custom-file">
                                 <select class="form-control form-control-sm" name="bulan" required onchange="this.form.submit()">
                                    <?php for ($i = 1; $i <= count($daftarbulan); $i++) { ?>
                                       <option value="<?php echo $i ?>" <?php if ((int)$bulan == $i) {
                                                                           echo "selected";
                                                                        } ?>><?php echo $daftarbulan[$i] ?></option>
                                    <?php } ?>
                                 </select>
                                 <select class="form-control form-control-sm" name="tahun" required onchange="this.form.submit()">
                                    <?php for ($i = $daftartahun; $i <= date('Y'); $i++) { ?>
                                       <option <?php if ($tahun == $i) {
                                                   echo "selected";
                                                } ?>><?php echo $daftartahun ?></option>
                                    <?php } ?>
                                 </select>
                              </div>
                           </div>
                        </form>
                        <h4 class="card-title mt-4">Library Transfer Cycle</h4>
                        <h6 class="card-subtitle mb-3">Select a period (Month and Year) to display the data.</h6>
                        <div class="table-responsive">
                           <table id="zero_config" class="table table-striped table-bordered no-wrap">
                              <thead>
                                 <tr>
                                    <th>#</th>
                                    <th>Code</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Description</th>

                                 </tr>
                              </thead>
                              <tbody>
                                 <?php
                                 $n = 1;
                                 foreach ($data as $d) {
                                    $tit = $db->query("select count(*) as jumlah from detailtransaksi where kodetransaksi = '" . $d['kodetransaksi'] . "'")->getRowArray()['jumlah'];
                                    $eks = $db->query("select sum(jumlah) as jumlah from detailtransaksi where kodetransaksi = '" . $d['kodetransaksi'] . "'")->getRowArray()['jumlah'];
                                    $keterangan = number_format($tit) . ' Pustaka, ' . number_format($eks) . ' Eksemplar';
                                 ?>
                                    <tr>
                                       <td><?php echo $n++ ?></td>
                                       <td><?php echo $d['kodetransaksi'] ?></td>
                                       <td><?php echo strtoupper($d['jenis']) ?></td>
                                       <td><?php echo date('d/m/Y', strtotime($d['waktu'])) ?></td>
                                       <td>
                                          <?php echo substr($d['keterangan'], 0, 100) . '.....' ?><br>
                                          <small><?php echo $keterangan ?></small>
                                       </td>
                                    </tr>
                                 <?php } ?>
                              </tbody>
                           </table>
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
</body>

</html>