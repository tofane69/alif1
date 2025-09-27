<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1><?php echo $data['title']; ?></h1>
    <?php if(hasRole([ROLE_ADMIN, ROLE_QUALITY_ASSURANCE, ROLE_SUPERVISOR])): ?>
        <a href="<?php echo URL_ROOT; ?>/articles/add" class="btn btn-success">
            <i class="fa fa-plus"></i> افزودن مطلب جدید
        </a>
    <?php endif; ?>
</div>

<?php if (empty($data['articles'])): ?>
    <div class="alert alert-warning text-center">هیچ مطلبی یافت نشد.</div>
<?php else: ?>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>عنوان</th>
                <th>نویسنده</th>
                <th>دسته‌بندی</th>
                <th>تاریخ ایجاد</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data['articles'] as $article): ?>
                <tr>
                    <td>
                        <?php if($article->is_pinned): ?>
                            <i class="fa fa-thumb-tack text-primary" title="پین شده"></i>
                        <?php endif; ?>
                        <?php echo htmlspecialchars($article->title); ?>
                    </td>
                    <td><?php echo htmlspecialchars($article->author); ?></td>
                    <td><?php echo htmlspecialchars($article->category_name); ?></td>
                    <td><?php echo date('Y/m/d', strtotime($article->created_at)); ?></td>
                    <td>
                        <a href="<?php echo URL_ROOT; ?>/articles/show/<?php echo $article->id; ?>" class="btn btn-sm btn-outline-info">مشاهده</a>
                        <?php if(hasRole([ROLE_ADMIN, ROLE_QUALITY_ASSURANCE, ROLE_SUPERVISOR])): ?>
                            <a href="<?php echo URL_ROOT; ?>/articles/edit/<?php echo $article->id; ?>" class="btn btn-sm btn-outline-primary">ویرایش</a>
                            <!-- Delete button will be a form -->
                            <form class="d-inline" action="<?php echo URL_ROOT; ?>/articles/delete/<?php echo $article->id; ?>" method="post" onsubmit="return confirm('آیا از حذف این مطلب مطمئن هستید؟');">
                                <?php echo csrf_input(); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require APP_ROOT . '/views/layouts/footer.php'; ?>