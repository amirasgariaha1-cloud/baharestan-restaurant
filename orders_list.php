
<?php
include '../includes/header.php';
include '../config.php';

$result = mysqli_query($conn, "
    SELECT * FROM orders ORDER BY created_at DESC
");
?>
<main>
<body>
    <h2 class="mb-4">لیست سفارش‌ها</h2>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>شناسه</th>
                <th>نام مشتری</th>
                <th>شماره تماس</th>
                <th>وضعیت</th>
                <th>مبلغ کل</th>
                <th>تاریخ</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td><?= htmlspecialchars($row['customer_phone']) ?></td>
                    <td><?= $row['status'] ?></td>
                    <td><?= number_format($row['total_price']) ?> تومان</td>
                    <td><?= $row['created_at'] ?></td>
                    <td>
						<a href="order_view.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">مشاهده / مدیریت</a>
					</td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
	<form method="post">
    <label for="status">تغییر وضعیت سفارش:</label>
    <select name="status" id="status" class="form-select">
        <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>در انتظار</option>
        <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>در حال پردازش</option>
        <option value="ready" <?= $order['status'] == 'ready' ? 'selected' : '' ?>>آماده ارسال</option>
        <option value="paid" <?= $order['status'] == 'paid' ? 'selected' : '' ?>>پرداخت‌شده</option>
    </select>
    <button type="submit" class="btn btn-primary mt-2">بروزرسانی وضعیت</button>
</form>
</main>
<?php include '../includes/footer.php'; ?>
