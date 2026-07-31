<?php

class ArticlesLikeDataHandler
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * 读取当前用户对指定文章的点赞状态
     */
    public function read($input)
    {
        // 验证输入参数
        if (!isset($input['article_id']) || !isset($input['user_id'])) {
            return json_encode([
                'success' => false,
                'error' => '缺少必要参数: article_id 或 user_id'
            ]);
        }

        $articleId = (int)$input['article_id'];
        $userId = (int)$input['user_id'];

        try {
            // 查询用户是否已点赞[3](@ref)
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as like_count 
                FROM article_likes 
                WHERE article_id = ? AND user_id = ?
            ");
            
            $stmt->execute([$articleId, $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $isLiked = ($result['like_count'] > 0);
            
            // 获取文章总点赞数
            $totalLikes = $this->getArticleTotalLikes($articleId);

            return json_encode([
                'success' => true,
                'is_liked' => $isLiked,
                'total_likes' => $totalLikes,
                'article_id' => $articleId,
                'user_id' => $userId
            ]);

        } catch (PDOException $e) {
            return json_encode([
                'success' => false,
                'error' => '数据库查询失败: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 写入点赞/取消点赞数据
     */
    public function write($input)
    {
        // 验证输入参数
        if (!isset($input['article_id']) || !isset($input['user_id']) || !isset($input['action'])) {
            return json_encode([
                'success' => false,
                'error' => '缺少必要参数: article_id, user_id 或 action'
            ]);
        }

        $articleId = (int)$input['article_id'];
        $userId = (int)$input['user_id'];
        $action = $input['action']; // 'like' 或 'unlike'

        try {
            // 开始事务
            $this->pdo->beginTransaction();

            if ($action === 'like') {
                $success = $this->handleLikeAction($articleId, $userId);
            } elseif ($action === 'unlike') {
                $success = $this->handleUnlikeAction($articleId, $userId);
            } else {
                throw new Exception('无效的操作类型');
            }

            if ($success) {
                $this->pdo->commit();
                $totalLikes = $this->getArticleTotalLikes($articleId);
                
                return json_encode([
                    'success' => true,
                    'action' => $action,
                    'total_likes' => $totalLikes,
                    'article_id' => $articleId,
                    'user_id' => $userId
                ]);
            } else {
                $this->pdo->rollBack();
                return json_encode([
                    'success' => false,
                    'error' => '操作失败'
                ]);
            }

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return json_encode([
                'success' => false,
                'error' => '操作失败: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 处理点赞操作
     */
    private function handleLikeAction($articleId, $userId)
    {
        // 检查是否已经点赞[3](@ref)
        $checkStmt = $this->pdo->prepare("
            SELECT id FROM article_likes 
            WHERE article_id = ? AND user_id = ?
        ");
        $checkStmt->execute([$articleId, $userId]);
        
        if ($checkStmt->fetch()) {
            throw new Exception('用户已经点赞过该文章');
        }

        // 插入点赞记录[1,2](@ref)
        $insertStmt = $this->pdo->prepare("
            INSERT INTO article_likes (article_id, user_id) 
            VALUES (?, ?)
        ");
        
        return $insertStmt->execute([$articleId, $userId]);
    }

    /**
     * 处理取消点赞操作
     */
    private function handleUnlikeAction($articleId, $userId)
    {
        // 删除点赞记录[2](@ref)
        $deleteStmt = $this->pdo->prepare("
            DELETE FROM article_likes 
            WHERE article_id = ? AND user_id = ?
        ");
        
        $result = $deleteStmt->execute([$articleId, $userId]);
        
        // 如果没有任何行被删除，说明之前没有点赞记录
        if ($deleteStmt->rowCount() === 0) {
            throw new Exception('用户尚未点赞该文章');
        }
        
        return $result;
    }

    /**
     * 获取文章总点赞数
     */
    private function getArticleTotalLikes($articleId)
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as total 
            FROM article_likes 
            WHERE article_id = ?
        ");
        $stmt->execute([$articleId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int)$result['total'];
    }
}