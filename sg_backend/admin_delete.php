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
    if($shift_total > 0) {
        $url = $cp_base_url . "admin_list.php?err=delete";
        header("Refresh: 0; url = $url");
        exit();
    } else { 
        $id = (int) ($_GET['id']);
        $id = $mysqli->real_escape_string($id);
        $today_dt  = date('Y-m-d H:i:s');  // Now date and time
        $user_id   = (isset($_SESSION['id']) ? $_SESSION['id'] : $_COOKIE['id']);
        $sql = "UPDATE `user`
        SET deleted_at = '$today_dt',
            deleted_by = '$user_id' WHERE id = '$id'";

        $result = $mysqli->query($sql);
        $url = $cp_base_url . "admin_list.php?msg=delete";
        header("Refresh: 0; url = $url");
        exit();
    }
}
?>