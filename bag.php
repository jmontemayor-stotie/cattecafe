<?php
session_start();

$is_logged_in = isset($_SESSION['user_id']);

if (isset($_POST['action'])) {
    header('Content-Type: application/json');

    if (!$is_logged_in) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }

    if ($_POST['action'] === 'sync_local_storage') {
        $local_items = isset($_POST['items']) ? json_encode($_POST['items']) : '[]';
        $decoded_items = json_decode($local_items, true);

        $_SESSION['cafe_bag'] = [];
        if (is_array($decoded_items)) {
            foreach ($decoded_items as $item) {
                $id = $item['id'];
                $_SESSION['cafe_bag'][$id] = [
                    'id' => $id,
                    'name' => $item['name'],
                    'price' => (float)$item['price'],
                    'quantity' => (int)$item['quantity']
                ];
            }
        }
        echo json_encode(['status' => 'success', 'cart' => array_values($_SESSION['cafe_bag'])]);
        exit;
    }

    if ($_POST['action'] === 'alter_qty') {
        $id = $_POST['id'];
        $amount = (int)$_POST['amount'];

        if (isset($_SESSION['cafe_bag'][$id])) {
            $_SESSION['cafe_bag'][$id]['quantity'] += $amount;
            if ($_SESSION['cafe_bag'][$id]['quantity'] <= 0) {
                unset($_SESSION['cafe_bag'][$id]);
            }
        }
        echo json_encode(['status' => 'success', 'cart' => array_values($_SESSION['cafe_bag'])]);
        exit;
    }

    if ($_POST['action'] === 'clear_bag') {
        $_SESSION['cafe_bag'] = [];
        echo json_encode(['status' => 'success', 'cart' => []]);
        exit;
    }
}

