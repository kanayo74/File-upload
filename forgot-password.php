<?php
// forgot-password.php

require_once 'config.php';
require_once 'auth.php';

// Redirect to dashboard if already logged in
if (Auth::isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';
$email = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Email address is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } else {
        // In a real application, you would:
        // 1. Check if email exists in database
        // 2. Generate a password reset token
        // 3. Store token in database with expiration
        // 4. Send email with reset link
        
        // For this example, we'll just show a success message
        $success = 'If an account with that email exists, we have sent a password reset link.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - NSIA Insurance DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
        }
        .forgot-card {
            max-width: 450px;
            margin: 0 auto;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border: none;
        }
        .forgot-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .forgot-logo img {
            height: 60px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card forgot-card">
            <div class="card-body p-4">
                <div class="forgot-logo">
                    <img src="logo.png" alt="NSIA Insurance">
                    <h4 class="mt-3">Reset Your Password</h4>
                    <p class="text-muted">Enter your email to receive a reset link</p>
                </div>
                
                <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="forgot-password.php">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                        <i class="fas fa-paper-plane me-2"></i> Send Reset Link
                    </button>
                    
                    <div class="text-center">
                        <a href="login.php" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i> Back to Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>