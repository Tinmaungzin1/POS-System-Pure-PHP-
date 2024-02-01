<?php 
require("common/config.php");
session_start();
session_unset();
session_destroy();
$url = $base_url."login.php";
header("Refresh: 0; url = $url");
exit();