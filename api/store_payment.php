<?php
session_start();
require("../common/config.php");
require("../common/database.php");  
require("../Auth/check_cashier_auth.php");
$input_data = json_decode(file_get_contents("php://input"), true);

$order_id = $input_data['id'];
$customer_pay = $input_data['customer_pay'];
$refund   = $input_data['refund'];
$kyats   = $input_data['kyats'];

    $update_payment = "UPDATE `orders` SET 
     payment = '$customer_pay',
     status = '$paid_status',
    refund = '$refund' WHERE id='$order_id' AND deleted_at IS NULL";
    $mysqli->query($update_payment);

    foreach($kyats as $kyat) {
        $cash = $kyat['cash'];
        $quantity = $kyat['quantity'];
        
        $ins_kyat  = "INSERT INTO `payment_history` (order_id, cash, quantity) VALUES ('$order_id', '$cash', '$quantity')";
        $mysqli->query($ins_kyat);
    }
    $data = ['success' => true];
    echo json_encode($data);
?>