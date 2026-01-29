<?php
require_once '../include/database.php';
require '../check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['id'];
    $userId = $_SESSION['user']['id'];
    $qty = $_POST['qty'];
    var_dump($qty);
    die();

    if (!empty($id) && !empty($qty)) {
        if (!isset($_SESSION['cart'][$userId][$productId])) {
            header('Location: cart.php');
            exit();
        }
        $_SESSION['cart'][$userId][$productId] = $qty;
    }
    // Redirect back to cart
    header('Location: cart.php');
    exit();
}
