<?php

class PictureDataHandler
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * 读取用户的头像图片
     * param array input ['user_id' => int]
     * return string JSON
     */
    public function read($input)
    {
        $stmt = $this->pdo->prepare("SELECT user_id, image_data, created_at FROM picture WHERE user_id = ?");
        $stmt->execute([$input['user_id']]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return json_encode($data);
    }

    /**
     * 保存或更新用户的头像图片
     * param array input ['user_id' => int, 'data' => ['image_data' => string(base64), 'filename' => string]]
     * return string JSON
     */
    public function write($input)
    {
        $data = $input['data'];
        $sql = "INSERT INTO picture (user_id, image_data) 
            VALUES (:user_id, :image_data)
            ON DUPLICATE KEY UPDATE 
                user_id = VALUES(user_id),
                image_data = VALUES(image_data)";
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([
            ':user_id' => $input['user_id'],
            ':image_data' => $data['image_data']
        ]);
        return json_encode(['success' => $success]);
    }
}
?>