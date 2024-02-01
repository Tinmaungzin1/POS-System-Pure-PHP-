<?php
    $base_url = "http://localhost/sg_pos/";
    $cp_base_url = "http://localhost/sg_pos/sg_backend/";
    date_default_timezone_set('Asia/Yangon');
    $shakey = "TIN105";

    for ($i = 0; $i <= 2; $i++) {
        $code_key = chr(rand(65, 99));
    }
    
    $admin_role = 1;
    $cashier_role = 2;
    $admin_enable_status = 0;
    $admin_disable_status = 1;
    $unpaid_status = 0;
    $paid_status = 1;
    $cancel_status = 2;
    $admin_row = 1;
    $cashier_row = 2;
    $enable_status = 0;
    $disable_status = 1;
    $image_width = 200;
    $image_height= 200;
    // $md5 = md5($shakey . md5('password'));
    // echo $md5;
    // exit();
?>