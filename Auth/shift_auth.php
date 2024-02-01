<?php
$order_check = "SELECT id FROM `shift` WHERE start_date_time IS NOT NULL AND end_date_time IS NULL";
$order_res = $mysqli->query($order_check);
$order_num_row = $order_res->num_rows;
  if ($order_num_row <= 0) {
    $url = $base_url."shift.php";
    header("Refresh: 0; url = $url");
      exit();
  }else {
    while($order_row = $order_res->fetch_assoc())
    { 
      $shift_id = $order_row['id'];
      $_SESSION['shift_id'] = $shift_id;
      
    }
  }
 
  ?>