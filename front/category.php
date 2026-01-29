<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">
    <title>BMS Ecommerce</title>
    <style>
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .category-banner {
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .price-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 2;
        }

        .empty-state {
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
        }
    </style>
</head>

<body class="bg-light">
    <?php
    include '../include/nav_front.php';
    require_once '../include/database.php';
    $id = $_GET['id'];
    $category = $pdo->query("SELECT label FROM category WHERE id = $id")->fetch(PDO::FETCH_ASSOC);
    $products = $pdo->query("SELECT * FROM product WHERE id_category = $id")->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="container py-5">
        <!-- Category Banner -->
        <div class="category-banner">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active"><?php echo htmlspecialchars($category['label']) ?></li>
                </ol>
            </nav>
            <h1 class="display-6 mb-0"><?php echo htmlspecialchars($category['label']) ?></h1>
            <p class="text-muted mb-0">Discover our collection of <?php echo strtolower(htmlspecialchars($category['label'])) ?></p>
        </div>

        <?php if (!empty($products)) { ?>
            <!-- Filter and Sort Options -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <p class="mb-0"><?php echo count($products) ?> products found</p>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option>Sort by: Featured</option>
                        <option>Price: Low to High</option>
                        <option>Price: High to Low</option>
                        <option>Newest First</option>
                    </select>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row g-4">
                <?php foreach ($products as $product) {
                    $maxLength = 100;
                    $description = $product['description'];
                    $p = (strlen($description) > $maxLength)
                        ? substr($description, 0, $maxLength) . '...'
                        : $description;

                    // Calculate discount if exists
                    $price = $product['price'];
                    $discount = $product['discount'] ?? null;
                    $final_price = !empty($discount) ?
                        $price - (($discount * $price) / 100) : $price;
                ?>
                    <div class="col-sm-6 col-lg-4">
                        <div class="card h-100 product-card">
                            <?php if (!empty($discount)) { ?>
                                <div class="price-badge">
                                    <span class="badge bg-danger">-<?php echo $discount ?>%</span>
                                </div>
                            <?php } ?>
                            <div class="overflow-hidden">
                                <img src="../upload/product/<?php echo $product['image'] ?>"
                                    class="card-img-top product-image"
                                    style="height: 280px; object-fit: contain;"
                                    alt="<?php echo htmlspecialchars($product['label']) ?>">
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">
                                    <a href="view_product.php?id=<?php echo $product['id'] ?>"
                                        class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($product['label']) ?>
                                    </a>
                                </h5>
                                <p class="card-text text-muted small flex-grow-1"><?php echo htmlspecialchars($p) ?></p>
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <?php if (!empty($discount)) { ?>
                                            <div>
                                                <span class="text-muted text-decoration-line-through me-2">
                                                    <?php echo $price ?> MAD
                                                </span>
                                                <span class="fw-bold text-danger">
                                                    <?php echo $final_price ?> MAD
                                                </span>
                                            </div>
                                        <?php } else { ?>
                                            <span class="fw-bold"><?php echo $price ?> MAD</span>
                                        <?php } ?>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            <?php echo (new DateTime($product['created_date']))->format('M j, Y'); ?>
                                        </small>
                                    </div>
                                    <a href="product.php?id=<?php echo $product['id'] ?>"
                                        class="btn btn-outline-primary w-100">
                                        <i class="fas fa-eye me-2"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <!-- Empty State -->
            <div class="empty-state">
                <div class="text-center">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No Products Found</h4>
                    <p class="text-muted mb-4">There are currently no products in this category.</p>
                    <a class="btn btn-primary" href="index.php">
                        <i class="fas fa-home me-2"></i>Back to Home
                    </a>
                </div>
            </div>
        <?php } ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'footer.php' ?>
</body>

</html>