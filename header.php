<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>کافه رستوران بهارستان</title>
    <link href="../assets/bootstrap/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<header class="bg-dark text-light p-3 mb-4">
    <div class="container d-flex justify-content-between align-items-center">
        <h1 class="h5 m-0">پنل مدیریت</h1>
        <div>
            <a href="dashboard.php" class="btn btn-sm btn-light me-2">🏠 داشبورد</a>
            <a href="javascript:history.back()" class="btn btn-sm btn-secondary me-2">🔙 بازگشت</a>
            <a href="../auth/logout.php" class="btn btn-sm btn-danger">🚪 خروج</a>
        </div>
    </div>
</header>

<main class="container flex-fill">
