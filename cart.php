<?php
session_start();
include __DIR__ . '/../config.php';

$cart = $_SESSION['cart'] ?? [];

$items = [];
$total_price = 0;

if (!empty($cart)) {
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $result = mysqli_query($conn, "SELECT * FROM menu_items WHERE id IN ($ids)");

    while ($row = mysqli_fetch_assoc($result)) {
        $row['quantity'] = $cart[$row['id']]['quantity']; // ← اصلاح شد
        $row['total'] = $row['price'] * $row['quantity'];
        $total_price += $row['total'];
        $items[] = $row;
    }
}
?>


<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>سبد خرید</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.rtl.min.css">
</head>
<body class="container pt-4">

    <h2 class="mb-4">🛒 سبد خرید</h2>
<a href="index.php" class="btn btn-secondary mb-3">⬅ بازگشت به منو</a>
    <?php if (empty($items)): ?>
        <div class="alert alert-warning">سبد خرید شما خالی است.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>نام غذا</th>
                    <th>قیمت واحد</th>
                    <th>تعداد</th>
                    <th>قیمت کل</th>
                    <th>حذف</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= number_format($item['price']) ?> تومان</td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= number_format($item['total']) ?> تومان</td>
                        <td>
                            <a href="remove_from_cart.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-danger">❌ حذف</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr class="table-light fw-bold">
                    <td colspan="3" class="text-end">جمع کل:</td>
                    <td colspan="2"><?= number_format($total_price) ?> تومان</td>
                </tr>
            </tbody>
        </table>

        <a href="checkout.php" class="btn btn-success btn-lg">ادامه به ثبت سفارش</a>
    <?php endif; ?>

</body>
</html>
