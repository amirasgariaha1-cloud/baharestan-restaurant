<?php
include '../includes/header.php';
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: menu_list.php");
    exit;
}

$msg = '';
$error = '';

// دریافت اطلاعات قبلی غذا
$stmt = $conn->prepare("SELECT * FROM menu_items WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    $error = "غذا پیدا نشد.";
}

// دریافت دسته‌بندی‌ها
$categories = [];
$result = $conn->query("SELECT id, name FROM categories");
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

// ذخیره تغییرات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category_id = $_POST['category'];
    $price = $_POST['price'];
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    $image = $item['image'];
    if (!empty($_FILES['image']['name'])) {
        $image = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $image);
    }

    $stmt = $conn->prepare("UPDATE menu_items SET name = ?, category_id = ?, price = ?, is_available = ?, image = ? WHERE id = ?");
    $stmt->bind_param("sidisi", $name, $category_id, $price, $is_available, $image, $id);

    if ($stmt->execute()) {
        header("Location: menu_list.php?msg=edited");
        exit;
    } else {
        $error = "خطا در ذخیره تغییرات.";
    }
}
?>
<?php include '../includes/header.php'; ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ویرایش غذا</title>
    <link href="../assets/bootstrap/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body>
    <h3 class="mb-4">ویرایش غذا</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="name" class="form-label">نام غذا</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($item['name']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">دسته‌بندی</label>
            <select name="category" class="form-select" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $item['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">قیمت</label>
            <input type="number" name="price" class="form-control" value="<?= htmlspecialchars($item['price']) ?>" required>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="is_available" id="available" <?= $item['is_available'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="available">
                فعال
            </label>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">تصویر غذا</label><br>
            <?php if (!empty($item['image'])): ?>
                <img src="../uploads/<?= htmlspecialchars($item['image']) ?>" alt="عکس غذا" width="120" class="mb-2"><br>
            <?php endif; ?>
            <input type="file" name="image" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
        <a href="menu_list.php" class="btn btn-secondary">بازگشت</a>
    </form>

    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include '../includes/footer.php'; ?>