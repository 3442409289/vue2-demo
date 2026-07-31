<?php

class ArticlesViewDataHandler
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function read($input)
    {
        // 分页参数处理
        $page = isset($input['page']) ? (int) $input['page'] : 1;
        $per_page = 10; // 每页显示10篇文章
        $offset = ($page - 1) * $per_page;

        // 获取总文章数
        $count_stmt = $this->pdo->query("SELECT COUNT(*) as total FROM articles");
        $total_articles = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $total_pages = ceil($total_articles / $per_page);

        // 获取当前页的文章数据
        $sql = "SELECT 
                a.id, 
                a.title, 
                a.author_id, 
                a.created_at,
                u.username as author_name 
                FROM articles a 
                LEFT JOIN users u ON a.author_id = u.id 
                ORDER BY a.created_at DESC 
                LIMIT $offset, $per_page";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // 转换特定字段为整数
        foreach ($articles as &$article) {
            $article['id'] = (int) $article['id'];
            $article['author_id'] = (int) $article['author_id'];
        }
        return json_encode(['data' => $articles, 'total_pages' => $total_pages]);
    }

    public function write($input)
    {
        return json_encode(['success' => '0']);
    }
}
?>