<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$cats_query = "SELECT cat_id, cat_name, img, description FROM cat_tbl";
$cats_result = $conn->query($cats_query);

$cats = [];
if ($cats_result) {
    while ($row = $cats_result->fetch_assoc()) {
        $cats[] = $row;
    }
}

$menu_query = "SELECT * FROM menuitem_tbl ORDER BY category, item_name ASC";
$menu_result = $conn->query($menu_query);

$categorized_menu = [];
if ($menu_result) {
    while ($item = $menu_result->fetch_assoc()) {
        $cat_group = strtolower($item['category']);

        if (strpos($cat_group, 'brownie') !== false) {
            $categorized_menu['Brownies'][] = $item;
        } elseif (strpos($cat_group, 'cookie') !== false) {
            $categorized_menu['Cookies'][] = $item;
        } elseif (strpos($cat_group, 'cake') !== false) {
            $categorized_menu['Cakes'][] = $item;
        } else {
            $categorized_menu['Drinks & Refreshments'][] = $item;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Order - Cat Cafe Lounge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/checkout.css">
    
    <style>
        .cat-selection-scroll {
            max-height: 420px; 
            overflow-y: auto; 
            padding-right: 6px;
        }
        .cat-selection-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .cat-selection-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .cat-selection-scroll::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }
        .cat-card-professional {
            border: 2px solid #e9ecef;
            border-radius: 1rem;
            transition: all 0.25s ease-in-out;
            cursor: pointer;
            background-color: #ffffff;
        }
        .cat-card-professional:hover {
            transform: translateY(-2px);
            border-color: #adb5bd;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
        }
        .cat-card-professional.selected {
            border-color: #212529 !important;
            background-color: #f8f9fa;
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.05) !important;
        }
        .cat-card-professional.disabled-card {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
            border-color: #e9ecef !important;
        }
        .cat-avatar-frame {
            width: 72px;
            height: 72px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #f8f9fa;
        }
        .cat-card-professional.selected .cat-avatar-frame {
            border-color: #212529;
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="checkout-page-wrapper">
        <div class="container-xl">

            <div class="flow-stepper">
                <div class="step-circle active" id="circle-type">1</div>
                <div class="step-circle" id="circle-query">2</div>
                <div class="step-circle" id="circle-cat">3</div>
                <div class="step-circle" id="circle-schedule">4</div>
            </div>

            <div class="row g-4 justify-content-center">

                <div class="col-lg-7 col-xl-8">
                    <div class="card flow-card p-4 p-md-5">

                        <div id="step-type" class="step-container active">
                            <div style="h2 font-family: 'Playfair Display', serif;" class="fw-bold text-dark mb-1">Choose Service Method</div>
                            <div class="text-muted small mb-4">Select how you'd like to enjoy your treats today to begin booking configurations.</div>

                            <div class="row g-3 mb-5">
                                <div class="col-sm-6">
                                    <button class="option-btn-card p-4 d-flex flex-column gap-2" onclick="setOrderType('Delivery')">
                                        <i class="bi bi-truck fs-2 text-dark"></i>
                                        <span class="fw-bold fs-5 text-dark d-block mb-0">Home Delivery Service</span>
                                        <span class="text-muted small lh-sm">Fresh bakery batches delivered right to your location.</span>
                                    </button>
                                </div>
                                <div class="col-sm-6">
                                    <button class="option-btn-card p-4 d-flex flex-column gap-2" onclick="setOrderType('Pre-order')">
                                        <i class="bi bi-calendar-heart fs-2 text-dark"></i>
                                        <span class="fw-bold fs-5 text-dark d-block mb-0">Pre-Order & Visit Lounge</span>
                                        <span class="text-muted small lh-sm">Secure an physical layout seat and play with our resident cats.</span>
                                    </button>
                                </div>
                            </div>

                            <div class="bg-light bg-opacity-70 rounded-4 p-4 border border-light shadow-sm">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-plus-circle-fill text-dark fs-5"></i>
                                    <div class="h5 fw-bold mb-0 text-dark">Want to add more treats?</div>
                                </div>
                                <div class="text-muted small mb-3">Browse our available categories to add extras directly into your checkout stream without leaving this screen.</div>

                                <div class="accordion accordion-flush bg-transparent" id="extraMenuAccordion">
                                    <?php $acc_index = 0;
                                    foreach ($categorized_menu as $category_name => $items_list): $acc_index++; ?>
                                        <div class="accordion-item bg-white shadow-sm border-0 rounded-3 mb-2">
                                            <div class="h2 accordion-header">
                                                <button class="accordion-button collapsed fw-semibold text-dark small" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-<?= $acc_index; ?>">
                                                    <?= $category_name; ?> (<?= count($items_list); ?> items)
                                                </button>
                                            </div>
                                            <div id="flush-collapse-<?= $acc_index; ?>" class="accordion-collapse collapse" data-bs-parent="#extraMenuAccordion">
                                                <div class="accordion-body p-2">
                                                    <div class="d-flex flex-column gap-2">
                                                        <?php foreach ($items_list as $menu_item): ?>
                                                            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light bg-opacity-40">
                                                                <div class="d-flex align-items-center gap-3">
                                                                    <?php if (!empty($menu_item['image'])): ?>
                                                                        <img src="<?= htmlspecialchars($menu_item['image']); ?>" class="inline-menu-img border" alt="treat">
                                                                    <?php else: ?>
                                                                        <div class="inline-menu-img bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image"></i></div>
                                                                    <?php endif; ?>
                                                                    <div>
                                                                        <h6 class="mb-0 fw-bold small text-dark"><?= htmlspecialchars($menu_item['item_name']); ?></h6>
                                                                        <span class="text-muted small">₱<?= number_format($menu_item['price'], 2); ?></span>
                                                                    </div>
                                                                </div>
                                                                <button class="btn btn-outline-dark btn-mini-add" onclick="addExtraToBag('<?= $menu_item['item_id']; ?>', '<?= htmlspecialchars($menu_item['item_name'], ENT_QUOTES); ?>', '<?= $menu_item['price']; ?>', this)">
                                                                    <i class="bi bi-plus-lg"></i> Add Extra
                                                                </button>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div id="step-cat-query" class="step-container">
                            <div style="h2 font-family: 'Playfair Display', serif;" class="fw-bold text-dark mb-1">Lounge Group Details</div>
                            <div class="text-muted small mb-4">How many guests will be coming to the lounge room layout? (Each guest can choose up to 2 cats!)</div>

                            <div class="mx-auto mb-4" style="max-width: 450px;">
                                <label for="guestCount" class="form-label small fw-semibold text-secondary">Number of Guests</label>
                                <select class="form-select form-select-lg rounded-3 mb-4" id="guestCount" onchange="handleGuestCountChange()">
                                    <option value="1" selected>1 Guest (Max 2 Cats)</option>
                                    <option value="2">2 Guests (Max 4 Cats)</option>
                                    <option value="3">3 Guests (Max 6 Cats)</option>
                                    <option value="4">4 Guests (Max 8 Cats)</option>
                                    <option value="5">5 Guests (Max 10 Cats)</option>
                                </select>

                                <div class="d-grid gap-3">
                                    <button type="button" class="btn btn-dark btn-lg py-3 fs-6 fw-medium shadow-sm rounded-3" onclick="navigateToStep('step-cat-select')">Proceed to Select Cat Companions</button>
                                    <button type="button" class="btn btn-outline-secondary btn-lg py-3 fs-6 rounded-3" onclick="skipCatSelection()">No Cats, just reserve basic table</button>
                                </div>
                            </div>
                            <button class="btn btn-link btn-sm text-dark d-block mx-auto mt-4 text-decoration-none small" onclick="navigateToStep('step-type')"><i class="bi bi-chevron-left"></i> Back to Step 1</button>
                        </div>

                        <div id="step-cat-select" class="step-container">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div style="h2 font-family: 'Playfair Display', serif;" class="fw-bold text-dark mb-0">Select Your Cat Companions</div>
                                <span class="badge bg-dark rounded-pill py-2 px-3 fw-medium fs-7" id="catLimitCounter">Selected: 0 / 2</span>
                            </div>
                            <div class="text-muted small mb-3">Pick your favorite cafe hosts. You can pick multiple choices up to your limit.</div>

                            <div class="alert alert-warning py-2 px-3 small d-none align-items-center gap-2 rounded-3 mb-3" id="catLimitWarning">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <span>Selection limit reached for your guest profile setup!</span>
                            </div>

                            <div class="row g-3 cat-selection-scroll mb-4">
                                <?php if (!empty($cats)): ?>
                                    <?php foreach ($cats as $cat): ?>
                                        <div class="col-12">
                                            <div class="cat-card-professional p-3 d-flex align-items-center justify-content-between shadow-sm" data-id="<?= $cat['cat_id'] ?>" data-name="<?= htmlspecialchars($cat['cat_name']) ?>" onclick="toggleCatSelection(this)">
                                                <div class="d-flex align-items-center gap-3">
                                                    
                                                    <?php if (!empty($cat['img'])): ?>
                                                        <?php 
                                                            $cleanPath = str_replace('\\', '/', $cat['img']);
                                                            $webPath = str_replace('C:/wamp64/www/cafe/', '', $cleanPath); 
                                                        ?>
                                                        <img src="<?= htmlspecialchars($webPath) ?>" class="cat-avatar-frame shadow-sm" alt="<?= htmlspecialchars($cat['cat_name']) ?>">
                                                    <?php else: ?>
                                                        <div class="cat-avatar-frame bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center text-secondary border border-2 border-light shadow-sm">
                                                            <i class="bi bi-heart-fill fs-4"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <div class="h5 fw-bold mb-1 text-dark text-capitalize" style="font-size: 1.05rem;"><?= htmlspecialchars($cat['cat_name']) ?></div>
                                                        <div class="text-muted mb-0 small lh-sm" style="max-width: 480px;"><?= htmlspecialchars($cat['description'] ?? 'Friendly and playful lounge room mate.'); ?></div>
                                                    </div>
                                                </div>
                                                <div class="pe-2">
                                                    <i class="bi bi-check-circle-fill text-dark fs-3 check-icon d-none"></i>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-5 text-muted small bg-light rounded-4 border">
                                        <i class="bi bi-database-exclamation d-block fs-2 mb-2 text-secondary"></i>
                                        No active kitty hosts loaded inside the database layout context.
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-3">
                                <button class="btn btn-outline-secondary btn-sm px-4 rounded-pill" onclick="navigateToStep('step-cat-query')">Back</button>
                                <button class="btn btn-dark btn-sm px-4 rounded-pill shadow-sm fw-medium" id="btnCatNext" onclick="navigateToStep('step-schedule')">Continue Selection</button>
                            </div>
                        </div>

                        <div id="step-schedule" class="step-container">
                            <div style="h2 font-family: 'Playfair Display', serif;" class="fw-bold text-dark mb-1">Schedule Your Visit</div>
                            <div class="text-muted small mb-4">When should we clear your seating layout table and prepare your items fresh?</div>

                            <form id="checkoutForm" action="seating.php" method="POST">
                                <input type="hidden" name="order_type" id="formOrderType">
                                <input type="hidden" name="guest_count" id="formGuestCount">
                                <input type="hidden" name="cat_ids" id="formCatIds"> <input type="hidden" name="cart_json" id="formCartJson">

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-secondary">Reservation Date</label>
                                        <input type="date" name="booking_date" class="form-control form-control-lg fs-6 rounded-3" id="flowDate" required min="<?= date('Y-m-d') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-secondary">Arrival Time Slot</label>
                                        <input type="time" name="booking_time" class="form-control form-control-lg fs-6 rounded-3" id="flowTime" required>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-3">
                                    <button type="button" class="btn btn-outline-secondary btn-sm px-4 rounded-pill" id="btnScheduleBack">Back</button>
                                    <button type="submit" class="btn btn-dark btn-sm px-4 rounded-pill fw-medium d-inline-flex align-items-center gap-2">
                                        Proceed To Choose Table Seat <i class="bi bi-arrow-right"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

                <div class="col-lg-5 col-xl-4">
                    <div class="card summary-card p-4">
                        <h4 style="font-family: 'Playfair Display', serif;" class="fw-bold text-dark mb-3 pb-2 border-bottom">Order Summary</h4>

                        <div id="summaryBagItems" class="mb-4" style="max-height: 240px; overflow-y: auto; padding-right: 4px;">
                            <div class="text-center text-muted py-3 small">Your checkout basket is currently empty.</div>
                        </div>

                        <div class="bg-light rounded-3 p-3 mb-4 border border-light small" id="summaryMetaBlock" style="display:none;">
                            <div class="h6 fw-bold mb-2 text-dark text-uppercase tracking-wider" style="font-size:0.75rem;">Booking Parameters</div>
                            <div class="d-flex justify-content-between text-muted mb-1" id="metaTypeRow" style="display:none;">
                                <span>Service Type:</span> <strong class="text-dark" id="lblMetaType">-</strong>
                            </div>
                            <div class="d-flex justify-content-between text-muted mb-1" id="metaGuestsRow" style="display:none;">
                                <span>Total Guests:</span> <strong class="text-dark" id="lblMetaGuests">-</strong>
                            </div>
                            <div class="d-flex justify-content-between text-muted mb-1" id="metaCatRow" style="display:none;">
                                <span>Cat Companions:</span> <strong class="text-dark" id="lblMetaCat">-</strong>
                            </div>
                            <div class="d-flex justify-content-between text-muted" id="metaSchedRow" style="display:none;">
                                <span>Schedule Slot:</span> <strong class="text-dark" id="lblMetaSched">-</strong>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <div>
                                <span class="text-muted d-block small fw-medium">GRAND TOTAL</span>
                                <span class="text-muted text-uppercase" style="font-size: 10px;">VAT Inclusive</span>
                            </div>
                            <div class="text-end">
                                <div class="h3 fw-bold text-dark mb-0" id="lblGrandTotal">₱0.00</div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
       
        let chosenCatsArray = []; 

        document.addEventListener('DOMContentLoaded', () => {
            renderSummarySidebar();
            document.getElementById('flowDate').addEventListener('change', updateMetaSummaryView);
            document.getElementById('flowTime').addEventListener('change', updateMetaSummaryView);
            
            localStorage.setItem('guest_count', document.getElementById('guestCount').value);
        });

        function renderSummarySidebar() {
            const container = document.getElementById('summaryBagItems');
            const totalLabel = document.getElementById('lblGrandTotal');
            let currentBag = JSON.parse(localStorage.getItem('cafe_bag')) || [];

            if (currentBag.length === 0) {
                container.innerHTML = '<div class="text-center text-muted py-4 small"><i class="bi bi-cart-x d-block fs-3 mb-2"></i>No items inside checkout bag array yet.</div>';
                totalLabel.innerText = "₱0.00";
                return;
            }

            let htmlString = "";
            let calculatedRunningSum = 0;

            currentBag.forEach(item => {
                let itemTotal = item.price * item.quantity;
                calculatedRunningSum += itemTotal;

                htmlString += `
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div style="max-width:70%;">
                            <div class="h6 mb-0 fw-semibold text-dark small text-truncate">${item.name}</div>
                            <span class="text-muted text-opacity-75" style="font-size:0.75rem;">₱${parseFloat(item.price).toFixed(2)} × ${item.quantity}</span>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold text-dark small">₱${itemTotal.toFixed(2)}</span>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = htmlString;
            totalLabel.innerText = "₱" + calculatedRunningSum.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            updateMetaSummaryView();
        }

        function addExtraToBag(id, name, price, buttonRef) {
            let currentBag = JSON.parse(localStorage.getItem('cafe_bag')) || [];
            const searchIdx = currentBag.findIndex(item => item.id === id);

            if (searchIdx > -1) {
                currentBag[searchIdx].quantity += 1;
            } else {
                currentBag.push({
                    id: id,
                    name: name,
                    price: parseFloat(price),
                    quantity: 1
                });
            }
            localStorage.setItem('cafe_bag', JSON.stringify(currentBag));
            renderSummarySidebar();

            const originalHTML = buttonRef.innerHTML;
            buttonRef.className = "btn btn-success btn-mini-add text-white border-success";
            buttonRef.innerHTML = '<i class="bi bi-check2"></i> Added!';
            buttonRef.disabled = true;

            setTimeout(() => {
                buttonRef.className = "btn btn-outline-dark btn-mini-add";
                buttonRef.innerHTML = '<i class="bi bi-plus-lg"></i> Add Extra';
                buttonRef.disabled = false;
            }, 1200);
        }
 
        function handleGuestCountChange() {
            const guestSelect = document.getElementById('guestCount');
            localStorage.setItem('guest_count', guestSelect.value);
            
            const maxAllowed = parseInt(guestSelect.value, 10) * 2;
            if(chosenCatsArray.length > maxAllowed) {
                chosenCatsArray = [];
                localStorage.removeItem('selected_cats_json');
                localStorage.removeItem('summary_cat_names');
                
                document.querySelectorAll('.cat-card-professional').forEach(c => {
                    c.classList.remove('selected', 'disabled-card');
                    c.querySelector('.check-icon').classList.add('d-none');
                });
                alert("Guest configuration updated! Your cat selection has been reset to match your group's layout limit.");
            }
            
            updateCatUiLimits();
            updateMetaSummaryView();
        }

        function updateCatUiLimits() {
            const guests = parseInt(localStorage.getItem('guest_count') || '1', 10);
            const maxCats = guests * 2;
            
            document.getElementById('catLimitCounter').innerText = `Selected: ${chosenCatsArray.length} / ${maxCats}`;
            
            const warningBanner = document.getElementById('catLimitWarning');
            if (chosenCatsArray.length >= maxCats) {
                warningBanner.classList.remove('d-none');
                warningBanner.classList.add('d-flex');
                
                document.querySelectorAll('.cat-card-professional').forEach(card => {
                    if(!card.classList.contains('selected')) {
                        card.classList.add('disabled-card');
                    }
                });
            } else {
                warningBanner.classList.add('d-none');
                warningBanner.classList.remove('d-flex');
                document.querySelectorAll('.cat-card-professional').forEach(card => {
                    card.classList.remove('disabled-card');
                });
            }
        }

        function toggleCatSelection(cardElement) {
            const catId = cardElement.getAttribute('data-id');
            const catName = cardElement.getAttribute('data-name');
            const guests = parseInt(localStorage.getItem('guest_count') || '1', 10);
            const maxCats = guests * 2;

            const existingIndex = chosenCatsArray.findIndex(item => item.id === catId);

            if (existingIndex > -1) {
                chosenCatsArray.splice(existingIndex, 1);
                cardElement.classList.remove('selected');
                cardElement.querySelector('.check-icon').classList.add('d-none');
            } else {
                if (chosenCatsArray.length >= maxCats) {
                    return; 
                }
                chosenCatsArray.push({ id: catId, name: catName });
                cardElement.classList.add('selected');
                cardElement.querySelector('.check-icon').classList.remove('d-none');
            }

            const namesString = chosenCatsArray.map(c => c.name).join(', ');
            localStorage.setItem('summary_cat_names', namesString);
            localStorage.setItem('selected_cats_json', JSON.stringify(chosenCatsArray));

            document.getElementById('btnScheduleBack').setAttribute('onclick', "navigateToStep('step-cat-select')");
            updateCatUiLimits();
            updateMetaSummaryView();
        }

        function skipCatSelection() {
            chosenCatsArray = [];
            localStorage.removeItem('selected_cats_json');
            localStorage.removeItem('summary_cat_names');
            
            document.querySelectorAll('.cat-card-professional').forEach(c => {
                c.classList.remove('selected', 'disabled-card');
                c.querySelector('.check-icon').classList.add('d-none');
            });

            document.getElementById('btnScheduleBack').setAttribute('onclick', "navigateToStep('step-cat-query')");
            navigateToStep('step-schedule');
        }

        function updateMetaSummaryView() {
            const metaBlock = document.getElementById('summaryMetaBlock');
            const typeRow = document.getElementById('metaTypeRow');
            const guestsRow = document.getElementById('metaGuestsRow');
            const catRow = document.getElementById('metaCatRow');
            const schedRow = document.getElementById('metaSchedRow');

            const typeValue = localStorage.getItem('order_type');
            const guestValue = localStorage.getItem('guest_count');
            const catNamesValue = localStorage.getItem('summary_cat_names');
            const dateValue = document.getElementById('flowDate').value;
            const timeValue = document.getElementById('flowTime').value;

            let showingAny = false;

            if (typeValue) {
                document.getElementById('lblMetaType').innerText = typeValue;
                typeRow.style.display = "flex";
                showingAny = true;
            } else {
                typeRow.style.display = "none";
            }

            if (typeValue === 'Pre-order' && guestValue) {
                document.getElementById('lblMetaGuests').innerText = `${guestValue} Pax`;
                guestsRow.style.display = "flex";
                showingAny = true;
            } else {
                guestsRow.style.display = "none";
            }

            if (typeValue === 'Pre-order' && catNamesValue && chosenCatsArray.length > 0) {
                document.getElementById('lblMetaCat').innerText = catNamesValue;
                catRow.style.display = "flex";
                showingAny = true;
            } else {
                catRow.style.display = "none";
            }

            if (typeValue === 'Pre-order' && (dateValue || timeValue)) {
                let readableDate = dateValue ? dateValue : '--/--';
                let readableTime = timeValue ? timeValue : '--:--';
                document.getElementById('lblMetaSched').innerText = `${readableDate} @ ${readableTime}`;
                schedRow.style.display = "flex";
                showingAny = true;
            } else {
                schedRow.style.display = "none";
            }

            metaBlock.style.display = showingAny ? "block" : "none";
        }

        function navigateToStep(stepId) {
            document.querySelectorAll('.step-container').forEach(s => s.classList.remove('active'));
            document.getElementById(stepId).classList.add('active');

            const stepMapping = {
                'step-type': 1,
                'step-cat-query': 2,
                'step-cat-select': 3,
                'step-schedule': 4
            };
            const currentStepNumber = stepMapping[stepId];

            document.querySelectorAll('.step-circle').forEach((circle, idx) => {
                const stepNum = idx + 1;
                circle.classList.remove('active', 'completed');
                if (stepNum === currentStepNumber) {
                    circle.classList.add('active');
                } else if (stepNum < currentStepNumber) {
                    circle.classList.add('completed');
                }
            });

            if(stepId === 'step-cat-select') {
                updateCatUiLimits();
            }
            updateMetaSummaryView();
        }

        function setOrderType(type) {
    if (type === 'Delivery') {
        window.location.href = 'delivery.php';
    } else {
        document.getElementById('formOrderType').value = type;
        localStorage.setItem('order_type', type);
        navigateToStep('step-cat-query');
    }
}

        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const dateInput = document.getElementById('flowDate').value;
            const timeInput = document.getElementById('flowTime').value;
            let currentBag = JSON.parse(localStorage.getItem('cafe_bag')) || [];

            if (!dateInput || !timeInput) {
                alert('Please provide both an arrival Date and Time slot selection.');
                return;
            }

            if (currentBag.length === 0) {
                alert('Your order summary bag is empty! Please add some extra treats before proceeding.');
                return;
            }

            localStorage.setItem('booking_date', dateInput);
            localStorage.setItem('booking_time', timeInput);

            document.getElementById('formOrderType').value = localStorage.getItem('order_type') || 'Pre-order';
            document.getElementById('formGuestCount').value = localStorage.getItem('guest_count') || '1';
            
            const idList = chosenCatsArray.map(c => c.id);
            document.getElementById('formCatIds').value = JSON.stringify(idList);
            document.getElementById('formCartJson').value = JSON.stringify(currentBag);

            this.submit();
        });
    </script>
</body>
</html>