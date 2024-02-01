<?php
  $order_check = "SELECT id FROM `shift` WHERE start_date_time IS NOT NULL AND end_date_time IS NULL";
  $order_res = $mysqli->query($order_check);
  $order_row = $order_res->fetch_assoc();
  $shift_start_id = htmlspecialchars($order_row['id']);
  $order_paid_check = "SELECT status FROM `orders` WHERE shift_id = '$shift_start_id'";
  $order_paid_res = $mysqli->query($order_paid_check);
    while($order_paid_row = $order_paid_res->fetch_assoc())
    { 
      $status = $order_paid_row['status'];
      
    }
 
  ?>