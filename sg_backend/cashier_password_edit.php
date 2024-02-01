<?php 
session_start();
require("../common/config.php");
require("../common/database.php");
require("../Auth/check_auth.php");
$order_check = "SELECT id FROM `shift` WHERE start_date_time IS NOT NULL AND end_date_time IS NULL";
$order_res = $mysqli->query($order_check);
$order_num_row = $order_res->num_rows;
  if ($order_num_row > 0) {
    $url = $cp_base_url."cashier_list.php?err=edit";
            header("Refresh: 0; url = $url");
            exit();
  }
$form = true;
$cashier_name = '';
$confirm_password = '';
$password = '';
$error = false;
$error_message = '';
if(isset($_POST['form-sub']) && $_POST['form-sub'] == 1) {
    $process_error = false;
    $cashier_name = $mysqli->real_escape_string($_POST['username']);
    $id = $mysqli->real_escape_string($_POST['id']);
    $password = $mysqli->real_escape_string($_POST['password']);
    $confirm_password = $mysqli->real_escape_string($_POST['confirm_password']);

    $md5_password =  md5($shakey . md5($password));


    if ($password == '') {   /// user input 
        $process_error = true; 
        $error = true;
        $error_message = 'Please Fill Password';
    }
    if ($confirm_password == '') {   /// user input 
        $process_error = true; 
        $error = true;
        $error_message = 'Please Fail Confirm Password';
    }
    if (strlen($password) < 8) {
        $process_error = true; 
        $error = true;
        $error_message = "Invalid password. Please enter at least 8 digits.";
    }
    if (!ctype_digit($password)) {
        $process_error = true; 
        $error = true;
        $error_message = "Invalid password. Please enter numbers only.";
    }
    if ($password !== $confirm_password) {
        $process_error = true; 
        $error = true;
        $error_message = "Confirm Passwords not equal to Password.";
    }
 

    if($process_error == false) {
        
        $today_dt  = date('Y-m-d H:i:s');  /// now date and time
        $user_id   = (isset($_SESSION['id']) ? $_SESSION['id'] : $_COOKIE['id']);  //get user id 
        $sql = "UPDATE `user`
                    SET password = '$md5_password',
                        updated_at = '$today_dt', 
                         updated_by = '$user_id' 
                    WHERE id = '$id'";
                    $result = $mysqli->query($sql);   

        
        if(!$result) {   /// data insert result error
            $error = true;
            $error_message = 'Oop! Something wrong.Please contact Administractor.';
        }else{
            $url = $cp_base_url."cashier_list.php?msg=update";
            header("Refresh: 0; url = $url");
            exit();   
        } 
        $url = $cp_base_url."cashier_list.php?err=update";
        header("Refresh: 0; url = $url");
        exit();
    }
} else {
    $id = (int)($_GET['id']);
    $edit_sql = "SELECT id, username,password FROM `user` where id = '$id' and deleted_at is null";
    $edit_res = $mysqli->query($edit_sql);
    
    $res_row = $edit_res->num_rows;
    if($res_row <= 0) {
        $form = false;
        $error = true;
        $error_message = 'This Cashier Name do not exist;'; 
    } else {
        $edit_row =  $edit_res->fetch_assoc();

        $cashier_name = (int)($edit_row['username']);

    }
   
}
?>


<?php 
    $title = "Adminpanel::Cashier Password Edit";
    require("../templates/cp_template_header.php");
    require("../templates/cp_template_sidebar.php");
    require("../templates/cp_template_top_nav.php");
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Cashier</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <?php if($form == true) { ?>
                        <form action="<?= $cp_base_url ?>cashier_password_edit.php" method="POST"
                            enctype="multipart/form-data" novalidate>
                            <span class="section">Cashier Password Edit</span>

                            <div class="field item form-group">
                                <label for="name" class="col-form-label col-md-3 col-sm-3  label-align">Cashier
                                    Name<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input id="name" type="number" class="form-control" name="name"
                                        value="<?= $cashier_name ?>" disabled />
                                </div>
                            </div>
                            <div class="field item form-group">
                                <label for="password" class="col-form-label col-md-3 col-sm-3  label-align">New
                                    Password<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input type="number" id="password" class="form-control" name="password"
                                        pattern="\d{8,}" value="<?= $password ?>" required />
                                </div>
                            </div>
                            <div class="field item form-group">
                                <label for="image" class="col-form-label col-md-3 col-sm-3  label-align">
                                    Confirm New Password<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input type="number" id="confirm_password" class="form-control"
                                        name="confirm_password" value="<?= $confirm_password ?>" />
                                </div>

                            </div>



                            <div class="ln_solid">
                                <div class="form-group">
                                    <div class="col-md-6 offset-md-3">
                                        <button type='submit' class="btn btn-primary">Submit</button>
                                        <button type='reset' class="btn btn-success">Reset</button>
                                        <input type="hidden" name="form-sub" value="1">
                                        <input type="hidden" name="id" value="<?= $id ?>">
                                    </div>
                                </div>
                            </div>
                        </form>
                        <?php } ?>
                        <?php if($form == false) { ?>
                        <a href="<?= $cp_base_url ?>cashier_list.php" class="btn btn-danger btn-xs"><i
                                class="fa fa-reply"></i>
                            Go Back </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->
<?php  require("../templates/cp_template_footer_start.php"); ?>


<?php 
require("../templates/cp_template_footer_end.php");
?>

<?php if($error == true) { ?>
<script>
swal({
    title: "Error!",
    text: "<?= $error_message ?>",
    type: "error",
    confirmButtonText: "OK"
});
</script>
<?php } ?>
<?php
require("../templates/cp_template_html_end.php");
?>