<?php
include '../includes/header.php';
include '../config.php';

$result = mysqli_query($conn, "SELECT * FROM categories ORDER BY id DESC");
$msg = $_GET['msg'] ?? '';
if ($msg === 'deleted') {
    echo '<div class="alert alert-success">دسته‌بندی با موفقیت حذف شد.</div>';
} elseif ($msg === 'has_items') {
    echo '<div class="alert alert-warning">این دسته‌بندی دارای غذا است و نمی‌توان آن را حذف کرد.</div>';
}
?>
<main>

    <h3 class="mb-4">لیست دسته‌بندی‌های منو</h3>

   <a href="category_add.php" class="btn btn-success mb-3">افزودن دسته‌بندی جدید</a>


    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>شناسه</th>
                <th>نام دسته‌بندی</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td>
                            <a href="category_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">ویرایش</a>
							<a href="category_delete.php?category_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('آیا از حذف این دسته‌بندی مطمئن هستید؟');">حذف</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center text-muted">هیچ دسته‌بندی‌ای ثبت نشده است.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</main>

<?php include '../includes/footer.php'; ?>
