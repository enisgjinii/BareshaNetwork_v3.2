<?php
include 'partials/header.php'; ?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Kontabiliteti</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="ttatimi.php" class="text-reset" style="text-decoration: none;">
                            Tatimi
                        </a>
                    </li>
                </ol>
            </nav>
            <?php
            $tabs = [
                ['id' => 'pills-Kontributet-tab', 'target' => '#pills-Kontributet', 'text' => 'Kontributet', 'active' => true],
                ['id' => 'pills-tvsh-tab', 'target' => '#pills-tvsh', 'text' => 'TVSH', 'active' => false],
                ['id' => 'pills-tatimi-tab', 'target' => '#pills-tatimi', 'text' => 'Tatim', 'active' => false]
            ];
            ?>

            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <?php foreach ($tabs as $tab) : ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-5<?= $tab['active'] ? ' active' : '' ?>" id="<?= $tab['id'] ?>" data-bs-toggle="pill" data-bs-target="<?= $tab['target'] ?>" type="button" role="tab" aria-controls="<?= ltrim($tab['target'], '#') ?>" aria-selected="<?= $tab['active'] ? 'true' : 'false' ?>">
                            <?= $tab['text'] ?>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="tab-content" id="pills-tabContent">
                <?php include('kontributet_pill.php'); ?>
                <?php include('tvsh_pill.php'); ?>
                <?php include('tatimi_pill.php'); ?>
            </div>
        </div>
    </div>
</div>
<!-- <script src="kontributet.js"></script> -->
<!-- <script src="tvsh.js"></script> -->
<!-- <script src="tatimi.js"></script> -->
<?php include 'partials/footer.php' ?>