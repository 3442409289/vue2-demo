<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/api/public/header.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/api/public/config.php';

// // 验证Token
// $decodedPayload = JWT::verifyJWT($jwtToken);
// if ($decodedPayload) {
//     echo "JWT is valid. Payload: " . print_r($decodedPayload, true) . "\n";
// } else {
//     echo "Invalid or expired JWT.\n";
// }

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true); // 转换为关联数组
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

// 初始化验证标志 (默认为false更安全)
$isValid = false;

// 这里假设你已建立数据库连接 $dbh
try {
    // 1. 预处理SQL语句防止SQL注入[7](@ref)
    $dbh = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $serverusername, $serverpassword);
    $stmt = $dbh->prepare("SELECT id, username, password, email, permissions, token_version FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    // 2. 获取用户记录
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3. 验证密码
    if ($user && password_verify($password, $user['password'])) {
        $isValid = true;
        // 可选：登录成功后创建用户会话[6](@ref)
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['logged_in'] = true;

        // 还可以检查密码是否需要重新哈希（例如算法升级时）
        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            // 将 $newHash 更新到数据库中
        }
    }

    // 先释放可能存在的语句对象
    if (isset($stmt)) {
        $stmt = null;
    }

} catch (PDOException $e) {
    // 生产环境中应记录日志而非直接输出错误
    error_log("Database error: " . $e->getMessage());
    $isValid = false;

    // 先释放可能存在的语句对象
    if (isset($stmt)) {
        $stmt = null;
    }
}

if ($isValid) {
    // 验证成功，返回 token 和用户信息

    $newTokenVersion = $user['token_version'] + 1;

    $stmt = $dbh->prepare("UPDATE users SET token_version = ? WHERE id = ?");
    $stmt->bindParam(1, $newTokenVersion, PDO::PARAM_INT); // 绑定第一个问号占位符
    $stmt->bindParam(2, $user['id'], PDO::PARAM_INT);          // 绑定第二个问号占位符
    $stmt->execute();

    // 使用示例：
    $payload = [
        'user_id' => $user['id'],
        'username' => $user['username'],
        'token_version' => $newTokenVersion, // 加入 token_version
        'iat' => time(), // 签发时间 (Issued At)
        'exp' => time() + 3600 // 过期时间 (Expiration Time)，1小时后
    ];

    try {
        // 生成Token
        $jwtToken = JWT::generateJWT($payload);

        $response = [
            'token' => $jwtToken, // 这里应替换为实际生成的 token，例如 JWT
            'token_type' => 'Bearer', // 明确Token类型，符合常见规范[6](@ref)
            'expires_in' => 3600, // 明确告知客户端Token的有效期（秒）
            'user' => [ // 用户信息对象，结构可根据需要调整
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'permissions' => $user['permissions']
                // ... 其他用户信息字段
            ]
        ];

        // 设置HTTP状态码为200 OK（默认，也可明确设置）
        http_response_code(200);
        header('Content-Type: application/json'); // 明确响应内容类型
        echo json_encode($response);

        // 先释放可能存在的语句对象
        if (isset($stmt)) {
            $stmt = null;
        }

    } catch (Exception $e) {
        // Token生成过程中发生错误
        http_response_code(500); // 内部服务器错误
        echo json_encode(['error' => 'Token生成失败，请稍后重试']);
        error_log("JWT Generation Error: " . $e->getMessage()); // 记录错误日志

        // 先释放可能存在的语句对象
        if (isset($stmt)) {
            $stmt = null;
        }
    }
} else {
    // 验证失败，返回错误信息[1,3](@ref)
    http_response_code(401); // 设置 HTTP 状态码为 401 未授权
    echo json_encode(['error' => '用户名或密码错误']);

    // 先释放可能存在的语句对象
    if (isset($stmt)) {
        $stmt = null;
    }
}

// 先释放可能存在的语句对象
if (isset($stmt)) {
    $stmt = null;
}

// 然后手动关闭连接
$dbh = null;
?>