<style>
:root {
    --brand-pink: #f8b6d2;
    --brand-pink-hover: #f49ac2;
    --brand-dark: #2c2523;
    --brand-light: #fdfbf7;
}

body {
    font-family: 'Poppins', sans-serif;
    background-color: var(--brand-light);
    color: #4a4a4a;
    overflow-x: hidden;
}

h1, h2, h3, h4, .navbar-brand {
    font-family: 'Playfair Display', serif;
}

.smooth-transition {
    transition: all 0.3s ease-in-out;
}

.navbar {
    background: rgba(44, 37, 35, 0.85);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.navbar-brand {
    font-size: 1.6rem;
    letter-spacing: 1px;
}

.nav-link {
    font-weight: 500;
    font-size: 0.95rem;
    letter-spacing: 0.5px;
    margin: 0 5px;
    position: relative;
}

.nav-link::after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    bottom: 0;
    left: 0;
    background-color: var(--brand-pink);
    transition: width 0.3s ease;
}

.nav-link:hover::after {
    width: 100%;
}

.profile-dropdown-toggle::after,
.profile-dropdown-toggle:hover::after,
.profile-dropdown-toggle:focus::after,
.bag-nav-link::after,
.bag-nav-link:hover::after,
.bag-nav-link:focus::after {
    display: none !important;
    content: none !important;
    width: 0 !important;
    height: 0 !important;
}
</style>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top py-3">
    <div class="container">

        <a class="navbar-brand fw-bold text-white d-flex align-items-center text-decoration-none" href="index.php">
            <img src="Logo.svg" alt="Logo" style="height: 80px; width: auto; margin: -20px 10px -15px 0;">
            
            <span class="d-flex align-items-center">
                <i class="bi bi-cat-fill me-2 text-pink" style="color: var(--brand-pink);"></i> 
                CATTÉ CAFÉ
            </span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto text-center mt-3 mt-lg-0 align-items-center">
                <li class="nav-item">
                    <a class="nav-link text-white smooth-transition" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white smooth-transition" href="menu.php">Menu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white smooth-transition" href="cat.php">Cats</a>
                </li>
                
                <li class="nav-item mx-lg-2">
                    <a class="nav-link text-white d-inline-flex align-items-center px-2 py-1 rounded-circle smooth-transition bag-nav-link" href="bag.php" title="View My Bag" style="background-color: rgba(255,255,255,0.05);">
                        <i class="bi bi-bag-heart fs-5" style="color: var(--brand-pink);"></i>
                    </a>
                </li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown ms-lg-2 mt-2 mt-lg-0">
                        <a class="nav-link dropdown-toggle text-white text-decoration-none d-inline-flex align-items-center gap-2 px-3 py-2 rounded-5 profile-dropdown-toggle" 
                           href="#" 
                           role="button" 
                           data-bs-toggle="dropdown" 
                           aria-expanded="false"
                           style="background-color: rgba(255,255,255,0.08); font-weight: 500; border-bottom: none !important;">
                            <i class="bi bi-person-circle fs-5" style="color: var(--brand-pink);"></i> 
                            <span>Hi, <?php echo htmlspecialchars($_SESSION['user_fname'] ?? 'User'); ?>!</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-2 text-start" 
                            style="background-color: #ffffff; border-radius: 16px; min-width: 200px; padding: 8px;">
                            <li>
                                <a class="dropdown-item small py-2.5 rounded-3 d-flex align-items-center gap-2 text-secondary text-decoration-none" href="profile.php">
                                    <i class="bi bi-person fs-5" style="color: #d06a93;"></i> My Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item small py-2.5 rounded-3 d-flex align-items-center gap-2 text-secondary text-decoration-none" href="reservations.php">
                                    <i class="bi bi-calendar-check fs-5" style="color: #d06a93;"></i> My Bookings
                                </a>
                            </li>
                            <li><hr class="dropdown-divider opacity-25 my-2"></li>
                            <li>
                                <a class="dropdown-item small py-2.5 rounded-3 d-flex align-items-center gap-2 text-danger fw-medium text-decoration-none" href="logout.php">
                                    <i class="bi bi-box-arrow-right fs-5"></i> Log Out
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a class="btn btn-outline-light btn-sm px-3 rounded-pill" href="login.php">Log In</a>
                    </li>
                    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                        <a class="btn btn-light btn-sm px-3 rounded-pill fw-semibold text-dark" href="signup.php">Sign Up</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

    </div>
</nav>