<?php
$db = db_connect();
$info = $db->query("select * from infosistem")->getRowArray();
?>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url('assets/gambar/' . $info['logo']) ?>">
	<title>Member</title>
	<link href="<?php echo base_url('assets/admin/extra-libs/c3/c3.min.css') ?>" rel="stylesheet">
	<link href="<?php echo base_url('assets/admin/libs/chartist/dist/chartist.min.css') ?>" rel="stylesheet">
	<link href="<?php echo base_url('assets/admin/extra-libs/jvector/jquery-jvectormap-2.0.2.css') ?>" rel="stylesheet" />
	<link href="<?php echo base_url('assets/admin/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css') ?>" rel="stylesheet">
	<link href="<?php echo base_url('assets/admin/dist/css/style.min.css') ?>" rel="stylesheet">
</head>