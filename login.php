<?php 
session_start();
require("common/config.php");
require("common/database.php");
$error = false;
$error_message = '';
if(isset($_POST['form-sub']) && $_POST['form-sub'] == 1) {
    $username = $mysqli->real_escape_string($_POST['user_name']);
    $password = $_POST['password'];
    if($username == '' || $password == '') {
        $error = true;
        $error_message = "Please fill your username or password!";
      } else {
        $sql = "SELECT id, username, password FROM `user` WHERE username = '$username' and status = '$admin_enable_status' and role = '$cashier_role' AND deleted_at is null";
        $result = $mysqli->query($sql);
        $row_num = $result->num_rows;
        if($row_num <= 0) {
            $error = true;
            $error_message = "Username do not have in database!";
        }else {
            while($row = $result->fetch_assoc()){
                $db_id        = (int)($row['id']);
                $db_username  = htmlspecialchars($row['username']);
                $db_password  = $row['password'];
                $md5_password =  md5($shakey . md5($password));
                if($db_password == $md5_password) {
                    $_SESSION['cid'] = $db_id;
                    $_SESSION['cusername'] = $db_username;
                    $url = $base_url."index";
                    header("Refresh: 0; url = $url");
                    exit();
                } else {
                    $error = true;
                    $error_message = "your password do not have in database!";
                }
            }
        }
      }

}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="<?= $base_url ?>asset/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="<?= $base_url ?>asset/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="<?= $base_url ?>asset/css/login.css" />

    <script src="<?= $base_url ?>asset/bootstrap/js/jquery-2.2.4.min.js"></script>
    <script src="<?= $base_url ?>asset/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?= $base_url ?>asset/js/angular.min.js"></script>
</head>

<body>
    <section class="intro" ng-app="myApp" ng-controller="myCtrl">

        <div class="inner">

            <div class="content">
                <form class="login-form" action="<?= $base_url ?>login.php" id="myForm" method="post">

                    <table style="margin:0 auto;width: 18vw;">
                        <?php if($error == true) {  ?>
                        <tr>
                            <td colspan="3">
                                <div class="alert-danger">
                                    <?= $error_message ?>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="3">
                                <input type="text" placeholder="Enter Username" class="userInput" id="inputUsername"
                                    name="user_name" ng-focus="usernameFocus()" ng-model="username">
                            </td>
                        </tr>

                        <tr>
                            <td colspan="3"><input type="password" placeholder="Enter Password" class="userInput"
                                    id="inputPassword" name="password" ng-focus="passwordFocus()" ng-model="password">
                            </td>
                        </tr>

                        <tr>
                            <td><button type="button" class="number-btn fl-left num-btn"
                                    ng-click="numberClick('0')">0</button></td>
                            <td><button type="button" class="number-btn num-btn" ng-click="numberClick('1')">1</button>
                            </td>
                            <td><button type="button" class="number-btn fl-right num-btn"
                                    ng-click="numberClick('2')">2</button></td>
                        </tr>

                        <tr>
                            <td><button type="button" class="number-btn fl-left num-btn"
                                    ng-click="numberClick('3')">3</button></td>
                            <td><button type="button" class="number-btn num-btn" ng-click="numberClick('4')">4</button>
                            </td>
                            <td><button type="button" class="number-btn fl-right num-btn"
                                    ng-click="numberClick('5')">5</button></td>
                        </tr>

                        <tr>
                            <td><button type="button" class="number-btn fl-left num-btn"
                                    ng-click="numberClick('6')">6</button></td>
                            <td><button type="button" class="number-btn num-btn" ng-click="numberClick('7')">7</button>
                            </td>
                            <td><button type="button" class="number-btn fl-right num-btn"
                                    ng-click="numberClick('8')">8</button></td>
                        </tr>

                        <tr>
                            <td><button type="button" class="number-btn fl-left num-btn"
                                    ng-click="numberClick('9')">9</button></td>
                            <td><button type="button" class="number-btn clear-btn" ng-click="delete()">X</button>
                            </td>
                            <td><button type="button" class="number-btn fl-right enter-btn"
                                    ng-click="Login()">Enter</button></td>
                        </tr>
                    </table>
                    <input type="hidden" name="form-sub" value="1">
                </form>
            </div>
        </div>
    </section>
</body>
<script src="<?= $base_url ?>asset/js/page/login.js"></script>

</html>