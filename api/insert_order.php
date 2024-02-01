<?php
session_start();
require("../common/config.php");
require("../common/database.php");  
require("../Auth/check_cashier_auth.php");
$input_data = json_decode(file_get_contents("php://input"), true);

$items = $input_data['items'];
$total_amount = $input_data['total'];
$shift_id   = $input_data['shift_id'];


        $today_dt  = date('Y-m-d H:i:s');  /// now date and time
        $user_id   = $_SESSION['cid'];

        $sql       = "INSERT INTO `orders` (total_amount,shift_id, created_at, created_by, updated_at, updated_by) VALUES ('$total_amount','$shift_id','$today_dt', '$user_id', '$today_dt', '$user_id')";
        $result = $mysqli->query($sql);

        $last_inserted_id = $mysqli->insert_id;

        foreach($items as $item) {
            $item_id = $mysqli->real_escape_string($item['id']);
            $item_discount = $mysqli->real_escape_string($item['discount']);
            $item_amount = $mysqli->real_escape_string($item['amount']);
            $item_price = $mysqli->real_escape_string($item['price']);
            $item_quantity = $mysqli->real_escape_string($item['quantity']);
            
            $order_detail_sql  = "INSERT INTO `order_detail` (item_id, order_id, quantity, price, discount, amount, created_at, created_by, updated_at, updated_by) VALUES ('$item_id', '$last_inserted_id', '$item_quantity', '$item_price', $item_discount, '$item_amount', '$today_dt', '$user_id', '$today_dt', '$user_id')";
            $order_detail_result = $mysqli->query($order_detail_sql);
        }
?>