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
                     <div class="card-body">
                        <h4 class="card-title">Data Classification Management</h4>
                        <h6 class="card-subtitle">Select the <code><i class="fa fa-plus-square"></i></code> button to add new data. Choose data to edit or delete it.</h6>
                        <div class="row">
                           <div class="col-lg-12">
                              <div id="accordion" class="custom-accordion mb-4">
                                 <?php
                                 foreach ($data as $d) {
                                    $sub = $db->query("select * from klasifikasi where tingkat = '2' and reff = '" . $d['kodeklasifikasi'] . "' order by kodeklasifikasi asc")->getResultArray();
                                 ?>
                                    <div class="card mb-0">
                                       <div class="card-header" id="headingOne">
                                          <h5 class="m-0">
                                             <a href="#tambah<?php echo $d['kodeklasifikasi'] ?>" data-toggle="modal" title="Tambah Sub Klasifikasi" style="float: right;"><i class="fa fa-plus-square"></i></a>
                                             <a class="custom-accordion-title d-block pt-2 pb-2" data-toggle="collapse" href="#c<?php echo $d['kodeklasifikasi'] ?>" aria-expanded="true" aria-controls="collapseOne" style="color: black;"> <?php echo $d['kodeklasifikasi'] . ' (' . $d['klasifikasi'] . ')' ?> <span class="float-right"><i class="mdi mdi-chevron-down accordion-arrow"></i></span></a>

                                          </h5>
                                       </div>
                                       <div id="c<?php echo $d['kodeklasifikasi'] ?>" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                                          <div class="card-body">
                                             <ul class="list-style-none">
                                                <?php
                                                foreach ($sub as $s) {
                                                   $x = str_replace(".", "", $s['kodeklasifikasi']);
                                                ?>
                                                   <li><a href="#d<?php echo $x ?>" data-toggle="modal" title="Klik untuk mengubah atau menghapus data"><i class="fa fa-check text-info"></i> <?php echo $s['kodeklasifikasi'] . ' - ' . $s['klasifikasi'] ?></a></li>
                                                <?php } ?>
                                             </ul>
                                          </div>
                                       </div>
                                    </div>
                                 <?php } ?>
                              </div>
                           </div>
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
<?php
foreach ($data as $d) {
   $sub = $db->query("select * from klasifikasi where tingkat = '2' and reff = '" . $d['kodeklasifikasi'] . "' order by kodeklasifikasi asc")->getResultArray();
?>
   <div class="modal fade" id="tambah<?php echo $d['kodeklasifikasi'] ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-lg">
         <div class="modal-content">
            <div class="modal-header">
               <h4 class="modal-title" id="myLargeModalLabel">Add Subclassification <?php echo $d['kodeklasifikasi'] . ' (' . $d['klasifikasi'] . ')' ?> </h4>
               <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form method="post" action="<?php echo base_url('a/klasifikasisimpan') ?>">
               <input type="hidden" name="aksi" value="simpan">
               <input type="hidden" name="tingkat" value="2">
               <input type="hidden" name="reff" value="<?php echo $d['kodeklasifikasi'] ?>">
               <div class="modal-body">
                  <div class="form-group row">
                     <label class="col-sm-3 col-form-label">Code</label>
                     <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" name="kode" placeholder="Kode Klasifikasi" maxlength="7" required />
                     </div>
                  </div>
                  <div class="form-group row">
                     <label class="col-sm-3 col-form-label">Classification</label>
                     <div class="col-sm-9">
                        <textarea class="form-control form-control-sm" name="klasifikasi" placeholder="Klasifikasi atau Kategori" maxlength="180" rows="3" style="resize: none;" required></textarea>
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
   <?php
   foreach ($sub as $s) {
      $x = str_replace(".", "", $s['kodeklasifikasi']);
      $cek = $db->query("select count(*) as jumlah from pustaka where kodeklasifikasi = '" . $s['kodeklasifikasi'] . "'")->getRowArray()['jumlah'];
   ?>
      <div class="modal fade" id="d<?php echo $x ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
         <div class="modal-dialog modal-lg">
            <div class="modal-content">
               <div class="modal-header">
                  <h4 class="modal-title" id="myLargeModalLabel">Tambah Sub Klasifikasi <?php echo $d['kodeklasifikasi'] . ' (' . $d['klasifikasi'] . ')' ?> </h4>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
               </div>
               <form method="post" <?php if ($cek == 0) { ?> action="<?php echo base_url('a/klasifikasisimpan') ?>" <?php } ?>>
                  <input type="hidden" name="aksi" value="ubah">
                  <input type="hidden" name="kodelama" value="<?php echo $s['kodeklasifikasi'] ?>">
                  <div class="modal-body">
                     <p style="text-align: justify;"><code>IMPORTANT! Deleted data cannot be recovered. Deleting data will also cause related data to be removed as a form of adjustment and to prevent errors (<i>error</i>).</code> If you agree to this, select the <code>Delete Data</code> button to delete the data.</p>
                     <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Code</label>
                        <div class="col-sm-9">
                           <input type="text" class="form-control form-control-sm" name="kodebaru" placeholder="Kode Klasifikasi" maxlength="7" value="<?php echo $s['kodeklasifikasi'] ?>" required />
                        </div>
                     </div>
                     <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Classification</label>
                        <div class="col-sm-9">
                           <textarea class="form-control form-control-sm" name="klasifikasi" placeholder="Klasifikasi atau Kategori" maxlength="180" rows="3" style="resize: none;" required><?php echo $s['klasifikasi'] ?></textarea>
                        </div>
                     </div>
                  </div>
                  <div class="modal-footer">
                     <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Batal</button>
                     <?php if ($cek == 0) { ?>
                        <button type="submit" class="btn btn-success btn-sm">Save Changes</button>
                        <a href="<?php echo base_url('a/klasifikasihapus/' . $s['kodeklasifikasi']) ?>" class="btn btn-danger btn-sm">Delete Data</a>
                     <?php } ?>
                  </div>
               </form>
            </div>
         </div>
      </div>
   <?php } ?>
<?php } ?>

</html>