<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2>افزودن مطلب جدید</h2>
            <p>لطفا فرم زیر را برای ایجاد یک مطلب جدید تکمیل کنید.</p>
            <form action="<?php echo URL_ROOT; ?>/articles/add" method="post">
                <?php echo csrf_input(); ?>
                <div class="form-group mb-3">
                    <label for="title">عنوان: <sup>*</sup></label>
                    <input type="text" name="title" class="form-control <?php echo (!empty($data['title_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['title']; ?>">
                    <span class="invalid-feedback"><?php echo $data['title_err']; ?></span>
                </div>

                <div class="form-group mb-3">
                    <label for="category_id">دسته‌بندی: <sup>*</sup></label>
                    <select name="category_id" class="form-control <?php echo (!empty($data['category_err'])) ? 'is-invalid' : ''; ?>">
                        <option value="">یک دسته‌بندی را انتخاب کنید</option>
                        <?php foreach($data['categories'] as $category): ?>
                            <option value="<?php echo $category->id; ?>" <?php echo ($data['category_id'] == $category->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="invalid-feedback"><?php echo $data['category_err']; ?></span>
                </div>

                <div class="form-group mb-3">
                    <label for="content">محتوا: <sup>*</sup></label>
                    <textarea id="summernote" name="content" class="form-control <?php echo (!empty($data['content_err'])) ? 'is-invalid' : ''; ?>"><?php echo $data['content']; ?></textarea>
                    <span class="invalid-feedback d-block"><?php echo $data['content_err']; ?></span>
                </div>

                <div class="form-group mb-3">
                    <label for="tags">برچسب‌ها (با کاما جدا کنید):</label>
                    <input type="text" name="tags" class="form-control" value="<?php echo $data['tags']; ?>">
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_pinned" value="1" id="is_pinned" <?php echo ($data['is_pinned']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="is_pinned">
                        پین کردن این مطلب (نمایش در بالای لیست)
                    </label>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">ایجاد مطلب</button>
                    <a href="<?php echo URL_ROOT; ?>/articles" class="btn btn-secondary">انصراف</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/views/layouts/footer.php'; ?>

<!-- Add script to initialize Summernote -->
<script>
    $(document).ready(function() {
        $('#summernote').summernote({
            placeholder: 'محتوای خود را اینجا بنویسید...',
            tabsize: 2,
            height: 300,
            lang: 'fa-IR', // Optional: if you have the Farsi language file
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });
</script>