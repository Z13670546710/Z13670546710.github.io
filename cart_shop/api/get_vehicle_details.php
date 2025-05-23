<?php
require '../tools.func.php';
require '../db.func.php';

$prefix = getDBPrefix();
$response = ['status' => 'error', 'message' => 'Invalid request'];

// 只处理带ID参数的请求
if (isset($_GET['id'])) {
    $carId = intval($_GET['id']);
    
    // 验证ID有效性
    if ($carId <= 0) {
        $response['message'] = 'Invalid vehicle ID';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    $sql = "SELECT * FROM {$prefix}product WHERE id = $carId";
    $result = query($sql);
    
    if (!empty($result)) {
        $car = $result[0];
        // 添加额外验证
        // if ($car['status'] == 0) {
        //     $response['message'] = 'Vehicle already rented';
        // } else {
        //     $response = [
        //         'status' => 'success',
        //         'data' => $car
        //     ];
        // }
        $response = [
            'status' => 'success',
            'data' => $car
        ];
    } else {
        $response['message'] = 'Vehicle not found';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>