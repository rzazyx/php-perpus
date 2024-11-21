<?php
$db = db_connect();
$kode = "";
$ada = true;
$x = 1;
while($ada){
   if($x < 10){
      $kode = "P".date('Y').'.00'.$x;
   }else if($x < 100){
      $kode = "P".date('Y').'.0'.$x;
   }else{
      $kode = "P".date('Y').'.'.$x;
   }
   $cek = $db->query("select kodepetugas from petugas where kodepetugas = '".$kode."'")->getResultArray();
   if(count($cek) == 0){
      $ada = false;
   }else{
      $x++;
   }
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
                        <button type="button" data-toggle="modal" data-target="#tambah" class="btn waves-effect waves-light btn-sm btn-success" style="float: right;">Tambah Data Baru</button>
                        <h4 class="card-title mt-5">Pengolahan Data Pustakawan</h4>
                        <h6 class="card-subtitle">pilih tombol <code>Tambah Data Baru</code> untuk menambahkan data baru. pilih tombol <code><i class="fa fa-edit"></i></code> untuk mengubah detail data. pilih tombol <code><i class="fa fa-trash"></i></code> untuk menghapus data</h6>
                        <div class="table-responsive">
                           <table id="zero_config" class="table table-striped table-bordered no-wrap">
                              <thead>
                                 <tr>
                                    <th>#</th>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Jekel</th>
                                    <th>Alamat</th>
                                    <th>Status</th>
                                    <th>**</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php
                                 $n = 1;
                                 foreach ($data as $d) {
                                    $x = str_replace(".", "", $d['kodepetugas']);
                                    ?>
                                    <tr>
                                       <td><?php echo $n++ ?></td>
                                       <td><?php echo $d['kodepetugas'] ?></td>
                                       <td><?php echo $d['nama'].' ('.$d['username'].')' ?></td>
                                       <td><?php echo $d['jekel'] ?></td>
                                       <td><?php echo $d['alamat'].', '.$d['telepon'] ?></td>
                                       <td>
                                          <?php if($d['status'] == '0'){?>
                                             <span class="badge badge-danger">nonaktif</span>
                                          <?php }else{ ?>
                                             <span class="badge badge-success">aktif</span>
                                          <?php } ?>
                                       </td>
                                       <td>
                                          <button type="button" data-toggle="modal" data-target="#detail<?php echo $x ?>" class="btn waves-effect waves-light btn-sm btn-warning"><i class="fa fa-edit"></i></button>
                                          <button type="button" data-toggle="modal" data-target="#hapus<?php echo $x ?>" class="btn waves-effect waves-light btn-sm btn-danger"><i class="fa fa-trash"></i></button>
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
<div class="modal fade" id="tambah" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="myLargeModalLabel">Tambah Data Baru</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
         </div>
         <form method="post" action="<?php echo base_url('a/pustakawansimpan') ?>">
            <input type="hidden" name="aksi" value="simpan">
            <input type="hidden" name="kode" value="<?php echo $kode ?>">
            <div class="modal-body">
               <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Kode</label>
                  <div class="col-sm-9">
                     <input type="text" class="form-control form-control-sm" value="<?php echo $kode ?>" disabled>
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-3 col-form-label">NIP</label>
                  <div class="col-sm-9">
                     <input type="text" class="form-control form-control-sm" name="nip" placeholder="Nomor Induk Pegawai (NIP)" maxlength="18" onkeypress="return event.charCode >= 48 && event.charCode <= 57" />
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Nama</label>
                  <div class="col-sm-9">
                     <input type="text" class="form-control form-control-sm" name="nama" placeholder="Nama Lengkap Pustakawan" maxlength="63" required>
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Jenis Kelamin</label>
                  <div class="col-sm-9">
                     <select class="form-control form-control-sm" name="jekel" required>
                        <option>Pria</option>
                        <option>Wanita</option>
                     </select>
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Telepon</label>
                  <div class="col-sm-9">
                     <input type="text" class="form-control form-control-sm" name="telepon" placeholder="Nomor Telepon" maxlength="14" onkeypress="return event.charCode >= 48 && event.charCode <= 57" required />
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Alamat</label>
                  <div class="col-sm-9">
                     <textarea class="form-control form-control-sm" name="alamat" placeholder="Alamat Lengkap" rows="3" style="resize: none;" required></textarea>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Batal</button>
               <button type="submit" class="btn btn-success btn-sm">Simpan Data</button>
            </div>
         </form>
      </div>
   </div>
</div>
<?php
foreach ($data as $d) {
   $x = str_replace(".", "", $d['kodepetugas']);
   ?>
   <div class="modal fade" id="detail<?php echo $x ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-lg">
         <div class="modal-content">
            <div class="modal-header">
               <h4 class="modal-title" id="myLargeModalLabel">Detail Data </h4>
               <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form method="post" action="<?php echo base_url('a/pustakawansimpan') ?>">
               <input type="hidden" name="aksi" value="ubah">
               <input type="hidden" name="kode" value="<?php echo $d['kodepetugas'] ?>">
               <div class="modal-body">
                  <div class="form-group row">
                     <label class="col-sm-3 col-form-label">Kode</label>
                     <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" value="<?php echo $d['kodepetugas'] ?>" disabled>
                     </div>
                  </div>
                  <div class="form-group row">
                     <label class="col-sm-3 col-form-label">NIP</label>
                     <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" name="nip" placeholder="Nomor Induk Pegawai (NIP)" maxlength="18" onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                        value="<?php echo $d['nip'] ?>" required />
                     </div>
                  </div>
                  <div class="form-group row">
                     <label class="col-sm-3 col-form-label">Nama</label>
                     <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" name="nama" placeholder="Nama Lengkap Pustakawan" maxlength="63" value="<?php echo $d['nama'] ?>" required>
                     </div>
                  </div>
                  <div class="form-group row">
                     <label class="col-sm-3 col-form-label">Jenis Kelamin</label>
                     <div class="col-sm-9">
                        <select class="form-control form-control-sm" name="jekel" required>
                           <option <?php if($d['jekel'] == 'Pria'){echo "selected";} ?>>Pria</option>
                           <option <?php if($d['jekel'] == 'Wanita'){echo "selected";} ?>>Wanita</option>
                        </select>
                     </div>
                  </div>
                  <div class="form-group row">
                     <label class="col-sm-3 col-form-label">Telepon</label>
                     <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" name="telepon" placeholder="Nomor Telepon" maxlength="14" onkeypress="return event.charCode >= 48 && event.charCode <= 57" 
                        value="<?php echo $d['telepon'] ?>" required />
                     </div>
                  </div>
                  <div class="form-group row">
                     <label class="col-sm-3 col-form-label">Alamat</label>
                     <div class="col-sm-9">
                        <textarea class="form-control form-control-sm" name="alamat" placeholder="Alamat Lengkap" rows="3" style="resize: none;" required><?php echo $d['alamat'] ?></textarea>
                     </div>
                  </div>
                  <div class="form-group row">
                     <label class="col-sm-3 col-form-label">Status Akun</label>
                     <div class="col-sm-9">
                        <select class="form-control form-control-sm" name="status" required>
                           <option <?php if($d['status'] == '1'){echo "selected";} ?> value="1">Aktif</option>
                           <option <?php if($d['status'] == '0'){echo "selected";} ?> value="0">Nonaktif</option>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Batal</button>
                  <button type="submit" class="btn btn-success btn-sm">Simpan Data</button>
               </div>
            </form>
         </div>
      </div>
   </div>
   <div class="modal fade" id="hapus<?php echo $x ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <h4 class="modal-title" id="myLargeModalLabel">Hapus Data </h4>
               <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
               <p style="text-align: justify;"><code>PENTING! Data yang telah dihapus tidak dapat dikembalikan. Menghapus data akan menyebabkan data terkait akan ikut dihapus sebagai bentuk penyesuaian dan menghindarkan dari kesalahan (<i>error</i>)</code>. Jika setuju dengan hal ini, pilih tombol <code>Hapus Data</code> untuk menghapus data</p>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Batal</button>
               <a href="<?php echo base_url('a/pustakawanehapus/'.$d['kodepetugas']) ?>" class="btn btn-danger btn-sm">Hapus Data</a>
            </div>
         </div>
      </div>
   </div>
<?php } ?>
</html>