<?php
session_start();
include __DIR__ . '/../config.php'; // اگر فایل توی پوشه public هست، مسیر رو درست کن: include '../config.php';

$id = $_GET['id'] ?? null;
$quantity = $_GET['qty'] ?? 1;

if (!$id || !is_numeric($id) || !is_numeric($quantity) || $quantity < 1) {
    http_response_code(400);
    echo "Invalid";
    exit;
}

$stmt = $conn->prepare("SELECT id, name, price, image FROM menu_items WHERE id = ? AND is_available = 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$id] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'image' => $row['image'],
            'quantity' => $quantity
        ];
    }

    echo "added";
} else {
    http_response_code(404);
    echo "not_found";
}
exit;
