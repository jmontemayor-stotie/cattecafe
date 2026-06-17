<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = (int)$_GET['order_id'];
$user_id = (int)$_SESSION['user_id'];

$stmt = $conn->prepare("SELECT total_amount, order_status, order_date FROM order_tbl WHERE order_id = ? AND customer_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    die("Order reference not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed! - Cat Cafe Lounge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background-color: #fcfbfa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .success-card { max-width: 550px; margin: 80px auto; border: none; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
        .success-icon { font-size: 4rem; color: #198754; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card success-card p-5 text-center bg-white">
            <div class="mb-3">
                <i class="bi bi-check-circle-fill success-icon"></i>
            </div>
            <div class="h2 fw-bold text-dark mb-2">Meow-tastic! Order Placed!</div>
            <div class="text-muted">Your order has been recorded and sent straight to our cafe kitchen.</div>
            
            <div class="bg-light rounded-3 p-4 my-4 text-start">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Order ID Ref:</span>
                    <strong class="text-dark">#<?= $order_id; ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Scheduled Date:</span>
                    <strong class="text-dark"><?= htmlspecialchars($order['order_date']); ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Fulfillment Status:</span>
                    <span class="badge bg-warning text-dark fw-medium"><?= htmlspecialchars($order['order_status']); ?></span>
                </div>
                <hr class="text-muted my-3" style="border-style: dashed;">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted fw-bold">Total Amount Due:</span>
                    <div class="h4 fw-bold text-dark mb-0">₱<?= number_format($order['total_amount'], 2); ?></div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <a href="index.php" class="btn btn-dark py-3 rounded-3 fw-medium">Back to Homepage</a>
            </div>
        </div>
    </div>
</body>
</html>