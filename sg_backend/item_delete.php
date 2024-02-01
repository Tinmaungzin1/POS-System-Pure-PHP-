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
        $url = $cp_base_url . "category_list.php?err=delete";
        header("Refresh: 0; url = $url");
        exit();
    } else { 
        $id = (int) ($_GET['id']);
        $id = $mysqli->real_escape_string($id);
        $upload_path = __DIR__ . "/../asset/upload/"; // Use __DIR__ to get the current script's directory
        $today_dt  = date('Y-m-d H:i:s');  // Now date and time
        $user_id   = (isset($_SESSION['id']) ? $_SESSION['id'] : $_COOKIE['id']);
        $sql = "UPDATE `item`
        SET deleted_at = '$today_dt',
            deleted_by = '$user_id' WHERE id = '$id'";

        $result = $mysqli->query($sql);
        $url = $cp_base_url . "item_list.php?msg=delete";
        header("Refresh: 0; url = $url");
        exit();
    }
}












// $sql = "DELETE FROM `category` where id = '$id' and deleted_at is null";

// $sql2 = "SELECT image FROM `category` WHERE id = '$id' AND deleted_at IS NULL";
// $delete_photo_result = $mysqli->query($sql2);

// if (!$delete_photo_result) {
//     die('Error: ' . $mysqli->error);
// }

// $photo_row = $delete_photo_result->fetch_assoc();

// $photo_path_dir = $id . "/" . $photo_row['image'];
// $full_directory_path = $upload_path . $photo_path_dir;

// // Check if the file and directory exist before attempting to delete
// if (file_exists($full_directory_path)) {
//     // Delete the file
//     if (unlink($full_directory_path)) {
//         // Delete the directory
//         rmdir(dirname($full_directory_path));
//     } else {
//         echo "Failed to delete the file.";
//     }
// } else {
//     echo "File or directory not found.";
// }

// $result = $mysqli->query($sql);


?>