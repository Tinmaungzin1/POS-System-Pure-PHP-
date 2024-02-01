<?php
session_start();
require("../common/config.php");
require("../common/database.php");  
require("../Auth/check_cashier_auth.php");
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_data = json_decode(file_get_contents("php://input"), true);
    $category_id = (int) ($post_data['category_id']);
    $item_sql = "SELECT id, name , image, price, code_no FROM `item` WHERE category_id = '$category_id' and status = '$admin_enable_status' and deleted_at is null";
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
        array_push($data, $res_data);
    }
    echo json_encode($data); 
}   
?>