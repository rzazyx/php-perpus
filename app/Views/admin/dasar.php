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
                     <form action="<?php echo base_url('a/dasarubah') ?>" method="post">
                        <div class="card-body">
                           <h4 class="card-title mt-3">Pengolahan Profil Sistem</h4>
                           <h6 class="card-subtitle mb-5">masukkan perubahan detail data, lalu pilih tombol <code>Simpan Perubahan Data</code> untuk menyimpan perubahan detail data</h6>
                           <div class="row">
                              <div class="col-10">
                                 <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Nama Sistem</label>
                                    <div class="col-sm-9">
                                       <input type="text" class="form-control form-control-sm" name="nama" placeholder="Nama Sistem" maxlength="18" value="<?php echo $data['nama'] ?>" required>
                                    </div>
                                 </div>
                                 <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Tarif Denda</label>
                                    <div class="col-sm-9">
                                       <input type="number" class="form-control form-control-sm" name="denda" placeholder="Tarif Denda Peminjaman" min="100" value="<?php echo $data['denda'] ?>" required>
                                    </div>
                                 </div>
                                 <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Kepala Sekolah (Aktif)</label>
                                    <div class="col-sm-9">
                                       <input type="text" class="form-control form-control-sm" name="kepsek" placeholder="Nama Kepala Sekolah Aktif" maxlength="63" value="<?php echo $data['kepsek'] ?>" required>
                                    </div>
                                 </div>
                                 <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">NIP Kepala Sekolah (Aktif)</label>
                                    <div class="col-sm-9">
                                       <input type="text" class="form-control form-control-sm" name="nip" placeholder="NIP Kepala Sekolah Aktif" maxlength="27" value="<?php echo $data['nipkepsek'] ?>" required>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-2">
                                 <img src="<?php echo base_url('assets/gambar/'.$data['logo']) ?>" width="100%">
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