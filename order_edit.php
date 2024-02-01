<?php 
session_start();
require("common/config.php");
require("common/database.php");
require("Auth/check_cashier_auth.php");
require("Auth/shift_auth.php");
$ctitle = "Make Order";
$id = (int) ($_GET['id']);
$id = $mysqli->real_escape_string($id);
$check_status = "SELECT count(id) as total FROM orders WHERE id = '$id' AND status = '0'";
$check_result = $mysqli->query($check_status);
while($row_cunt = $check_result->fetch_assoc())
        {
            $total = $row_cunt['total'];
        }
        if($total <= 0) {
            $url = $base_url."order_list";
            header("Refresh: 0; url = $url");
            exit();
        }
require("templates/template_header.php");
?>

<div class="container-fluid receipt category-pg" ng-app="myApp" ng-controller="myCtrl" ng-init="init(<?= $id ?>)">
    <div class="row cmn-ttl cmn-ttl2">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-5 col-sm-6 col-6">
                    <h3>
                        Category
                    </h3>
                </div>
                <div class="col-lg-8 col-md-7 col-sm-6 col-6 receipt-btn">
                    <button class="btn" ng-click="returnBack()">
                        <img src="<?= $base_url ?>asset/images/payment/previous_img.png" alt="Previous"
                            class="heightLine_06" />
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="item-container">
        <div class="row">
            <div class="col-md-9">
                <div class="cat-table">
                    <div class="table-responsive">
                        <button class="scroll-txt cat-to-btm2"><i class="fas fa-angle-double-down"></i></button>
                        <form class="form-horizontal" id="order-form">
                            <table class="table table-hover item-list" style="text-align:left;">
                                <thead>
                                    <tr>
                                        <th width="20%" align="center">Item Name</th>
                                        <th width="20%" align="center">Quantity</th>
                                        <th width="10%" align="center">Price</th>
                                        <th width="10%" align="center">Discount</th>
                                        <th width="10%" align="center">Amount</th>
                                        <th width="15%" align="center">Item Code</th>
                                        <th width="15%" align="center">Cancel</th>
                                    </tr>
                                </thead>
                                <tbody id="cat-table-body">
                                    <tr class="item-tr" ng-repeat="data in itemData">
                                        <td class="item-td" width="20%">
                                            <p>{{ data.name }}</p>
                                        </td>
                                        <td class="cart_quantity item-td" width="20%">
                                            <div class="qty-box">
                                                <input type='button' value='-' class='qtyminus'
                                                    ng-click="minusItem(data.id)" field='quantity' />
                                                <input type='text' value='{{ data.quantity }}' class='qty' />
                                                <input type='button' value='+' class='qtyplus'
                                                    ng-click="plusItem(data.id)" field='quantity' />
                                            </div>
                                        </td>
                                        <td class="item-td" width="10%">{{ data.price }}</td>
                                        <td id="discount" class="item-td">
                                            <span>{{data.discount}}</span>
                                        </td>
                                        <td class="item-td" width="10%">
                                            <input type="text" value="{{data.amount}}"
                                                style="border:none;background:none;" readonly class="price-input" />
                                        </td>
                                        <td class="item-td" width="15%">
                                            <span>{{data.code_no}}</span>
                                        </td>

                                        <td class="item-td" width="15%">
                                            <button class="cancel-btn" type="button"
                                                ng-click="cancelItem(data.id)">Cancel</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                        <button class="scroll-txt cat-to-top2" type="button"><i
                                class="fas fa-angle-double-up"></i></button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="price-table">
                            <table>
                                <tbody>
                                    <tr>
                                        <td colspan="2" rowspan="5" class="order-btn-gp">
                                            <button class="order-btn makeorder-btn" id="back-lg" ng-click="orderList()">
                                                <img src="<?= $base_url ?>asset/images/payment/previous_img.png"
                                                    alt="Previous" class="heightLine_06">
                                            </button>
                                            <button class="order-btn makeorder-btn" id="order-item"
                                                ng-click="insertOrder(<?= $id ?>)">
                                                <img src="<?= $base_url ?>asset/images/payment/order.png"
                                                    class="heightLine_06">
                                            </button>
                                        </td>
                                        <td>Sub Total : </td>
                                        <td id="sub-total">{{total}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> <!-- col-md-9 -->

            <div class="col-md-3">
                <div class="row category">
                    <div class="col-md-12 cat-list" id="cathome">
                        <div class="cat-ttl">
                            <button class="backBtn" id="" ng-click="getParentCategory()"><i
                                    class="fas fa-angle-left"></i></button>
                            <input type="text" class="search-item" ng-model="search_item" ng-change="searchItem()">
                            <div style="clear:both"></div>
                        </div>

                        <div class="tab-content row" id="cat-tab-content">
                            <button class="scroll-txt cat-to-btm"><i class="fas fa-angle-double-down"></i></button>
                            <div class="tab-pane active clearfix" id="categoryDiv" role="tabpanel">
                                <!-- Category Loop Start Here -->
                                <div class="cat-box" style="width: 45%" ng-repeat="category in categories"
                                    ng-if="showCategory">
                                    <button ng-click="getChildCategory(category.id)">
                                        <figure>
                                            <img ng-src="{{base_url}}asset/upload/{{category.id}}/{{category.image}}"
                                                class="img-responsive">
                                            <figcaption>{{ category.name }}</figcaption>
                                        </figure>
                                    </button>
                                </div>
                                <!-- Category Loop End Here -->
                                <div class="cat-box" ng-if="showItem" style="width: 45%" ng-repeat="item in items">
                                    <button ng-click="getItem(item.id)">
                                        <figure>
                                            <img ng-src="{{base_url}}asset/item/{{item.id}}/{{item.image}}"
                                                class="img-responsive">
                                            <figcaption>{{ item.name }}</figcaption>
                                        </figure>
                                    </button>
                                </div>

                            </div>

                            <div class="tab-pane" id="setDiv" role="tabpanel">
                            </div>
                            <button class="scroll-txt cat-to-top"><i class="fas fa-angle-double-up"></i></button>
                        </div> <!-- tab-content -->
                    </div>
                </div> <!-- row -->
            </div> <!-- col-md-3 -->
        </div><!-- row -->
    </div>
</div><!-- container-fluid -->
</div><!-- wrapper -->
<script src="<?= $base_url ?>asset/js/page/order_edit.js"></script>
<?php 
require("templates/template_footer.php");
?>