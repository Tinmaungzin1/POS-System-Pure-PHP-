<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" acontent="">

    <title><?= $ctitle ?></title>
    <link rel="stylesheet" type="text/css" href="<?= $base_url ?>asset/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="<?= $base_url ?>asset/css/styles.css" />
    <link rel="stylesheet" type="text/css" href="<?= $base_url ?>asset/bootstrap/css/fontawesomeall.css" />
    <link rel="stylesheet" type="text/css" href="<?= $base_url ?>asset/font-awesome/css/font-awesome.css" />
    <!-- <link rel="icon" href="<?= $base_url ?>asset/images/favicon-32x32.png" type="image/x-icon"> -->
    <link rel="stylesheet" type="text/css" href="<?= $base_url ?>asset/css/swiper.min.css" />
    <link href="<?= $base_url ?>asset/css/sweetalert.css" rel="stylesheet">

    <script src="<?= $base_url ?>asset/bootstrap/js/jquery-2.2.4.min.js"></script>
    <script src="<?= $base_url ?>asset/bootstrap/js/popper.min.js"></script>
    <script src="<?= $base_url ?>asset/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?= $base_url ?>asset/bootstrap/js/heightLine.js"></script>
    <script src="<?= $base_url ?>asset/js/sweetalert-dev.js"></script>
    <script src="<?= $base_url ?>asset/js/swiper.min.js"></script>
    <script src="<?= $base_url ?>asset/js/angular.min.js"></script>
    <script src="<?= $base_url ?>asset/angular-route.js"></script>
    <script src="<?= $base_url ?>asset/js/sweet-alert/sweetalert2@10.js"></script>

    <!--
    <script src="{{ URL::asset('assets/cashier/js/common.js') }}"></script>
    <script src="{{ URL::asset('assets/js/common.js') }}"></script>
-->
    <style>
    .item-td {
        text-align: center !important;
    }

    .price-input {
        width: 100% !important;
        text-align: center;
    }

    .search-item {
        background: none;
        height: 30px;
        width: 80%;
        padding: 0;
        margin-bottom: 3px;
        border-radius: 3px;
        padding: 0 5px;
    }

    .clediv {
        clear: both;
    }
    </style>
    <script>
    const base_url = 'http://localhost/sg_pos/';
    const shift_id = <?= $_SESSION['shift_id']; ?>
    </script>
</head>

<body>
    <div class="wrapper">