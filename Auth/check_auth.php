<?php
$authentication = false;
$user_id = false;
if(isset($_SESSION['id']) || isset($_SESSION['username'])) {
    $user_id = $_SESSION['id'];
  } else {
    if(isset($_COOKIE['id']) || isset($_COOKIE['username'])) {
      $user_id = $_COOKIE['id'];
      }
  }
  if($user_id != null) {
    $check_auth = "SELECT count(id) as total FROM `user` where id = '$user_id' AND deleted_at is null AND deleted_by is null";
    $result_auth = $mysqli->query($check_auth);
    while($row_auth = $result_auth->fetch_assoc()){
      $user_total = $row_auth['total'];
      if($user_total > 0 ) {
        $authentication = true;
      }
    }

  }
  
if($authentication == false) {
    $url = $cp_base_url."login.php";
    header("Refresh: 0; url = $url");
     exit();
}
 
  ?>