<?php
require '../tools.func.php';
require '../db.func.php';

header('Content-Type: application/json');

$prefix = getDBPrefix();

// 获取查询参数
$keyword = addslashes($_GET['keyword'] ?? '');
$brandId = intval($_GET['brand'] ?? 0);
$typeId = intval($_GET['type'] ?? 0);

// 构建基础查询
$sql = "SELECT p.*, b.name as brand_name, t.name as type_name 
        FROM {$prefix}product p
        LEFT JOIN {$prefix}product_brand b ON p.brandId = b.id
        LEFT JOIN {$prefix}product_type t ON p.typeId = t.id
        WHERE 1=1";

// 添加搜索条件
if (!empty($keyword)) {
    $safeKeyword = mysqli_real_escape_string(connect(), $keyword);
    $sql .= " AND (p.name LIKE '%$safeKeyword%' 
              OR p.description LIKE '%$safeKeyword%'
              OR b.name LIKE '%$safeKeyword%'
              OR t.name LIKE '%$safeKeyword%')";
}

// 添加品牌筛选
if ($brandId > 0) {
    $sql .= " AND p.brandId = $brandId";
}

// 添加类型筛选
if ($typeId > 0) {
    $sql .= " AND p.typeId = $typeId";
}

$sql .= " ORDER BY p.created_at DESC";

// 执行查询
$data = query($sql);

echo json_encode([
    'status' => 'success',
    'data' => $data
]);