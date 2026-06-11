<?php
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['user_id']); 

$query1 = $conn->query("SELECT * FROM menuitem_tbl ORDER BY category, item_id ASC");
$all_items = $query1->fetch_all(MYSQLI_ASSOC);

$query2 = $conn->query("SELECT cat_id, cat_name, img, description FROM cat_tbl");
$cats = $query2->fetch_all(MYSQLI_ASSOC);

$brownies = [];
$cookies  = [];
$drinks   = []; 
$cakes    = []; 

foreach ($all_items as $item) {
    $category = strtolower($item['category']); 
    
    if ($category === 'brownie' || $category === 'brownies') {
        $brownies[] = $item;
    } elseif ($category === 'cookie' || $category === 'cookies') {
        $cookies[] = $item;
    } elseif ($category === 'coffee' || $category === 'drink' || $category === 'drinks') { 
        $drinks[] = $item;
    } elseif ($category === 'cake' || $category === 'cakes') {
        $cakes[] = $item;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Menu - Cat Cafe Lounge</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/menu.css">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        .cat-profile-card {
            border: 2px solid #f0f0f0;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .cat-profile-card:hover {
            border-color: #bbb;
            transform: translateY(-2px);
        }
        .cat-profile-card.selected {
            border-color: #000;
            background-color: #fcfcfc;
        }
        .cat-profile-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 50%;
            background-color: #e9ecef;
        }
        .modal-steps-indicator {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .step-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #dee2e6;
        }
        .step-dot.active {
            background-color: #000;
            width: 24px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>

    <?php include 'navbar.php'; ?>

    <section class="page-banner">
        <div class="container text-white">
            <div class="h1 display-5 fw-bold mb-2">Our Culinary Menu</div>
            <div class="lead text-white-50 small font-monospace text-uppercase tracking-wider">Artisan pastries & premium house blends prepared daily</div>
        </div>
    </section>

    <div class="container py-5">
        <ul class="nav nav-pills justify-content-center gap-2 mb-5" id="menuControlPicker" role="tablist">
            <li class="nav-item"><button class="nav-link nav-link-picker rounded-pill active" id="brownies-tab" data-bs-toggle="pill" data-bs-target="#pane-brownies" type="button"><i class="bi bi-grid-3x3-gap me-2"></i> Brownies</button></li>
            <li class="nav-item"><button class="nav-link nav-link-picker rounded-pill" id="cookies-tab" data-bs-toggle="pill" data-bs-target="#pane-cookies" type="button"><i class="bi bi-cookie me-2"></i> Cookies</button></li>
            <li class="nav-item"><button class="nav-link nav-link-picker rounded-pill" id="drinks-tab" data-bs-toggle="pill" data-bs-target="#pane-drinks" type="button"><i class="bi bi-cup-hot me-2"></i> Drinks</button></li>
            <li class="nav-item"><button class="nav-link nav-link-picker rounded-pill" id="cakes-tab" data-bs-toggle="pill" data-bs-target="#pane-cakes" type="button"><i class="bi bi-cake2 me-2"></i> Cakes</button></li>
        </ul>

        <div class="tab-content" id="menuControlPickerContent">
            <?php 
function render_menu_grid($items, $is_brownie_tab = false) {
    if (!empty($items)): 
        foreach ($items as $item): 
            $isOutOfStock = ($item['stock'] <= 0);
    ?>
        <div class="col-12 col-xl-6"> 
            <div class="card menu-card h-100 border-0 shadow-sm overflow-hidden">
                <div class="row g-0 h-100">
                    
                    <div class="col-4 position-relative bg-light">
                        <?php if (!empty($item['image'])): ?>
                            <img src="<?= htmlspecialchars($item['image']); ?>" 
                                 alt="<?= htmlspecialchars($item['item_name']); ?>" 
                                 class="w-100 h-100 img-fluid position-absolute start-0 top-0" 
                                 style="object-fit: cover; object-position: center;">
                        <?php else: ?>
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                <i class="bi bi-image fs-3"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-8 d-flex flex-column justify-content-between">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="h5 card-title fw-bold text-dark mb-0">
                                    <?= htmlspecialchars($item['item_name']); ?>
                                </div>
                                <span class="item-price ms-2 fw-semibold text-nowrap">₱<?= number_format($item['price'], 2); ?></span>
                            </div>
                            
                            <div class="card-text text-muted item-description mb-1 small"><?= htmlspecialchars($item['description']); ?></div>
                            
                            <div class="mb-0 small fw-bold <?= $isOutOfStock ? 'text-danger' : 'text-success' ?>">
                                <i class="bi bi-box-seam me-1"></i> 
                                <?= $isOutOfStock ? 'Out of Stock' : 'Stock: ' . $item['stock']; ?>
                            </div>
                        </div>

                        <div class="card-action-box p-3 border-top border-light bg-transparent">
                            <div class="row g-2">
                                <div class="col-6">
                                    <button class="btn btn-bag w-100 btn-add-bag py-2 small" 
                                            <?= $isOutOfStock ? 'disabled' : ''; ?>
                                            data-id="<?= $item['item_id']; ?>" 
                                            data-name="<?= htmlspecialchars($item['item_name']); ?>" 
                                            data-price="<?= $item['price']; ?>">
                                        <i class="bi bi-bag me-1"></i> <?= $isOutOfStock ? 'N/A' : '+ Bag'; ?>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button class="btn btn-order w-100 btn-order-now py-2 small" 
                                            <?= $isOutOfStock ? 'disabled' : ''; ?>
                                            data-id="<?= $item['item_id']; ?>" 
                                            data-name="<?= htmlspecialchars($item['item_name']); ?>" 
                                            data-price="<?= $item['price']; ?>">
                                        Buy Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach;
    else: ?>
        <div class="col-12 text-center py-4"><p class="text-muted">No items found under this section.</p></div>
    <?php endif; 
} ?>

            <div class="tab-pane fade show active" id="pane-brownies" role="tabpanel"><div class="row g-4"><?php render_menu_grid($brownies, true); ?></div></div>
            <div class="tab-pane fade" id="pane-cookies" role="tabpanel"><div class="row g-4"><?php render_menu_grid($cookies); ?></div></div>
            <div class="tab-pane fade" id="pane-drinks" role="tabpanel"><div class="row g-4"><?php render_menu_grid($drinks); ?></div></div>
            <div class="tab-pane fade" id="pane-cakes" role="tabpanel"><div class="row g-4"><?php render_menu_grid($cakes); ?></div></div>
        </div>
    </div>

    <div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow p-3">
                <div class="modal-header border-0 pb-0 justify-content-between align-items-center">
                    <div class="h5 modal-title fw-bold text-dark fs-5" id="modalTitle">Order Method</div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="modal-steps-indicator">
                        <div class="step-dot active" id="dot-step1"></div>
                        <div class="step-dot" id="dot-step2"></div>
                        <div class="step-dot" id="dot-step3"></div>
                        <div class="step-dot" id="dot-step4"></div>
                    </div>

                    <div id="step1">
                        <div class="text-muted small mb-4 text-center">Please select how you'd like to fulfill your current order.</div>
                        <div class="d-grid gap-3">
                            <button class="btn btn-outline-dark btn-lg py-3 fs-6 fw-medium d-flex align-items-center justify-content-center gap-2" onclick="selectOrderType('Pre-order')">
                                <i class="bi bi-calendar2-check fs-5"></i> Pre-order for Lounge Visit
                            </button>
                            <button class="btn btn-outline-dark btn-lg py-3 fs-6 fw-medium d-flex align-items-center justify-content-center gap-2" onclick="savePref('Delivery')">
                                <i class="bi bi-truck fs-5"></i> Home Delivery Service
                            </button>
                        </div>
                    </div>

                    <div id="step2" style="display:none;">
                        <div class="text-muted small mb-4 text-center">Would you like to reserve an affectionate cat companion to join you at your table?</div>
                        <div class="row g-2">
                            <div class="col-6">
                                <button class="btn btn-dark w-100 py-3" onclick="showStep('step3')">Yes, Select Cat</button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-outline-secondary w-100 py-3" onclick="goToScheduler(false)">No, Food Only</button>
                            </div>
                        </div>
                        <button class="btn btn-link btn-sm text-dark d-block mx-auto mt-4 text-decoration-none" onclick="showStep('step1')"><i class="bi bi-arrow-left"></i> Back</button>
                    </div>

                    <div id="step3" style="display:none;">
                        <p class="text-muted small mb-3">Choose your host kitty:</p>
                        <div class="cat-list-container pe-1" style="max-height: 280px; overflow-y: auto;">
                            <?php if(!empty($cats)): ?>
                                <?php foreach($cats as $cat): ?>
                                    <div class="cat-profile-card p-3 mb-2 d-flex align-items-center gap-3" data-cat-id="<?= $cat['cat_id'] ?>" onclick="selectCatCard(this)">
                                        <img src="<?= !empty($cat['img']) ? htmlspecialchars($cat['img']) : 'images/default-cat.jpg' ?>" class="cat-profile-img shadow-sm" alt="Cat Image">
                                        <div class="flex-grow-1">
                                            <div class="h6 fw-bold mb-1 text-dark"><?= htmlspecialchars($cat['cat_name']) ?></div>
                                            <div class="text-muted mb-0 small text-truncate" style="max-width: 220px;">
                                                <?= !empty($cat['description']) ? htmlspecialchars($cat['description']) : 'Friendly lounge companion.' ?>
                                            </div>
                                        </div>
                                        <i class="bi bi-check-circle-fill text-dark fs-5 check-icon opacity-0"></i>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted text-center py-3 small">No cat profiles loaded in database.</p>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <button class="btn btn-sm btn-outline-secondary px-3" onclick="showStep('step2')">Back</button>
                            <button class="btn btn-sm btn-dark px-4" id="btnNextToSchedule" disabled onclick="goToScheduler(true)">Continue</button>
                        </div>
                    </div>

                    <div id="step4" style="display:none;">
                        <p class="text-muted small mb-3 text-center">Set your targeted reservation schedule details below:</p>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Reservation Date</label>
                            <input type="date" class="form-control form-control-lg fs-6" id="reserveDate" required min="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-secondary">Arrival Time Slot</label>
                            <input type="time" class="form-control form-control-lg fs-6" id="reserveTime" required>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2">
                            <button class="btn btn-sm btn-outline-secondary px-3" id="btnScheduleBack">Back</button>
                            <button class="btn btn-sm btn-dark px-4" onclick="finalizePreOrder()">Confirm & Complete</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <footer class="py-4 text-center bg-dark text-white-50 border-top border-secondary border-opacity-10">
        <p class="small mb-0">&copy; 2026 Cat Cafe Lounge. Curated and crafted responsibly.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>

    let selectedCatId = null;
    let hasCatBooking = false;
    const IS_LOGGED_IN = <?php echo $is_logged_in ? 'true' : 'false'; ?>;

    const saveToBag = (id, name, price) => {
        if (!IS_LOGGED_IN) {
            Swal.fire({
                icon: 'info',
                title: 'Login Required',
                text: 'Please log in or sign up first to add treats to your bag!',
                confirmButtonColor: '#000'
            }).then(() => {
                window.location.href = "login.php";
            });
            return false;
        }

        let currentBag = JSON.parse(localStorage.getItem('cafe_bag')) || [];
        const searchIdx = currentBag.findIndex(item => item.id === id);
        
        if (searchIdx > -1) {
            currentBag[searchIdx].quantity += 1;
        } else {
            currentBag.push({ id, name, price: parseFloat(price), quantity: 1 });
        }
        
        localStorage.setItem('cafe_bag', JSON.stringify(currentBag));
        return true;
    };

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.btn-add-bag').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const ds = e.currentTarget.dataset;
                const success = saveToBag(ds.id, ds.name, ds.price);
                
                if (success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Added to Bag',
                        text: `${ds.name} has been added to your collection!`,
                        timer: 1500,
                        showConfirmButton: false,
                        position: 'bottom-end',
                        toast: true
                    });
                }
            });
        });

       
