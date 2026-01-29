<?php
require_once 'cart_actions.php';

// Get cart data
$cart = $_SESSION['cart'][$user_id] ?? [];
$idProducts = array_keys($cart);

if (!empty($idProducts)) {
    $idProducts = implode(',', $idProducts);
    $products = $pdo->query("SELECT * FROM product WHERE id IN ($idProducts)")->fetchAll(PDO::FETCH_ASSOC);
} else {
    $products = [];
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
<!DOCTYPE html>
<html lang="en">
<!-- Rest of your HTML code remains the same -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">
    <script src="btn_qnty_cart.js"></script>
    <title>BMS Ecommerce - Cart</title>
    <style>
        /* Enhanced styles */
        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }

        .cart-item {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .cart-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .quantity-control {
            width: 120px;
            background: #f8f9fa;
            border-radius: 20px;
            padding: 5px;
        }

        .quantity-btn {
            width: 28px;
            height: 28px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 14px;
        }

        .quantity-input {
            width: 40px !important;
            text-align: center;
            border: none;
            background: transparent;
            font-weight: 600;
        }

        .quantity-input:focus {
            outline: none;
            box-shadow: none;
        }

        .product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
        }

        .remove-btn {
            transition: color 0.2s ease;
            color: #6c757d;
        }

        .remove-btn:hover {
            color: #dc3545 !important;
        }

        .discount-badge {
            position: absolute;
            top: -10px;
            right: -10px;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
            background: #dc3545;
            color: white;
            box-shadow: 0 2px 5px rgba(220, 53, 69, 0.3);
        }

        .cart-summary {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .empty-cart {
            min-height: 400px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .empty-cart i {
            font-size: 5rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body class="bg-light">
    <?php
    include '../include/nav_front.php';
    ?>
    <div class="container py-5">
        <!-- Category Banner -->
        <!-- <div class="category-banner">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Shopping Cart</li>
                </ol>
            </nav>
        </div> -->
        <div class="row">
            <!-- Main Cart Content -->
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Shopping Cart</h2>
                    <span class="badge bg-primary rounded-pill fs-6">
                        <?php echo count($products); ?> items
                    </span>
                </div>

                <?php if (empty($products)) { ?>
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart mb-3"></i>
                        <h3 class="text-muted mb-3">Your cart is empty</h3>
                        <p class="text-muted mb-4">Looks like you haven't added any items yet</p>
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                        </a>
                    </div>
                <?php } else { ?>
                    <?php foreach ($products as $product) {
                        $qty = $_SESSION['cart'][$user_id][$product['id']];
                        $original_price = $product['price'];
                        $final_price = !empty($product['discount'])
                            ? $original_price * (1 - $product['discount'] / 100)
                            : $original_price;
                    ?>
                        <div class="card cart-item mb-3">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <!-- Product Image -->
                                    <div class="col-md-2">
                                        <div class="position-relative">
                                            <img src="../upload/product/<?php echo $product['image'] ?>"
                                                class="product-image"
                                                alt="<?php echo htmlspecialchars($product['label']) ?>">
                                            <?php if (!empty($product['discount'])) { ?>
                                                <span class="discount-badge">
                                                    -<?php echo $product['discount'] ?>%
                                                </span>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <!-- Product Details -->
                                    <div class="col-md-4">
                                        <h5 class="mb-2"><?php echo htmlspecialchars($product['label']) ?></h5>
                                        <div class="mb-2">
                                            <?php if (!empty($product['discount'])) { ?>
                                                <span class="text-muted text-decoration-line-through me-2">
                                                    <?php echo number_format($original_price, 2) ?> MAD
                                                </span>
                                                <span class="text-danger fw-bold">
                                                    <?php echo number_format($final_price, 2) ?> MAD
                                                </span>
                                            <?php } else { ?>
                                                <span class="fw-bold">
                                                    <?php echo number_format($original_price, 2) ?> MAD
                                                </span>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <!-- Quantity Controls -->
                                    <div class="col-md-3">
                                        <form method="post" action="cart_actions.php" class="counter col-md-3 col-lg-3 col-xl-2 d-flex">
                                            <button id="decrement-<?php echo $product['id'] ?>"
                                                type="button"
                                                class="btn btn-outline-dark btn-sm"
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
                                                class="btn btn-outline-dark btn-sm"
                                                style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .55rem;">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            <div class="px-2">
                                                <button class="btn btn-warning btn-sm" type="submit" name="edit">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Subtotal -->
                                    <div class="col-md-2 text-end">
                                        <h5 class="mb-0 fw-bold text-primary">
                                            <?php echo number_format($final_price * $qty, 2) ?> MAD
                                        </h5>
                                    </div>

                                    <!-- Remove Button -->
                                    <div class="col-md-1 text-end">
                                        <form method="post">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id'] ?>">
                                            <button type="submit"
                                                name="remove"
                                                class="btn btn-link remove-btn">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>

            <!-- Cart Summary -->
            <?php if (!empty($products)) {
                $total = calculateTotal($products, $cart, $user_id);
                // if (isset($_SESSION['user'])) {

                //     $_SESSION['total'][$user_id] = $total;
                // }
            ?>
                <div class="col-lg-4">
                    <div class="cart-summary">
                        <h4 class="mb-4">Cart Summary</h4>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal</span>
                            <span class="fw-bold"><?php echo number_format($total, 2) ?> MAD</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Shipping</span>
                            <span class="text-success">Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold">Total</span>
                            <span class="fw-bold text-primary h4 mb-0">
                                <?php echo number_format($total, 2) ?> MAD
                            </span>
                        </div>
                        <form method="post" class="d-grid gap-2">
                            <input type="hidden" name="total" value="<?php echo $total ?>">
                            <button type="submit" name="add" class="btn btn-primary">
                                <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                            </button>
                            <button type="submit"
                                name="clear"
                                class="btn btn-outline-danger"
                                onclick="return confirm('Are you sure you want to empty your cart?')">
                                <i class="fas fa-trash-alt me-2"></i>Empty Cart
                            </button>
                        </form>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>