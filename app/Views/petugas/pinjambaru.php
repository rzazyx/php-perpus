<?php
$db = db_connect();
date_default_timezone_set('Asia/Jakarta');
$kode = "";
$x = $db->query("select count(*) as jumlah from transaksi where kodeanggota > 0")->getRowArray()['jumlah'];
if ($x == 0) {
   $x = 1;
}
$ada = true;
$kode = '';
while ($ada) {
   if ($x < 10) {
      $kode = date('dmHi') . "00000" . $x . date('Y');
   } else if ($x < 100) {
      $kode = date('dmHi') . "0000" . $x . date('Y');
   } else if ($x < 1000) {
      $kode = date('dmHi') . "000" . $x . date('Y');
   } else if ($x < 10000) {
      $kode = date('dmHi') . "00" . $x . date('Y');
   } else if ($x < 100000) {
      $kode = date('dmHi') . "0" . $x . date('Y');
   } else {
      $kode = date('dmHi') . $x . date('Y');
   }
   $cek = $db->query("select * from transaksi where kodetransaksi = '" . $kode . "'")->getResultArray();
   if (count($cek) == 0) {
      $ada = false;
   } else {
      $x++;
   }
}
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
                     <div class="card-body">
                        <h4 class="card-title"><a href="<?php echo base_url('p/pinjam') ?>">Library Loan Cycle</a> - New Loan</h4>
                        <h6 class="card-subtitle mb-3">Enter the number of items to be processed, then select the <code>Loan Book</code> button to add data. Choose the <code>Save</code> button to save the library loan.</h6>
                        <form action="<?php echo base_url('p/pinjamtambahdetail') ?>" method="post">
                           <input type="hidden" name="peminjam" value="<?php echo base64_encode(serialize($peminjam)) ?>">
                           <input type="hidden" name="detail" value="<?php echo base64_encode(serialize($detail)) ?>">
                           <div class="input-group">
                              <div class="custom-file">
                                 <select class="form-control col-sm-11" name="pustaka" required>
                                    <?php
                                    foreach ($pustaka as $p) {
                                       if ($p['eksemplar'] > 0) {
                                          $aut = $db->query("select * from pengarang where kodepustaka = '" . $p['kodepustaka'] . "' order by kodepengarang asc limit 1")->getRowArray();
                                    ?>
                                          <option value="<?php echo $p['kodepustaka'] ?>"><?php echo substr($p['judul'], 0, 80) . ' (' . $aut['depan'] . ' ' . $aut['tengah'] . ' ' . $aut['belakang'] . ', ' . $p['tahun'] . ')' ?></option>
                                       <?php } ?>
                                    <?php } ?>
                                 </select>
                                 <input type="number" class="form-control col-sm-1" name="jumlah" placeholder="Jumlah" min="1" value="1" required>
                              </div>
                              <div class="input-group-append">
                                 <button class="btn btn-success" type="submit">Borrow Book</button>
                              </div>
                           </div>
                        </form>
                        <br>
                        <?php if (count($detail) > 0) { ?>
                           <?php
                           for ($i = 0; $i < count($detail); $i++) {
                              $p = $db->query("select * from pustaka where kodepustaka = '" . $detail[$i]['kode'] . "'")->getRowArray();
                              $aut = $db->query("select * from pengarang where kodepustaka = '" . $p['kodepustaka'] . "' order by kodepengarang asc limit 1")->getRowArray();
                           ?>
                              <div class="form-group row">
                                 <label class="col-sm-11">
                                    <?php
                                    echo $p['kodepustaka'] . ' ' . $p['judul'] . ' (' . $aut['depan'] . ' ' . $aut['tengah'] . ' ' . $aut['belakang'] . ', ' . $p['tahun'] . ') = ' . $detail[$i]['jumlah'] . ' buah';
                                    ?>
                                 </label>
                                 <label class="col-sm-1"><a href="<?php echo base_url('p/pinjamhapusdetail/' . $i . '/' . base64_encode(serialize($peminjam)) . '/' . base64_encode(serialize($detail))) ?>" title="Klik untuk menghapus data"><i data-feather="trash" class="feather-icon" style="width: 18px;"></i></a></label>
                              </div>
                           <?php } ?>
                        <?php } ?>
                     </div>
                     <form method="post" action="<?php echo base_url('p/pinjamsimpan') ?>">
                        <input type="hidden" name="kode" value="<?php echo $kode ?>">
                        <input type="hidden" name="peminjam" value="<?php echo base64_encode(serialize($peminjam)) ?>">
                        <input type="hidden" name="detail" value="<?php echo base64_encode(serialize($detail)) ?>">
                        <div class="card-footer">
                           <a href="<?php echo base_url('p/pinjam') ?>" class="btn btn-primary btn-sm">Back</a>
                           <?php if (count($detail) > 0) { ?>
                              <button type="submit" class="btn waves-effect waves-light btn-sm btn-success" style="float: right;">Save</button>
                           <?php } ?>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
            <?php echo view('petugas/part_footer') ?>
         </div>
      </div>
   </div>
   <?php echo view('petugas/part_script') ?>
</body>

</html>