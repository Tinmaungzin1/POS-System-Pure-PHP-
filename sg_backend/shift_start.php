<?php 
session_start();
require("../common/config.php");
require("../common/database.php");
require("../Auth/check_auth.php");

$shift_check = "SELECT count(id) AS total FROM `shift` WHERE start_date_time IS NOT NULL and end_date_time is null and deleted_at IS NULL";
$shift_res = $mysqli->query($shift_check);
while($shift_row = $shift_res->fetch_assoc())
{
    $shift_total = $shift_row['total'];
}
    if($shift_total > 0) {
    $url = $cp_base_url."shift.php?err=start";
    header("Refresh: 0; url = $url");
    exit();
    } else {
        
        $today_dt  = date('Y-m-d H:i:s');  /// now date and time
        $user_id   = (isset($_SESSION['id']) ? $_SESSION['id'] : $_COOKIE['id']);  //get user id 
        $sql       = "INSERT INTO `shift` (start_date_time, created_at, created_by, updated_at, updated_by) 
                        VALUES ('$today_dt', '$today_dt', '$user_id', '$today_dt', '$user_id')";
        $result = $mysqli->query($sql);
        
        $url = $cp_base_url."shift.php?msg=start";
        header("Refresh: 0; url = $url");
        exit();
            
        }
?>