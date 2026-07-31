<?php

class PhotoWallDataHandler
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
        $stmt = $this->pdo->prepare("SELECT user_id, image_data1, image_data2, image_data3, created_at FROM photowall WHERE user_id = ?");
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
        $sql1 = "INSERT INTO photowall (user_id, image_data1) 
            VALUES (:user_id, :image_data1)
            ON DUPLICATE KEY UPDATE 
                user_id = VALUES(user_id),
                image_data1 = VALUES(image_data1)";

        $sql2 = "INSERT INTO photowall (user_id, image_data2) 
            VALUES (:user_id, :image_data2)
            ON DUPLICATE KEY UPDATE 
                user_id = VALUES(user_id),
                image_data2 = VALUES(image_data2)";

        $sql3 = "INSERT INTO photowall (user_id, image_data3) 
            VALUES (:user_id, :image_data3)
            ON DUPLICATE KEY UPDATE 
                user_id = VALUES(user_id),
                image_data3 = VALUES(image_data3)";

        switch ($data['index']) {
            case 0:
                $stmt = $this->pdo->prepare($sql1);
                $success = $stmt->execute([
                    ':user_id' => $input['user_id'],
                    ':image_data1' => $data['image_data']
                ]);
                break;
            case 1:
                $stmt = $this->pdo->prepare($sql2);
                $success = $stmt->execute([
                    ':user_id' => $input['user_id'],
                    ':image_data2' => $data['image_data']
                ]);
                break;
            case 2:
                $stmt = $this->pdo->prepare($sql3);
                $success = $stmt->execute([
                    ':user_id' => $input['user_id'],
                    ':image_data3' => $data['image_data']
                ]);
                break;
        }

        return json_encode(['success' => $success]);
    }
}
?>