document.querySelectorAll('.btn-order-now').forEach(btn => {
    btn.addEventListener('click', async (e) => {
        const ds = e.currentTarget.dataset;
        const success = saveToBag(ds.id, ds.name, ds.price);
        
        if (success) {
            const localData = localStorage.getItem('cafe_bag');
            const fd = new FormData();
            fd.append('action', 'sync_local_storage');
            fd.append('items', localData);

            // Send to bag.php (which handles the session update)
            try {
                const res = await fetch('bag.php', { method: 'POST', body: fd });
                const data = await res.json();
                
                if (data.status === 'success') {
                    // Now that the session is synced, go to checkout
                    window.location.href = 'checkout.php';
                }
            } catch (err) {
                console.error("Sync failed", err);
            }
        }
    });
});

        const hash = window.location.hash;
        if (hash) {
            const targetTabButton = document.querySelector(`[data-bs-target="${hash}"]`);
            if (targetTabButton) {
                new bootstrap.Tab(targetTabButton).show();
            }
        }
    });

    function showStep(id) {
        const steps = ['step1', 'step2', 'step3', 'step4'];
        steps.forEach(s => document.getElementById(s).style.display = 'none');
        document.getElementById(id).style.display = 'block';
        document.getElementById('modalTitle').innerText = {
            'step1': 'Order Method', 'step2': 'Cat Companion Option',
            'step3': 'Select Your Cat Host', 'step4': 'Schedule Lounge Visit'
        }[id];
        document.querySelectorAll('.step-dot').forEach((dot, index) => {
            dot.classList.toggle('active', index < parseInt(id.replace('step', '')));
        });
    }

    function selectCatCard(element) {
        document.querySelectorAll('.cat-profile-card').forEach(card => {
            card.classList.remove('selected');
            card.querySelector('.check-icon').classList.add('opacity-0');
        });
        element.classList.add('selected');
        element.querySelector('.check-icon').classList.remove('opacity-0');
        selectedCatId = element.getAttribute('data-cat-id');
        document.getElementById('btnNextToSchedule').disabled = false;
    }

    function goToScheduler(withCat) {
        hasCatBooking = withCat;
        document.getElementById('btnScheduleBack').setAttribute('onclick', withCat ? "showStep('step3')" : "showStep('step2')");
        if (!withCat) selectedCatId = null;
        showStep('step4');
    }

    function finalizePreOrder() {
        const dateVal = document.getElementById('reserveDate').value;
        const timeVal = document.getElementById('reserveTime').value;
        if(!dateVal || !timeVal) {
            Swal.fire('Incomplete', 'Please select a date and time.', 'warning');
            return;
        }
        localStorage.setItem('order_type', 'Pre-order');
        localStorage.setItem('cat_id', hasCatBooking ? selectedCatId : '');
        localStorage.setItem('booking_date', dateVal);
        localStorage.setItem('booking_time', timeVal);
        window.location.href = 'bag.php';
    }
</script>
</body>
</html>