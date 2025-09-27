<?php
// Simple Web Installer
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

define('INSTALL_ROOT', __DIR__);
define('PROJECT_ROOT', dirname(__DIR__));

require_once 'functions.php';

// Determine the current step
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

// Basic security: ensure steps are followed in order
if ($step > 1 && empty($_SESSION['install_step_' . ($step - 1)])) {
    // Trying to skip a step, redirect to step 1
    header('Location: index.php?step=1');
    exit;
}

// Handle POST requests to move to the next step
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $next_step = $step + 1;
    if ($step === 1 && all_requirements_met(check_requirements())) {
        $_SESSION['install_step_1'] = true;
        header('Location: index.php?step=' . $next_step);
        exit;
    }
    // TODO: Add handlers for other steps
    if ($step === 2 && isset($_POST['submit_db'])) {
        $db_host = trim($_POST['db_host']);
    } elseif ($step === 3 && isset($_POST['submit_setup'])) {
        // --- This is the main installation logic ---
        $db_details = $_SESSION['db_details'];
        $conn = new mysqli($db_details['host'], $db_details['user'], $db_details['pass'], $db_details['name']);

        if ($conn->connect_error) {
            $_SESSION['install_error'] = 'خطای اتصال مجدد به دیتابیس: ' . $conn->connect_error;
            header('Location: index.php?step=2'); // Go back to DB config
            exit;
        }

        try {
            // 1. Import the SQL file
            $sql_file = PROJECT_ROOT . '/kb.sql';
            if (!import_sql_file($conn, $sql_file)) {
                throw new Exception('خطا در درون‌ریزی فایل SQL: ' . $conn->error);
            }

            // 2. Prepare admin user data
            $admin_username = trim($_POST['admin_username']);
            $admin_display_name = trim($_POST['admin_display_name']);
            $admin_password = $_POST['admin_password'];

            if (empty($admin_username) || empty($admin_password)) {
                throw new Exception('نام کاربری و رمز عبور ادمین نمی‌تواند خالی باشد.');
            }

            $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

            // 3. Update the admin user record
            // The default admin user has id = 1
            $stmt = $conn->prepare("UPDATE users SET username = ?, display_name = ?, password = ? WHERE id = 1");
            $stmt->bind_param('sss', $admin_username, $admin_display_name, $hashed_password);

            if (!$stmt->execute()) {
                throw new Exception('خطا در به‌روزرسانی حساب ادمین: ' . $stmt->error);
            }
            $stmt->close();

            // 4. Store site config in session for the final step
            $_SESSION['site_config'] = [
                'site_name' => trim($_POST['site_name']),
                'url_root' => trim($_POST['url_root']),
            ];

            $_SESSION['install_step_3'] = true;
            $conn->close();
            header('Location: index.php?step=4');
            exit;

        } catch (Exception $e) {
            $_SESSION['install_error'] = 'فرآیند نصب با خطا مواجه شد: ' . $e->getMessage();
            header('Location: index.php?step=3');
            exit;
        }
    }
    if ($step === 2 && isset($_POST['submit_db'])) {
        $db_host = trim($_POST['db_host']);
        $db_name = trim($_POST['db_name']);
        $db_user = trim($_POST['db_user']);
        $db_pass = trim($_POST['db_pass']);

        // Try to connect to the database
        try {
            $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
            if ($conn->connect_error) {
                throw new Exception($conn->connect_error);
            }
            // Connection successful
            $_SESSION['db_details'] = [
                'host' => $db_host,
                'name' => $db_name,
                'user' => $db_user,
                'pass' => $db_pass,
            ];
            $_SESSION['install_step_2'] = true;
            $conn->close();
            header('Location: index.php?step=3');
            exit;
        } catch (Exception $e) {
            $_SESSION['install_error'] = 'اتصال به دیتابیس ناموفق بود. خطا: ' . $e->getMessage();
            header('Location: index.php?step=2');
            exit;
        }
    }
}


// Simple router for steps
switch ($step) {
    case 1:
        $page_title = 'خوش‌آمدید و بررسی پیش‌نیازها';
        include 'header.php';
        include 'views/step1.php';
        include 'footer.php';
        break;
    case 2:
        $page_title = 'پیکربندی دیتابیس';
        include 'header.php';
        include 'views/step2.php';
        include 'footer.php';
        break;
    case 3:
        $page_title = 'راه‌اندازی سایت و حساب ادمین';
        include 'header.php';
        include 'views/step3.php';
        include 'footer.php';
        break;
    case 4:
        $page_title = 'پایان نصب';
        // Create the config file
        if (!create_config_file($_SESSION['db_details'], $_SESSION['site_config'])) {
            $_SESSION['install_error'] = 'خطا در ایجاد فایل پیکربندی (config.php). لطفا اطمینان حاصل کنید که پوشه /config قابل نوشتن است.';
            header('Location: index.php?step=3');
            exit;
        }

        include 'header.php';
        include 'views/step4.php';
        include 'footer.php';
        // Clean up the session
        session_destroy();
        break;
    default:
        header('Location: index.php?step=1');
        exit;
}

?>