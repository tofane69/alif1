<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<div class="p-5 mb-4 bg-light rounded-3">
    <div class="container-fluid py-5">
        <h1 class="display-5 fw-bold"><?php echo $data['title']; ?></h1>
        <p class="col-md-8 fs-4"><?php echo $data['description']; ?></p>
        <p>کاربر گرامی، <?php echo $_SESSION['user_display_name']; ?> خوش آمدید!</p>
    </div>
</div>

<!-- TODO: Add dashboard widgets and reports here -->

<?php require APP_ROOT . '/views/layouts/footer.php'; ?>