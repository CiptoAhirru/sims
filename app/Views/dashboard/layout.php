<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="<?= csrf_token() ?>" content="<?= csrf_hash() ?>">
    <title><?= $this->renderSection('title') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-white shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand fw-bold text-danger d-flex align-items-center" href="<?= base_url('/dashboard') ?>">
                <div class="logo me-2">SP</div> SIMS PPOB
            </a>
            <div class="ms-auto d-flex gap-4">
                <a href="<?= base_url('/topup') ?>"
                    class="nav-link fw-medium <?= url_is('topup*') ? 'text-danger' : '' ?>">
                    Top Up
                </a>
                <a href="<?= base_url('/transaction') ?>"
                    class="nav-link fw-medium <?= url_is('transaction*') ? 'text-danger' : '' ?>">
                    Transaction
                </a>
                <a href="<?= base_url('/profile') ?>"
                    class="nav-link fw-medium <?= url_is('profile*') ? 'text-danger' : '' ?>">
                    Akun
                </a>
            </div>
        </div>
    </nav>


    <?= $this->renderSection('content') ?>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>

    <?= $this->renderSection('scripts') ?>
</body>

</html>