<?php
// db_connection.php

include_once $_SERVER['DOCUMENT_ROOT'] . '/api/public/header.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/api/public/config.php';

try {
    // 创建PDO实例，DSN指定了数据库类型、主机和数据库名
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $serverusername, $serverpassword);
    
    // 设置错误模式为异常，这样出错时会抛出PDOException，便于调试
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 可选的：设置默认的提取数据模式为关联数组
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // 连接成功，$pdo变量可在其他文件中使用
    // echo "数据库连接成功！"; // 调试时可打开，正常运行时建议关闭

} catch (PDOException $e) {
    // 捕获连接异常，并显示错误信息（生产环境中应记录到日志，而非直接输出）
    die("数据库连接失败: " . $e->getMessage());
}
?>