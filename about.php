<?php
include 'includes/header.php';
include 'config.php';

$page = 'about';
$content = '';

$stmt = $conn->prepare("SELECT content FROM site_pages WHERE page = ?");
$stmt->bind_param("s", $page);
$stmt->execute();
$stmt->bind_result($content);
$stmt->fetch();
$stmt->close();
?>

<div class="container mt-4">
    <h2>درباره ما</h2>
    <div><?= nl2br($content) ?></div>
</div>

<?php include 'includes/footer.php'; ?>