<?php
$db = db_connect();
$info = $db->query("select * from infosistem")->getRowArray();
?>
<!DOCTYPE html>
<html dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url('assets/gambar/' . $info['logo']) ?>">
    <title>Login</title>
    <link href="<?php echo base_url('assets/admin/dist/css/style.min.css') ?>" rel="stylesheet">
</head>

<body>
    <div class="main-wrapper">
        <div class="preloader">
            <div class="lds-ripple">
                <div class="lds-pos"></div>
                <div class="lds-pos"></div>
            </div>
        </div>
        <div class="auth-wrapper d-flex no-block justify-content-center align-items-center position-relative"
            style="background:url(<?php echo base_url('assets/gambar/background.jpg') ?>) no-repeat center center;">
            <div class="auth-box row">
                <div class="col-lg-7 col-md-5 modal-bg-img" style="background-image: url(<?php echo base_url('assets/gambar/login.jpg') ?>);">
                </div>
                <div class="col-lg-5 col-md-7 bg-white">
                    <div class="p-3">
                        <a href="<?php echo base_url('kunjungan') ?>" target="blank" title="Klik untuk membuka panel Kunjungan" style="color: black;float: right;"><i class="fa fa-retweet"></i></a>
                        <div class="text-center">
                            <img src="<?php echo base_url('assets/gambar/' . $info['logo']) ?>" alt="wrapkit" width=63%>
                        </div>
                        <h2 class="mt-3 text-center">Login Access</h2>
                        <p class="text-center">Enter your Username and Password to access your account</p>
                        <form class="mt-4" method="post" action="<?php echo base_url('proseslogin') ?>">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="text-dark" for="uname">Username</label>
                                        <input class="form-control" id="uname" type="text"
                                            placeholder="enter username" name="username" maxlength="99" autofocus required>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="text-dark" for="pwd">Password</label>
                                        <input class="form-control" id="pwd" type="password"
                                            placeholder="enter password" name="password" required>
                                    </div>
                                </div>
                                <div class="col-lg-12 text-center">
                                    <button type="submit" class="btn btn-block btn-dark">Login</button>
                                    <?php if (session()->getFlashData('gagal')) { ?>
                                        <div class="col-lg-12 text-center mt-3" style="color: red;font-size: 10pt;">
                                            <?php echo session()->getFlashData('gagal') ?>
                                        </div>
                                    <?php } ?>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo base_url('assets/admin/libs/jquery/dist/jquery.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/admin/libs/popper.js/dist/umd/popper.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/admin/libs/bootstrap/dist/js/bootstrap.min.js') ?>"></script>
    <script>
        $(".preloader ").fadeOut();
    </script>
</body>

</html>