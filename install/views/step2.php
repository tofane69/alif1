<p>در این مرحله، لطفا اطلاعات اتصال به دیتابیس MySQL خود را وارد کنید. اگر از قبل دیتابیس نساخته‌اید، لطفا ابتدا یک دیتابیس خالی ایجاد کنید.</p>

<?php
// Display errors if they exist from a failed attempt
if (isset($_SESSION['install_error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['install_error']) . '</div>';
    unset($_SESSION['install_error']);
}
?>

<form method="post" action="index.php?step=2">
    <div class="mb-3">
        <label for="db_host" class="form-label">نام هاست (Database Host)</label>
        <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
        <div class="form-text">معمولاً `localhost` است.</div>
    </div>
    <div class="mb-3">
        <label for="db_name" class="form-label">نام دیتابیس (Database Name)</label>
        <input type="text" class="form-control" id="db_name" name="db_name" required>
    </div>
    <div class="mb-3">
        <label for="db_user" class="form-label">نام کاربری دیتابیس (Database User)</label>
        <input type="text" class="form-control" id="db_user" name="db_user" required>
    </div>
    <div class="mb-3">
        <label for="db_pass" class="form-label">رمز عبور دیتابیس (Database Password)</label>
        <input type="password" class="form-control" id="db_pass" name="db_pass">
    </div>

    <div class="mt-4 text-center">
        <button type="submit" name="submit_db" class="btn btn-primary btn-next">بررسی و ادامه</button>
    </div>
</form>