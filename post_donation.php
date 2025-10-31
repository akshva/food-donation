<?php
// post_donation.php
include 'config/auth.php'; // Must be logged in
include 'config/db.php';

// Only donors can access this page
if ($_SESSION["user_type"] != 1) {
    header("location: dashboard.php");
    exit;
}

$message = '';
$message_type = 'danger'; // Default to error
$donor_id = $_SESSION["id"];
$user_name = $_SESSION["full_name"]; // Get user's name for navbar

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $food_item = $_POST['food_item'];
    $quantity = $_POST['quantity'];
    $pickup_address = $_POST['pickup_address'];
    $expiry_date = $_POST['expiry_date'];
    $description = $_POST['description'];

    $sql = "INSERT INTO donations (donor_id, food_item, quantity, pickup_address, expiry_date, description, status) 
            VALUES (?, ?, ?, ?, ?, ?, 1)"; // Status 1 = Available

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("isssss", $donor_id, $food_item, $quantity, $pickup_address, $expiry_date, $description);
        
        if ($stmt->execute()) {
            $message = "Donation posted successfully!";
            $message_type = 'success'; // Set to success
        } else {
            $message = "Error: " . $stmt->error;
            $message_type = 'danger'; // Keep as error
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- ADDED for mobile responsiveness -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Donation</title>
    
    <!-- ADDED Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Your custom CSS (must be AFTER Bootstrap) -->
    <link rel="stylesheet" href="css/style.css">
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
                        <a class="nav-link active" href="post_donation.php">Post a Donation</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_donations.php">My Donations</a>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="btn btn-danger ms-lg-2 mt-2 mt-lg-0">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- UPDATED: Re-styled main content as a Card Form -->
    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="card-title text-center mb-4">Post a New Donation</h2>
                        
                        <!-- UPDATED: Bootstrap alert for dynamic messages -->
                        <?php if(!empty($message)): ?>
                            <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
                        <?php endif; ?>

                        <form action="post_donation.php" method="post">
                            <div class="mb-3">
                                <label for="food_item" class="form-label">Food Item(s)</label>
                                <input type="text" name="food_item" id="food_item" class="form-control" placeholder="e.g., Bread, Canned Goods, Fruits" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="text" name="quantity" id="quantity" class="form-control" placeholder="e.g., 5 loaves, 20 cans, 3 boxes" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="pickup_address" class="form-label">Pickup Address</label>
                                <textarea name="pickup_address" id="pickup_address" class="form-control" rows="3" required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="expiry_date" class="form-label">Expiry Date</label>
                                <input type="date" name="expiry_date" id="expiry_date" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description (Optional)</label>
                                <textarea name="description" id="description" class="form-control" rows="2" placeholder="Any special notes or details..."></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Post Donation</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional: Bootstrap JS for the navbar toggle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
