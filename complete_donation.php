<?php
include 'config/auth.php';
include 'config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['donation_id'])) {
    $donation_id = $_POST['donation_id'];
    $donor_id = $_SESSION["id"];

    // Update status to 3 (Completed)
    // We check donor_id to make sure only the owner can complete it
    $sql = "UPDATE donations SET status = 3 WHERE id = ? AND donor_id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $donation_id, $donor_id);
        $stmt->execute();
        $stmt->close();
    }
}
header("location: my_donations.php");
exit;
?>