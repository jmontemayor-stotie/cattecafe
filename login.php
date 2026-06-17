<?php
require_once 'config.php';

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) && isset($_POST['password'])) {
    
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM customer_tbl WHERE email = '$email'");

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['PASSWORD'])) {
            $_SESSION['user_id'] = $user['customer_id'] ?? $user['id']; 
            $_SESSION['user_fname'] = $user['Fname'];
            
            header("Location: index.php");
            exit();
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Back - Cat Cafe</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght=0,400..900;1,400..900&family=Poppins:wght=300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
</head>

<body>

    <div class="container py-5 d-flex justify-content-center">
        <div class="card auth-card shadow">
            <div class="row g-0">
                
                <div class="col-lg-5 d-none d-lg-flex auth-side-panel flex-column justify-content-between text-center text-lg-start">
                    <div>
                        <a href="index.php" class="text-white text-decoration-none d-inline-flex align-items-center mb-4">
                            <i class="bi bi-cat-fill me-2 fs-4" style="color: var(--brand-pink);"></i>
                            <span class="fw-bold tracking-wider fs-5">CAT CAFE</span>
                        </a>
                    </div>
                    
                    <div class="my-auto py-4">
                        <div class="h2 display-6 fw-bold text-white mb-3">Welcome Back, Friend!</div>
                        <div class="text-white-50 font-weight-light small lh-lg">
                            Log in to view your current lounge bookings, manage your purr-fect loyalty reward points, and see if any new rescue cats have arrived since your last visit.
                        </div>
                    </div>
                    
                    <div>
                        <div class="small text-white-50 mb-0">&copy; 2026 Cat Cafe Lounge.</div>
                    </div>
                </div>

                <div class="col-lg-7 p-4 p-sm-5 d-flex flex-column justify-content-center bg-white">
                    
                    <div class="d-lg-none text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center mb-2" style="color: var(--brand-dark);">
                            <i class="bi bi-cat-fill fs-2 me-2" style="color: var(--brand-pink-hover);"></i>
                            <span class="fw-bold fs-4 brand-title">CAT CAFE</span>
                        </div>
                    </div>

                    <div class="mb-4 text-center text-lg-start">
                        <div class="h2 fw-bold text-dark mb-1">Sign In</div>
                        <div class="text-muted small">Welcome back! Please sign in to your dashboard.</div>
                    </div>

                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger text-center py-2 rounded-3 small mb-3">
                            <i class="bi bi-exclamation-circle me-1"></i> <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST" autocomplete="off">
                        
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control rounded-3" id="emailAddress" name="email" placeholder="name@example.com" required>
                            <label for="emailAddress">Email Address</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" class="form-control rounded-3" id="userPassword" name="password" placeholder="Password" required>
                            <label for="userPassword">Password</label>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4 small">
                            <div class="form-check text-start">
                                <input class="form-check-input" type="checkbox" value="" id="rememberMe">
                                <label class="form-check-label text-muted" for="rememberMe">
                                    Remember me
                                </label>
                            </div>
                            <div>
                                <a href="#" class="text-decoration-none fw-medium" style="color: #d06a93;">Forgot Password?</a>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-submit w-100 mb-4 shadow-sm">
                            Log In
                        </button>

                        <div class="text-center">
                            <div class="small text-muted mb-3">New to our community? <a href="signup.php" class="fw-semibold text-decoration-none" style="color: #d06a93;">Create an Account</a></div>
                            <hr class="w-25 mx-auto opacity-25 my-3">
                            <a href="index.php" class="btn-back d-inline-flex align-items-center gap-2">
                                <i class="bi bi-arrow-left"></i> Back to Homepage
                            </a>
                        </div>

                    </form>

                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>