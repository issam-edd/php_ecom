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
        <h4>Detail Command</h4>
        <!-- <a href="add_category.php" class="btn btn-primary">+Add Category</a> -->
        <table class="table table-striped table-hover py-2">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>CLIENT</th>
                    <th>TOTAL</th>
                    <th>DATE</th>
                    <th>DETAIL</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $commands = $pdo->query('SELECT C.*,U.login as "login" FROM command C INNER JOIN user U ON C.id_client=U.id ORDER BY C.created_date DESC')->fetchAll(PDO::FETCH_ASSOC);
                foreach ($commands as $command) {
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($command['id']) ?></td>
                        <td><?php echo $command['login'] ?></td>
                        <td><?php echo floor($command['total']) ?> MAD</td>
                        <td><?php echo (new DateTime($command['created_date']))->format('m/d/y') ?></td>
                        <td>
                            <a href="details_command.php?id=<?php echo $command['id'] ?>" class="btn btn-dark" name="add"><i class="fa-solid fa-eye"></i></a>
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