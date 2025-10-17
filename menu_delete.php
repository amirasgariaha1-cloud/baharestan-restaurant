<?php
// menu_delete.php
include '../includes/header.php';

$id = $_GET['id'] ?? null;

if ($id) {
    // حذف عکس مربوطه (اگر هست)
    $q = mysqli_query($conn, "SELECT image FROM menu_items WHERE id = $id");
    if ($q && $row = mysqli_fetch_assoc($q)) {
        if (!empty($row['image'])) {
            $filePath = '../uploads/' . $row['image'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    // حذف ردیف از دیتابیس
    mysqli_query($conn, "DELETE FROM menu_items WHERE id = $id");
}

header("Location: menu_list.php?msg=deleted");
exit;
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/footer.php'; ?>