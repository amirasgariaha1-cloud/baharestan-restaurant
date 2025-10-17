<?php
// manage_menu.php
include '../includes/header.php';
?>
<main>

    <div class="container mt-4">
        <h3 class="mb-4">مدیریت منوهای غذایی</h3>

        <div class="row g-4">
            <div class="col-md-4">
                <a href="menu_list.php" class="btn btn-primary w-100">📋 مشاهده لیست غذاها</a>
            </div>
            <div class="col-md-4">
                <a href="menu_add.php" class="btn btn-success w-100">➕ افزودن غذای جدید</a>
            </div>
            <div class="col-md-4">
                <a href="dashboard.php" class="btn btn-secondary w-100">⬅️ بازگشت به داشبورد</a>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>

</body>
</html>
