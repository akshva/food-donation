<?php
// register.php
include 'config/db.php';

$message = '';
$message_type = 'danger'; // Default to error style

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $user_type = $_POST['user_type'];
    
    // Hash the password for security
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Prepare an insert statement
    $sql = "INSERT INTO users (full_name, email, password, phone, address, user_type) VALUES (?, ?, ?, ?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("sssssi", $full_name, $email, $password, $phone, $address, $user_type);
        
        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            $message = "Registration successful! You can now <a href='index.php' class='alert-link'>login</a>.";
            $message_type = 'success'; // Set to success style
        } else {
            // Check for duplicate email error
            if ($conn->errno == 1062) {
                $message = "Error: An account with this email already exists.";
            } else {
                $message = "Error: " . $stmt->error;
            }
            $message_type = 'danger'; // Keep as error style
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
    <title>Register - Food Donation</title>
    
    <!-- ADDED Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Your custom CSS (must be AFTER Bootstrap) -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light">

    <!-- UPDATED: Using Bootstrap classes for a centered form -->
    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-md-8 col-lg-6 mx-auto"> <!-- Made card slightly wider for the form -->
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="card-title text-center mb-3">Register</h2>
                        <p class="card-text text-center text-muted mb-4">Create an account to donate or receive food.</p>
                        
                        <!-- UPDATED: Bootstrap alert for dynamic messages -->
                        <?php if(!empty($message)): ?>
                            <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
                        <?php endif; ?>

                        <form action="register.php" method="post">
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name</label>
                                <!-- ADDED autocomplete="off" -->
                                <input type="text" name="full_name" id="full_name" class="form-control" required autocomplete="off">
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <!-- ADDED autocomplete="off" -->
                                <input type="email" name="email" id="email" class="form-control" required autocomplete="off">
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <!-- CHANGED autocomplete to "new-password" -->
                                <input type="password" name="password" id="password" class="form-control" required autocomplete="new-password">
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <!-- ADDED autocomplete="off" -->
                                <input type="text" name="phone" id="phone" class="form-control" required autocomplete="off">
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <!-- ADDED autocomplete="off" -->
                                <textarea name="address" id="address" class="form-control" rows="3" required autocomplete="off"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="user_type" class="form-label">I am a...</label>
                                <!-- UPDATED: Use .form-select for dropdowns -->
                                <select name="user_type" id="user_type" class="form-select" required>
                                    <option value="" disabled selected>-- Select your role --</option>
                                    <option value="1">Donor (I want to donate food)</option>
                                    <option value="2">Recipient (I am an NGO/person in need)</option>
                                </select>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Register</button>
                            </div>
                            
                            <p class="text-center mt-4">
                                Already have an account? <a href="index.php">Login here</a>.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

