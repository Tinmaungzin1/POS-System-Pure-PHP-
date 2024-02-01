<?php
session_start();
require("../common/config.php");
require("../common/database.php");  
require("../Auth/check_cashier_auth.php");
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_data = json_decode(file_get_contents("php://input"), true);
$shift_start_id = (int)($post_data['shift_id']);
    $order_sql = "SELECT id, shift_id,	total_amount, created_at, status FROM `orders` WHERE shift_id = '$shift_start_id' AND  deleted_at IS NULL ORDER BY
    status ASC, id DESC;";
    $order_result = $mysqli->query($order_sql);
    $data = [];
    while($order_row = $order_result->fetch_assoc()) {
        $res_data = [];
        $order_id = (int)($order_row['id']);
        $shift_id = (int)($order_row['shift_id']);
        $total_amount = (int)($order_row['total_amount']);
        $order_time = $order_row['created_at'];

        $order_date = new DateTime($order_time);
        $formattedDate = $order_date->format('Ymd');
        $formattedTime = $order_date->format('H:i:s');

        $status = (int)($order_row['status']);
        
        $res_data['id'] = $order_id;
        $res_data['shift_id'] = $shift_id;
        $res_data['total_amount'] = $total_amount;
        $res_data['created_at'] = $order_time;
        $res_data['order_time'] = $formattedTime;
        $res_data['status'] = $status;
        $res_data['order_no'] = $shift_id . "-" . $order_id . $formattedDate;
        array_push($data, $res_data);
    }
    echo json_encode($data); 
    
 }

    
?>