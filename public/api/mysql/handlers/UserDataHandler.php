<?php

class UserDataHandler
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function read($input)
    {
        // 处理读取用户数据的逻辑
        $stmt = $this->pdo->prepare("SELECT id, username, email, password, permissions, created_at FROM users WHERE id = ?");
        $stmt->execute([$input['id']]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return json_encode($data);
    }

    public function write($input)
    {
        $data = $input['data'];
        // 处理写入/更新用户数据的逻辑，使用预处理语句防SQL注入[3](@ref)
        $sql = "INSERT INTO users (id, username, email, password, permissions) 
            VALUES (:id, :username, :email, :password, :permissions)
            ON DUPLICATE KEY UPDATE 
                username = VALUES(username),
                email = VALUES(email),
                password = VALUES(password),
                permissions = VALUES(permissions)";
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([
            ':id' => $data['id'],
            ':username' => $data['username'],
            ':email' => $data['email'],
            ':password' => $data['password'],
            ':permissions' => $data['permissions']
        ]);
        return json_encode(['success' => $success]);
    }
}
?>