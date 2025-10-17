<?php
include '../config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

// دریافت اطلاعات غذا
$stmt = $conn->prepare("SELECT mi.*, c.name AS category_name FROM menu_items mi LEFT JOIN categories c ON mi.category_id = c.id WHERE mi.id = ? AND mi.is_available = 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    echo "<div class='alert alert-danger text-center mt-4'>غذا پیدا نشد یا در دسترس نیست.</div>";
    exit;
}
?>
<main>
    <a href="index.php" class="btn btn-secondary mb-4">بازگشت به منو</a>

    <div class="card mx-auto shadow" style="max-width: 500px;">
        <?php if (!empty($item['image'])): ?>
            <img src="../uploads/<?= htmlspecialchars($item['image']) ?>" 
                 class="card-img-top img-fluid"
                 alt="عکس غذا"
                 style="max-height: 300px; object-fit: cover;">
        <?php endif; ?>
        <div class="card-body">
            <h4 class="card-title"><?= htmlspecialchars($item['name']) ?></h4>
            <p class="text-muted">دسته‌بندی: <?= htmlspecialchars($item['category_name'] ?? '---') ?></p>
            <?php if (!empty($item['description'])): ?>
                <p class="card-text"><?= nl2br(htmlspecialchars($item['description'])) ?></p>
            <?php endif; ?>
            <p class="h5 mt-3">قیمت: <?= number_format($item['price']) ?> تومان</p>

            <!-- <a href="checkout.php?meal_id=<?= $item['id'] ?>" class="btn btn-success mt-3">سفارش غذا</a> -->
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</main>
