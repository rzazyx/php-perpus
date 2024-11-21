<?php
$db = db_connect();
$denda = $db->query("select denda from infosistem")->getRowArray()['denda'];
$tahun = date('Y');
$cek = $db->query("select * from transaksi")->getResultArray();
if (count($cek) > 0) {
   $tahun = $db->query("select year(waktu) as tahun from transaksi order by waktu asc limit 1")->getRowArray()['tahun'];
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
                        <h4 class="card-title mt-4">Book Return Cycle</h4>
                        <div class="table-responsive">
                           <table id="zero_config" class="table table-striped table-bordered no-wrap">
                              <thead>
                                 <tr>
                                    <th>#</th>
                                    <th>Code</th>
                                    <th>Borrower</th>
                                    <th>Borrow Date</th>
                                    <th>Return Date</th>
                                    <th>Late</th>
                                    <th>Status</th>
                                    <th>**</th>

                                 </tr>
                              </thead>
                              <tbody>
                                 <?php
                                 $n = 1;
                                 foreach ($data as $d) {
                                    $k = $db->query("select * from denda where kodetransaksi = '" . $d['kodetransaksi'] . "'")->getRowArray();
                                    $a = $db->query("select nama from anggota where kodeanggota = '" . $d['kodeanggota'] . "'")->getRowArray()['nama'];
                                 ?>
                                    <tr>
                                       <td><?php echo $n++ ?></td>
                                       <td><?php echo $d['kodetransaksi'] ?></td>
                                       <td><?php echo $a ?></td>
                                       <td><?php echo date('d/m/Y', strtotime($k['tglpinjam'])) ?></td>
                                       <td><?php echo date('d/m/Y', strtotime($k['tglkembali'])) ?></td>
                                       <td><?php echo $k['telat'] . " hari (Rp" . number_format($k['total']) . ")" ?></td>
                                       <td>
                                          <?php
                                          if ($d['status'] == '1') {
                                             echo "Dipinjam";
                                          } else {
                                             echo "Selesai";
                                          }
                                          ?>
                                       </td>
                                       <td>
                                          <?php if ($d['status'] == '1') { ?>
                                             <a href="#kembali<?php echo $d['kodetransaksi'] ?>" data-toggle="modal" class="btn waves-effect waves-light btn-sm btn-warning" title="Klik untuk proses pengembalian"><i class="fa fa-download"></i></a>
                                          <?php } else { ?>
                                             <a href="#detail<?php echo $d['kodetransaksi'] ?>" data-toggle="modal" class="btn waves-effect waves-light btn-sm btn-warning" title="Klik untuk menampilkan detail"><i class="fa fa-expand"></i></a>
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
         <?php echo view('petugas/part_footer') ?>
      </div>
   </div>
   <?php echo view('petugas/part_script') ?>
</body>
<div class="modal fade" id="tambah" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="myLargeModalLabel">Add New Data</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
         </div>
         <form method="post" action="<?php echo base_url('p/pinjambaru') ?>">
            <div class="modal-body">
               <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Borrower (Member)</label>
                  <div class="col-sm-8">
                     <select class="form-control form-control-sm" name="anggota" required>
                        <?php foreach ($anggota as $a) { ?>
                           <option value="<?php echo $a['kodeanggota'] ?>"><?php echo $a['nama'] ?></option>
                        <?php } ?>
                     </select>
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Borrow Date</label>
                  <div class="col-sm-4">
                     <input type="date" name="tglpinjam" class="form-control form-control-sm" value="<?php echo date('Y-m-d') ?>" required>
                  </div>
                  <div class="col-sm-4">
                     <input type="date" name="tglbatas" class="form-control form-control-sm" value="<?php echo date('Y-m-d') ?>" required>
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Note</label>
                  <div class="col-sm-8">
                     <textarea class="form-control form-control-sm" name="keterangan" rows="4" style="resize: none;" required></textarea>
                  </div>
               </div>
            </div>

            <div class="modal-footer">
               <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-success btn-sm">Add Data</button>
            </div>
      </div>
      </form>
   </div>
</div>
<?php
foreach ($data as $d) {
   $status = "Selesai (Dikembalikan)";
   if ($d['status'] == '1') {
      $status = "Proses (Dipinjam)";
   }
   $p = $db->query("select * from anggota where kodeanggota = '" . $d['kodeanggota'] . "'")->getRowArray();
   $dd = $db->query("select * from denda where kodetransaksi = '" . $d['kodetransaksi'] . "'")->getRowArray();
   $detail = $db->query("select * from detailtransaksi where subjek = '' and kodetransaksi = '" . $d['kodetransaksi'] . "'")->getResultArray();
?>
   <?php if ($d['status'] == '1') { ?>
      <div class="modal fade" id="kembali<?php echo $d['kodetransaksi'] ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
         <div class="modal-dialog modal-lg">
            <div class="modal-content">
               <div class="modal-header">
                  <h4 class="modal-title" id="myLargeModalLabel">Library Return Details</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
               </div>
               <form method="post" action="<?php echo base_url('p/kembalisimpan') ?>">
                  <input type="hidden" name="kode" value="<?php echo $d['kodetransaksi'] ?>">
                  <input type="hidden" name="tglbatas" value="<?php echo $dd['tglbatas'] ?>">
                  <input type="hidden" name="denda" value="<?php echo $denda ?>">
                  <div class="modal-body">
                     <div class="row">
                        <div class="col-sm-4"><b>Code</b></div>
                        <div class="col-sm-8">: <?php echo $d['kodetransaksi'] ?></div>
                     </div>
                     <div class="row">
                        <div class="col-sm-4"><b>ID</b></div>
                        <div class="col-sm-8">: <?php echo $p['kodeanggota'] ?></div>
                     </div>
                     <div class="row">
                        <div class="col-sm-4"><b>Borrower</b></div>
                        <div class="col-sm-8">: <?php echo $p['nama'] ?></div>
                     </div>
                     <div class="row">
                        <div class="col-sm-4"><b>Borrow Date</b></div>
                        <div class="col-sm-8">: <?php echo date('d/m/Y', strtotime($dd['tglpinjam'])) ?></div>
                     </div>
                     <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Return Date</label>
                        <div class="col-sm-4">
                           <input type="date" name="tglkembali" class="form-control form-control-sm" value="<?php echo date('Y-m-d', strtotime($dd['tglbatas'])) ?>" required>
                        </div>
                     </div>
                     <hr>
                     <?php
                     foreach ($detail as $dt) {
                        $pt = $db->query("select * from pustaka where kodepustaka = '" . $dt['kodepustaka'] . "'")->getRowArray();
                     ?>
                        <div class="form-group row">
                           <label class="col-sm-8 col-form-label"><?php echo $pt['judul'] ?></label>
                           <div class="col-sm-2">
                              <input type="number" class="form-control form-control-sm" name="jm<?php echo $dt['kodedetail'] ?>" min="1" max="<?php echo $dt['jumlah'] ?>" value="<?php echo $dt['jumlah'] ?>" required>
                           </div>
                           <div class="col-sm-2">
                              <select class="form-control form-control-sm" name="st<?php echo $dt['kodedetail'] ?>" required>
                                 <option value="0">Return</option>
                                 <option value="5">Damaged</option>
                                 <option value="6">Lost</option>
                              </select>
                           </div>
                        </div>
                     <?php } ?>
                  </div>
                  <div class="modal-footer">
                     <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                     <button type="submit" class="btn btn-success btn-sm">Save Return</button>
                  </div>
            </div>
            </form>
         </div>
      </div>
   <?php
   } else {
      $detail = $db->query("select * from detailtransaksi where subjek = 'kembali' and kodetransaksi = '" . $d['kodetransaksi'] . "'")->getResultArray();
   ?>
      <div class="modal fade" id="detail<?php echo $d['kodetransaksi'] ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
         <div class="modal-dialog modal-lg">
            <div class="modal-content">
               <div class="modal-header">
                  <h4 class="modal-title" id="myLargeModalLabel">Book Loan Details</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
               </div>

               <div class="modal-body">
                  <div class="row col-12">
                     <div class="col-6">
                        <div class="row">
                           <div class="col-sm-4"><b>Code</b></div>
                           <div class="col-sm-8">: <?php echo $d['kodetransaksi'] ?></div>
                        </div>
                        <div class="row">
                           <div class="col-sm-4"><b>ID</b></div>
                           <div class="col-sm-8">: <?php echo $p['kodeanggota'] ?></div>
                        </div>
                        <div class="row">
                           <div class="col-sm-4"><b>Borrower</b></div>
                           <div class="col-sm-8">: <?php echo $p['nama'] ?></div>
                        </div>
                        <div class="row">
                           <div class="col-sm-4"><b>Borrow Date</b></div>
                           <div class="col-sm-8">: <?php echo date('d/m/Y', strtotime($dd['tglpinjam'])) ?></div>
                        </div>
                     </div>
                     <div class="col-6">
                        <div class="row">
                           <div class="col-sm-4"><b>Return Date</b></div>
                           <div class="col-sm-8">: <?php echo date('d/m/Y', strtotime($dd['tglkembali'])) ?></div>
                        </div>
                        <div class="row">
                           <div class="col-sm-4"><b>Late</b></div>
                           <div class="col-sm-8">: <?php echo $dd['telat'] . ' Hari' ?></div>
                        </div>
                        <div class="row">
                           <div class="col-sm-4"><b>Penalty</b></div>
                           <div class="col-sm-8">: <?php echo 'Rp' . number_format($dd['total']) ?></div>
                        </div>
                        <div class="row">
                           <div class="col-sm-4"><b>Status</b></div>
                           <div class="col-sm-8">: <?php echo $status ?></div>
                        </div>
                     </div>
                  </div>
                  <hr>
                  <?php
                  foreach ($detail as $dt) {
                     $pt = $db->query("select * from pustaka where kodepustaka = '" . $dt['kodepustaka'] . "'")->getRowArray();
                     $status = "Kembali";
                     if ($dt['status'] == '5') {
                        $status = "Rusak";
                     } else if ($dt['status'] == '6') {
                        $status = "Hilang";
                     }
                  ?>
                     <div class="row">
                        <div class="col-sm-9"><?php echo $pt['judul'] ?></div>
                        <div class="col-sm-3">: <?php echo $dt['jumlah'] . ' (' . $status . ')' ?></div>
                     </div>
                  <?php } ?>
               </div>
            </div>
         </div>
      </div>
   <?php } ?>
<?php } ?>

</html>