<?php
include 'partials/header.php';
include 'conn-d.php';

// Sanitizimi i inputit
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $query = "SELECT * FROM klientet WHERE id = $id";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_array($result);
        $splited_percentage = isset($row['splited_percentage']) ? htmlspecialchars($row['splited_percentage']) : '';
        $company_percentage = isset($row['perqindja']) ? (int)$row['perqindja'] : 0;
        $client_percentage = isset($row['perqindja_e_klientit']) ? (int)$row['perqindja_e_klientit'] : 0;
    } else {
        echo "<div class='alert alert-danger rounded'>Gabim në query: " . mysqli_error($conn) . "</div>";
        $row = null;
        $splited_percentage = '';
        $company_percentage = 0;
        $client_percentage = 0;
    }
} else {
    echo "<div class='alert alert-danger rounded'>ID e pavlefshme.</div>";
    $row = null;
    $splited_percentage = '';
    $company_percentage = 0;
    $client_percentage = 0;
}

mysqli_close($conn);

// Funksioni për të analizuar dhe formatuar splited_percentage dhe për të llogaritur totalin e përqindjes së ndarë
function formatSplitedPercentage($splited_percentage)
{
    $splits = explode(',', $splited_percentage);
    $formatted_splits = '<ul class="list-group">';
    $total_split_percentage = 0;

    foreach ($splits as $split) {
        $split = trim($split);
        if (strpos($split, '-') !== false) {
            list($email, $percentage) = explode('-', $split);
            $email = trim($email);
            $percentage = trim($percentage);
            $total_split_percentage += (int)$percentage;
            $formatted_splits .= '<li class="list-group-item">' . htmlspecialchars($email) . ' - ' . htmlspecialchars($percentage) . '%</li>';
        }
    }

    $formatted_splits .= '</ul>';
    return [$formatted_splits, $total_split_percentage];
}

list($formatted_splits, $total_split_percentage) = formatSplitedPercentage($splited_percentage);
$remaining_percentage = $client_percentage - $total_split_percentage;

?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card rounded">
                <div class="card-header bg-primary text-white rounded">
                    Informacioni i Klientit
                </div>
                <div class="card-body">
                    <p><strong>Emri & Mbiemri:</strong> <?php echo isset($row['emri']) ? htmlspecialchars($row['emri']) : 'N/A'; ?></p>
                    <p><strong>Përqindja e Bareshës:</strong> <?php echo $company_percentage; ?>%</p>
                    <p><strong>Përqindja totale e klientit:</strong> <?php echo $client_percentage; ?>%</p>
                    <p><strong>Ndarjet e Subllogarive:</strong></p>
                    <?php echo $formatted_splits; ?>
                    <br>
                    <p><strong>Përqindja e mbetur:</strong> <?php echo $remaining_percentage; ?>%</p>
                </div>
            </div>

            <div class="card mt-4 rounded">
                <div class="card-header bg-secondary text-white rounded">
                    Përditëso Ndarjet e Subllogarive
                </div>
                <div class="card-body">
                    <form id="add-splited-percentage-form" method="post" action="add_splited_percentage.php">
                        <div class="form-group">
                            <label for="splited_percentage" class="form-label">Email dhe Përqindje (p.sh., egjini17@gmail.com - 40%, filan@gmail.com - 45%):</label>
                            <textarea name="splited_percentage" id="splited_percentage" class="form-control rounded" rows="3"><?php echo $splited_percentage; ?></textarea>
                        </div>
                        <input type="hidden" name="client_id" value="<?php echo $id; ?>">
                        <button type="submit" class="input-custom-css px-3 py-2 mt-2 rounded-pill">Përditëso</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>