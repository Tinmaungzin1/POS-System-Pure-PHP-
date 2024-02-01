<?php
session_start();
require("../common/config.php");
require("../common/database.php");  
require("../Auth/check_cashier_auth.php");
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_data = json_decode(file_get_contents("php://input"), true);
    $order_id = (int) ($post_data['order_id']);
    $status = (int) ($post_data['status']);
    $order_paid_sql = "UPDATE `orders` SET status = '$status' WHERE id = '$order_id'";
    $order_paid_result = $mysqli->query($order_paid_sql);

 }
?>