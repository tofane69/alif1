<?php
class Likes extends Controller {

    public function __construct(){
        if(!isLoggedIn()){
            // For AJAX requests, it's better to send an error response
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }

        $this->likeModel = $this->model('Like');
    }

    // Toggle a like on an item (comment or article)
    public function toggle($likeableType, $likeableId){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // CSRF Check for AJAX
            $submittedToken = isset($_SERVER['HTTP_X_CSRF_TOKEN']) ? $_SERVER['HTTP_X_CSRF_TOKEN'] : '';
            if(!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $submittedToken)){
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Invalid CSRF Token.']);
                exit();
            }

            $userId = $_SESSION['user_id'];
            $likeableId = (int)$likeableId;

            // Basic validation
            if(!in_array($likeableType, ['comment', 'article']) || empty($likeableId)){
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Invalid request']);
                exit();
            }

            $hasLiked = $this->likeModel->hasUserLiked($userId, $likeableId, $likeableType);

            if($hasLiked){
                // Unlike the item
                $this->likeModel->removeLike($userId, $likeableId, $likeableType);
                $userHasLiked = false;
            } else {
                // Like the item
                $this->likeModel->addLike($userId, $likeableId, $likeableType);
                $userHasLiked = true;
            }

            // Get the new like count
            $newLikeCount = $this->likeModel->getLikesCount($likeableId, $likeableType);

            // Send JSON response back to the client
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'likes_count' => $newLikeCount,
                'user_has_liked' => $userHasLiked
            ]);
            exit();

        } else {
            $this->redirect('articles');
        }
    }
}
?>