<?php
// get_product.php

// Ensure this script only handles GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// Check if 'id' parameter is present
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Product ID is required.']);
    exit;
}

$product_id = intval($_GET['id']);

// Database configuration
$host = 'localhost';
$db   = 'test_food';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// Set up DSN and options for PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Enable exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Set default fetch mode
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Disable emulation
];

try {
    // Create PDO instance
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit;
}

// Fetch product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo json_encode(['status' => 'error', 'message' => 'Product not found.']);
    exit;
}

// Fetch related data
// Sizes
$stmt = $pdo->prepare("SELECT ps.size_id, s.name, ps.price FROM product_sizes ps JOIN sizes s ON ps.size_id = s.id WHERE ps.product_id = ?");
$stmt->execute([$product_id]);
$sizes = $stmt->fetchAll();

// Options
$stmt = $pdo->prepare("SELECT po.option_id, o.name, po.is_extra_sauce FROM product_options po JOIN options o ON po.option_id = o.id WHERE po.product_id = ?");
$stmt->execute([$product_id]);
$options = $stmt->fetchAll();

// Allergies
$stmt = $pdo->prepare("SELECT allergy_id FROM product_allergies WHERE product_id = ?");
$stmt->execute([$product_id]);
$allergies = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Mix Options
$stmt = $pdo->prepare("SELECT mix_option_id FROM product_mix_options WHERE product_id = ?");
$stmt->execute([$product_id]);
$mix_options = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Prepare response
$response = [
    'status' => 'success',
    'data' => [
        'id' => $product['id'],
        'name' => $product['name'],
        'description' => $product['description'],
        'item_number' => $product['item_number'],
        'is_new' => $product['is_new'],
        'has_new_offers' => $product['has_new_offers'],
        'allergy_info' => $product['allergy_info'],
        'sizes' => $sizes,
        'options' => $options,
        'allergies' => $allergies,
        'mix_options' => $mix_options
    ]
];

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
