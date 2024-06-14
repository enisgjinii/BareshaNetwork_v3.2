<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Html;

function convertExcelToHtml($filePath)
{
    $spreadsheet = IOFactory::load($filePath);
    $writer = new Html($spreadsheet);

    // Capture the HTML content
    ob_start();
    $writer->save('php://output');
    $htmlContent = ob_get_contents();
    ob_end_clean();

    return $htmlContent;
}
