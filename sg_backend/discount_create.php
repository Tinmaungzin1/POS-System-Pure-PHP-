<?php 
session_start();
require("../common/config.php");
require("../common/database.php");
require("../Auth/check_auth.php");
require("../include/include_function.php");

$discount_name = "";
$discount_type = "Percentage";
$discount_amount ="";
$item = [];
$start_date="";
$end_date ="";
$description ="";
$error = false;
$error_message = '';
$item_names = array();


// Get current timestamp
$currentTimestamp = time();

$item_sql = "SELECT id, name, category_id
FROM `item`
WHERE deleted_at IS NULL AND status = '$admin_enable_status'
ORDER BY category_id ASC, id ASC";

$item_result = $mysqli->query($item_sql);

if(isset($_POST['form-sub']) && $_POST['form-sub'] == 1) {
    $process_error = false;
    $discount_name = $mysqli->real_escape_string($_POST['name']);
    $discount_type   = $mysqli->real_escape_string($_POST['discount_type']);
    $discount_amount   = $mysqli->real_escape_string($_POST['amount']);
    $start_date = $mysqli->real_escape_string($_POST['startDate']);
    $end_date = $mysqli->real_escape_string($_POST['endDate']);
    $item = (isset($_POST['item'])) ? $_POST['item'] : [];
    $description = $mysqli->real_escape_string($_POST['description']);

    $start_date_ymt = convertDateFormatYMD($start_date);
    $end_date_ymt = convertDateFormatYMD($end_date);

 
    // Convert date strings to timestamps
    $startTimestamp = strtotime($start_date);
    $endTimestamp = strtotime($end_date);
 
    
    // Validate end date greater than start date
    if ($endTimestamp <= $startTimestamp) {
        $process_error = true; 
        $error = true;
        $error_message ="End date must be greater than the start date.";
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
    if ($description == '') {   /// user input 
        $process_error = true; 
        $error = true;
        $error_message = 'Please Fill Description';
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
            $sql = "SELECT name, price FROM `item` WHERE id = '$product' AND deleted_at IS NULL";
            $result = $mysqli->query($sql);
        
            if ($result && $item_row = $result->fetch_assoc()) {
                $row_name = $item_row['name'];
                $row_price = $item_row['price'];
        
                if ($discount_amount >= $row_price) {
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
                             

    if($process_error == false) { 
        $existingDiscount = false;
        $check_item_error = '';
        foreach ($item as $product) {
            $check_dis_sql = "SELECT name FROM `item` WHERE id = '$product' AND deleted_at IS NULL";
            $check_dis_result = $mysqli->query($check_dis_sql);
        
            if ($check_dis_result && $item_check_row = $check_dis_result->fetch_assoc()) {
                $check_row_name = $item_check_row['name'];
                 }

            // Check for existing discounts with start date
            $discount_exist_start_date_sql = "SELECT COUNT(T01.id) AS total FROM discount_item T01 
                                            LEFT JOIN discount_promotion T02 ON T01.discount_promotion_id = T02.id
                                            WHERE T01.item_id = '$product' AND '$start_date_ymt' >= T02.start_date
                                            AND '$start_date_ymt' <= T02.end_date";
            $dis_exist_start_date_result = $mysqli->query($discount_exist_start_date_sql);
            $dis_exist_start_date_row = $dis_exist_start_date_result->fetch_assoc();
            $dis_exist_start_date_total = $dis_exist_start_date_row['total'];

            // Check for existing discounts with end date
            $discount_exist_end_date_sql = "SELECT COUNT(T01.id) AS total FROM discount_item T01 
                                            LEFT JOIN discount_promotion T02 ON T01.discount_promotion_id = T02.id
                                            WHERE T01.item_id = '$product'
                                            AND '$end_date_ymt' >= T02.start_date
                                            AND '$end_date_ymt' <= T02.end_date";
            $dis_exist_end_date_result = $mysqli->query($discount_exist_end_date_sql);
            $dis_exist_end_date_row = $dis_exist_end_date_result->fetch_assoc();
            $dis_exist_end_date_total = $dis_exist_end_date_row['total'];
            if ($dis_exist_end_date_total > 0 || $dis_exist_start_date_total > 0) {
                $existingDiscount = true;
                $check_item_error .= $check_row_name . ',';
                  // Exit the loop early since there's already a discount
            }
        } 
        if ($existingDiscount) {
            $error = true;
            $error_message = 'This Item(' . rtrim($check_item_error, ', ') . ') Already has a discount within the specified date range.';
        } else {
            $check = "SELECT count(id) AS total FROM `discount_promotion` WHERE name = '$discount_name' and deleted_at IS NULL";
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
            // $start_date_ymt = convertDateFormatYMD($start_date);
            // $end_date_ymt = convertDateFormatYMD($end_date);

            // data insert into category table ....
            if($discount_type == "Percentage") {
                if ($discount_amount >= 100) {  
                    $error = true;
                    $error_message = 'Percentage is less than 100%.';
                }
                

                $sql = "INSERT INTO `discount_promotion` (name, percentage, start_date, end_date, description, created_at, created_by, updated_at, updated_by) VALUES ('$discount_name', '$discount_amount','$start_date_ymt', '$end_date_ymt', '$description', '$today_dt', '$user_id', '$today_dt', '$user_id')";
                
            } 
            if($discount_type == "Cash"){
                if ($discount_amount >=  $row_price ) {   /// user input 
                    $process_error = true; 
                    $error = true;
                    $error_message = 'Amount is less than Item Price';
                }
                $sql       = "INSERT INTO `discount_promotion` (name, amount, start_date, end_date, description, created_at, created_by, updated_at, updated_by) VALUES ('$discount_name', $discount_amount,'$start_date_ymt', '$end_date_ymt', '$description', '$today_dt', '$user_id', '$today_dt', '$user_id')";
            }
            $result = $mysqli->query($sql);


            // data insert into category table ....


            $last_inserted_id = $mysqli->insert_id; // find last insert id 

            
            if(!$result) {   /// data insert result error
                $error = true;
                $error_message = 'Oop! Something wrong.Please contact Administractor.';
            }else{
                foreach($item as $ite) {
                    $dis_item_sql  = "INSERT INTO `discount_item` (item_id, discount_promotion_id, created_at, created_by, updated_at, updated_by) VALUES ('$ite', '$last_inserted_id', '$today_dt', '$user_id', '$today_dt', '$user_id')";
                    $dis_item_result = $mysqli->query($dis_item_sql);
                }
                $url = $cp_base_url."discount_list.php?msg=create";
                header("Refresh: 0; url = $url");
                exit();
            }
                $url = $cp_base_url."discount_list.php?err=create";
                header("Refresh: 0; url = $url");
                exit();    
            }
        }  
    } 
}
?>


<?php 
    $title = "Adminpanel::Discount Create";
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
                        <form action="<?= $cp_base_url ?>discount_create.php" method="POST"
                            enctype="multipart/form-data" novalidate>
                            <span class="section">Item Create</span>

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
                                    <div class="radio" styl="margin-top:9px">
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
                                    <span class="amount-text">Discount Percentage Amount</span><span
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
                                    <?php while ($item_row = $item_result->fetch_assoc()) {
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





                            <div class="field item form-group">
                                <label for="de" class="col-form-label col-md-3 col-sm-3  label-align">Description<span
                                        class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <textarea name="description" id="de"
                                        class="form-control"><?= $description ?></textarea>
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


<?php 
require("../templates/cp_template_footer_end.php");
?>
<script>
// $(document).ready(function() {
//     // Initialize start date picker
//     $('#startDate').datetimepicker({
//         format: 'YYYY-MM-DD',
//         minDate: moment(), // Set minimum date to today
//     });

//     // Initialize end date picker
//     $('#endDate').datetimepicker({
//         format: 'YYYY-MM-DD',
//         useCurrent: false, // Important for preventing automatic date selection
//     });

//     // Set the start date picker's change event
//     $('#startDate').on('change.datetimepicker', function(e) {
//         // Update the minimum date of the end date picker
//         $('#endDate').datetimepicker('minDate', e.date);

//         if ($('#endDate').data('DateTimePicker').date() > 'minDate') {
//             $('#endDate').datetimepicker('date', e.date);
//         }
//     });

//     // Set the end date picker's change event
//     $('#endDate').on('change.datetimepicker', function(e) {
//         // Optional: You can add additional validation logic here
//         if ($('#endDate').data('DateTimePicker').date() < e.date) {
//             $('#endDate').datetimepicker('date', e.date);
//         }
//     });
// });
</script>

<script>
$(document).ready(function() {
    $("input[name='discount_type']").on('change', function() {
        const discount_type = $('input[name="discount_type"]:checked').val();
        if (discount_type == 'Cash') {
            $(".amount-text").text('Discount Cash Amount')
        } else {
            $(".amount-text").text('Discount Percentage Amount')
        }
    })
})
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