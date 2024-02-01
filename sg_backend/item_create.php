<?php 
session_start();
require("../common/config.php");
require("../common/database.php");
require("../Auth/check_auth.php");
$item_name = "";
$parent_id ="";
$price ="";
$quantity="";
$code ="";
$error = false;
$error_message = '';

if(isset($_POST['form-sub']) && $_POST['form-sub'] == 1) {
    $process_error = false;
    $item_name = $mysqli->real_escape_string($_POST['name']);
    $parent_id = $mysqli->real_escape_string($_POST['category_id']);
    $price = $mysqli->real_escape_string($_POST['price']);
    $quantity = $mysqli->real_escape_string($_POST['quantity']);
    
    $file = $_FILES['file'];
    
    if ($file['error'] != 0) {
        $process_error = true; 
        $error = true;
        $error_message = 'Please upload Category image';
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
            $upload_path = "../asset/item/";
            $unique_name = $name_without_ext . "_" . date("Ydm_His") . "_" . uniqid() . "." . $extension;
        }
    }

    if ($quantity == '') {  /// user input 
        $process_error = true; 
        $error = true;
        $error_message = 'Please Fill quantity';
    }

    if ($price == '') {   /// user input 
        $process_error = true; 
        $error = true;
        $error_message = 'Please Fill Price';
    }
    if ($parent_id == '') {   /// user input 
        $process_error = true; 
        $error = true;
        $error_message = 'Please choose Category id';
    }
    if ($item_name == '') {   /// user input 
        $process_error = true; 
        $error = true;
        $error_message = 'Please Fill Item name';
    }
    

    // Category name check already exist//

        // Category name check already exist//

    if($process_error == false) {
        $check = "SELECT count(id) AS total FROM `item` WHERE name = '$item_name' and deleted_at IS NULL";
        $res = $mysqli->query($check);
        while($row_cunt = $res->fetch_assoc())
        {
            $total = $row_cunt['total'];
        }
        if($total > 0 ) 
        {
            $error = true;
            $error_message .= "Item Name already exists. Choose a different Name.";
        } else {
            $image = $file['name']; 
            $today_dt  = date('Y-m-d H:i:s');  /// now date and time
            $user_id   = (isset($_SESSION['id']) ? $_SESSION['id'] : $_COOKIE['id']);  //get user id 
    
    
            // data insert into category table ....
            $sql       = "INSERT INTO `item` (name, category_id, price, quantity, image, created_at, created_by, updated_at, updated_by) VALUES ('$item_name', '$parent_id','$price', '$quantity', '$unique_name', '$today_dt', '$user_id', '$today_dt', '$user_id')";
            $result = $mysqli->query($sql);
    
            // data insert into category table ....
    
    
            $last_inserted_id = $mysqli->insert_id; // find last insert id 
    
            
            if(!$result) {   /// data insert result error
                $error = true;
                $error_message = 'Oop! Something wrong.Please contact Administractor.';
            }else{
                
                $full_path_dir  = $upload_path . $last_inserted_id;    // upload photo part 
                $full_path_image = $full_path_dir  . "/" . $unique_name;
                if(!file_exists($full_path_dir)){
                    mkdir($full_path_dir, 0777, true);
                }
                move_uploaded_file($file['tmp_name'], $full_path_image);
                $imagePath = $full_path_image;
                require("../lib/image_crop_resize.php");
    
                $randomString = chr(rand(65, 90)) . chr(rand(95, 122)) . chr(rand(65, 90)) . chr(rand(95, 122));
                $code_no = $user_id . $last_inserted_id . '-' . $randomString;
                $code_sql = "UPDATE `item` SET code_no ='$code_no'
                WHERE id = '$last_inserted_id' and deleted_at is null";
                $code_result = $mysqli->query($code_sql);
        
        
                
                $url = $cp_base_url."item_list.php?msg=create";
                header("Refresh: 0; url = $url");
                exit();
            }
            $url = $cp_base_url."item_list.php?err=create";
            header("Refresh: 0; url = $url");
            exit();
        }
       

    } 


}
?>


<?php 
    $title = "Adminpanel::Create Item";
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
                        <h2>Item</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <form action="<?= $cp_base_url ?>item_create.php" method="POST" enctype="multipart/form-data"
                            novalidate>
                            <span class="section">Item Create</span>
                            <div class="field item form-group">
                                <label for="name" class="col-form-label col-md-3 col-sm-3  label-align">Item
                                    Name<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input id="name" class="form-control" name="name" value="<?= $item_name ?>" />
                                </div>
                            </div>

                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align">Category List<span
                                        class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <select class="select2_group form-control" name="category_id">
                                        <option value="">Choose Category</option>
                                        <option value="0" <?php if($parent_id == 0 ) { echo "selected";} ?>>
                                            Parent Category</option>
                                        <?php   
                                        require("../include/include_category.php"); 
                                        getParentCategory($mysqli, $parent_id, true);
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="field item form-group">
                                <label id="price" class="col-form-label col-md-3 col-sm-3  label-align">Price<span
                                        class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input id="price" class="form-control" type="number" value="<?= $price ?>"
                                        class='number' name="price">
                                </div>
                            </div>

                            <div class="field item form-group">
                                <label id="quantity" class="col-form-label col-md-3 col-sm-3  label-align">Quantity
                                    <span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" id="quantity" type="number" value="<?= $quantity ?>"
                                        class='number' name="quantity">
                                </div>
                            </div>

                            <!-- <div class="field item form-group">
                                <label for="code" class="col-form-label col-md-3 col-sm-3  label-align">Code Number<span
                                        class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input id="code" class="form-control" name="code" value="<?= $code ?>"
                                        type="text" />
                                </div>
                            </div> -->


                            <div class="field item form-group">
                                <label for="image" class="col-form-label col-md-3 col-sm-3  label-align">
                                    Category Item Image<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <div id="preview-wrapper">
                                        <div class="vertical-center">
                                            <label class="choose-file" onclick="fileBrowse()" for="upload">Choose
                                                File</label>
                                        </div>
                                    </div>
                                    <div id="preview-wrapper-img" style="display:none;">
                                        <div class="vertical-center">
                                            <img src="" id="image-preview" style="width:100%">
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
<!-- <script src="<?= $base_url ?>asset/js/validator/validator.js"></script> -->


<script>
</script>

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
Swal.fire({
    title: 'error!',
    text: '<?= $error_message?>',
    icon: 'error', // Can be 'success', 'error', 'warning', 'info', 'question'
});
</script>
<?php } ?>

<?php
require("../templates/cp_template_html_end.php");
?>