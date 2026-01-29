<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../include/database.php';
require '../check.php';

$user_id = $_SESSION['user']['id'];

// Handle clear cart
if (isset($_POST['clear'])) {
    $_SESSION['cart'][$user_id] = [];
    if (empty($_SESSION['cart'][$user_id])) {
        header('location:index.php');
        exit();
    }
}

// Handle confirm command
if (isset($_POST['add'])) {
    $total = $_POST['total'];
    $cart = $_SESSION['cart'][$user_id];
    if (!empty($cart)) {
        $idProducts = array_keys($cart);
        $idProducts = implode(',', $idProducts);
        $products = $pdo->query("SELECT * FROM product WHERE id IN ($idProducts)")->fetchAll(PDO::FETCH_ASSOC);
        $price = 0;
        $p_total = 0;
        $commands = [];
        $sql = "INSERT INTO line_command (id_product, id_command, price, quantity, total) VALUES ";

        foreach ($products as $product) {
            $idProduct = $product['id'];
            $discount = $product['discount'] ?? 0;
            $price = $product['price'] * (1 - $discount / 100);
            $p_qnty = $cart[$idProduct];
            $p_total = $price * $p_qnty;
            $commands[$idProduct] = [
                'id' => $idProduct,
                'price' => $price,
                'p_qnty' => $p_qnty,
                'p_total' => $p_total,
            ];
            $sql .= "(:id$idProduct, :commandId, :price$idProduct, :quantity$idProduct, :total$idProduct),";
        }
        $sql = rtrim($sql, ',');

        $pdo->query("INSERT INTO command (id_client, total) VALUES ($user_id, $total)");
        $idCommand = $pdo->lastInsertId();

        $sqlState = $pdo->prepare($sql);
        foreach ($commands as $command) {
            $id = $command['id'];
            $sqlState->bindParam(':id' . $id, $command['id']);
            $sqlState->bindParam(':commandId', $idCommand);
            $sqlState->bindParam(':price' . $id, $command['price']);
            $sqlState->bindParam(':quantity' . $id, $command['p_qnty']);
            $sqlState->bindParam(':total' . $id, $command['p_total']);
        }
        $sqlState->execute();
        if (isset($_SESSION['cart'][$user_id])) {
            unset($_SESSION['cart'][$user_id]);
        }
    }
}

// Handle remove item
if (isset($_POST['remove'])) {
    $productId = $_POST['product_id'];
    if (isset($_SESSION['cart'][$user_id][$productId])) {
        unset($_SESSION['cart'][$user_id][$productId]);
    }
    header('Location: cart.php');
    exit();
}

// Handle edit quantity
if (isset($_POST['edit'])) {
    $productId = $_POST['id'];
    $qty = $_POST['qty'];

    if (!empty($productId) && !empty($qty)) {
        if (isset($_SESSION['cart'][$user_id][$productId])) {
            $_SESSION['cart'][$user_id][$productId] = $qty;
        }
    }
    header('Location: cart.php');
    exit();
}
