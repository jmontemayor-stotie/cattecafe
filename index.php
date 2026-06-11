<?php
require_once 'config.php';
$sql = "SELECT * FROM cat_tbl"; 
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catte Cafe - Sweet Treats & Purrfect Moments</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2=family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
     <?php include 'navbar.php'; ?>

    <section class="hero">
        <div class="hero-content px-3">
            <div class="h1 hero-title">
                Sweet Treats &<br>Purrfect Moments
            <div>
            <div class="hero-text">
                Enjoy delicious artisanal cakes, premium brewed coffees, and unforgettable <br class="d-none d-md-inline">
                moments wrapped in the cozy company of our lovable resident cats.
</div>
            <a href="menu.php" class="btn btn-pink px-5 py-3 shadow smooth-transition text-decoration-none">
                Explore Menu <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </section>

    <section class="container py-5 my-4">
        <div class="row g-4 text-center justify-content-center">
            <div class="col-md-4 px-4">
                <div class="mb-3 text-center">
                    <div class="d-inline-flex align-items-center justify-content-center bg-white shadow-sm rounded-circle" style="width: 70px; height: 70px;">
                        <i class="bi bi-cup-hot fs-3" style="color: var(--brand-pink-hover);"></i>
                    </div>
                </div>
                <div class="h4 fw-bold mb-2">Artisanal Bakery</div>
                <div class="text-muted small">Freshly baked pastries and crafted espresso selections prepared daily by our expert baristas.</div>
            </div>
            <div class="col-md-4 px-4">
                <div class="mb-3 text-center">
                    <div class="d-inline-flex align-items-center justify-content-center bg-white shadow-sm rounded-circle" style="width: 70px; height: 70px;">
                        <i class="bi bi-heart fs-3" style="color: var(--brand-pink-hover);"></i>
                    </div>
                </div>
                <div class="h4 fw-bold mb-2">Therapeutic Space</div>
                <div class="text-muted small">Unwind and de-stress in an environment designed strictly for relaxation, warmth, and joy.</div>
            </div>
            <div class="col-md-4 px-4">
                <div class="mb-3 text-center">
                    <div class="d-inline-flex align-items-center justify-content-center bg-white shadow-sm rounded-circle" style="width: 70px; height: 70px;">
                        <i class="bi bi-shield-check fs-3" style="color: var(--brand-pink-hover);"></i>
                    </div>
                </div>
                <div class="h4 fw-bold mb-2">Safe & Happy Felines</div>
                <div class="text-muted small">Our rescued companions live in highly hygienic spaces with dedicated vet checks and infinite love.</div>
            </div>
        </div>
    </section>

    <hr class="container opacity-25">

    <section class="container py-5">
        <div class="text-center mb-5">
            <span class="section-badge">From Our Kitchen</span>
            <div class="h2 section-title">Explore Our Signature Delights</div>
            <div class="text-muted mt-2">Handcrafted treats made with love to complement your feline friend date.</div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 menu-card smooth-transition d-flex flex-column justify-content-between">
                    <div>
                        <div class="card-icon-header">
                            <div class="card-icon-circle">
                                <i class="bi bi-cake2"></i>
                            </div>
                        </div>
                        <div class="card-body text-center p-4">
                            <div class="h5 card-title fw-bold mb-2">Cakes</div>
                            <div class="card-text text-muted small">
                                Decadent and rich homemade layered signature cakes, crafted perfectly for sweet cravings.
</div>
                        </div>
                    </div>
                    <div class="p-4 pt-0">
                        <a href="menu.php#pane-cakes" class="btn btn-outline-custom w-100 smooth-transition">Order Now</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 menu-card smooth-transition d-flex flex-column justify-content-between">
                    <div>
                        <div class="card-icon-header">
                            <div class="card-icon-circle">
                                <i class="bi bi-cookie"></i>
                            </div>
                        </div>
                        <div class="card-body text-center p-4">
                            <div class="h5 card-title fw-bold mb-2">Cookies</div>
                            <div class="card-text text-muted small">
                                Freshly baked, crisp on the edges and soft-centered premium cookies straight from the oven.
</div>
                        </div>
                    </div>
                    <div class="p-4 pt-0">
                        <a href="menu.php#pane-cookies" class="btn btn-outline-custom w-100 smooth-transition">Order Now</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 menu-card smooth-transition d-flex flex-column justify-content-between">
                    <div>
                        <div class="card-icon-header">
                            <div class="card-icon-circle">
                                <i class="bi bi-grid-3x3-gap"></i>
                            </div>
                        </div>
                        <div class="card-body text-center p-4">
                            <div class="h5 card-title fw-bold mb-2">Brownies</div>
                            <div class="card-text text-muted small">
                                Fudgy, dense, and premium rich dark chocolate square brownies dusted with love.
</div>
                        </div>
                    </div>
                    <div class="p-4 pt-0">
                        <a href="menu.php#pane-brownies" class="btn btn-outline-custom w-100 smooth-transition">Order Now</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 menu-card smooth-transition d-flex flex-column justify-content-between">
                    <div>
                        <div class="card-icon-header">
                            <div class="card-icon-circle">
                                <i class="bi bi-cup-straw"></i>
                            </div>
                        </div>
                        <div class="card-body text-center p-4">
                            <div class="h5 card-title fw-bold mb-2">Drinks</div>
                            <div class="card-text text-muted small">
                                Expertly brewed hot espresso blends and refreshing, artisanal iced companion creations.
</div>
                        </div>
                    </div>
                    <div class="p-4 pt-0">
                        <a href="menu.php#pane-drinks" class="btn btn-outline-custom w-100 smooth-transition">Order Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-5 mt-5" style="background-color: var(--brand-dark); color: rgba(255,255,255,0.75);">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5 col-md-12">
                    <div class="h5 text-white fw-bold mb-3"><i class="bi bi-cat-fill me-2 text-pink" style="color: var(--brand-pink);"></i> CAT CAFE</div>
                    <div class="small text-muted mb-0" style="max-width: 380px;">
                        A safe sanctuary where coffee aroma marries sweet purrs. Come visit us for unique baked pastries and cozy feline therapy.
</div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="h6 text-white fw-semibold mb-3 small text-uppercase" style="letter-spacing: 1px;">Quick Links</div>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="menu.php" class="text-decoration-none link-light opacity-75">Our Pastry Menu</a></li>
                        <li class="mb-2"><a href="cat.php" class="text-decoration-none link-light opacity-75">Meet the Cats</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none link-light opacity-75">Book a Reservation</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="h6 text-white fw-semibold mb-3 small text-uppercase" style="letter-spacing: 1px;">Hours & Location</div>
                    <div class="small mb-1 text-light opacity-75"><i class="bi bi-clock me-2"></i> Mon - Sun: 9:00 AM - 9:00 PM</div>
                    <div class="small text-light opacity-75"><i class="bi bi-geo-alt me-2"></i> Purok 3A San Rafel, Sto Tomas Batangas</div>
                </div>
            </div>
            <hr class="my-4 opacity-10">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center small">
                <div class="mb-0 text-muted">&copy; <?php echo date("Y"); ?> Cat Cafe. All rights reserved.</div>
                <div class="d-flex gap-3 mt-3 mt-sm-0">
                    <a href="#" class="text-white opacity-75"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-white opacity-75"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-white opacity-75"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>