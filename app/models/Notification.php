<?php
class Notification {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // Create a new notification
    public function create($data){
        $this->db->query('INSERT INTO notifications (user_id, type, message, link) VALUES (:user_id, :type, :message, :link)');

        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':message', $data['message']);
        $this->db->bind(':link', $data['link']);

        return $this->db->execute();
    }

    // Get unread notifications count for a user
    public function getUnreadCountByUser($userId){
        $this->db->query('SELECT COUNT(id) as count FROM notifications WHERE user_id = :user_id AND is_read = 0');
        $this->db->bind(':user_id', $userId);
        $row = $this->db->single();
        return $row->count;
    }

    // Get recent unread notifications for a user
    public function getUnreadNotificationsByUser($userId, $limit = 5){
        $this->db->query('SELECT * FROM notifications WHERE user_id = :user_id AND is_read = 0 ORDER BY created_at DESC LIMIT :limit');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    // Get all notifications for a user
    public function getAllNotificationsByUser($userId){
        $this->db->query('SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC');
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }

    // Mark all notifications for a user as read
    public function markAllAsRead($userId){
        $this->db->query('UPDATE notifications SET is_read = 1 WHERE user_id = :user_id AND is_read = 0');
        $this->db->bind(':user_id', $userId);
        return $this->db->execute();
    }
}
?>