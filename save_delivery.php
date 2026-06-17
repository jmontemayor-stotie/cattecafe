<?php
header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['customer_id'], $input['delivery'], $input['cart_items'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data parameters.']);
    exit();
}

$customer_id    = intval($input['customer_id']);
$cart_items     = $input['cart_items'];
$address        = trim($input['delivery']['address']);
$recipient      = trim($input['delivery']['recipient']);
$contact_number = trim($input['delivery']['contact']);
$payment_method = trim($input['delivery']['payment']);

if (empty($cart_items)) {
    echo json_encode(['success' => false, 'message' => 'Cannot process an empty cart.']);
    exit();
}

try {
    mysqli_begin_transaction($conn);

    $total_amount = 0.00;
    foreach ($cart_items as $item) {
        $total_amount += floatval($item['price']) * intval($item['quantity']);
    }

    $order_type   = 'Delivery';
    $order_date   = date('Y-m-d');
    $order_status = 'Pending';

    $stmt = mysqli_prepare($conn, "INSERT INTO order_tbl (customer_id, order_type, order_date, total_amount, order_status) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "issds", $customer_id, $order_type, $order_date, $total_amount, $order_status);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to create order: " . mysqli_stmt_error($stmt));
    }
    $order_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    $d_stmt = mysqli_prepare($conn, "INSERT INTO orderdetails_tbl (order_id, item_id, quantity, sub_total) VALUES (?, ?, ?, ?)");
    foreach ($cart_items as $item) {
        $item_id   = intval($item['id']);
        $quantity  = intval($item['quantity']);
        $sub_total = floatval($item['price']) * $quantity;
        mysqli_stmt_bind_param($d_stmt, "iiid", $order_id, $item_id, $quantity, $sub_total);
        if (!mysqli_stmt_execute($d_stmt)) {
            throw new Exception("Failed to insert order item: " . mysqli_stmt_error($d_stmt));
        }
    }
    mysqli_stmt_close($d_stmt);

    $stock_stmt = mysqli_prepare($conn, "
        UPDATE stock_tbl
        SET quantity_available = GREATEST(0, quantity_available - ?)
        WHERE menuitem_id = ?
    ");
    foreach ($cart_items as $item) {
        $quantity = intval($item['quantity']);
        $item_id  = intval($item['id']);
        mysqli_stmt_bind_param($stock_stmt, "ii", $quantity, $item_id);
        if (!mysqli_stmt_execute($stock_stmt)) {
            throw new Exception("Failed to update stock for item {$item_id}: " . mysqli_stmt_error($stock_stmt));
        }
    }
    mysqli_stmt_close($stock_stmt);

    $paypal_details    = $input['delivery']['paypal_details'] ?? null;
    $db_payment_method = ($payment_method === 'Online' && $paypal_details !== null) ? 'PayPal' : 'Cash';
    $payment_status    = ($db_payment_method === 'PayPal') ? 'Completed' : 'Pending';

    $p_stmt = mysqli_prepare($conn, "INSERT INTO payment_tbl (order_id, customer_id, payment_method, amount, payment_status) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($p_stmt, "iisds", $order_id, $customer_id, $db_payment_method, $total_amount, $payment_status);
    if (!mysqli_stmt_execute($p_stmt)) {
        throw new Exception("Failed to record payment: " . mysqli_stmt_error($p_stmt));
    }
    mysqli_stmt_close($p_stmt);

    $del_stmt = mysqli_prepare($conn, "INSERT INTO delivery_tbl (customer_id, delivery_address, recipient_name, contact_number, delivery_status) VALUES (?, ?, ?, ?, ?)");
    $delivery_status = 'Pending';
    mysqli_stmt_bind_param($del_stmt, "issss", $customer_id, $address, $recipient, $contact_number, $delivery_status);
    if (!mysqli_stmt_execute($del_stmt)) {
        throw new Exception("Failed to create delivery record: " . mysqli_stmt_error($del_stmt));
    }
    mysqli_stmt_close($del_stmt);

    mysqli_commit($conn);

    echo json_encode([
        'success'  => true,
        'message'  => 'Order placed successfully.',
        'order_id' => $order_id
    ]);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>