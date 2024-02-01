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

// IFNULL(c2.name, 'None') as parent_name
// COLAESCE(c2.name, 'None') as parent_name
$sql = "
    SELECT 
        c1.id,
        c1.name,
        c1.parent_id,
        c1.image,
        c1.status,
        IFNULL(c2.name, 'None') as parent_name
    FROM 
        `category` c1
    LEFT JOIN 
        `category` c2 ON c1.parent_id = c2.id
    WHERE 
        c1.deleted_at IS NULL";
$result = $mysqli->query($sql);

if(isset($_GET['msg'])){
    switch ($_GET['msg']) {
        case "create":
          $success = true;
          $success_message = "Category Create success!";
          break;
        case "update":
            $success = true;
            $success_message = "Category Update success!";
          break;
        case "delete":
            $success = true;
            $success_message = "Category delete success!";
          break;
      }
}
if(isset($_GET['err'])){
    switch ($_GET['err']) {
        case "create":
            $error = true;
            $error_message = "Category Create Fail!";
          break;
          case "edit":
            $error = true;
            $error_message = "Shift is open, Category cannot Edit";
          break;
        case "update":
            $error = true;
            $error_message = "Category Update Fail!";
        break;
        case "delete":
            $error = true;
            $error_message = "Category Delete Fail!";
          break;
      }
}
?>

<?php 
  $title = "Adminpanel::Category List";
  require("../templates/cp_template_header.php");
  require("../templates/cp_template_sidebar.php");
  require("../templates/cp_template_top_nav.php");

?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Category Table</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row" style="display: block;">
            <div class="col-md-12 col-sm-12  ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Category List</h2>
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
                                        <th class="column-title">Name </th>
                                        <th class="column-title">Pareant Category </th>
                                        <th class="column-title">Status </th>
                                        <th class="column-title">Image </th>
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
                                            $name = htmlspecialchars($row['name']);
                                            $parentName = htmlspecialchars($row['parent_name']);
                                            $status =  (int)($row['status']);
                                            $image = htmlspecialchars($row['image']);
                                            $full_path_image = $base_url . "asset/upload/" . $id . "/" . $image;
                                            $edit_path = $cp_base_url . "category_edit.php?id=" . $id; 
                                            $delete_path = $cp_base_url . "category_delete.php?id=" . $id;
                                             ?>

                                    <tr class="even pointer">
                                        <td class="a-center ">
                                            <input type="checkbox" class="flat" name="table_records">
                                        </td>
                                        <td class=" "><?= $name ?></td>
                                        <td class=" "><?= $parentName ?></td> <!-- Display parent category name -->
                                        <td class=" ">
                                            <?php
                                                if ($status == 0) {
                                                    echo '<span class="badge badge-primary">enable</span>';
                                                } else {
                                                    echo '<span class="badge badge-secondary">disable</span>';
                                                }
                                                ?>
                                        </td>
                                        <td class=" ">
                                            <!-- <div style="width: 100px; height: auto;"> -->
                                            <img src="<?= $full_path_image ?>" style="width: 100px; height: auto;"
                                                alt="Image">
                                            <!-- </div> -->

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