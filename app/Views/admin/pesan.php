<?php
$db = db_connect();
?>
<!doctype html>
<html lang="en">
<head>
   <?php echo view('admin/part_head') ?>
</head>
<body class="sidebar-main-active right-column-fixed">
   <div class="wrapper">
      <?php echo view('admin/part_sidebar') ?>
      <?php echo view('admin/part_topbar') ?>
      <div id="content-page" class="content-page">
         <div class="container-fluid">
            <div class="row">
               <div class="col-lg-12">
                  <div class="iq-card">
                     <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                           <h4 class="card-title">Detail Data Pesanan</h4>
                        </div>
                        <div class="iq-card-header-toolbar d-flex align-items-center">
                           <form method="post" action="<?php echo base_url('admin/pesanshow') ?>">
                              <ul class="nav nav-pills">
                                 <li class="nav-item">
                                    <input type="date" name="dari" value="<?php echo date('Y-m-d', strtotime($dari)) ?>" class="form-control form-control-sm" onchange="this.form.submit();" required>
                                 </li>
                                 <li>&nbsp;&nbsp;s.d&nbsp;&nbsp;</li>
                                 <li class="nav-item">
                                    <input type="date" name="sampai" value="<?php echo date('Y-m-d', strtotime($sampai)) ?>" class="form-control form-control-sm" onchange="this.form.submit();" required>
                                 </li>
                                 <li>&nbsp;&nbsp;&nbsp;&nbsp;</li>
                              </ul>
                           </form>
                        </div>
                     </div>
                     <div class="iq-card-body">
                        <p>Pengolahan data pesanan barang. Pilih periode data untuk menampilkan data pesanan. <code>Detail Data</code> untuk menampilkan detail data</p>
                        <div class="table-responsive">
                           <table id="datatable" class="table table-striped table-bordered" >
                              <thead>
                                 <tr>
                                    <th>Kode</th>
                                    <th>Tanggal</th>
                                    <th>Pemesan</th>
                                    <th>Alamat</th>
                                    <th>Total</th>
                                    <td>Status</td>
                                    <td>Aksi</td>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php foreach ($data as $d) {?>
                                    <tr>
                                       <td><?php echo $d['id_pesan'] ?></td>
                                       <td>
                                          <?php echo date('d/m/Y', strtotime($d['waktu_pesan'])) ?><br>
                                          <small>Kirim tgl : <?php echo date('d/m/Y', strtotime($d['tglkirim_pesan'])) ?></small>
                                       </td>
                                       <td><?php echo $d['vendor_pesan']." : ".$d['an_pesan'].", ".$d['jabatan_pesan'] ?></td>
                                       <td><?php echo $d['alamat_pesan'] ?></td>
                                       <td><?php echo "Rp".number_format($d['total_pesan']) ?></td>
                                       <td>
                                          <?php if($d['status_pesan'] == '0'){ ?>
                                             <span class="badge badge-pill badge-warning">proses</span>
                                          <?php }else{ ?>
                                             <span class="badge badge-pill badge-primary">selesai</span>
                                          <?php } ?>
                                       </td>
                                       <td><a href="#detail<?php echo $d['id_pesan'] ?>" data-toggle="modal" class="btn btn-sm btn-primary mb-3">Detail Data</a></td>
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
      </div>
   </div>
   <?php echo view('admin/part_footer') ?>
   <?php echo view('admin/part_script') ?>
</body>
<?php foreach ($data as $d) {?>
   <div class="modal fade" id="detail<?php echo $d['id_pesan'] ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">Detail Data Pesanan</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <dl class="row">
                  <dt class="col-sm-3">No. PO</dt>
                  <dd class="col-sm-9"><?php echo $d['id_pesan'] ?></dd>

                  <dt class="col-sm-3">Tgl. Pesan</dt>
                  <dd class="col-sm-9"><?php echo date('d/m/Y H:i:s', strtotime($d['waktu_pesan'])).", Kirim : ".date('d/m/Y', strtotime($d['tglkirim_pesan'])) ?></dd>

                  <dt class="col-sm-3">Wiraniaga (Vendor)</dt>
                  <dd class="col-sm-9"><?php echo $d['vendor_pesan'] ?></dd>

                  <dt class="col-sm-3">Atas Nama</dt>
                  <dd class="col-sm-9"><?php echo $d['an_pesan'].", ".$d['jabatan_pesan'] ?></dd>
                  
                  <dt class="col-sm-3">Alamat</dt>
                  <dd class="col-sm-9"><?php echo $d['alamat_pesan'] ?></dd>

                  <dt class="col-sm-3">Telepon</dt>
                  <dd class="col-sm-9"><?php echo $d['telepon_pesan'] ?></dd>

                  <dt class="col-sm-3">Keterangan</dt>
                  <dd class="col-sm-9"><?php echo $d['keterangan_pesan'] ?></dd>
               </dl>
               <hr>
               <?php $detail = $db->query("select * from tbl_dpesan where id_pesan = '".$d['id_pesan']."'")->getResultArray(); ?>
               <ul class="list-group">
                  <?php
                  foreach ($detail as $dt) {
                     $b = $db->query("select * from tbl_barang where id_barang = '".$dt['id_barang']."'")->getRowArray();
                     ?>
                     <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo $dt['id_barang']." ".$b['nama_barang']." ( Rp.".number_format($dt['hbeli_dpesan'])." x ".number_format($dt['jumlah_dpesan'])." ".$b['satuan_barang']." )" ?>
                        <span><?php echo "Rp.".number_format($dt['subtotal_dpesan']) ?></span>
                     </li>
                  <?php } ?>
               </ul>
               <br>
               <div class="row" style="font-size: 12pt;font-weight: bold;">
                  <div class="col-sm-9 text-right">Total Penjualan</div>
                  <div class="col-sm-3 text-right"><span><?php echo "Rp.".number_format($d['total_pesan']) ?></span></div>
               </div>
            </div>
         </div>
      </div>
   </div>
<?php } ?>
</html>