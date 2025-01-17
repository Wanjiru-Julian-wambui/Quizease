<?php
session_start();
include 'connection.php';

// Initialize error array
$errors = [];

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['SignUp'])) {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = "Invalid form submission.";
    } else {
        // Sanitize and validate inputs
        $firstName = filter_var(trim($_POST['fname']), FILTER_SANITIZE_STRING);
        $lastName = filter_var(trim($_POST['lname']), FILTER_SANITIZE_STRING);
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['password']);

        // Validate first name
        if (empty($firstName) || strlen($firstName) > 50) {
            $errors[] = "First name must be between 1 and 50 characters.";
        }

        // Validate last name
        if (empty($lastName) || strlen($lastName) > 50) {
            $errors[] = "Last name must be between 1 and 50 characters.";
        }

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address.";
        }

        // Validate password strength
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long.";
        }
        if (!preg_match("/[A-Z]/", $password)) {
            $errors[] = "Password must contain at least one uppercase letter.";
        }
        if (!preg_match("/[a-z]/", $password)) {
            $errors[] = "Password must contain at least one lowercase letter.";
        }
        if (!preg_match("/[0-9]/", $password)) {
            $errors[] = "Password must contain at least one number.";
        }

        if (empty($errors)) {
            try {
                // Check if email already exists
                $checkEmail = "SELECT id FROM users WHERE email = ?";
                $stmt = $conn->prepare($checkEmail);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $errors[] = "This email address is already registered.";
                } else {
                    // Hash password securely
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    // Insert new user
                    $insertQuery = "INSERT INTO users (first_name, last_name, email, password) 
                                  VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($insertQuery);
                    $stmt->bind_param("ssss", $firstName, $lastName, $email, $hashedPassword);

                    if ($stmt->execute()) {
                        $_SESSION['registration_success'] = true;
                        header("Location: login.php");
                        exit();
                    } else {
                        $errors[] = "Registration failed. Please try again later.";
                    }
                }
                $stmt->close();
            } catch (Exception $e) {
                $errors[] = "An error occurred. Please try again later.";
                // Log the error securely
                error_log("Registration error: " . $e->getMessage());
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="form-box" id="signup">
        <h1 class="form-title">Register</h1>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="input-group">
                <i class="fa fa-user"></i>
                <input type="text" 
                       name="fname" 
                       id="fname" 
                       placeholder="First Name" 
                       required
                       maxlength="50"
                       value="<?php echo isset($_POST['fname']) ? htmlspecialchars($_POST['fname']) : ''; ?>">
            </div>
            <div class="input-group">
                <i class="fa fa-user"></i>
                <input type="text" 
                       name="lname" 
                       id="lname" 
                       placeholder="Last Name" 
                       required
                       maxlength="50"
                       value="<?php echo isset($_POST['lname']) ? htmlspecialchars($_POST['lname']) : ''; ?>">
            </div>
            <div class="input-group">
                <i class="fa fa-envelope"></i>
                <input type="email" 
                       name="email" 
                       id="email" 
                       placeholder="Email" 
                       required
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="input-group">
                <i class="fa fa-lock"></i>
                <input type="password" 
                       name="password" 
                       id="password" 
                       placeholder="Password" 
                       required
                       pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                       title="Must contain at least one number, one uppercase and lowercase letter, and be at least 8 characters long">
            </div>
            <div class="password-requirements">
                <p>Password must contain:</p>
                <ul>
                    <li>At least 8 characters</li>
                    <li>One uppercase letter</li>
                    <li>One lowercase letter</li>
                    <li>One number</li>
                </ul>
            </div>
            <button type="submit" name="SignUp" class="btn">Sign Up</button>
        </form>

        <div class="divider"><span>or</span></div>
        <div class="social-icons">
            <i class="fa fa-google"></i>
            <i class="fa fa-facebook"></i>
            <i class="fa fa-github"></i>
        </div>
        <p class="account-switch">
            Already have an account? 
            <button id="signInButton">Sign In</button>
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
    }
    .error-message p {
        margin: 5px 0;
    }
    .password-requirements {
        font-size: 0.9em;
        color: #666;
        margin: 10px 0;
    }
    .password-requirements ul {
        margin: 5px 0;
        padding-left: 20px;
    }
    .password-requirements li {
        margin: 2px 0;
    }
    </style>
</body>
</html>