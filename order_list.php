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
                            href="<?= $base_url ?>index">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dashboard</a>
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
    <div class="container" ng-app="myApp" ng-controller="myCtrl" ng-init="init()">
        <div class="row">
            <div class="col-md-11">
                <span style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="h3" style="margin: 0;"><strong>Order Listing</strong></h3>
                    <a href="<?= $base_url ?>order" class="btn btn-success pull-left" style="margin: 0;"><i
                            class="fa fa-mail-reply-all fa-2x"></i></a>
                </span>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-11">
                    <table id="invoice">
                        <thead>
                            <tr>
                                <th>Order No</th>
                                <th>Order Time</th>
                                <th>Total Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="data in allOrder" ng-class="{ 'canceled-order': data.status == 2 }">
                                <td>{{data.order_no}}
                                    <span class="badge badge-secondary" ng-if="data.status == 0">unpaid</span>
                                    <span class="badge badge-success" ng-if="data.status == 1">paid</span>
                                    <span class="badge badge-danger" ng-if="data.status == 2">cancel</span>
                                </td>
                                <td>{{data.order_time}}</td>
                                <td>{{ data.total_amount}}</td>
                                <td>
                                    <a href="<?= $base_url ?>view_order/{{data.id}}"
                                        class="btn btn-secondary btn-mid"><i class="fa fa-eye"></i>&nbsp;View
                                        Detail</a>

                                    <a href="<?= $base_url ?>order/edit/{{data.id}}"
                                        ng-if="data.status != 1 && data.status != 2" class="btn btn-primary btn-mid"><i
                                            class="fa fa-edit"></i>&nbsp;Edit</a>


                                    <!-- Condition for data.status == 2 -->
                                    <button ng-if="data.status == 2" class="btn btn-success btn-mid"
                                        ng-click="orderCancel(data.id, 0)">
                                        Active
                                    </button>

                                    <!-- Condition for data.status not being 1 or 2 -->
                                    <a href="<?= $base_url ?>payment/{{data.id}}"
                                        ng-if="data.status != 1 && data.status != 2" class="btn btn-success btn-mid"><i
                                            class="fa fa-money"></i>&nbsp;Click to Pay
                                    </a>

                                    <!-- Condition for data.status not being 1 or 2 -->
                                    <button ng-if="data.status != 1 && data.status != 2" class="btn btn-danger btn-mid"
                                        ng-click="orderCancel(data.id, 2)">
                                        <i class="fa fa-trash"></i>&nbsp;Cancel&nbsp;&nbsp;
                                    </button>


                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>



        <div class="footer text-center">
            <img src="<?= $base_url ?>asset/images/softguide_logo.png">
        </div><!-- footer -->
    </div><!-- wrapper -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script src="<?= $base_url ?>asset/js/page/order_list.js"></script>
    <?php 
    require("templates/template_footer.php");
    ?>