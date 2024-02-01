<?php 
session_start();
require("../common/config.php");
require("../common/database.php");
require("../Auth/check_auth.php");
$form = true;
$admin_name = '';
$error = false;
$error_message = '';
if(isset($_POST['form-sub']) && $_POST['form-sub'] == 1) {
    $process_error = false;
    $admin_name = $mysqli->real_escape_string($_POST['username']);
    $id = $mysqli->real_escape_string($_POST['id']);


    if ($admin_name == '') {  /// user input 
        $process_error = true; 
        $error = true;
        $error_message = 'Please choose Admin name';
    }
 

    if($process_error == false) {
        // Category name check already exist//
        $check = "SELECT count(id) AS total FROM `user` WHERE username = '$admin_name' and id != '$id' and deleted_at IS NULL";
        $res = $mysqli->query($check);
        while($row_cunt = $res->fetch_assoc())
        {
            $total = $row_cunt['total'];
        }
        if($total > 0 ) 
        {
            $error = true;
            $error_message = "Admin Name already exists. Choose a different Name.";
        } else {
            $today_dt  = date('Y-m-d H:i:s');  /// now date and time
        $user_id   = (isset($_SESSION['id']) ? $_SESSION['id'] : $_COOKIE['id']);  //get user id 
        $sql = "UPDATE `user`
                    SET username = '$admin_name',
                        updated_at = '$today_dt', 
                         updated_by = '$user_id' 
                    WHERE id = '$id'";
                    $result = $mysqli->query($sql);   

        
        if(!$result) {   /// data insert result error
            $error = true;
            $error_message = 'Oop! Something wrong.Please contact Administractor.';
        }else{
            $url = $cp_base_url."admin_list.php?msg=update";
            header("Refresh: 0; url = $url");
            exit();   
        } 
        $url = $cp_base_url."admin_list.php?err=update";
        header("Refresh: 0; url = $url");
        exit();
    }
        
       
}
} else {
    $id = (int)($_GET['id']);
    $edit_sql = "SELECT id, username FROM `user` where id = '$id' and role = '$admin_role' and deleted_at is null";
    $edit_res = $mysqli->query($edit_sql);
    
    $res_row = $edit_res->num_rows;
    if($res_row <= 0) {
        $form = false;
        $error = true;
        $error_message = 'This Admin Name do not exist;'; 
    } else {
        $edit_row =  $edit_res->fetch_assoc();

        $admin_name = htmlspecialchars($edit_row['username']);
    }   
   
}
?>


<?php 
    $title = "Adminpanel::Admin Name Edit";
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
                        <h2>Admin</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <?php if($form == true) { ?>
                        <form action="<?= $cp_base_url ?>admin_name_edit.php" method="POST"
                            enctype="multipart/form-data" novalidate>
                            <span class="section">Admin Name Edit</span>

                            <div class="field item form-group">
                                <label for="name" class="col-form-label col-md-3 col-sm-3  label-align">Admin
                                    Name<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input id="name" type="text" class="form-control" name="username"
                                        value="<?= $admin_name ?>" />
                                </div>
                            </div>

                            <!-- <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align">Status<span
                                        class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <div class="radio">
                                        <label for="enable">
                                            <input id="enable" type="radio" class="flat" name="status" value="0"
                                                <?php if($status == 0) {echo 'checked';} ?>>
                                            Enable
                                        </label>&nbsp;&nbsp;&nbsp;
                                        <label for="disable">
                                            <input id="disable" type="radio" class="flat" name="status" value="1"
                                                <?php if($status != 0) {echo 'checked';} ?>>
                                            Disable
                                        </label>
                                    </div>
                                </div>
                            </div> -->

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
                        <a href="<?= $cp_base_url ?>admin_list.php" class="btn btn-danger btn-xs"><i
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