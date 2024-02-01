<?php 
session_start();
require("../common/config.php");
require("../common/database.php");
require("../Auth/check_auth.php");

// if (isset($_SESSION['success_message'])) {
//     $success = true;   
//     unset($_SESSION['success_message']);

// }
$error = false;
$success = false;
$start_date = $_GET['start'];


$order_check = "SELECT id FROM `shift` WHERE start_date_time = '$start_date' AND end_date_time IS NULL";
    $order_res = $mysqli->query($order_check);
    $order_row = $order_res->fetch_assoc();
    $shift_start_id = htmlspecialchars($order_row['id']);

$order_sql = "SELECT id, shift_id, created_at, status FROM `orders` WHERE shift_id = '$shift_start_id' AND  deleted_at IS NULL";
    $order_result = $mysqli->query($order_sql);

?>

<?php 
  $title = "Adminpanel::Order List";
  require("../templates/cp_template_header.php");
  require("../templates/cp_template_sidebar.php");
  require("../templates/cp_template_top_nav.php");

?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Order Table</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row" style="display: block;">
            <div class="col-md-12 col-sm-12  ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Order List</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="table-responsive">
                            <table class="table table-striped jambo_table bulk_action">
                                <thead>
                                    <tr class="headings">
                                        <th>
                                            <input type="checkbox" id="check-all" class="flat">
                                        </th>
                                        <th class="column-title">Order Id </th>
                                        <th class="column-title">Created At </th>
                                        <th class="column-title">Status </th>
                                        <th class="bulk-actions" colspan="7">
                                            <a class="antoo" style="color:#fff; font-weight:500;">Bulk Actions ( <span
                                                    class="action-cnt"> </span> ) <i class="fa fa-chevron-down"></i></a>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php
                                    while ($order_row = $order_result->fetch_assoc()) {
                                            $id = (int)($order_row['id']);
                                            $status = (int)($order_row['status']);
                                            $created_at = htmlspecialchars($order_row['created_at']);
                                            $order_date = new DateTime($created_at);
                                            $formattedTime = $order_date->format('H:i:s');
                                            $edit_path = $cp_base_url . "item_edit.php?id=" . $id; 
                                             ?>

                                    <tr class="even pointer">
                                        <td class="a-center ">
                                            <input type="checkbox" class="flat" name="table_records">
                                        </td>
                                        <td class=""><?= $id ?></td>
                                        <td class=" "><?= $formattedTime ?></td>
                                        <td class=" ">
                                            <?php
                                                switch ($status) {
                                                    case "0":
                                                        echo '<span class="badge badge-primary">unpaid</span>';
                                                      break;
                                                    case "1":
                                                        echo '<span class="badge badge-secondary">paid</span>';
                                                      break;
                                                    case "2":
                                                        echo '<span class="badge badge-danger">cancle</span>';
                                                      break;
                                                  }
                                                ?>
                                        </td>
                                    </tr>
                                    <?php } ?>

                                </tbody>
                            </table>
                        </div>


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
<?php if($success == true) { ?>
<script>
new PNotify({
    title: ' Success',
    text: "<?= $success_message ?>",
    type: 'success',
    styling: 'bootstrap3'
});
</script>
<?php } ?>

<?php if($error == true) { ?>
<script>
new PNotify({
    title: 'Fail!',
    text: "<?= $error_message ?>",
    type: 'error',
    styling: 'bootstrap3'
});
</script>
<?php } ?>

<script>
function confirmDelete(event) {
    event.preventDefault(); // Prevent the default link behavior

    Swal.fire({
        title: 'Are you sure?',
        text: 'You want to Delete!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // If the user clicks "Yes," follow the link
            window.location.href = event.target.href;
        }
    });
}
</script>
<?php
require("../templates/cp_template_html_end.php");
?>