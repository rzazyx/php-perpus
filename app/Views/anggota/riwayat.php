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
<?php echo view('anggota/part_head') ?>

<body>
   <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
      <?php echo view('anggota/part_sidebar') ?>
      <div class="page-wrapper">
         <div class="container-fluid">
            <div class="row">
               <div class="col-12">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title mt-4">Library Borrowing History</h4>
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
                                          <a href="#detail<?php echo $d['kodetransaksi'] ?>" data-toggle="modal" class="btn waves-effect waves-light btn-sm btn-warning" title="Klik untuk menampilkan detail"><i class="fa fa-expand"></i></a>
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
         <?php echo view('anggota/part_footer') ?>
      </div>
   </div>
   <?php echo view('anggota/part_script') ?>
</body>
<?php
foreach ($data as $d) {
   $p = $db->query("select * from anggota where kodeanggota = '" . $d['kodeanggota'] . "'")->getRowArray();
   $dd = $db->query("select * from denda where kodetransaksi = '" . $d['kodetransaksi'] . "'")->getRowArray();
   if ($d['status'] == '1') {
      $detail = $db->query("select * from detailtransaksi where subjek = '' and kodetransaksi = '" . $d['kodetransaksi'] . "'")->getResultArray();
   } else {
      $detail = $db->query("select * from detailtransaksi where subjek = 'kembali' and kodetransaksi = '" . $d['kodetransaksi'] . "'")->getResultArray();
   }
?>
   <div class="modal fade" id="detail<?php echo $d['kodetransaksi'] ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-lg">
         <div class="modal-content">
            <div class="modal-header">
               <h4 class="modal-title" id="myLargeModalLabel">Detail Peminjaman Pustaka</h4>
               <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>

            <div class="modal-body">
               <div class="row col-12">
                  <div class="col-6">
                     <div class="row">
                        <div class="col-sm-4"><b>Kode</b></div>
                        <div class="col-sm-8">: <?php echo $d['kodetransaksi'] ?></div>
                     </div>
                     <div class="row">
                        <div class="col-sm-4"><b>ID</b></div>
                        <div class="col-sm-8">: <?php echo $p['kodeanggota'] ?></div>
                     </div>
                     <div class="row">
                        <div class="col-sm-4"><b>Peminjam</b></div>
                        <div class="col-sm-8">: <?php echo $p['nama'] ?></div>
                     </div>
                     <div class="row">
                        <div class="col-sm-4"><b>Tgl. Pinjam</b></div>
                        <div class="col-sm-8">: <?php echo date('d/m/Y', strtotime($dd['tglpinjam'])) ?></div>
                     </div>
                  </div>
                  <div class="col-6">
                     <div class="row">
                        <div class="col-sm-4"><b>ID Kembali</b></div>
                        <div class="col-sm-8">: #<?php echo $dd['kodedenda'] ?></div>
                     </div>
                     <div class="row">
                        <div class="col-sm-4"><b>Tgl. Kembali</b></div>
                        <div class="col-sm-8">: <?php echo date('d/m/Y', strtotime($dd['tglkembali'])) ?></div>
                     </div>
                     <div class="row">
                        <div class="col-sm-4"><b>Telat</b></div>
                        <div class="col-sm-8">: <?php echo $dd['telat'] . ' Hari' ?></div>
                     </div>
                     <div class="row">
                        <div class="col-sm-4"><b>Denda</b></div>
                        <div class="col-sm-8">: <?php echo 'Rp' . number_format($dd['total']) ?></div>
                     </div>
                  </div>
               </div>
               <hr>
               <?php
               foreach ($detail as $dt) {
                  $pt = $db->query("select * from pustaka where kodepustaka = '" . $dt['kodepustaka'] . "'")->getRowArray();
                  $at = $db->query("select * from atribut where kodepustaka = '" . $dt['kodepustaka'] . "'")->getRowArray();
                  if ($dt['status'] == '5') {
                     $status = "Rusak";
                  } else if ($dt['status'] == '6') {
                     $status = "Hilang";
                  } else if ($dt['status'] == '7') {
                     $status = "Pinjam";
                  } else {
                     $status = "Kembali";
                  }
               ?>
                  <div class="row">
                     <div class="col-sm-9">
                        <?php echo $pt['judul'] ?>
                        <?php if ($at['file'] != '') { ?>
                           <a href="<?php echo base_url('assets/file/' . $at['file']) ?>" target="blank">lihat ebook</a>
                        <?php } ?>
                     </div>
                     <div class="col-sm-3">: <?php echo $dt['jumlah'] . ' (' . $status . ')' ?></div>
                  </div>
               <?php } ?>
            </div>
         </div>
      </div>
   </div>
<?php } ?>

</html>