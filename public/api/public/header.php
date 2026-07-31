<?php
// header.php - 公共头部文件

include_once $_SERVER['DOCUMENT_ROOT'] . '/api/public/config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/api/mysql/db_connection.php';

// 1. 设置CORS策略[1,2,3](@ref)
// 允许的起源，根据实际情况调整
header("Access-Control-Allow-Origin: *"); // [1,2,3](@ref) 生产环境建议替换为具体域名，例如 'http://localhost:8080'
header("Access-Control-Allow-Credentials: true"); // [2,4](@ref) 允许携带凭证（如Cookies）
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // [1,2,3](@ref) 允许的HTTP方法
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With"); // [1,2,3](@ref) 允许的请求头

// 2. 处理预检请求（OPTIONS）[1,3,5](@ref)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200); // 确保返回200
    exit(); // 不再执行后续代码
}

// 1. 定义免认证的白名单路径（根据你的实际路由调整）
$publicRoutes = [
    '/api/login',                // 登录接口
    '/api/register',             // 注册接口（如果存在）
    '/api/public',              // 任何以 /api/public 开头的接口
    '/api/mysql',              // 任何以 /api/mysql 开头的接口
    // ... 其他无需认证的接口，如获取验证码、健康检查等
];

// 2. 获取当前请求的路径
$currentPath = $_SERVER['REQUEST_URI'];
// 处理掉可能的查询参数，例如 /api/login?username=foo 变成 /api/login
$currentPath = strtok($currentPath, '?');

// 3. 检查当前请求是否在白名单内
foreach ($publicRoutes as $publicRoute) {
    // 简单判断：如果白名单项是路径的开头部分，则匹配
    if (strpos($currentPath, $publicRoute) === 0) {
        // 如果是白名单内的请求，直接跳过后续所有认证逻辑
        return; // 注意：如果header.php是被include的，使用return会返回到调用文件继续执行
    }
}

// 4. 只有非白名单请求（需要认证的请求）才会执行以下的Token验证逻辑
$token = '';
if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $token = $_SERVER['HTTP_AUTHORIZATION'];
} elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
    $token = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
} elseif (function_exists('apache_request_headers')) {
    $headers = apache_request_headers();
    $token = $headers['Authorization'] ?? '';
}

if (!preg_match('/^Bearer\s+(.+)$/i', $token, $matches)) {
    http_response_code(401);
    die(json_encode(['error' => $token]));
}

$jwtToken = $matches[1];
try {
    $decoded = JWT::verifyJWT($jwtToken);
    // 验证成功，将用户信息存入全局变量或请求上下文

    $stmt = $pdo->prepare("SELECT token_version FROM users WHERE id = ?");
    $stmt->bindParam(1, $decoded['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user['token_version'] != $decoded['token_version']) {
        http_response_code(401);
        echo json_encode(['error' => '账号已在其他地方登录，请重新登录']);
        exit();
    }

    echo json_encode(['code' => 0]);

    // 先释放可能存在的语句对象
    if (isset($stmt)) {
        $stmt = null;
    }

} catch (Exception $e) {

    // 先释放可能存在的语句对象
    if (isset($stmt)) {
        $stmt = null;
    }

    http_response_code(401);
    die(json_encode(['error' => 'Token verification failed: ' . $e->getMessage()]));
}

class JWT
{
    // 建议使用足够复杂且保密的密钥
    private static $secretKey = 'your_secret_key';//密钥
    // 签名算法
    private static $algo = 'HS256';

    /**
     * 生成 JWT
     * @param array $payload 需要携带的数据负载，如用户ID、用户名等。可包含标准声明如 'exp'（过期时间）、'iat'（签发时间）等。
     * @return string 返回生成的JWT字符串
     */
    public static function generateJWT(array $payload): string
    {
        // 1. 生成Header并编码
        $header = json_encode(['alg' => self::$algo, 'typ' => 'JWT']);
        $base64UrlHeader = self::base64UrlEncode($header);

        // 2. 生成Payload并编码
        // 确保payload是数组，然后编码
        $base64UrlPayload = self::base64UrlEncode(json_encode($payload));

        // 3. 生成Signature
        // 使用HMAC-SHA256算法和密钥对"header.payload"部分进行签名
        $signature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, self::$secretKey, true);
        $base64UrlSignature = self::base64UrlEncode($signature);

        // 4. 组合三部分，构成完整的JWT
        return $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature;
    }

    /**
     * 验证并解析JWT
     * @param string $token 待验证的JWT字符串
     * @return array|bool 验证成功返回解析后的Payload数组，失败返回false
     */
    public static function verifyJWT(string $token)
    {
        // 1. 拆分JWT为三部分
        $tokenParts = explode('.', $token);
        if (count($tokenParts) != 3) {
            return false;
        }

        list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $tokenParts;

        // 2. 验证签名是否有效
        // 重新计算签名，并与传入的签名对比
        $expectedSignature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, self::$secretKey, true);
        $providedSignature = self::base64UrlDecode($base64UrlSignature);

        // 使用hash_equals防止时序攻击
        if (!hash_equals($expectedSignature, $providedSignature)) {
            return false;
        }

        // 3. 解码Payload
        $payload = json_decode(self::base64UrlDecode($base64UrlPayload), true);

        // 4. 验证过期时间（如果payload中有exp字段）
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }

        return $payload;
    }

    /**
     * Base64URL编码，替换+/为-_，并去除末尾的=
     * @param string $data 待编码数据
     * @return string 编码后的字符串
     */
    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64URL解码，将-_恢复为+/，并补全可能缺失的=
     * @param string $data 待解码数据
     * @return string 解码后的数据
     */
    private static function base64UrlDecode(string $data): string
    {
        // 补全可能缺失的padding
        $padding = strlen($data) % 4;
        if ($padding) {
            $data .= str_repeat('=', 4 - $padding);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}

?>