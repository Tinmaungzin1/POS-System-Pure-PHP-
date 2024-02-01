<?php 
session_start();
require("../common/config.php");
require("../common/database.php");
require("../Auth/check_auth.php");
require("../include/include_function.php");
$order_check = "SELECT id FROM `shift` WHERE start_date_time IS NOT NULL AND end_date_time IS NULL";
$order_res = $mysqli->query($order_check);
$order_num_row = $order_res->num_rows;
  if ($order_num_row > 0) {
    $url = $cp_base_url."discount_list.php?err=edit";
            header("Refresh: 0; url = $url");
            exit();
  }
$form = true;
$discount_name = "";
$discount_type = "";
$discount_amount ="";
$item = [];
$start_date="";
$end_date ="";
$error = false;
$error_message = '';
$item_names = array();


if(isset($_POST['form-sub']) && $_POST['form-sub'] == 1) {
    $process_error = false;

    $id = (int)($_POST['id']);
    $discount_name = $mysqli->real_escape_string($_POST['name']);
    $discount_type   = $mysqli->real_escape_string($_POST['discount_type']);
    $discount_amount   = $mysqli->real_escape_string($_POST['amount']);
    $start_date = $mysqli->real_escape_string($_POST['startDate']);
    $end_date = $mysqli->real_escape_string($_POST['endDate']);
    $status = $mysqli->real_escape_string($_POST['status']);
    $item = (isset($_POST['item'])) ? $_POST['item'] : [];
    $description = $mysqli->real_escape_string($_POST['description']);
    $start_date_ymt = convertDateFormatYMD($start_date);
    $end_date_ymt = convertDateFormatYMD($end_date);



    if ($start_date_ymt > $end_date_ymt) {
        $process_error = true; 
        $error = true;
        $error_message = "End date must be greater than start date.";
    }
    
    if ($discount_name == '') {  /// user input 
        $process_error = true; 
        $error = true;
        $error_message = 'Please Fill Discount Name';
    }
    if ($discount_amount == '') {   /// user input 
        $process_error = true; 
        $error = true;
        $error_message = 'Please Fill Discount amount';
    }
    if ($start_date == '') {   /// user input 
        $process_error = true; 
        $error = true;
        $error_message = 'Please Choose Start Date';
    }
    if ($end_date == '') {   /// user input 
        $process_error = true; 
        $error = true;
        $error_message = 'Please Choose End Date';
    }
    if ($item == []) {   /// user input 
        $process_error = true; 
        $error = true;
        $error_message = 'Please Choose item';
    }

    if($discount_type == "Percentage") {
        if ($discount_amount >= 100) { 
            $process_error = true; 
            $error = true;
            $error_message = 'Percentage is Greater than 100%.';
        }
    }
    if ($discount_type == "Cash") {
        foreach ($item as $product) {
            $item_sql = "SELECT name, price FROM `item` WHERE id = '$product' AND deleted_at IS NULL";
            $it_result = $mysqli->query($item_sql);
        
            if ($it_result && $item_row = $it_result->fetch_assoc()) {
                $row_name = $item_row['name'];
                $row_price = $item_row['price'];
                if ($discount_amount > $row_price) {
                    // Add the name to the array
                    $item_names[] = $row_name;
                    $process_error = $error = true;
                }
            } else {
                $process_error = $error = true;
                $error_message = 'Database error: ' . $mysqli->error;
            }
        }
        
        // Check if there are multiple names, then concatenate them in parentheses
        if (!empty($item_names)) {
            $error_message = 'Amount is Greater than Item(' . implode(', ', $item_names) . ') Price';
        }
    }
    

    //Category name check already exist//

        // Category name check already exist//

    if($process_error == false) { 
        $check = "SELECT name,count(id) AS total FROM `discount_promotion` WHERE name = '$discount_name' and id != '$id' and deleted_at IS NULL";
        $res = $mysqli->query($check);
        while($row_cunt = $res->fetch_assoc())
        {
            $total = $row_cunt['total'];
        }

        if($total > 0 ) 
        {
            $error = true;
            $error_message = "Discount Name already exists. Choose a different Name.";
        } else {
            $today_dt  = date('Y-m-d H:i:s');  /// now date and time
        $user_id   = (isset($_SESSION['id']) ? $_SESSION['id'] : $_COOKIE['id']);  //get user id


        // data insert into category table ....
        if($discount_type == "Percentage") {

            $sql ="UPDATE `discount_promotion`
            SET 
                name = '$discount_name',
                amount = NULL,
                percentage = '$discount_amount',
                start_date = '$start_date_ymt',
                end_date = '$end_date_ymt',
                description = '$description',
                status     = '$status',
                updated_at = '$today_dt',
                updated_by = '$user_id'
            WHERE 
                id = '$id'";
            
        } else {

            $sql ="UPDATE `discount_promotion`
            SET 
                name = '$discount_name',
                amount = '$discount_amount',
                percentage = NULL,
                start_date = '$start_date_ymt',
                end_date = '$end_date_ymt',
                description = '$description',
                status     = '$status',
                updated_at = '$today_dt',
                updated_by = '$user_id'
            WHERE 
                id = '$id' ";
        }
        $result = $mysqli->query($sql);

        
        if(!$result) {   /// data insert result error
            $process_error = true; 
            $error = true;
            $error_message = 'Update is Error';
        }else{
            $delete_sql = "DELETE FROM `discount_item` where discount_promotion_id = '$id' and deleted_at is null";
            $delete_result = $mysqli->query($delete_sql);
            if($delete_result) {
            foreach($item as $ite) {
                $dis_item_sql  = "INSERT INTO `discount_item` (item_id, discount_promotion_id, created_at,      created_by, updated_at, updated_by) VALUES ('$ite', '$id', '$today_dt', '$user_id', '$today_dt', '$user_id')";
                $dis_item_result = $mysqli->query($dis_item_sql);
                 }
                }
                $url = $cp_base_url."discount_list.php?msg=update";
                header("Refresh: 0; url = $url");
                exit();
              }
              $url = $cp_base_url."discount_list.php?err=update";
              header("Refresh: 0; url = $url");
              exit();
        }
        

     
    } 
} else {
    $id = (int)($_GET['id']);
    $edit_sql =  "SELECT name,amount, percentage,
     start_date,end_date,description,status FROM `discount_promotion` where id = '$id' And deleted_at IS NULL";
    $edit_res = $mysqli->query($edit_sql);

    $res_row = $edit_res->num_rows;
    if($res_row <= 0) {
        $form = false;
        $error = true;
        $error_message = 'This Promotion Item do not exist;'; 
    } else {
        $edit_row =  $edit_res->fetch_assoc();

        $discount_name = htmlspecialchars($edit_row['name']);
        $amount = isset($edit_row['amount']) ? (int)$edit_row['amount'] : null;
        $percentage = isset($edit_row['percentage']) ? (int)$edit_row['percentage'] : null;
        $status = (int)($edit_row['status']);
        $db_start_date = htmlspecialchars($edit_row['start_date']);
        $db_end_date = htmlspecialchars($edit_row['end_date']);
        $start_date = convertDateFormatDMY($db_start_date);
        $end_date = convertDateFormatDMY($db_end_date );
        $description = htmlspecialchars($edit_row['description']);
        $item = [];

        $pro_item_sql = "SELECT item_id FROM `discount_item` where discount_promotion_id = '$id'";
        $pro_item_res = $mysqli->query($pro_item_sql);
        while ($pro_item_row = $pro_item_res->fetch_assoc()) {
          array_push($item, $pro_item_row['item_id']);
          $pro_item_id = $pro_item_row['item_id'];
        } 


        if ($amount === null && $percentage !== null) {
            $discount_type = 'Percentage';
            $discount_amount = $percentage;
        } elseif ($amount !== null && $percentage === null) {
            $discount_type = 'Cash';
            $discount_amount = $amount;
        }

    }


   
}
?>


