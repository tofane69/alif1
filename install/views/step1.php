<?php
$requirements = check_requirements();
$all_met = all_requirements_met($requirements);
?>

<p>به نصب‌کننده پایگاه دانش خوش آمدید. این اسکریپت شما را در فرآیند نصب راهنمایی خواهد کرد.</p>
<p>در مرحله اول، پیش‌نیازهای سرور برای اجرای صحیح برنامه بررسی می‌شود.</p>

<h4 class="mt-4">بررسی پیش‌نیازها:</h4>
<ul class="list-group">
    <?php foreach ($requirements as $requirement): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><?php echo htmlspecialchars($requirement['name']); ?></span>
            <?php if ($requirement['status']): ?>
                <span class="badge bg-success">موفق</span>
            <?php else: ?>
                <span class="badge bg-danger">ناموفق</span>
            <?php endif; ?>
        </li>
        <?php if (!$requirement['status']): ?>
            <li class="list-group-item list-group-item-warning">
                <small><strong>راهنمایی:</strong> <?php echo htmlspecialchars($requirement['message']); ?></small>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>

<div class="mt-4 text-center">
    <?php if ($all_met): ?>
        <p class="text-success">تمام پیش‌نیازها با موفقیت برآورده شده‌اند. می‌توانید به مرحله بعد بروید.</p>
        <form method="post" action="index.php?step=1">
            <button type="submit" class="btn btn-primary btn-next">ادامه</button>
        </form>
    <?php else: ?>
        <div class="alert alert-danger">
            متاسفانه یک یا چند مورد از پیش‌نیازهای سرور شما برآورده نشده است. لطفا مشکلات مشخص شده را برطرف کرده و سپس صفحه را مجدداً بارگذاری کنید.
        </div>
        <button type="button" class="btn btn-primary btn-next" disabled>ادامه</button>
    <?php endif; ?>
</div>