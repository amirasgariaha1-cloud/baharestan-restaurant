<?php
include '../includes/header.php';
include '../config.php';

$page = $_GET['page'] ?? 'about';

$stmt = $conn->prepare("SELECT content FROM site_pages WHERE page = ?");
$stmt->bind_param("s", $page);
$stmt->execute();
$stmt->bind_result($content);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newContent = $_POST['content'];
    $stmt = $conn->prepare("UPDATE site_pages SET content = ? WHERE page = ?");
    $stmt->bind_param("ss", $newContent, $page);
    $stmt->execute();
    echo "<div class='alert alert-success'>✅ با موفقیت ذخیره شد.</div>";
    $content = $newContent;
}
?>
<main>
<div class="container mt-4">
    <h4>ویرایش: <?= $page === 'about' ? 'درباره ما' : 'تماس با ما' ?></h4>
    <form method="post">
        <div class="mb-3">
            <textarea name="content" rows="10" class="form-control"><?= htmlspecialchars($content) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">💾 ذخیره</button>
    </form>
    <hr>
    <a href="?page=about" class="btn btn-outline-dark btn-sm">ویرایش درباره ما</a>
    <a href="?page=contact" class="btn btn-outline-dark btn-sm">ویرایش تماس با ما</a>
</div>
</main>
<?php include '../includes/footer.php'; ?>
