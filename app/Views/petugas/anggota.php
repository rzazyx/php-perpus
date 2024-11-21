<?php
$db = db_connect();
$kelas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
$kode = "";
$ada = true;
$x = 1;
while ($ada) {
   if ($x < 10) {
      $kode = date('Y') . '00' . $x . date('m');
   } else if ($x < 100) {
      $kode = date('Y') . '0' . $x . date('m');
   } else {
      $kode = date('Y') . $x . date('m');
   }
   $cek = $db->query("select kodeanggota from anggota where kodeanggota = '" . $kode . "'")->getResultArray();
   if (count($cek) == 0) {
      $ada = false;
   } else {
      $x++;
   }
}
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
                        <a href="#cetak" data-toggle="modal" class="btn waves-effect waves-light btn-sm btn-primary" style="float: right;">Print Report</a>
                        <button type="button" data-toggle="modal" data-target="#tambah" class="btn waves-effect waves-light btn-sm btn-success" style="float: right;margin-right: 10px;">Add New Data</button>
                        <h4 class="card-title">Member Data Management</h4>
                        <h6 class="card-subtitle">Select the <code><i class="fa fa-edit"></i></code> button to view data details.</h6>
                        <div class="table-responsive">
                           <table id="zero_config" class="table table-striped table-bordered no-wrap">
                              <thead>
                                 <tr>
                                    <th>#</th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Gender</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>**</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php
                                 $n = 1;
                                 foreach ($data as $d) {
                                    $cek = $db->query("select count(*) as jumlah from transaksi where kodeanggota = '" . $d['kodeanggota'] . "'")->getRowArray()['jumlah'];
                                 ?>
                                    <tr>
                                       <td><?php echo $n++ ?></td>
                                       <td><?php echo $d['kodeanggota'] ?></td>
                                       <td><?php echo $d['nama'] . ' (' . $d['username'] . ')' ?></td>
                                       <td><?php echo $d['jekel'] ?></td>
                                       <td><?php echo $d['alamat'] . ', ' . $d['telepon'] ?></td>
                                       <td>
                                          <?php if ($d['status'] == '0') { ?>
                                             <span class="badge badge-danger">Inactive</span>
                                          <?php } else { ?>
                                             <span class="badge badge-success">Active</span>
                                          <?php } ?>
                                       </td>
                                       <td>
                                          <a href="<?php echo base_url('p/anggotadetail/' . $d['kodeanggota']) ?>" class="btn waves-effect waves-light btn-sm btn-warning"><i class="fa fa-edit"></i></a>
                                          <?php if ($cek == 0) { ?>
                                             <button type="button" data-toggle="modal" data-target="#hapus<?php echo $d['kodeanggota'] ?>" class="btn waves-effect waves-light btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                                          <?php } ?>
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
<div class="modal fade" id="tambah" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="myLargeModalLabel">Add New Data</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
         </div>
         <form method="post" action="<?php echo base_url('p/anggotasimpan') ?>">
            <input type="hidden" name="aksi" value="simpan">
            <input type="hidden" name="kode" value="<?php echo $kode ?>">
            <div class="modal-body">
               <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Code</label>
                  <div class="col-sm-9">
                     <input type="text" class="form-control form-control-sm" value="<?php echo $kode ?>" disabled>
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-3 col-form-label">NISN / NIP</label>
                  <div class="col-sm-9">
                     <input type="text" class="form-control form-control-sm" name="nisn" placeholder="Nomor Induk Siswa Nasional (NISN) / Nomor Induk Pegawai (NIP)" maxlength="21" onkeypress="return event.charCode >= 48 && event.charCode <= 57" required />
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Name</label>
                  <div class="col-sm-9">
                     <input type="text" class="form-control form-control-sm" name="nama" placeholder="Nama Lengkap Anggota" maxlength="63" required>
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Gender</label>
                  <div class="col-sm-9">
                     <select class="form-control form-control-sm" name="jekel" required>
                        <option>Male</option>
                        <option>Female</option>
                     </select>
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Grade</label>
                  <div class="col-sm-9">
                     <select class="form-control form-control-sm" name="kelas" required>
                        <option value="Staf">Staff / Teacher</option>
                        <optgroup label="7th (VII)">
                           <?php for ($i = 0; $i < count($kelas); $i++) { ?>
                              <option><?php echo "VII " . $kelas[$i] ?></option>
                           <?php } ?>
                        </optgroup>
                        <optgroup label="8th (VIII)">
                           <?php for ($i = 0; $i < count($kelas); $i++) { ?>
                              <option><?php echo "VIII " . $kelas[$i] ?></option>
                           <?php } ?>
                        </optgroup>
                        <optgroup label="9th(IX)">
                           <?php for ($i = 0; $i < count($kelas); $i++) { ?>
                              <option><?php echo "IX " . $kelas[$i] ?></option>
                           <?php } ?>
                        </optgroup>
                     </select>
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Phone</label>
                  <div class="col-sm-9">
                     <input type="text" class="form-control form-control-sm" name="telepon" placeholder="Nomor Telepon" maxlength="14" onkeypress="return event.charCode >= 48 && event.charCode <= 57" required />
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Email</label>
                  <div class="col-sm-9">
                     <input type="text" class="form-control form-control-sm" name="email" placeholder="Akun Email (jika ada)" maxlength="99" />
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Address</label>
                  <div class="col-sm-9">
                     <textarea class="form-control form-control-sm" name="alamat" placeholder="Alamat Lengkap" rows="3" style="resize: none;" required></textarea>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-success btn-sm">Save Data</button>
            </div>
         </form>
      </div>
   </div>
</div>
<?php foreach ($data as $d) { ?>
   <div class="modal fade" id="hapus<?php echo $d['kodeanggota'] ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
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
               <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
               <a href="<?php echo base_url('p/anggotahapus/' . $d['kodeanggota']) ?>" class="btn btn-danger btn-sm">Hapus Data</a>
            </div>
         </div>
      </div>
   </div>
<?php } ?>
<div class="modal fade" id="cetak" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="myLargeModalLabel">Print Data Report </h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
         </div>
         <form action="<?php echo base_url('cetakanggota') ?>" method="post" target="blank">
            <div class="modal-body">
               <p style="text-align: justify;">Select the data report period, then click the <code>Print Report</code> button.</p>
               <div class="form-group row">
                  <label class="col-sm-6 col-form-label">Month Period</label>
                  <div class="col-sm-6">
                     <select class="form-control form-control-sm" name="bulan" required>
                        <?php for ($i = 1; $i <= 12; $i++) { ?>
                           <option value="<?php echo $i ?>"><?php echo $bulan[$i] ?></option>
                        <?php } ?>
                     </select>
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-6 col-form-label">Year Period</label>
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
               <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-success btn-sm">Print Report</button>
            </div>
         </form>
      </div>
   </div>
</div>

</html>