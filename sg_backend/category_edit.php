<?php 
session_start();
require("../common/config.php");
require("../common/database.php");
require("../Auth/check_auth.php");
$order_check = "SELECT id FROM `shift` WHERE start_date_time IS NOT NULL AND end_date_time IS NULL";
$order_res = $mysqli->query($order_check);
$order_num_row = $order_res->num_rows;
  if ($order_num_row > 0) {
    $url = $cp_base_url."category_list.php?err=edit";
            header("Refresh: 0; url = $url");
            exit();
  }
$form = true;
$category_name = '';
$parent_id ='';
$error = false;
$error_message = '';
if(isset($_POST['form-sub']) && $_POST['form-sub'] == 1) {
    $upload_process = true;
    $process_error = false;
    $category_name = $mysqli->real_escape_string($_POST['name']);
    $id = $mysqli->real_escape_string($_POST['id']);
    $parent_id = $mysqli->real_escape_string($_POST['parent_id']);
    $status = $mysqli->real_escape_string($_POST['status']);
    $file = $_FILES['file'];

    if ($file['error'] != 0) {
        $upload_process = false;
    }else {
        $allow_extension = array('jpg', 'jpeg', 'png' , 'svg');   // check image extension 
        $photo_name = $file['name'];
        $explode = explode('.', $photo_name);
        $name_without_ext = $explode[0];
        $extension = end($explode);
       
        if(!in_array($extension,$allow_extension)){      // check image extension
            $process_error = true; 
            $error = true;
            $error_message .= "Please upload extension [jpg, jpeg, png, sav].";
        }else{
            $upload_path = "../asset/upload/";
            $unique_name = $name_without_ext . "_" . date("Ydm_His") . "_" . uniqid() . "." . $extension;
        }
    }

    if ($category_name == '') {  /// user input 
        $process_error = true; 
        $error = true;
        $error_message = 'Please choose category name';
    }
    if ($parent_id == '') {   /// user input 
        $process_error = true; 
        $error = true;
        $error_message = 'Please choose Parent id';
    }

    // Category name check already exist//

        // Category name check already exist//

    if($process_error == false) {
        $check = "SELECT name,parent_id,count(id) AS total FROM category WHERE name = '$category_name' and id != '$id'  and deleted_at IS NULL";
        $res = $mysqli->query($check);
        while($row_cunt = $res->fetch_assoc())
        {
            $total = $row_cunt['total'];
        }
        if($total > 0 ) 
        {
            $error = true;
            $error_message = "Category Name already exists. Choose a different Name.";
        } else { 
        $image = $file['name']; 
        $today_dt  = date('Y-m-d H:i:s');  /// now date and time
        $user_id   = (isset($_SESSION['id']) ? $_SESSION['id'] : $_COOKIE['id']);  //get user id 
        
        // ... (existing code)

    if ($upload_process == true) {
            $sql = "SELECT image FROM `category` WHERE id = '$id'";
            $old_image_result = $mysqli->query($sql);

            $old_image_res_row = $old_image_result->num_rows;
            if ($old_image_res_row > 0) {
                $old_image_row = $old_image_result->fetch_assoc();
                $old_image = $old_image_row['image'];
            }
        $sql = "UPDATE `category`
                    SET name = '$category_name',
                        parent_id = '$parent_id',
                        image     = '$unique_name',
                        status    =  '$status',
                        updated_at = '$today_dt', 
                         updated_by = '$user_id' 
                    WHERE id = '$id'";
        } else {
            $sql = "UPDATE `category`
            SET name = '$category_name',
                parent_id = '$parent_id',
                status    =  '$status', 
                updated_at = '$today_dt', 
                updated_by = '$user_id' 
            WHERE id = '$id'";
        }
        $result = $mysqli->query($sql);   

        if(!$result) {   /// data insert result error
            $error = true;
            $error_message = 'Oop! Something wrong.Please contact Administractor.';
        }else{
            if($upload_process == true) {
                $full_path_dir  = $upload_path . $id;    // upload photo part 
            $full_path_image = $full_path_dir  . "/" . $unique_name;
            if(!file_exists($full_path_dir)){
                mkdir($full_path_dir, 0777, true);
            }
            move_uploaded_file($file['tmp_name'], $full_path_image);
            $imagePath = $full_path_image;
            require("../lib/image_crop_resize.php");
            
            $old_image_full_path = $full_path_dir . '/' . $old_image;
            unlink($old_image_full_path);

            }
            $url = $cp_base_url."category_list.php?msg=update";
            header("Refresh: 0; url = $url");
            exit();   
    } 
            $url = $cp_base_url."category_list.php?err=update";
            header("Refresh: 0; url = $url");
            exit();
}
}
} else {
    $id = (int)($_GET['id']);
    $edit_sql = "SELECT id, name, parent_id, image,status FROM `category` where id = '$id' and deleted_at is null";
    $edit_res = $mysqli->query($edit_sql);
    
    $res_row = $edit_res->num_rows;
    if($res_row <= 0) {
        $form = false;
        $error = true;
        $error_message = 'This Category do not exist;'; 
    } else {
        $edit_row =  $edit_res->fetch_assoc();

        $category_name = htmlspecialchars($edit_row['name']);
        $parent_id = (int)($edit_row['parent_id']);
        $status = (int)($edit_row['status']);
        $image = $edit_row['image'];
        $full_path_img = $base_url . "asset/upload/" . $id . "/" . $image;
    }


   
}
?>


