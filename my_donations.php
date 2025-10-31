<?php
// my_donations.php
include 'config/auth.php'; // Must be logged in
include 'config/db.php';

// Only donors can access this page
if ($_SESSION["user_type"] != 1) {
    header("location: dashboard.php");
    exit;
}

$donor_id = $_SESSION["id"];
$user_name = $_SESSION["full_name"]; // **NEW:** Get user's name for navbar

// Fetch this donor's donations and the recipient's info if claimed
// **MODIFIED:** Added d.id to the SELECT statement
$sql = "SELECT 
            d.id, d.food_item, d.quantity, d.status, d.created_at,
            u.full_name AS recipient_name, u.phone AS recipient_phone
        FROM donations d
        LEFT JOIN users u ON d.claimed_by_id = u.id
        WHERE d.donor_id = ?
        ORDER BY d.created_at DESC";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $donor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- ADDED for mobile responsiveness -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Donations</title>
    
    <!-- ADDED Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Your custom CSS (must be AFTER Bootstrap) -->
    <link rel="stylesheet" href="css/style.css">
    
    <!-- You can remove the old .status- classes from style.css if you want -->
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
                        <a class="nav-link" href="post_donation.php">Post a Donation</a>
                    </li>
                    <li class="nav-item">
                        <!-- UPDATED: Set this as the active link -->
                        <a class="nav-link active" href="my_donations.php">My Donations</a>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="btn btn-danger ms-lg-2 mt-2 mt-lg-0">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 mb-5">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">My Donations Status</h2>

                <!-- UPDATED: Added classes for responsive table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <!-- **FIXED:** Removed extra </tr> -->
                            <tr>
                                <th>Food Item</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th>Claimed By (Recipient)</th>
                                <th>Recipient Phone</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['food_item']); ?></td>
                                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                        <td>
                                            <!-- UPDATED: Styled statuses as Bootstrap Badges -->
                                            <?php 
                                            if ($row['status'] == 1) echo '<span class="badge bg-success">Available</span>';
                                            if ($row['status'] == 2) echo '<span class="badge bg-primary">Claimed</span>';
                                            if ($row['status'] == 3) echo '<span class="badge bg-secondary">Completed</span>';
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['recipient_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($row['recipient_phone'] ?? 'N/A'); ?></td>
                                        
                                        <!-- **ADDED:** New <td> for the button -->
                                        <td class="text-center">
                                            <?php if ($row['status'] == 2): // If it's Claimed ?>
                                                <form action="complete_donation.php" method="post" style="display:inline;">
                                                    <input type="hidden" name="donation_id" value="<?php echo $row['id']; ?>">
                                                    <!-- UPDATED: Styled the button -->
                                                    <button typeB="submit" class="btn btn-primary btn-sm">Mark as Completed</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <!-- **MODIFIED:** Updated colspan from 5 to 6 -->
                                    <td colspan="6" class="text-center text-muted p-4">You have not posted any donations yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional: Bootstrap JS for the navbar toggle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

