<?php
session_start();
require("../common/config.php");
require("../common/database.php");  
require("../Auth/check_cashier_auth.php");
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_data = json_decode(file_get_contents("php://input"), true);
    $order_id = (int) ($post_data['id']);
    $get_items_sql = "SELECT item_id,quantity FROM order_detail WHERE order_id = '$order_id' AND deleted_at IS NULL";
    $get_items_result = $mysqli->query($get_items_sql);
    $itemsId = [];
    $itemsQty = [];
    while($get_items_row = $get_items_result->fetch_assoc()) {
        array_push($itemsQty, $get_items_row['quantity']);
        array_push($itemsId, $get_items_row['item_id']);

    }
 $ids = implode(',', $itemsId);
 
    $now  = date('Y-m-d');  /// now date and time
    $item_sql = "SELECT
    t1.id,
    t1.category_id, 
    t1.name , 
    t1.image, 
    t1.price, 
    t1.code_no,
    CAST(CASE 
        WHEN t3.amount IS NULL AND '$now' BETWEEN t3.start_date AND t3.end_date 
        THEN (t1.price * t3.percentage / 100)
        WHEN t3.amount IS NOT NULL AND '$now' BETWEEN t3.start_date AND t3.end_date
        THEN t3.amount
        ELSE 0
    END AS UNSIGNED) as Calculated_value
    FROM
    `item` AS t1
    LEFT JOIN
    `discount_item` AS t2 ON t1.id = t2.item_id
    LEFT JOIN
    `discount_promotion` AS t3 ON t2.discount_promotion_id = t3.id 
    WHERE 
        t1.id IN ($ids)
        AND t1.status = '$admin_enable_status' 
        AND t1.deleted_at is null
    ";
    $item_result = $mysqli->query($item_sql);
    $data = [];
    while($item_row = $item_result->fetch_assoc()) {
        $res_data = [];
        $item_id = (int)($item_row['id']);
        $item_name = htmlspecialchars($item_row['name']);
        $item_image = $item_row['image'];
        $item_price = $item_row['price'];
        $item_code = $item_row['code_no'];
        $res_data['id'] = $item_id;
        $res_data['name'] = $item_name;
        $res_data['image'] = $item_image;
        $res_data['price'] = $item_price;
        $res_data['code_no'] = $item_code;
        $res_data['quantity'] = 1;
        $res_data['discount'] = $item_row['Calculated_value'];
        $res_data['origin_discount'] = $item_row['Calculated_value'];
        $res_data['amount'] = $item_price - $item_row['Calculated_value'];
        $res_data['origin_amount'] = $item_price - $item_row['Calculated_value'];
        array_push($data, $res_data);
    }
    echo json_encode($data); 
}   
?>