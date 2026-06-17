<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$status_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_profile'])) {
    $fname   = trim($_POST['Fname']);
    $lname   = trim($_POST['Lname']);
    $contact = trim($_POST['contact']);

    $stmt = $conn->prepare("UPDATE customer_tbl SET Fname=?, Lname=?, contact=? WHERE customer_id=?");
    $stmt->bind_param("sssi", $fname, $lname, $contact, $user_id);
    $stmt->execute();
    $stmt->close();
    $status_message = "saved";
}

$stmt = $conn->prepare("SELECT * FROM customer_tbl WHERE customer_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("SELECT * FROM order_tbl WHERE customer_id = ? ORDER BY order_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
$stmt->close();

$stmt = $conn->prepare("
    SELECT od.order_id, od.quantity, od.sub_total, m.item_name
    FROM orderdetails_tbl od
    JOIN menuitem_tbl m ON od.item_id = m.item_id
    WHERE od.order_id IN (
        SELECT order_id FROM order_tbl WHERE customer_id = ?
    )
    ORDER BY od.order_id DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$details_result = $stmt->get_result();
$stmt->close();

$order_items = [];
while ($d = $details_result->fetch_assoc()) {
    $order_items[$d['order_id']][] = $d;
}

$stmt = $conn->prepare("
    SELECT cb.*, c.cat_name
    FROM catbooking_tbl cb
    LEFT JOIN cat_tbl c ON cb.cat_id = c.cat_id
    WHERE cb.customer_id = ?
    ORDER BY cb.booking_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings_result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard | Cat Cafe Lounge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --rose:    #d46a94;
            --rose-dk: #b95d82;
            --ink:     #1a1a2e;
            --surface: #ffffff;
            --muted:   #6b7280;
            --bg:      #f5f4f2;
            --border:  #e5e7eb;
            --radius:  18px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            padding-top: 90px;
            padding-bottom: 60px;
            color: var(--ink);
        }

        .panel {
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
            padding: 1.75rem;
        }

        .avatar {
            width: 72px; height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f9d0e2, #f3a8c7);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem;
            margin: 0 auto 1rem;
            border: 3px solid #fff;
            box-shadow: 0 4px 12px rgba(212,106,148,0.25);
        }

        .form-control {
            border-radius: 10px;
            border: 1.5px solid var(--border);
            padding: 0.6rem 0.9rem;
            font-size: 0.875rem;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            border-color: var(--rose);
            box-shadow: 0 0 0 3px rgba(212,106,148,0.12);
        }
        .form-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }

        .btn-rose {
            background: var(--rose);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.6rem 1.2rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: background 0.2s, transform 0.15s;
        }
        .btn-rose:hover { background: var(--rose-dk); color: #fff; transform: translateY(-1px); }

        .tab-strip {
            display: flex;
            gap: 6px;
            border-bottom: 2px solid var(--border);
            margin-bottom: 1.25rem;
        }
        .tab-btn {
            background: none;
            border: none;
            padding: 8px 16px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--muted);
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            cursor: pointer;
            transition: color 0.2s, border-color 0.2s;
        }
        .tab-btn.active { color: var(--rose); border-bottom-color: var(--rose); }
        .tab-pane { display: none; }
        .tab-pane.active { display: block; }

        .order-card {
            border: 1.5px solid var(--border);
            border-radius: 14px;
            margin-bottom: 12px;
            overflow: hidden;
            transition: box-shadow 0.2s;
        }
        .order-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.07); }

        .order-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            background: #fafafa;
            border-bottom: 1px solid var(--border);
            cursor: pointer;
            gap: 10px;
        }
        .order-header .order-id {
            font-weight: 700;
            font-size: 0.875rem;
        }
        .order-header .order-meta {
            font-size: 0.75rem;
            color: var(--muted);
        }
        .order-header .chevron {
            color: var(--muted);
            font-size: 0.8rem;
            transition: transform 0.25s;
        }
        .order-card.open .chevron { transform: rotate(180deg); }

        .order-body {
            display: none;
            padding: 12px 16px;
        }
        .order-card.open .order-body { display: block; }

        .item-line {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            padding: 5px 0;
            border-bottom: 1px dashed #f0f0f0;
            color: var(--ink);
        }
        .item-line:last-child { border-bottom: none; }

        .order-total {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            font-weight: 700;
            padding-top: 10px;
            margin-top: 6px;
            border-top: 1.5px solid var(--border);
        }

        .tx-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }
        .tx-row:last-child { border-bottom: none; }

        .tx-icon {
            width: 40px; height: 40px; min-width: 40px;
            border-radius: 12px;
            background: #fdf2f7;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
            color: var(--rose);
        }
        .tx-meta { flex: 1; overflow: hidden; }
        .tx-meta .title { font-weight: 600; font-size: 0.875rem; }
        .tx-meta .sub   { font-size: 0.75rem; color: var(--muted); }

        .pill {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            white-space: nowrap;
        }
        .pill-pending   { background: #fef9c3; color: #854d0e; }
        .pill-confirmed { background: #dcfce7; color: #166534; }
        .pill-cancelled { background: #fee2e2; color: #991b1b; }
        .pill-paid      { background: #dbeafe; color: #1e40af; }
        .pill-default   { background: #f3f4f6; color: #374151; }

        .empty-state { text-align: center; padding: 2.5rem 1rem; color: var(--muted); }
        .empty-state i { font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4; }

        .toast-saved {
            position: fixed; bottom: 24px; right: 24px;
            background: #1a1a2e; color: #fff;
            padding: 12px 20px; border-radius: 12px;
            font-size: 0.85rem; font-weight: 500;
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
            display: flex; align-items: center; gap: 8px;
            opacity: 0; transform: translateY(12px);
            transition: opacity 0.3s, transform 0.3s;
            z-index: 9999;
            pointer-events: none;
        }
        .toast-saved.show { opacity: 1; transform: translateY(0); }

        @media (max-width: 768px) {
            body { padding-top: 70px; }
            .panel { padding: 1.25rem; }
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container" style="max-width: 1060px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="h4 fw-bold mb-0" style="font-family:'Playfair Display',serif;">My Dashboard</div>
            <div class="text-muted small mb-0">Manage your profile and orders</div>
        </div>
        <a href="index.php" class="btn btn-outline-dark btn-sm rounded-pill px-3">
            <i class="bi bi-shop me-1"></i> Shop
        </a>
    </div>

    <div class="row g-4">

        <div class="col-12 col-lg-4">
            <div class="panel">
                <div class="avatar"><i class="bi bi-person-heart"></i></div>
                <div class="text-center fw-bold mb-0"><?= htmlspecialchars($user['Fname'] . ' ' . $user['Lname']) ?></div>
                <div class="text-center text-muted small mb-4"><?= htmlspecialchars($user['email'] ?? '') ?></div>
                <hr class="my-3">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" name="Fname" class="form-control" value="<?= htmlspecialchars($user['Fname']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="Lname" class="form-control" value="<?= htmlspecialchars($user['Lname']) ?>" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="contact" class="form-control" value="<?= htmlspecialchars($user['contact']) ?>">
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-rose w-100">
                        <i class="bi bi-check2 me-1"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="panel">

                <div class="tab-strip">
                    <button class="tab-btn active" onclick="switchTab('orders', this)">
                        <i class="bi bi-receipt me-1"></i> Orders
                    </button>
                    <button class="tab-btn" onclick="switchTab('bookings', this)">
                        <i class="bi bi-calendar-heart me-1"></i> Bookings
                    </button>
                </div>

                <div id="tab-orders" class="tab-pane active">
                    <?php if ($orders_result->num_rows === 0): ?>
                        <div class="empty-state">
                            <i class="bi bi-bag-x"></i>
                            <div class="mb-0 fw-semibold">No orders yet</div>
                            <a href="menu.php" class="btn btn-rose btn-sm mt-3 px-4">Browse Menu</a>
                        </div>
                    <?php else: ?>
                        <?php while ($row = $orders_result->fetch_assoc()):
                            $status = strtolower($row['order_status']);
                            $pillClass = match($status) {
                                'pending'   => 'pill-pending',
                                'confirmed' => 'pill-confirmed',
                                'cancelled' => 'pill-cancelled',
                                'paid'      => 'pill-paid',
                                default     => 'pill-default'
                            };
                            $items = $order_items[$row['order_id']] ?? [];
                        ?>
                        <div class="order-card" id="order-<?= $row['order_id'] ?>">
                            <div class="order-header" onclick="toggleOrder(<?= $row['order_id'] ?>)">
                                <div>
                                    <div class="order-id">Order #<?= $row['order_id'] ?> &mdash; <?= htmlspecialchars($row['order_type']) ?></div>
                                    <div class="order-meta"><?= date('M d, Y', strtotime($row['order_date'])) ?> &bull; ₱<?= number_format($row['total_amount'], 2) ?></div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="pill <?= $pillClass ?>"><?= htmlspecialchars($row['order_status']) ?></span>
                                    <i class="bi bi-chevron-down chevron"></i>
                                </div>
                            </div>
                            <div class="order-body">
                                <?php if (empty($items)): ?>
                                    <div class="text-muted small mb-0">No item details available.</div>
                                <?php else: ?>
                                    <?php foreach ($items as $item): ?>
                                        <div class="item-line">
                                            <span><?= htmlspecialchars($item['item_name']) ?> &times; <?= $item['quantity'] ?></span>
                                            <span>₱<?= number_format($item['sub_total'], 2) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                    <div class="order-total">
                                        <span>Total</span>
                                        <span>₱<?= number_format($row['total_amount'], 2) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>

                <div id="tab-bookings" class="tab-pane">
                    <?php if ($bookings_result->num_rows === 0): ?>
                        <div class="empty-state">
                            <i class="bi bi-calendar-x"></i>
                            <div class="mb-0 fw-semibold">No bookings yet</div>
                            <a href="checkout.php" class="btn btn-rose btn-sm mt-3 px-4">Book a Visit</a>
                        </div>
                    <?php else: ?>
                        <?php while ($row = $bookings_result->fetch_assoc()):
                            $status = strtolower($row['booking_status']);
                            $pillClass = match($status) {
                                'pending'   => 'pill-pending',
                                'confirmed' => 'pill-confirmed',
                                'cancelled' => 'pill-cancelled',
                                default     => 'pill-default'
                            };
                        ?>
                        <div class="tx-row">
                            <div class="tx-icon"><i class="bi bi-calendar-heart"></i></div>
                            <div class="tx-meta">
                                <div class="title">
                                    <?= $row['cat_name'] ? 'With ' . htmlspecialchars($row['cat_name']) : 'Cat Lounge Visit' ?>
                                </div>
                                <div class="sub">
                                    <?= date('M d, Y', strtotime($row['booking_date'])) ?>
                                    <?= !empty($row['booking_time']) ? ' @ ' . date('g:i A', strtotime($row['booking_time'])) : '' ?>
                                </div>
                            </div>
                            <span class="pill <?= $pillClass ?>"><?= htmlspecialchars($row['booking_status']) ?></span>
                        </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="toast-saved" id="toastSaved">
    <i class="bi bi-check-circle-fill text-success"></i> Profile saved successfully.
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function switchTab(name, btn) {
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + name).classList.add('active');
        btn.classList.add('active');
    }

    function toggleOrder(id) {
        const card = document.getElementById('order-' + id);
        card.classList.toggle('open');
    }

    <?php if ($status_message === 'saved'): ?>
    document.addEventListener('DOMContentLoaded', () => {
        const toast = document.getElementById('toastSaved');
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    });
    <?php endif; ?>
</script>
</body>
</html>