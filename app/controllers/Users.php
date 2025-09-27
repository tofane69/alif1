<?php
class Users extends Controller {
    public function __construct(){
        $this->userModel = $this->model('User');
    }

    public function login(){
        // Check for POST
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Check CSRF Token
            if(!check_csrf_token()){
                // In a real app, you'd log this attempt.
                $this->redirect('users/login');
            }

            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'username_err' => '',
                'password_err' => '',
            ];

            // Validate Username
            if(empty($data['username'])){
                $data['username_err'] = 'لطفا نام کاربری را وارد کنید.';
            }

            // Validate Password
            if(empty($data['password'])){
                $data['password_err'] = 'لطفا رمز عبور را وارد کنید.';
            }

            // Make sure errors are empty before proceeding
            if(empty($data['username_err']) && empty($data['password_err'])){
                // Check if user is locked out
                if ($this->userModel->isUserLockedOut($data['username'])) {
                    $data['username_err'] = 'حساب کاربری شما به دلیل تلاش‌های ناموفق زیاد موقتا قفل شده است. لطفا بعدا تلاش کنید.';
                    $this->view('auth/login', $data);
                    return; // Stop execution
                }

                // Check and set logged in user
                $loggedInUser = $this->userModel->login($data['username'], $data['password']);

                if($loggedInUser){
                    // Login successful, clear attempts and create session
                    $this->userModel->clearLoginAttempts($data['username']);
                    $this->createUserSession($loggedInUser);
                } else {
                    // Login failed
                    // Check if user exists before recording failed attempt
                    if($this->userModel->findUserByUsername($data['username'])){
                        $this->userModel->recordFailedLoginAttempt($data['username']);
                    }
                    $data['password_err'] = 'نام کاربری یا رمز عبور اشتباه است.';
                    $this->view('auth/login', $data);
                }
            } else {
                // Load view with validation errors
                $this->view('auth/login', $data);
            }

        } else {
            // Init data
            $data = [
                'username' => '',
                'password' => '',
                'username_err' => '',
                'password_err' => '',
            ];
            // Load view
            $this->view('auth/login', $data);
        }
    }

    public function createUserSession($user){
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_username'] = $user->username;
        $_SESSION['user_display_name'] = $user->display_name;
        $_SESSION['user_role_id'] = $user->role_id;
        // TODO: Redirect to dashboard
        $this->redirect('dashboard');
    }

    public function logout(){
        unset($_SESSION['user_id']);
        unset($_SESSION['user_username']);
        unset($_SESSION['user_display_name']);
        unset($_SESSION['user_role_id']);
        session_destroy();
        $this->redirect('users/login');
    }
}
?>