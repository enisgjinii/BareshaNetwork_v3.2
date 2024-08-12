<?php
session_start();
require_once 'conn-d.php';

// Function to get client IP
function getClientIP()
{
    return $_SERVER['HTTP_CLIENT_IP']
        ?? $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['REMOTE_ADDR'];
}

// Function to check if IP is in range
function ip_in_range($ip, $range)
{
    if (strpos($range, '/') !== false) {
        list($subnet, $bits) = explode('/', $range);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask;
        return ($ip & $mask) == $subnet;
    }

    if (strpos($range, '-') !== false) {
        list($start, $end) = array_map('ip2long', explode('-', $range));
        $ip = ip2long($ip);
        return ($ip >= $start && $ip <= $end);
    }

    return $ip === $range;
}

// Get user info
$user_email = strtolower($_COOKIE['email'] ?? '');
$visitor_ip = getClientIP();
$time = date("Y-m-d H:i:s");

// Log access
$log_entry = [
    $time,
    basename($_SERVER['PHP_SELF']),
    $user_email,
    $visitor_ip,
    ($visitor_ip === '127.0.0.1' || $visitor_ip === '::1') ? 'Localhost' : 'Remote'
];

$log_file = 'access_log.csv';
if (!file_exists($log_file)) {
    file_put_contents($log_file, implode(',', ['Time', 'Filename', 'User Email', 'IP Address', 'Type']) . PHP_EOL);
}
file_put_contents($log_file, implode(',', $log_entry) . PHP_EOL, FILE_APPEND);

// Check access
$stmt = $conn->prepare("SELECT ip_address FROM allowed_ips WHERE ? LIKE CONCAT(ip_address, '%')");
$stmt->bind_param("s", $visitor_ip);
$stmt->execute();
$result = $stmt->get_result();
$ip_allowed = $result->num_rows > 0;

if ($user_email !== 'egjini17@gmail.com'  && !$ip_allowed) {
    http_response_code(403);
    die("Access denied: Your IP address is not allowed to access this page.");
}

// Main code starts here
