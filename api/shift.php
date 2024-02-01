<?php
session_start();
require("../common/config.php");
require("../common/database.php");  
require("../Auth/check_cashier_auth.php");
$input_data = json_decode(file_get_contents("php://input"), true);

$shift_check = "SELECT count(id) AS total FROM `shift` WHERE start_date_time IS NOT NULL and end_date_time is null and deleted_at IS NULL";
$shift_res = $mysqli->query($shift_check);
while($shift_row = $shift_res->fetch_assoc())
{
    $shift_total = $shift_row['total'];
    if($shift_total > 0) {
        $message = "success";
        echo json_encode([$message]);

    } else {
        $message = "shif is closed can not order";
        http_response_code(404); // Set HTTP status code to 401
        echo json_encode([$message]);
    }
}

?>