<?php
class Like {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // Check if a user has already liked an item
    public function hasUserLiked($userId, $likeableId, $likeableType){
        $this->db->query('SELECT id FROM likes WHERE user_id = :user_id AND likeable_id = :likeable_id AND likeable_type = :likeable_type');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':likeable_id', $likeableId);
        $this->db->bind(':likeable_type', $likeableType);
        $this->db->single();
        return $this->db->rowCount() > 0;
    }

    // Add a like
    public function addLike($userId, $likeableId, $likeableType){
        $this->db->query('INSERT INTO likes (user_id, likeable_id, likeable_type) VALUES (:user_id, :likeable_id, :likeable_type)');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':likeable_id', $likeableId);
        $this->db->bind(':likeable_type', $likeableType);
        return $this->db->execute();
    }

    // Remove a like
    public function removeLike($userId, $likeableId, $likeableType){
        $this->db->query('DELETE FROM likes WHERE user_id = :user_id AND likeable_id = :likeable_id AND likeable_type = :likeable_type');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':likeable_id', $likeableId);
        $this->db->bind(':likeable_type', $likeableType);
        return $this->db->execute();
    }

    // Get the number of likes for an item
    public function getLikesCount($likeableId, $likeableType){
        $this->db->query('SELECT COUNT(id) as like_count FROM likes WHERE likeable_id = :likeable_id AND likeable_type = :likeable_type');
        $this->db->bind(':likeable_id', $likeableId);
        $this->db->bind(':likeable_type', $likeableType);
        $result = $this->db->single();
        return $result->like_count;
    }
}
?>