<?= $this->extend('dashboard/layout') ?>

<?= $this->section('title') ?>
SIMS PPOB - Dashboard
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container my-4">
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <img src="<?= base_url('assets/images/Profile Photo.png') ?>"
                    class="rounded-circle me-3" width="60" height="60" alt="profile">
                <div>
                    <p class="mb-0 text-muted">Selamat datang,</p>
                    <h5 class="fw-bold mb-0"><?= esc($profile['first_name'] ?? 'User') ?> <?= esc($profile['last_name'] ?? '') ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="saldo-card p-4 rounded-3 mb-4 text-white d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1">Saldo anda</p>
                    <h4 class="fw-bold" id="saldoDisplay" data-saldo="<?= number_format(esc($saldo['balance'] ?? '0'), 0, ',', '.') ?>">
                        *****
                    </h4>
                    <a href="javascript:void(0)" id="toggleSaldo" class="text-white-50 small text-decoration-none">
                        Lihat Saldo <i class="bi bi-eye-slash"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>




    <!-- Menu Grid -->
    <div class="row text-center g-3 mb-4">
        <?= csrf_field() ?>
        <?php foreach ($services as $menu): ?>
            <!-- Mobile: col-4 (3 per baris) | Tablet: col-3 (4 per baris) | Desktop: col-1 (12 per baris) -->
            <div class="col-4 col-sm-3 col-lg-1">
                <button type="button"
                    class="btn-service border-0 bg-transparent text-center"
                    data-code="<?= $menu['service_code'] ?>"
                    data-name="<?= $menu['service_name'] ?>">

                    <div class="menu-icon rounded-3 d-flex justify-content-center align-items-center mx-auto"
                        style="width: 60px; height: 60px;">
                        <img src="<?= $menu['service_icon'] ?>"
                            width="60" height="60" alt="<?= $menu['service_code'] ?>">
                    </div>
                    <small class="d-block mt-2"><?= $menu['service_name'] ?></small>
                </button>
            </div>
        <?php endforeach; ?>

    </div>



    <!-- Promo Section -->
    <h6 class="fw-bold mb-3">Temukan promo menarik</h6>
    <div class="row g-3">
        <?php

        foreach ($banner as $bn): ?>
            <div class="col-md-3">
                <div class="promo-card rounded-3 overflow-hidden">
                    <img src="<?= $bn['banner_image'] ?>" class="img-fluid w-100" alt="Promo">
                </div>
            </div>
        <?php endforeach ?>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const saldoDisplay = document.getElementById("saldoDisplay");
        const toggleSaldo = document.getElementById("toggleSaldo");
        const saldoAsli = saldoDisplay.dataset.saldo;

        let visible = false;

        toggleSaldo.addEventListener("click", function() {
            visible = !visible;
            if (visible) {
                saldoDisplay.textContent = "Rp " + saldoAsli;
                toggleSaldo.innerHTML = 'Sembunyikan Saldo <i class="bi bi-eye-slash"></i>'
            } else {
                saldoDisplay.textContent = "*****";
                toggleSaldo.innerHTML = 'Lihat Saldo <i class="bi bi-eye-slash"></i>'
            }
        });
    });

    $(document).on("click", ".btn-service", function() {
        let code = $(this).data("code");
        let name = $(this).data("name");
        let csrfName = $('meta[name="<?= csrf_token() ?>"]').attr('name'); // contoh: csrf_test_name
        let csrfHash = $('meta[name="<?= csrf_token() ?>"]').attr('content');


        // Contoh kirim ke backend via AJAX
        Swal.fire({
            title: 'Konfirmasi Transaksi',
            text: `Apakah Anda yakin ingin melakukan transaksi ${name} ?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Transaksi',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url('actiontransaksi') ?>",
                    type: "POST",
                    data: {
                        [csrfName]: csrfHash, // token wajib
                        service_code: code
                    },
                    dataType: "json",
                    success: function(res) {
                        if (res.status === "success") {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = res.redirect;
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: res.message
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan server.'
                        });
                    }
                });
            }
        });
    });
</script>

<?= $this->endSection() ?>