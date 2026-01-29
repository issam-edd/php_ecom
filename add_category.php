<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>BMS AddCategory</title>
</head>
<?php include 'include/nav.php';
require 'check.php'
?>

<body>
    <div class="container">
        <h4>Add Category</h4>
        <?php
        if (isset($_POST['add'])) {
            $label = $_POST['label'];
            $icon = $_POST['icon'];

            if (!empty($label) && !empty($icon)) {
                require_once 'include/database.php';
                $date = date('Y-m-d');
                $sqlState = $pdo->prepare('INSERT INTO category(label,icon,created_date) VALUES(?,?,?)');
                $sqlState->execute([$label, $icon, $date]);
        ?>
                <div class="alert alert-success" role="alert">
                    Category added successfully
                </div>
            <?php
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
                <label for="exampleInputlabel" class="form-label">Label</label>
                <input type="text" class="form-control" id="exampleInputlabel" name="label">
            </div>
            <div class="mb-3">
                <label for="exampleInputicon" class="form-label">Icon</label>
                <input type="text" class="form-control" id="exampleInputicon" name="icon">
            </div>
            <button type="submit" class="btn btn-primary" name="add">+ Add</button>
        </form>
    </div>
</body>

</html>