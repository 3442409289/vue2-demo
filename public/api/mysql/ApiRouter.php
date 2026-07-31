<?php
// ApiRouter.php
class ApiRouter
{
    private $pdo; // 数据库连接实例

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function handleRequest($input)
    {
        // 1. 参数验证与获取
        $mode = $input['mode'] ?? '';
        $type = (int) ($input['type'] ?? -1); // 默认为-1，表示未提供

        // 2. 构建处理类的类名，例如：将 'user' 映射为 'UserDataHandler'
        $className = ucfirst(strtolower($mode)) . 'DataHandler';

        // 3. 检查类是否存在
        if (!class_exists($className)) {
            return json_encode(['error' => 'Unsupported mode or handler not found.' . $className]);
        }

        // 4. 实例化处理类并调用相应方法 (0=读, 1=写)
        $handler = new $className($this->pdo);
        if ($type === 0) {
            return $handler->read($input);
        } elseif ($type === 1) {
            return $handler->write($input);
        } else {
            return json_encode(['error' => 'Invalid type parameter.']);
        }
    }
}
?>