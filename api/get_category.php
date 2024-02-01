<?php
session_start();
require("../common/config.php");
require("../common/database.php");  
require("../Auth/check_cashier_auth.php");
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_data = json_decode(file_get_contents("php://input"), true);
    $parent_id = (int) ($post_data['parent_id']);
    $parent_cat_sql = "SELECT id, name , image FROM `category` WHERE parent_id = '$parent_id' and status = '$admin_enable_status' and deleted_at is null";
    $parent_cat_result = $mysqli->query($parent_cat_sql);
    $data = [];
    while($parent_cat_row = $parent_cat_result->fetch_assoc()) {
        $res_data = [];
        $cat_id = (int)($parent_cat_row['id']);
        $cat_name = htmlspecialchars($parent_cat_row['name']);
        $cat_image = $parent_cat_row['image'];
        $res_data['id'] = $cat_id;
        $res_data['name'] = $cat_name;
        $res_data['image'] = $cat_image;
        array_push($data, $res_data);
    }
    echo json_encode($data);
 }

    
?>