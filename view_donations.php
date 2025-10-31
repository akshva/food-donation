<?php
// view_donations.php
include 'config/auth.php'; // Must be logged in
include 'config/db.php';

// Only recipients can access this page
if ($_SESSION["user_type"] != 2) {
    header("location: dashboard.php");
    exit;
}

$recipient_id = $_SESSION["id"];
$user_name = $_SESSION["full_name"]; // Get user's name for navbar
$message = '';
$message_type = 'danger'; // Default to error

// Handle the claim action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['claim_donation_id'])) {
    $donation_id = $_POST['claim_donation_id'];
    
    // Update the donation record to "Claimed" (status 2)
    // We add "AND status = 1" to prevent claiming an already-claimed item
    $sql_claim = "UPDATE donations SET status = 2, claimed_by_id = ? WHERE id = ? AND status = 1";
    
    if ($stmt = $conn->prepare($sql_claim)) {
        $stmt->bind_param("ii", $recipient_id, $donation_id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $message = "Donation claimed successfully!";
                $message_type = "success";
            } else {
                $message = "This item may have already been claimed by someone else.";
                $message_type = "warning";
            }
        } else {
            $message = "Error: " . $stmt->error;
            $message_type = "danger";
        }
        $stmt->close();
    }
}

// Fetch all available donations (status 1)
$sql_fetch = "SELECT d.id, d.food_item, d.quantity, d.pickup_address, d.expiry_date, u.full_name AS donor_name, u.phone AS donor_phone
              FROM donations d
              JOIN users u ON d.donor_id = u.id
              WHERE d.status = 1
              ORDER BY d.created_at DESC";
              
$result = $conn->query($sql_fetch);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- ADDED for mobile responsiveness -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Donations</title>
    
    <!-- ADDED Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Your custom CSS (must be AFTER Bootstrap) -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Ensures all cards in a row are the same height */
        .card {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .card-body .btn {
            margin-top: auto; /* Pushes button to the bottom */
        }
    </style>
</head>
<body class="bg-light">

    <!-- UPDATED: Replaced header with a Bootstrap Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                Welcome, <?php echo htmlspecialchars($user_name); ?>!
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <!-- UPDATED: Set this as the active link -->
                        <a class="nav-link active" href="view_donations.php">View Available Donations</a>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="btn btn-danger ms-lg-2 mt-2 mt-lg-0">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 mb-5">
        <h2 class="text-center mb-4">Available Donations</h2>

        <!-- ADDED: Message for claim success/failure -->
        <?php if(!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- UPDATED: Re-styled list as a responsive Card grid -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="col">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h5 class="card-title text-success"><?php echo htmlspecialchars($row['food_item']); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted">
                                    <strong>Quantity:</strong> <?php echo htmlspecialchars($row['quantity']); ?>
                                </h6>
                                
                                <ul class="list-group list-group-flush my-3">
                                    <li class="list-group-item px-0">
                                        <strong>Expires:</strong> <?php echo htmlspecialchars($row['expiry_date']); ?>
                                    </li>
                                    <li class="list-group-item px-0">
                                        <strong>Address:</strong> <?php echo htmlspecialchars($row['pickup_address']); ?>
                                    </li>
                                    <li class="list-group-item px-0">
                                        <strong>Donor:</strong> <?php echo htmlspecialchars($row['donor_name']); ?>
                                    </li>
                                    <li class="list-group-item px-0">
                                        <strong>Donor Phone:</strong> <?php echo htmlspecialchars($row['donor_phone']); ?>
                                    </li>
                                </ul>
                                
                                <form action="view_donations.php" method="post" class="d-grid" onsubmit="return confirm('Are you sure you want to claim this item?');">
                                    <input type="hidden" name="claim_donation_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn btn-success">Claim This Donation</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- UPDATED: Styled "no donations" message -->
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        No available donations at this time. Please check back later.
                    </div>
                </div>
            <?php endif; ?>
            <?php $conn->close(); ?>
        </div>
    </div>

    <!-- Optional: Bootstrap JS for the navbar toggle and alerts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
