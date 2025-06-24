<?php
// merge_pdf.php
require_once 'vendor/autoload.php';

use setasign\Fpdi\Fpdi;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get uploaded files and parameters
    $baseFile = $_FILES['base_file']['tmp_name'];
    $mergeFile = $_FILES['merge_file']['tmp_name'];
    $mergePosition = $_POST['merge_position'];
    $specificPage = isset($_POST['specific_page']) ? (int)$_POST['specific_page'] : 0;

    // Initialize FPDI
    $pdf = new Fpdi();

    // Handle different merge positions
    if ($mergePosition === 'before') {
        // Add merge file first
        $pageCount = $pdf->setSourceFile($mergeFile);
        for ($i = 1; $i <= $pageCount; $i++) {
            $templateId = $pdf->importPage($i);
            $pdf->AddPage();
            $pdf->useTemplate($templateId);
        }

        // Add base file
        $pageCount = $pdf->setSourceFile($baseFile);
        for ($i = 1; $i <= $pageCount; $i++) {
            $templateId = $pdf->importPage($i);
            $pdf->AddPage();
            $pdf->useTemplate($templateId);
        }
    } 
    elseif ($mergePosition === 'after') {
        // Add base file first
        $pageCount = $pdf->setSourceFile($baseFile);
        for ($i = 1; $i <= $pageCount; $i++) {
            $templateId = $pdf->importPage($i);
            $pdf->AddPage();
            $pdf->useTemplate($templateId);
        }

        // Add merge file
        $pageCount = $pdf->setSourceFile($mergeFile);
        for ($i = 1; $i <= $pageCount; $i++) {
            $templateId = $pdf->importPage($i);
            $pdf->AddPage();
            $pdf->useTemplate($templateId);
        }
    }
    elseif ($mergePosition === 'specific' && $specificPage > 0) {
        // Add base file up to specific page
        $basePageCount = $pdf->setSourceFile($baseFile);
        for ($i = 1; $i <= $specificPage; $i++) {
            $templateId = $pdf->importPage($i);
            $pdf->AddPage();
            $pdf->useTemplate($templateId);
        }

        // Add merge file
        $mergePageCount = $pdf->setSourceFile($mergeFile);
        for ($i = 1; $i <= $mergePageCount; $i++) {
            $templateId = $pdf->importPage($i);
            $pdf->AddPage();
            $pdf->useTemplate($templateId);
        }

        // Add remaining pages from base file
        for ($i = $specificPage + 1; $i <= $basePageCount; $i++) {
            $templateId = $pdf->importPage($i);
            $pdf->AddPage();
            $pdf->useTemplate($templateId);
        }
    }

    // Output the merged PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="merged_document.pdf"');
    $pdf->Output();
    exit;
}

http_response_code(400);
echo "Invalid request";