<!-- Button trigger modal -->
<button type="button" class="input-custom-css px-3 py-2 mb-3" data-bs-toggle="modal" data-bs-target="#backupTokens">
    Lista e kanaleve te fshira
</button>
<!-- Modal -->
<div class="modal fade" id="backupTokens" tabindex="-1" aria-labelledby="backupTokensLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="backupTokensLabel">Lista e kanaleve te fshira</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php
                $sql = "SELECT * FROM backup_refresh_tokens";
                $result = $conn->query($sql);
                ?>
                <table class="table " id="backupTokensTable">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>ID-ja e kanalit</th>
                            <th>Emri i kanalit</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                        ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= $row['channel_id'] ?></td>
                                    <td><?= $row['channel_name'] ?></td>
                                    <td>
                                        <a style="text-decoration: none; display: flex; align-items: center;width: fit-content;" class="input-custom-css px-3 py-2" href="https://youtube.com/channel/<?= $row['channel_id'] ?>" target="_blank">
                                            <i style="font-size: 20px; margin-right: 5px;" class="fi fi-brands-youtube text-danger"></i>
                                            Shiko kanalin
                                        </a>
                                    </td>
                                </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>