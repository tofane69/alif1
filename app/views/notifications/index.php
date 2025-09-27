<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1><?php echo $data['title']; ?></h1>
    <form action="<?php echo URL_ROOT; ?>/notifications/markAllRead" method="post">
        <button type="submit" class="btn btn-primary">علامت‌گذاری همه به عنوان خوانده‌شده</button>
    </form>
</div>

<div class="list-group">
    <?php if (empty($data['notifications'])): ?>
        <p class="text-center">شما هیچ اعلانی ندارید.</p>
    <?php else: ?>
        <?php foreach($data['notifications'] as $notification): ?>
            <a href="<?php echo URL_ROOT . '/' . $notification->link; ?>" class="list-group-item list-group-item-action <?php echo ($notification->is_read == 0) ? 'list-group-item-light fw-bold' : ''; ?>">
                <div class="d-flex w-100 justify-content-between">
                    <p class="mb-1"><?php echo htmlspecialchars($notification->message); ?></p>
                    <small><?php echo date('Y/m/d H:i', strtotime($notification->created_at)); ?></small>
                </div>
                <?php if($notification->is_read == 0): ?>
                    <small class="text-primary">جدید</small>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require APP_ROOT . '/views/layouts/footer.php'; ?>