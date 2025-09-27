<?php
// Simple helper functions

// Check if user is logged in
function isLoggedIn(){
    if(isset($_SESSION['user_id'])){
        return true;
    } else {
        return false;
    }
}

// Check user role
// Accepts a single role or an array of roles
function hasRole($roles) {
    if (!isLoggedIn()) {
        return false;
    }

    $userRoleId = $_SESSION['user_role_id'];

    if (is_array($roles)) {
        return in_array($userRoleId, $roles);
    } else {
        return $userRoleId == $roles;
    }
}

// Flash message helper
// EXAMPLE - flash('register_success', 'You are now registered', 'alert alert-danger');
// DISPLAY IN VIEW - <?php echo flash('register_success');
function flash($name = '', $message = '', $class = 'alert alert-success'){
  if(!empty($name)){
    if(!empty($message) && empty($_SESSION[$name])){
      if(!empty($_SESSION[$name])){
        unset($_SESSION[$name]);
      }

      if(!empty($_SESSION[$name. '_class'])){
        unset($_SESSION[$name. '_class']);
      }

      $_SESSION[$name] = $message;
      $_SESSION[$name. '_class'] = $class;
    } elseif(empty($message) && !empty($_SESSION[$name])){
      $class = !empty($_SESSION[$name. '_class']) ? $_SESSION[$name. '_class'] : '';
      echo '<div class="'.$class.'" id="msg-flash">'.$_SESSION[$name].'</div>';
      unset($_SESSION[$name]);
      unset($_SESSION[$name. '_class']);
    }
  }
}
?>