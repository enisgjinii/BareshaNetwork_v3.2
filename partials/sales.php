<?php

include_once "../conn-d.php";

class PaymentAnalyzer
{
    private $conn;

    /**
     * Constructor for the class.
     *
     * @param datatype $conn description
     */
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * analyzePayments function analyzes payments for a given year and returns the results.
     *
     * @param string $selectedYear The year for which payments are to be analyzed
     * @return array The analyzed payment results
     */
    public function analyzePayments($selectedYear)
    {
        $months = range(1, 12);
        $shitjeResults = [];
        $mbetjeResults = [];

        if ($selectedYear == '2024') {
            $sqlPayments = "SELECT MONTH(payment_date) AS month, SUM(payment_amount) AS sum
                            FROM payments
                            WHERE YEAR(payment_date) = ?
                            GROUP BY MONTH(payment_date)";
            $stmtPayments = $this->conn->prepare($sqlPayments);
            $stmtPayments->bind_param("s", $selectedYear);
            $stmtPayments->execute();
            $resultPayments = $stmtPayments->get_result();

            while ($rowPayments = $resultPayments->fetch_assoc()) {
                $month = $rowPayments['month'];
                $shitjeResults[$month] = $rowPayments['sum'] ?: 0;
                $mbetjeResults[$month] = 0; // No corresponding data in "shitje2" for payments
            }
            $stmtPayments->close();
        } else {
            foreach ($months as $month) {
                $startDateMonth = date("$selectedYear-$month-01");
                $endDateMonth = date("Y-m-t", strtotime($startDateMonth));

                $sqlShitje = "SELECT SUM(klientit) AS sum FROM shitje WHERE data >= ? AND data <= ?";
                $stmtShitje = $this->conn->prepare($sqlShitje);
                $stmtShitje->bind_param("ss", $startDateMonth, $endDateMonth);
                $stmtShitje->execute();
                $resultShitje = $stmtShitje->get_result();
                $shitjeResults[$month] = $resultShitje->fetch_assoc()['sum'] ?: 0;
                $stmtShitje->close();

                $sqlMbetje = "SELECT SUM(mbetja) AS sum FROM shitje2 WHERE data >= ? AND data <= ?";
                $stmtMbetje = $this->conn->prepare($sqlMbetje);
                $stmtMbetje->bind_param("ss", $startDateMonth, $endDateMonth);
                $stmtMbetje->execute();
                $resultMbetje = $stmtMbetje->get_result();
                $mbetjeResults[$month] = $resultMbetje->fetch_assoc()['sum'] ?: 0;
                $stmtMbetje->close();
            }
        }

        $data = [
            'shitje' => $shitjeResults,
            'mbetje' => $mbetjeResults
        ];

        return $data;
    }
}

$selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');
$paymentAnalyzer = new PaymentAnalyzer($conn);
$data = $paymentAnalyzer->analyzePayments($selectedYear);

header('Content-Type: application/json');
echo json_encode($data);
