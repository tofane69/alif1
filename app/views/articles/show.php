<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo URL_ROOT; ?>/articles">مطالب</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($data['article']->title); ?></li>
    </ol>
</nav>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h1><?php echo htmlspecialchars($data['article']->title); ?></h1>
        <div>
            <?php if(hasRole([ROLE_ADMIN, ROLE_QUALITY_ASSURANCE, ROLE_SUPERVISOR])): ?>
                <a href="<?php echo URL_ROOT; ?>/articles/edit/<?php echo $data['article']->id; ?>" class="btn btn-primary">ویرایش</a>
                <form class="d-inline" action="<?php echo URL_ROOT; ?>/articles/delete/<?php echo $data['article']->id; ?>" method="post" onsubmit="return confirm('آیا از حذف این مطلب مطمئن هستید؟');">
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="text-muted mb-3">
            نوشته شده توسط: <strong><?php echo htmlspecialchars($data['article']->author); ?></strong>
            در تاریخ: <strong><?php echo date('Y/m/d', strtotime($data['article']->created_at)); ?></strong>
            در دسته‌بندی: <strong><?php echo htmlspecialchars($data['article']->category_name); ?></strong>
        </div>
        <hr>

        <div class="article-content">
            <?php echo $data['article']->content; // Content from Summernote is HTML ?>
        </div>

    </div>
    <div class="card-footer">
        <?php if(!empty($data['article']->tags)): ?>
            <strong>برچسب‌ها:</strong>
            <?php foreach($data['article']->tags as $tag): ?>
                <span class="badge bg-secondary"><?php echo htmlspecialchars($tag->name); ?></span>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Comments Section -->
<div class="mt-5">
    <h3>نظرات</h3>
    <hr>

    <!-- Add Comment Form (for top-level comments) -->
    <div class="card my-4">
        <h5 class="card-header">نظر خود را ثبت کنید:</h5>
        <div class="card-body">
            <form action="<?php echo URL_ROOT; ?>/comments/add/<?php echo $data['article']->id; ?>" method="post" enctype="multipart/form-data" class="comment-form">
                <?php echo csrf_input(); ?>
                <div class="form-group mb-2">
                    <textarea name="comment_text" class="form-control" rows="3" placeholder="نظر شما..."></textarea>
                </div>
                <div class="d-flex align-items-center">
                    <button type="submit" class="btn btn-success me-3">ارسال نظر</button>
                    <button type="button" class="btn btn-danger start-recording-btn"><i class="fa fa-microphone"></i> شروع ضبط</button>
                    <button type="button" class="btn btn-warning stop-recording-btn" style="display: none;"><i class="fa fa-stop"></i> توقف</button>
                    <audio controls class="audio-preview ms-2" style="display: none;"></audio>
                    <input type="file" name="voice_note" class="voice-note-input" style="display: none;">
                </div>
            </form>
        </div>
    </div>

    <!-- Display Threaded Comments -->
    <div class="comments-container mt-4">
        <?php
        // Helper function to display comments in a threaded manner
        function displayThreadedComments($comments, $articleId, $parentId = null, $level = 0) {
            $children = array_filter($comments, function($comment) use ($parentId) {
                return $comment->parent_id == $parentId;
            });

            if (empty($children) && $level == 0 && $parentId == null) {
                 echo '<p>هنوز هیچ نظری برای این مطلب ثبت نشده است.</p>';
                 return;
            }

            foreach ($children as $comment) {
                $marginLeft = $level * 35; // Indentation for replies
                echo '<div class="d-flex mb-4" style="margin-right: ' . $marginLeft . 'px;">';
                echo '    <div class="flex-shrink-0">';
                echo '        <img class="rounded-circle" src="https://via.placeholder.com/50" alt="profile">';
                echo '    </div>';
                echo '    <div class="ms-3 flex-grow-1">';
                echo '        <div class="fw-bold">' . htmlspecialchars($comment->author_name) . ' <small class="text-muted">(' . htmlspecialchars($comment->author_role) . ')</small></div>';
                if (!empty($comment->comment_text)) {
                    echo '    <div class="comment-text">' . nl2br(htmlspecialchars($comment->comment_text)) . '</div>';
                }
                if (!empty($comment->voice_note_path)) {
                    // IMPORTANT: This assumes a route will handle serving the file securely
                    // as the uploads folder is not directly accessible.
                    echo '    <audio controls src="' . URL_ROOT . '/uploads/' . htmlspecialchars($comment->voice_note_path) . '" class="mt-2"></audio>';
                }
                echo '        <div class="text-muted mt-1" style="font-size: 0.8rem;">';
                echo '            ' . date('Y/m/d H:i', strtotime($comment->created_at));
                echo '            <a href="#" class="ms-2 reply-btn" data-comment-id="' . $comment->id . '">پاسخ</a>';
                // Like Button
                echo '            <a href="#" class="ms-2 like-btn text-decoration-none" data-likeable-type="comment" data-likeable-id="' . $comment->id . '">';
                echo '                <i class="' . ($comment->user_has_liked ? 'fa-solid text-primary' : 'fa-regular') . ' fa-thumbs-up"></i>';
                echo '                <span class="likes-count ms-1">' . $comment->likes_count . '</span>';
                echo '            </a>';

                // Delete Button
                $canDelete = ($_SESSION['user_id'] == $comment->user_id) || hasRole([ROLE_ADMIN, ROLE_QUALITY_ASSURANCE, ROLE_SUPERVISOR]);
                if ($canDelete) {
                    echo '            <form class="d-inline" action="' . URL_ROOT . '/comments/delete/' . $comment->id . '" method="post" onsubmit="return confirm(\'آیا از حذف این نظر مطمئن هستید؟\');">';
                    echo                 csrf_input();
                    echo '                <button type="submit" class="btn btn-link btn-sm text-danger text-decoration-none p-0 ms-2">حذف</button>';
                    echo '            </form>';
                }

                echo '        </div>';

                // Reply Form (hidden by default)
                echo '        <div class="reply-form-container mt-2" id="reply-form-' . $comment->id . '" style="display: none;">';
                echo '            <form action="' . URL_ROOT . '/comments/add/' . $articleId . '" method="post" enctype="multipart/form-data" class="comment-form">';
                echo '                <input type="hidden" name="parent_id" value="' . $comment->id . '">';
                echo                 csrf_input();
                echo '                <div class="form-group mb-2">';
                echo '                    <textarea name="comment_text" class="form-control" rows="2" placeholder="پاسخ شما..."></textarea>';
                echo '                </div>';
                echo '                <div class="d-flex align-items-center">';
                echo '                    <button type="submit" class="btn btn-sm btn-success me-3">ارسال پاسخ</button>';
                echo '                    <button type="button" class="btn btn-sm btn-danger start-recording-btn"><i class="fa fa-microphone"></i></button>';
                echo '                    <button type="button" class="btn btn-sm btn-warning stop-recording-btn" style="display: none;"><i class="fa fa-stop"></i></button>';
                echo '                    <audio controls class="audio-preview ms-2" style="display: none; height: 30px;"></audio>';
                echo '                    <input type="file" name="voice_note" class="voice-note-input" style="display: none;">';
                echo '                </div>';
                echo '            </form>';
                echo '        </div>';
                echo '    </div>';
                echo '</div>';

                // Recursive call for children of the current comment
                displayThreadedComments($comments, $articleId, $comment->id, $level + 1);
            }
        }

        // Initial call to the function for top-level comments
        displayThreadedComments($data['comments'], $data['article']->id);
        ?>
    </div>
</div>


<?php require APP_ROOT . '/views/layouts/footer.php'; ?>