<?php 
    $title = "Adminpanel::Category Edit";
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
                        <h2>Category</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <?php if($form == true) { ?>
                        <form action="<?= $cp_base_url ?>category_edit.php" method="POST" enctype="multipart/form-data"
                            novalidate>
                            <span class="section">Category Edit</span>
                            <div class="field item form-group">
                                <label for="name" class="col-form-label col-md-3 col-sm-3  label-align">Category
                                    Edit<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input id="name" class="form-control" name="name" value="<?= $category_name ?>" />
                                </div>
                            </div>
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align">Category List<span
                                        class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <select class="select2_group form-control" name="parent_id">
                                        <option value="">Choose Category</option>
                                        <option value="0" <?php if($parent_id == 0 ) { echo "selected";} ?>>Parent
                                            Category</option>
                                        <?php    
                                        require("../include/include_category.php");
                                        getParentCategory($mysqli, $parent_id); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="field item form-group">
                                <label for="image" class="col-form-label col-md-3 col-sm-3  label-align">
                                    Category Item Image<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <div id="preview-wrapper" style="display:none;">
                                        <div class="vertical-center">
                                            <label class="choose-file" onclick="fileBrowse()" for="upload">Choose
                                                File</label>
                                        </div>
                                    </div>
                                    <div id="preview-wrapper-img">
                                        <div class="vertical-center">
                                            <img src="<?= $full_path_img; ?>" id="image-preview" style="width:100%">
                                            <label class="choose-file" onclick="fileBrowse()" for="upload">Choose
                                                File</label>
                                        </div>
                                    </div>
                                </div>
                                <input class="hide img-upload" type="file" name="file" onchange='SelectFile(this)' />

                            </div>

                            <div class="field item form-group">
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
                        <a href="<?= $cp_base_url ?>category_list.php" class="btn btn-danger btn-xs"><i
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



<script src="<?= $base_url ?>asset/js/jquery1.9/jquery1.9.min.js"></script>
<script src="<?= $base_url ?>asset/js/validator/multifield.js"></script>
<!-- <script src="<?= $base_url ?>asset/js/validator/validator.js"></script> -->



<?php 
require("../templates/cp_template_footer_end.php");
?>
<script>
function fileBrowse() {
    $('.img-upload').click();
}

function SelectFile(input) {
    const file = input.files[0];

    if (file) {
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/svg+xml'];
        // Check if the selected file type is in the allowedTypes array
        if (!allowedTypes.includes(file.type)) {

            alert("Only JPG, JPEG, PNG, and SVG files are allowed!");
        } else {
            let reader = new FileReader();
            reader.onload = function(e) {
                var imageDataUrl = e.target.result;
                $('#image-preview').attr('src', imageDataUrl);
            }
            reader.readAsDataURL(file);
            $('#preview-wrapper').hide();
            $('#preview-wrapper-img').show();
        }
    }
}
</script>
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