<?php
session_start();
include '../config.php';
// بررسی سبد خرید
$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسویه حساب</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.rtl.min.css">
</head>
<body class="container pt-5">

    <h3 class="mb-4">تسویه حساب</h3>

    <form action="order_process.php" method="post">
        <div class="mb-3">
            <label for="customer_name" class="form-label">نام و نام خانوادگی *</label>
            <input type="text" name="customer_name" id="customer_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="customer_phone" class="form-label">شماره تماس *</label>
            <input type="tel" name="customer_phone" id="customer_phone" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="customer_address" class="form-label">آدرس کامل *</label>
            <textarea name="customer_address" id="customer_address" class="form-control" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label for="payment_method" class="form-label">روش پرداخت *</label>
            <select name="payment_method" id="payment_method" class="form-select" required>
                <option value="operator">پرداخت پس از تماس اپراتور</option>
                <option value="online">پرداخت آنلاین (در حال توسعه)</option>
            </select>
        </div>

        <h5 class="mt-4 mb-3">خلاصه سفارش:</h5>
        <ul class="list-group mb-3">
            <?php
            $total = 0;
            foreach ($cart as $item):
                $item_total = $item['price'] * $item['quantity'];
                $total += $item_total;
            ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?>
                    <span><?= number_format($item_total) ?> تومان</span>
                </li>
            <?php endforeach; ?>
            <li class="list-group-item d-flex justify-content-between">
                <strong>مبلغ کل:</strong>
                <strong><?= number_format($total) ?> تومان</strong>
            </li>
        </ul>

        <button type="submit" class="btn btn-success">ثبت سفارش</button>
        <a href="cart.php" class="btn btn-secondary">بازگشت به سبد خرید</a>
    </form>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>

