<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نصب پایگاه دانش - مرحله <?php echo $step; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h1 class="text-center">نصب پایگاه دانش</h1>
            </div>
            <div class="card-body">
                <h3 class="card-title text-center mb-4"><?php echo $page_title; ?></h3>
                <div class="progress mb-4">
                    <?php
                    $progress = ($step / 4) * 100;
                    echo '<div class="progress-bar" role="progressbar" style="width: ' . $progress . '%" aria-valuenow="' . $progress . '" aria-valuemin="0" aria-valuemax="100">مرحله ' . $step . ' از 4</div>';
                    ?>
                </div>