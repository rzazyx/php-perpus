<?php
$db = db_connect();
$kelas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
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
    <title><?php echo $info['nama'] ?></title>
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
        <div class="auth-wrapper d-flex no-block justify-content-center align-items-center position-relative" style="background:url(<?php echo base_url('assets/gambar/background.jpg') ?>) no-repeat center center;">
            <div class="row col-lg-8">

                <div class="col-lg-4 col-md-7 bg-white">
                    <div class="p-3">
                        <div class="text-center">
                            <img src="<?php echo base_url('assets/gambar/' . $info['logo']) ?>" alt="wrapkit" width=63%>
                        </div>
                        <h2 class="mt-3 text-center">WELCOME</h2>
                        <p class="text-center">Please enter your Name and Class</p>
                        <form class="mt-4" method="post" action="<?php echo base_url('berkunjung') ?>">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="text-dark" for="uname">Name</label>
                                        <input class="form-control" id="uname" type="text" placeholder="Enter Name" name="nama" maxlength="99" required>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="text-dark" for="pwd">Class</label>
                                        <select class="form-control" id="pwd" name="kelas" required>
                                            <optgroup label="7th (VII)">
                                                <?php for ($i = 0; $i < count($kelas); $i++) { ?>
                                                    <option><?php echo "VII " . $kelas[$i] ?></option>
                                                <?php } ?>
                                            </optgroup>
                                            <optgroup label="8th (VIII)">
                                                <?php for ($i = 0; $i < count($kelas); $i++) { ?>
                                                    <option><?php echo "VIII " . $kelas[$i] ?></option>
                                                <?php } ?>
                                            </optgroup>
                                            <optgroup label="9th (IX)">
                                                <?php for ($i = 0; $i < count($kelas); $i++) { ?>
                                                    <option><?php echo "IX " . $kelas[$i] ?></option>
                                                <?php } ?>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12 text-center">
                                    <button type="submit" class="btn btn-block btn-dark">Record Visit</button>
                                    <?php if (session()->getFlashData('berhasil')) { ?>
                                        <div class="col-lg-12 text-center mt-3" style="color: green;font-size: 10pt;">
                                            <?php echo session()->getFlashData('berhasil') ?>
                                            <br>
                                        </div>
                                    <?php } ?>
                                </div>
                                <br><br>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-7 col-md-5 bg-white">
                    <div class="p-3">
                        <form class="mt-4" method="post" action="<?php echo base_url('pencarian') ?>">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <input class="form-control" id="uname" type="text" placeholder="Enter the title or author of the book...." name="cari" maxlength="99" autofocus required>
                                    </div>
                                </div>
                                <div class="col-lg-12" style="overflow-y: scroll;height: 500px;font-size: 10pt;">
                                    <?php if (session()->getFlashData('gagal')) { ?>
                                        <?php if (count($hasil) > 0) { ?>
                                            <i>Search results for "<?php echo $x ?>"</i><br><br>
                                            <?php foreach ($hasil as $h) { ?>
                                                <strong><?php echo substr($h['judul'], 0, 200) . ', ' . $h['depan'] . ' (' . $h['tahun'] . ')' ?></strong><br>
                                                <i>Rak : <?php echo $h['rak'] ?>, Baris : <?php echo $h['baris'] ?></i>
                                                <hr>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <i>"<?php echo $x ?>" No results found in any library...</i>
                                        <?php } ?>
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