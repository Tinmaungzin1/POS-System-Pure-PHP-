    <?php 
    session_start();
    require("common/config.php");
    require("common/database.php");
    require("Auth/check_cashier_auth.php");
    require("Auth/shift_auth.php");
    $ctitle = "Order List";
    require("templates/template_header.php");
    ?>
    <style>
table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

th,
td {
    padding: 8px;
    text-align: left;
    border: 1px solid #86bc25;
    /* Set collapsed border for every td */
}

th {
    background-color: #4CAF50;
    color: white;
}

tr:nth-child(even) {
    background-color: rgba(0, 0, 0, 0.12);
}

.canceled-order {

    background-color: #ffdddd;
    /* Set the background color for canceled orders */
    text-decoration: line-through;
    /* Add a line through the text for canceled orders */

}
    </style>
    <div class="header-sec">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-4 heightLine_01 head-lbox">
                    <div>
                        <a class="btn btn-large dash-btn"
                            href="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dashboard</a>
                    </div>
                </div>
                <div class="col-md-2 col-2 heightLine_01">
                    <img src="<?= $base_url ?>asset/images/resturant_logo.png" alt="ROS logo" class="ros-logo">
                </div>

                <div class="col-md-3 col-3 heightLine_01 head-rbox">
                    <div>
                        <span class="staff-name">
                            <?=  $_SESSION['cusername'] ?>
                        </span>
                        <div class="dropdown show pull-right">
                            <button role="button" id="dropdownMenuLink" class="btn btn-primary user-btn"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="<?= $base_url ?>asset/images/login_img.png" alt="login image">
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item" href="<?= $base_url ?>logout.php">Logout</a>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div><!-- header-sec -->

    <!-- here -->
    <div class="wrapper">
        <div class="container">
            <div class="row cmn-ttl cmn-ttl1 ">
                <div class="container">
                    <h3>Make Order</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <a href="<?= $base_url ?>order">
                        <img id="img" class="bottom image" src="<?= $base_url ?>asset/images/dashboard/make-order.png">
                    </a>
                </div>

                <div class="col-md-6">
                    <a href="<?= $base_url ?>order_list">
                        <img id="img" class="bottom image" src="<?= $base_url ?>asset/images/dashboard/order-list.png">
                    </a>
                </div>
            </div><!-- End Row -->

        </div><!-- container-fluid -->
        <div class="footer text-center">
            <img src="images/softguide_logo.png" />
        </div><!-- footer -->
    </div><!-- wrapper -->
    <!-- here -->


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script src="<?= $base_url ?>asset/js/page/order_list.js"></script>
    <?php 
    require("templates/template_footer.php");
    ?>