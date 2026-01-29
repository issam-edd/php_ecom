<?php
if (session_status() === PHP_SESSION_NONE) {
    // Session has not been started yet
    session_start();
}
$connect = false;
if (isset($_SESSION['user'])) {
    $connect = true;
}
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css" integrity="sha512-9xKTRVabjVeZmc+GUW8GgSmcREDunMM+Dt/GrzchfN8tkwHizc5RP4Ok/MXFFy5rIjJjzhndFScTceq5e6GvVQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<nav class="navbar navbar-expand-lg bg-white sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-store text-primary me-2"></i>BMS
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#categories">Categories</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#products">Products</a>
                </li>
            </ul>
            <?php
            $user_id = $_SESSION['user']['id'] ?? null;
            if (!isset($_SESSION['cart'][$user_id])) {
                $qnty = 0;
            } else {
                $qnty =  count($_SESSION['cart'][$user_id]);
            }
            ?>
            <div class="d-flex align-items-center gap-3">
                <a href="cart.php" class="btn btn-outline-primary cart-btn" aria-label="View your shopping cart">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge badge bg-danger rounded-pill"><?php echo $qnty ?></span>
                </a>
                <?php if ($connect) { ?>
                    <a class="btn btn-outline-danger" href="../logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                <?php } else { ?>
                    <a class="btn btn-primary" href="../connection.php">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
</nav>