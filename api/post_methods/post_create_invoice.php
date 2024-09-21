<?php
// Enable error reporting (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Redirects to a specified URL and exits the script.
 *
 * @param string $url The URL to redirect to.
 */
function redirect($url)
{
    header("Location: $url");
    exit;
}

/**
 * Handles file upload and returns the file path or null if no file is uploaded.
 *
 * @param array $file The $_FILES['file_upload'] array.
 * @return string|null The destination path of the uploaded file or null.
 * @throws Exception If the upload fails.
 */
function handleFileUpload($file)
{
    if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
        $uploadsDir = 'uploads/';
        // Ensure the uploads directory exists
        if (!is_dir($uploadsDir) && !mkdir($uploadsDir, 0755, true)) {
            throw new Exception("Failed to create uploads directory.");
        }
        // Generate a unique file name to prevent overwriting
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $uniqueName = uniqid('invoice_', true) . '.' . $fileExtension;
        $fileDestination = $uploadsDir . $uniqueName;
        if (!move_uploaded_file($file['tmp_name'], $fileDestination)) {
            throw new Exception("Failed to move uploaded file.");
        }
        return $fileDestination;
    }
    return null; // No file uploaded
}

/**
 * Fetches the client details based on customer ID.
 *
 * @param mysqli $conn The database connection.
 * @param int $customer_id The customer ID.
 * @return array The client's details including name.
 * @throws Exception If the client is not found or query fails.
 */
function getClientDetails($conn, $customer_id)
{
    $stmt = $conn->prepare("SELECT emri FROM klientet WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Failed to prepare client details query: " . $conn->error);
    }
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception("Error getting client details: " . $stmt->error);
    }
    $row = $result->fetch_assoc();
    $stmt->close();
    if (!$row) {
        throw new Exception("Client not found.");
    }
    return $row;
}

/**
 * Inserts an invoice into the database.
 *
 * @param mysqli_stmt $stmt The prepared statement for inserting invoices.
 * @param array $invoiceData The data for the invoice.
 * @return bool Success or failure of the execution.
 */
function insertInvoice($stmt, $invoiceData)
{
    $stmt->bind_param(
        "sisddssddsss",
        $invoiceData['invoice_number'],
        $invoiceData['customer_id'],
        $invoiceData['item'],
        $invoiceData['total_amount'],
        $invoiceData['total_amount_after_percentage'],
        $invoiceData['created_date'],
        $invoiceData['status'],
        $invoiceData['total_amount_in_eur'],
        $invoiceData['total_amount_in_eur_after_percentage'],
        $invoiceData['type'],
        $invoiceData['file_destination'],
        $invoiceData['subaccount_name']
    );
    return $stmt->execute();
}

