<?php
$db = db_connect();
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
                     <form action="<?php echo base_url('a/aksesubah') ?>" method="post">
                        <div class="card-body">
                           <h4 class="card-title mt-3">Pengolahan Profil Akses</h4>
                           <h6 class="card-subtitle mb-5">masukkan perubahan detail data, lalu pilih tombol <code>Simpan Perubahan Data</code> untuk menyimpan perubahan detail data</h6>
                           <?php if(session()->getFlashData('gagal')){ ?>
                              <div class="alert alert-warning" role="alert"><i class="dripicons-wrong mr-2"></i><?php echo session()->getFlashData('gagal') ?></div>
                           <?php } ?>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Password Lama</label>
                              <div class="col-sm-10">
                                 <input type="password" class="form-control form-control-sm" name="plama" placeholder="Password Lama (Password Sekarang)" autofocus required>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Password Baru</label>
                              <div class="col-sm-10">
                                 <input type="password" class="form-control form-control-sm" name="pbaru" placeholder="Password Baru" required>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Password Baru (Ulang)</label>
                              <div class="col-sm-10">
                                 <input type="password" class="form-control form-control-sm" name="pkonfirmasi" placeholder="Konfirmasi Password Baru" required>
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
         <?php echo view('admin/part_footer') ?>
      </div>
   </div>
   <?php echo view('admin/part_script') ?>
</body>
</html>