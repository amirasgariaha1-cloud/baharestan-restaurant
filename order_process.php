<?php
session_start();
include '../config.php';

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    header("Location: cart.php");
    exit;
}

// دریافت اطلاعات فرم
$customer_name    = $_POST['customer_name'] ?? '';
$customer_phone   = $_POST['customer_phone'] ?? '';
$customer_address = $_POST['customer_address'] ?? '';
$payment_method   = $_POST['payment_method'] ?? 'operator';

if (!$customer_name || !$customer_phone || !$customer_address) {
    echo "اطلاعات ناقص است.";
    exit;
}

// محاسبه قیمت کل
$total_price = 0;
foreach ($cart as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

// ثبت سفارش در جدول orders
$stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, status, payment_method, created_at, customer_name, customer_phone, customer_address) VALUES (?, ?, 'pending', ?, NOW(), ?, ?, ?)");
$user_id = $_SESSION['user_id'] ?? null; // اگر لاگین کرده باشد
$stmt->bind_param("idssss", $user_id, $total_price, $payment_method, $customer_name, $customer_phone, $customer_address);
$stmt->execute();
$order_id = $stmt->insert_id;
$stmt->close();

// ثبت آیتم‌های سفارش در جدول order_items
$item_stmt = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price_each) VALUES (?, ?, ?, ?)");
foreach ($cart as $item) {
    $item_stmt->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
    $item_stmt->execute();
}
$item_stmt->close();

// پاک کردن سبد خرید
unset($_SESSION['cart']);

// هدایت به صفحه موفقیت
header("Location: order_success.php?order_id=" . $order_id);
exit;
?>
