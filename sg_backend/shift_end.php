<?php 
session_start();
require("../common/config.php");
require("../common/database.php");
require("../Auth/check_auth.php");

$shift_check = "SELECT id FROM `shift` WHERE start_date_time IS NOT NULL and end_date_time is null and deleted_at IS NULL";
$shift_res = $mysqli->query($shift_check);
$shift_num_row = $shift_res->num_rows;


    if($shift_num_row <= 0) {
    $url = $cp_base_url."shift.php?err=end";
    header("Refresh: 0; url = $url");
    exit();
    } else { 

        $shift_row = $shift_res->fetch_assoc();
        $shift_id = $shift_row['id'];
        $order_check = "SELECT count(id) AS total FROM `orders` WHERE status = '$unpaid_status' AND shift_id = '$shift_id' AND deleted_at IS NULL";
        $order_result = $mysqli->query($order_check);
        while($order_row = $order_result->fetch_assoc())
        {
            $order_total = $order_row['total'];
        }
            if($order_total > 0) {
            $url = $cp_base_url."shift.php?err=orderstart";
            header("Refresh: 0; url = $url");
            exit();
            } else { 
        $today_dt  = date('Y-m-d H:i:s');  /// now date and time
        $user_id   = (isset($_SESSION['id']) ? $_SESSION['id'] : $_COOKIE['id']);  //get user id 
        $sql = "UPDATE `shift`
                    SET end_date_time = '$today_dt',
                        updated_at = '$today_dt', 
                        updated_by = '$user_id' 
                    WHERE end_date_time is null";
        $result = $mysqli->query($sql);
        $url = $cp_base_url."shift.php?msg=end";
        header("Refresh: 0; url = $url");
        exit();
            }
    }


?>