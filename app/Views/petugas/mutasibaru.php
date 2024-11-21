<?php
$db = db_connect();
date_default_timezone_set('Asia/Jakarta');
$kode = "";
$x = $db->query("select count(*) as jumlah from transaksi where kodeanggota = 0")->getRowArray()['jumlah'];
if($x == 0){
   $x = 1;
}
$ada = true;
$kode = '';
while ($ada) {
   if($x < 10){
      $kode = date('dmHi')."00000".$x.date('Y');
   }else if($x < 100){
      $kode = date('dmHi')."0000".$x.date('Y');
   }else if($x < 1000){
      $kode = date('dmHi')."000".$x.date('Y');
   }else if($x < 10000){
      $kode = date('dmHi')."00".$x.date('Y');
   }else if($x < 100000){
      $kode = date('dmHi')."0".$x.date('Y');
   }else{
      $kode = date('dmHi').$x.date('Y');
   }
   $cek = $db->query("select * from transaksi where kodetransaksi = '".$kode."'")->getResultArray();
   if(count($cek) == 0){
      $ada = false;
   }else{
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
                        <h4 class="card-title"><a href="<?php echo base_url('p/mutasi') ?>">Siklus Mutasi Pustaka</a> - Mutasi Baru</h4>
                        <h6 class="card-subtitle mb-3">masukkan jumlah pustaka yang akan diproses, lalu pilih tombol <code>Tambahkan Mutasi</code> untuk menambahkan data. pilih tombol <code>Proses</code> untuk menyimpan mutasi pustaka</h6>
                        <form action="<?php echo base_url('p/mutasitambahdetail') ?>" method="post">
                           <input type="hidden" name="jenis" value="<?php echo $jenis ?>">
                           <input type="hidden" name="detail" value="<?php echo base64_encode(serialize($detail)) ?>">
                           <div class="input-group">
                              <div class="custom-file">
                                 <select class="form-control col-sm-7" name="pustaka" required>
                                    <?php
                                    foreach ($pustaka as $p) {
                                       $aut = $db->query("select * from pengarang where kodepustaka = '".$p['kodepustaka']."' order by kodepengarang asc limit 1")->getRowArray();
                                       ?>
                                       <option value="<?php echo $p['kodepustaka'] ?>"><?php echo substr($p['judul'], 0, 80).' ('.$aut['depan'].' '.$aut['tengah'].' '.$aut['belakang'].', '.$p['tahun'].')' ?></option>
                                    <?php } ?>
                                 </select>
                                 <input type="number" class="form-control col-sm-1" name="jumlah" placeholder="Jumlah" min="1" value="1" required>
                                 <input type="text" class="form-control col-sm-3" name="subjek" placeholder="Sumber / Tujuan" maxlength="63" required>
                                 <select class="form-control col-sm-2" name="status" required>
                                    <?php if($jenis == '1'){ ?>
                                       <option value="1">Pembelian</option>
                                       <option value="2">Donasi</option>
                                       <option value="3">Pengganti</option>
                                    <?php }else{ ?>
                                       <option value="4">Dijual</option>
                                       <option value="5">Rusak</option>
                                       <option value="6">Hilang</option>
                                    <?php } ?>
                                 </select>
                              </div>
                              <div class="input-group-append">
                                 <button class="btn btn-success" type="submit">Tambahkan Mutasi</button>
                              </div>
                           </div>
                        </form>
                        <br>
                        <?php if(count($detail) > 0){ ?>
                           <?php
                           for ($i=0; $i < count($detail); $i++) {
                              $p = $db->query("select * from pustaka where kodepustaka = '".$detail[$i]['kode']."'")->getRowArray();
                              $aut = $db->query("select * from pengarang where kodepustaka = '".$p['kodepustaka']."' order by kodepengarang asc limit 1")->getRowArray();
                              ?>
                              <div class="form-group row">
                                 <label class="col-sm-11">
                                    <?php
                                    echo $p['kodepustaka'].' '.$p['judul'].' ('.$aut['depan'].' '.$aut['tengah'].' '.$aut['belakang'].', '.$p['tahun'].') = '.$detail[$i]['jumlah'].' buah';
                                    if($detail[$i]['status'] == '1'){
                                       echo " (Pembelian, ".$detail[$i]['subjek'].")";
                                    }else if($detail[$i]['status'] == '2'){
                                       echo " (Donasi, ".$detail[$i]['subjek'].")";
                                    }else if($detail[$i]['status'] == '3'){
                                       echo " (Pengganti, ".$detail[$i]['subjek'].")";
                                    }else if($detail[$i]['status'] == '4'){
                                       echo " (Dijual, ".$detail[$i]['subjek'].")";
                                    }else if($detail[$i]['status'] == '5'){
                                       echo " (Rusak, ".$detail[$i]['subjek'].")";
                                    }else {
                                       echo " (Hilang, ".$detail[$i]['subjek'].")";
                                    }
                                    ?>
                                 </label>
                                 <label class="col-sm-1"><a href="<?php echo base_url('p/mutasihapusdetail/'.$i.'/'.$jenis.'/'.base64_encode(serialize($detail))) ?>" title="Klik untuk menghapus data"><i data-feather="trash" class="feather-icon" style="width: 18px;"></i></a></label>
                              </div>
                           <?php } ?>
                        <?php } ?>
                     </div>
                     <div class="card-footer">
                        <a href="<?php echo base_url('p/mutasi/') ?>" class="btn btn-primary btn-sm">Kembali</a>
                        <?php if(count($detail) > 0){ ?>
                           <button type="button" data-toggle="modal" data-target="#simpan" class="btn waves-effect waves-light btn-sm btn-success" style="float: right;">Proses</button>
                        <?php } ?>
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
<div class="modal fade" id="simpan" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="myLargeModalLabel">Proses Mutasi Pustaka</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
         </div>
         <form method="post" action="<?php echo base_url('p/mutasisimpan') ?>">
            <input type="hidden" name="jenis" value="<?php echo $jenis ?>">
            <input type="hidden" name="kode" value="<?php echo $kode ?>">
            <input type="hidden" name="detail" value="<?php echo base64_encode(serialize($detail)) ?>">
            <div class="modal-body">
               <p></p>
               <div class="form-group row">
                  <label class="col-sm-2 col-form-label">Kode</label>
                  <div class="col-sm-10">
                     <input type="text" class="form-control form-control-sm" value="<?php echo $kode ?>" disabled>
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-2 col-form-label">Catatan</label>
                  <div class="col-sm-10">
                     <textarea class="form-control form-control-sm" name="keterangan" placeholder="Catatan atau Keterangan Tambahan" rows="4" style="resize: none;" required></textarea>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Batal</button>
               <button type="submit" class="btn btn-success btn-sm">Simpan Data</button>
            </div>
         </form>
      </div>
   </div>
</div>
</html>