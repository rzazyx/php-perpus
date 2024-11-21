<?php
$db = db_connect();
$info = $db->query("select * from infosistem")->getRowArray();
$pengguna = $db->query("select * from petugas where kodepetugas = '" . session()->get('petugas') . "'")->getRowArray();
$fp = base_url('assets/gambar/' . $pengguna['jekel'] . '.png')
?>
<header class="topbar" data-navbarbg="skin6">
   <nav class="navbar top-navbar navbar-expand-md" style="background-color: #edfce6;">
      <div class="navbar-header" data-logobg="skin6">
         <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
         <div class="navbar-brand" style="background-color: #edfce6;">
            <a href="<?php echo base_url('') ?>">
               <b class="logo-icon">
                  <img src="<?php echo base_url('assets/gambar/' . $info['logo']) ?>" alt="homepage" class="dark-logo" style="width: 36%;" />
                  <img src="<?php echo base_url('assets/gambar/' . $info['logo']) ?>" alt="homepage" class="light-logo" style="width: 36%;" />
               </b>
            </a>
         </div>
         <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i class="ti-more"></i></a>
      </div>
      <div class="navbar-collapse collapse" id="navbarSupportedContent">
         <ul class="navbar-nav float-left mr-auto ml-3 pl-1">
         </ul>
         <ul class="navbar-nav float-right">
            <li class="nav-item dropdown">
               <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <img src="<?php echo $fp ?>" alt="user" class="rounded-circle" width="40">
                  <span class="ml-2 d-none d-lg-inline-block"><span class="text-dark"><?php echo $pengguna['nama'] ?></span> <i data-feather="chevron-down" class="svg-icon"></i></span>
               </a>
               <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                  <a class="dropdown-item" href="<?php echo base_url('p/profil') ?>"><i data-feather="user" class="svg-icon mr-2 ml-1"></i> Account Profile</a>
                  <a class="dropdown-item" href="<?php echo base_url('p/akses') ?>"><i data-feather="settings" class="svg-icon mr-2 ml-1"></i> Login Access</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="<?php echo base_url('proseslogout') ?>"><i data-feather="power" class="svg-icon mr-2 ml-1"></i>Logout</a>
               </div>
            </li>
         </ul>
      </div>
   </nav>
</header>
<aside class="left-sidebar" data-sidebarbg="skin6">
   <div class="scroll-sidebar" data-sidebarbg="skin6">
      <nav class="sidebar-nav">
         <ul id="sidebarnav">
            <li class="sidebar-item"> <a class="sidebar-link sidebar-link" href="<?php echo base_url('') ?>" aria-expanded="false"><i data-feather="home" class="feather-icon"></i><span class="hide-menu">Dashboard</span></a></li>

            <li class="list-divider"></li>
            <li class="nav-small-cap"><span class="hide-menu">Master Data</span></li>
            <li class="sidebar-item"> <a class="sidebar-link" href="<?php echo base_url('p/pustaka') ?>" aria-expanded="false"><i data-feather="book" class="feather-icon"></i><span class="hide-menu">Library</span></a></li>
            <li class="sidebar-item"> <a class="sidebar-link" href="<?php echo base_url('p/anggota') ?>" aria-expanded="false"><i data-feather="users" class="feather-icon"></i><span class="hide-menu">Member</span></a></li>

            <li class="list-divider"></li>
            <li class="nav-small-cap"><span class="hide-menu">Library Cycle</span></li>
            <li class="sidebar-item"> <a class="sidebar-link" href="<?php echo base_url('p/pinjam') ?>" aria-expanded="false"><i data-feather="repeat" class="feather-icon"></i><span class="hide-menu">Book Borrowing</span></a></li>
            <li class="sidebar-item"> <a class="sidebar-link" href="<?php echo base_url('p/kembali') ?>" aria-expanded="false"><i data-feather="copy" class="feather-icon"></i><span class="hide-menu">Book Return</span></a></li>
            <li class="sidebar-item"> <a class="sidebar-link" href="<?php echo base_url('p/mutasi') ?>" aria-expanded="false"><i data-feather="refresh-cw" class="feather-icon"></i><span class="hide-menu">Mutation</span></a></li>
            <li class="sidebar-item"> <a class="sidebar-link" href="<?php echo base_url('p/kunjungan') ?>" aria-expanded="false"><i data-feather="activity" class="feather-icon"></i><span class="hide-menu">Visits</span></a></li>
         </ul>
      </nav>
   </div>
</aside>