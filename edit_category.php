<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>BMS EditCategory</title>
</head>
<?php include 'include/nav.php';
require 'check.php';
require_once 'include/database.php';
?>

<body>
    <div class="container">
        <h4>Edit Category</h4>
        <?php
        $id = intval($_GET['id'] ?? 0);

        if (!$id) {
            die('Invalid category ID!');
        }
        $category = $pdo->query("SELECT * FROM category WHERE id = $id")->fetch(PDO::FETCH_ASSOC);
        if (!$category) {
            die('Category not found!');
        }
        if (isset($_POST['update'])) {
            $label = $_POST['label'];
            $icon = $_POST['icon'];
            if (!empty($label) && !empty($icon)) {
                $smt = $pdo->prepare("UPDATE category SET label=?,icon=? WHERE id=?");
                $update = $smt->execute([$label, $icon, $id]);
                if ($update) {
                    session_start();
                    $_SESSION['category_update'] = true;
                    header('location:List_categories.php');
                }
            } else {
        ?>
                <div class="alert alert-danger" role="alert">
                    label , icon are obligatory !
                </div>
        <?php
            }
        }
        ?>
        <form method="post">
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Label</label>
                <input type="text" class="form-control" id="exampleInputEmail1" name="label" value="<?php echo $category['label'] ?>">
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Icon</label>
                <input type="text" class="form-control" id="exampleInputPassword1" value="<?php echo $category['icon'] ?>" name=" icon">
            </div>
            <button type="submit" class="btn btn-warning" name="update">+ Update</button>
        </form>
    </div>

</body>

</html>