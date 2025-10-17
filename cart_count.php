<?php
session_start();
include '../config.php';
$count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $count += $item['quantity'];
    }
}
echo $count;