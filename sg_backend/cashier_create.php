<?php 
session_start();
require("../common/config.php"); 
require("../common/database.php"); 
require("../Auth/check_auth.php"); 


$cashier_name = '';
$password ='';
$confirm_password ='';
$error = false;
$error_message = '';

if(isset($_POST['form-sub']) && $_POST['form-sub'] == 1) {
    $process_error = false;
    $cashier_name = $mysqli->real_escape_string($_POST['name']);
    $password = $mysqli->real_escape_string($_POST['password']);
    $confirm_password = $mysqli->real_escape_string($_POST['confirm_password']);
    
    $md5_password =  md5($shakey . md5($password));
    
    if ($cashier_name == '') {  /// user input 
        $process_error = true; 
        $error = true;
        $error_message = 'Please choose Cashier name';
    }
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
    if (!is_numeric($cashier_name)) {
        $process_error = true; 
        $error = true;
        $error_message = "Invalid username. Please enter numbers only.";
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
         // Category name check already exist//
        $check = "SELECT count(id) AS total FROM `user` WHERE username = '$cashier_name' and deleted_at IS NULL";
        $res = $mysqli->query($check);
        while($row_cunt = $res->fetch_assoc())
        {
            $total = $row_cunt['total'];
        }
        if($total > 0 ) 
        {
            $error = true;
            $error_message = "Cashier Name already exists. Choose a different Name.";
        } else { 
        $today_dt  = date('Y-m-d H:i:s');  /// now date and time
        $user_id   = (isset($_SESSION['id']) ? $_SESSION['id'] : $_COOKIE['id']);  //get user id 

        // data insert into category table ....
        $sql       = "INSERT INTO `user` (username, password, role, created_at, created_by, updated_at, updated_by) VALUES ('$cashier_name', '$md5_password', '$cashier_role', '$today_dt', '$user_id', '$today_dt', '$user_id')";
        $result = $mysqli->query($sql);
        // data insert into category table ....


        // $last_inserted_id = $mysqli->insert_id; // find last insert id 
        if($result) {
            $url = $cp_base_url."cashier_list.php?msg=create";
            header("Refresh: 0; url = $url");
            exit();
        }else {
            $url = $cp_base_url."cashier_list.php?err=create";
            header("Refresh: 0; url = $url");
            exit();
        }
        }
        
    } 
    
}
?>


<?php 
    $title = "Adminpanel::Create Cashier";
    require("../templates/cp_template_header.php");
    require("../templates/cp_template_sidebar.php");
    require("../templates/cp_template_top_nav.php");
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="clearfix"></div>
        <div class="row">
            <!-- <div class="col-md-12 col-sm-12  mb-2">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked" checked>
                    <label class="form-check-label" for="flexSwitchCheckChecked">Checked switch checkbox input</label>
                </div>
            </div> -->
            <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Cashier</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <form action="<?= $cp_base_url ?>cashier_create.php" method="POST" enctype="multipart/form-data"
                            novalidate>
                            <span class="section">Cashier Create</span>

                            <div class="field item form-group">
                                <label for="name" class="col-form-label col-md-3 col-sm-3  label-align">Cashier
                                    Name<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input id="name" type="number" class="form-control" name="name"
                                        value="<?= $cashier_name ?>" />
                                </div>
                            </div>
                            <div class="field item form-group">
                                <label for="password"
                                    class="col-form-label col-md-3 col-sm-3  label-align">Password<span
                                        class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input type="number" id="password" class="form-control" name="password"
                                        pattern="\d{8,}" value="<?= $password ?>" required />
                                </div>
                            </div>
                            <div class="field item form-group">
                                <label for="image" class="col-form-label col-md-3 col-sm-3  label-align">
                                    Confirm Password<span class="required">*</span></label>
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
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->
<?php  require("../templates/cp_template_footer_start.php"); ?>



<script src="<?= $base_url ?>asset/js/jquery1.9/jquery1.9.min.js"></script>
<script src="<?= $base_url ?>asset/js/validator/multifield.js"></script>
<!-- <script src="<?= $base_url ?>asset/js/validator/validator.js"></script> -->


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