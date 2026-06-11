<?php
include 'config.php';

$sql = "SELECT * FROM cat_tbl";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cat Profiles - Cat Cafe</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/cat.css">
</head>

<body>
    

   <?php include 'navbar.php'; ?>

    <header class="container text-center py-5">
        <div class="h2 section-title display-5 fw-bold">Meet Our Residents</div>
        <div class="text-muted mx-auto" style="max-width: 600px;">
            Every cat at our cafe has a unique story and personality. Click on their profiles to learn more about your future furry friends!
</div>
    </header>

    <section class="container pb-5">
        <div class="row g-4">

            <?php 
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $imageName = basename($row['img']);
                    $imagePath = "Cat/" . $imageName;
                    
                    $catName = ucfirst(htmlspecialchars($row['cat_name']));
                    $breed = ucfirst(htmlspecialchars($row['breed']));
                    $gender = ucfirst(htmlspecialchars($row['gender']));
                    $age = htmlspecialchars($row['age']);
                    $description = htmlspecialchars($row['description']);
                    $catId = $row['cat_id'];
            ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card cat-card">
                            <div class="cat-img-container">
                                <img src="<?php echo $imagePath; ?>" alt="<?php echo $catName; ?>">
                            </div>
                            <div class="card-body text-center p-4 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="h3 cat-name h4 mb-1"><?php echo $catName; ?></div>
                                    <div class="small text-muted mb-3"><?php echo $breed; ?></div>
                                    <div class="mb-4">
                                        <span class="trait-tag"><?php echo $gender; ?></span>
                                        <span class="trait-tag"><?php echo $age; ?> <?php echo ($age == 1) ? 'Year' : 'Years'; ?> Old</span>
                                    </div>
                                </div>
                                <button class="btn btn-view-profile w-100 mt-auto" data-bs-toggle="modal" data-bs-target="#catModal<?php echo $catId; ?>">
                                    View Profile
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="catModal<?php echo $catId; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="row g-0">
                                    <div class="col-md-5">
                                        <div class="modal-cat-img-wrap">
                                            <img src="<?php echo $imagePath; ?>" class="modal-cat-img" alt="<?php echo $catName; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="modal-body p-5">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <div class="h2 cat-name h1 mb-1"><?php echo $catName; ?></div>
                                                    <div class="text-pink fw-bold" style="color: #d06a93;">
                                                        <?php echo $age; ?> <?php echo ($age == 1) ? 'Year' : 'Years'; ?> Old • <?php echo $gender; ?>
</div>
                                                </div>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            
                                            <hr class="my-4 opacity-10">

                                            <div class="h5 fw-bold mb-3">About <?php echo $catName; ?></div>
                                            <div class="text-muted">
                                                <?php echo $description; ?>
</div>

                                            <div class="row mt-4">
                                                <div class="col-6">
                                                    <div class="h6 fw-bold small text-uppercase text-muted">Breed</div>
                                                    <div><?php echo $breed; ?></div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="h6 fw-bold small text-uppercase text-muted">Status</div>
                                                    <div>Resident</div>
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <button class="btn btn-dark rounded-pill px-4" data-bs-dismiss="modal">Close Profile</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php 
                }
            } else {
                echo "<div class='col-12 text-center'><p class='text-muted'>No cats found in the records.</p></div>";
            }
            $conn->close();
            ?>

        </div>
    </section>

    <footer class="py-5 bg-dark text-white-50">
        <div class="container text-center">
            <div class="small mb-0">&copy; 2026 Cat Cafe. Purrfectly designed for cat lovers.</div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>