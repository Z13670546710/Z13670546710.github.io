<?php
require '../tools.func.php';
require '../db.func.php';

header('Content-Type: application/json');

$prefix = getDBPrefix();
$response = [
    'status' => 'success',
    'data' => [
        'types' => [],
        'brands' => []
    ]
];

try {
    // 获取类型数据
    $types = query("SELECT id, name FROM {$prefix}product_type ORDER BY name");
    $response['data']['types'] = $types ?: [];
    
    // 获取品牌数据
    $brands = query("SELECT id, name FROM {$prefix}product_brand ORDER BY name");
    $response['data']['brands'] = $brands ?: [];
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>