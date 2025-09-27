<?php
class Comments extends Controller {

    public function __construct(){
        if(!isLoggedIn()){
            $this->redirect('users/login');
        }

        $this->commentModel = $this->model('Comment');
        $this->articleModel = $this->model('Article'); // To check if article exists
        $this->userModel = $this->model('User'); // For mentions
        $this->notificationModel = $this->model('Notification'); // For mentions
    }

    // Add a comment to an article
    public function add($article_id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if(!check_csrf_token()){
                $this->redirect('articles/show/' . $article_id);
            }

            // Check if article exists
            if(!$this->articleModel->getArticleById($article_id)){
                $this->redirect('articles'); // Redirect if article is not found
            }

            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'article_id' => $article_id,
                'user_id' => $_SESSION['user_id'],
                'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
                'comment_text' => trim($_POST['comment_text']),
                'voice_note_path' => null,
                'comment_err' => ''
            ];

            // Validate comment text or voice note
            if(empty($data['comment_text']) && (!isset($_FILES['voice_note']) || $_FILES['voice_note']['error'] != UPLOAD_ERR_OK)){
                $data['comment_err'] = 'نظر شما نمی‌تواند خالی باشد. لطفا متنی بنویسید یا یک فایل صوتی ضبط کنید.';
                // Redirect with error message (using session flash is ideal)
                $this->redirect('articles/show/' . $article_id);
            }

            // Handle file upload
            if(isset($_FILES['voice_note']) && $_FILES['voice_note']['error'] == UPLOAD_ERR_OK){
                $uploadDir = UPLOAD_PATH . '/voice_notes/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileName = uniqid('voice_') . '.wav';
                $targetPath = $uploadDir . $fileName;

                // Validate file
                $fileType = mime_content_type($_FILES['voice_note']['tmp_name']);
                if ($_FILES['voice_note']['size'] > MAX_FILE_SIZE || !in_array($fileType, ['audio/wav', 'audio/x-wav'])) {
                    $data['comment_err'] = 'فایل صوتی نامعتبر است یا حجم آن بیش از حد مجاز است.';
                     $this->redirect('articles/show/' . $article_id);
                }

                if(move_uploaded_file($_FILES['voice_note']['tmp_name'], $targetPath)){
                    $data['voice_note_path'] = 'voice_notes/' . $fileName;
                } else {
                    $data['comment_err'] = 'خطا در آپلود فایل صوتی.';
                    $this->redirect('articles/show/' . $article_id);
                }
            }


            if(empty($data['comment_err'])){
                $newCommentId = $this->commentModel->addComment($data);
                if($newCommentId){
                    // Handle mentions
                    if(!empty($data['comment_text'])){
                        preg_match_all('/@([a-zA-Z0-9_]+)/', $data['comment_text'], $matches);
                        if(!empty($matches[1])){
                            $mentionedUsernames = array_unique($matches[1]);
                            foreach($mentionedUsernames as $username){
                                $mentionedUser = $this->userModel->findUserByUsername($username);
                                if($mentionedUser && $mentionedUser->id != $_SESSION['user_id']){
                                    $notificationData = [
                                        'user_id' => $mentionedUser->id,
                                        'type' => 'mention',
                                        'message' => 'کاربر ' . $_SESSION['user_display_name'] . ' در یک نظر از شما نام برد.',
                                        'link' => 'articles/show/' . $article_id . '#comment-' . $newCommentId
                                    ];
                                    $this->notificationModel->create($notificationData);
                                }
                            }
                        }
                    }
                    // Invalidate article cache since a new comment was added
                    Cache::delete('article_' . $article_id);
                    flash('global_message', 'نظر شما با موفقیت ثبت شد.');
                    $this->redirect('articles/show/' . $article_id);
                } else {
                    die('Something went wrong');
                }
            } else {
                 $this->redirect('articles/show/' . $article_id);
            }
        } else {
            $this->redirect('articles');
        }
    }

    // Delete a comment
    public function delete($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $comment = $this->commentModel->getCommentById($id);

            // Check if comment exists before checking token to get article_id for redirect
            if(!$comment){
                $this->redirect('articles');
            }

            // Check CSRF Token
            if(!check_csrf_token()){
                $this->redirect('articles/show/' . $comment->article_id);
            }

            // Check for authorization
            $isOwner = $comment->user_id == $_SESSION['user_id'];
            $hasDeletePermission = hasRole([ROLE_ADMIN, ROLE_QUALITY_ASSURANCE, ROLE_SUPERVISOR]);

            if($isOwner || $hasDeletePermission){
                if($this->commentModel->deleteComment($id)){
                    // Invalidate cache for the article
                    Cache::delete('article_' . $comment->article_id);
                    flash('global_message', 'نظر با موفقیت حذف شد.');
                    $this->redirect('articles/show/' . $comment->article_id);
                } else {
                    die('Something went wrong');
                }
            } else {
                // Not authorized, redirect
                $this->redirect('articles/show/' . $comment->article_id);
            }
        } else {
            $this->redirect('articles');
        }
    }
}
?>