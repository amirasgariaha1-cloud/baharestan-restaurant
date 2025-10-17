<?php
include '../config.php';
include '../includes/header.php';

$result = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <title>لیست کاربران</title>
    <link href="../assets/bootstrap/css/bootstrap.rtl.min.css" rel="stylesheet" />
</head>

<body>
    <h3 class="mb-4">لیست کاربران</h3>

    <a href="users_add.php" class="btn btn-success mb-3">افزودن کاربر جدید</a>

    <table class="table table-bordered table-hover text-center">
        <thead class="table-dark">
            <tr>
                <th>شناسه</th>
                <th>نام کاربری</th>
                <th>نقش</th>
                <th>وضعیت</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= $row['role'] === 'admin' ? 'مدیر' : 'کاربر عادی' ?></td>
                    <td><?= $row['active'] ? 'فعال' : 'غیرفعال' ?></td>
                    <td>
                        <a href="users_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">ویرایش</a>
                        <a href="users_delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('آیا از حذف این کاربر مطمئن هستید؟')">حذف</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5" class="text-muted">هیچ کاربری یافت نشد.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include '../includes/footer.php'; ?>
