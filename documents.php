<?php
// documents.php

require_once 'config.php';
require_once 'auth.php';

class DocumentManager {
    public static function getAllDepartments() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM departments ORDER BY name");
        return $stmt->fetchAll();
    }
    
    public static function getDepartmentById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM departments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public static function getDocumentsByDepartment($departmentId, $page = 1, $perPage = 10, $search = '') {
        $db = getDB();
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT d.*, u.first_name, u.last_name 
                FROM documents d
                JOIN users u ON d.uploaded_by = u.id
                WHERE d.department_id = ?";
        
        $params = [$departmentId];
        
        if (!empty($search)) {
            $sql .= " AND (d.name LIKE ? OR d.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $sql .= " ORDER BY d.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $documents = $stmt->fetchAll();
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(*) as total FROM documents WHERE department_id = ?";
        $countParams = [$departmentId];
        
        if (!empty($search)) {
            $countSql .= " AND (name LIKE ? OR description LIKE ?)";
            $countParams[] = "%$search%";
            $countParams[] = "%$search%";
        }
        
        $stmt = $db->prepare($countSql);
        $stmt->execute($countParams);
        $total = $stmt->fetch()['total'];
        
        return [
            'documents' => $documents,
            'total' => $total,
            'pages' => ceil($total / $perPage)
        ];
    }
    
    public static function getRecentDocuments($limit = 10) {
        $db = getDB();
        $stmt = $db->prepare("SELECT d.*, dep.name as department_name, u.first_name, u.last_name 
                             FROM documents d
                             JOIN departments dep ON d.department_id = dep.id
                             JOIN users u ON d.uploaded_by = u.id
                             ORDER BY d.created_at DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public static function uploadDocument($file, $departmentId, $userId, $name = null, $description = '', $isConfidential = false, $tags = []) {
        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('File upload error: ' . $file['error']);
        }
        
        if ($file['size'] > Config::MAX_FILE_SIZE) {
            throw new Exception('File size exceeds maximum limit of 10MB');
        }
        
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExt, Config::ALLOWED_TYPES)) {
            throw new Exception('Invalid file type. Allowed types: ' . implode(', ', Config::ALLOWED_TYPES));
        }
        
        // Create upload directory if it doesn't exist
        if (!file_exists(Config::UPLOAD_DIR)) {
            mkdir(Config::UPLOAD_DIR, 0755, true);
        }
        
        // Generate unique filename
        $filename = uniqid() . '_' . preg_replace('/[^a-z0-9\.]/i', '_', $file['name']);
        $filepath = Config::UPLOAD_DIR . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception('Failed to move uploaded file');
        }
        
        // Use original name if not provided
        $documentName = $name ?: pathinfo($file['name'], PATHINFO_FILENAME);
        
        $db = getDB();
        $db->beginTransaction();
        
        try {
            // Insert document
            $stmt = $db->prepare("INSERT INTO documents 
                                (name, description, file_path, file_type, file_size, department_id, uploaded_by, is_confidential)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $documentName,
                $description,
                $filepath,
                $fileExt,
                $file['size'],
                $departmentId,
                $userId,
                $isConfidential ? 1 : 0
            ]);
            
            $documentId = $db->lastInsertId();
            
            // Add tags if any
            if (!empty($tags)) {
                $tagIds = [];
                foreach ($tags as $tagName) {
                    $tagName = trim($tagName);
                    if (empty($tagName)) continue;
                    
                    // Check if tag exists
                    $stmt = $db->prepare("SELECT id FROM tags WHERE name = ?");
                    $stmt->execute([$tagName]);
                    $tag = $stmt->fetch();
                    
                    if (!$tag) {
                        // Create new tag
                        $stmt = $db->prepare("INSERT INTO tags (name) VALUES (?)");
                        $stmt->execute([$tagName]);
                        $tagId = $db->lastInsertId();
                    } else {
                        $tagId = $tag['id'];
                    }
                    
                    $tagIds[] = $tagId;
                }
                
                // Link tags to document
                if (!empty($tagIds)) {
                    $stmt = $db->prepare("INSERT INTO document_tags (document_id, tag_id) VALUES (?, ?)");
                    foreach ($tagIds as $tagId) {
                        $stmt->execute([$documentId, $tagId]);
                    }
                }
            }
            
            $db->commit();
            
            // Log the action
            Auth::logAction($userId, 'upload', 'documents', $documentId, null, [
                'name' => $documentName,
                'department_id' => $departmentId
            ]);
            
            return $documentId;
        } catch (Exception $e) {
            $db->rollBack();
            // Delete the uploaded file if transaction failed
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            throw $e;
        }
    }
    
    public static function mergeDocuments($sourceDocId, $targetDocId, $departmentId, $userId, $mergeName, $keepOriginals = true, $notifyTeam = false, $additionalFile = null) {
        $db = getDB();
        
        // Get source documents
        $sourceDoc = self::getDocumentById($sourceDocId);
        $targetDoc = self::getDocumentById($targetDocId);
        
        if (!$sourceDoc || !$targetDoc) {
            throw new Exception('One or both documents not found');
        }
        
        if ($sourceDoc['file_type'] !== 'pdf' || $targetDoc['file_type'] !== 'pdf') {
            throw new Exception('Only PDF documents can be merged');
        }
        
        // Create merged filename
        $mergedFilename = uniqid() . '_' . preg_replace('/[^a-z0-9\.]/i', '_', $mergeName);
        $mergedFilepath = Config::UPLOAD_DIR . $mergedFilename;
        
        // In a real application, you would use a PDF library like TCPDF or FPDI to merge the PDFs
        // This is a simplified example that just copies the first file
        if (!copy($sourceDoc['file_path'], $mergedFilepath)) {
            throw new Exception('Failed to create merged document');
        }
        
        // Get file size
        $mergedSize = filesize($mergedFilepath);
        
        // Insert merged document record
        $stmt = $db->prepare("INSERT INTO merged_documents 
                            (name, file_path, file_size, department_id, created_by, source_documents)
                            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $mergeName,
            $mergedFilepath,
            $mergedSize,
            $departmentId,
            $userId,
            json_encode([$sourceDocId, $targetDocId])
        ]);
        
        $mergedId = $db->lastInsertId();
        
        // Log the action
        Auth::logAction($userId, 'merge', 'merged_documents', $mergedId, null, [
            'name' => $mergeName,
            'source_documents' => [$sourceDocId, $targetDocId]
        ]);
        
        return $mergedId;
    }
    
    public static function getDocumentById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT d.*, u.first_name, u.last_name, dep.name as department_name
                             FROM documents d
                             JOIN users u ON d.uploaded_by = u.id
                             JOIN departments dep ON d.department_id = dep.id
                             WHERE d.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public static function deleteDocument($id, $userId) {
        $db = getDB();
        
        // Get document info for logging
        $document = self::getDocumentById($id);
        if (!$document) {
            throw new Exception('Document not found');
        }
        
        $db->beginTransaction();
        
        try {
            // Delete document-tag relationships
            $stmt = $db->prepare("DELETE FROM document_tags WHERE document_id = ?");
            $stmt->execute([$id]);
            
            // Delete document
            $stmt = $db->prepare("DELETE FROM documents WHERE id = ?");
            $stmt->execute([$id]);
            
            // Delete the file
            if (file_exists($document['file_path'])) {
                unlink($document['file_path']);
            }
            
            $db->commit();
            
            // Log the action
            Auth::logAction($userId, 'delete', 'documents', $id, $document, null);
            
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
    
    public static function downloadDocument($id) {
        $document = self::getDocumentById($id);
        if (!$document) {
            throw new Exception('Document not found');
        }
        
        if (!file_exists($document['file_path'])) {
            throw new Exception('File not found on server');
        }
        
        // Log the download
        Auth::logAction($_SESSION['user']['id'] ?? null, 'download', 'documents', $id);
        
        // Set headers for download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($document['file_path']) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($document['file_path']));
        flush();
        
        readfile($document['file_path']);
        exit;
    }
}