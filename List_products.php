<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }

        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }

        .card-footer {
            background-color: transparent;
            border-top: none;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }
    </style>
</head>

<body>
    <?php
    include 'include/nav.php';
    require_once 'include/database.php';
    require 'check.php';

    // Pagination setup
    $itemsPerPage = 10;
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $offset = ($page - 1) * $itemsPerPage;

    // Count total products
    $totalProductsQuery = $pdo->query("SELECT COUNT(*) FROM product")->fetchColumn();
    $totalPages = ceil($totalProductsQuery / $itemsPerPage);

    // Fetch products with pagination
    $stmt = $pdo->prepare("
        SELECT product.*, category.label as category_label 
        FROM product 
        INNER JOIN category ON product.id_category = category.id 
        ORDER BY product.created_date DESC 
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="container-fluid px-4 py-4">
        <!-- Header and Add Product Button -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h1 class="h2 mb-0">Product Management</h1>
                <p class="text-muted">Manage and view your products</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="add_product.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Product
                </a>
            </div>
        </div>

        <!-- Success Messages -->
        <?php
        $messages = [
            'product_delete' => 'Product deleted successfully!',
            'product_update' => 'Product updated successfully!'
        ];

        foreach ($messages as $key => $message) {
            if (isset($_SESSION[$key])) {
                echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        $message
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                      </div>";
                unset($_SESSION[$key]);
            }
        }
        ?>

        <!-- Products Table -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Discount</th>
                                <th>Net Price</th>
                                <th>Category</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product):
                                $price = $product['price'];
                                $discount = $product['discount'];
                                $netPrice = $price - (($price * $discount) / 100);
                            ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td>
                                        <img src="upload/product/<?php echo $product['image']; ?>"
                                            class="img-thumbnail"
                                            style="width: 80px; height: 80px; object-fit: cover;"
                                            alt="<?php echo htmlspecialchars($product['label']); ?>">
                                    </td>
                                    <td><?php echo htmlspecialchars($product['label']); ?></td>
                                    <td><?php echo number_format($price, 2) . " MAD"; ?></td>
                                    <td><?php echo $product['discount'] . " %"; ?></td>
                                    <td><?php echo number_format($netPrice, 2) . " MAD"; ?></td>
                                    <td><?php echo htmlspecialchars($product['category_label']); ?></td>
                                    <td><?php echo (new DateTime($product['created_date']))->format('d/m/y'); ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="edit_product.php?id=<?php echo $product['id']; ?>"
                                                class="btn btn-sm btn-outline-warning"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete.php?id=<?php echo $product['id']; ?>&type=product"
                                                onclick="return confirm('Do you really want to delete this product: <?php echo htmlspecialchars($product['label']); ?>')"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Product pagination" class="mt-3">
                <ul class="pagination justify-content-center">
                    <?php
                    // Previous page link
                    if ($page > 1) {
                        echo "<li class='page-item'><a class='page-link' href='?page=" . ($page - 1) . "'>Previous</a></li>";
                    }

                    // Page numbers
                    for ($i = 1; $i <= $totalPages; $i++) {
                        $activeClass = ($i == $page) ? 'active' : '';
                        echo "<li class='page-item $activeClass'><a class='page-link' href='?page=$i'>$i</a></li>";
                    }

                    // Next page link
                    if ($page < $totalPages) {
                        echo "<li class='page-item'><a class='page-link' href='?page=" . ($page + 1) . "'>Next</a></li>";
                    }
                    ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>