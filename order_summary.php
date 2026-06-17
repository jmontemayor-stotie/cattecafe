<?php
session_start();
require_once 'config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'place_order') {

    $user_id = (int)$_SESSION['user_id'];
    $order_type = $conn->real_escape_string($_POST['order_type']);
    $booking_date = !empty($_POST['booking_date']) ? $conn->real_escape_string($_POST['booking_date']) : date('Y-m-d');
    $booking_time = !empty($_POST['booking_time']) ? $conn->real_escape_string($_POST['booking_time']) : date('H:i:s');
    $cat_id = !empty($_POST['cat_id']) ? intval($_POST['cat_id']) : null;
    $payment_method = !empty($_POST['payment_method']) ? $conn->real_escape_string($_POST['payment_method']) : 'Cash';
    $cart_items = json_decode($_POST['cart_json'], true);

    if (!empty($cart_items)) {
        $conn->begin_transaction();

        try {
            $total_amount = 0.00;
            foreach ($cart_items as $item) {
                $total_amount += (float)$item['price'] * (int)$item['quantity'];
            }

            $order_status   = ($payment_method === 'Cash') ? 'Pending'  : 'Confirmed';
            $payment_status = ($payment_method === 'Cash') ? 'Pending'  : 'Paid';

            $stmt = $conn->prepare("INSERT INTO order_tbl (customer_id, order_type, order_date, total_amount, order_status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issds", $user_id, $order_type, $booking_date, $total_amount, $order_status);
            $stmt->execute();
            $order_id = $conn->insert_id;
            $stmt->close();

            $item_stmt = $conn->prepare("INSERT INTO orderdetails_tbl (order_id, item_id, quantity, sub_total) VALUES (?, ?, ?, ?)");
            foreach ($cart_items as $item) {
                $item_id   = intval($item['id']);
                $quantity  = intval($item['quantity']);
                $sub_total = floatval($item['price']) * $quantity;
                $item_stmt->bind_param("iiid", $order_id, $item_id, $quantity, $sub_total);
                $item_stmt->execute();
            }
            $item_stmt->close();

            $stock_stmt = $conn->prepare("
                UPDATE stock_tbl
                SET quantity_available = GREATEST(0, quantity_available - ?)
                WHERE menuitem_id = ?
            ");
            foreach ($cart_items as $item) {
                $quantity = intval($item['quantity']);
                $item_id  = intval($item['id']);
                $stock_stmt->bind_param("ii", $quantity, $item_id);
                $stock_stmt->execute();
            }
            $stock_stmt->close();


            try {
                $pay_stmt = $conn->prepare("INSERT INTO payment_tbl (order_id, customer_id, payment_method, amount, payment_status) VALUES (?, ?, ?, ?, ?)");
                $pay_stmt->bind_param("iisds", $order_id, $user_id, $payment_method, $total_amount, $payment_status);
                $pay_stmt->execute();
                $pay_stmt->close();
            } catch (Exception $pay_err) {
               
            }

            if (!empty($cat_id) && $order_type === 'Pre-order') {
                $booking_status = 'Pending';
                $cat_stmt = $conn->prepare("
                    INSERT INTO catbooking_tbl (customer_id, cat_id, booking_date, booking_time, booking_status) 
                    VALUES ( ?, ?, ?, ?, ?)
                ");
                $cat_stmt->bind_param("iiiss", $user_id, $cat_id, $booking_date, $booking_time, $booking_status);
                $cat_stmt->execute();
                $cat_stmt->close();
            }

            $conn->commit();
            header("Location: order_success.php?success=1&order_id=" . $order_id);
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $error_message = "Failed to process order: " . $e->getMessage();
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
    <link rel="stylesheet" href="css/checkout.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #fcfbfa;
            padding-top: 80px;
        }

        @media (max-width: 991px) {
            body {
                padding-top: 60px;
            }
        }

        .summary-card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        }

        .review-badge {
            background-color: #f8f9fa;
            border: 1px solid #eaeaea;
            border-radius: 12px;
            padding: 16px;
        }

        .item-row {
            border-bottom: 1px dashed #eaeaea;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }

        .item-row:last-child {
            border-bottom: none;
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container py-5">
        <div class="text-center mb-5">
            <div style="font-family: 'Playfair Display', serif;" class="h1 fw-bold text-dark">Review Your Order</div>
            <div class="text-muted">Please double-check your booking details and cart items before confirming payment.</div>
        </div>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger rounded-3 shadow-sm mb-4" style="max-width:600px; margin:0 auto;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error_message ?>
            </div>
        <?php endif; ?>

        <div class="row g-4 justify-content-center">

            <div class="col-lg-6">
                <div class="card summary-card p-4 h-100">
                    <div style="font-family:'Playfair Display',serif;" class="h4 fw-bold text-dark mb-4">
                        <i class="bi bi-calendar-check text-muted me-2"></i>Reservation Details
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <div class="review-badge">
                            <span class="text-muted d-block small text-uppercase fw-semibold">Service Option</span>
                            <strong class="fs-5 text-dark" id="viewOrderType">Loading...</strong>
                        </div>

                        <div id="loungeMetaGroup" style="display:none;" class="d-flex flex-column gap-3">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="review-badge">
                                        <span class="text-muted d-block small text-uppercase fw-semibold">Date</span>
                                        <strong class="text-dark" id="viewDate">-</strong>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="review-badge">
                                        <span class="text-muted d-block small text-uppercase fw-semibold">Arrival Time</span>
                                        <strong class="text-dark" id="viewTime">-</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="review-badge">
                                <span class="text-muted d-block small text-uppercase fw-semibold">Cat Companion</span>
                                <strong class="text-dark" id="viewCatName">None Selected</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card summary-card p-4">
                    <div style="font-family:'Playfair Display',serif;" class="h4 fw-bold text-dark mb-4">
                        <i class="bi bi-basket text-muted me-2"></i>Ordered Items
                    </div>

                    <div id="cartItemsContainer" class="mb-4" style="max-height:300px; overflow-y:auto;"></div>

                    <div class="border-top pt-3 mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <span class="text-muted d-block small fw-medium">GRAND TOTAL</span>
                                <span class="text-muted text-uppercase small" style="font-size:10px;">VAT Inclusive</span>
                            </div>
                            <div class="h2 fw-bold text-dark mb-0" id="viewGrandTotal">₱0.00</div>
                        </div>

                        <form id="finalOrderForm" method="POST" action="order_summary.php">
                            <input type="hidden" name="action" value="place_order">
                            <input type="hidden" id="dbOrderType" name="order_type" value="">
                            <input type="hidden" id="dbDate" name="booking_date" value="">
                            <input type="hidden" id="dbTime" name="booking_time" value="">
                            <input type="hidden" id="dbCatId" name="cat_id" value="">
                            <input type="hidden" id="dbCartJson" name="cart_json" value="">
                            <input type="hidden" id="dbPaymentMethod" name="payment_method" value="Cash">

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-dark w-100 py-3 rounded-3 fw-medium shadow-sm fs-5">
                                    Pay at Counter
                                </button>
                                <div id="paypal-button-container" class="w-100"></div>
                            </div>
                        </form>

                        <a href="index.php" class="btn btn-link btn-sm text-muted d-block text-center mt-3 text-decoration-none">
                            <i class="bi bi-arrow-left"></i> Cancel and return to home
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentCartTotal = 0;

        function loadPayPalSDK() {
            return new Promise((resolve, reject) => {
                if (typeof paypal !== 'undefined') {
                    resolve();
                    return;
                }
                const script = document.createElement('script');
                script.src = 'https://www.paypal.com/sdk/js?client-id=AR_ityCiAr_1l5CInno8S9b7EVE0xZMxuGTaky01nSU3vZUi4DH2UuKmQyCkVs-SDiDondbdcl8VZM4I&currency=PHP';
                script.onload = () => resolve();
                script.onerror = () => reject(new Error('Failed to load PayPal SDK'));
                document.body.appendChild(script);
            });
        }

        function renderPayPalButton() {
            if (currentCartTotal <= 0) {
                document.getElementById('paypal-button-container').innerHTML =
                    '<div class="text-muted small text-center">Add items to your cart to pay with PayPal.</div>';
                return;
            }

            paypal.Buttons({
                createOrder: (data, actions) => actions.order.create({
                    purchase_units: [{
                        amount: {
                            currency_code: 'PHP',
                            value: currentCartTotal.toFixed(2)
                        }
                    }]
                }),
                onApprove: (data, actions) => actions.order.capture().then(() => {
                    clearOrderLocalStorage();
                    document.getElementById('dbPaymentMethod').value = 'PayPal';
                    document.getElementById('finalOrderForm').submit();
                }),
                onError: (err) => {
                    console.error(err);
                    alert('Something went wrong with PayPal. Please try again.');
                },
                onCancel: () => {
                    alert('Payment cancelled. Your order has not been placed.');
                }
            }).render('#paypal-button-container');
        }

        function clearOrderLocalStorage() {
            localStorage.removeItem('cafe_bag');
            localStorage.removeItem('booking_date');
            localStorage.removeItem('booking_time');
            localStorage.removeItem('cat_id');
            localStorage.removeItem('summary_cat_name');
            localStorage.removeItem('order_type');
            localStorage.removeItem('guest_count');
            localStorage.removeItem('selected_cats_json');
            localStorage.removeItem('summary_cat_names');

            const fd = new FormData();
            fd.append('action', 'clear_after_order');
            fetch('bag.php', {
                    method: 'POST',
                    body: fd
                })
                .catch(err => console.error('Session clear failed:', err));
        }

        document.addEventListener('DOMContentLoaded', () => {
            const orderType = localStorage.getItem('order_type') || 'Pre-order';
            const bookingDate = localStorage.getItem('booking_date');
            const bookingTime = localStorage.getItem('booking_time');
            const catId = localStorage.getItem('cat_id');
            const catName = localStorage.getItem('summary_cat_name');
            let currentBag = JSON.parse(localStorage.getItem('cafe_bag')) || [];

            document.getElementById('viewOrderType').innerText = orderType;

            if (orderType === 'Pre-order') {
                document.getElementById('loungeMetaGroup').style.display = 'block';
                document.getElementById('viewDate').innerText = bookingDate || 'Not Configured';
                document.getElementById('viewTime').innerText = bookingTime || 'Not Configured';
                document.getElementById('viewCatName').innerText = catName || 'None Selected';
            }

            const itemsContainer = document.getElementById('cartItemsContainer');

            if (currentBag.length === 0) {
                itemsContainer.innerHTML = '<div class="text-center text-muted py-4">Your bag is empty!</div>';
            } else {
                currentBag.forEach(item => {
                    const rowTotal = item.price * item.quantity;
                    currentCartTotal += rowTotal;
                    itemsContainer.innerHTML += `
                    <div class="d-flex justify-content-between align-items-center item-row">
                        <div>
                            <div class="h6 mb-0 fw-bold text-dark small">${item.name}</div>
                            <span class="text-muted small">₱${parseFloat(item.price).toFixed(2)} × ${item.quantity}</span>
                        </div>
                        <span class="fw-bold text-dark small">₱${rowTotal.toFixed(2)}</span>
                    </div>`;
                });
            }

            document.getElementById('viewGrandTotal').innerText =
                '₱' + currentCartTotal.toLocaleString('en-US', {
                    minimumFractionDigits: 2
                });

            document.getElementById('dbOrderType').value = orderType;
            document.getElementById('dbDate').value = bookingDate || '';
            document.getElementById('dbTime').value = bookingTime || '';
            document.getElementById('dbCatId').value = catId || '';
            document.getElementById('dbCartJson').value = JSON.stringify(currentBag);

            loadPayPalSDK()
                .then(() => renderPayPalButton())
                .catch(err => {
                    console.error(err);
                    document.getElementById('paypal-button-container').innerHTML =
                        '<div class="text-danger small">Failed to load PayPal. Please refresh the page.</div>';
                });
        });

        document.getElementById('finalOrderForm').addEventListener('submit', () => {
            clearOrderLocalStorage();
        });
    </script>
</body>

</html>