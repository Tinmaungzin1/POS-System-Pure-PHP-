<?php 
session_start();
require("../common/config.php");
require("../common/database.php");
require("../Auth/check_auth.php");
require("../include/include_function.php");

// if (isset($_SESSION['success_message'])) {
//     $success = true;   
//     unset($_SESSION['success_message']);

// }
$error = false;
$success = false;

$sql = "SELECT
t1.id,
t1.name AS promotion_name,
CASE 
    WHEN t1.amount IS NULL THEN CONCAT(t1.percentage, '%') 
    ELSE CONCAT(t1.amount, ' Kyats') 
END AS Calculated_value,
t1.start_date,
t1.end_date,
t1.status,
t1.description,
GROUP_CONCAT(t2.item_id) AS item_ids,
GROUP_CONCAT(t3.name) AS item_names
FROM
`discount_promotion` AS t1
LEFT JOIN
`discount_item` AS t2 ON t1.id = t2.discount_promotion_id
LEFT JOIN
`item` AS t3 ON t2.item_id = t3.id 
WHERE t1.deleted_at IS NULL
GROUP BY
t1.id, t1.name, t1.amount, t1.percentage, t1.start_date, t1.end_date, t1.status, t1.description;
";





$result = $mysqli->query($sql);

// $result = $mysqli->query($sql);

if(isset($_GET['msg'])){
    switch ($_GET['msg']) {
        case "create":
          $success = true;
          $success_message = "Discount Create success!";
          break;
        case "update":
            $success = true;
            $success_message = "Discount Update success!";
          break;
        case "delete":
            $success = true;
            $success_message = "Discount delete success!";
          break;
      }
}
if(isset($_GET['err'])){
    switch ($_GET['err']) {
        case "edit":
            $error = true;
            $error_message = "Shift is open, Discount cannot Edit";
          break;
        case "create":
            $error = true;
            $error_message = "Discount Create Fail!";
          break;
        case "update":
            $error = true;
            $error_message = "Discount Update Fail!";
        break;
        case "delete":
            $error = true;
            $error_message = "Discount Delete Fail!";
          break;
      }
}
?>

<?php 
  $title = "Adminpanel::Discount List";
  require("../templates/cp_template_header.php");
  require("../templates/cp_template_sidebar.php");
  require("../templates/cp_template_top_nav.php");

?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Discount Table</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row" style="display: block;">
            <div class="col-md-12 col-sm-12  ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Discount List</h2>
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
                                        <th class="column-title">Item Name </th>
                                        <th class="column-title">Discount Name </th>
                                        <th class="column-title">Discount Amount </th>
                                        <th class="column-title">Start Date </th>
                                        <th class="column-title">End Date </th>
                                        <!-- <th class="column-title">description </th> -->
                                        <th class="column-title">Status </th>
                                        <th class="column-title no-link last"><span class="nobr">Action</span>
                                        </th>
                                        <th class="bulk-actions" colspan="7">
                                            <a class="antoo" style="color:#fff; font-weight:500;">Bulk Actions ( <span
                                                    class="action-cnt"> </span> ) <i class="fa fa-chevron-down"></i></a>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php
                                    while ($row = $result->fetch_assoc()) {
                                        $id = (int)($row['id']);
                                        $item_name = htmlspecialchars($row['item_names']);
                                        $name = htmlspecialchars($row['promotion_name']);
                                        $discount_amount = htmlspecialchars($row['Calculated_value']);
                                        $start_date = htmlspecialchars($row['start_date']);
                                        $end_date = htmlspecialchars($row['end_date']);
                                        $start_format_date = convertDataFormat($start_date);
                                        $end_format_date = convertDataFormat($end_date);
                                        $status =  (int)($row['status']);
                                        // $description = htmlspecialchars($row['description']);
                                        $edit_path = $cp_base_url . "discount_edit.php?id=" . $id; 
                                        $delete_path = $cp_base_url . "discount_delete.php?id=" . $id;
                                            ?>

                                    <tr class="even pointer">
                                        <td class="a-center ">
                                            <input type="checkbox" class="flat" name="table_records">
                                        </td>

                                        <td class=" "><?= $item_name ?></td>
                                        <td class=" "><?= $name ?></td>
                                        <td class=" "><?= $discount_amount ?></td>
                                        <td class=" "><?= $start_format_date ?></td>
                                        <td class=" "><?= $end_format_date ?></td>
                                        <!-- <td class=" "><?= $description ?></td>  -->
                                        <td class=" ">
                                            <?php
                                                if ($status == 0) {
                                                    echo '<span class="badge badge-primary">enable</span>';
                                                } else {
                                                    echo '<span class="badge badge-secondary">disable</span>';
                                                }
                                                ?>
                                        </td>
                                        <td class=" last">
                                            <a href="<?= $edit_path ?>" class="btn btn-info btn-xs"><i
                                                    class="fa fa-pencil"></i> Edit
                                            </a>
                                            <a href="<?= $delete_path ?>" class="btn btn-danger btn-xs"
                                                onclick="confirmDelete(event)">
                                                <i class="fa fa-trash-o"></i>
                                                Delete
                                            </a>

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