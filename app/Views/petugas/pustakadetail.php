<?php
$db = db_connect();
$sampul = base_url('assets/gambar/sampul/' . $atribut['sampul']);
if ($atribut['sampul'] == '') {
   $sampul = base_url('assets/gambar/Blank.png');
}
$author = '';
foreach ($pengarang as $p) {
   $author .= $p['depan'] . ' ' . $p['tengah'] . ' ' . $p['belakang'] . ', ';
}
$author = substr($author, 0, strlen($author) - 2);
$k = $db->query("select * from klasifikasi where kodeklasifikasi = '" . $data['kodeklasifikasi'] . "'")->getRowArray();
$r = $db->query("select * from rak where koderak = '" . $data['koderak'] . "'")->getRowArray();

$db = db_connect();
$jenis = ['buku', 'jurnal', 'majalah', 'koran', 'ebook', 'gambar', 'audio', 'video', 'lain'];
$bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
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
                        <h4 class="card-title mb-3">Library Data Management - Pustaka Details</h4>
                        <div class="row">
                           <div class="col-9">
                              <p style="text-align: justify;font-size: 11pt;">
                                 <b style="font-size: 14pt;"><?php echo $data['judul'] ?></b><br>
                                 Author : <?php echo $author ?><br>
                                 Publisher : <?php echo $data['bulan'] . ' ' . $data['tahun'] ?><br>
                                 Edition : <?php echo "Vol. " . $atribut['volume'] . ', ' . $atribut['halaman'] . ' hlm, ' . $atribut['romawi'] ?><br>
                                 <?php echo $atribut['ns'] ?>
                              </p>
                              <hr>
                              <div class="row">
                                 <div class="col-2">Position</div>
                                 <div class="col-10">: <?php echo $r['rak'] . ' [' . $data['baris'] . ']' ?></div>
                              </div>
                              <div class="row">
                                 <div class="col-2">Classification</div>
                                 <div class="col-10">: <?php echo $k['kodeklasifikasi'] . ' ' . $k['klasifikasi'] ?></div>
                              </div>
                              <div class="row">
                                 <div class="col-2">Language</div>
                                 <div class="col-10">: <?php echo $atribut['bahasa'] ?></div>
                              </div>
                              <div class="row">
                                 <div class="col-2">Genre / Duration</div>
                                 <div class="col-10">: <?php echo $atribut['durasi'] . ' / ' . $atribut['genre'] ?></div>
                              </div>
                              <div class="row">
                                 <div class="col-2">Available</div>
                                 <div class="col-10">: <?php echo $data['eksemplar'] . ' eksemplar' ?></div>
                              </div>
                              <div class="row">
                                 <div class="col-2"><i>e-book</i></div>
                                 <div class="col-10">:
                                    <?php
                                    if ($atribut['file'] == '') {
                                       echo "Tidak tersedia";
                                    } else {
                                    ?>
                                       <a href="<?php echo base_url('assets/file/' . $atribut['file']) ?>" target="blank">View ebook</a>
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
                        <a href="<?php echo base_url('p/pustaka/') ?>" class="btn btn-primary btn-sm">Back</a>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-12">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title">Edit Book Details</h4>
                        <h6 class="card-subtitle mb-3">Enter the changes to the data details, then select the <code>Save Data</code> button to save the changes.</h6>
                        <form action="<?php echo base_url('p/pustakaubah') ?>" method="post" enctype="multipart/form-data">
                           <input type="hidden" name="kode" value="<?php echo $data['kodepustaka'] ?>">
                           <hr>
                           <strong>Basic Information</strong>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Title</label>
                              <div class="col-sm-10">
                                 <input type="text" class="form-control form-control-sm" name="judul" placeholder="Judul Pustaka" value="<?php echo $data['judul'] ?>" required>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Month / Year</label>
                              <div class="col-sm-5">
                                 <select class="form-control form-control-sm" name="bulan" required>
                                    <?php for ($i = 0; $i < count($bulan); $i++) { ?>
                                       <option value="<?php echo $bulan[$i] ?>" <?php if ($bulan[$i] == $data['bulan']) {
                                                                                    echo "selected";
                                                                                 } ?>><?php echo $bulan[$i] ?></option>
                                    <?php } ?>
                                 </select>
                              </div>
                              <div class="col-sm-5">
                                 <input type="text" class="form-control form-control-sm" name="tahun" placeholder="Tahun Terbit Pustaka" maxlength="4"
                                    minlength="4" onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    value="<?php echo $data['tahun'] ?>" required />
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Publisher / City</label>
                              <div class="col-sm-5">
                                 <input type="text" class="form-control form-control-sm" name="penerbit" maxlength="63" placeholder="Penerbit Pustaka" value="<?php echo $data['penerbit'] ?>" required>
                              </div>
                              <div class="col-sm-5">
                                 <input type="text" class="form-control form-control-sm" name="kota" maxlength="36" placeholder="Kota Penerbit" value="<?php echo $data['kota'] ?>" required>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Type / Classification</label>
                              <div class="col-sm-2">
                                 <select class="form-control form-control-sm" name="jenis" required>
                                    <?php for ($i = 0; $i < count($jenis); $i++) { ?>
                                       <option value="<?php echo $jenis[$i] ?>" <?php if ($jenis[$i] == $data['jenis']) {
                                                                                    echo "selected";
                                                                                 } ?>><?php echo ucfirst($jenis[$i]) ?></option>
                                    <?php } ?>
                                 </select>
                              </div>
                              <div class="col-sm-8">
                                 <select class="form-control form-control-sm" name="klasifikasi" required>
                                    <?php
                                    foreach ($klasifikasi as $k) {
                                       $sub = $db->query("select * from klasifikasi where tingkat = '2' and reff = '" . $k['kodeklasifikasi'] . "' order by kodeklasifikasi asc")->getResultArray();
                                    ?>
                                       <optgroup label="<?php echo $k['klasifikasi'] ?>">
                                          <option value="<?php echo $k['kodeklasifikasi'] ?>"><?php echo $k['kodeklasifikasi'] . ' - ' . $k['klasifikasi'] ?></option>
                                          <?php foreach ($sub as $s) { ?>
                                             <option value="<?php echo $s['kodeklasifikasi'] ?>" <?php if ($s['kodeklasifikasi'] == $data['kodeklasifikasi']) {
                                                                                                      echo "selected";
                                                                                                   } ?>><?php echo $s['kodeklasifikasi'] . ' - ' . $s['klasifikasi'] ?></option>
                                          <?php } ?>
                                       </optgroup>
                                    <?php } ?>
                                 </select>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Shelf / Row / Status</label>
                              <div class="col-sm-6">
                                 <select class="form-control form-control-sm" name="rak" required>
                                    <?php foreach ($rak as $r) { ?>
                                       <option value="<?php echo $r['koderak'] ?>" <?php if ($r['koderak'] == $data['koderak']) {
                                                                                       echo "selected";
                                                                                    } ?>><?php echo $r['rak'] ?></option>
                                    <?php } ?>
                                 </select>
                              </div>
                              <div class="col-sm-2">
                                 <input type="text" class="form-control form-control-sm" name="baris" placeholder="Baris ke-" value="<?php echo $data['baris'] ?>" minlength="1" onkeypress="return event.charCode >= 48 && event.charCode <= 57" required />
                              </div>
                              <div class="col-sm-2">
                                 <select class="form-control form-control-sm" name="status" required>
                                    <option value="1" <?php if ($data['status'] == '1') {
                                                         echo "selected";
                                                      } ?>>Active</option>
                                    <option value="0" <?php if ($data['status'] == '0') {
                                                         echo "selected";
                                                      } ?>>Inactive</option>
                                 </select>
                              </div>
                           </div>
                           <hr>
                           <strong>Attribute</strong>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Standard Number</label>
                              <div class="col-sm-10">
                                 <input type="text" class="form-control form-control-sm" name="ns" placeholder="Nomor Standar (ISSN, ISBN, dll)" value="<?php echo $atribut['ns'] ?>">
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Volume / Language</label>
                              <div class="col-sm-5">
                                 <input type="text" class="form-control form-control-sm" name="volume" placeholder="Volume ke-" value="<?php echo $atribut['volume'] ?>" max="3" onkeypress="return event.charCode >= 48 && event.charCode <= 57" />
                              </div>
                              <div class="col-sm-5">
                                 <input type="text" class="form-control form-control-sm" name="bahasa" placeholder="Bahasa Pustaka" value="<?php echo $atribut['bahasa'] ?>" required>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Content</label>
                              <div class="col-sm-3">
                                 <input type="number" class="form-control form-control-sm" name="halaman" placeholder="Jml. Halaman" value="<?php echo $atribut['halaman'] ?>" min="1" required>
                              </div>
                              <div class="col-sm-4">
                                 <input type="text" class="form-control form-control-sm" name="romawi" placeholder="Jml. Halaman Romawi" value="<?php echo $atribut['romawi'] ?>" required>
                              </div>
                              <div class="col-sm-3">
                                 <input type="number" class="form-control form-control-sm" name="ilustrasi" placeholder="Jml. Ilustrasi" value="<?php echo $atribut['ilustrasi'] ?>" min="0" required>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Genre / Duration</label>
                              <div class="col-sm-8">
                                 <input type="text" class="form-control form-control-sm" name="genre" placeholder="Genre Pustaka" value="<?php echo $atribut['genre'] ?>">
                              </div>
                              <div class="col-sm-2">
                                 <input type="text" class="form-control form-control-sm" name="durasi" placeholder="Durasi Pustaka" value="<?php echo $atribut['durasi'] ?>">
                              </div>
                           </div>
                           <div class="card-footer">
                              <a href="<?php echo base_url('p/pustaka/') ?>" class="btn btn-primary btn-sm">Back</a>
                              <?php if (count($pengarang) > 0) { ?>
                                 <button type="submit" class="btn btn-success btn-sm" style="float: right;">Save Changes</button>
                              <?php } ?>
                           </div>
                        </form>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <?php echo view('petugas/part_footer') ?>
      </div>
   </div>
   <?php echo view('petugas/part_script') ?>
</body>

</html>