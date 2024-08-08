<?php include('partials/header.php'); ?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <div class="p-5 mb-4 card shadow-sm rounded-5">
                    <h4 class="font-weight-bold text-gray-800 mb-4 text-dark">Platformat tjera</h4>
                    <nav class="d-flex">
                        <h6 class="mb-0 text-dark">
                            <a href="" class="text-reset text-dark">Financat</a>
                            <span>/</span>
                        </h6>
                    </nav>
                </div>
                <div class="p-5 rounded-5 shadow-sm mb-4 card text-dark">
                    <table id="example" data-ordering="false" class="table w-100 table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-dark">Artist(s)</th>
                                <th class="text-dark">Reporting Period</th>
                                <th class="text-dark">Accounting Period</th>
                                <th class="text-dark">Release</th>
                                <th class="text-dark">Track</th>
                                <th class="text-dark">Country</th>
                                <th class="text-dark">Revenue (USD)</th>
                                <th class="text-dark">Revenue Share (%)</th>
                                <th class="text-dark">Split Pay Share (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $emri = $_SESSION["emri"];
                            $kueri = $conn->query("SELECT * FROM platformat WHERE Artist='$emri'");
                            $q4 = $conn->query("SELECT SUM(`RevenueUSD`) as `sum` FROM `platformat` WHERE Artist='$emri'");
                            $qq4 = mysqli_fetch_array($q4);
                            if (mysqli_num_rows($kueri) > 0) {
                                while ($k = mysqli_fetch_array($kueri)) {
                            ?>
                                    <tr>
                                        <td>
                                            <?php echo $k['Artist']; ?>
                                        </td>
                                        <td>
                                            <?php echo $k['ReportingPeriod']; ?>
                                        </td>
                                        <td>
                                            <?php echo $k['AccountingPeriod']; ?>
                                        </td>
                                        <td>
                                            <?php echo $k['rel']; ?>
                                        </td>
                                        <td>
                                            <?php echo $k['Track']; ?>
                                        </td>
                                        <td>
                                            <?php echo $k['Country']; ?>
                                        </td>
                                        <td>
                                            <?php echo $k['RevenueUSD']; ?>
                                        </td>
                                        <td>
                                            <?php echo $k['RevenueShare']; ?>
                                        </td>
                                        <td>
                                            <?php echo $k['SplitPayShare']; ?>
                                        </td>
                                    </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <th class="text-dark">Artist(s)</th>
                                <th class="text-dark">Reporting Period</th>
                                <th class="text-dark">Accounting Period</th>
                                <th class="text-dark">Release</th>
                                <th class="text-dark">Track</th>
                                <th class="text-dark">Country</th>
                                <th class="text-dark">Revenue (USD)</th>
                                <th class="text-dark">Revenue Share (%)</th>
                                <th class="text-dark">Split Pay Share (%)</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
<script>
    $('#example').DataTable({
        responsive: true,
        search: {
            return: true,
        },
        dom: 'frtip',
        fixedHeader: true,
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
        },
    })
</script>