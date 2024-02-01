<?php
$authentication = false;
$user_id = false;
if(isset($_SESSION['cid']) || isset($_SESSION['cusername'])) {
    $user_id = $_SESSION['cid'];
  }
  if($user_id != null) {
    $check_auth = "SELECT count(id) as total FROM `user` where id = '$user_id' and role = '$cashier_role' and status = '$admin_enable_status' AND deleted_at is null AND deleted_by is null";
    $result_auth = $mysqli->query($check_auth);
    while($row_auth = $result_auth->fetch_assoc()){
      $user_total = $row_auth['total'];
      if($user_total > 0 ) {
        $authentication = true;
      }
    }

  }
  
if($authentication == false) {
    $url = $base_url."login.php";
    header("Refresh: 0; url = $url");
     exit();
}

 
  ?>