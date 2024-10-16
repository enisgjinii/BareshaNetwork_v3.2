<?php
require_once 'conn-d.php';
// Function to execute prepared statements securely
function executePreparedStatement($conn, $query, $types = '', $params = [])
{
  $stmt = $conn->prepare($query);
  if (!$stmt) {
    throw new Exception("Përgatitja dështoi: " . $conn->error);
  }
  if ($types && $params) {
    $stmt->bind_param($types, ...$params);
  }
  if (!$stmt->execute()) {
    throw new Exception("Ekzekutimi dështoi: " . $stmt->error);
  }
  return $stmt;
}
// Check if 'fshij' and 'fatura' parameters are set
if (isset($_GET['fshij']) && isset($_GET['fatura'])) {
  $fshijid = intval($_GET['fshij']);
  $fatura = $_GET['fatura'];
  try {
    // Delete the entry from 'shitje' table securely
    executePreparedStatement($conn, "DELETE FROM shitjeFacebook WHERE id = ? AND fatura = ?", "is", [$fshijid, $fatura]);
    // Redirect back with success message
    header("Location: shitjeFacebook.php?fatura=" . urlencode($fatura) . "&action=delete_success&message=" . urlencode("Fatura u fshi me sukses."));
    exit();
  } catch (Exception $e) {
    // Redirect back with error message
    header("Location: shitjeFacebook.php?fatura=" . urlencode($fatura) . "&action=delete_error&message=" . urlencode($e->getMessage()));
    exit();
  }
} else {
  // Redirect back with error if parameters are missing
  $faturaParam = isset($_GET['fatura']) ? $_GET['fatura'] : '';
  header("Location: shitjeFacebook.php?fatura=" . urlencode($faturaParam) . "&action=delete_error&message=" . urlencode("Parametrat nuk janë të plota."));
  exit();
}
