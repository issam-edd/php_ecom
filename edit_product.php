<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>BMS EditProduct</title>
</head>
<?php include 'include/nav.php';
require 'check.php';
require_once 'include/database.php';
?>

<body>
    <div class="container py-4">
        <h4>Edit Product</h4>
        <?php
        $id = intval($_GET['id'] ?? 0);

        if (!$id) {
            die('Invalid product ID!');
        }
        $product = $pdo->query("SELECT product.*,category.label as 'category_label' FROM product INNER JOIN category ON product.id_category = category.id WHERE product.id = $id")->fetch(PDO::FETCH_ASSOC);
        if (!$product) {
            die('Product not found!');
        }
        define('UPLOAD_DIR', 'upload/product/');
        define('DEFAULT_IMAGE', 'default.jpg');
        if (isset($_POST['update'])) {
            $label = $_POST['label'];
            $price = $_POST['price'];
            $discount = $_POST['discount'] ?? 0;
            $description = $_POST['description'];
            $id_category = $_POST['id_category'];
            $file_name = $product['image'] ?: DEFAULT_IMAGE;

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
            if (!empty($label) && !empty($price) && $price >= 0.00 && !empty($description) && !empty($discount) && $discount >= 0 && $discount <= 100  && !empty($id_category)) {
                require_once 'include/database.php';
                $sqlState = $pdo->prepare('UPDATE product SET label=?,price=?,discount=?,description=?,image=?,id_category=? WHERE id=?');
                $update = $sqlState->execute([$label, $price, $discount, $description, $file_name, $id_category, $id]);
                if ($update) {
                    session_start();
                    $_SESSION['product_update'] = true;
                    header('location:List_products.php');
                    exit;
                }
            } else {
        ?>
                <div class="alert alert-danger" role="alert">
                    label, price, discount, category, id are obligatory !
                </div>
        <?php
            }
        }
        ?>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="label" class="form-label">Label</label>
                <input type="text" class="form-control" id="label" name="label" value="<?php echo $product['label'] ?>">
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" class="form-control" id="price" value="<?php echo $product['price'] ?>" name="price" min="0" step="0.01">
            </div>
            <div class="mb-3">
                <label for="discount" class="form-label">Discount</label>
                <input type="number" class="form-control" id="descount" value="<?php echo $product['discount'] ?>" name="discount" min="0" max="100">
            </div>
            <div class="mb-3">
                <label for="exampledescription" class="form-label">Description</label>
                <textarea class="form-control" id="exampledescription" value="description" name="description"><?php echo $product['description'] ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label" for="inputGroupFile01">Product image</label>
                <input type="file" class="form-control" id="inputGroupFile01" name="image">
                <img class="img-thumbnail mt-2" src="upload/product/<?php echo $product['image'] ?>" width="15%" height="auto" alt="">
            </div>
            <div class="mb-3">
                <label for="descount" class="form-label">Categories</label>
                <?php
                $categories = $pdo->query('SELECT * FROM category')->fetchAll(PDO::FETCH_ASSOC);
                // var_dump($categories);
                ?>
                <select class="form-control" name="id_category">
                    <option value="" disabled>-- Category --</option>
                    <?php
                    foreach ($categories as $category) {
                        $selected = ($category['id'] == $product['id_category']) ? ' selected' : '';
                        echo "<option value=" . $category['id'] . "$selected>" . $category['label'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-warning" name="update">+ Update</button>
        </form>
    </div>

</body>

</html>