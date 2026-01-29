<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>BMS Ecommerce</title>
    <style>
        .product-image {
            transition: transform 0.3s ease;
        }

        .product-image:hover {
            transform: scale(1.05);
        }

        .recommended-card {
            transition: transform 0.2s;
        }

        .recommended-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-light">
    <?php
    include '../include/nav_front.php';
    require_once '../include/database.php';
    $id = $_GET['id'];
    if (!$id) {
        die('Invalid product ID!');
    }

    // Fetch product with category name
    $product = $pdo->query("
        SELECT p.*, c.label as category_name 
        FROM product p 
        LEFT JOIN category c ON p.id_category = c.id 
        WHERE p.id = $id
    ")->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die('Product not found!');
    }

    // Fetch recommended products from same category
    $recommended = $pdo->query("
        SELECT * FROM product 
        WHERE id_category = {$product['id_category']} 
        AND id != {$product['id']} 
        LIMIT 4
    ")->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require '../check.php';
        $id = $_POST['id'];
        $qty = $_POST['qty'];
        $user_id = $_SESSION['user']['id'];

        if (!empty($id) && !empty($qty)) {
            if (!isset($_SESSION['cart'][$user_id])) {
                $_SESSION['cart'][$user_id] = [];
            }
            $_SESSION['cart'][$user_id][$id] = $qty;
        }
        header('location:cart.php');
    }
    ?>

    <div class="container py-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="category.php?id=<?php echo $product['id_category'] ?>" class="text-decoration-none"><?php echo htmlspecialchars($product['category_name']) ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['label']) ?></li>
            </ol>
        </nav>

        <!-- Main Product Section -->
        <div class="card border-0 shadow-sm mb-5">
            <div class="row g-0">
                <!-- Product Image -->
                <div class="col-md-6 p-4 text-center">
                    <img class="img-fluid rounded product-image"
                        src="../upload/product/<?php echo $product['image'] ?>"
                        style="max-height: 400px; object-fit: contain;"
                        alt="<?php echo htmlspecialchars($product['label']) ?>">
                </div>

                <!-- Product Details -->
                <div class="col-md-6 p-4">
                    <div class="card-body">
                        <span class="badge bg-secondary mb-2"><?php echo htmlspecialchars($product['category_name']) ?></span>
                        <h2 class="card-title mb-3"><?php echo htmlspecialchars($product['label']) ?></h2>

                        <?php
                        $discount = $product['discount'];
                        $price = $product['price'];
                        if (!empty($discount)) {
                            $netprice = $price - (($discount * $price) / 100);
                        ?>
                            <div class="mb-3">
                                <span class="text-muted text-decoration-line-through fs-5"><?php echo $price ?> MAD</span>
                                <span class="badge bg-danger ms-2">-<?php echo $discount ?>%</span>
                            </div>
                        <?php
                        } else {
                            $netprice = $price;
                        }
                        ?>

                        <h3 class="text-primary mb-4"><?php echo $netprice ?> MAD</h3>

                        <div class="mb-4">
                            <h5 class="mb-3">Description</h5>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($product['description'])) ?></p>
                        </div>

                        <form method="post" class="mt-4">
                            <div class="d-flex align-items-center mb-4">
                                <label class="me-3 fw-bold">Quantity:</label>
                                <div class="counter d-flex align-items-center">
                                    <?php
                                    $user_id_u = $_SESSION['user']['id'] ?? null;
                                    $qty_u = $_SESSION['cart'][$user_id_u][$id] ?? 1;
                                    ?>
                                    <button id="decrement" type="button"
                                        class="btn btn-outline-dark btn-sm"
                                        <?php if ($qty_u <= 1) echo 'disabled' ?>>
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" id="numberInput" name="qty"
                                        class="form-control form-control-sm mx-2"
                                        style="width: 60px; text-align: center;"
                                        value="<?php echo $qty_u ?>" readonly>
                                    <button id="increment" type="button"
                                        class="btn btn-outline-dark btn-sm">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="id" value="<?php echo $product['id'] ?>">
                            <button class="btn btn-primary btn-lg w-100" type="submit">
                                <i class="fas fa-shopping-cart me-2"></i>Add To Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommended Products Section -->
        <?php if (!empty($recommended)) { ?>
            <section class="mt-5">
                <h3 class="mb-4">Recommended Products</h3>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                    <?php foreach ($recommended as $rec_product) {
                        $rec_price = $rec_product['price'];
                        $rec_discount = $rec_product['discount'];
                        $rec_final_price = !empty($rec_discount) ?
                            $rec_price - (($rec_discount * $rec_price) / 100) : $rec_price;
                    ?>
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm recommended-card">
                                <img src="../upload/product/<?php echo $rec_product['image'] ?>"
                                    class="card-img-top p-2"
                                    style="height: 200px; object-fit: contain;"
                                    alt="<?php echo htmlspecialchars($rec_product['label']) ?>">
                                <div class="card-body">
                                    <h5 class="card-title text-truncate">
                                        <?php echo htmlspecialchars($rec_product['label']) ?>
                                    </h5>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <?php if (!empty($rec_discount)) { ?>
                                            <div>
                                                <span class="text-muted text-decoration-line-through">
                                                    <?php echo $rec_price ?> MAD
                                                </span>
                                                <span class="text-danger ms-2">
                                                    <?php echo $rec_final_price ?> MAD
                                                </span>
                                            </div>
                                            <span class="badge bg-danger">-<?php echo $rec_discount ?>%</span>
                                        <?php } else { ?>
                                            <span class="fs-5"><?php echo $rec_final_price ?> MAD</span>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-0 p-3">
                                    <a href="product.php?id=<?php echo $rec_product['id'] ?>"
                                        class="btn btn-outline-primary w-100">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </section>
        <?php } ?>
    </div>

    <script src="btn_qnty.js"></script>
</body>

</html>