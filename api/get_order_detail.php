<?php
session_start();
require("../common/config.php");
require("../common/database.php");  
require("../Auth/check_cashier_auth.php");
require("../include/include_function.php");
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_data = json_decode(file_get_contents("php://input"), true);
$id = (int)($post_data['id']);
    $order_sql = "SELECT 
    t1.quantity, 
    t1.amount,
    t2.name AS name,
    t3.id,
    t3.shift_id,
    t3.total_amount, 
    t3.created_at, 
    t3.status
    FROM
    `order_detail` AS t1
    LEFT JOIN
    `item` AS t2 ON t1.item_id = t2.id 
    LEFT JOIN
    `orders` AS t3 ON t1.order_id = t3.id
    WHERE order_id = '$id'";
    $order_result = $mysqli->query($order_sql);

    $setting_sql = "SELECT * FROM `setting`";
    $setting_result = $mysqli->query($setting_sql);
    $settng_data = [];
    $setting_row = $setting_result->fetch_assoc();
        $setting_res_data = [];
        $company_name = htmlspecialchars($setting_row['company_name']);
        $company_phone = htmlspecialchars($setting_row['company_phone']);
        $company_email = htmlspecialchars($setting_row['company_email']);
        $company_address = htmlspecialchars($setting_row['company_address']);
        // $company_logo = htmlspecialchars($setting_row['company_logo']);

        $setting_res_data['company_name'] = $company_name;
        $setting_res_data['company_phone'] = $company_phone;
        $setting_res_data['company_email'] = $company_email;
        $setting_res_data['company_address'] = $company_address;
        array_push($settng_data, $setting_res_data);
    

    $data = [];
    while($order_row = $order_result->fetch_assoc()) {
        $res_data = [];
        $item_name = htmlspecialchars($order_row['name']);
        $quantity = (int)($order_row['quantity']);
        $amount = (int)($order_row['amount']);
        $id = (int)($order_row['id']);
        $shift_id = (int)($order_row['shift_id']);
        $total_amount = (int)($order_row['total_amount']);
        $order_time = $order_row['created_at'];
        $formattedDate = FormatDMY($order_time);

        
        $res_data['name'] = $item_name;
        $res_data['quantity'] = $quantity;
        $res_data['amount'] = $amount;
        $res_data['id'] = $id;
        $res_data['date'] = convertDateFormatDMY($order_time);
        $res_data['time'] = FormatHi($order_time);
        $res_data['total_amount'] = $total_amount;
        $res_data['shift_id'] = $shift_id;
        $res_data['order_no'] = $shift_id . "-" . $id . $formattedDate;
        $res_data['setting'] = $settng_data;
        array_push($data, $res_data);
    }
    echo json_encode($data); 
    
 }

    
?>