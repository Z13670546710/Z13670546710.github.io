<?php
require '../tools.func.php';
require '../db.func.php';

$prefix = getDBPrefix();
$response = ['status' => 'error', 'message' => 'Invalid request'];

// 只处理POST请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取并验证POST数据
    $fullname = htmlspecialchars(trim($_POST['fullname'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $license = htmlspecialchars(trim($_POST['LicenseNumber'] ?? ''));
    $startDate = htmlspecialchars(trim($_POST['LeaseStartDate'] ?? ''));
    $days = intval($_POST['LeaseDays'] ?? 0);
    $pid = intval($_POST['pid'] ?? 0);
    
    // // 基本验证
    // if (empty($fullname) || empty($email) || empty($phone) || empty($license) || 
    //     empty($startDate) || $days <= 0 || $pid <= 0) {
    //     $response['message'] = 'Please fill in all required fields correctly';
    //     header('Content-Type: application/json');
    //     echo json_encode($response);
    //     exit;
    // }

    // // 验证邮箱格式
    // if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    //     $response['message'] = 'Invalid email format';
    //     header('Content-Type: application/json');
    //     echo json_encode($response);
    //     exit;
    // }

    // // 验证手机号格式 (假设是10-11位数字)
    // if (!preg_match('/^\d{10,11}$/', $phone)) {
    //     $response['message'] = 'Phone number must be 10-11 digits';
    //     header('Content-Type: application/json');
    //     echo json_encode($response);
    //     exit;
    // }

    // // 验证驾照号码 (假设是17位数字)
    // if (!preg_match('/^\d{17}$/', $license)) {
    //     $response['message'] = 'License number must be 17 digits';
    //     header('Content-Type: application/json');
    //     echo json_encode($response);
    //     exit;
    // }

    // // 验证开始日期是否在未来
    // if (strtotime($startDate) < time()) {
    //     $response['message'] = 'Lease start date must be in the future';
    //     header('Content-Type: application/json');
    //     echo json_encode($response);
    //     exit;
    // }

    // 获取车辆日租金
    $carSql = "SELECT price FROM {$prefix}product WHERE id = $pid";
    $carResult = query($carSql);
    
    if (empty($carResult)) {
        $response['message'] = 'Vehicle not found';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    $dailyPrice = $carResult[0]['price'];
    $totalPrice = $dailyPrice * $days;
    $createdAt = date('Y-m-d H:i:s');

    // 开始事务
    // beginTransaction();

    try {
        // 1. 插入订单数据
        $orderSql = "INSERT INTO {$prefix}order (
            price, created_at, fullname, email, phone, 
            LeaseDays, LeaseStartDate, LicenseNumber, pid
        ) VALUES (
            '$totalPrice', '$createdAt', '$fullname', '$email', '$phone',
            '$days', '$startDate', '$license', '$pid'
        )";
        
        if (!execute($orderSql)) {
            throw new Exception('Failed to create order');
        }

        // 2. 更新车辆状态为已租用
        $updateSql = "UPDATE {$prefix}product SET status = 0 WHERE id = $pid";
        if (!execute($updateSql)) {
            throw new Exception('Failed to update vehicle status');
        }

        // 提交事务
        // commit();

        $response = [
            'status' => 'success',
            'message' => 'Order submitted successfully'
        ];

    } catch (Exception $e) {
        // 回滚事务
        // rollback();
        $response['message'] = $e->getMessage();
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>