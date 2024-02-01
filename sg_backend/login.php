<?php 
session_start();
require("../common/config.php");
        require("../common/database.php");

$error = false;
$error_message ='';

if(isset($_POST['form-sub']) && $_POST['form-sub'] == 1 ) {
  $username = $mysqli->real_escape_string($_POST['username']);
  $password = $_POST['password'];
  $remember = isset($_POST['remember']) ? $_POST['remember'] : 0;
  if($username == '' || $password == '') {
    $error = true;
    $error_message = "Please fill your username or password!";
  }else {
    $sql = "SELECT id,username, password, role FROM `user` where username = '$username' and status= '$admin_enable_status' and deleted_at is null and deleted_by is null";
    $result = $mysqli->query($sql);
    $row_num = $result->num_rows;
    if($row_num > 0) {
    while($row = $result->fetch_assoc()){
      $db_id        = (int)($row['id']);
      $db_username  = htmlspecialchars($row['username']);
      $db_password  = $row['password'];
      $md5_password =  md5($shakey . md5($password));
      $role         = (int)($row['role']);
      if($db_password == $md5_password) {
          if($role == $admin_row){
            if($remember == 1){
              // set cookie
              $cookiename = 'id';
              $cookievalue = $db_id;
              setcookie($cookiename, $cookievalue, time() + (86400 * 30), '/');
              $cookiename = 'username';
              $cookievalue = $db_username;
              setcookie($cookiename, $cookievalue, time() + (86400 * 30), '/');
              $url = $cp_base_url."index.php";
              header("Refresh: 0; url = $url");
              exit();
            }else {
              // set session
              $_SESSION['id'] = $db_id;
              $_SESSION['username'] = $db_username;
              $url = $cp_base_url."index.php";
              header("Refresh: 0; url = $url");
              exit();
            }
          }else {
            $error = true;
            $error_message = "you can not see!";
        }

      }else {
        $error = true;
        $error_message = "your password is invilade";
      }
    }
    } else {
      $error = true;
      $error_message = "your username is invilade";
    }
  }          
}else {

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Admin Login</title>

    <!-- Bootstrap -->
    <link href="<?= $base_url ?>asset/css/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?= $base_url ?>asset/css/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <!-- <link href="../vendors/nprogress/nprogress.css" rel="stylesheet"> -->
    <!-- Animate.css -->
    <!-- <link href="../vendors/animate.css/animate.min.css" rel="stylesheet"> -->

    <!-- Custom Theme Style -->
    <link href="<?= $base_url ?>asset/css/build/css/custom.css?v=20231211" rel="stylesheet">
    <link href="<?= $base_url ?>asset/css/sweet-alert/sweet-alert.css" rel="stylesheet">
    <script src="<?= $base_url ?>asset/js/sweet-alert/sweet-alert.min.js"></script>

</head>

<body class="login">
    <div>
        <a class="hiddenanchor" id="signup"></a>
        <a class="hiddenanchor" id="signin"></a>

        <div class="login_wrapper">
            <div class="animate form login_form">
                <section class="login_content">

                    <form action="<?= $cp_base_url ?>login.php" method="POST">
                        <h1>Login Form</h1>
                        <div>
                            <input type="text" class="form-control" placeholder="Username" name="username" />
                        </div>
                        <div>
                            <input type="password" class="form-control" placeholder="Password" name="password" />
                        </div>
                        <div>
                            <input type="checkbox" id="remember" name="remember" value="1" /> <label
                                for="remember">Remember me</label>
                            <button type="submit" class="btn btn-default submit">Log in</button>
                            <input type="hidden" name="form-sub" value="1">
                        </div>
                        <div class="clearfix"></div>
                        <div class="separator">
                            <div class="clearfix"></div>
                            <br />
                        </div>
                        <div>
                            <p>@2023 all Rights Reserved. </p>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
</body>
<?php if($error == true) { ?>
<script>
swal({
    title: "Error!",
    text: "<?= $error_message ?>",
    type: "error",
    confirmButtonText: "Error"
});
</script>
<?php } ?>


</html>