<?php

class ArticlesDataHandler
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function read($input)
    {
        // 处理读取用户数据的逻辑
        if (isset($input['id'])) {
            $stmt = $this->pdo->prepare("SELECT id, title, content, author_id, created_at FROM articles WHERE id = ?");
            $stmt->execute([$input['id']]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
        } else if (isset($input['author_id'])) {
            $stmt = $this->pdo->prepare("SELECT id, title, content, author_id, created_at FROM articles WHERE author_id = ?");
            $stmt->execute([$input['author_id']]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return;
        }
        return json_encode($data);
    }

    public function write($input)
    {
        $data = $input['data'];
        // 处理写入/更新用户数据的逻辑，使用预处理语句防SQL注入[3](@ref)
        $sql = "INSERT INTO articles (id, title, content, author_id) 
            VALUES (:id, :title, :content, :author_id)
            ON DUPLICATE KEY UPDATE 
                title = VALUES(title),
                content = VALUES(content),
                author_id = VALUES(author_id)";
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([
            ':id' => $data['id'],
            ':title' => $data['title'],
            ':content' => $data['content'],
            ':author_id' => $data['author_id']
        ]);
        return json_encode(['success' => $success]);
    }
}
?>