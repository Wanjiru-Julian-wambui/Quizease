<?php 
session_start();
include("connection.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomePage</title>
</head>
<body>
    <div style="text-align: center; padding: 15%;">
        <p style="font-size: 50px; font-weight: bold;">
            Hello 
            <?php 
            if (isset($_SESSION['email'])) {
                $email = $_SESSION['email'];

                // Correct SQL Query
                $query = mysqli_query($conn, "SELECT first_name, last_name FROM users WHERE email = '$email'");

                // Fetch user information
                if ($row = mysqli_fetch_assoc($query)) {
                    echo $row['first_name'] . ' ' . $row['last_name'];
                } else {
                    echo "User not found!";
                }
            } else {
                echo "Guest";
            }
            ?>
        </p>
        <a href="logout.php">LogOut</a>
    </div>
</body>
</html>
