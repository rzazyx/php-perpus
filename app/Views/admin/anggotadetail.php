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
                        <h4 class="card-title mb-3"><a href="<?php echo base_url('a/anggota') ?>">Member Data Management</a> - Member Details</h4>
                        <p style="text-align: justify;font-size: 11pt;">
                           <b style="font-size: 14pt;"><?php echo $data['kodeanggota'] . ' (' . $data['username'] . ')' ?></b><br>
                           NISN: <?php echo $data['nisn'] ?><br>
                           Name: <?php echo $data['nama'] ?><br>
                           Address: <?php echo $data['alamat'] ?><br>
                           Phone: <?php echo $data['telepon'] ?><br>
                           Email: <?php echo $data['email'] ?><br>
                           Class: <?php echo $data['kelas'] ?><br>
                           Status: <?php if ($data['status'] == '1') {
                                       echo "Aktif";
                                    } else {
                                       echo "Nonaktif";
                                    } ?><br>
                        </p>
                     </div>
                     <div class="card-footer">
                        <a href="<?php echo base_url('a/anggota/') ?>" class="btn btn-primary btn-sm">Back</a>
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