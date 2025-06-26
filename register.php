<?php
// register.php

require_once 'config.php';
require_once 'auth.php';

// Redirect to dashboard if already logged in
if (Auth::isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';
$firstName = '';
$lastName = '';
$email = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = 'All fields are required';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } else {
        $result = Auth::register($firstName, $lastName, $email, $password);
        
        if ($result['success']) {
            $success = 'Registration successful! Redirecting to dashboard...';
            header("Refresh: 2; url=index.php");
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - NSIA Insurance DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
        }
        .register-card {
            max-width: 500px;
            margin: 0 auto;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border: none;
        }
        .register-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .register-logo img {
            height: 60px;
        }
        .password-strength {
            height: 5px;
            margin-top: 5px;
            background-color: #e9ecef;
        }
        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card register-card">
            <div class="card-body p-4">
                <div class="register-logo">
                    <img src="logo.png" alt="NSIA Insurance">
                    <h4 class="mt-3">Create Your Account</h4>
                </div>
                
                <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="register.php">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="firstName" 
                                   value="<?php echo htmlspecialchars($firstName); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" 
                                   value="<?php echo htmlspecialchars($lastName); ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength">
                            <div class="password-strength-bar" id="passwordStrengthBar"></div>
                        </div>
                        <div class="form-text">Minimum 8 characters with numbers and symbols</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="agreeTerms" name="agreeTerms" required>
                        <label class="form-check-label" for="agreeTerms">
                            I agree to the <a href="terms.php" class="text-decoration-none">terms and conditions</a>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="fas fa-user-plus me-2"></i> Register
                    </button>
                </form>
            </div>
            <div class="card-footer text-center bg-white">
                Already have an account? <a href="login.php" class="text-decoration-none">Login</a>
            </div>
        </div>
    </div>

    <script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('confirmPassword');
        const icon = this.querySelector('i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            confirmInput.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            confirmInput.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
    
    // Password strength indicator
    document.getElementById('password').addEventListener('input', function() {
        const strengthBar = document.getElementById('passwordStrengthBar');
        const password = this.value;
        let strength = 0;
        
        // Length check
        if (password.length >= 8) strength += 25;
        if (password.length >= 12) strength += 25;
        
        // Character type checks
        if (/[A-Z]/.test(password)) strength += 15;
        if (/[0-9]/.test(password)) strength += 15;
        if (/[^A-Za-z0-9]/.test(password)) strength += 20;
        
        // Update strength bar
        strength = Math.min(strength, 100);
        strengthBar.style.width = strength + '%';
        
        // Update color
        if (strength < 40) {
            strengthBar.style.backgroundColor = '#dc3545'; // Red
        } else if (strength < 70) {
            strengthBar.style.backgroundColor = '#fd7e14'; // Orange
        } else if (strength < 90) {
            strengthBar.style.backgroundColor = '#ffc107'; // Yellow
        } else {
            strengthBar.style.backgroundColor = '#28a745'; // Green
        }
    });
    
    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!document.getElementById('agreeTerms').checked) {
            e.preventDefault();
            alert('You must agree to the terms and conditions');
        }
    });
    </script>
</body>
</html>