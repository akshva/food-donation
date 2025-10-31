<?php
// dashboard.php
include 'config/auth.php'; // Check if user is logged in
include 'config/db.php'; // Get session variables

// Get user's info
$user_id = $_SESSION["id"];
$user_name = $_SESSION["full_name"];
$user_type = $_SESSION["user_type"]; // 1 = Donor, 2 = Recipient

// --- NEW: Queries for Dashboard Stats ---
$stat_count_1 = 0;
$stat_label_1 = '';
$stat_count_2 = 0;
$stat_label_2 = '';

if ($user_type == 1) { // Donor Stats
    // Count Active Donations (Status 1)
    $stmt1 = $conn->prepare("SELECT COUNT(id) FROM donations WHERE donor_id = ? AND status = 1");
    $stmt1->bind_param("i", $user_id);
    $stmt1->execute();
    $stmt1->bind_result($stat_count_1);
    $stmt1->fetch();
    $stmt1->close();
    $stat_label_1 = "Available Donations";

    // Count Completed Donations (Status 3)
    $stmt2 = $conn->prepare("SELECT COUNT(id) FROM donations WHERE donor_id = ? AND status = 3");
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
    $stmt2->bind_result($stat_count_2);
    $stmt2->fetch();
    $stmt2->close();
    $stat_label_2 = "Completed Donations";

} else { // Recipient Stats
    // Count Claimed Donations (Status 2)
    $stmt1 = $conn->prepare("SELECT COUNT(id) FROM donations WHERE claimed_by_id = ? AND status = 2");
    $stmt1->bind_param("i", $user_id);
    $stmt1->execute();
    $stmt1->bind_result($stat_count_1);
    $stmt1->fetch();
    $stmt1->close();
    $stat_label_1 = "Claimed Donations";

    // Count Total Completed by you (Status 3)
    $stmt2 = $conn->prepare("SELECT COUNT(id) FROM donations WHERE claimed_by_id = ? AND status = 3");
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
    $stmt2->bind_result($stat_count_2);
    $stmt2->fetch();
    $stmt2->close();
    $stat_label_2 = "Received Donations";
}
// --- End of new stats query ---

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Food Donation</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- *** THIS IS THE IMPORTANT ICON LINK *** -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Your custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<!-- *** THIS CLASS ADDS THE GRAY BACKGROUND *** -->
<body class="bg-light"> 

    <!-- *** THIS NAVBAR CONTAINS ALL THE LINKS *** -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-box-seam-fill me-2"></i> FoodDonation
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php"><i class="bi bi-grid-fill me-1"></i> Dashboard</a>
                    </li>
                    
                    <!-- *** THESE ARE THE LINKS THAT WERE MISSING *** -->
                    <?php if ($user_type == 1): // Donor Links ?>
                        <li class="nav-item">
                            <a class="nav-link" href="post_donation.php"><i class="bi bi-plus-square-dotted me-1"></i> Post a Donation</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="my_donations.php"><i class="bi bi-list-task me-1"></i> My Donations</a>
                        </li>
                    <?php else: // Recipient Links ?>
                        <li class="nav-item">
                            <a class="nav-link" href="view_donations.php"><i class="bi bi-eye-fill me-1"></i> View Available</a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a href="logout.php" class="btn btn-danger ms-lg-2 mt-2 mt-lg-0"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- UPDATED: Main content card is now much more dynamic -->
    <div class="container mt-5 mb-5">
        
        <!-- NEW: Welcome message is now inside the card -->
        <h2 class="mb-4">Welcome, <?php echo htmlspecialchars($user_name); ?>!</h2>

        <!-- NEW: Stat boxes -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center p-4">
                        <h1 class="display-4 fw-bold text-success"><?php echo $stat_count_1; ?></h1>
                        <p class="fs-5 text-muted"><?php echo $stat_label_1; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center p-4">
                        <h1 class="display-4 fw-bold text-primary"><?php echo $stat_count_2; ?></h1>
                        <p class="fs-5 text-muted"><?php echo $stat_label_2; ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main action card -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-md-5">
                <h3 class="card-title text-center mb-3">What's next?</h3>
                
                <?php if ($user_type == 1): ?>
                    <p class="text-center text-muted">You can post a new food donation or view the status of your existing donations.</p>
                    <div class="text-center">
                        <!-- NEW: Button now has an icon -->
                        <a href="post_donation.php" class="btn btn-primary btn-lg">
                            <i class="bi bi-plus-circle-fill me-2"></i> Post New Donation
                        </a>
                    </div>
                <?php else: ?>
                    <p class="text-center text-muted">You can browse all available food donations and claim them for your organization or community.</p>
                    <div class="text-center">
                        <!-- NEW: Button now has an icon -->
                        <a href="view_donations.php" class="btn btn-primary btn-lg">
                            <i class="bi bi-eye-fill me-2"></i> View Available Donations
                        </a>
                    </div>
                <?php endif; ?>
                
            </div>
        </div>
        
    </div>

    <!-- Bootstrap JS for the navbar toggle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

