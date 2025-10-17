<?php
include '../includes/header.php';
include '../config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    if ($name === '') {
        $error = "نام دسته‌بندی نمی‌تواند خالی باشد.";
    } else {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            $success = "دسته‌بندی با موفقیت اضافه شد.";
        } else {
            $error = "خطا در افزودن دسته‌بندی.";
        }
        $stmt->close();
    }
}
?>
<main>
<div class="container pt-4">
    <h3>افزودن دسته‌بندی جدید</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label for="name" class="form-label">نام دسته‌بندی</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">ثبت</button>
        <a href="categories_list.php" class="btn btn-secondary">بازگشت</a>
    </form>
</div>
</main>
<?php include '../includes/footer.php'; ?>
