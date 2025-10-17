<?php
include '../includes/header.php';

$error = '';
$success = '';

// خواندن دسته‌بندی‌ها برای نمایش در فرم
$categories = [];
$result = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name");
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $available = isset($_POST['available']) ? 1 : 0;

    // آپلود عکس
    $image = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $tmp_name = $_FILES['photo']['tmp_name'];
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('food_') . '.' . $ext;
        $target_path = $upload_dir . $filename;

        if (move_uploaded_file($tmp_name, $target_path)) {
            $image = $filename; // فقط نام فایل ذخیره می‌شود
        } else {
            $error = 'آپلود عکس انجام نشد.';
        }
    }

    if ($name === '' || $price <= 0 || $category_id <= 0) {
        $error = 'لطفاً نام، قیمت و دسته‌بندی را به درستی وارد کنید.';
    } elseif (!$error) {
        $stmt = mysqli_prepare($conn, "INSERT INTO menu_items (name, description, price, category_id, is_available, image) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssdiss", $name, $description, $price, $category_id, $available, $image);
            if (mysqli_stmt_execute($stmt)) {
                $success = 'غذای جدید با موفقیت اضافه شد.';
                $_POST = []; // پاک کردن فرم پس از موفقیت
            } else {
                $error = 'خطا در ذخیره اطلاعات: ' . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error = 'خطا در آماده‌سازی کوئری.';
        }
    }
}
?>
<?php include '../includes/header.php'; ?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <title>افزودن غذای جدید</title>
    <link href="../assets/bootstrap/css/bootstrap.rtl.min.css" rel="stylesheet" />
</head>
<body>

<h3>افزودن غذای جدید به منو</h3>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="mt-3">
    <div class="mb-3">
        <label for="name" class="form-label">نام غذا *</label>
        <input type="text" name="name" id="name" class="form-control" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" />
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">توضیحات</label>
        <textarea name="description" id="description" class="form-control"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
        <label for="price" class="form-label">قیمت (تومان) *</label>
        <input type="number" name="price" id="price" class="form-control" min="0" step="0.01" required value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" />
    </div>

    <div class="mb-3">
        <label for="category_id" class="form-label">دسته‌بندی *</label>
        <select name="category_id" id="category_id" class="form-select" required>
            <option value="">انتخاب کنید</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="photo" class="form-label">عکس غذا</label>
        <input type="file" name="photo" id="photo" class="form-control" accept="image/*" />
    </div>

    <div class="form-check mb-3">
        <input type="checkbox" name="available" id="available" class="form-check-input" <?= isset($_POST['available']) ? 'checked' : '' ?> />
        <label for="available" class="form-check-label">فعال باشد</label>
    </div>

    <button type="submit" class="btn btn-primary">افزودن غذا</button>
    <a href="menu_list.php" class="btn btn-secondary">بازگشت</a>
</form>

<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include '../includes/footer.php'; ?>