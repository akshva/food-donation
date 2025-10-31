<?php
// index.php (Login Page)
include 'config/db.php';

$message = '';
$message_type = 'danger'; // Default to error

// Check if user is already logged in, if yes then redirect to dashboard
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare a select statement
    $sql = "SELECT id, full_name, email, password, user_type FROM users WHERE email = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        
        if ($stmt->execute()) {
            $stmt->store_result();
            
            // Check if email exists
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id, $full_name, $email, $hashed_password, $user_type);
                if ($stmt->fetch()) {
                    // Verify password
                    if (password_verify($password, $hashed_password)) {
                        // Password is correct, start a new session
                        // Session is already started in db.php
                        
                        // Store data in session variables
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["full_name"] = $full_name;
                        $_SESSION["user_type"] = $user_type; // 1=Donor, 2=Recipient
                        
                        // Redirect user to dashboard
                        header("location: dashboard.php");
                    } else {
                        $message = "The password you entered was not valid.";
                    }
                }
            } else {
                $message = "No account found with that email.";
            }
        } else {
            $message = "Oops! Something went wrong. Please try again later.";
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
    <title>Login - Food Donation</title>
    
    <!-- ADDED Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Your custom CSS (must be AFTER Bootstrap) -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light"> <!-- ADDED a light background color -->

    <!-- UPDATED: Using Bootstrap classes for a centered form -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 col-lg-5 mx-auto"> <!-- Centers the form -->
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="card-title text-center mb-3">Login</h2>
                        <p class="card-text text-center text-muted mb-4">Welcome back. Please login to your account.</p>

                        <!-- UPDATED: Bootstrap alert for error messages -->
                        <?php if(!empty($message)): ?>
                            <!-- Using $message_type to make alert dynamic (e.g., alert-danger) -->
                            <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
                        <?php endif; ?>

                        <form action="index.php" method="post">
                            
                            <!-- UPDATED: Bootstrap form group -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <!-- ADDED autocomplete="off" -->
                                <input type="email" name="email" id="email" class="form-control" required autocomplete="off">
                            </div>
                            
                            <!-- UPDATED: Bootstrap form group -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <!-- ADDED autocomplete="off" -->
                                <input type="password" name="password" id="password" class="form-control" required autocomplete="off">
                            </div>
                            
                            <!-- UPDATED: Bootstrap button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Login</button>
                            </div>
                            
                            <p class="text-center mt-4">
                                Don't have an account? <a href="register.php">Register here</a>.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional: Bootstrap JS (if you need it later for dropdowns, etc.) -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
</body>
</html>

