<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_query = $conn->query("SELECT Fname, Mname, Lname, contact, email FROM customer_tbl WHERE customer_id = $user_id");$user_data = $user_query->fetch_assoc();
$full_name = trim($user_data['Fname'] . ' ' . $user_data['Mname'] . ' ' . $user_data['Lname']);

$menu_query = "SELECT * FROM menuitem_tbl ORDER BY category, item_name ASC";
$menu_result = $conn->query($menu_query);
$categorized_menu = [];
if ($menu_result) {
    while ($item = $menu_result->fetch_assoc()) {
        $cat_group = strtolower($item['category']);
        if (strpos($cat_group, 'brownie') !== false) $categorized_menu['Brownies'][] = $item;
        elseif (strpos($cat_group, 'cookie') !== false) $categorized_menu['Cookies'][] = $item;
        elseif (strpos($cat_group, 'cake') !== false) $categorized_menu['Cakes'][] = $item;
        else $categorized_menu['Drinks & Refreshments'][] = $item;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Details - Cat Cafe Lounge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; padding-top: 80px; }
        .delivery-wrapper { padding: 60px 0; }
        .form-card { border: none; border-radius: 1.25rem; box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.05); background: #ffffff; }
        .summary-sticky { position: sticky; top: 20px; }
        .inline-menu-img { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #dee2e6; }
        .btn-mini-add { font-size: 0.75rem; padding: 0.4rem 0.8rem; border-radius: 20px; }
        .accordion-item { border-radius: 12px !important; overflow: hidden; margin-bottom: 8px; }
        @media (max-width: 991px) { body { padding-top: 60px; } }
    </style>
    <script src="https://www.paypal.com/sdk/js?client-id=test&currency=PHP"></script>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="delivery-wrapper">
    <div class="container-xl">
        <div class="row g-4 justify-content-center">

            <div class="col-lg-7 col-xl-8">
                <div class="card form-card p-4 p-md-5">
                    <div style="h2 font-family: 'Playfair Display', serif;" class="fw-bold text-dark mb-4">Shipping Information</div>
                    <form id="deliveryForm">
                        <input type="hidden" id="customer_id" value="<?= $_SESSION['user_id'] ?>">

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Full Name</label>
                                <input type="text" class="form-control bg-light" id="recipient_name"
                                    value="<?= htmlspecialchars($full_name ?? '') ?>"
                                    readonly style="cursor: not-allowed;">
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold">Email</label>
                                <input type="email" class="form-control bg-light" id="recipient_email"
                                    value="<?= htmlspecialchars($user_data['email'] ?? '') ?>"
                                    readonly style="cursor: not-allowed;">
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold">Contact Number</label>
                                <input type="tel" class="form-control" id="contact_number"
                                    value="<?= htmlspecialchars($user_data['contact'] ?? '') ?>"
                                    required>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold">Address</label>
                                <textarea class="form-control bg-light" id="delivery_address" rows="2" required></textarea>
                            </div>

                            <div class="col-12 mt-3">
                                <label class="form-label small fw-semibold">Payment Method</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="pay" id="cod" value="COD" checked>
                                        <label class="btn btn-outline-dark w-100 py-3" for="cod">
                                            <i class="bi bi-cash-coin"></i> COD
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="pay" id="online" value="Online">
                                        <label class="btn btn-outline-dark w-100 py-3" for="online">
                                            <i class="bi bi-credit-card"></i> Online (PayPal)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 p-4 bg-light rounded-4 border">
                            <div class="h5 fw-bold mb-3"><i class="bi bi-plus-circle"></i> Add More Treats</div>
                            <div class="accordion accordion-flush" id="extraMenuAccordion">
                                <?php $acc_index = 0; foreach ($categorized_menu as $category => $items): $acc_index++; ?>
                                <div class="accordion-item shadow-sm border-0">
                                    <button class="accordion-button collapsed fw-semibold small" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#flush-<?= $acc_index ?>">
                                        <?= $category ?>
                                    </button>
                                    <div id="flush-<?= $acc_index ?>" class="accordion-collapse collapse"
                                         data-bs-parent="#extraMenuAccordion">
                                        <div class="accordion-body">
                                            <?php foreach ($items as $item): ?>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="<?= htmlspecialchars($item['image'] ?? 'img/default.jpg') ?>"
                                                         class="inline-menu-img" alt="treat">
                                                    <div>
                                                        <div class=" h6 mb-0 fw-bold small"><?= $item['item_name'] ?></div>
                                                        <span class="text-muted small">₱<?= number_format($item['price'], 2) ?></span>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-outline-dark btn-mini-add"
                                                        onclick="addExtraToBag('<?= $item['item_id'] ?>',
                                                                '<?= htmlspecialchars($item['item_name'], ENT_QUOTES) ?>',
                                                                '<?= $item['price'] ?>')">Add</button>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div id="cod-actions" class="d-flex justify-content-between align-items-center mt-4 border-top pt-3">
                            <a href="checkout.php" class="btn btn-outline-secondary btn-sm rounded-pill">Back</a>
                            <button type="submit" id="btnSubmit" class="btn btn-dark btn-sm px-4 rounded-pill">
                                Confirm & Place Order
                            </button>
                        </div>

                        <div id="paypal-actions" class="mt-4 border-top pt-3 d-none">
                            <div id="paypal-button-container"></div>
                        </div>

                    </form>
                </div>
            </div>

            <div class="col-lg-5 col-xl-4">
                <div class="card card-body form-card p-4 summary-sticky">
                    <div class="h4 fw-bold border-bottom pb-2">Order Summary</div>
                    <div id="deliveryBagItems" class="mb-3" style="max-height: 300px; overflow-y: auto;"></div>
                    <div class="d-flex justify-content-between pt-2 border-top">
                        <span class="fw-bold">GRAND TOTAL</span>
                        <div class="h3  fw-bold" id="lblDeliveryTotal">₱0.00</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="orderSuccessModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="bi bi-bicycle display-1 text-dark"></i>
                </div>
                <div class="h3 fw-bold">Order Received!</div>
                <div class="text-muted">Thank you for choosing Catte Cafe. Your order is being carefully prepared.</div>
                <div class="bg-light p-3 rounded-3 my-4">
                    <div class="small text-muted mb-0">Estimated Delivery</div>
                    <div class="h5 fw-bold text-dark">30mins-1hr</div>
                </div>
                <a href="index.php" class="btn btn-dark rounded-pill px-4">Back to Home</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let currentCartTotal = 0;

    document.addEventListener('DOMContentLoaded', () => {
        renderDeliverySidebar();
        setupPaymentToggle();
        renderPayPalButton();
    });

    function renderDeliverySidebar() {
        const container = document.getElementById('deliveryBagItems');
        let bag = JSON.parse(localStorage.getItem('cafe_bag')) || [];
        let total = 0;

        if (bag.length === 0) {
            container.innerHTML = '<p class="text-muted small">Cart is empty.</p>';
        } else {
            container.innerHTML = bag.map(i => {
                total += (i.price * i.quantity);
                return `<div class="d-flex justify-content-between mb-2 small">
                            <span>${i.name} x ${i.quantity}</span>
                            <strong>₱${(i.price * i.quantity).toFixed(2)}</strong>
                        </div>`;
            }).join('');
        }

        currentCartTotal = total;
        document.getElementById('lblDeliveryTotal').innerText = "₱" + total.toFixed(2);
    }

    function addExtraToBag(id, name, price) {
        let bag = JSON.parse(localStorage.getItem('cafe_bag')) || [];
        let existing = bag.find(i => i.id === id);
        if (existing) existing.quantity++;
        else bag.push({ id, name, price: parseFloat(price), quantity: 1 });
        localStorage.setItem('cafe_bag', JSON.stringify(bag));
        renderDeliverySidebar();
    }

    function setupPaymentToggle() {
        const codRadio = document.getElementById('cod');
        const onlineRadio = document.getElementById('online');
        const codActions = document.getElementById('cod-actions');
        const paypalActions = document.getElementById('paypal-actions');

        const toggleUI = () => {
            if (onlineRadio.checked) {
                codActions.classList.add('d-none');
                paypalActions.classList.remove('d-none');
            } else {
                codActions.classList.remove('d-none');
                paypalActions.classList.add('d-none');
            }
        };

        codRadio.addEventListener('change', toggleUI);
        onlineRadio.addEventListener('change', toggleUI);
    }

    async function submitOrderToBackend(paymentDetails = null) {
        const payload = {
            customer_id: document.getElementById('customer_id').value,
            cart_items: JSON.parse(localStorage.getItem('cafe_bag')) || [],
            delivery: {
                address: document.getElementById('delivery_address').value,
                recipient: document.getElementById('recipient_name').value,
                contact: document.getElementById('contact_number').value,
                payment: document.querySelector('input[name="pay"]:checked').value,
                paypal_details: paymentDetails
            }
        };

        const res = await fetch('save_delivery.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        return await res.json();
    }

    document.getElementById('deliveryForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('btnSubmit');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

        try {
            const data = await submitOrderToBackend();
            if (data.success) {
                localStorage.removeItem('cafe_bag');
                new bootstrap.Modal(document.getElementById('orderSuccessModal')).show();
            } else {
                alert("Error: " + data.message);
                btn.disabled = false;
                btn.innerHTML = 'Confirm & Place Order';
            }
        } catch (err) {
            alert("Something went wrong. Please try again.");
            btn.disabled = false;
            btn.innerHTML = 'Confirm & Place Order';
        }
    });

    function renderPayPalButton() {
        if (typeof paypal === 'undefined') {
            console.error('PayPal SDK not loaded.');
            return;
        }

        paypal.Buttons({
            onClick: function(data, actions) {
                const form = document.getElementById('deliveryForm');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return actions.reject();
                }
                if (currentCartTotal <= 0) {
                    alert("Your cart is empty!");
                    return actions.reject();
                }
                return actions.resolve();
            },
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            currency_code: 'PHP',
                            value: currentCartTotal.toFixed(2)
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(async function(details) {
                    try {
                        const backendResponse = await submitOrderToBackend(details);
                        if (backendResponse.success) {
                            localStorage.removeItem('cafe_bag');
                            new bootstrap.Modal(document.getElementById('orderSuccessModal')).show();
                        } else {
                            alert("Payment captured but failed to save: " + backendResponse.message);
                        }
                    } catch (err) {
                        alert("Network error while saving order.");
                    }
                });
            },
            onCancel: function() {
                alert("Payment cancelled. Your order has not been placed.");
            },
            onError: function(err) {
                console.error(err);
                alert("A PayPal error occurred. Please try again.");
            }
        }).render('#paypal-button-container');
    }
</script>
</body>
</html>