$cart = isset($_SESSION['cafe_bag']) ? $_SESSION['cafe_bag'] : [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Bag - Cat Cafe Lounge</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght=0,400..900;1,400..900&family=Poppins:wght=300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bag.css">
</head>

<body>

    <?php
    if (file_exists('navbar.php')) {
        include 'navbar.php';
    } else { ?>
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top py-3">
            <div class="container">
                <a class="navbar-brand fw-bold" href="index.php">Cat Cafe Lounge</a>
                <div class="navbar-nav ms-auto">
                    <a class="nav-link text-white" href="menu.php"><i class="bi bi-arrow-left me-1"></i> Back to Menu</a>
                </div>
            </div>
        </nav>
    <?php } ?>

    <section class="page-banner">
        <div class="container text-white">
            <div class= "h1 display-6 fw-bold mb-1">Your Selected Delights</div>
            <div class="lead text-white-50 small font-monospace text-uppercase tracking-wider">Review your artisan choices before ordering</div>
        </div>
    </section>

    <div class="container py-5">
        <?php if (!$is_logged_in): ?>
            <div class="row justify-content-center">
                <div class="col-md-6 text-center py-5 bag-container-card p-5">
                    <i class="bi bi-lock text-muted display-3 mb-3"></i>
                    <div class="h3 fw-bold text-dark">Access Denied</div>
                    <div class="text-muted">You must be logged in to view your bag or add artisan choices to a cart.</div>
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <a href="login.php" class="btn btn-dark rounded-pill px-4">Log In</a>
                        <a href="signup.php" class="btn btn-outline-dark rounded-pill px-4">Sign Up</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="bag-container-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                            <div class= " h4 mb-0 fw-bold">Shopping Bag</div>
                            <a href="#" class="btn-clear-bag" id="clearBagLink"><i class="bi bi-trash3 me-1"></i> Empty Bag</a>
                        </div>

                        <div id="bagItemsContainer">
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="bag-container-card p-4 position-sticky" style="top: 110px;">
                        <div class=" h4 mb-4 fw-bold summary-title">Order Summary</div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Subtotal</span>
                            <span class="fw-medium text-dark" id="summarySubtotal">₱0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="text-muted small">Eco-friendly Packaging</span>
                            <span class="text-success small">Free</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-baseline mb-4">
                            <span class="fw-bold text-dark">Total Amount</span>
                            <span class="h4 fw-bold text-dark mb-0" id="summaryTotal">₱0.00</span>
                        </div>

                        <button class="btn btn-checkout w-100" id="btnPlaceOrder">
                            Proceed to Checkout <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <footer class="py-4 text-center bg-dark text-white-50 border-top border-secondary border-opacity-10">
        <div class="small mb-0">&copy; 2026 Cat Cafe Lounge. Curated and crafted responsibly.</div>
    </footer>

    <?php if ($is_logged_in): ?>
        <div class="modal fade" id="clearBagConfirmModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-body text-center p-5">
                        <div class="mb-4">
                            <i class="bi bi-trash3 text-danger display-1"></i>
                        </div>
                        <div class="h3 fw-bold">Empty Your Bag?</div>
                        <div class="text-muted">Are you sure you want to remove all treats from your bag? This action cannot be undone.</div>

                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" id="btnConfirmClearBag" class="btn btn-danger rounded-pill px-4">Yes, Empty Bag</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="emptyCartModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-body text-center p-5">
                        <div class="mb-4">
                            <i class="bi bi-bag-x display-1 text-danger"></i>
                        </div>
                        <div class="h3 fw-bold">Your Bag is Empty!</div>
                        <div class="text-muted">Please add some treats and refreshments from our menu before proceeding to checkout.</div>
                        <button type="button" class="btn btn-dark rounded-pill px-4 mt-3" data-bs-dismiss="modal">Browse Menu</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php if ($is_logged_in): ?>
        <script>
            let clearBagModalInstance = null;

            document.addEventListener('DOMContentLoaded', async () => {
                const itemsContainer = document.getElementById('bagItemsContainer');
                const subtotalEl = document.getElementById('summarySubtotal');
                const totalEl = document.getElementById('summaryTotal');

                let globalCart = [];

                const postData = async (formData) => {
                    try {
                        const res = await fetch('bag.php', {
                            method: 'POST',
                            body: formData
                        });
                        return await res.json();
                    } catch (err) {
                        console.error("Error communicating with server", err);
                    }
                };

                const updateTotals = (bag) => {
                    const total = bag.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                    const formatted = '₱' + total.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    subtotalEl.innerText = formatted;
                    totalEl.innerText = formatted;
                };

                const renderBag = (bag) => {
                    itemsContainer.innerHTML = '';
                    globalCart = bag;

                    localStorage.setItem('cafe_bag', JSON.stringify(bag));

                    if (!bag || bag.length === 0) {
                        itemsContainer.innerHTML = `
                        <div class="text-center py-5">
                            <i class="bi bi-bag-x text-muted display-4"></i>
                            <div class="text-muted mt-3 mb-0">Your gourmet selection bag is empty.</div>
                            <a href="menu.php" class="btn btn-sm btn-outline-dark rounded-pill mt-3 px-4">Browse Our Menu</a>
                        </div>
                    `;
                        updateTotals([]);
                        return;
                    }

                    bag.forEach(item => {
                        const row = document.createElement('div');
                        row.className = 'row bag-item-row align-items-center';
                        row.innerHTML = `
                        <div class="col-md-5 mb-2 mb-md-0">
                            <div class="h6 fw-bold text-dark mb-0">${item.name}</div>
                            <small class="text-muted">₱${parseFloat(item.price).toFixed(2)} each</small>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="qty-control">
                                <button class="qty-btn" onclick="alterQty('${item.id}', -1)">-</button>
                                <span class="qty-val">${item.quantity}</span>
                                <button class="qty-btn" onclick="alterQty('${item.id}', 1)">+</button>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 text-end">
                            <span class="fw-bold text-dark">₱${(item.price * item.quantity).toFixed(2)}</span>
                        </div>
                    `;
                        itemsContainer.appendChild(row);
                    });

                    updateTotals(bag);
                };

                const localCartData = JSON.parse(localStorage.getItem('cafe_bag')) || [];
                if (localCartData.length > 0) {
                    const fd = new FormData();
                    fd.append('action', 'sync_local_storage');

                    localCartData.forEach((item, index) => {
                        fd.append(`items[${index}][id]`, item.id);
                        fd.append(`items[${index}][name]`, item.name);
                        fd.append(`items[${index}][price]`, item.price);
                        fd.append(`items[${index}][quantity]`, item.quantity);
                    });

                    const syncRes = await postData(fd);
                    if (syncRes && syncRes.status === 'success') {
                        renderBag(syncRes.cart);
                    }
                } else {
                    renderBag(<?php echo json_encode(array_values($cart)); ?>);
                }

                window.alterQty = async (id, amount) => {
                    const fd = new FormData();
                    fd.append('action', 'alter_qty');
                    fd.append('id', id);
                    fd.append('amount', amount);

                    const data = await postData(fd);
                    if (data && data.status === 'success') {
                        renderBag(data.cart);
                    }
                };

                document.getElementById('clearBagLink').addEventListener('click', (e) => {
                    e.preventDefault();
                    clearBagModalInstance = new bootstrap.Modal(document.getElementById('clearBagConfirmModal'));
                    clearBagModalInstance.show();
                });

                document.getElementById('btnConfirmClearBag').addEventListener('click', async () => {
                    const fd = new FormData();
                    fd.append('action', 'clear_bag');

                    const data = await postData(fd);

                    if (data && data.status === 'success') {
                        renderBag(data.cart);
                        if (clearBagModalInstance) {
                            clearBagModalInstance.hide();
                        }
                    }
                });

                document.getElementById('btnPlaceOrder').addEventListener('click', () => {
                    if (globalCart.length === 0) {
                        const emptyCartModal = new bootstrap.Modal(document.getElementById('emptyCartModal'));
                        emptyCartModal.show();
                        return;
                    }
                    window.location.href = 'checkout.php';
                });
            });
        </script>
    <?php endif; ?>
</body>

</html>