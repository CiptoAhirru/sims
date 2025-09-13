<?= $this->extend('dashboard/layout') ?>

<?= $this->section('title') ?>
SIMS PPOB - Profile
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- app/Views/dashboard/account.php -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">

            <!-- Foto Profil -->
            <div class="position-relative d-inline-block mb-3">
                <img src="<?= esc($profile['profile_image'] ?? base_url('assets/images/Profile Photo.png')) ?>"
                    class="rounded-circle border"
                    style="width: 120px; height: 120px; object-fit: cover;"
                    alt="Profile Image">
                <button class="btn btn-light position-absolute bottom-0 end-0 p-1 border rounded-circle" style="transform: translate(25%, 25%);">
                    <i class="bi bi-pencil-fill"></i>
                </button>
            </div>

            <!-- Nama -->
            <h3 class="fw-bold mb-4"><?= esc($profile['first_name'] ?? '') ?> <?= esc($profile['last_name'] ?? '') ?></h3>

            <!-- Form Profil -->
            <form id="form">
                <?= csrf_field() ?>
                <div class="mb-3 text-start">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" class="form-control" id="email" name="email"
                            value="<?= esc($email ?? '') ?>" readonly>
                    </div>
                </div>

                <div class="mb-3 text-start">
                    <label for="first_name" class="form-label">Nama Depan</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" class="form-control" id="first_name" name="first_name"
                            value="<?= esc($first_name ?? '') ?>">
                    </div>
                </div>

                <div class="mb-4 text-start">
                    <label for="last_name" class="form-label">Nama Belakang</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" class="form-control" id="last_name" name="last_name"
                            value="<?= esc($last_name ?? '') ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-danger w-100">Edit</button>
            </form>
            <form id="logout">
                <?= csrf_field() ?>
                <button type="submit" class="mt-5 btn btn-outline-danger w-100">logout</button>
            </form>

        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $("#form").on("submit", function(e) {
        e.preventDefault();

        let form = $(this);
        let formData = form.serialize();

        $.ajax({
            url: "<?= base_url('edit-profile') ?>",
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

    $("#logout").on("submit", function(e) {
        e.preventDefault();

        let form = $(this);
        let formData = form.serialize();

        Swal.fire({
            title: 'Konfirmasi Top Up',
            text: `Apakah Anda yakin ingin logout ?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Top Up',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url('logout') ?>",
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    success: function(res) {
                        if (res.status === "success") {
                            Swal.fire({
                                icon: 'success',
                                title: 'Logout',
                                text: res.message,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = res.redirect;
                                }
                            });
                        } else {
                            Swal.fire("Oops...", "Logout gagal!", "error");
                        }
                    },
                    error: function() {
                        Swal.fire("Error", "Terjadi kesalahan server.", "error");
                    }
                });
            }
        });
    });
</script>
<?= $this->endSection() ?>