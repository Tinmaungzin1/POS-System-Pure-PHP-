<?php 
    session_start();
    require("common/config.php");
    require("common/database.php");
    require("Auth/check_cashier_auth.php");
    require("Auth/shift_auth.php");
    $ctitle = "Order List";
    $id = (int) ($_GET['id']);
    $id = $mysqli->real_escape_string($id);
    $source = htmlspecialchars($_GET['source']);
    require("templates/template_header.php");
    ?>


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
<div class="container" ng-app="myApp" ng-controller="myCtrl" ng-init="init(<?= $id ?>)">
    <div class="row">
        <div class="col-md-11">
            <div>
                <span style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="h3" style="margin: 0;"><strong>View Order Details</strong></h3>
                    <?php if($source == 'payment') { ?>
                    <a href="<?= $base_url ?>payment/<?= $id ?>" class="btn btn-success pull-left" style="margin: 0;"><i
                            class="fa fa-mail-reply-all fa-2x"></i></a>
                    <?php } else { ?>
                    <a href="<?= $base_url ?>order_list" class="btn btn-success pull-left" style="margin: 0;"><i
                            class="fa fa-mail-reply-all fa-2x"></i></a>
                    <?php } ?>
                </span>
            </div>

            <div id="order_detail"
                style="width:350px;max-width: 350px;padding: 25px; margin: 50px auto 0;box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);">

                <div class="receipt_header"
                    style="padding-bottom: 20px;border-bottom: 1px dashed #000;text-align: center;"
                    ng-repeat="setting in order.setting">
                    <h1 style="font-size: 20px;margin-bottom: 5px;">Receipt of Sale
                        <span style="display: block;font-size: 25px;">{{setting.company_name}}</span>
                    </h1>
                    <h2 style="font-size: 14px; color: #727070; font-weight: 300;">Address: {{setting.company_address}}
                        <span style="display: block;">Tel:
                            {{setting.company_phone}}</span>
                    </h2>
                    <h2 style="font-size: 14px; color: #727070; font-weight: 300;">OrderNO: {{ order.order_no }}</h2>
                </div>

                <div class="receipt_body" style="margin-top: 20px;">

                    <div class="date_time_con" style="display: flex;justify-content: center;column-gap: 25px;">
                        <div class="date">{{ order.date}}</div>
                        <div class="time">{{ order.time}}</div>
                    </div>

                    <div class="items" style="margin-top: 25px;">
                        <table style="width: 100%">

                            <thead style="position: relative; text-align: left; border-bottom: 1px dashed #000;">
                                <tr>
                                    <th style="text-align: left;">QTY</th>
                                    <th style="text-align: left;">ITEM</th>
                                    <th style="text-align: right; position: relative;">
                                        PRICE</th>
                                </tr>
                            </thead>

                            <tbody ng-repeat="data in allOrder" style="text-align: left;">
                                <tr>
                                    <td style="padding-top: 15px;">{{data.quantity}}</td>
                                    <td style="padding-top: 15px;">{{data.name}}</td>
                                    <td style="padding-top: 15px; text-align: right;">{{data.amount}}</td>
                                </tr>
                            </tbody>

                            <tfoot style="position: relative; border-top: 1px dashed #000;">
                                <tr>
                                    <td style="padding-top: 15px; font-weight: bold; font-size: 20px;">Total</td>
                                    <td style="padding-top: 15px;"></td>
                                    <td style="padding-top: 15px; font-weight: bold; font-size: 20px;">
                                        {{order.total_amount}}
                                    </td>
                                </tr>
                            </tfoot>


                        </table>
                    </div>

                </div>


                <h5 style="border-top: 1px dashed #000;padding-top: 10px;  
                            margin-top: 25px;
                            text-align: center;
                            text-transform: uppercase;">Thank You!</h5>

                <h2 style="font-size: 14px; color: #727070; font-weight: 300;" ng-repeat="setting in order.setting">
                    Email: <a href="mailto:{{setting.company_email}}" style="color:black">{{setting.company_email}}</a>
                </h2>
            </div>
            <div style="text-align: center;margin-top: 25px;">
                <button id="printButton" class="btn btn-primary" onclick="printInvoice()"><i
                        class="fa fa-print fa-3x"></i></button>
                <!-- <button class="btn print-modal" id="printInvoice" onclick="printInvoice()">
                    <img src="<?= $base_url ?>asset/images/payment/print_img.png" alt="Print Image"
                        class="heightLine_06">
                </button> -->
            </div>

        </div>
    </div>

    <div class="footer text-center">
        <img src="<?= $base_url ?>asset/images/softguide_logo.png">
    </div><!-- footer -->
</div><!-- wrapper -->

<script src="<?= $base_url ?>asset/js/page/view_order.js"></script>
<script>
function printInvoice() {
    var printContent = document.getElementById("order_detail"); // ID of the container you want to print
    var originalContent = document.body.innerHTML;

    document.body.innerHTML = printContent.innerHTML;

    window.print();

    document.body.innerHTML = originalContent;
}
</script>

<?php 
    require("templates/template_footer.php");
    ?>