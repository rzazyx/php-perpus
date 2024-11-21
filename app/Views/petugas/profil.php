<?php
$db = db_connect();
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
                     <form action="<?php echo base_url('p/profilubah') ?>" method="post">
                        <div class="card-body">
                           <h4 class="card-title mt-3">Pengolahan Profil Pengguna</h4>
                           <h6 class="card-subtitle mb-5">masukkan perubahan detail data, lalu pilih tombol <code>Simpan Perubahan Data</code> untuk menyimpan perubahan detail data</h6>
                           <input type="hidden" name="kode" value="<?php echo $data['kodepetugas'] ?>">
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">ID Profil</label>
                              <div class="col-sm-10">
                                 <input type="text" class="form-control form-control-sm" value="<?php echo $data['kodepetugas'] ?>" disabled>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">NIP</label>
                              <div class="col-sm-10">
                                 <input type="text" class="form-control form-control-sm" name="nip" placeholder="Nomor Induk Pegawai (NIP)" maxlength="18" onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                 value="<?php echo $data['nip'] ?>" />
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Nama / Username</label>
                              <div class="col-sm-7">
                                 <input type="text" class="form-control form-control-sm" name="nama" placeholder="Nama Lengkap Pengguna" maxlength="63" value="<?php echo $data['nama'] ?>" required>
                              </div>
                              <div class="col-sm-3">
                                 <input type="text" class="form-control form-control-sm" name="username" placeholder="Username Pengguna" maxlength="99" value="<?php echo $data['username'] ?>" required>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Telepon</label>
                              <div class="col-sm-10">
                                 <input type="text" class="form-control form-control-sm" name="telepon" placeholder="Nomor Telepon" maxlength="14" onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                 value="<?php echo $data['telepon'] ?>" required />
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Alamat</label>
                              <div class="col-sm-10">
                                 <textarea class="form-control form-control-sm" name="alamat" placeholder="Alamat Lengkap" rows="3" style="resize: none;" required><?php echo $data['alamat'] ?></textarea>
                              </div>
                           </div>
                        </div>
                        <div class="modal-footer">
                           <button type="submit" class="btn btn-success btn-sm">Simpan Perubahan Data</button>
                        </div>
                     </form>
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