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
                        <h4 class="card-title">Library Data Management</h4>
                        <h6 class="card-subtitle">Select the <code><i class="fa fa-edit"></i></code> button to view data details.</h6>
                        <div class="table-responsive">
                           <table id="zero_config" class="table table-striped table-bordered no-wrap">
                              <thead>
                                 <tr>
                                    <th>#</th>
                                    <th>Code</th>
                                    <th>Title</th>
                                    <th>Publisher</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>**</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php
                                 $n = 1;
                                 foreach ($data as $d) {
                                 ?>
                                    <tr>
                                       <td><?php echo $n++ ?></td>
                                       <td><?php echo $d['kodepustaka'] ?></td>
                                       <td><?php echo $d['judul'] ?></td>
                                       <td><?php echo $d['penerbit'] ?></td>
                                       <td><?php echo $d['jenis'] ?></td>
                                       <td>
                                          <?php if ($d['status'] == '0') { ?>
                                             <span class="badge badge-danger">Active</span>
                                          <?php } else { ?>
                                             <span class="badge badge-success">Inactive</span>
                                          <?php } ?>
                                       </td>
                                       <td>
                                          <a href="<?php echo base_url('a/pustakadetail/' . $d['kodepustaka']) ?>" class="btn waves-effect waves-light btn-sm btn-warning"><i class="fa fa-edit"></i></a>
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

</html>