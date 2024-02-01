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
if(isset($_GET['msg'])){
    switch ($_GET['msg']) {
        case "start":
          $success = true;
          $success_message = "Shift Start";
          break;
        case "end":
            $success = true;
            $success_message = "Shift End";
          break;
      }
}
if(isset($_GET['err'])){
    switch ($_GET['err']) {
        case "start":
          $error = true;
          $error_message = "Shift Already Start";
          break;
          case "orderstart":
            $error = true;
            $error_message = "Order exist,Shift cannot Close";
            break;
        case "end":
            $error = true;
            $error_message = "Shift Already End";
          break;
      }
}

$shift_open = false;
$shift_sql = "SELECT id, start_date_time, end_date_time, status From `shift` where deleted_at is null order by id DESC";
$shift_result = $mysqli->query($shift_sql);
$shift_res_row = $shift_result->num_rows;

$shift_check = "SELECT count(id) AS total FROM `shift` WHERE start_date_time IS NOT NULL and end_date_time is null and deleted_at IS NULL";
$shift_res = $mysqli->query($shift_check);
while($shift_row = $shift_res->fetch_assoc())
{
    $shift_total = $shift_row['total'];
    if($shift_total > 0) {
        $shift_open = true;
    }
}
?>

<?php 
  $title = "Adminpanel::Shift";
  require("../templates/cp_template_header.php");
  require("../templates/cp_template_sidebar.php");
  require("../templates/cp_template_top_nav.php");

?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Control Shift</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row" style="display: block;">
            <div class="col-md-12 col-sm-12  mb-2">
                <a href="<?= $cp_base_url ?>shift_start.php" class="btn btn-success btn-lg" id="shiftOpenBtn"
                    style="display:<?php if($shift_open){echo "none";} else {echo "inline";} ?>">
                    <span class="glyphicon glyphicon-open"></span> Shift Start
                </a>
                <a href="<?= $cp_base_url ?>shift_end.php" class="btn btn-danger btn-lg" id="shiftEndBtn"
                    style="display:<?php if(!$shift_open){echo "none";} else {echo "inline";} ?>">
                    <span class="glyphicon glyphicon-download-alt"></span> Shift Close
                </a>

            </div>
            <div class="col-md-12 col-sm-12  ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Shift Table</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="table-responsive">
                            <table class="table table-striped jambo_table bulk_action">
                                <thead>
                                    <tr class="headings">
                                        <th class="column-title">shift Start time </th>
                                        <th class="column-title">Shift End Time </th>
                                        <th class="column-title">Action </th>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                if ($shift_res_row > 0) { 
                                    while ($shift_row = $shift_result->fetch_assoc()) { 
                                        $id = (int)($shift_row['id']);
                                        $start_date_time = $shift_row['start_date_time'];
                                        $end_date_time = $shift_row['end_date_time'];
                                        $status =  (int)($shift_row['status']);
                                        $order_view = $cp_base_url . "order_view.php?start=" . $start_date_time . "&end=" . $end_date_time;
                                        ?>
                                    <tr class="even pointer">
                                        <td class=" "><?= $start_date_time ?></td>
                                        <td class=" "><?= $end_date_time ?></td>
                                        <td>
                                            <a href="<?= $order_view ?>" class="btn btn-info btn-xs"><i
                                                    class="fa fa-eye"></i> View Order
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                }
                                ?>


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
$(document).ready(function() {
    $("#shiftOpenBtn").click(function(e) {
        e.preventDefault(); // Prevent the default link behavior

        // Display a SweetAlert confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to shift start?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, shift start!',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                // User clicked OK, redirect to shift_start.php
                window.location.href = '<?= $cp_base_url ?>shift_start.php';
            } else {
                // User clicked Cancel, do nothing or provide feedback
                Swal.fire('Shift start canceled', '', 'info');
            }
        });
    });
});

$(document).ready(function() {
    $("#shiftEndBtn").click(function(e) {
        e.preventDefault(); // Prevent the default link behavior

        // Display a SweetAlert confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to shift end?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, shift end!',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                // User clicked OK, redirect to shift_end.php
                window.location.href = '<?= $cp_base_url ?>shift_end.php';
            } else {
                // User clicked Cancel, do nothing or provide feedback
                Swal.fire('Shift end canceled', '', 'info');
            }
        });
    });
});
</script>

<?php
require("../templates/cp_template_html_end.php");
?>