<?php
class Comment {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // Get all comments for a specific article, including like data
    public function getCommentsByArticleId($articleId, $currentUserId){
        $this->db->query('
            SELECT
                comments.id,
                comments.user_id,
                comments.parent_id,
                comments.comment_text,
                comments.voice_note_path,
                comments.created_at,
                users.display_name as author_name,
                roles.fa_name as author_role,
                (SELECT COUNT(*) FROM likes WHERE likes.likeable_id = comments.id AND likes.likeable_type = "comment") as likes_count,
                (SELECT COUNT(*) FROM likes WHERE likes.likeable_id = comments.id AND likes.likeable_type = "comment" AND likes.user_id = :current_user_id) as user_has_liked
            FROM comments
            INNER JOIN users ON comments.user_id = users.id
            INNER JOIN roles ON users.role_id = roles.id
            WHERE comments.article_id = :article_id
            ORDER BY comments.created_at ASC
        ');
        $this->db->bind(':article_id', $articleId);
        $this->db->bind(':current_user_id', $currentUserId);
        return $this->db->resultSet();
    }

    // Add a new comment
    public function addComment($data){
        $this->db->query('INSERT INTO comments (article_id, user_id, parent_id, comment_text, voice_note_path) VALUES (:article_id, :user_id, :parent_id, :comment_text, :voice_note_path)');

        $this->db->bind(':article_id', $data['article_id']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':parent_id', $data['parent_id']);
        $this->db->bind(':comment_text', $data['comment_text']);
        $this->db->bind(':voice_note_path', $data['voice_note_path']);

        if($this->db->execute()){
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // Get a single comment by its ID
    public function getCommentById($id){
        $this->db->query('SELECT * FROM comments WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Delete a comment
    public function deleteComment($id){
        $this->db->query('DELETE FROM comments WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
?>