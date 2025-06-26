<?php
// reset-password.php

require_once 'config.php';
require_once 'auth.php';

// Redirect to dashboard if already logged in
if (Auth::isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';
$token = $_GET['token'] ?? '';

// Validate token (in a real app, you would check this against the database)
$validToken = !empty($token); // Simplified for example

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    
    if (empty($token)) {
        $error = 'Invalid reset token';
    } elseif (empty($password) || empty($confirmPassword)) {
        $error = 'Both password fields are required';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } else {
        // In a real application, you would:
        // 1. Verify the token is valid and not expired
        // 2. Update the user's password
        // 3. Invalidate the token
        // 4. Log the user in or redirect to login
        
        $success = 'Your password has been reset successfully. Redirecting to login...';
        header("Refresh: 3; url=login.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - NSIA Insurance DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
        }
        .reset-card {
            max-width: 450px;
            margin: 0 auto;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border: none;
        }
        .reset-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .reset-logo img {
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
        <div class="card reset-card">
            <div class="card-body p-4">
                <div class="reset-logo">
                    <img src="logo.png" alt="NSIA Insurance">
                    <h4 class="mt-3">Set New Password</h4>
                </div>
                
                <?php if (!$validToken): ?>
                <div class="alert alert-danger">
                    Invalid or expired password reset link. Please request a new one.
                </div>
                <div class="text-center mt-3">
                    <a href="forgot-password.php" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> Request New Link
                    </a>
                </div>
                <?php else: ?>
                
                    <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="reset-password.php">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
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
                            <label for="confirmPassword" class="form-label">Confirm New Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-save me-2"></i> Reset Password
                        </button>
                    </form>
                    
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($validToken): ?>
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
    </script>
    <?php endif; ?>
</body>
</html>