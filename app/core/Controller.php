<?php
/*
 * Base Controller
 * Loads the models and views
 */
class Controller {
    // Load model
    public function model($model){
        // Require model file
        $modelPath = '../app/models/' . $model . '.php';
        if(file_exists($modelPath)){
            require_once $modelPath;
            // Instantiate model
            return new $model();
        } else {
            die('Model does not exist: ' . $model);
        }
    }

    // Load view
    public function view($view, $data = []){
        // Check for view file
        $viewPath = '../app/views/' . $view . '.php';
        if(file_exists($viewPath)){
            // Extract data to be available in the view
            extract($data);

            require_once $viewPath;
        } else {
            // View does not exist
            die('View does not exist: ' . $view);
        }
    }

    // Redirect helper
    protected function redirect($url){
        header('Location: ' . URL_ROOT . '/' . $url);
        exit();
    }
}
?>