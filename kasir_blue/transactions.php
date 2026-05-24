<?php
require_once __DIR__ . '/inc/auth.php';
ensure_login();
$page_title = 'Transaksi';
$pdo = pdo();
$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$products = $pdo->query('SELECT p.*, c.name AS kategori FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.stok > 0 ORDER BY p.id DESC')->fetchAll();
$qrisImage = get_setting('qris_image');
$qrisImageUrl = $qrisImage ? 'uploads/qris/' . basename($qrisImage) : null;
?>
<?php require_once __DIR__ . '/inc/header.php'; ?>
<?php require_once __DIR__ . '/inc/navbar.php'; ?>
<div class="main-content container-fluid">
    <div class="topbar mb-4">
        <div>
            <h1 class="h3 mb-1">Transaksi Kasir</h1>
            <p class="text-muted">Tambahkan produk ke keranjang, pilih metode pembayaran, dan cetak struk otomatis.</p>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card card-glass p-4 shadow-soft mb-4">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-3">
                    <div>
                        <h2 class="h5 mb-1">Katalog Produk</h2>
                        <p class="text-muted mb-0">Cari dan pilih menu untuk ditambahkan ke keranjang.</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <input id="productSearch" class="form-control form-control-sm" type="text" placeholder="Cari produk ...">
                        <select id="categoryFilter" class="form-select form-select-sm">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['name']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row g-3" id="productCatalog">
                    <?php foreach ($products as $product): ?>
                        <div class="col-sm-6 col-lg-4 product-card" data-name="<?= strtolower($product['nama_produk']) ?>" data-category="<?= strtolower($product['kategori']) ?>">
                            <div class="card product-card h-100 shadow-soft">
                                <img src="<?= build_product_image($product['gambar_produk']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['nama_produk']) ?>">
                                <div class="card-body d-flex flex-column">
                                    <span class="badge badge-soft mb-2"><?= htmlspecialchars($product['kategori']) ?></span>
                                    <h5 class="card-title mb-2"><?= htmlspecialchars($product['nama_produk']) ?></h5>
                                    <p class="card-text text-muted mb-3">Stok <?= $product['stok'] ?> <?= htmlspecialchars($product['unit']) ?></p>
                                    <?php if ($product['kategori'] === 'Makanan' && !empty($product['level_pedas'])): ?>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Level Pedas</small>
                                            <span class="badge bg-<?= $product['level_pedas'] === 'Pedas' ? 'danger' : 'warning' ?>">
                                                <i class="fa-solid fa-fire me-1"></i><?= htmlspecialchars($product['level_pedas'])?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <strong><?= format_rp($product['harga_jual']) ?></strong>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <div class="input-group input-group-sm flex-grow-1">
                                                <button class="btn btn-outline-secondary qty-minus" type="button" data-pid="<?= $product['id'] ?>">−</button>
                                                <input type="number" class="form-control form-control-sm qty-input" value="1" min="1" max="<?= $product['stok'] ?>" data-pid="<?= $product['id'] ?>" style="text-align:center;">
                                                <button class="btn btn-outline-secondary qty-plus" type="button" data-pid="<?= $product['id'] ?>" data-max="<?= $product['stok'] ?>">+</button>
                                            </div>
                                            <button class="btn btn-sm btn-primary add-to-cart flex-shrink-0" data-id="<?= $product['id'] ?>" data-name="<?= htmlspecialchars($product['nama_produk']) ?>" data-price="<?= $product['harga_jual'] ?>" data-max="<?= $product['stok'] ?>"><i class="fa-solid fa-cart-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($products)): ?>
                        <div class="col-12">
                            <div class="card card-glass p-4 text-center">
                                <p class="mb-0 text-muted">Tidak ada produk tersedia. Tambahkan produk terlebih dahulu.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card card-glass p-4 shadow-soft mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h2 class="h5 mb-0">Keranjang Belanja</h2>
                    <span id="itemCount" class="badge bg-primary">0 item</span>
                </div>
                <div class="table-responsive mb-3" style="max-height: 320px; overflow:auto;">
                    <table class="table table-borderless align-middle mb-0">
                        <tbody id="cartBody"></tbody>
                    </table>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><strong id="subtotalText">Rp 0</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>Pajak (10%)</span><strong id="taxText">Rp 0</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>Diskon</span><strong id="discountText">Rp 0</strong></div>
                    <hr>
                    <div class="d-flex justify-content-between"><span>Total Bayar</span><strong id="grandTotalText">Rp 0</strong></div>
                </div>
                <form id="transactionForm" method="post" action="transaction_save.php">
                    <input type="hidden" name="cart_data" id="cartData">
                    <div class="mb-3">
                        <label class="form-label">Nama Pembeli</label>
                        <input type="text" name="buyer_name" class="form-control" placeholder="Masukkan nama pembeli (opsional)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Metode Pembayaran</label>
                        <select name="payment_method" class="form-select" onchange="toggleQris(this.value)" required>
                            <option value="CASH">Cash</option>
                            <option value="QRIS">QRIS</option>
                        </select>
                    </div>
                    <div id="qrisSection" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label">Tipe QRIS</label>
                            <select name="qris_type" class="form-select">
                                <option value="DANA">DANA</option>
                                <option value="OVO">OVO</option>
                                <option value="GoPay">GoPay</option>
                                <option value="ShopeePay">ShopeePay</option>
                            </select>
                        </div>
                        <div class="mb-3 text-center">
                            <?php if ($qrisImageUrl): ?>
                                <img src="<?= htmlspecialchars($qrisImageUrl) ?>" alt="QRIS" class="img-fluid rounded-4" style="max-height:220px;">
                            <?php else: ?>
                                <div class="bg-light rounded-4 p-4 text-center text-muted">Upload QRIS di Pengaturan untuk menampilkan QR.</div>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-center gap-2 mb-3 flex-wrap">
                            <span class="badge bg-info text-dark"><i class="fa-brands fa-cc-paypal me-1"></i> DANA</span>
                            <span class="badge bg-warning text-dark"><i class="fa-brands fa-cc-visa me-1"></i> OVO</span>
                            <span class="badge bg-primary"><i class="fa-brands fa-paypal me-1"></i> GoPay</span>
                            <span class="badge bg-danger"><i class="fa-solid fa-qrcode me-1"></i> ShopeePay</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Diskon (Rp)</label>
                        <input type="number" name="discount" id="discountInput" class="form-control" value="0" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bayar</label>
                        <input type="number" name="cash" id="cashInput" class="form-control" value="0" min="0" required>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Kembalian</span>
                            <strong id="changeText">Rp 0</strong>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Pembayaran Selesai</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="toastContainer" class="toast-container"></div>