try {
    // Define required fields
    $requiredFields = [
        "invoice_number",
        "customer_id",
        "item",
        "total_amount",
        "total_amount_after_percentage",
        "created_date",
        "invoice_status",
        "type"
    ];
    // Check if the form has been submitted and all required fields are set
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field])) {
            throw new Exception("Required field '$field' is missing.");
        }
    }
    // Sanitize and validate form data
    $invoice_number = filter_input(INPUT_POST, "invoice_number", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $customer_id = filter_input(INPUT_POST, "customer_id", FILTER_VALIDATE_INT);
    if ($customer_id === false) {
        throw new Exception("Invalid Customer ID.");
    }
    $item = filter_input(INPUT_POST, "item", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $total_amount = filter_input(INPUT_POST, "total_amount", FILTER_VALIDATE_FLOAT) ?: 0;
    $total_amount_after_percentage = filter_input(INPUT_POST, "total_amount_after_percentage", FILTER_VALIDATE_FLOAT) ?: 0;
    $total_amount_in_eur = filter_input(INPUT_POST, "total_amount_in_eur", FILTER_VALIDATE_FLOAT) ?: 0;
    $total_amount_after_percentage_in_eur = filter_input(INPUT_POST, "total_amount_after_percentage_in_eur", FILTER_VALIDATE_FLOAT) ?: 0;
    $created_date = filter_input(INPUT_POST, "created_date", FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: date('Y-m-d');
    $status = filter_input(INPUT_POST, "invoice_status", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $type = filter_input(INPUT_POST, "type", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Validate the 'type' field to accept both 'individual' and 'grupor'
    if ($type !== 'grupor' && $type !== 'individual') {
        throw new Exception("Invalid invoice type. Only 'individual' and 'grupor' are supported.");
    }

    // Handle file upload
    $file_destination = handleFileUpload($_FILES['file_upload'] ?? null);

    // Connect to the database
    require_once '../../conn-d.php';
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Fetch client details
    $client_details = getClientDetails($conn, $customer_id);
    $client_name = $client_details['emri'];

    // Prepare the invoice insertion statement
    $insertStmt = $conn->prepare("
        INSERT INTO invoices (
            invoice_number,
            customer_id,
            item,
            total_amount,
            total_amount_after_percentage,
            created_date,
            state_of_invoice,
            total_amount_in_eur,
            total_amount_in_eur_after_percentage,
            type,
            file_path,
            subaccount_name
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    if (!$insertStmt) {
        throw new Exception("Failed to prepare invoice insertion: " . $conn->error);
    }

    if ($type === 'grupor') {
        // Start transaction for grupor invoices
        $conn->begin_transaction();
        try {
            // Fetch all sub-accounts for the client
            $subStmt = $conn->prepare("SELECT name, percentage FROM client_subaccounts WHERE client_id = ?");
            if (!$subStmt) {
                throw new Exception("Failed to prepare sub-accounts query: " . $conn->error);
            }
            $subStmt->bind_param("i", $customer_id);
            $subStmt->execute();
            $subResult = $subStmt->get_result();
            if (!$subResult) {
                throw new Exception("Failed to fetch sub-accounts: " . $subStmt->error);
            }
            $subaccounts = $subResult->fetch_all(MYSQLI_ASSOC);
            $subStmt->close();
            if (empty($subaccounts)) {
                throw new Exception("No sub-accounts found for the client.");
            }
            // Calculate the sum of sub-accounts' percentages
            $sum_percentage = 0.0;
            foreach ($subaccounts as $subaccount) {
                $sum_percentage += floatval($subaccount['percentage']);
            }
            // Validate the sum of percentages
            if ($sum_percentage > 100.00) {
                throw new Exception("The total percentage of sub-accounts (" . number_format($sum_percentage, 2) . "%) exceeds 100%.");
            } elseif ($sum_percentage < 0.00) {
                throw new Exception("The total percentage of sub-accounts (" . number_format($sum_percentage, 2) . "%) is invalid.");
            }
            // Calculate base_percentage dynamically
            $base_percentage = 100.00 - $sum_percentage;
            // Optional: Handle case where base_percentage is zero or negative
            if ($base_percentage < 0.00) {
                throw new Exception("Base percentage calculated as negative. Please check sub-accounts' percentages.");
            }
            // Calculate the adjusted total amount after removing base_percentage
            $adjusted_total_amount_after_percentage = $total_amount;
            $adjusted_total_amount_in_eur_after_percentage = $total_amount_in_eur;
            // **Create only sub-account invoices without a main invoice**
            foreach ($subaccounts as $index => $subaccount) {
                $new_invoice_number = "{$invoice_number}-" . ($index + 1);
                $item_for_invoice = "{$item}";
                $subaccount_name = "{$subaccount['name']}";
                // Calculate amounts based on sub-account percentage
                $percentage = $subaccount['percentage'];
                $total_amount_sub = ($adjusted_total_amount_after_percentage * $percentage) / 100;
                $total_amount_in_eur_sub = ($adjusted_total_amount_in_eur_after_percentage * $percentage) / 100;
                // Optional: Round the amounts to 2 decimal places
                $total_amount_sub = round($total_amount_sub, 2);
                $total_amount_in_eur_sub = round($total_amount_in_eur_sub, 2);
                $subInvoiceData = [
                    'invoice_number' => $new_invoice_number,
                    'customer_id' => $customer_id,
                    'item' => $item_for_invoice,
                    'total_amount' => $total_amount_sub,
                    'total_amount_after_percentage' => $total_amount_sub, // Assuming same as total_amount
                    'created_date' => $created_date,
                    'status' => $status,
                    'total_amount_in_eur' => $total_amount_in_eur_sub,
                    'total_amount_in_eur_after_percentage' => $total_amount_in_eur_sub, // Assuming same as total_amount_in_eur
                    'type' => $type,
                    'file_destination' => $file_destination,
                    'subaccount_name' => $subaccount_name
                ];
                if (!insertInvoice($insertStmt, $subInvoiceData)) {
                    throw new Exception("Error inserting invoice for sub-account '{$subaccount['name']}': " . $insertStmt->error);
                }
            }
            // Commit the transaction
            $conn->commit();
            // Close the prepared statement and connection
            $insertStmt->close();
            $conn->close();
            // Redirect with success message
            redirect("../../invoice.php?success=1");
        } catch (Exception $e) {
            // Rollback the transaction on error
            $conn->rollback();
            error_log("Transaction failed: " . $e->getMessage());
            throw new Exception("An error occurred while processing the grupor invoices.");
        }
    } elseif ($type === 'individual') {
        // Process individual invoice without splitting into sub-accounts
        $individualInvoiceData = [
            'invoice_number' => $invoice_number,
            'customer_id' => $customer_id,
            'item' => $item,
            'total_amount' => $total_amount,
            'total_amount_after_percentage' => $total_amount_after_percentage,
            'created_date' => $created_date,
            'status' => $status,
            'total_amount_in_eur' => $total_amount_in_eur,
            'total_amount_in_eur_after_percentage' => $total_amount_after_percentage_in_eur,
            'type' => $type,
            'file_destination' => $file_destination,
            'subaccount_name' => null // No sub-account for individual invoices
        ];
        if (!insertInvoice($insertStmt, $individualInvoiceData)) {
            throw new Exception("Error inserting individual invoice: " . $insertStmt->error);
        }
        // Close the prepared statement and connection
        $insertStmt->close();
        $conn->close();
        // Redirect with success message
        redirect("../../invoice.php?success=1");
    }
} catch (Exception $e) {
    // Handle any exceptions and display a user-friendly message
    error_log("Error: " . $e->getMessage());
    die("An error occurred: " . htmlspecialchars($e->getMessage()));
}
