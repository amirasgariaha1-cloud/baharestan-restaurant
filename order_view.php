<?php
include '../config.php';
include '../includes/header.php';

$order_id = $_GET['id'] ?? null;

if (!$order_id) {
    echo "<div class='alert alert-danger'>شناسه سفارش مشخص نیست.</div>";
    include '../includes/footer.php';
    exit;
}

// پردازش تغییر وضعیت اگر POST ارسال شده باشد
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $new_status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    $stmt->close();
    echo "<div class='alert alert-success'>وضعیت سفارش بروزرسانی شد.</div>";
}

// دریافت مشخصات سفارش
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();

if (!$order) {
    echo "<div class='alert alert-warning'>سفارش یافت نشد.</div>";
    include '../includes/footer.php';
   exit;
}