<script>
const addButtons = document.querySelectorAll('.add-to-cart');
const cartBody = document.getElementById('cartBody');
const itemCount = document.getElementById('itemCount');
const subtotalText = document.getElementById('subtotalText');
const taxText = document.getElementById('taxText');
const discountText = document.getElementById('discountText');
const grandTotalText = document.getElementById('grandTotalText');
const changeText = document.getElementById('changeText');
const cartData = document.getElementById('cartData');
const discountInput = document.getElementById('discountInput');
const cashInput = document.getElementById('cashInput');
const searchInput = document.getElementById('productSearch');
const categoryFilter = document.getElementById('categoryFilter');
let cart = [];

// Qty increment/decrement handlers
document.querySelectorAll('.qty-plus').forEach(btn => {
    btn.addEventListener('click', function () {
        const pid = this.dataset.pid;
        const max = parseInt(this.dataset.max);
        const input = document.querySelector(`.qty-input[data-pid="${pid}"]`);
        const current = parseInt(input.value) || 1;
        if (current < max) {
            input.value = current + 1;
        }
    });
});

document.querySelectorAll('.qty-minus').forEach(btn => {
    btn.addEventListener('click', function () {
        const pid = this.dataset.pid;
        const input = document.querySelector(`.qty-input[data-pid="${pid}"]`);
        const current = parseInt(input.value) || 1;
        if (current > 1) {
            input.value = current - 1;
        }
    });
});

function formatRupiah(number) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
}

