<?php
require '../tools.func.php';
require '../db.func.php';

header('Content-Type: application/json');

$keyword = addslashes($_GET['keyword'] ?? '');
$prefix = getDBPrefix();

if (!empty($keyword)) {
    $safeKeyword = mysqli_real_escape_string(connect(), $keyword);
    
    $sql = "SELECT p.name 
            FROM {$prefix}product p
            LEFT JOIN {$prefix}product_brand b ON p.brandId = b.id
            LEFT JOIN {$prefix}product_type t ON p.typeId = t.id
            WHERE p.name LIKE '%$safeKeyword%'
               OR b.name LIKE '%$safeKeyword%'
               OR t.name LIKE '%$safeKeyword%'
            GROUP BY p.name
            LIMIT 5";
    
    $results = query($sql);
    $suggestions = array_column($results, 'name');
    
    echo json_encode($suggestions);
} else {
    echo json_encode([]);
}
?>