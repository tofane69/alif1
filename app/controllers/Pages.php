<?php
class Pages extends Controller {
    public function __construct(){
        // Models can be loaded here if needed
    }

    public function index(){
        // Since no content should be visible without login,
        // redirect to the login page.
        // We will create the Users controller and login method next.
        $this->redirect('users/login');
    }

    // You can add other static pages here like about, etc.
    // But for now, we'll keep it simple.
}
?>