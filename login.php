<?php
session_start();
include 'connection.php';

if (isset($_POST['signIn'])) {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);
    
    // Check user credentials
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify password using password_verify()
        if (password_verify($password, $row['password'])) {
            $_SESSION['email'] = $row['email'];
            $_SESSION['user_id'] = $row['id'];
            
            // Prevent session fixation
            session_regenerate_id(true);
            
            header("Location: homepage.php");
            exit();
        } else {
            $error = "Incorrect email or password";
        }
    } else {
        $error = "Incorrect email or password";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="form-box" id="signin">
        <h1 class="form-title">Sign In</h1>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="input-group">
                <i class="fa fa-envelope"></i>
                <input type="email" 
                       name="email" 
                       id="email-signin" 
                       placeholder="Email" 
                       required
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="input-group">
                <i class="fa fa-lock"></i>
                <input type="password" 
                       name="password" 
                       id="password-signin" 
                       placeholder="Password" 
                       required>
            </div>
            <p class="recover">
                <a href="recover-password.php">Recover Password</a>
            </p>
            <button type="submit" name="signIn" class="btn">Sign In</button>
        </form>
        
        <div class="divider"><span>or</span></div>
        <div class="social-icons">
            <i class="fa fa-google"></i>
            <i class="fa fa-facebook"></i>
            <i class="fa fa-github"></i>
        </div>
        <p class="account-switch">
            Don't have an account yet? 
            <button id="signUpButton" onclick="register1.php">Sign Up</button>
        </p>
    </div>

    <style>
    .error-message {
        color: #721c24;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        border-radius: 4px;
        padding: 10px;
        margin-bottom: 15px;
        text-align: center;
    }
    </style>
</body>
</html>