<?php
$db = db_connect();
$sampul = base_url('assets/gambar/sampul/'.$atribut['sampul']);
if($atribut['sampul'] == ''){
   $sampul = base_url('assets/gambar/Blank.png');
}
$author = '';
foreach ($pengarang as $p) {
   $author .= $p['depan'].' '.$p['tengah'].' '.$p['belakang'].', ';
}
$author = substr($author, 0, strlen($author) - 2);
$k = $db->query("select * from klasifikasi where kodeklasifikasi = '".$data['kodeklasifikasi']."'")->getRowArray();
$r = $db->query("select * from rak where koderak = '".$data['koderak']."'")->getRowArray();
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
                        <h4 class="card-title mb-3"><a href="<?php echo base_url('a/pustaka') ?>">Pengolahan Data Pustaka</a> - Detail Pustaka</h4>
                        <div class="row">
                           <div class="col-9">
                              <p style="text-align: justify;font-size: 11pt;">
                                 <b style="font-size: 14pt;"><?php echo $data['judul'] ?></b><br>
                                 Pengarang : <?php echo $author ?><br>
                                 Penerbit : <?php echo $data['bulan'].' '.$data['tahun'] ?><br>
                                 Terbitan : <?php echo "Vol. ".$atribut['volume'].', '.$atribut['halaman'].' hlm, '.$atribut['romawi'] ?><br>
                                 <?php echo $atribut['ns'] ?>
                              </p>
                              <hr>
                              <div class="row">
                                 <div class="col-2">Posisi</div>
                                 <div class="col-10">: <?php echo $r['rak'].' ['.$data['baris'].']' ?></div>
                              </div>
                              <div class="row">
                                 <div class="col-2">Klasifikasi</div>
                                 <div class="col-10">: <?php echo $k['kodeklasifikasi'].' '.$k['klasifikasi'] ?></div>
                              </div>
                              <div class="row">
                                 <div class="col-2">Bahasa</div>
                                 <div class="col-10">: <?php echo $atribut['bahasa'] ?></div>
                              </div>
                              <div class="row">
                                 <div class="col-2">Durasi / Genre</div>
                                 <div class="col-10">: <?php echo $atribut['durasi'].' / '.$atribut['genre'] ?></div>
                              </div>
                              <div class="row">
                                 <div class="col-2"><i>e-book</i></div>
                                 <div class="col-10">:
                                    <?php
                                    if($atribut['file'] == ''){
                                       echo "Tidak tersedia";
                                    }else{
                                       ?>
                                       <a href="<?php echo base_url('assets/file/'.$atribut['file']) ?>" target="blank">Lihat e-book</a>
                                    <?php } ?>
                                 </div>
                              </div>
                           </div>
                           <div class="col-3">
                              <img src="<?php echo $sampul ?>" width="100%">
                           </div>
                        </div>
                     </div>
                     <div class="card-footer">
                        <a href="<?php echo base_url('a/pustaka/') ?>" class="btn btn-primary btn-sm">Kembali</a>
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