<?php
include '../includes/header.php';
include '../config.php';

$id = $_GET['id'] ?? null;
$error = '';
$success = '';

if (!$id) {
    header("Location: categories_list.php");
    exit;
}

// دریافت اطلاعات قبلی
$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();
$stmt->close();

if (!$category) {
    die("دسته‌بندی پیدا نشد.");
}

// ویرایش
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    if ($name === '') {
        $error = "نام دسته‌بندی نمی‌تواند خالی باشد.";
    } else {
        $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
        if ($stmt->execute()) {
            $success = "تغییرات ذخیره شد.";
            $category['name'] = $name;
        } else {
            $error = "خطا در ذخیره تغییرات.";
        }
        $stmt->close();
    }
}
?>
<main>
<div class="container pt-4">
    <h3>ویرایش دسته‌بندی</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label for="name" class="form-label">نام دسته‌بندی</label>
            <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($category['name']) ?>">
        </div>
        <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
        <a href="categories_list.php" class="btn btn-secondary">بازگشت</a>
    </form>
</div>
</main>
<?php include '../includes/footer.php'; ?>
