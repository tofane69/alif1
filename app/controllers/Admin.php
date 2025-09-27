<?php
class Admin extends Controller {

    public function __construct(){
        // First, ensure user is logged in
        if(!isLoggedIn()){
            $this->redirect('users/login');
        }

        // Now, check for the required roles
        $allowedRoles = [ROLE_ADMIN, ROLE_QUALITY_ASSURANCE, ROLE_SUPERVISOR];
        if(!hasRole($allowedRoles)){
            // Optionally, you could set a flash message here to show an error
            $this->redirect('dashboard'); // Redirect unauthorized users
        }

        // Load models that will be used by this controller
        $this->userModel = $this->model('User');
    }

    // Default method, maybe show an admin dashboard index
    public function index(){
        $this->users();
    }

    // Manage users
    public function users(){
        // TODO: Get users from model
        $users = []; // Placeholder

        $data = [
            'title' => 'مدیریت کاربران',
            'users' => $users
        ];

        $this->view('admin/users', $data);
    }
}
?>