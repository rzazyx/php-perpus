<?php
$db = db_connect();
$bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$tahun = date('Y');
$cek = $db->query("select * from transaksi")->getResultArray();
if (count($cek) > 0) {
   $tahun = $db->query("select year(waktu) as tahun from transaksi order by waktu asc limit 1")->getRowArray()['tahun'];
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
                        <a href="#cetak" data-toggle="modal" class="btn waves-effect waves-light btn-sm btn-primary" style="float: right;margin-right: 10px;">Cetak Laporan</a>
                        <div class="btn-group" style="float: right;">
                           <button type="button" class="btn btn-success waves-effect waves-light btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style=" right;margin-right: 10px;">Tambah Data Baru</button>
                           <div class="dropdown-menu">
                              <a class="dropdown-item" href="<?php echo base_url('p/mutasimasukbaru') ?>">Pustaka Masuk</a>
                              <a class="dropdown-item" href="<?php echo base_url('p/mutasikeluarbaru') ?>">Pustaka Keluar</a>
                           </div>
                        </div>
                        <h4 class="card-title mt-4">Siklus Mutasi Pustaka</h4>
                        <h6 class="card-subtitle mb-3">pilih tombol <code>Tambah Data Baru</code> untuk menambahkan data baru. pilih tombol <code>Cetak Laporan</code> untuk mencetak laporan data</h6>
                        <div class="table-responsive">
                           <table id="zero_config" class="table table-striped table-bordered no-wrap">
                              <thead>
                                 <tr>
                                    <th>#</th>
                                    <th>Kode</th>
                                    <th>Jenis</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
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
         <?php echo view('petugas/part_footer') ?>
      </div>
   </div>
   <?php echo view('petugas/part_script') ?>
</body>
<div class="modal fade" id="cetak" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="myLargeModalLabel">Cetak Laporan Data </h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
         </div>
         <form action="<?php echo base_url('cetakmutasi') ?>" method="post" target="blank">
            <div class="modal-body">
               <p style="text-align: justify;">Pilih periode laporan data, lalu pilih tombol <code>Cetak Laporan</code></p>
               <div class="form-group row">
                  <label class="col-sm-6 col-form-label">Periode Bulan</label>
                  <div class="col-sm-6">
                     <select class="form-control form-control-sm" name="bulan" required>
                        <?php for ($i = 1; $i <= 12; $i++) { ?>
                           <option value="<?php echo $i ?>"><?php echo $bulan[$i] ?></option>
                        <?php } ?>
                     </select>
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-6 col-form-label">Periode Tahun</label>
                  <div class="col-sm-6">
                     <select class="form-control form-control-sm" name="periode" required>
                        <?php for ($i = date('Y'); $i >= $tahun; $i--) { ?>
                           <option><?php echo $i ?></option>
                        <?php } ?>
                     </select>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Batal</button>
               <button type="submit" class="btn btn-success btn-sm">Cetak Laporan</button>
            </div>
         </form>
      </div>
   </div>
</div>

</html>