<?php
// your_api_endpoint.php
require_once 'db_connection.php';        // 你的数据库连接文件
require_once 'ApiRouter.php'; // 新引入的路由器
// 自动加载所有处理类（推荐使用自动加载器）
require_once 'handlers/UserDataHandler.php';
require_once 'handlers/JsonArraysTableDataHandler.php';
require_once 'handlers/ArticlesDataHandler.php';
require_once 'handlers/ArticlesViewDataHandler.php';
require_once 'handlers/ArticlesLikeDataHandler.php';
require_once 'handlers/PictureDataHandler.php';
require_once 'handlers/PhotoWallDataHandler.php';
// ... 引入其他表的处理类

header('Content-Type: application/json');

try {
    // 获取输入数据
    $input = json_decode(file_get_contents('php://input'), true) ?? [];

    // 创建路由并处理请求
    $router = new ApiRouter($pdo); // $pdo 来自 db.php
    $result = $router->handleRequest($input);

    echo $result;

} catch (Exception $e) {
    // 统一的异常处理[7](@ref)
    echo json_encode(['success' => false, 'error' => 'A server error occurred.']);
}
?>