<?php
class Notifications extends Controller {

    public function __construct(){
        if(!isLoggedIn()){
            $this->redirect('users/login');
        }

        $this->notificationModel = $this->model('Notification');
    }

    // Display all notifications for the user
    public function index(){
        $notifications = $this->notificationModel->getAllNotificationsByUser($_SESSION['user_id']);

        $data = [
            'title' => 'مرکز اعلان‌ها',
            'notifications' => $notifications
        ];

        $this->view('notifications/index', $data);
    }

    // Mark all notifications as read
    public function markAllRead(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $this->notificationModel->markAllAsRead($_SESSION['user_id']);
            $this->redirect('notifications');
        } else {
            $this->redirect('notifications');
        }
    }

    // Fetch unread notifications for AJAX request
    public function fetchUnread(){
        $count = $this->notificationModel->getUnreadCountByUser($_SESSION['user_id']);
        $notifications = $this->notificationModel->getUnreadNotificationsByUser($_SESSION['user_id']);

        header('Content-Type: application/json');
        echo json_encode([
            'count' => $count,
            'notifications' => $notifications
        ]);
        exit();
    }
}
?>