function updateCart() {
    cartBody.innerHTML = '';
    let subtotal = 0;
    cart.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><strong>${item.qty}</strong></td>
            <td>${item.name}</td>
            <td>${formatRupiah(item.price)}</td>
            <td>${formatRupiah(item.qty * item.price)}</td>
            <td>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-secondary cart-qty-minus" data-id="${item.id}">−</button>
                    <button type="button" class="btn btn-outline-secondary cart-qty-plus" data-id="${item.id}">+</button>
                    <button type="button" class="btn btn-outline-danger remove-item" data-id="${item.id}"><i class="fa-solid fa-trash"></i></button>
                </div>
            </td>
        `;
        cartBody.appendChild(row);
        subtotal += item.qty * item.price;
    });
    itemCount.textContent = `${cart.reduce((sum, item) => sum + item.qty, 0)} item`;
    const tax = Math.round(subtotal * 0.1);
    const discount = Number(discountInput.value) || 0;
    const total = Math.max(subtotal + tax - discount, 0);
    subtotalText.textContent = formatRupiah(subtotal);
    taxText.textContent = formatRupiah(tax);
    discountText.textContent = formatRupiah(discount);
    grandTotalText.textContent = formatRupiah(total);
    const cash = Number(cashInput.value) || 0;
    const change = Math.max(cash - total, 0);
    changeText.textContent = formatRupiah(change);
    cartData.value = JSON.stringify({ items: cart, subtotal, tax, discount, total, cash, payment_method: document.querySelector('select[name="payment_method"]').value, qris_type: document.querySelector('select[name="qris_type"]').value });
    
    // Attach event listeners untuk cart qty buttons
    document.querySelectorAll('.cart-qty-plus').forEach(btn => {
        btn.addEventListener('click', () => {
            const itemId = btn.dataset.id;
            const item = cart.find(i => i.id === itemId);
            if (item) item.qty += 1;
            updateCart();
        });
    });
    
    document.querySelectorAll('.cart-qty-minus').forEach(btn => {
        btn.addEventListener('click', () => {
            const itemId = btn.dataset.id;
            const item = cart.find(i => i.id === itemId);
            if (item && item.qty > 1) item.qty -= 1;
            updateCart();
        });
    });
    
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', () => {
            cart = cart.filter(item => item.id !== btn.dataset.id);
            updateCart();
        });
    });
}

function addItem(id, name, price, qty = 1) {
    qty = Math.max(1, qty);
    const existing = cart.find(item => item.id === id);
    if (existing) {
        existing.qty += qty;
    } else {
        cart.push({ id, name, price, qty });
    }
    updateCart();
}

addButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        const pid = btn.dataset.id;
        const qtyInput = document.querySelector(`.qty-input[data-pid="${pid}"]`);
        const qty = parseInt(qtyInput.value) || 1;
        addItem(btn.dataset.id, btn.dataset.name, Number(btn.dataset.price), qty);
        qtyInput.value = 1;
        showToast('success', 'Produk ditambahkan ke keranjang.');
    });
});

if (discountInput) discountInput.addEventListener('input', updateCart);
if (cashInput) cashInput.addEventListener('input', updateCart);

document.getElementById('transactionForm').addEventListener('submit', function (event) {
    if (cart.length === 0) {
        event.preventDefault();
        showToast('danger', 'Keranjang kosong. Tambahkan produk terlebih dahulu.');
        return;
    }
    updateCart();
});

function filterProducts() {
    const query = searchInput.value.toLowerCase();
    const category = categoryFilter.value.toLowerCase();
    document.querySelectorAll('#productCatalog .product-card').forEach(card => {
        const name = card.dataset.name;
        const cat = card.dataset.category;
        const match = name.includes(query) && (category === '' || cat === category);
        card.style.display = match ? 'block' : 'none';
    });
}
searchInput.addEventListener('input', filterProducts);
categoryFilter.addEventListener('change', filterProducts);

function toggleQris(value) {
    const section = document.getElementById('qrisSection');
    section.style.display = value === 'QRIS' ? 'block' : 'none';
}

updateCart();
</script>
<?php require_once __DIR__ . '/inc/footer.php'; ?>