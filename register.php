<?php
include 'connection.php';

if (isset($_POST['SignUp'])) {
    $FirstName = trim($_POST['fName']);
    $LastName = trim($_POST['lName']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $password = md5($password); // Encrypt password with MD5 (consider using bcrypt or password_hash in production)

    // Check if the email already exists
    $checkEmail = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmail);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Email Address Already Exists!";
    } else {
        // Insert the new user into the database
        $insertQuery = "INSERT INTO users (FirstName, LastName, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ssss", $FirstName, $LastName, $email, $password);

        if ($stmt->execute()) {
            header("Location: index.html");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    }
    $stmt->close();
}

if (isset($_POST['signIn'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $password = md5($password); // Encrypt password with MD5 (consider stronger encryption in production)

    // Check user credentials
    $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        session_start();
        $row = $result->fetch_assoc();
        $_SESSION['email'] = $row['email']; // Store email in session
        header("Location: homepage.php");
        exit();
    } else {
        echo "Incorrect Email or Password!";
    }
    $stmt->close();
}

$conn->close();
?>
