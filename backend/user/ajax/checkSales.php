<?php
session_name('serverfund');
session_start();
require_once '../../inc/connect.php';

require_once '../../inc/config.php';

$sql             = "SELECT * FROM products";
$stmt            = $pdo->prepare($sql);
$stmt->execute();
$productsDB = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($productsDB['sale_end'] < $us_date) {
    $sql2 = "UPDATE `products` SET `sale`=NULL, `sale_end`=NULL WHERE `id`=:id";
    $stmt2 = $pdo->prepare($sql2);
    $stmt2->bindValue(':id', $productsDB['id']);
    $result2 = $stmt2->execute();
}

echo $productsDB['sale_end'] .' - '. $us_date;