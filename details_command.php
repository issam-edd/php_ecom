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
        <a href="List_commands.php" class="btn btn-primary"><i class=" fa-solid fa-circle-chevron-left"></i> List Commands</a>
        <table class="table table-striped table-hover py-4 my-4">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>CLIENT</th>
                    <th>TOTAL</th>
                    <th>DATE</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
                    echo "Invalid command ID.";
                    exit;
                }
                $id = $_GET['id'];
                $command = $pdo->query("SELECT C.*,U.login as 'login' FROM command C INNER JOIN user U ON C.id_client=U.id WHERE C.id =$id")->fetch(PDO::FETCH_ASSOC);
                if (!$command) {
                    echo "Command not found.";
                    exit;
                }
                function is_valid($test): bool
                {
                    return $test === 0;
                }
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($command['id']) ?></td>
                    <td><?php echo $command['login'] ?></td>
                    <td><?php echo $command['total'] ?> MAD</td>
                    <td><?php echo (new DateTime($command['created_date']))->format('m/d/y') ?></td>
                    <td>
                        <?= is_valid($command['valid'])
                            ? '<a href="validate_command.php?id=' . htmlspecialchars($id) . '&state=1" class="btn btn-success"><i class="fa-solid fa-check"></i> Validate Command</a>'
                            : '<a href="validate_command.php?id=' . htmlspecialchars($id) . '&state=0" class="btn btn-danger" onclick="return confirm(\'Are you sure you want to cancel your Command !\')"><i class="fa-solid fa-check"></i> Cancel Command</a>' ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <h4>List Commands</h4>
        <table class="table table-striped table-hover py-4 my-4">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>PRODUCT</th>
                    <th>PRICE</th>
                    <th>QUANTITY</th>
                    <th>TOTAL</th>
                    <th>DATE</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $id = $_GET['id'];
                $line_commands = $pdo->query("SELECT l.*,p.label as 'label' FROM line_command l INNER JOIN product p ON l.id_product=p.id WHERE l.id_command = $id")->fetchAll(PDO::FETCH_ASSOC);
                if (empty($line_commands)) {
                    echo 'NOT EXIST !!';
                    exit;
                }
                foreach ($line_commands as $line_command) {
                    $test = $line_command['id_product'];
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($line_command['id']) ?></td>

                        <td><a href="/php_ecom/front/product.php?id=<?php echo $test ?>" target="_blank"><?php echo $line_command['label'] ?></a></td>
                        <td><?php echo $line_command['price'] ?></td>
                        <td>x <?php echo $line_command['quantity'] ?></td>
                        <td><?php echo $line_command['total'] ?></td>
                        <td><?php echo (new DateTime($line_command['created_date']))->format('m/d/y') ?></td>
                    </tr>
                <?php
                }
                ?>
            </tbody>

        </table>
    </div>
    </div>

</body>

</html>