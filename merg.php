<?php
function mergePDFsWithLibreOffice($file1, $file2, $output) {
    // Check if LibreOffice is installed
    if (!isLibreOfficeInstalled()) {
        throw new Exception("LibreOffice is not installed or not in PATH");
    }
    
    // Create a temporary directory
    $tempDir = sys_get_temp_dir() . '/pdf_merge_' . uniqid();
    if (!mkdir($tempDir)) {
        throw new Exception("Failed to create temporary directory");
    }
    
    try {
        // Copy input files to temp directory
        copy($file1, "$tempDir/document1.pdf");
        copy($file2, "$tempDir/document2.pdf");
        
        // Create a simple text file with the merge order
        file_put_contents("$tempDir/merge_list.txt", "document1.pdf\ndocument2.pdf");
        
        // Run LibreOffice to merge PDFs
        $command = "libreoffice --headless --convert-to pdf --outdir $tempDir $tempDir/merge_list.txt";
        exec($command, $outputLines, $returnCode);
        
        if ($returnCode !== 0) {
            throw new Exception("LibreOffice failed to merge PDFs");
        }
        
        // The output will be named merge_list.pdf
        $mergedFile = "$tempDir/merge_list.pdf";
        if (!file_exists($mergedFile)) {
            throw new Exception("Merged PDF not created");
        }
        
        // Move the merged file to the desired output location
        rename($mergedFile, $output);
        
        return file_exists($output);
    } finally {
        // Clean up temporary files
        array_map('unlink', glob("$tempDir/*"));
        rmdir($tempDir);
    }
}

function isLibreOfficeInstalled() {
    exec("which libreoffice", $output, $returnCode);
    return $returnCode === 0;
}

// Usage example:
$file1 = 'document1.pdf';
$file2 = 'document2.pdf';
$output = 'merged.pdf';

try {
    if (mergePDFsWithLibreOffice($file1, $file2, $output)) {
        echo "PDFs merged successfully using LibreOffice! Output file: $output";
    } else {
        echo "Failed to merge PDFs";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>