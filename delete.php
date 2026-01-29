<?php
require_once 'include/database.php';
if ($_GET['type'] === 'product') {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM product WHERE id = ?");
    $delete = $stmt->execute([$id]);
    if ($delete) {
        session_start();
        $_SESSION['product_delete'] = true;
        header('location:List_products.php');
    }
} elseif ($_GET['type'] === 'category') {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM category WHERE id = ?");
    $delete = $stmt->execute([$id]);
    if ($delete) {
        session_start();
        $_SESSION['category_delete'] = true;
        header('location:List_categories.php');
    }
}
