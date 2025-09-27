<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2><?php echo $data['title']; ?></h2>
            <p>یک دسته‌بندی جدید برای مطالب ایجاد کنید.</p>
            <form action="<?php echo URL_ROOT; ?>/categories/add" method="post">
                <?php echo csrf_input(); ?>
                <div class="form-group mb-3">
                    <label for="name">نام دسته‌بندی: <sup>*</sup></label>
                    <input type="text" name="name" class="form-control <?php echo (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['name']; ?>">
                    <span class="invalid-feedback"><?php echo $data['name_err']; ?></span>
                </div>

                <div class="form-group mb-3">
                    <label for="parent_id">والد (اختیاری):</label>
                    <select name="parent_id" class="form-control">
                        <option value="">بدون والد (شاخه‌ی اصلی)</option>
                        <?php foreach($data['categories'] as $category): ?>
                            <option value="<?php echo $category->id; ?>">
                                <?php echo htmlspecialchars($category->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="description">توضیحات (اختیاری):</label>
                    <textarea name="description" class="form-control"><?php echo $data['description']; ?></textarea>
                </div>

                <div class="form-group mb-3">
                    <label for="sort_order">ترتیب نمایش:</label>
                    <input type="number" name="sort_order" class="form-control" value="<?php echo $data['sort_order']; ?>">
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">افزودن</button>
                    <a href="<?php echo URL_ROOT; ?>/categories" class="btn btn-secondary">انصراف</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/views/layouts/footer.php'; ?>