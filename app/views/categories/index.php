<?php
require APP_ROOT . '/views/layouts/header.php';

// Helper function to display categories as a nested list
function displayCategories($categories, $parentId = null) {
    echo '<ul class="list-group">';
    foreach ($categories as $category) {
        if ($category->parent_id == $parentId) {
            echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
            echo '<span>' . htmlspecialchars($category->name) . '</span>';
            echo '<div>';
            echo '<a href="' . URL_ROOT . '/categories/edit/' . $category->id . '" class="btn btn-sm btn-outline-primary me-2">ویرایش</a>';
            echo '<form class="d-inline" action="' . URL_ROOT . '/categories/delete/' . $category->id . '" method="post" onsubmit="return confirm(\'آیا از حذف این دسته‌بندی مطمئن هستید؟\');">';
            echo csrf_input();
            echo '<button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>';
            echo '</form>';
            echo '</div>';
            echo '</li>';

            // Find and display children
            $children = array_filter($categories, function($c) use ($category) {
                return $c->parent_id == $category->id;
            });

            if (!empty($children)) {
                echo '<li class="list-group-item" style="border: none; padding-left: 40px;">';
                displayCategories($categories, $category->id);
                echo '</li>';
            }
        }
    }
    echo '</ul>';
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1><?php echo $data['title']; ?></h1>
    <a href="<?php echo URL_ROOT; ?>/categories/add" class="btn btn-success">
        <i class="fa fa-plus"></i> افزودن دسته‌بندی جدید
    </a>
</div>

<?php if (empty($data['categories'])): ?>
    <p class="text-center">هیچ دسته‌بندی‌ای یافت نشد.</p>
<?php else: ?>
    <?php displayCategories($data['categories']); ?>
<?php endif; ?>


<?php require APP_ROOT . '/views/layouts/footer.php'; ?>