<?php

// config.php

$serverIp = $_SERVER['SERVER_ADDR'];
// 将IP地址按点号分割成数组
$ipParts = explode('.', $serverIp);

if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['SERVER_ADDR'] == '127.0.0.1' || ($ipParts[0] == '192' && $ipParts[1] == '168')) {
    // 本地开发环境配置
    $servername = "127.0.0.1";
    $serverusername = "root";
    $serverpassword = "root";
    $dbname = "295496";
} else {
    // 线上生产环境配置
    $servername = "127.0.0.1";
    $serverusername = "295496";
    $serverpassword = "jiang20030527";
    $dbname = "295496";
}

?>