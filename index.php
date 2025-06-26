<?php
// index.php

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/documents.php'; // This contains the DocumentManager class

// Redirect to login if not authenticated
if (!Auth::isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Now you can safely use DocumentManager
$user = Auth::getUser();
$departments = DocumentManager::getAllDepartments();
$recentDocuments = DocumentManager::getRecentDocuments(5);

// Get current department if specified
$currentDepartment = null;
if (isset($_GET['department'])) {
    $currentDepartment = DocumentManager::getDepartmentById($_GET['department']);
    $departmentDocuments = DocumentManager::getDocumentsByDepartment($_GET['department'], 1, 10);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Your HTML head content from earlier -->
</head>
<body>
    <!-- Your HTML body content from earlier -->
    
    <script>
    // Example AJAX calls to interact with the API
        
    // Load documents for a department
    function loadDepartmentDocuments(departmentId, page = 1, search = '') {
        fetch(`api.php/documents/${departmentId}?page=${page}&search=${search}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the UI with the documents
                    console.log('Documents loaded:', data.data);
                    // Update pagination info
                    console.log('Pagination:', data.pagination);
                } else {
                    alert('Error loading documents: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    }
    
    // Upload document
    function uploadDocument(formData) {
        fetch('api.php/upload', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Document uploaded successfully!');
                // Refresh the document list
                loadDepartmentDocuments(formData.get('departmentId'));
            } else {
                alert('Error uploading document: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
    // Merge documents
    function mergeDocuments(sourceDocId, targetDocId, departmentId, mergeName) {
        fetch('api.php/merge', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                sourceDocId,
                targetDocId,
                departmentId,
                mergeName,
                keepOriginals: true,
                notifyTeam: false
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Documents merged successfully!');
                // Refresh the document list
                loadDepartmentDocuments(departmentId);
            } else {
                alert('Error merging documents: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
    // Download document
    function downloadDocument(docId) {
        window.location.href = `api.php/download/${docId}`;
    }
    </script>
</body>
</html>