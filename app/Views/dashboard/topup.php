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

    <!-- Promo Section -->
    <div class="row g-3">
        <!-- app/Views/dashboard/topup.php -->

        <div class="container py-5">
            <div class="row">
                <div class="col-md-12">

                    <!-- Judul -->
                    <p class="mb-0">Silahkan masukan</p>
                    <h2 class="fw-bold mb-4">Nominal Top Up</h2>

                    <form id="form">
                        <?= csrf_field() ?>
                        <div class="row">
                            <!-- Kolom Input & Tombol -->
                            <div class="col-md-8">
                                <!-- Input Nominal -->
                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                                        <input type="number" class="form-control" id="nominalInput" name="nominal" placeholder="masukan nominal Top Up">
                                    </div>
                                </div>
                                <!-- Tombol Submit -->
                                <button type="submit" id="btnSubmit" class="btn btn-secondary w-100 py-2">Top Up</button>
                            </div>

                            <!-- Kolom Tombol Nominal -->
                            <div class="col-md-4">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <button type="button" class="btn btn-outline-secondary w-100 nominal-btn" data-value="10000">Rp10.000</button>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" class="btn btn-outline-secondary w-100 nominal-btn" data-value="20000">Rp20.000</button>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" class="btn btn-outline-secondary w-100 nominal-btn" data-value="50000">Rp50.000</button>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" class="btn btn-outline-secondary w-100 nominal-btn" data-value="100000">Rp100.000</button>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" class="btn btn-outline-secondary w-100 nominal-btn" data-value="250000">Rp250.000</button>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" class="btn btn-outline-secondary w-100 nominal-btn" data-value="500000">Rp500.000</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Script untuk isi otomatis -->
<script>
    const nominalInput = document.getElementById("nominalInput");
    const btnSubmit = document.getElementById("btnSubmit");
    const nominalButtons = document.querySelectorAll(".nominal-btn");

    // Fungsi update tombol submit
    function updateSubmitButton() {
        if (nominalInput.value && nominalInput.value > 0) {
            btnSubmit.classList.remove("btn-secondary");
            btnSubmit.classList.add("btn-danger");
            btnSubmit.disabled = false;
        } else {
            btnSubmit.classList.remove("btn-danger");
            btnSubmit.classList.add("btn-secondary");
            btnSubmit.disabled = true;
        }
    }

    // Event input manual
    nominalInput.addEventListener("input", () => {
        // Reset highlight tombol jika input manual
        nominalButtons.forEach(btn => btn.classList.remove("btn-danger"));
        nominalButtons.forEach(btn => btn.classList.add("btn-outline-secondary"));
        updateSubmitButton();
    });

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

    // Event klik tombol nominal
    nominalButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            // Hapus highlight dari semua tombol
            nominalButtons.forEach(b => {
                b.classList.remove("btn-danger");
                b.classList.add("btn-outline-secondary");
            });

            // Aktifkan tombol yang diklik
            btn.classList.remove("btn-outline-secondary");
            btn.classList.add("btn-danger");

            // Isi input nominal
            nominalInput.value = btn.getAttribute("data-value");

            updateSubmitButton();
        });
    });

    $("#form").on("submit", function(e) {
        e.preventDefault();

        let form = $(this);
        let formData = form.serialize();

        // Ambil nilai nominal dari input
        let nominal = $("input[name='nominal']").val();

        Swal.fire({
            title: 'Konfirmasi Top Up',
            text: `Apakah Anda yakin ingin top up sebesar Rp${parseInt(nominal).toLocaleString('id-ID')} ?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Top Up',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url('actiontopup') ?>",
                    type: "POST",
                    data: formData,
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