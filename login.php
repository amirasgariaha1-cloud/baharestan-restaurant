<?php
session_start();
include('../config.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username === '' || $password === '') {
        $error = 'لطفاً نام کاربری و رمز عبور را وارد کنید.';
    } else {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (hash('sha256', $password) === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['is_admin'] = $user['is_admin'];

                header('Location: ../admin/dashboard.php');
                exit;
            } else {
                $error = 'رمز عبور اشتباه است.';
            }
        } else {
            $error = 'کاربری با این مشخصات یافت نشد.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ورود</title>
    <link href="../assets/bootstrap/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body class="container pt-5">
    <h3>ورود به پنل مدیریت</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="mt-4">
        <div class="mb-3">
            <label for="username" class="form-label">نام کاربری:</label>
            <input type="text" name="username" id="username" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">رمز عبور:</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <button class="btn btn-primary" type="submit">ورود</button>
    </form>
</body>
</html>
