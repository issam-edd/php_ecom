<?php
session_start();
require 'check.php';

$state = $_GET['state'];
$id = $_GET['id'];

if (!isset($id) || !isset($state) || ($state != 0 && $state != 1)) {
    die('Invalid input.');
}

function update($test, $test1)
{
    require_once 'include/database.php';
    return $pdo->query("UPDATE command SET valid = $test WHERE id = $test1");
}
if (update($state, $id)) {
    header('Location: details_command.php?id=' . urlencode($id));
} else
    die('Failed to update the command.');
