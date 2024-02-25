<?php
class PageAccessController
{
    private $conn;
    private $user_info;
    private $user_credentials;
    private $current_url;
    private $filename;
    private $has_access;
    // Konstruktori për të filluar objektin me parametrat e nevojshëm
    public function __construct($conn, $user_info)
    {
        $this->conn = $conn;
        $this->user_info = $user_info;
        $this->current_url = $_SERVER['REQUEST_URI'];
        $this->filename = basename($this->current_url);
        $this->has_access = false;
        $this->user_credentials = $_SESSION['id'];
    }
    // Metoda për të regjistruar aktivitetin e përdoruesit
    public function logActivity()
    {
        if ($this->filename == "logs.php") {
            $user_informations = $this->user_info['givenName'] . ' ' . $this->user_info['familyName'];
            $log_description = $user_informations . " ka vrojtuar listen e aktiviteteve";
            $date_information = date('Y-m-d H:i:s');
            $stmt = $this->conn->prepare("INSERT INTO logs (stafi, ndryshimi, koha) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $user_informations, $log_description, $date_information);
            if ($stmt->execute()) {
                // Logu u fut me sukses
            } else {
                echo "Gabim: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    // Metoda për të verifikuar qasjen e përdoruesit në faqen aktuale
    public function checkAccess()
    {
        $stmt = $this->conn->prepare("SELECT googleauth.firstName AS user_name, roles.name AS role_name, roles.id AS role_id, GROUP_CONCAT(DISTINCT role_pages.page) AS pages
            FROM googleauth
            LEFT JOIN user_roles ON googleauth.id = user_roles.user_id
            LEFT JOIN roles ON user_roles.role_id = roles.id
            LEFT JOIN role_pages ON roles.id = role_pages.role_id
            WHERE googleauth.id = ?
            GROUP BY googleauth.id, roles.id");
        $stmt->bind_param("i", $this->user_credentials);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $menu_pages = explode(',', $row['pages']);
                if (in_array($this->filename, $menu_pages)) {
                    $this->has_access = true;
                    break;
                }
            }
            $result->free();
        }
        return $this->has_access;
    }
}
// Krijo objektin PageAccessController me lidhjen në bazën e të dhënave dhe informacionin e përdoruesit
$pageAccessController = new PageAccessController($conn, $user_info);
// Regjistro aktivitetin e përdoruesit
$pageAccessController->logActivity();
// Kontrollo qasjen e përdoruesit në faqen aktuale
if (!$pageAccessController->checkAccess()) {
    // Nëse përdoruesi nuk ka qasje, ridrejto në faqen e gabimit dhe dal
    header('Location:error.php');
    exit;
}
