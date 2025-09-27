<div class="text-center">
    <h2 class="text-success">نصب با موفقیت انجام شد!</h2>
    <p>پایگاه دانش شما با موفقیت نصب و پیکربندی شد.</p>

    <div class="alert alert-danger mt-4">
        <h4><i class="fa fa-exclamation-triangle"></i> اقدام امنیتی مهم</h4>
        <p>برای تکمیل فرآیند و امن‌سازی سایت خود، **باید فوراً پوشه `install` را از روی هاست خود حذف کنید.**</p>
    </div>

    <div class="mt-4">
        <a href="<?php echo htmlspecialchars($_SESSION['site_config']['url_root'] ?? '../'); ?>/public" class="btn btn-primary btn-lg">ورود به پایگاه دانش</a>
    </div>
</div>