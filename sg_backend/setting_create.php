<?php 
session_start();
require("../common/config.php");
require("../common/database.php");
require("../Auth/check_auth.php");
$company_name = "";
$company_phone ="";
$company_email ="";
$company_address="";
$error = false;
$error_message = '';
$image_exit = false;
if(isset($_POST['form-sub']) && $_POST['form-sub'] == 1) {
    $upload_process = true;
    $process_error = false;
    $company_name = $mysqli->real_escape_string($_POST['name']);
    $company_phone = $mysqli->real_escape_string($_POST['phone']);
    $company_email = $mysqli->real_escape_string($_POST['email']);
    $company_address = $mysqli->real_escape_string($_POST['address']);
    
    $file = $_FILES['file'];
    if ($file['error'] != 0) {
        $upload_process = false;
    }else {
        $allow_extension = array('jpg', 'jpeg', 'png');   // check image extension 
        $photo_name = $file['name'];
        $explode = explode('.', $photo_name);
        $name_without_ext = $explode[0];
        $extension = end($explode);
       
        if(!in_array($extension,$allow_extension)){      // check image extension
            $process_error = true; 
            $error = true;
            $error_message = "Please upload extension [jpg, jpeg, png].";
        }else{
            $upload_path = "../asset/image/company-logo/";
            $unique_name = $name_without_ext . "_" . date("Ydm_His") . "_" . uniqid() . "." . $extension;
        }
    }

    if ($company_name == '') {  /// user input 
        $process_error = true; 
        $error = true;
        $error_message = 'Please Fill Company Name';
    }
    
    if ($company_phone == '') {   /// user input 
        $process_error = true; 
        $error = true;
        $error_message = 'Please choose Company Phone';
    }
    if ($company_email == '') {   /// user input 
        $process_error = true; 
        $error = true;
        $error_message = 'Please Fill Company Email';
    }
    if (!filter_var($company_email, FILTER_VALIDATE_EMAIL)) {
        $process_error = true; 
        $error = true;
        $error_message = "Invalid email address.";

    }
    if ($company_address == '') {   /// user input 
        $process_error = true; 
        $error = true;
        $error_message = 'Please Fill Company Address';
    }
    

    // Category name check already exist//

        // Category name check already exist//

    if($process_error == false) {
            $image = $file['name']; 
            $today_dt  = date('Y-m-d H:i:s');  /// now date and time
            $user_id   = (isset($_SESSION['id']) ? $_SESSION['id'] : $_COOKIE['id']);  //get user id 

           

        if($upload_process == true) {
            $check_existing_data = "SELECT id, company_logo FROM `setting`";
            $result_existing_data = $mysqli->query($check_existing_data);

            if ($result_existing_data->num_rows > 0) {
                $image_exit = true;
                $old_image_row = $result_existing_data->fetch_assoc();
                $old_image = $old_image_row['company_logo'];
                $id = $old_image_row['id'];

                $update_sql = "UPDATE `setting` 
                    SET company_name = '$company_name',
                        company_phone = '$company_phone', 
                        company_email = '$company_email', 
                        company_address = '$company_address', 
                        company_logo = '$unique_name'";
                $result = $mysqli->query($update_sql);
                } else {

                    $sql = "INSERT INTO `setting` 
                            (company_name, company_phone, company_email, company_address, company_logo) VALUES  
                            ('$company_name', '$company_phone','$company_email', '$company_address', '$unique_name')";
                    $result = $mysqli->query($sql);
                    $id = $mysqli->insert_id;
                    } 

            } else {

                $update_sql = "UPDATE `setting` 
                    SET company_name = '$company_name', 
                        company_phone = '$company_phone', 
                        company_email = '$company_email', 
                        company_address = '$company_address'";
                $result = $mysqli->query($update_sql);
                
            }
               
            if(!$result) {   /// data insert result error
                $error = true;
                $error_message = 'Oop! Something wrong.Please contact Administractor.';
            }else{

               if($upload_process == true) {  //where $id 
                    $full_path_dir  = $upload_path . $id;    // upload photo part error this herreeeee
                    $full_path_image = $full_path_dir  . "/" . $unique_name;

                    if(!file_exists($full_path_dir)){
                        mkdir($full_path_dir, 0777, true);
                    }
                    
                    move_uploaded_file($file['tmp_name'], $full_path_image);
                    $imagePath = $full_path_image;
                    require("../lib/image_crop_resize.php");

                    if($image_exit == true){
                        if($old_image != $unique_name) {
                            $old_image_full_path = $full_path_dir . '/' . $old_image;
                            unlink($old_image_full_path);
                        }                   
                    }              
                }
            $url = $cp_base_url."list.php?msg=create";
            header("Refresh: 0; url = $url");
            exit();              
        }
        
    }

} else {

    $setting = "SELECT * FROM `setting`";
    $result = $mysqli->query($setting);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $id = (int) ($row['id']);
        $company_name = htmlspecialchars($row['company_name']);
        $company_phone = htmlspecialchars($row['company_phone']);
        $company_email = htmlspecialchars($row['company_email']);
        $company_address = htmlspecialchars($row['company_address']);
        $company_logo = ($row['company_logo']);
        $full_path_img = $base_url . "asset/image/company-logo/" . $id . "/" . $company_logo;
    } else {
        // Set default values or handle the case when no data is found
        $company_name = "";
        $company_phone = "";
        $company_email = "";
        $company_address = "";
        $full_path_img = ""; // You might want to set a default image path or handle it accordingly
    }
}


?>


<?php 
    $title = "Adminpanel::Create Setting";
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
                        <h2>Setting</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <form action="<?= $cp_base_url ?>setting_create.php" method="POST" enctype="multipart/form-data"
                            novalidate>
                            <span class="section">Setting Create</span>

                            <div class="field item form-group">
                                <label for="name" class="col-form-label col-md-3 col-sm-3  label-align">Company
                                    Name<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input id="name" type="text" class="form-control" name="name"
                                        value="<?= $company_name ?>" />
                                </div>
                            </div>

                            <div class="field item form-group">
                                <label for="phone" class="col-form-label col-md-3 col-sm-3  label-align">Company
                                    Phone<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input type="text" id="phone" class="form-control" name="phone"
                                        value="<?= $company_phone ?>" />
                                </div>
                            </div>

                            <div class="field item form-group">
                                <label for="email" class="col-form-label col-md-3 col-sm-3  label-align">Company
                                    Email<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" id="email" name="email" class='email'
                                        required="required" type="email" value="<?= $company_email?>" />
                                </div>
                            </div>

                            <div class="field item form-group">
                                <label for="address" class="col-form-label col-md-3 col-sm-3  label-align">
                                    Company Address<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <textarea id="address" class="form-control"
                                        name="address"><?= $company_address ?></textarea>
                                </div>
                            </div>

                            <div class="field item form-group">
                                <label for="image" class="col-form-label col-md-3 col-sm-3  label-align">
                                    Item Image<span class="required">*</span></label>
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