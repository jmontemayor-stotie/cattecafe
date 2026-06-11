<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$status_message = "";

if (isset($_GET['action']) && $_GET['action'] == 'cancel' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $type = $_GET['type'];
    
    if ($type == 'order') {
        $conn->query("UPDATE order_tbl SET order_status = 'Cancelled' WHERE order_id = '$id' AND customer_id = '$user_id'");
    } else {
        $conn->query("UPDATE catbooking_tbl SET booking_status = 'Cancelled' WHERE booking_id = '$id' AND customer_id = '$user_id'");
    }
    header("Location: profile.php?msg=cancelled");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $fname = $conn->real_escape_string(trim($_POST['Fname']));
    $lname = $conn->real_escape_string(trim($_POST['Lname']));
    $contact = $conn->real_escape_string(trim($_POST['contact']));

    $conn->query("UPDATE customer_tbl SET Fname='$fname', Lname='$lname', contact='$contact' WHERE customer_id='$user_id'");
    $status_message = "<div class='alert alert-success'>Profile updated successfully.</div>";
}

$user = $conn->query("SELECT * FROM customer_tbl WHERE customer_id = '$user_id'")->fetch_assoc();
$orders = $conn->query("SELECT * FROM order_tbl WHERE customer_id = '$user_id' ORDER BY order_date DESC");
$bookings = $conn->query("SELECT cb.*, c.cat_name FROM catbooking_tbl cb LEFT JOIN cat_tbl c ON cb.cat_id = c.cat_id WHERE cb.customer_id = '$user_id' ORDER BY cb.booking_date DESC");


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard | Cat Cafe Lounge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --accent: #d46a94; --bg-gradient: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); }
        body { background: var(--bg-gradient); min-height: 100vh; font-family: 'Segoe UI', sans-serif; }
        
        .glass-card { 
            background: rgba(255, 255, 255, 0.9); 
            backdrop-filter: blur(10px); 
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            padding: 1.5rem;
        }
        
        .btn-accent { background: var(--accent); color: white; border-radius: 12px; transition: 0.3s; }
        .btn-accent:hover { background: #b95d82; color: white; transform: scale(1.02); }
        
        .profile-img-placeholder { 
            width: 80px; height: 80px; border-radius: 50%; 
            background: #eee; display: flex; align-items: center; 
            justify-content: center; font-size: 2rem; margin: auto; border: 4px solid #fff;
        }
        
        .status-pill { padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        
        .activity-row { transition: background 0.3s; padding: 12px; border-radius: 15px; }
        .activity-row:hover { background: #f8f9fa; }

        @media (max-width: 768px) {
            .container { padding: 10px; }
            .glass-card { border-radius: 16px; padding: 1rem; }
            h3 { font-size: 1.25rem; }
        }
    </style>
</head>
<body class="py-4">

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="h3 fw-bold"><i class="bi bi-grid-fill text-muted me-2"></i>Dashboard</div>
        <a href="index.php" class="btn btn-outline-dark rounded-pill px-3 btn-sm">Shop</a>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-4">
            <div class="glass-card text-center">
                <div class="profile-img-placeholder mb-3"><i class="bi bi-person-heart"></i></div>
                <div class="h5 fw-bold"><?= htmlspecialchars($user['Fname'] . ' ' . $user['Lname']) ?></div>
                <hr>
                <form method="POST" class="text-start">
                    <div class="mb-2">
                        <label class="small text-muted fw-bold">First Name</label>
                        <input type="text" name="Fname" class="form-control rounded-pill border-0 shadow-sm" value="<?= htmlspecialchars($user['Fname']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted fw-bold">Contact Number</label>
                        <input type="text" name="contact" class="form-control rounded-pill border-0 shadow-sm" value="<?= htmlspecialchars($user['contact']) ?>">
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-accent w-100 py-2">Save Changes</button>
                </form>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="glass-card h-100">
                <div class="h5 fw-bold mb-3">Recent Transactions</div>
                <?php while($row = $orders->fetch_assoc()): ?>
                    <div class="activity-row d-flex align-items-center border-bottom">
                        <div class="me-3 fs-5 text-secondary"><i class="bi bi-receipt"></i></div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="fw-bold text-dark text-truncate">Order #<?= $row['order_id'] ?></div>
                            <small class="text-muted"><?= $row['order_date'] ?> • ₱<?= number_format($row['total_amount'], 2) ?></small>
                        </div>
                        <div class="text-end ms-2">
                            <span class="status-pill badge bg-light text-dark border"><?= $row['order_status'] ?></span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transition = 'opacity 0.6s ease';
        setTimeout(() => {
            card.style.opacity = '1';
        }, index * 100);
    });
});
</script>
</body>
</html>