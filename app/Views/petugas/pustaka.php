<?php
$db = db_connect();
$bulan = [1 => 'Januari', 'Februari', 'Maret', 'Aoril', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
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
                        <a href="#cetak" data-toggle="modal" class="btn waves-effect waves-light btn-sm btn-primary" style="float: right; margin-right: 10px;">Print Report</a>
                        <a href="#cetaklabel" data-toggle="modal" class="btn waves-effect waves-light btn-sm btn-warning" style="float: right; margin-right: 10px;">Print Label</a>
                        <a href="<?php echo base_url('p/pustakabaru') ?>" class="btn waves-effect waves-light btn-sm btn-success" style="float: right; margin-right: 10px;">Add New Data</a>
                        <h4 class="card-title mt-4">Library Data Management</h4>
                        <h6 class="card-subtitle mb-3">Select the <code>Add New Data</code> button to add new data. Select the <code>Print Label</code> button to print library labels. Select the <code>Print Report</code> button to print library reports. Select the <code><i class="fa fa-edit"></i></code> button to edit data details. Select the <code><i class="fa fa-trash"></i></code> button to delete data.</h6>
                        <div class="table-responsive">
                           <table id="zero_config" class="table table-striped table-bordered no-wrap">
                              <thead>
                                 <tr>
                                    <th>#</th>
                                    <th>Code</th>
                                    <th>Title</th>
                                    <th>Publisher</th>
                                    <th>Type</th>
                                    <th>Available</th>
                                    <th>Status</th>
                                    <th>**</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php
                                 $n = 1;
                                 foreach ($data as $d) { ?>
                                    <tr>
                                       <td><?php echo $n++ ?></td>
                                       <td><?php echo $d['kodepustaka'] ?></td>
                                       <td><?php echo $d['judul'] ?></td>
                                       <td><?php echo $d['penerbit'] ?></td>
                                       <td><?php echo $d['jenis'] ?></td>
                                       <td><?php echo number_format($d['eksemplar']) ?></td>
                                       <td>
                                          <?php if ($d['status'] == '0') { ?>
                                             <span class="badge badge-danger">Inactive</span>
                                          <?php } else { ?>
                                             <span class="badge badge-success">Active</span>
                                          <?php } ?>
                                       </td>
                                       <td>
                                          <a href="<?php echo base_url('p/pustakadetail/' . $d['kodepustaka']) ?>" class="btn waves-effect waves-light btn-sm btn-warning"><i class="fa fa-edit"></i></a>
                                          <button type="button" data-toggle="modal" data-target="#hapus<?php echo $d['kodepustaka'] ?>" class="btn waves-effect waves-light btn-sm btn-danger"><i class="fa fa-trash"></i></button>
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
<?php
foreach ($data as $d) {
   $cek = $db->query("select count(*) as jumlah from detailtransaksi where kodepustaka = '" . $d['kodepustaka'] . "'")->getRowArray()['jumlah'];
   if ($cek == 0) {
?>
      <div class="modal fade" id="hapus<?php echo $d['kodepustaka'] ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <h4 class="modal-title" id="myLargeModalLabel">Delete Data </h4>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
               </div>
               <div class="modal-body">
                  <p style="text-align: justify;"><code>IMPORTANT! Deleted data cannot be recovered. Deleting data will also cause related data to be removed as a form of adjustment and to prevent errors (<i>error</i>).</code> If you agree to this, select the <code>Delete Data</code> button to delete the data.</p>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Batal</button>
                  <a href="<?php echo base_url('p/pustakahapus/' . $d['kodepustaka']) ?>" class="btn btn-danger btn-sm">Hapus Data</a>
               </div>
            </div>
         </div>
      </div>
   <?php } ?>
<?php } ?>
<div class="modal fade" id="cetak" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="myLargeModalLabel">Cetak Laporan Data </h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
         </div>
         <form action="<?php echo base_url('cetakpustaka') ?>" method="post" target="blank">
            <div class="modal-body">
               <p style="text-align: justify;">Pilih jenis dan periode laporan data, lalu pilih tombol <code>Cetak Laporan</code></p>
               <div class="form-group row">
                  <label class="col-sm-6 col-form-label">Jenis Laporan</label>
                  <div class="col-sm-6">
                     <select class="form-control form-control-sm" name="jenis" onchange="pilihan(this.value);" required>
                        <option value="detail">Detail Pustaka</option>
                        <option value="pinjam">Pustaka Dipinjam</option>
                        <option value="status">Status Pustaka</option>
                     </select>
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-6 col-form-label">Periode Bulan</label>
                  <div class="col-sm-6">
                     <select class="form-control form-control-sm" name="bulan" id="bulan" disabled required>
                        <?php for ($i = 1; $i <= 12; $i++) { ?>
                           <option value="<?php echo $i ?>"><?php echo $bulan[$i] ?></option>
                        <?php } ?>
                     </select>
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-6 col-form-label">Periode Tahun</label>
                  <div class="col-sm-6">
                     <select class="form-control form-control-sm" name="periode" id="periode" disabled required>
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
<div class="modal fade" id="cetaklabel" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="myLargeModalLabel">Cetak Label Pustaka </h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
         </div>
         <form action="<?php echo base_url('cetaklabel') ?>" method="post" target="blank">
            <div class="modal-body">
               <p style="text-align: justify;">Pilih pustaka dan jumlah label, lalu pilih tombol <code>Cetak Label</code></p>
               <div class="form-group row">
                  <label class="col-sm-2 col-form-label">Pustaka</label>
                  <div class="col-sm-10">
                     <select class="form-control form-control-sm" name="pustaka" required>
                        <?php
                        foreach ($data as $d) {
                           $p = $db->query("select belakang from pengarang where kodepustaka = '" . $d['kodepustaka'] . "' order by kodepengarang asc")->getRowArray()['belakang'];
                        ?>
                           <option value="<?php echo $d['kodepustaka'] ?>"><?php echo $d['judul'] . ' (' . $p . ', ' . $d['tahun'] . ')'; ?></option>
                        <?php } ?>
                     </select>
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-2 col-form-label">Jumlah</label>
                  <div class="col-sm-2">
                     <input type="number" name="jumlah" min="1" value="1" class="form-control form-control-sm" required>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Batal</button>
               <button type="submit" class="btn btn-success btn-sm">Cetak Label</button>
            </div>
         </form>
      </div>
   </div>
</div>
<script type="text/javascript">
   function pilihan(x) {
      if (x == 'detail') {
         document.getElementById("bulan").disabled = true;
         document.getElementById("periode").disabled = true;
      } else {
         document.getElementById("bulan").disabled = false;
         document.getElementById("periode").disabled = false;
      }
   }
</script>

</html>