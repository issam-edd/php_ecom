<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php
    include 'include/nav.php';
    require_once 'include/database.php';
    require 'check.php';

    define('UPLOAD_DIR', 'upload/product/');
    define('DEFAULT_IMAGE', 'default.jpg');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
        $label = htmlspecialchars($_POST['label'] ?? '', ENT_QUOTES, 'UTF-8');
        $price = floatval($_POST['price'] ?? 0);
        $discount = intval($_POST['discount'] ?? 0);
        $description = htmlspecialchars($_POST['description'] ?? '', ENT_QUOTES, 'UTF-8');
        $id_category = intval($_POST['id_category'] ?? 0);
        $file_name = DEFAULT_IMAGE;

        // File upload handling
        if (!empty($_FILES['image']['name'])) {
            $image = $_FILES['image'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            $maxFileSize = 2 * 1024 * 1024; // 2MB

            if (in_array($image['type'], $allowedTypes) && $image['size'] <= $maxFileSize) {
                $file_name = uniqid() . '-' . basename($image['name']);
                move_uploaded_file($image['tmp_name'], UPLOAD_DIR . $file_name);
            } else {
                echo '<div class="alert alert-danger">Invalid file type or size. Only images under 2MB are allowed.</div>';
                exit;
            }
        }

        // Validate inputs
        if ($label && $price >= 0 && $discount >= 0 && $discount <= 100 && $description && $id_category) {
            $sqlState = $pdo->prepare('INSERT INTO product (label, price, discount, description, image, id_category) VALUES (?, ?, ?, ?, ?, ?)');
            if ($sqlState->execute([$label, $price, $discount, $description, $file_name, $id_category])) {
                echo '<div class="alert alert-success">Product added successfully</div>';
            } else {
                echo '<div class="alert alert-danger">Failed to add product. Try again later.</div>';
            }
        } else {
            echo '<div class="alert alert-danger">All fields are required, and values must be valid!</div>';
        }
    }
    ?>

    <div class="container mt-4 py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="mb-4">Add New Product</h2>
                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="label" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="label" name="label" required>
                    </div>

                    <div class="mb-3">
                        <label for="id_category" class="form-label">Category</label>
                        <select class="form-select" name="id_category" required>
                            <option value="">Select Category</option>
                            <?php
                            $categories = $pdo->query('SELECT * FROM category')->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($categories as $category) {
                                echo "<option value='" . $category['id'] . "'>" .
                                    htmlspecialchars($category['label'], ENT_QUOTES, 'UTF-8') .
                                    "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="discount" class="form-label">Discount (%)</label>
                            <input type="number" class="form-control" id="discount" name="discount" min="0" max="100">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Product Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    </div>

                    <button type="submit" class="btn btn-primary" name="add">Add Product</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>