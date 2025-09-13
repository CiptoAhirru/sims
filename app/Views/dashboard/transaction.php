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
    <h6 class="mb-3">PemBayaran</h6>
    <a class="navbar-brand fw-bold text-black d-flex align-items-center" href="#">
        <img src="<?= base_url('assets/images/Listrik.png') ?>"
            width="60" height="60" alt="listrik"> Litrik Prabyar
    </a>
    <div class="row g-3">
        <!-- Daftar Transaksi -->
        <div class="transaction-list" id="transactionList">
            <input type="hidden" id="limit" name="limit" value="<?= $transactions['limit'] ?>">
            <?php foreach ($transactions['records'] as $index => $transaction): ?>
                <div class="transaction-item d-flex align-items-start">
                    <div class="transaction-icon <?= $transaction['transaction_type'] == 'TOPUP' ? 'icon-income' : 'icon-expense' ?>">
                        <i class="bi <?= $transaction['transaction_type'] == 'TOPUP' ? 'bi-arrow-down-left' : 'bi-arrow-up-right' ?>"></i>
                    </div>
                    <div class="transaction-details">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="transaction-amount <?= $transaction['transaction_type'] == 'TOPUP' ? 'income' : 'expense' ?>">
                                <?= $transaction['transaction_type'] == 'TOPUP' ? '+' : '-' ?> Rp<?= number_format($transaction['total_amount'], 0, ',', '.') ?>
                            </div>
                            <div class="transaction-description">
                                <?= $transaction['description'] ?>
                            </div>
                        </div>
                        <div class="transaction-date">
                            <?= date('Y-m-d H:i:s', strtotime($transaction['created_on']))  ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Tombol Show More -->

        </div>
        <div class="show-more">
            <button type="button" id="showmore">
                <span>Show more</span>
            </button>
        </div>
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

    document.addEventListener('DOMContentLoaded', function() {
        const showMoreBtn = document.getElementById('showmore');
        const transactionList = document.getElementById('transactionList');
        let currentPage = document.getElementById('limit').value;
        let isLoading = false;

        showMoreBtn.addEventListener('click', function() {
            if (isLoading) return;
            console.log('jos')

            loadMoreTransactions();
        });

        function loadMoreTransactions() {
            isLoading = true;
            showMoreBtn.classList.add('loading');
            showMoreBtn.innerHTML = '<div class="loading-spinner"></div>Loading...';
            showMoreBtn.disabled = true;

            fetch(`/transactions/loadMore?page=${currentPage}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        transactionList.insertAdjacentHTML('beforeend', data.html);
                        currentPage = data.limit;

                        resetButton();
                    } else if (data.status === 'end') {
                        showMoreBtn.innerHTML = '<span>Tidak ada data lagi</span>';
                        showMoreBtn.disabled = true;
                        showMoreBtn.classList.remove('loading');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMoreBtn.innerHTML = '<span>Error, coba lagi</span>';
                    setTimeout(() => {
                        resetButton();
                    }, 2000);
                })
                .finally(() => {
                    isLoading = false;
                });
        }

        function resetButton() {
            showMoreBtn.innerHTML = '<span>Show more</span>';
            showMoreBtn.disabled = false;
            showMoreBtn.classList.remove('loading');
        }

        // Infinite scroll (opsional)
        // window.addEventListener('scroll', function() {
        //     if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 100 && !isLoading) {
        //         loadMoreTransactions();
        //     }
        // });
    });
</script>
<?= $this->endSection() ?>