<?php
class Articles extends Controller {

    public function __construct(){
        if(!isLoggedIn()){
            $this->redirect('users/login');
        }

        // Only allow users with content management roles to create/edit/delete
        // Reading can be done by employees, but we'll handle that logic within methods.
        // For now, let's restrict the whole controller for simplicity of setup.
        $this->articleModel = $this->model('Article');
        $this->categoryModel = $this->model('Category'); // We'll need this for forms
        $this->commentModel = $this->model('Comment'); // We'll need this for the show page
    }

    // List all articles
    public function index(){
        // Access control: Anyone logged in can see the list.
        $articles = $this->articleModel->getArticles();

        $data = [
            'title' => 'لیست مطالب',
            'articles' => $articles
        ];

        $this->view('articles/index', $data);
    }

    // Add a new article
    public function add(){
        // Check for role
        if(!hasRole([ROLE_ADMIN, ROLE_QUALITY_ASSURANCE, ROLE_SUPERVISOR])){
            $this->redirect('articles');
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Check CSRF Token
            if(!check_csrf_token()){
                $this->redirect('articles');
            }

            // Sanitize POST array
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'title' => trim($_POST['title']),
                'content' => $_POST['content'], // Summernote content is HTML, needs careful handling
                'category_id' => (int)$_POST['category_id'],
                'user_id' => $_SESSION['user_id'],
                'is_pinned' => isset($_POST['is_pinned']) ? 1 : 0,
                'tags' => trim($_POST['tags']), // Comma-separated tags
                'title_err' => '',
                'content_err' => '',
                'category_err' => ''
            ];

            // Validate data
            if(empty($data['title'])){
                $data['title_err'] = 'لطفا عنوان مطلب را وارد کنید.';
            }
            if(empty($data['content'])){
                $data['content_err'] = 'محتوای مطلب نمی‌تواند خالی باشد.';
            }
            if(empty($data['category_id'])){
                $data['category_err'] = 'لطفا یک دسته‌بندی انتخاب کنید.';
            }

            // Make sure no errors
            if(empty($data['title_err']) && empty($data['content_err']) && empty($data['category_err'])){
                // Validated
                if($this->articleModel->addArticle($data)){
                    flash('global_message', 'مطلب جدید با موفقیت ایجاد شد.');
                    $this->redirect('articles');
                } else {
                    die('Something went wrong');
                }
            } else {
                // Load view with errors
                $data['categories'] = $this->categoryModel->getAllCategories();
                $this->view('articles/add', $data);
            }

        } else {
            $data = [
                'title' => '',
                'content' => '',
                'category_id' => '',
                'is_pinned' => 0,
                'tags' => '',
                'categories' => $this->categoryModel->getAllCategories(),
                'title_err' => '',
                'content_err' => '',
                'category_err' => ''
            ];

            $this->view('articles/add', $data);
        }
    }

    // Show single article
    public function show($id){
        $cacheKey = 'article_' . $id;
        $cachedData = Cache::get($cacheKey);

        if($cachedData){
            // Use cached data
            $data = $cachedData;
        } else {
            // Fetch from DB
            $article = $this->articleModel->getArticleById($id);

            if(!$article){
                $this->redirect('articles');
            }

            $comments = $this->commentModel->getCommentsByArticleId($id, $_SESSION['user_id']);

            $data = [
                'article' => $article,
                'comments' => $comments
            ];

            // Store in cache for 1 hour
            Cache::set($cacheKey, $data, 3600);
        }

        $this->view('articles/show', $data);
    }

    // Edit an article
    public function edit($id){
        // Get existing article from model
        $article = $this->articleModel->getArticleById($id);

        // Check for owner/role
        if(!hasRole([ROLE_ADMIN, ROLE_QUALITY_ASSURANCE, ROLE_SUPERVISOR])){
             $this->redirect('articles');
        }
        // A more granular check could be if($article->user_id != $_SESSION['user_id'] && !hasRole... for allowing authors to edit their own posts

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Check CSRF Token
            if(!check_csrf_token()){
                $this->redirect('articles');
            }

            // Sanitize POST array
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'id' => $id,
                'title' => trim($_POST['title']),
                'content' => $_POST['content'],
                'category_id' => (int)$_POST['category_id'],
                'user_id' => $_SESSION['user_id'], // The user who is editing
                'is_pinned' => isset($_POST['is_pinned']) ? 1 : 0,
                'tags' => trim($_POST['tags']),
                'title_err' => '',
                'content_err' => '',
                'category_err' => ''
            ];

            // Validate data
            if(empty($data['title'])){
                $data['title_err'] = 'لطفا عنوان مطلب را وارد کنید.';
            }
            if(empty($data['content'])){
                $data['content_err'] = 'محتوای مطلب نمی‌تواند خالی باشد.';
            }
            if(empty($data['category_id'])){
                $data['category_err'] = 'لطفا یک دسته‌بندی انتخاب کنید.';
            }

            // Make sure no errors
            if(empty($data['title_err']) && empty($data['content_err']) && empty($data['category_err'])){
                if($this->articleModel->updateArticle($data)){
                    // Invalidate cache for this article
                    Cache::delete('article_' . $id);
                    flash('global_message', 'مطلب با موفقیت ویرایش شد.');
                    $this->redirect('articles/show/' . $id);
                } else {
                    die('Something went wrong');
                }
            } else {
                // Load view with errors
                $data['categories'] = $this->categoryModel->getAllCategories();
                $this->view('articles/edit', $data);
            }

        } else {
            if(!$article){
                $this->redirect('articles');
            }

            // Convert tags object array to a comma-separated string
            $tagNames = array_map(function($tag){ return $tag->name; }, $article->tags);
            $tagsString = implode(', ', $tagNames);

            $data = [
                'id' => $id,
                'title' => $article->title,
                'content' => $article->content,
                'category_id' => $article->category_id,
                'is_pinned' => $article->is_pinned,
                'tags' => $tagsString,
                'categories' => $this->categoryModel->getAllCategories(),
                'title_err' => '',
                'content_err' => '',
                'category_err' => ''
            ];

            $this->view('articles/edit', $data);
        }
    }

    // Delete an article
    public function delete($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Check CSRF Token
            if(!check_csrf_token()){
                $this->redirect('articles');
            }

            // Check for role
            if(!hasRole([ROLE_ADMIN, ROLE_QUALITY_ASSURANCE, ROLE_SUPERVISOR])){
                $this->redirect('articles');
            }

            // Get existing article to be safe
            $article = $this->articleModel->getArticleById($id);
            if(!$article){
                 $this->redirect('articles');
            }

            if($this->articleModel->deleteArticle($id)){
                // Invalidate cache for this article
                Cache::delete('article_' . $id);
                flash('global_message', 'مطلب با موفقیت حذف شد.');
                $this->redirect('articles');
            } else {
                die('Something went wrong');
            }
        } else {
            $this->redirect('articles');
        }
    }
}
?>