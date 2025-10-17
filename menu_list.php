<?php
include '../includes/header.php';
$msg = $_GET['msg'] ?? '';
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <title>لیست غذاها</title>
    <link href="../assets/bootstrap/css/bootstrap.rtl.min.css" rel="stylesheet" />
</head>

<body>
    <h3 class="mb-4">لیست غذاهای منو</h3>

    <?php if ($msg === 'deleted'): ?>
        <div class="alert alert-success">غذا با موفقیت حذف شد.</div>
    <?php endif; ?>

    <a href="menu_add.php" class="btn btn-success mb-3">افزودن غذای جدید</a>

    <table class="table table-striped table-bordered table-hover align-middle text-center">
        <thead class="table-dark">
            <tr>
                <th>نام غذا</th>
                <th>دسته‌بندی</th>
                <th>قیمت (تومان)</th>
                <th>وضعیت</th>
                <th>عکس</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $result = mysqli_query($conn, "
            SELECT mi.id, mi.name, c.name AS category_name, mi.price, mi.is_available, mi.image
            FROM menu_items mi
            LEFT JOIN categories c ON mi.category_id = c.id
            ORDER BY mi.id DESC
        ");

        if (!$result) {
            echo '<tr><td colspan="6" class="text-danger">خطا در دریافت اطلاعات: ' . mysqli_error($conn) . '</td></tr>';
        } elseif (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)):
        ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['category_name'] ?? 'بدون دسته‌بندی') ?></td>
                <td><?= number_format($row['price']) ?></td>
                <td>
                    <span class="badge bg-<?= $row['is_available'] ? 'success' : 'secondary' ?>">
                        <?= $row['is_available'] ? 'فعال' : 'غیرفعال' ?>
                    </span>
                </td>
                <td>
                    <?php if (!empty($row['image'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" alt="عکس غذا" class="img-thumbnail" style="max-width: 80px; max-height: 80px;">
                    <?php else: ?>
                        <span class="text-muted">ندارد</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="menu_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">ویرایش</a>
                    <a href="menu_delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('آیا از حذف این غذا مطمئن هستید؟');">حذف</a>
                </td>
            </tr>
        <?php
            endwhile;
        } else {
        ?>
            <tr>
                <td colspan="6" class="text-center text-muted">هیچ غذایی ثبت نشده است.</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include '../includes/footer.php'; ?>