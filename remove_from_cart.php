<?php
session_start();
include '../config.php';
// بررسی وجود شناسه غذا در پارامتر GET
$id = $_GET['id'] ?? null;

if ($id && isset($_SESSION['cart'][$id])) {
    unset($_SESSION['cart'][$id]);
}

// بازگشت به سبد خرید
header("Location: cart.php");
exit;

