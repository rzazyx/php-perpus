<?php
$db = db_connect();
$daftarbulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$daftartahun = date('Y');
$cek = $db->query("select count(*) as jumlah from kunjungan")->getRowArray()['jumlah'];
if ($cek > 0) {
   $daftartahun = $db->query("select year(waktu) as tahun from kunjungan order by waktu asc limit 1")->getRowArray()['tahun'];
}
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<?php echo view('petugas/part_head') ?>

<body>
   <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
      <?php echo view('petugas/part_sidebar') ?>
      <div class="page-wrapper">
         <div class="container-fluid">
            <div class="row">
               <div class="col-12">
                  <div class="card">
                     <div class="card-body">
                        <form class="row col-3" action="<?php echo base_url('p/kunjungantampil') ?>" method="post" style="float: right;">
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
                                                } ?>><?php echo $i ?></option>
                                    <?php } ?>
                                 </select>
                                 <div class="input-group-append">
                                    <button class="btn btn-primary btn-sm" onclick=" window.open('<?php echo base_url('cetakkunjungan/' . $tahun) ?>','_blank')" type="button">Cetak</button>
                                 </div>
                              </div>
                           </div>
                        </form>
                        <h4 class="card-title mt-4">Library Visit Cycle</h4>
                        <h6 class="card-subtitle mb-3">Select the period (Month and Year) of the data, then choose the <code>Print Report</code> button to print the report</h6>
                        <div class="table-responsive">
                           <table id="zero_config" class="table table-striped table-bordered no-wrap">
                              <thead>
                                 <tr>
                                    <th>#</th>
                                    <th>Time</th>
                                    <th>Name</th>
                                    <th>Class</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php
                                 $n = 1;
                                 foreach ($data as $d) {
                                 ?>
                                    <tr>
                                       <td><?php echo $n++ ?></td>
                                       <td><?php echo date('d/m/Y H:i:s', strtotime($d['waktu'])) ?></td>
                                       <td><?php echo $d['nama'] ?></td>
                                       <td><?php echo $d['kelas'] ?></td>
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
         <?php echo view('petugas/part_footer') ?>
      </div>
   </div>
   <?php echo view('petugas/part_script') ?>
</body>

</html>