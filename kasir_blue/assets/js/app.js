function showToast(type, message) {
    const toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) return;

    const toastEl = document.createElement('div');
    toastEl.className = `toast align-items-center text-bg-${type} border-0 show`;
    toastEl.role = 'alert';
    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" aria-label="Close"></button>
        </div>
    `;
    toastContainer.appendChild(toastEl);
    toastEl.querySelector('.btn-close').addEventListener('click', () => toastEl.remove());
    setTimeout(() => { if (toastEl.parentNode) toastEl.parentNode.removeChild(toastEl); }, 4500);
}

function formatRupiah(number) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
}

function filterCards(inputId, cardContainerSelector) {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.addEventListener('input', function () {
        const query = this.value.toLowerCase();
        document.querySelectorAll(cardContainerSelector).forEach(card => {
            const text = card.textContent.toLowerCase();
            card.style.display = text.includes(query) ? 'block' : 'none';
        });
    });
}

function toggleQrisDetails() {
    const method = document.querySelector('input[name="payment_method"]:checked');
    const qrisSection = document.getElementById('qrisSection');
    if (!method || !qrisSection) return;
    qrisSection.style.display = method.value === 'QRIS' ? 'block' : 'none';
}

window.addEventListener('DOMContentLoaded', function () {
    filterCards('productSearch', '.product-card');
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    paymentRadios.forEach(radio => radio.addEventListener('change', toggleQrisDetails));
    toggleQrisDetails();
});
