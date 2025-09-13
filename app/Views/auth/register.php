<?= $this->extend('auth/layout') ?>

<?= $this->section('title') ?>
SIMS PPOB - Register
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="col-12 col-md-6 d-flex justify-content-center mb-4 mb-md-0">
    <div class="card shadow-sm p-4 border-0 w-100" style="max-width: 400px;">
        <div class="text-center mb-4">
            <div class="d-flex justify-content-center align-items-center mb-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                    style="width:40px; height:40px; background:#ef4444; color:white; font-weight:700">
                    B
                </div>
            </div>
            <h5 class="fw-bold">SIMS PPOB</h5>
            <p class="text-muted">Masuk atau buat akun untuk memulai</p>
        </div>

        <form id="form">
            <?= csrf_field() ?>
            <div class=" mb-3">
                <input type="email" class="form-control" name="email" id="email" placeholder="masukan email anda" required>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="namadepan" id="namadepan" placeholder="masukan email anda" required>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="namabelakang" id="namabelakang" placeholder="masukan email anda" required>
            </div>
            <div class="mb-3 position-relative">
                <input type="password" id="password" name="password"
                    class="form-control" placeholder="masukan password anda" required>
                <i class="bi bi-eye position-absolute top-50 end-0 translate-middle-y me-3"
                    id="togglePassword" style="cursor:pointer;"></i>
            </div>

            <div class="mb-3 position-relative">
                <input type="password" id="konfirmasipassword" name="konfirmasipassword"
                    class="form-control" placeholder="masukan konfirmasi password anda" required>
                <i class="bi bi-eye position-absolute top-50 end-0 translate-middle-y me-3"
                    id="toggleKonfirmasiPassword" style="cursor:pointer;"></i>
            </div>
            <div id="errorMessage" class="text-danger mb-3" style="display:none;"></div>
            <div class="d-grid">
                <button class="btn btn-danger">Daftar</button>
            </div>
        </form>

        <div class="mt-3 text-center">
            <small>Sudah punya akun? <a href="<?= base_url('/login') ?>" class="text-danger fw-bold">Login</a></small>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const password = document.getElementById("password");
    const konfirmasi = document.getElementById("konfirmasipassword");
    const errorMessage = document.getElementById("errorMessage");

    document.getElementById("togglePassword").addEventListener("click", function() {
        const passwordInput = document.getElementById("password");
        const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
        passwordInput.setAttribute("type", type);

        this.classList.toggle("bi-eye");
        this.classList.toggle("bi-eye-slash");
    });

    document.getElementById("toggleKonfirmasiPassword").addEventListener("click", function() {
        const passwordInput = document.getElementById("konfirmasipassword");
        const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
        passwordInput.setAttribute("type", type);

        this.classList.toggle("bi-eye");
        this.classList.toggle("bi-eye-slash");
    });

    function validatePassword() {
        if (konfirmasi.value.length > 0) {
            if (password.value !== konfirmasi.value) {
                errorMessage.textContent = "Password dan konfirmasi password tidak sama!";
                errorMessage.style.display = "block";
                konfirmasi.classList.add("is-invalid");
                errorMessage.classList.add("text-danger");
                konfirmasi.classList.remove("is-valid");
            } else {
                errorMessage.textContent = "Password cocok!";
                errorMessage.style.display = "block";
                errorMessage.classList.remove("text-danger");
                errorMessage.classList.add("text-success");
                konfirmasi.classList.remove("is-invalid");
                konfirmasi.classList.add("is-valid");
            }
        } else {
            errorMessage.style.display = "none";
            konfirmasi.classList.remove("is-invalid", "is-valid");
        }
    }

    password.addEventListener("input", validatePassword);
    konfirmasi.addEventListener("input", validatePassword);

    $("#form").on("submit", function(e) {
        e.preventDefault();

        let form = $(this);
        let formData = form.serialize();

        $.ajax({
            url: "<?= base_url('auth/registrasi') ?>",
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
    });
</script>
<?= $this->endSection() ?>