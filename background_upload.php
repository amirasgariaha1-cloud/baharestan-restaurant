<?php
include '../config.php';
include '../includes/header.php';

$msg = '';
$error = '';

$upload_dir = '../assets/bg/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['bg_image']) && $_FILES['bg_image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($_FILES['bg_image']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $error = "فرمت فایل باید JPG یا PNG باشد.";
        } elseif ($_FILES['bg_image']['size'] > 3 * 1024 * 1024) {
            $error = "اندازه فایل نباید بیشتر از ۳ مگابایت باشد.";
        } else {
            $new_name = 'hero-bg.' . $ext;

            // حذف فایل قبلی
            foreach (glob($upload_dir . 'hero-bg.*') as $old) {
                unlink($old);
            }

            if (move_uploaded_file($_FILES['bg_image']['tmp_name'], $upload_dir . $new_name)) {
                $msg = "بک‌گراند با موفقیت به‌روزرسانی شد.";
            } else {
                $error = "خطا در آپلود فایل.";
            }
        }
    } else {
        $error = "لطفاً یک فایل انتخاب کنید.";
    }
}
?>
<main>
<div class="container pt-4">
    <h4 class="mb-4">🎨 تغییر تصویر بک‌گراند صفحه اصلی</h4>

    <?php if ($msg): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title mb-3">بارگذاری تصویر جدید</h5>
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <input type="file" name="bg_image" class="form-control" required accept="image/*" onchange="updateFileName(this)">
                        </div>
                        <button type="submit" class="btn btn-primary">آپلود و جایگزینی</button>
                        <a href="dashboard.php" class="btn btn-secondary">بازگشت</a>
                    </form>
                    <small class="text-muted mt-2 d-block">حداکثر ۳ مگابایت - فرمت: JPG یا PNG</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 text-center">
            <h5 class="mb-3">پیش‌نمایش تصویر فعلی</h5>
            <?php
                $current_bg = glob($upload_dir . 'hero-bg.*');
                if ($current_bg && file_exists($current_bg[0])):
            ?>
                <img src="<?= str_replace('../', '../', $current_bg[0]) ?>" class="img-fluid rounded shadow" style="max-height: 300px;" alt="بک‌گراند فعلی">
            <?php else: ?>
                <div class="alert alert-warning">هیچ تصویر فعالی وجود ندارد.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
</main>
<script>
function updateFileName(input) {
    const label = input.files[0]?.name;
    if (label) {
        input.nextElementSibling?.textContent = label;
    }
}
</script>

<?php include '../includes/footer.php'; ?>
