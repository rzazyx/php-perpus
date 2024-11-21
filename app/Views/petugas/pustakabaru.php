<?php
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
                        <h4 class="card-title"><a href="<?php echo base_url('p/pustaka') ?>">Library Data Management</a> - New Library Item</h4>
                        <h6 class="card-subtitle mb-3">Enter the details for the new item, then select the <code>Save Data</code> button to save the new data</h6>
                        <strong>Author</strong>
                        <br><br>
                        <form action="<?php echo base_url('p/pustakatambahpengarang') ?>" method="post">
                           <input type="hidden" name="pengarang" value="<?php echo base64_encode(serialize($pengarang)) ?>">
                           <div class="input-group">
                              <div class="custom-file">
                                 <input type="text" class="form-control" name="depan" placeholder="Nama Depan Pengarang" autofocus required>
                                 <input type="text" class="form-control" name="tengah" placeholder="Nama Tengah Pengarang">
                                 <input type="text" class="form-control" name="belakang" placeholder="Nama Belakang Pengarang">
                              </div>
                              <div class="input-group-append">
                                 <button class="btn btn-success btn-sm" type="submit">Add Author</button>
                              </div>
                           </div>
                        </form>
                        <br>
                        <?php if (count($pengarang) > 0) { ?>
                           <?php for ($i = 0; $i < count($pengarang); $i++) { ?>
                              <div class="form-group row">
                                 <label class="col-sm-4"><?php echo $pengarang[$i]['belakang'] . ', ' . $pengarang[$i]['depan'] . ' ' . $pengarang[$i]['tengah'] ?></label>
                                 <label class="col-sm-2"><a href="<?php echo base_url('p/pustakahapuspengarang/' . $i . '/' . base64_encode(serialize($pengarang))) ?>" title="Klik untuk menghapus data"><i data-feather="trash" class="feather-icon" style="width: 18px;"></i></a></label>
                              </div>
                           <?php } ?>
                        <?php } ?>
                        <form action="<?php echo base_url('p/pustakasimpan') ?>" method="post" enctype="multipart/form-data">
                           <input type="hidden" name="pengarang" value="<?php echo base64_encode(serialize($pengarang)) ?>">
                           <hr>
                           <strong>Basic Information</strong>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Title</label>
                              <div class="col-sm-10">
                                 <input type="text" class="form-control form-control-sm" name="judul" placeholder="Judul Pustaka" required>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Month / Year</label>
                              <div class="col-sm-5">
                                 <select class="form-control form-control-sm" name="bulan" required>
                                    <?php for ($i = 0; $i < count($bulan); $i++) { ?>
                                       <option value="<?php echo $bulan[$i] ?>"><?php echo $bulan[$i] ?></option>
                                    <?php } ?>
                                 </select>
                              </div>
                              <div class="col-sm-5">
                                 <input type="text" class="form-control form-control-sm" name="tahun" placeholder="Tahun Terbit Pustaka" maxlength="4" minlength="4" onkeypress="return event.charCode >= 48 && event.charCode <= 57" required />
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Publisher / City</label>
                              <div class="col-sm-5">
                                 <input type="text" class="form-control form-control-sm" name="penerbit" maxlength="63" placeholder="Penerbit Pustaka" required>
                              </div>
                              <div class="col-sm-5">
                                 <input type="text" class="form-control form-control-sm" name="kota" maxlength="36" placeholder="Kota Penerbit" required>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Type / Classification</label>
                              <div class="col-sm-2">
                                 <select class="form-control form-control-sm" name="jenis" required>
                                    <?php for ($i = 0; $i < count($jenis); $i++) { ?>
                                       <option value="<?php echo $jenis[$i] ?>"><?php echo ucfirst($jenis[$i]) ?></option>
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
                                             <option value="<?php echo $s['kodeklasifikasi'] ?>"><?php echo $s['kodeklasifikasi'] . ' - ' . $s['klasifikasi'] ?></option>
                                          <?php } ?>
                                       </optgroup>
                                    <?php } ?>
                                 </select>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Shelf / Row</label>
                              <div class="col-sm-8">
                                 <select class="form-control form-control-sm" name="rak" required>
                                    <?php foreach ($rak as $r) { ?>
                                       <option value="<?php echo $r['koderak'] ?>"><?php echo $r['rak'] ?></option>
                                    <?php } ?>
                                 </select>
                              </div>
                              <div class="col-sm-2">
                                 <input type="text" class="form-control form-control-sm" name="baris" placeholder="Baris ke-" minlength="1" onkeypress="return event.charCode >= 48 && event.charCode <= 57" required />
                              </div>
                           </div>
                           <hr>
                           <strong>Attribute</strong>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Standard Number</label>
                              <div class="col-sm-10">
                                 <input type="text" class="form-control form-control-sm" name="ns" placeholder="Nomor Standar (ISSN, ISBN, dll)">
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Volume / Language</label>
                              <div class="col-sm-5">
                                 <input type="text" class="form-control form-control-sm" name="volume" placeholder="Volume ke-" max="3" onkeypress="return event.charCode >= 48 && event.charCode <= 57" />
                              </div>
                              <div class="col-sm-5">
                                 <input type="text" class="form-control form-control-sm" name="bahasa" placeholder="Bahasa Pustaka" required>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Content</label>
                              <div class="col-sm-3">
                                 <input type="number" class="form-control form-control-sm" name="halaman" placeholder="Jml. Halaman" min="1" required>
                              </div>
                              <div class="col-sm-4">
                                 <input type="text" class="form-control form-control-sm" name="romawi" placeholder="Jml. Halaman Romawi" required>
                              </div>
                              <div class="col-sm-3">
                                 <input type="number" class="form-control form-control-sm" name="ilustrasi" placeholder="Jml. Ilustrasi" min="0" required>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Genre / Duration</label>
                              <div class="col-sm-8">
                                 <input type="text" class="form-control form-control-sm" name="genre" placeholder="Genre Pustaka">
                              </div>
                              <div class="col-sm-2">
                                 <input type="text" class="form-control form-control-sm" name="durasi" placeholder="Durasi Pustaka">
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Cover / File (<i>ebook</i>)</label>
                              <div class="col-sm-5">
                                 <input type="file" class="form-control form-control-sm" name="sampul" accept="image/*">
                              </div>
                              <div class="col-sm-5">
                                 <input type="file" class="form-control form-control-sm" name="ebook" accept="application/pdf">
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
            <?php echo view('petugas/part_footer') ?>
         </div>
      </div>
   </div>
   <?php echo view('petugas/part_script') ?>
</body>

</html>