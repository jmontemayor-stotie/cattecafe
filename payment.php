<?php
session_start();
require_once 'config.php'; 

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error_message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'place_order') {
    
    $user_id = (int)$_SESSION['user_id'];
    $order_type = $conn->real_escape_string($_POST['order_type']);
    $booking_date = !empty($_POST['booking_date']) ? $conn->real_escape_string($_POST['booking_date']) : date('Y-m-d');
    $booking_time = !empty($_POST['booking_time']) ? $conn->real_escape_string($_POST['booking_time']) : date('H:i:s');
    $cat_id = !empty($_POST['cat_id']) ? (int)$_POST['cat_id'] : null;
    
    $payment_method = !empty($_POST['payment_method']) ? $conn->real_escape_string($_POST['payment_method']) : 'Cash';
    
    $cart_json = $_POST['cart_json'] ?? '[]';
    $cart_items = json_decode($cart_json, true);
    
    if (!empty($cart_items)) {
        $conn->begin_transaction();
        
        try {
            $total_amount = 0.00;
            foreach ($cart_items as $item) {
                $total_amount += (float)$item['price'] * (int)$item['quantity'];
            }
            
            $order_status = "Pending";
            $payment_status = ($payment_method === 'Cash') ? 'Pending' : 'Paid';

            $order_stmt = $conn->prepare("
                INSERT INTO order_tbl (customer_id, order_type, order_date, total_amount, order_status) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $order_stmt->bind_param("issss", $user_id, $order_type, $booking_date, $total_amount, $order_status);
            $order_stmt->execute();
            $order_id = $conn->insert_id;
            $order_stmt->close();

            $item_stmt = $conn->prepare("
                INSERT INTO orderdetails_tbl (order_id, item_id, quantity, sub_total) 
                VALUES (?, ?, ?, ?)
            ");
            
            foreach ($cart_items as $item) {
                $item_id = (int)$item['id'];
                $qty = (int)$item['quantity'];
                $sub_total = (float)$item['price'] * $qty;

                $item_stmt->bind_param("iiid", $order_id, $item_id, $qty, $sub_total);
                $item_stmt->execute();
            }
            $item_stmt->close();
            
            $pay_stmt = $conn->prepare("
                INSERT INTO payment_tbl (order_id, customer_id, payment_method, amount, payment_status) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $pay_stmt->bind_param("iisss", $order_id, $user_id, $payment_method, $total_amount, $payment_status);
            $pay_stmt->execute();
            $pay_stmt->close();

            if ($order_type === 'Pre-order' && !empty($cat_id)) {
                $cat_stmt = $conn->prepare("
                    INSERT INTO catbooking_tbl (customer_id, cat_id, order_id, booking_date, booking_time) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $cat_stmt->bind_param("iiiss", $user_id, $cat_id, $order_id, $booking_date, $booking_time);
                $cat_stmt->execute();
                $cat_stmt->close();
            }

            $conn->commit();

            header("Location: seating.php?clear_cart=1&id=" . $order_id);
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $error_message = "Transaction Failed: " . $e->getMessage();
        }
    } else {
        $error_message = "Your shopping cart array is empty.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Order Review - Cat Cafe Lounge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #fcfbfa; }
        .summary-card { border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.04); }
        .review-badge { background-color: #f8f9fa; border: 1px solid #eaeaea; border-radius: 12px; padding: 16px; }
        .item-row { border-bottom: 1px dashed #eaeaea; padding-bottom: 12px; margin-bottom: 12px; }
        .item-row:last-child { border-bottom: none; }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container py-5">
        <div class="text-center mb-5">
            <div style="font-family: 'Playfair Display', serif;" class="h1 fw-bold text-dark">Review Your Order</div>
            <p class="text-muted">Please double-check your booking parameters and cart treats before confirming payment.</p>
        </div>

        <?php if ($error_message): ?>
            <div class="alert alert-danger mx-auto rounded-3 shadow-sm mb-4" style="max-width: 600px;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="row g-4 justify-content-center">
            <div class="col-lg-6">
                <div class="card summary-card p-4 h-100">
                    <div style="font-family: 'Playfair Display', serif;" class="h4 fw-bold text-dark mb-4"><i class="bi bi-calendar-check text-muted me-2"></i>Reservation Metadata</div>
                    
                    <div class="d-flex flex-column gap-3">
                        <div class="review-badge">
                            <span class="text-muted d-block small text-uppercase tracking-wider fw-semibold">Service Option</span>
                            <strong class="fs-5 text-dark" id="viewOrderType">Loading...</strong>
                        </div>

                        <div id="loungeMetaGroup" style="display: none;" class="d-flex flex-column gap-3">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="review-badge">
                                        <span class="text-muted d-block small text-uppercase tracking-wider fw-semibold">Date</span>
                                        <strong class="text-dark" id="viewDate">-</strong>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="review-badge">
                                        <span class="text-muted d-block small text-uppercase tracking-wider fw-semibold">Arrival Time Slot</span>
                                        <strong class="text-dark" id="viewTime">-</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="review-badge">
                                        <span class="text-muted d-block small text-uppercase tracking-wider fw-semibold">Cat Companion Host</span>
                                        <strong class="text-dark" id="viewCatName">None Selected</strong>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="review-badge">
                                        <span class="text-muted d-block small text-uppercase tracking-wider fw-semibold">Assigned Seating</span>
                                        <strong class="text-dark" id="viewTableSeat">-</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card summary-card p-4">
                    <div style="font-family: 'Playfair Display', serif;" class="h4 fw-bold text-dark mb-4"><i class="bi bi-basket text-muted me-2"></i>Ordered Items</div>

                    <div id="cartItemsContainer" class="mb-4" style="max-height: 250px; overflow-y: auto;"></div>

                    <form id="finalOrderForm" method="POST" action="">
                        <div class="mb-4">
                            <label class="form-label small text-muted text-uppercase tracking-wider fw-bold mb-2">Select Payment Method</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-credit-card-2-back"></i></span>
                                <select class="form-select border-start-0 ps-0 fw-medium" name="payment_method" id="paymentMethodSelect">
                                    <option value="Cash" selected>Cash Payment at Counter</option>
                                    <option value="GCash">GCash Transfer</option>
                                    <option value="Paypal">PayPay Wallet</option>
                                    <option value="Credit Card">Credit / Debit Card</option>
                                </select>
                            </div>
                        </div>

                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <span class="text-muted d-block small fw-medium">GRAND TOTAL</span>
                                    <span class="text-muted text-uppercase small" style="font-size: 10px;">VAT Inclusive</span>
                                </div>
                                <div class="h2 fw-bold text-dark mb-0" id="viewGrandTotal">₱0.00</div>
                            </div>

                            <input type="hidden" name="action" value="place_order">
                            <input type="hidden" name="order_type" id="dbOrderType">
                            <input type="hidden" name="booking_date" id="dbDate">
                            <input type="hidden" name="booking_time" id="dbTime">
                            <input type="hidden" name="cat_id" id="dbCatId">
                            <input type="hidden" name="cart_json" id="dbCartJson">

                            <button type="submit" class="btn btn-dark w-100 py-3 rounded-3 fw-medium shadow-sm fs-5 d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-shield-check"></i> Confirm & Place Order
                            </button>
                        </div>
                    </form>
                    
                    <a href="index.php" class="btn btn-link btn-sm text-muted d-block text-center mt-3 text-decoration-none">
                        <i class="bi bi-arrow-left"></i> Cancel and return to home
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if(urlParams.get('clear_cart') === '1') {
                localStorage.removeItem('cafe_bag');
                localStorage.removeItem('booking_date');
                localStorage.removeItem('booking_time');
                localStorage.removeItem('cat_id');
                localStorage.removeItem('summary_cat_name');
                localStorage.removeItem('selected_table_seat');
            }

            const orderType = localStorage.getItem('order_type') || 'Pre-order';
            const bookingDate = localStorage.getItem('booking_date');
            const bookingTime = localStorage.getItem('booking_time');
            const catId = localStorage.getItem('cat_id');
            const catName = localStorage.getItem('summary_cat_name');
            const tableSeat = localStorage.getItem('selected_table_seat') || 'Not Selected';
            let currentBag = JSON.parse(localStorage.getItem('cafe_bag')) || [];

            document.getElementById('viewOrderType').innerText = orderType;
            
            if (orderType === 'Pre-order') {
                document.getElementById('loungeMetaGroup').style.display = 'block';
                document.getElementById('viewDate').innerText = bookingDate || 'Not Configured';
                document.getElementById('viewTime').innerText = bookingTime || 'Not Configured';
                document.getElementById('viewCatName').innerText = catName || 'Standard Seat (No Host)';
                document.getElementById('viewTableSeat').innerText = tableSeat;
            }

            const itemsContainer = document.getElementById('cartItemsContainer');
            let grandTotal = 0;

            if (currentBag.length === 0) {
                itemsContainer.innerHTML = '<div class="text-center text-muted py-4"><i class="bi bi-cart-x fs-2 d-block mb-2"></i>Your bag is completely empty!</div>';
                document.querySelector('button[type="submit"]').disabled = true;
                return;
            }

            let htmlPayload = "";
            currentBag.forEach(item => {
                let rowTotal = item.price * item.quantity;
                grandTotal += rowTotal;

                htmlPayload += `
                    <div class="d-flex justify-content-between align-items-center item-row">
                        <div>
                            <div class="h6 mb-0 fw-bold text-dark small">${item.name}</div>
                            <span class="text-muted small">₱${parseFloat(item.price).toFixed(2)} × ${item.quantity}</span>
                        </div>
                        <span class="fw-bold text-dark small">₱${rowTotal.toFixed(2)}</span>
                    </div>
                `;
            });

            itemsContainer.innerHTML = htmlPayload;
            document.getElementById('viewGrandTotal').innerText = "₱" + grandTotal.toLocaleString('en-US', {
                minimumFractionDigits: 2, maximumFractionDigits: 2
            });

            document.getElementById('dbOrderType').value = orderType;
            document.getElementById('dbDate').value = bookingDate || '';
            document.getElementById('dbTime').value = bookingTime || '';
            document.getElementById('dbCatId').value = catId || '';
            document.getElementById('dbCartJson').value = JSON.stringify(currentBag);
        });

        document.getElementById('finalOrderForm').addEventListener('submit', () => {
            // Clear local storage cache flags right as submission hits the backend transaction engine
            localStorage.removeItem('cafe_bag');
            localStorage.removeItem('booking_date');
            localStorage.removeItem('booking_time');
            localStorage.removeItem('cat_id');
            localStorage.removeItem('summary_cat_name');
            localStorage.removeItem('selected_table_seat');
        });
    </script>
</body>
</html>