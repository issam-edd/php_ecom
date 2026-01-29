<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>BMS Ecommerce</title>
</head>

<body>
    <?php
    include '../include/nav_front.php';
    require_once '../include/database.php';
    require '../check.php';

    $user_id = $_SESSION['user']['id'];
    $cart = $_SESSION['cart'][$user_id] ?? [];
    $idProducts = array_keys($cart);

    if (!empty($idProducts)) {
        $idProducts = implode(',', $idProducts);
        $products = $pdo->query("SELECT * FROM product WHERE id IN ($idProducts)")->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $products = [];
    }
    if (isset($_POST['clear'])) {
        $_SESSION['cart'][$user_id] = [];
        if (empty($_SESSION['cart'][$user_id])) {
            header('location:index.php');
        }
    }
    if (isset($_POST['remove'])) {
        $productId = $_POST['product_id'];
        $userId = $_SESSION['user']['id'];

        // Check if product exists in cart
        if (isset($_SESSION['cart'][$userId][$productId])) {
            // Remove the specific product
            unset($_SESSION['cart'][$userId][$productId]);
        }
        header('Location: cart.php');
        exit();
    }
    if (isset($_POST['edit'])) {
        $productId = $_POST['id'];
        $userId = $_SESSION['user']['id'];
        $qty = $_POST['qty'];

        if (!empty($userId) && !empty($qty)) {
            if (!isset($_SESSION['cart'][$userId][$productId])) {
                header('Location: cart.php');
                exit();
            } else {
                $_SESSION['cart'][$userId][$productId] = $qty;
            }
        }
        header('Location: cart.php');
        exit();
    }

    // Calculate total price function
    function calculateTotal($products, $cart, $user_id)
    {
        $total = 0;
        foreach ($products as $product) {
            $qty = $cart[$product['id']];
            $price = $product['price'];
            if (!empty($product['discount'])) {
                $discounted_price = $price * (1 - $product['discount'] / 100);
                $total += $discounted_price * $qty;
            } else {
                $total += $price * $qty;
            }
        }
        return $total;
    }
    ?>

    <section class="h-100">
        <div class="container h-100 py-5">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-10">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="fw-normal mb-0">Shopping Cart (<?php echo count($products); ?> items)</h3>
                    </div>

                    <?php
                    if (empty($products)) {
                        echo '
                        <div class="text-center py-5">
                            <h4 class="text-muted">THERE IS NO PRODUCT :/</h4>
                            <a class="btn btn-primary mt-3" href="index.php" role="button">Back Home</a>
                        </div>
                        ';
                    }

                    foreach ($products as $product) {
                        $qty = $_SESSION['cart'][$user_id][$product['id']];
                        $original_price = $product['price'];
                        $final_price = !empty($product['discount'])
                            ? $original_price * (1 - $product['discount'] / 100)
                            : $original_price;
                    ?>
                        <div class="card rounded-3 mb-4">
                            <div class="card-body p-4">
                                <div class="row d-flex justify-content-between align-items-center">
                                    <!-- Product Image -->
                                    <div class="col-md-2 col-lg-2 col-xl-2">
                                        <img src="../upload/product/<?php echo $product['image'] ?>"
                                            style="max-height:100px"
                                            class="img-fluid rounded-3"
                                            alt="<?php echo htmlspecialchars($product['label']) ?>">
                                    </div>

                                    <!-- Product Details -->
                                    <div class="col-md-3 col-lg-3 col-xl-3">
                                        <p class="lead fw-normal mb-2"><?php echo htmlspecialchars($product['label']) ?></p>
                                        <div>
                                            <?php if (!empty($product['discount'])) { ?>
                                                <span class="text-muted text-decoration-line-through">
                                                    <?php echo number_format($original_price, 2) ?> MAD
                                                </span>
                                                <span class="text-danger ms-2">
                                                    <?php echo number_format($final_price, 2) ?> MAD
                                                    (<?php echo $product['discount'] ?>% OFF)
                                                </span>
                                            <?php } else { ?>
                                                <span><?php echo number_format($original_price, 2) ?> MAD</span>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <!-- Quantity Controls - Keeping your original structure -->
                                    <form method="post" class="counter col-md-3 col-lg-3 col-xl-2 d-flex">
                                        <button id="decrement-<?php echo $product['id'] ?>"
                                            type="button"
                                            class="btn btn-dark btn-sm"
                                            style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .55rem;"
                                            <?php if ($qty <= 1) echo 'disabled' ?>>
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="hidden" name="id" value="<?php echo $product['id'] ?>">
                                        <input type="number"
                                            id="numberInput-<?php echo $product['id'] ?>"
                                            name="qty"
                                            class="form-control form-control-sm mx-2"
                                            style="width: 50px; text-align: center;"
                                            value="<?php echo $qty ?>"
                                            readonly>
                                        <button id="increment-<?php echo $product['id'] ?>"
                                            type="button"
                                            class="btn btn-dark btn-sm"
                                            style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .55rem;">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <div class="px-2">
                                            <button class="btn btn-warning btn-sm" type="submit" name="edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        </div>
                                    </form>

                                    <!-- Subtotal -->
                                    <div class="col-md-3 col-lg-2 col-xl-2 offset-lg-1">
                                        <h5 class="mb-0">
                                            <?php echo number_format($final_price * $qty, 2) ?> MAD
                                        </h5>
                                    </div>

                                    <!-- Delete Button -->
                                    <form method="post" class="col-md-1 col-lg-1 col-xl-1 text-end">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id'] ?>">
                                        <button type="submit" name="remove" class="btn btn-sm text-danger"><i class="fas fa-trash fa-lg"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php }
                    ?>
                    <?php if (!empty($products)) {
                        $total = calculateTotal($products, $cart, $user_id);
                    ?>
                        <!-- Total and Checkout -->
                        <div class="card rounded-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h3 class="mb-0">Total: <?php echo number_format($total, 2) ?> MAD</h3>
                                    <form action="" method="post">
                                        <button type="submit" name="add" class="btn btn-warning ">
                                            Proceed to Pay
                                        </button>
                                        <button type="submit" name="clear" onclick="return confirm('Do you realy want the clear your cart !!')" class="btn btn-danger ">
                                            Empty your cart !
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <script src="btn_qnty_cart.js"></script>
    </section>
</body>

</html>