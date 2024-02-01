<?php 
session_start();
require("common/config.php");
require("common/database.php");
require("Auth/check_cashier_auth.php");
$order_check = "SELECT id FROM `shift` WHERE start_date_time IS NOT NULL AND end_date_time IS NULL";
$order_res = $mysqli->query($order_check);
$order_num_row = $order_res->num_rows;
  if ($order_num_row > 0) {
    $url = $base_url."order.php";
    header("Refresh: 0; url = $url");
    exit();
  }else {

  }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Close</title>
    <link rel="stylesheet" type="text/css" href="<?= $base_url ?>asset/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="<?= $base_url ?>asset/font-awesome/css/font-awesome.css" />
    <style>
    body {
        margin: 0;
        overflow: hidden;
    }

    .col-12 {
        position: relative;
        text-align: center;
        height: 100vh;
        /* Set the container height to the full viewport height */
    }

    .col-12 a {
        position: absolute;
        top: 100px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1;
        /* Place the button above the image */
    }

    .col-12 img {
        position: absolute;
        top: 20px;
        left: 24%;
        text: center;
        width: 800px;
        height: 100%;
        object-fit: cover;
        /* Ensure the image covers the entire container */
    }
    </style>

</head>

<body>

    <div class="col-12">
        <a href="<?= $base_url ?>order.php" class="btn btn-success"><i class="fa fa-refresh fa-4x"></i></i>
        </a>
        <img src="<?= $base_url ?>asset/images/shift1.jpg" alt="Shift Closed Image">
    </div>
    <!-- <div class="shift-close-container">
        
    </div> -->

</body>

</html>