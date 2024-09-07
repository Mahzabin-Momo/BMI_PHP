<?php
session_start(); // Start session to manage user login

include 'config.php';

// Login check
if (!isset($_SESSION['username'])) {
    die("You must be logged in to access this page. <a href='login.php'>Login</a>");
}

// Check if user is submitting BMI data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize user inputs
    $height = (float)$_POST['height'];
    $weight = (float)$_POST['weight'];

    // Validate input
    if ($height <= 0 || $weight <= 0) {
        die("Height and weight must be positive numbers.");
    }

    // Calculate BMI
    $bmi = $weight / ($height * $height);

    // Get the user ID
    $username = $_SESSION['username'];
    $result = $conn->query("SELECT BMIUserID FROM BMIUsers WHERE Name='$username'");
    $row = $result->fetch_assoc();
    $bmiUserID = $row['BMIUserID'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO BMIRecords (BMIUserID, Height, Weight, BMI) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iddd", $bmiUserID, $height, $weight, $bmi);

    // Execute the statement
    if ($stmt->execute()) {
        echo "BMI recorded successfully. Your BMI is " . number_format($bmi, 2);
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BMI Calculator</title>
</head>
<body>
    <h1>BMI Calculator</h1>
    <form method="post" action="">
        <label for="height">Height (m):</label>
        <input type="number" step="0.01" id="height" name="height" required><br><br>

        <label for="weight">Weight (kg):</label>
        <input type="number" step="0.1" id="weight" name="weight" required><br><br>

        <input type="submit" value="Calculate BMI">
    </form>
</body>
</html>
