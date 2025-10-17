<?php
include '../includes/header.php';
?>
<main>
    <h2 class="text-center mb-5">💼 داشبورد مدیریت کافه رستوران بهارستان</h2>

    <div class="row g-4">

        <div class="col-md-4">
            <div class="card border-primary h-100">
                <div class="card-body text-center">
                    <h5 class="card-title text-primary">🍽️ مدیریت غذاها</h5>
                    <p class="card-text">مشاهده و ویرایش آیتم‌های منو</p>
                    <a href="menu_list.php" class="btn btn-primary btn-custom">📋 لیست غذاها</a>
                    <a href="menu_add.php" class="btn btn-outline-primary btn-custom mt-2">➕ افزودن غذا</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-success h-100">
                <div class="card-body text-center">
                    <h5 class="card-title text-success">📂 دسته‌بندی‌ها</h5>
                    <p class="card-text">مدیریت گروه‌بندی غذاها</p>
                    <a href="categories_list.php" class="btn btn-success btn-custom">📁 مدیریت دسته‌بندی‌ها</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-warning h-100">
                <div class="card-body text-center">
                    <h5 class="card-title text-warning">👥 کاربران</h5>
                    <p class="card-text">مدیریت کاربران و نقش‌ها</p>
                    <a href="../users/users_list.php" class="btn btn-warning btn-custom">👤 لیست کاربران</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-info h-100">
                <div class="card-body text-center">
                    <h5 class="card-title text-info">📦 سفارشات</h5>
                    <p class="card-text">لیست سفارش‌های ثبت‌شده</p>
                    <a href="orders_list.php" class="btn btn-info btn-custom">📑 لیست سفارشات</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-dark h-100">
                <div class="card-body text-center">
                    <h5 class="card-title text-dark">🖼️ پس‌زمینه</h5>
                    <p class="card-text">تغییر تصویر بک‌گراند سایت</p>
                    <a href="background_upload.php" class="btn btn-dark btn-custom">⬆️ آپلود بک‌گراند</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-danger h-100">
                <div class="card-body text-center">
                    <h5 class="card-title text-danger">📞 تماس‌های ورودی</h5>
                    <p class="card-text">ارسال پیامک تماس‌ها</p>
                    <a href="../sms/index.php" class="btn btn-danger btn-custom">✉️ پیامک تماس‌ها</a>
                </div>
            </div>
        </div>
<div class="col-md-4">
    <div class="card border-secondary h-100">
        <div class="card-body text-center">
            <h5 class="card-title">مدیریت صفحات</h5>
            <p class="card-text">درباره ما و تماس با ما</p>
            <a href="pages_manage.php" class="btn btn-secondary">ویرایش صفحات</a>
        </div>
    </div>
</div>

    </div>
</main>

<?php include '../includes/footer.php'; ?>
