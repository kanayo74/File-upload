<?php
// api.php

require_once 'config.php';
require_once 'auth.php';
require_once 'documents.php';

header('Content-Type: application/json');

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path = str_replace('/nsia-dms/api.php', '', $path);
    $path = trim($path, '/');
    $segments = explode('/', $path);
    
    $response = ['success' => false, 'message' => 'Invalid request'];
    
    // Public endpoints
    if ($method === 'POST' && $segments[0] === 'login') {
        $data = json_decode(file_get_contents('php://input'), true);
        $response = Auth::login($data['email'] ?? '', $data['password'] ?? '');
    }
    elseif ($method === 'POST' && $segments[0] === 'register') {
        $data = json_decode(file_get_contents('php://input'), true);
        $response = Auth::register(
            $data['firstName'] ?? '',
            $data['lastName'] ?? '',
            $data['email'] ?? '',
            $data['password'] ?? ''
        );
    }
    
    // Protected endpoints
    elseif (Auth::isLoggedIn()) {
        $user = Auth::getUser();
        
        // Departments
        if ($method === 'GET' && $segments[0] === 'departments') {
            $response = [
                'success' => true,
                'data' => DocumentManager::getAllDepartments()
            ];
        }
        
        // Documents by department
        elseif ($method === 'GET' && $segments[0] === 'documents' && isset($segments[1])) {
            $page = $_GET['page'] ?? 1;
            $search = $_GET['search'] ?? '';
            $result = DocumentManager::getDocumentsByDepartment($segments[1], $page, 10, $search);
            $response = [
                'success' => true,
                'data' => $result['documents'],
                'pagination' => [
                    'page' => $page,
                    'total' => $result['total'],
                    'pages' => $result['pages']
                ]
            ];
        }
        
        // Recent documents
        elseif ($method === 'GET' && $segments[0] === 'recent-documents') {
            $response = [
                'success' => true,
                'data' => DocumentManager::getRecentDocuments()
            ];
        }
        
        // Upload document
        elseif ($method === 'POST' && $segments[0] === 'upload') {
            if (!isset($_FILES['file'])) {
                throw new Exception('No file uploaded');
            }
            
            $documentId = DocumentManager::uploadDocument(
                $_FILES['file'],
                $_POST['departmentId'] ?? 0,
                $user['id'],
                $_POST['name'] ?? null,
                $_POST['description'] ?? '',
                isset($_POST['isConfidential']),
                explode(',', $_POST['tags'] ?? '')
            );
            
            $response = [
                'success' => true,
                'message' => 'Document uploaded successfully',
                'documentId' => $documentId
            ];
        }
        
        // Merge documents
        elseif ($method === 'POST' && $segments[0] === 'merge') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $mergedId = DocumentManager::mergeDocuments(
                $data['sourceDocId'] ?? 0,
                $data['targetDocId'] ?? 0,
                $data['departmentId'] ?? 0,
                $user['id'],
                $data['mergeName'] ?? 'Merged_Document.pdf',
                $data['keepOriginals'] ?? true,
                $data['notifyTeam'] ?? false
            );
            
            $response = [
                'success' => true,
                'message' => 'Documents merged successfully',
                'mergedId' => $mergedId
            ];
        }
        
        // Download document
        elseif ($method === 'GET' && $segments[0] === 'download' && isset($segments[1])) {
            DocumentManager::downloadDocument($segments[1]);
            exit;
        }
    }
    
    // Logout
    elseif ($method === 'POST' && $segments[0] === 'logout') {
        Auth::logout();
        $response = ['success' => true, 'message' => 'Logged out successfully'];
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}