<?php
// (A) Load PhpSpreadsheet library and set up autoload
require "vendor/autoload.php";

try {
    // Get the document path from the POST data
    $documentPath = $_POST['documentPath'];

    // Load the Excel file
    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadsheet = $reader->load($documentPath);

    // HTML output setup
    $html = '<html><head><style>
                body { font-family: Arial, sans-serif; }
                .sheet { margin-bottom: 20px; }
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .modal-header { border-bottom: 1px solid #dee2e6; }
                .modal-title { margin: 0; }
                .modal-body { padding: 20px; }
                .modal-footer { border-top: 1px solid #dee2e6; padding: 10px 20px; }
             </style></head><body>';

    // Loop through all worksheets in the spreadsheet
    for ($i = 0; $i < $spreadsheet->getSheetCount(); $i++) {
        // Get the current worksheet
        $worksheet = $spreadsheet->getSheet($i);
        $sheetName = $worksheet->getTitle();

        // Display sheet name
        $html .= "<div class='sheet'><h2>Sheet: {$sheetName}</h2><table class='table table-striped'>";

        // Loop through each row in the worksheet
        foreach ($worksheet->getRowIterator() as $row) {
            $html .= '<tr>';

            // Loop through each cell in the row
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            foreach ($cellIterator as $cell) {
                $cellValue = $cell->getValue();
                $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($cell->getColumn());

                // Highlight the header row
                if ($row->getRowIndex() == 1) {
                    $html .= "<th>{$cellValue}</th>";
                } else {
                    $html .= "<td>{$cellValue}</td>";
                }
            }
            $html .= '</tr>';
        }

        $html .= '</table></div>';
    }

    $html .= '</body></html>';

    // Output HTML
    echo $html;
} catch (Exception $e) {
    // Error handling
    echo "Error loading spreadsheet: " . $e->getMessage();
}
