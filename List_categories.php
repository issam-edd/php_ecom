<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>BMS Ecommerce</title>
</head>
<?php include 'include/nav.php';
require_once 'include/database.php';
require 'check.php'
?>

<body>
    <div class="container py-2">
        <h4>List Categories</h4>
        <?php
        if (isset($_SESSION['category_delete'])) {
        ?>
            <div class="alert alert-success" role="alert"> Category deleted Successfully !</div>
        <?php
            unset($_SESSION['category_delete']);
        }
        if (isset($_SESSION['category_update'])) {
        ?>
            <div class="alert alert-success" role="alert"> Category updated Successfully !</div>
        <?php
            unset($_SESSION['category_update']);
        }
        ?>
        <a href="add_category.php" class="btn btn-primary">+Add Category</a>
        <table class="table table-striped table-hover py-2">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>LABEL</th>
                    <th>Icon</th>
                    <th>DATE</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $categories = $pdo->query('SELECT * FROM category')->fetchAll(PDO::FETCH_ASSOC);
                foreach ($categories as $category) {
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($category['id']) ?></td>
                        <td><?php echo $category['label'] ?></td>
                        <td><i class="<?php echo $category['icon'] ?>"></i></td>
                        <td><?php echo (new DateTime($category['created_date']))->format('m/d/y') ?></td>
                        <td>
                            <a href="edit_category.php?id=<?php echo $category['id'] ?>" class="btn btn-warning" name="add"><i class="fa-solid fa-pen-to-square"></i></a>
                            <a href="delete.php?id=<?php echo $category['id'] ?>&type=category" onclick="return confirm('do you realy want to delete this category:  <?php echo $category['label'] ?>')" class="btn btn-danger" name="add"><i class="fa-solid fa-trash-can"></i></a>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>

</body>

</html>