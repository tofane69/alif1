<?php
class Categories extends Controller {

    public function __construct(){
        if(!isLoggedIn()){
            $this->redirect('users/login');
        }

        // Only allow users with content management roles
        $allowedRoles = [ROLE_ADMIN, ROLE_QUALITY_ASSURANCE, ROLE_SUPERVISOR];
        if(!hasRole($allowedRoles)){
            $this->redirect('dashboard');
        }

        $this->categoryModel = $this->model('Category');
    }

    // List all categories
    public function index(){
        $categories = $this->categoryModel->getAllCategories();

        // We need a function to arrange categories into a tree structure
        // This will be implemented later. For now, we pass the flat list.
        $data = [
            'title' => 'مدیریت دسته‌بندی‌ها',
            'categories' => $categories
        ];

        $this->view('categories/index', $data);
    }

    // Add a new category
    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Check CSRF Token
            if(!check_csrf_token()){
                $this->redirect('categories');
            }

            // Sanitize POST array
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'name' => trim($_POST['name']),
                'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
                'description' => trim($_POST['description']),
                'sort_order' => (int)$_POST['sort_order'],
                'name_err' => ''
            ];

            // Validate data
            if(empty($data['name'])){
                $data['name_err'] = 'لطفا نام دسته‌بندی را وارد کنید.';
            }

            // Make sure no errors
            if(empty($data['name_err'])){
                // Validated
                if($this->categoryModel->addCategory($data)){
                    flash('global_message', 'دسته‌بندی جدید با موفقیت ایجاد شد.');
                    $this->redirect('categories');
                } else {
                    die('Something went wrong');
                }
            } else {
                // Load view with errors
                // Get categories for parent dropdown
                $data['categories'] = $this->categoryModel->getAllCategories();
                $this->view('categories/add', $data);
            }

        } else {
            $data = [
                'title' => 'افزودن دسته‌بندی جدید',
                'categories' => $this->categoryModel->getAllCategories(),
                'name' => '',
                'parent_id' => null,
                'description' => '',
                'sort_order' => 0,
                'name_err' => ''
            ];

            $this->view('categories/add', $data);
        }
    }

    // Edit a category
    public function edit($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Check CSRF Token
            if(!check_csrf_token()){
                $this->redirect('categories');
            }

            // Sanitize POST array
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'id' => $id,
                'name' => trim($_POST['name']),
                'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
                'description' => trim($_POST['description']),
                'sort_order' => (int)$_POST['sort_order'],
                'name_err' => ''
            ];

            // Validate data
            if(empty($data['name'])){
                $data['name_err'] = 'لطفا نام دسته‌بندی را وارد کنید.';
            }

            // Prevent category from being its own parent
            if ($data['parent_id'] == $data['id']) {
                // In a real app, you'd pass an error to the view
                die('یک دسته‌بندی نمی‌تواند والد خودش باشد.');
            }

            // Make sure no errors
            if(empty($data['name_err'])){
                if($this->categoryModel->updateCategory($data)){
                    flash('global_message', 'دسته‌بندی با موفقیت ویرایش شد.');
                    $this->redirect('categories');
                } else {
                    die('Something went wrong');
                }
            } else {
                // Load view with errors
                $data['categories'] = $this->categoryModel->getAllCategories();
                $this->view('categories/edit', $data);
            }

        } else {
            // Get existing category from model
            $category = $this->categoryModel->getCategoryById($id);

            // Check for owner (not really owner, but authorization is in constructor)
            // For now, just check if category exists
            if(!$category){
                $this->redirect('categories');
            }

            $data = [
                'title' => 'ویرایش دسته‌بندی',
                'id' => $id,
                'categories' => $this->categoryModel->getAllCategories(),
                'name' => $category->name,
                'parent_id' => $category->parent_id,
                'description' => $category->description,
                'sort_order' => $category->sort_order,
                'name_err' => ''
            ];

            $this->view('categories/edit', $data);
        }
    }

    // Delete a category
    public function delete($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Check CSRF Token
            if(!check_csrf_token()){
                $this->redirect('categories');
            }

            // Get existing category from model
            $category = $this->categoryModel->getCategoryById($id);

            if(!$category){
                $this->redirect('categories');
            }

            if($this->categoryModel->deleteCategory($id)){
                flash('global_message', 'دسته‌بندی با موفقیت حذف شد.');
                $this->redirect('categories');
            } else {
                die('Something went wrong');
            }
        } else {
            $this->redirect('categories');
        }
    }
}
?>