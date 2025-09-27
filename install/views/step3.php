<p>اطلاعات اتصال به دیتابیس با موفقیت تأیید شد. اکنون، لطفا اطلاعات مربوط به سایت و حساب کاربری ادمین اصلی را وارد کنید.</p>

<?php
if (isset($_SESSION['install_error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['install_error']) . '</div>';
    unset($_SESSION['install_error']);
}
?>

<form method="post" action="index.php?step=3">
    <fieldset>
        <legend>اطلاعات سایت</legend>
        <div class="mb-3">
            <label for="site_name" class="form-label">نام سایت</label>
            <input type="text" class="form-control" id="site_name" name="site_name" value="پایگاه دانش" required>
        </div>
        <div class="mb-3">
            <label for="url_root" class="form-label">آدرس ریشه سایت (URL Root)</label>
            <?php
                // Auto-detect a reasonable default for URL_ROOT
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
                $host = $_SERVER['HTTP_HOST'];
                $script_path = str_replace('/install/index.php', '', $_SERVER['SCRIPT_NAME']);
                $default_url_root = rtrim("$protocol://$host$script_path", '/');
            ?>
            <input type="text" class="form-control" id="url_root" name="url_root" value="<?php echo htmlspecialchars($default_url_root); ?>" required>
            <div class="form-text">این آدرس باید به ریشه اصلی پروژه (نه پوشه public) اشاره کند.</div>
        </div>
    </fieldset>

    <hr class="my-4">

    <fieldset>
        <legend>حساب کاربری ادمین</legend>
        <div class="mb-3">
            <label for="admin_username" class="form-label">نام کاربری ادمین</label>
            <input type="text" class="form-control" id="admin_username" name="admin_username" value="admin" required>
        </div>
        <div class="mb-3">
            <label for="admin_display_name" class="form-label">نام نمایشی ادمین</label>
            <input type="text" class="form-control" id="admin_display_name" name="admin_display_name" value="مدیر سیستم" required>
        </div>
        <div class="mb-3">
            <label for="admin_password" class="form-label">رمز عبور ادمین</label>
            <input type="password" class="form-control" id="admin_password" name="admin_password" required>
        </div>
    </fieldset>

    <div class="mt-4 text-center">
        <button type="submit" name="submit_setup" class="btn btn-primary btn-next">نصب و راه‌اندازی</button>
    </div>
</form>