<?php
// terms.php

require_once 'config.php';
require_once 'auth.php';

$user = Auth::getUser(); // Get user if logged in
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions - NSIA Insurance DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .terms-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .terms-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .terms-logo img {
            height: 60px;
        }
        .terms-content {
            max-height: 60vh;
            overflow-y: auto;
            padding-right: 1rem;
        }
        .terms-content h4 {
            margin-top: 1.5rem;
            margin-bottom: 1rem;
        }
        .terms-content p {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <div class="terms-container">
            <div class="terms-logo">
                <img src="logo.png" alt="NSIA Insurance">
                <h3 class="mt-3">Terms and Conditions</h3>
                <p class="text-muted">Last updated: <?php echo date('F j, Y'); ?></p>
            </div>
            
            <div class="terms-content">
                <h4>1. Acceptance of Terms</h4>
                <p>By accessing and using the NSIA Insurance Document Management System (DMS), you accept and agree to be bound by the terms and provisions of this agreement. In addition, when using this system's particular services, you shall be subject to any posted guidelines or rules applicable to such services.</p>
                
                <h4>2. Description of Service</h4>
                <p>The NSIA Insurance DMS provides users with access to a rich collection of resources, including document storage, retrieval, and management tools. Unless explicitly stated otherwise, any new features that augment or enhance the current Service shall be subject to the Terms of Service.</p>
                
                <h4>3. User Responsibilities</h4>
                <p>You are responsible for maintaining the confidentiality of your account and password and for restricting access to your computer. You agree to accept responsibility for all activities that occur under your account or password.</p>
                
                <h4>4. Privacy Policy</h4>
                <p>Your use of the NSIA Insurance DMS is subject to our Privacy Policy. Please review our Privacy Policy, which also governs the system and informs users of our data collection practices.</p>
                
                <h4>5. Electronic Communications</h4>
                <p>Visiting the NSIA Insurance DMS or sending emails to NSIA Insurance constitutes electronic communications. You consent to receive electronic communications and you agree that all agreements, notices, disclosures and other communications that we provide to you electronically, via email and on the system, satisfy any legal requirement that such communications be in writing.</p>
                
                <h4>6. User Conduct</h4>
                <p>You agree not to use the system to upload, post, email, or otherwise transmit any content that is unlawful, harmful, threatening, abusive, harassing, defamatory, vulgar, obscene, libelous, invasive of another's privacy, hateful, or racially, ethnically or otherwise objectionable.</p>
                
                <h4>7. Intellectual Property</h4>
                <p>You acknowledge that all content included on this system, such as text, graphics, logos, button icons, images, and software, is the property of NSIA Insurance or its content suppliers and protected by international copyright laws.</p>
                
                <h4>8. Disclaimer of Warranties</h4>
                <p>The NSIA Insurance DMS is provided "as is" and on an "as available" basis. NSIA Insurance makes no representations or warranties of any kind, express or implied, as to the operation of this system or the information, content, materials, or products included on this system.</p>
                
                <h4>9. Limitation of Liability</h4>
                <p>NSIA Insurance will not be liable for any damages of any kind arising from the use of this system, including, but not limited to direct, indirect, incidental, punitive, and consequential damages.</p>
                
                <h4>10. Changes to Terms</h4>
                <p>NSIA Insurance reserves the right, at its sole discretion, to change, modify, add or remove portions of these Terms of Service at any time. It is your responsibility to check these Terms of Service periodically for changes.</p>
            </div>
            
            <div class="text-center mt-4">
                <?php if ($user): ?>
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                    </a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Registration
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>