<?php
class Dashboard extends Controller {
    public function __construct(){
        if(!isLoggedIn()){
            $this->redirect('users/login');
        }
    }

    public function index(){
        $data = [
            'title' => 'داشبورد',
            'description' => 'به پایگاه دانش خوش آمدید.'
        ];

        $this->view('dashboard/index', $data);
    }
}
?>