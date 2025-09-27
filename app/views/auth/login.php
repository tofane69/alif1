<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2>ورود به سیستم</h2>
            <p>لطفا اطلاعات کاربری خود را برای ورود وارد کنید.</p>
            <form action="<?php echo URL_ROOT; ?>/users/login" method="post">
                <?php echo csrf_input(); ?>
                <div class="form-group mb-3">
                    <label for="username">نام کاربری: <sup>*</sup></label>
                    <input type="text" name="username" class="form-control form-control-lg <?php echo (!empty($data['username_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['username']; ?>">
                    <span class="invalid-feedback"><?php echo $data['username_err']; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label for="password">رمز عبور: <sup>*</sup></label>
                    <input type="password" name="password" class="form-control form-control-lg <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['password']; ?>">
                    <span class="invalid-feedback"><?php echo $data['password_err']; ?></span>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="submit" value="ورود" class="btn btn-success btn-block">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/views/layouts/footer.php'; ?>