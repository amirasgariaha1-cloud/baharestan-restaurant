<?php
include '../config.php';

$id = $_GET['category_id'] ?? null;

if (!$id) {
    header("Location: categories_list.php");
    exit;
}

// بررسی اینکه آیا غذایی به این دسته‌بندی وصل است یا نه
$stmt = $conn->prepare("SELECT COUNT(*) AS count FROM menu_items WHERE category_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if ($data['count'] > 0) {
    // دسته‌بندی متصل به غذاهاست؛ اجازه حذف نده
    header("Location: categories_list.php?msg=has_items");
    exit;
}

// در صورت نبود غذا، حذف انجام شود
$stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: categories_list.php?msg=deleted");
exit;
