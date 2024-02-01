<?php
session_start();
require("../common/config.php");
require("../common/database.php");  
require("../Auth/check_cashier_auth.php");
$input_data = json_decode(file_get_contents("php://input"), true);

$id = $input_data['id'];
$items = $input_data['items'];
$total_amount = $input_data['total'];
$shift_id   = $input_data['shift_id'];


        $today_dt  = date('Y-m-d H:i:s');  /// now date and time
        $user_id   = $_SESSION['cid'];

        $sql= "UPDATE `orders` SET total_amount = '$total_amount', updated_at = '$today_dt', updated_by ='$user_id' WHERE id = '$id'";
        $result = $mysqli->query($sql);

        $order_id = $id;
        $delete_sql = "DELETE FROM `order_detail` WHERE order_id = '$order_id'";
        $delete_result = $mysqli->query($delete_sql);
        foreach($items as $item) {
            $item_id = $mysqli->real_escape_string($item['id']);
            $item_discount = $mysqli->real_escape_string($item['discount']);
            $item_amount = $mysqli->real_escape_string($item['amount']);
            $item_price = $mysqli->real_escape_string($item['price']);
            $item_quantity = $mysqli->real_escape_string($item['quantity']);
            
            $order_detail_sql  = "INSERT INTO `order_detail` (item_id, order_id, quantity, price, discount, amount, created_at, created_by, updated_at, updated_by) VALUES ('$item_id', '$order_id', '$item_quantity', '$item_price', $item_discount, '$item_amount', '$today_dt', '$user_id', '$today_dt', '$user_id')";
            $order_detail_result = $mysqli->query($order_detail_sql);
        }
        $data = ['success' => true];
        echo json_encode($data);
?>