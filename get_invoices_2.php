<?php
// Include your database connection here
include 'conn-d.php';

// Cache for conversion rates
$conversionCache = [];

// Function to convert currency to EUR
function convertToEUR($amount)
{
    global $conversionCache;

    // Check if the conversion rate is already cached
    if (isset($conversionCache[$amount])) {
        return $conversionCache[$amount];
    }

    // API URL for currency conversion
    $apiUrl = "https://api.exconvert.com/convert?from=USD&to=EUR&amount=" . $amount . "&access_key=7ac9d0d8-2c2a1729-0a51382b-b85cd112";

    // Fetch conversion rate from API
    $response = file_get_contents($apiUrl);

    // Check if API call was successful
    if ($response !== false) {
        // Decode JSON response
        $result = json_decode($response, true);

        // Check if conversion was successful
        if ($result && isset($result['result'])) {
            // Cache the conversion rate
            $conversionCache[$amount] = $result['result'];

            // Return converted amount
            return $result['result'];
        } else {
            // Conversion failed
            return 'Conversion failed';
        }
    } else {
        // API call failed
        return 'API call failed';
    }
}
// Define your database table
$table = 'invoices';
// Define your primary key column
$primaryKey = 'id';
// Map your DataTables columns to your database columns
$columns = array(
    array('db' => 'id', 'dt' => 'id', 'searchable' => false),
    array('db' => 'invoice_number', 'dt' => 'invoice_number', 'searchable' => true),
    array('db' => 'customer_id', 'dt' => 'customer_id', 'searchable' => true),
    array('db' => 'item', 'dt' => 'item', 'searchable' => true),
    array('db' => 'total_amount_after_percentage', 'dt' => 'total_amount_after_percentage', 'searchable' => false),
    array('db' => 'paid_amount', 'dt' => 'paid_amount', 'searchable' => false),
    array('db' => 'k.emri AS customer_name', 'dt' => 'customer_name', 'searchable' => true),
    array('db' => 'y.shuma AS customer_loan', 'dt' => 'customer_loan', 'searchable' => false)
);

// Start building the SQL query
$sql = "SELECT i.id, i.invoice_number, i.item, i.customer_id, i.state_of_invoice,
                i_agg.total_amount,
                i_agg.total_amount_after_percentage,
                i_agg.paid_amount,
                k.emri AS customer_name,
                y.customer_loan_amount,
                y.customer_loan_paid
        FROM (
            SELECT id, invoice_number, item, customer_id, state_of_invoice,
                   total_amount_after_percentage, paid_amount
            FROM $table
            GROUP BY invoice_number
        ) AS i
        JOIN klientet AS k ON i.customer_id = k.id
        LEFT JOIN (
            SELECT kanali, 
                SUM(shuma) AS customer_loan_amount,
                SUM(pagoi) AS customer_loan_paid
            FROM yinc
            GROUP BY kanali
        ) AS y ON i.customer_id = y.kanali
        LEFT JOIN (
            SELECT invoice_number,  
                SUM(total_amount) as total_amount,
                SUM(total_amount_after_percentage) as total_amount_after_percentage,
                SUM(paid_amount) as paid_amount
            FROM $table
            GROUP BY invoice_number
        ) AS i_agg ON i.invoice_number = i_agg.invoice_number";

// Append WHERE clause for filtering
$sql .= " WHERE ($total_amount_after_percentage_eur - i.paid_amount) > 1
          AND (k.lloji_klientit = 'Personal' OR k.lloji_klientit IS NULL)";

// Apply filtering (search)
if (!empty($_REQUEST['search']['value'])) {
    $sql .= " AND (";
    $searchConditions = array();
    foreach ($columns as $column) {
        if ($column['searchable']) {
            if ($column['dt'] === 'customer_name' || $column['dt'] === 'customer_loan') {
                $searchConditions[] = "`k`.`emri` LIKE '%" . mysqli_real_escape_string($conn, $_REQUEST['search']['value']) . "%'";
            } else {
                $searchConditions[] = "`i`.`" . $column['db'] . "` LIKE '%" . mysqli_real_escape_string($conn, $_REQUEST['search']['value']) . "%'";
            }
        }
    }
    $sql .= implode(" OR ", $searchConditions);
    $sql .= ")";
}

// Get the total number of records after filtering
$sqlCount = "SELECT COUNT(*) as count FROM (" . $sql . ") AS countTable";
$totalRecords = mysqli_fetch_assoc(mysqli_query($conn, $sqlCount))['count'];

// Apply ordering and pagination
$start = $_REQUEST['start'];
$length = $_REQUEST['length'];
$orderColumn = $columns[$_REQUEST['order'][0]['column']]['db'];
$orderDirection = $_REQUEST['order'][0]['dir'];
$sql .= " ORDER BY " . $orderColumn . " " . $orderDirection . " LIMIT " . $start . ", " . $length;

// Execute the modified SQL query
$query = mysqli_query($conn, $sql);

// Prepare the response data
$data = array();
while ($row = mysqli_fetch_assoc($query)) {
    // Convert total amount after percentage to EUR
    $total_amount_after_percentage_eur = convertToEUR($row['total_amount_after_percentage']);

    // Check if the conversion result is an object and contains the EUR key
    if (is_array($total_amount_after_percentage_eur) && isset($total_amount_after_percentage_eur['EUR'])) {
        // Extract the EUR amount
        $eur_amount = $total_amount_after_percentage_eur['EUR'];

        // Format the EUR amount with thousand separators and decimal precision
        $formatted_eur_amount = number_format($eur_amount, 2, '.', ',');

        // Add the formatted EUR amount to the row data
        $row['total_amount_after_percentage_eur'] = $formatted_eur_amount;
    } else {
        // Set an error message
        $error_message = 'Conversion error: Unable to retrieve converted amount';

        // Log the error or handle it as required

        // Add the error message to the row data
        $row['total_amount_after_percentage_eur'] = $error_message;
    }

    // Add the row to the response data
    $data[] = $row;
}

// Return the JSON response
$response = array(
    "draw" => intval($_REQUEST['draw']),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecords,
    "data" => $data
);

// Return the JSON response
echo json_encode($response);
