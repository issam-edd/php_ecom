<?php
session_start();
$connect = isset($_SESSION['user']);
$currentPage = basename($_SERVER['PHP_SELF'] ?? 'index.php');

// Dynamic active state for pages
$activePages = [
    'categories' => ['add_category.php', 'List_categories.php'],
    'products' => ['add_product.php', 'List_products.php'],
];

function isActive($page)
{
    global $currentPage;
    return ($currentPage === $page) ? 'active' : '';
}

function isDropdownActive($group)
{
    global $currentPage, $activePages;
    return in_array($currentPage, $activePages[$group] ?? []) ? 'active' : '';
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css"
    integrity="sha512-9xKTRVabjVeZmc+GUW8GgSmcREDunMM+Dt/GrzchfN8tkwHizc5RP4Ok/MXFFy5rIjJjzhndFScTceq5e6GvVQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    .navbar-custom {
        background-color: #f8f9fa;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .nav-link:hover {
        color: #007bff;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-light navbar-custom">
    <div class="container-fluid">
        <a class="navbar-brand" href="admin.php">
            <i class="fas fa-store text-primary me-2"></i>BMS
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <?php if ($connect): ?>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('index.php') ?>" href="index.php">Add user</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= isDropdownActive('categories') ?>" href="#" id="categoriesDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Manage categories">
                            Categories
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
                            <li><a class="dropdown-item <?= isActive('add_category.php') ?>" href="add_category.php">Add category</a></li>
                            <li><a class="dropdown-item <?= isActive('List_categories.php') ?>" href="List_categories.php">List categories</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= isDropdownActive('products') ?>" href="#" id="productsDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Manage products">
                            Products
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="productsDropdown">
                            <li><a class="dropdown-item <?= isActive('add_product.php') ?>" href="add_product.php">Add product</a></li>
                            <li><a class="dropdown-item <?= isActive('List_products.php') ?>" href="List_products.php">List products</a></li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= isActive('List_commands.php') ?>" href="List_commands.php">List commands</a>
                    </li>

                    <li class="nav-item">
                        <form method="POST" action="logout.php" style="display:inline;">
                            <button class="nav-link btn btn-link text-danger" type="submit">Log out</button>
                        </form>
                    </li>
                </ul>
            <?php else: ?>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="connection.php">Log in</a>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>