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
                     <form action="<?php echo base_url('ag/profilubah') ?>" method="post">
                        <div class="card-body">
                           <h4 class="card-title mt-3">User Profile Management</h4>
                           <h6 class="card-subtitle mb-5">Enter the changes to the data details, then click the <code>Save Data Changes</code> button to save the changes to the data details</h6>
                           <input type="hidden" name="kode" value="<?php echo $data['kodeanggota'] ?>">
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Profile ID</label>
                              <div class="col-sm-10">
                                 <input type="text" class="form-control form-control-sm" value="<?php echo $data['kodeanggota'] ?>" disabled>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Name / Username</label>
                              <div class="col-sm-7">
                                 <input type="text" class="form-control form-control-sm" name="nama" placeholder="Full Name of User" maxlength="63" value="<?php echo $data['nama'] ?>" required>
                              </div>
                              <div class="col-sm-3">
                                 <input type="text" class="form-control form-control-sm" name="username" placeholder="Username of User" maxlength="99" value="<?php echo $data['username'] ?>" required>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Phone</label>
                              <div class="col-sm-10">
                                 <input type="text" class="form-control form-control-sm" name="telepon" placeholder="Phone Number" maxlength="14" onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    value="<?php echo $data['telepon'] ?>" required />
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Address</label>
                              <div class="col-sm-10">
                                 <textarea class="form-control form-control-sm" name="alamat" placeholder="Full Address" rows="3" style="resize: none;" required><?php echo $data['alamat'] ?></textarea>
                              </div>
                           </div>
                        </div>

                        <div class="modal-footer">
                           <button type="submit" class="btn btn-success btn-sm">Save</button>
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