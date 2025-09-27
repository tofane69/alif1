<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1><?php echo $data['title']; ?></h1>
    <!-- TODO: Add "New User" button for authorized roles -->
</div>

<p>در این بخش، کاربران سیستم مدیریت خواهند شد. این قابلیت به زودی پیاده‌سازی می‌شود.</p>

<!-- Placeholder for users table -->
<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>نام نمایشی</th>
            <th>نام کاربری</th>
            <th>نقش</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        <!-- TODO: Loop through users and display them -->
        <tr>
            <td colspan="5" class="text-center">هیچ کاربری برای نمایش وجود ندارد.</td>
        </tr>
    </tbody>
</table>


<?php require APP_ROOT . '/views/layouts/footer.php'; ?>