<?php 
session_start();
require("common/config.php");
require("common/database.php");
require("Auth/check_cashier_auth.php");
require("Auth/shift_auth.php");
$ctitle = "Payment";
$id = (int)($_GET['id']);
$id = $mysqli->real_escape_string($id);
require("templates/template_header.php");
?>
<div class="header-sec">
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-4 heightLine_01 head-lbox">
                <div>
                    <a class="btn btn-large dash-btn"
                        href="<?= $base_url ?>index">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dashboard</a>
                </div>
            </div>
            <div class="col-md-2 col-2 heightLine_01">
                <img src="<?= $base_url ?>asset/images/resturant_logo.png" alt="ROS logo" class="ros-logo">
            </div>

            <div class="col-md-3 col-3 heightLine_01 head-rbox">
                <div>
                    <span class="staff-name">
                        <?= $_SESSION['cusername']?>
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

<div class="wrapper" ng-app="myApp" ng-controller="myCtrl" ng-init="init(<?= $id ?>)">
    <div id="order_detail" ng-show="showInvoice"
        style="width:350px;max-width: 350px;padding: 25px; margin: 50px auto 0;box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);">

        <div class="receipt_header" style="padding-bottom: 20px;border-bottom: 1px dashed #000;text-align: center;"
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

    <div class="container-fluid receipt">
        <div class="row cmn-ttl cmn-ttl2">
            <div class="container">
                <div class="row">
                    <input type="hidden" class="void-value" id="" />
                    <input type="hidden" class="void-type" id="" />
                    <div class="col-lg-4 col-md-5 col-sm-6 col-6">
                        <h3>Order no : {{order.order_no}}
                        </h3>
                    </div>
                    <div class="col-lg-8 col-md-7 col-sm-6 col-6 receipt-btn">
                        <button class="btn print-modal" id="printInvoice" ng-click="printInvoice()">
                            <img src="<?= $base_url ?>asset/images/payment/print_img.png" alt="Print Image"
                                class="heightLine_06">
                        </button>


                        <a class="btn" href="<?= $base_url ?>order_list">
                            <img src="<?= $base_url ?>asset/images/payment/previous_img.png" alt="Previous"
                                class="heightLine_06">
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 col-sm-4 col-6">
                        <div class="table-responsive">
                            <table class="table receipt-table">
                                <tr>
                                    <td>Sub Total</td>
                                    <td>{{order.total_amount}}</td>
                                </tr>
                                <tr>
                                    <td class="bg-gray">Item</td>
                                    <td class="bg-gray">Quantity</td>
                                    <td class="bg-gray-price">Price</td>
                                </tr>
                                <tr ng-repeat="data in allOrder">
                                    <td>{{data.name}}</td>
                                    <td style="text:center;">{{data.quantity}}</td>
                                    <td>{{data.amount}}</td>
                                </tr>


                            </table>
                        </div><!-- table-responsive -->

                        <h3 class="receipt-ttl">TOTAL - {{order.total_amount}}</h3>
                        <div class="table-responsive">
                            <table class="table receipt-table" id="invoice-table">
                                <tr class="before-tr" style="height: 32px;">
                                    <td colspan="2" class="bl-data"></td>
                                </tr>
                                <tr class="tender" ng-class="{ 'bg-cash': selectIndex.indexOf(kyat.index) !== -1}"
                                    ng-repeat="kyat in kyats">
                                    <td></td>
                                    <td class="pointer" ng-click="selectCash(kyat.index)">{{kyat.total_cash}} MMK</td>
                                </tr>
                                <tr>
                                    <td>BALANCE</td>
                                    <td class="balance">{{balance}} MMK</td>
                                </tr>
                                <tr>
                                    <td>REFUND</td>
                                    <td class="change">{{refund}} MMK</td>
                                </tr>
                            </table>
                        </div><!-- table-responsive -->
                        <div class="row receipt-btn02">

                            <div class="col-md-6 col-sm-6 col-6">
                                <a class="btn btn-primary view-btn"
                                    href="<?= $base_url ?>view_order/<?= $id ?>/payment">VIEW
                                    DETAILS</a>


                            </div>
                        </div>

                    </div>
                    <div class="col-md-8 col-sm-8 col-6">
                        <div class="row">
                            <div class="col-md-12 list-group" id="myList" role="tablist">
                                <a class="list-group-item list-group-item-action heightLine_05 active"
                                    data-toggle="list" href="#home" role="tab" id="payment-cash">
                                    <span class="receipt-type cash-img"></span><span class="receipt-txt">Cash</span>
                                </a>
                                <a class="list-group-item list-group-item-action heightLine_05" data-toggle="list"
                                    href="#profile" role="tab" id="payment-card">
                                    <span class="receipt-type card-img"></span><span class="receipt-txt">Card</span>
                                </a>
                                <a class="list-group-item list-group-item-action heightLine_05" data-toggle="list"
                                    href="#messages" role="tab" id="payment-voucher">
                                    <span class="receipt-type voucher-img"></span><span
                                        class="receipt-txt">Voucher</span>
                                </a>
                                <a class="list-group-item list-group-item-action heightLine_05" data-toggle="list"
                                    href="#settings" role="tab" id="payment-nocollection">
                                    <span class="receipt-type collection-img"></span><span class="receipt-txt">No
                                        Collection</span>
                                </a>
                                <a class="list-group-item list-group-item-action heightLine_05" data-toggle="list"
                                    href="#settings" role="tab" id="payment-loyalty">
                                    <span class="receipt-type loyality-img"></span><span
                                        class="receipt-txt">Loyalty</span>
                                </a>
                            </div> <!-- list-group -->
                            <div class="col-md-12">
                                <div class="tab-content row">
                                    <div class="tab-pane active" id="home" role="tabpanel">
                                        <button class="btn heightLine_04 cash-payment" id="CASH"><span
                                                class="extra-cash"></span><span>Kyats</span></button>
                                        <button class="btn heightLine_04 cash-payment" id="CASH50"
                                            ng-disabled="disabled" ng-click="payCash(50)"><span class="money">50</span>
                                            <span>Kyats</span></button>
                                        <button class="btn heightLine_04 cash-payment" id="CASH100"
                                            ng-disabled="disabled" ng-click="payCash(100)"><span
                                                class="money">100</span><span>Kyats</span></button>
                                        <button class="btn heightLine_04 cash-payment" id="CASH200"
                                            ng-disabled="disabled" ng-click="payCash(200)"><span
                                                class="money">200</span><span>Kyats</span></button>
                                        <button class="btn heightLine_04 cash-payment" id="CASH500"
                                            ng-disabled="disabled" ng-click="payCash(500)"> <span
                                                class="money">500</span>
                                            <span>Kyats</span></button>
                                        <button class="btn heightLine_04 cash-payment" id="CASH1000"
                                            ng-disabled="disabled" ng-click="payCash(1000)"><span
                                                class="money">1000</span><span>Kyats</span></button>
                                        <button class="btn heightLine_04 cash-payment" id="CASH5000"
                                            ng-disabled="disabled" ng-click="payCash(5000)"><span
                                                class="money">5000</span><span>Kyats</span>
                                        </button>
                                        <button class="btn heightLine_04 cash-payment" id="CASH10000"
                                            ng-disabled="disabled" ng-click="payCash(10000)"> <span
                                                class="money">10000</span><span>Kyats</span></button>
                                    </div>
                                    <div class="tab-pane" id="profile" role="tabpanel">
                                        <button class="btn heightLine_05 mpu-type agd-mpu card-payment"
                                            id="MPU_AGD"><span class="receipt-type cash-img"></span><span
                                                class="receipt-txt">AGD</span></button>
                                        <button class="btn heightLine_05 mpu-type kbz-mpu card-payment"
                                            id="MPU_KBZ"><span class="receipt-type cash-img"></span><span
                                                class="receipt-txt">KBZ</span></button>
                                        <button class="btn heightLine_05 mpu-type uab-mpu card-payment"
                                            id="MPU_UAB"><span class="receipt-type cash-img"></span><span
                                                class="receipt-txt">UAB</span></button>
                                        <button class="btn heightLine_05 mpu-type mob-mpu card-payment"
                                            id="MPU_MOB"><span class="receipt-type cash-img"></span><span
                                                class="receipt-txt">MOB</span></button>
                                        <button class="btn heightLine_05 mpu-type chd-mpu card-payment"
                                            id="MPU_CHD"><span class="receipt-type cash-img"></span><span
                                                class="receipt-txt">CHD</span></button>

                                        <button class="btn heightLine_05 mpu-type kbz-visa card-payment"
                                            id="VISA_KBZ"><span class="receipt-type cash-img"></span><span
                                                class="receipt-txt">KBZ</span></button>
                                        <button class="btn heightLine_05 mpu-type cb-visa card-payment"
                                            id="VISA_CB"><span class="receipt-type cash-img"></span><span
                                                class="receipt-txt">CB</span></button>
                                    </div>
                                </div>
                            </div>
                            <div class="payment-cal col-md-12">
                                <div class="row">
                                    <div class="col-md-12 payment-show">
                                        <p class="amount-quantity" style="min-height: 33px;">{{number}}</p>
                                    </div>
                                    <div class="col-md-12 receipt-btn3">
                                        <button class="btn quantity" id="1" ng-click="numberClick('1')">1</button>
                                        <button class="btn quantity" id="2" ng-click="numberClick('2')">2</button>
                                        <button class="btn quantity" id="3" ng-click="numberClick('3')">3</button>
                                        <button class="btn quantity" id="4" ng-click="numberClick('4')">4</button>
                                        <button class="btn quantity" id="5" ng-click="numberClick('5')">5</button>
                                        <button class="btn quantity" id="6" ng-click="numberClick('6')">6</button>
                                        <button class="btn quantity" id="7" ng-click="numberClick('7')">7</button>
                                        <button class="btn quantity" id="8" ng-click="numberClick('8')">8</button>
                                        <button class="btn quantity" id="9" ng-click="numberClick('9')">9</button>
                                        <button class="btn quantity" id="0" ng-click="numberClick('0')">0</button>
                                    </div>
                                    <div class="col-md-12 receipt-btn4">
                                        <button class="btn btn-primary void-btn" id='void-item' ng-click="void()">VOID
                                            <i class="fa fa-trash-alt"></i></button>
                                        <button class="btn clear-input-btn" ng-click="clearInput()">CLEAR INPUT</button>
                                        <button class="btn btn-primary foc-btn" ng-disabled="payment_disabled"
                                            ng-click="payment()">to pay</button>
                                    </div>
                                </div>

                            </div>
                        </div> <!-- row -->
                    </div> <!-- col-md-8 -->

                </div>

            </div>
        </div>
    </div><!-- container-fluid -->
</div><!-- wrapper -->

<!-- print template here -->

<!-- @include('cashier.invoice.payment_print') -->

<!-- item print her
        @include('cashier.invoice.items_list')
    -->
<div class="footer text-center">
    <img src="<?= $base_url ?>asset/images/softguide_logo.png" alt="Softguide logo">
</div><!-- footer -->
</div><!-- wrapper -->
<script>
function printInvoice() {
    var printContent = document.getElementById("order_detail"); // ID of the container you want to print
    var originalContent = document.body.innerHTML;

    document.body.innerHTML = printContent.innerHTML;

    window.print();

    document.body.innerHTML = originalContent;
}
</script>
<script src="<?= $base_url ?>asset/js/page/payment.js"></script>
<?php 
require("templates/template_footer.php");
?>