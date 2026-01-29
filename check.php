<?php
if (!isset($_SESSION['user'])) {
    // header("location:".$_SERVER['PHP_SELF']);
    header('location:http://localhost/php_ecom/connection.php');
}
