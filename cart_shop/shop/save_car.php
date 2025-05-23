<?php
// save_car.php
require '../tools.func.php';
require '../db.func.php';

$prefix = getDBPrefix();
$carId = intval($_GET['id'] ?? 0);

if ($carId > 0) {
    $sql = "SELECT * FROM {$prefix}product WHERE id = $carId";
    $car = query($sql)[0] ?? [];
    
    if (!empty($car)) {
        setcookie('last_viewed_car', json_encode($car), time()+86400*30, '/');
        header("Location: reserve.php");
        exit;
    }
}

header("Location: index.php");
exit;