<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>BMS Connection</title>
</head>
<?php include 'include/nav.php' ?>

<body>
    <div class="container">
        <?php
        require 'check.php'
        ?>
        <h4> Wellcome <?php echo $_SESSION['user']['login']; ?> </h4>
    </div>
</body>

</html>