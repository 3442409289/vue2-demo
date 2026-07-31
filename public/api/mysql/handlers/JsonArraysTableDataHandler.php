<?php

class JsonArraysTableDataHandler
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function read($input)
    {
        // 处理读取用户数据的逻辑
        $stmt = $this->pdo->prepare("SELECT id, data, created_at FROM json_arrays_table WHERE id = ?");
        $stmt->execute([$input['id']]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data['data'];
    }

    public function write($input)
    {
        // 处理写入/更新用户数据的逻辑，使用预处理语句防SQL注入[3](@ref)
        $stmt = $this->pdo->prepare("
        INSERT INTO json_arrays_table (id, data) VALUES (:id, :data)
        ON DUPLICATE KEY UPDATE
        id = VALUES(id),
        data = VALUES(data)");
        $success = $stmt->execute(['id' => $input['id'], 'data' => json_encode($input['data'])]);
        return json_encode(['success' => $success]);
    }
}
?>