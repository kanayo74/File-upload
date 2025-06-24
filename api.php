<?php
header('Content-Type: application/json');
require_once 'config.php';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $uploadDir = 'uploads/';
    $fileName = basename($_FILES['file']['name']);
    $filePath = $uploadDir . $fileName;
    $fileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    
    // Check if file is a PDF
    if ($fileType != "pdf") {
        echo json_encode(['success' => false, 'message' => 'Only PDF files are allowed']);
        exit;
    }
    
    // Check file size (10MB max)
    if ($_FILES['file']['size'] > 10000000) {
        echo json_encode(['success' => false, 'message' => 'File is too large (max 10MB)']);
        exit;
    }
    
    // Upload file
    if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
        // Save to database
        $stmt = $pdo->prepare("INSERT INTO documents (name, department, file_path, uploaded_by, file_size) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['department'],
            $filePath,
            $_POST['user_id'],
            $_FILES['file']['size']
        ]);
        
        echo json_encode(['success' => true, 'filePath' => $filePath]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error uploading file']);
    }
    exit;
}

// Handle document merge
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['merge'])) {
    $sourcePath = $_POST['source_path'];
    $targetPath = $_POST['target_path'];
    $newName = $_POST['new_name'];
    $department = $_POST['department'];
    $userId = $_POST['user_id'];
    
    // In a real application, you would use a PDF library to merge the files
    // This is just a simulation
    
    $mergedPath = 'uploads/' . uniqid() . '_merged.pdf';
    
    // Simulate merge by copying one of the files
    if (!copy($sourcePath, $mergedPath)) {
        echo json_encode(['success' => false, 'message' => 'Error merging files']);
        exit;
    }
    
    // Save to database
    $stmt = $pdo->prepare("INSERT INTO documents (name, department, file_path, uploaded_by, is_merged) 
                          VALUES (?, ?, ?, ?, 1)");
    $stmt->execute([
        $newName,
        $department,
        $mergedPath,
        $userId
    ]);
    
    echo json_encode(['success' => true, 'filePath' => $mergedPath]);
    exit;
}

// Get documents by department
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['department'])) {
    $stmt = $pdo->prepare("SELECT * FROM documents WHERE department = ? ORDER BY uploaded_at DESC");
    $stmt->execute([$_GET['department']]);
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($documents);
    exit;
}

// Get recent documents
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['recent'])) {
    $stmt = $pdo->prepare("SELECT d.*, u.name as uploaded_by_name 
                          FROM documents d
                          JOIN users u ON d.uploaded_by = u.id
                          ORDER BY d.uploaded_at DESC 
                          LIMIT 10");
    $stmt->execute();
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($documents);
    exit;
}
?>