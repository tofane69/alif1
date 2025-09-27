<?php
class Article {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // Get all articles with user and category data
    public function getArticles(){
        $this->db->query('
            SELECT
                articles.id,
                articles.title,
                articles.created_at,
                articles.is_pinned,
                users.display_name as author,
                categories.name as category_name
            FROM articles
            INNER JOIN users ON articles.user_id = users.id
            INNER JOIN categories ON articles.category_id = categories.id
            ORDER BY articles.is_pinned DESC, articles.created_at DESC
        ');

        return $this->db->resultSet();
    }

    public function addArticle($data){
        // Begin Transaction
        $this->db->beginTransaction();

        try {
            // 1. Insert the article
            $this->db->query('INSERT INTO articles (title, content, category_id, user_id, is_pinned) VALUES (:title, :content, :category_id, :user_id, :is_pinned)');
            $this->db->bind(':title', $data['title']);
            $this->db->bind(':content', $data['content']); // Storing raw HTML from Summernote
            $this->db->bind(':category_id', $data['category_id']);
            $this->db->bind(':user_id', $data['user_id']);
            $this->db->bind(':is_pinned', $data['is_pinned']);
            $this->db->execute();

            $articleId = $this->db->lastInsertId();

            // 2. Handle tags
            if(!empty($data['tags'])){
                $tags = array_unique(array_map('trim', explode(',', $data['tags'])));
                foreach($tags as $tagName){
                    if(empty($tagName)) continue;

                    // Check if tag exists
                    $this->db->query('SELECT id FROM tags WHERE name = :name');
                    $this->db->bind(':name', $tagName);
                    $tag = $this->db->single();

                    if($tag){
                        $tagId = $tag->id;
                    } else {
                        // Insert new tag
                        $this->db->query('INSERT INTO tags (name) VALUES (:name)');
                        $this->db->bind(':name', $tagName);
                        $this->db->execute();
                        $tagId = $this->db->lastInsertId();
                    }
                    // Link tag to article
                    $this->db->query('INSERT INTO article_tags (article_id, tag_id) VALUES (:article_id, :tag_id)');
                    $this->db->bind(':article_id', $articleId);
                    $this->db->bind(':tag_id', $tagId);
                    $this->db->execute();
                }
            }

            // 3. Create the first version
            $this->db->query('INSERT INTO article_versions (article_id, user_id, title, content) VALUES (:article_id, :user_id, :title, :content)');
            $this->db->bind(':article_id', $articleId);
            $this->db->bind(':user_id', $data['user_id']);
            $this->db->bind(':title', $data['title']);
            $this->db->bind(':content', $data['content']);
            $this->db->execute();

            // If all is well, commit the transaction
            if($this->db->commit()){
                return true;
            } else {
                $this->db->rollBack();
                return false;
            }

        } catch (Exception $e) {
            // If anything goes wrong, roll back
            $this->db->rollBack();
            // Optional: log the error $e->getMessage()
            return false;
        }
    }

    public function getArticleById($id){
        $this->db->query('
            SELECT
                articles.*,
                users.display_name as author,
                categories.name as category_name
            FROM articles
            INNER JOIN users ON articles.user_id = users.id
            INNER JOIN categories ON articles.category_id = categories.id
            WHERE articles.id = :id
        ');
        $this->db->bind(':id', $id);
        $article = $this->db->single();

        if($article){
            // Get tags for this article
            $this->db->query('
                SELECT tags.name
                FROM tags
                INNER JOIN article_tags ON tags.id = article_tags.tag_id
                WHERE article_tags.article_id = :article_id
            ');
            $this->db->bind(':article_id', $id);
            $tags = $this->db->resultSet();
            $article->tags = $tags;
        }

        return $article;
    }

    public function updateArticle($data){
        $this->db->beginTransaction();

        try {
            // 1. Update the article itself
            $this->db->query('UPDATE articles SET title = :title, content = :content, category_id = :category_id, is_pinned = :is_pinned WHERE id = :id');
            $this->db->bind(':id', $data['id']);
            $this->db->bind(':title', $data['title']);
            $this->db->bind(':content', $data['content']);
            $this->db->bind(':category_id', $data['category_id']);
            $this->db->bind(':is_pinned', $data['is_pinned']);
            $this->db->execute();

            // 2. Handle tags (simple approach: delete all and re-add)
            // Delete old tags associations
            $this->db->query('DELETE FROM article_tags WHERE article_id = :article_id');
            $this->db->bind(':article_id', $data['id']);
            $this->db->execute();

            // Add new tags
            if(!empty($data['tags'])){
                $tags = array_unique(array_map('trim', explode(',', $data['tags'])));
                foreach($tags as $tagName){
                    if(empty($tagName)) continue;

                    $this->db->query('SELECT id FROM tags WHERE name = :name');
                    $this->db->bind(':name', $tagName);
                    $tag = $this->db->single();

                    if($tag){
                        $tagId = $tag->id;
                    } else {
                        $this->db->query('INSERT INTO tags (name) VALUES (:name)');
                        $this->db->bind(':name', $tagName);
                        $this->db->execute();
                        $tagId = $this->db->lastInsertId();
                    }

                    $this->db->query('INSERT INTO article_tags (article_id, tag_id) VALUES (:article_id, :tag_id)');
                    $this->db->bind(':article_id', $data['id']);
                    $this->db->bind(':tag_id', $tagId);
                    $this->db->execute();
                }
            }

            // 3. Create a new version
            $this->db->query('INSERT INTO article_versions (article_id, user_id, title, content) VALUES (:article_id, :user_id, :title, :content)');
            $this->db->bind(':article_id', $data['id']);
            $this->db->bind(':user_id', $data['user_id']);
            $this->db->bind(':title', $data['title']);
            $this->db->bind(':content', $data['content']);
            $this->db->execute();

            if($this->db->commit()){
                return true;
            } else {
                $this->db->rollBack();
                return false;
            }

        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function deleteArticle($id){
        $this->db->query('DELETE FROM articles WHERE id = :id');
        $this->db->bind(':id', $id);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }
}
?>