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
                        <button type="button" data-toggle="modal" data-target="#tambah" class="btn waves-effect waves-light btn-sm btn-success" style="float: right;">Add New Data</button>
                        <h4 class="card-title mt-5">Bookshelf Management</h4>
                        <h6 class="card-subtitle">Select the <code>Add New Data</code> button to add new data. Choose the <code><i class="fa fa-edit"></i></code> button to edit data details. Select the <code><i class="fa fa-trash"></i></code> button to delete data.</h6>
                        <div class="table-responsive">
                           <table id="zero_config" class="table table-striped table-bordered no-wrap">
                              <thead>
                                 <tr>
                                    <th>#</th>
                                    <th>Shelf Type</th>
                                    <th>Shelf Rows</th>
                                    <th>**</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php
                                 $n = 1;
                                 foreach ($data as $d) {
                                    $cek = $db->query("select count(*) as jumlah from pustaka where koderak = '" . $d['koderak'] . "'")->getRowArray()['jumlah'];
                                 ?>
                                    <tr>
                                       <td><?php echo $n++ ?></td>
                                       <td><?php echo $d['rak'] ?></td>
                                       <td><?php echo $d['baris'] ?></td>
                                       <td>
                                          <button type="button" data-toggle="modal" data-target="#detail<?php echo $d['koderak'] ?>" class="btn waves-effect waves-light btn-sm btn-warning"><i class="fa fa-edit"></i></button>
                                          <?php if ($cek == 0) { ?>
                                             <button type="button" data-toggle="modal" data-target="#hapus<?php echo $d['koderak'] ?>" class="btn waves-effect waves-light btn-sm btn-danger"><i class="fa fa-trash"></i></button>
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
         <?php echo view('admin/part_footer') ?>
      </div>
   </div>
   <?php echo view('admin/part_script') ?>
</body>
<div class="modal fade" id="tambah" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="myLargeModalLabel">Add New Data</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
         </div>
         <form method="post" action="<?php echo base_url('a/raksimpan') ?>">
            <input type="hidden" name="aksi" value="simpan">
            <div class="modal-body">
               <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Shelf Type</label>
                  <div class="col-sm-9">
                     <input type="text" class="form-control form-control-sm" name="rak" placeholder="Nama Rak, Lemari, atau Loker" maxlength="18" required />
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Shelf Rows</label>
                  <div class="col-sm-9">
                     <input type="number" class="form-control form-control-sm" name="baris" min="1" placeholder="Jumlah Baris" required>
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
foreach ($data as $d) {
   $cek = $db->query("select count(*) as jumlah from pustaka where koderak = '" . $d['koderak'] . "'")->getRowArray()['jumlah'];
?>
   <div class="modal fade" id="detail<?php echo $d['koderak'] ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-lg">
         <div class="modal-content">
            <div class="modal-header">
               <h4 class="modal-title" id="myLargeModalLabel">Data Details </h4>
               <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form method="post" action="<?php echo base_url('a/raksimpan') ?>">
               <input type="hidden" name="aksi" value="ubah">
               <input type="hidden" name="kode" value="<?php echo $d['koderak'] ?>">
               <div class="modal-body">
                  <div class="form-group row">
                     <label class="col-sm-3 col-form-label">Shelf Type</label>
                     <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" name="rak" placeholder="Nama Rak, Lemari, atau Loker" maxlength="18" value="<?php echo $d['rak'] ?>" required />
                     </div>
                  </div>
                  <div class="form-group row">
                     <label class="col-sm-3 col-form-label">Shelf Rows</label>
                     <div class="col-sm-9">
                        <input type="number" class="form-control form-control-sm" name="baris" min="1" placeholder="Jumlah Baris" value="<?php echo $d['baris'] ?>" required>
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
   <?php if ($cek == 0) { ?>
      <div class="modal fade" id="hapus<?php echo $d['koderak'] ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
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
                  <a href="<?php echo base_url('a/rakhapus/' . $d['koderak']) ?>" class="btn btn-danger btn-sm">Hapus Data</a>
               </div>
            </div>
         </div>
      </div>
   <?php } ?>
<?php } ?>

</html>