<?php 
    $title = "Adminpanel::Discount Edit";
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
                        <form action="<?= $cp_base_url ?>discount_edit.php" method="POST" enctype="multipart/form-data"
                            novalidate>
                            <span class="section">Category Edit</span>


                            <div class="field item form-group">
                                <label for="name" class="col-form-label col-md-3 col-sm-3  label-align">Discount
                                    Name<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input id="name" class="form-control" type="text" class='number' name="name"
                                        value="<?= $discount_name ?>">
                                </div>
                            </div>

                            <div class="field item form-group">
                                <label for="name" class="col-form-label col-md-3 col-sm-3 label-align">Discount Type
                                    <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 mt-2">
                                    <div class="radio" style="margin-top:9px">
                                        <label>
                                            <input type="radio" id="optionsRadios1" name="discount_type"
                                                value="Percentage"
                                                <?php if( $discount_type == "Percentage"){ echo "checked";} ?>>
                                            Percentage
                                        </label>&nbsp;&nbsp;&nbsp;&nbsp;
                                        <label>
                                            <input type="radio" id="optionsRadios2" name="discount_type" value="Cash"
                                                <?php if( $discount_type == "Cash"){ echo "checked";} ?>>
                                            Cash
                                        </label>
                                    </div>
                                </div>
                            </div>


                            <div class="field item form-group">
                                <label for="percentageAmount" class="col-form-label col-md-3 col-sm-3 label-align">
                                    <span
                                        class="amount-text"><?php if($discount_type == "Cash") {echo 'Discount Cash Amount';}else {echo 'Discount Percentage Amount';} ?></span><span
                                        class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6">
                                    <input id="percentageAmount" class="form-control" type="number" class='number'
                                        name="amount" value="<?= $discount_amount ?>">
                                </div>
                            </div>


                            <div class="field item form-group">
                                <label for="startDate" class="col-form-label col-md-3 col-sm-3  label-align">Start
                                    Date<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input type="text" id="startDate" name="startDate" value="<?= $start_date ?>"
                                        class="form-control has-feedback-left" aria-describedby="inputSuccess2Status2">
                                    <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                                    <span id="inputSuccess2Status2" class="sr-only">(success)</span>
                                </div>
                            </div>

                            <div class="field item form-group">
                                <label for="endDate" class="col-form-label col-md-3 col-sm-3  label-align">End Date<span
                                        class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input type="text" id="endDate" name="endDate" value="<?= $end_date ?>"
                                        class="form-control has-feedback-left" aria-describedby="inputSuccess2Status1">
                                    <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                                    <span id="inputSuccess2Status1" class="sr-only">(success)</span>
                                </div>
                            </div>


                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align">Item<span
                                        class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <?php 
                                            $sql = "SELECT id,name FROM `item` where deleted_at is null";
                                            $item_result = $mysqli->query($sql);
                                    while ($item_row = $item_result->fetch_assoc()) {
                                            $row_item_id = $item_row['id'];
                                            $row_item_name = $item_row['name'];
                                            ?>
                                    <div class="col-md-4">
                                        <label>
                                            <input type="checkbox" class="flat" name="item[]"
                                                value="<?= $row_item_id ?>"
                                                <?php  if(in_array( $row_item_id, $item )){ echo "checked";} ?>>
                                            <?= $row_item_name ?>
                                        </label>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <!-- <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align">Status<span
                                        class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <select class="select2_group form-control" name="status">
                                        <option value="0" <?php if($status == 0) {echo 'selected';} ?>>Enable</option>
                                        <option value="1" <?php if($status != 0) {echo 'selected';} ?>>Disable</option>
                                    </select>
                                </div>
                            </div> -->



                            <div class="field item form-group">
                                <label for="de" class="col-form-label col-md-3 col-sm-3  label-align">Description<span
                                        class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <textarea name="description" value="" id="de"
                                        class="form-control"><?= $description ?></textarea>
                                </div>
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
                        <a href="<?= $cp_base_url ?>discount_list.php" class="btn btn-danger btn-xs"><i
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


<!-- <script>
$(document).ready(function() {
    $("input[name='discount_type']").on('change', function() {
        const discount_type = $('input[name="discount_type"]:checked').val();
        if (discount_type == 'Cash') {
            $(".amount-text").text('Discount Cash Amount*')
        } else {
            $(".amount-text").text('Discount Percentage Amount*')
        }
    })
})
</script> -->

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