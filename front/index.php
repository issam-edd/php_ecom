<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">
    <title>BMS Ecommerce</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="bg-light">
    <?php
    include '../include/nav_front.php';
    require_once '../include/database.php';
    $categories = $pdo->query('SELECT * FROM category')->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="container py-4">
        <!-- Featured Products Carousel -->
        <div id="featuredCarousel" class="carousel slide mb-5 shadow rounded" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <?php
                // Fetch featured products (e.g., newest or most popular)
                $featured_products = $pdo->query("
                    SELECT p.*, c.label as category_name 
                    FROM product p 
                    LEFT JOIN category c ON p.id_category = c.id 
                    ORDER BY p.created_date DESC 
                    LIMIT 3
                ")->fetchAll(PDO::FETCH_ASSOC);

                foreach ($featured_products as $index => $product) {
                    echo '<button type="button" data-bs-target="#featuredCarousel" data-bs-slide-to="' . $index . '" 
                    ' . ($index === 0 ? 'class="active"' : '') . '></button>';
                }
                ?>
            </div>

            <div class="carousel-inner rounded">
                <?php foreach ($featured_products as $index => $product) { ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                        <div class="carousel-overlay d-flex align-items-center justify-content-center">
                            <img src="../upload/product/<?php echo $product['image'] ?>"
                                class="d-block w-100 h-100"
                                style="object-fit: cover;"
                                alt="<?php echo htmlspecialchars($product['label']) ?>">
                            <div class="carousel-caption text-center">
                                <span class="badge bg-primary mb-2">
                                    <?php echo htmlspecialchars($product['category_name']) ?>
                                </span>
                                <h2><?php echo htmlspecialchars($product['label']) ?></h2>
                                <p class="d-none d-md-block">
                                    <?php echo substr($product['description'], 0, 100) . '...' ?>
                                </p>
                                <a href="product.php?id=<?php echo $product['id'] ?>"
                                    class="btn btn-primary">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#featuredCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#featuredCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>

        <!-- Categories Section remains the same -->
        <section id="categories" class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Categories</h2>
                <button class="btn btn-outline-primary d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#categoriesSidebar">
                    <i class="fas fa-filter me-2"></i>Show Categories
                </button>
            </div>

            <div class="row g-4">
                <?php foreach ($categories as $category) { ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="card category-card h-100 text-center">
                            <div class="card-body">
                                <i class="<?php echo $category['icon'] ?> category-icon"></i>
                                <h5 class="card-title"><?php echo htmlspecialchars($category['label']) ?></h5>
                                <p class="card-text text-muted small">Explore our collection</p>
                                <a href="category.php?id=<?php echo $category['id'] ?>"
                                    class="btn btn-outline-primary mt-3">
                                    Browse Products
                                </a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>

        <!-- Products Section with Pagination -->
        <section id="products" class="mb-5">
            <div class="row">
                <!-- Sidebar Filters -->
                <div class="col-lg-3 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
                        </div>
                        <div class="card-body">
                            <!-- Category Filter -->
                            <h6 class="mb-3">Categories</h6>
                            <div class="list-group list-group-flush mb-4">
                                <?php foreach ($categories as $category) { ?>
                                    <a href="category.php?id=<?php echo $category['id'] ?>"
                                        class="list-group-item list-group-item-action">
                                        <i class="<?php echo $category['icon'] ?> me-2"></i>
                                        <?php echo htmlspecialchars($category['label']) ?>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Grid with Pagination -->
                <div class="col-lg-9">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">Latest Products</h2>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-primary">
                                <i class="fas fa-sort me-2"></i>Sort
                            </button>
                        </div>
                    </div>

                    <?php
                    // Pagination configuration
                    $items_per_page = 6;
                    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($current_page - 1) * $items_per_page;

                    // Get total products count
                    $total_products = $pdo->query("SELECT COUNT(*) FROM product")->fetchColumn();
                    $total_pages = ceil($total_products / $items_per_page);

                    // Fetch products for current page
                    $products = $pdo->query("
                        SELECT p.*, c.label as category_name 
                        FROM product p 
                        LEFT JOIN category c ON p.id_category = c.id 
                        ORDER BY p.created_date DESC 
                        LIMIT $offset, $items_per_page
                    ")->fetchAll(PDO::FETCH_ASSOC);
                    ?>

                    <div class="row g-4">
                        <?php foreach ($products as $product) {
                            $maxLength = 100;
                            $description = $product['description'];
                            $p = (strlen($description) > $maxLength)
                                ? substr($description, 0, $maxLength) . '...'
                                : $description;
                        ?>
                            <!-- Product card code remains the same -->
                            <div class="col-md-6 col-lg-4">
                                <div class="card product-card h-100 shadow-sm">
                                    <img src="../upload/product/<?php echo $product['image'] ?>"
                                        class="card-img-top product-img"
                                        alt="<?php echo htmlspecialchars($product['label']) ?>">
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title mb-0 text-truncate">
                                                <?php echo htmlspecialchars($product['label']) ?>
                                            </h5>
                                            <span class="badge bg-primary">
                                                <?php echo htmlspecialchars($product['category_name']) ?>
                                            </span>
                                        </div>
                                        <p class="card-text text-muted small flex-grow-1"><?php echo $p ?></p>
                                        <div class="mt-auto">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="fw-bold text-primary"><?php echo $product['price'] ?> MAD</span>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?php echo (new DateTime($product['created_date']))->format('M j, Y'); ?>
                                                </small>
                                            </div>
                                            <a href="product.php?id=<?php echo $product['id'] ?>"
                                                class="btn btn-primary w-100">
                                                <i class="fas fa-eye me-2"></i>View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1) { ?>
                        <nav aria-label="Product pagination" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?php echo $current_page - 1 ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>

                                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                                    <li class="page-item <?php echo $current_page == $i ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?php echo $i ?>"><?php echo $i ?></a>
                                    </li>
                                <?php } ?>

                                <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?php echo $current_page + 1 ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php } ?>

                    <?php if (empty($products)) { ?>
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No Products Available</h4>
                            <p class="text-muted">Check back later for new products!</p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'footer.php' ?>
</body>

</html>