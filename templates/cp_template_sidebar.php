<?php 
require("../common/config.php");
?>
<div class="col-md-3 left_col">
    <div class="left_col scroll-view">
        <div class="navbar nav_title" style="border: 0;">
            <a href="index.html" class="site_title"><i class="fa fa-paw"></i> <span>SG POS</span></a>
        </div>

        <div class="clearfix"></div>

        <!-- menu profile quick info -->
        <div class="profile clearfix">
            <div class="profile_pic">
                <img src="<?= $base_url ?>asset/images/img.jpg" alt="..." class="img-circle profile_img">
            </div>
            <div class="profile_info">
                <span>Welcome,</span>
                <h2>John Doe</h2>
            </div>
        </div>
        <!-- /menu profile quick info -->

        <br />

        <!-- sidebar menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
                <h3>General</h3>
                <ul class="nav side-menu">
                    <li><a href="<?= $cp_base_url ?>index.php"><i class="fa fa-home"></i> Home </a>
                    </li>
                    <li><a><i class="fa fa-edit"></i> Shift <span class="fa fa-chevron-down"><span></a>
                        <ul class="nav child_menu">
                            <li><a href="<?= $cp_base_url ?>shift.php">shift</a></li>
                        </ul>
                    </li>
                    <li><a><i class="fa fa-edit"></i> Category <span class="fa fa-chevron-down"><span></a>
                        <ul class="nav child_menu">
                            <li><a href="<?= $cp_base_url ?>category_create.php">Create category</a></li>
                            <li><a href="<?= $cp_base_url ?>category_list.php">Category List</a></li>
                        </ul>
                    </li>
                    <li><a><i class="fa fa-edit"></i> Item <span class="fa fa-chevron-down"><span></a>
                        <ul class="nav child_menu">
                            <li><a href="<?= $cp_base_url ?>item_create.php">Create Item</a></li>
                            <li><a href="<?= $cp_base_url ?>item_list.php">Item List</a></li>
                        </ul>
                    </li>
                    <li><a><i class="fa fa-edit"></i>Discount<span class="fa fa-chevron-down"><span></a>
                        <ul class="nav child_menu">
                            <li><a href="<?= $cp_base_url ?>discount_create.php">Discount Item</a></li>
                            <li><a href="<?= $cp_base_url ?>discount_list.php">Discount List</a></li>
                        </ul>
                    </li>
                    <li><a><i class="fa fa-edit"></i>Cashier<span class="fa fa-chevron-down"><span></a>
                        <ul class="nav child_menu">
                            <li><a href="<?= $cp_base_url ?>Cashier_create.php">Cashier Create</a></li>
                            <li><a href="<?= $cp_base_url ?>Cashier_list.php">Cashier List</a></li>
                        </ul>
                    </li>
                    <li><a><i class="fa fa-edit"></i>Admin<span class="fa fa-chevron-down"><span></a>
                        <ul class="nav child_menu">
                            <li><a href="<?= $cp_base_url ?>Admin_create.php">Admin Create</a></li>
                            <li><a href="<?= $cp_base_url ?>Admin_list.php">Admin List</a></li>
                        </ul>
                    </li>
                    <li><a><i class="fa fa-edit"></i>Setting<span class="fa fa-chevron-down"><span></a>
                        <ul class="nav child_menu">
                            <li><a href="<?= $cp_base_url ?>setting_create.php">Create</a></li>
                            <li><a href="<?= $cp_base_url ?>list.php">List</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

        </div>
        <!-- /sidebar menu -->
    </div>
</div>