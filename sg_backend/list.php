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

$sql = "SELECT * FROM `setting`";
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
  $title = "Adminpanel::Admin List";
  require("../templates/cp_template_header.php");
  require("../templates/cp_template_sidebar.php");
  require("../templates/cp_template_top_nav.php");

?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Admin Table</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row" style="display: block;">
            <div class="col-md-12 col-sm-12  ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Admin List</h2>
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
                                        <th class="column-title">Company Name </th>
                                        <th class="column-title">Company Phone </th>
                                        <th class="column-title">Company Email </th>
                                        <th class="column-title">Company Address </th>
                                        <th class="column-title">Company Logo </th>

                                    </tr>
                                </thead>

                                <tbody>

                                    <?php
                                    while ($row = $result->fetch_assoc()) {
                                            $id = (int)($row['id']);
                                            $company_name = htmlspecialchars($row['company_name']);
                                            $company_phone = htmlspecialchars($row['company_phone']);
                                            $company_email = htmlspecialchars($row['company_email']);
                                            $company_address = htmlspecialchars($row['company_address']);
                                            $company_logo = ($row['company_logo']);
                                            $full_path_image = $base_url . "asset/image/company-logo/" . $id . "/" . $company_logo;
                                             ?>

                                    <tr class="even pointer">
                                        <td class="a-center ">
                                            <input type="checkbox" class="flat" name="table_records">
                                        </td>
                                        <td class=" "><?= $company_name ?></td>
                                        <td class=" "><?= $company_phone ?></td>
                                        <td class=" "><?= $company_email ?></td>
                                        <td class=" "><?= $company_address ?></td>
                                        <td class=" ">
                                            <div>
                                                <img src="<?= $full_path_image ?>"
                                                    style="width: 100px; height: auto; object-fit: cover;">

                                            </div>
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