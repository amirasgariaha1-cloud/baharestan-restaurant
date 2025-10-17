<?php
session_start();
$order_id = $_GET['order_id'] ?? null;
include '../config.php';
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>سفارش ثبت شد</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.rtl.min.css">
</head>
<body class="container text-center pt-5">

    <div class="alert alert-success">
        <h2>✅ سفارش شما با موفقیت ثبت شد!</h2>
        <?php if ($order_id): ?>
            <p>شماره سفارش شما: <strong>#<?= htmlspecialchars($order_id) ?></strong></p>
        <?php endif; ?>
        <p>با تشکر از اعتماد شما. همکاران ما در اسرع وقت با شما تماس خواهند گرفت.</p>
    </div>

    <a href="index.php" class="btn btn-primary mt-3">بازگشت به صفحه اصلی</a>

</body>
</html>

