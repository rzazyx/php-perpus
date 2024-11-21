<?php
$db = db_connect();
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<?php echo view('anggota/part_head') ?>

<body>
   <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
      <?php echo view('anggota/part_sidebar') ?>
      <div class="page-wrapper">
         <div class="container-fluid">
            <div class="row">
               <div class="col-12">
                  <div class="card">
                     <form action="<?php echo base_url('ag/aksesubah') ?>" method="post">
                        <div class="card-body">
                           <h4 class="card-title mt-3">Profile Access Management</h4>
                           <h6 class="card-subtitle mb-5">Enter the changes to the data details, then click the <code>Save Data Changes</code> button to save the changes to the data details</h6>
                           <?php if (session()->getFlashData('gagal')) { ?>
                              <div class="alert alert-warning" role="alert"><i class="dripicons-wrong mr-2"></i><?php echo session()->getFlashData('gagal') ?></div>
                           <?php } ?>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Old Password</label>
                              <div class="col-sm-10">
                                 <input type="password" class="form-control form-control-sm" name="plama" placeholder="Old Password (Current Password)" autofocus required>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">New Password</label>
                              <div class="col-sm-10">
                                 <input type="password" class="form-control form-control-sm" name="pbaru" placeholder="New Password" required>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Confirm New Password</label>
                              <div class="col-sm-10">
                                 <input type="password" class="form-control form-control-sm" name="pkonfirmasi" placeholder="Confirm New Password" required>
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
         <?php echo view('anggota/part_footer') ?>
      </div>
   </div>
   <?php echo view('anggota/part_script') ?>
</body>

</html>