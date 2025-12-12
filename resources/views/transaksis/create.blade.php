@extends('layout')

@section('content')
<div class="container mt-4">
    <div id="barang-data" data-barangs='{!! json_encode($barangs->keyBy('id')) !!}' style="display: none;"></div>

    <div class="row">
        <div class="col-lg-8">
            <div class="shadow-sm">
                <div class="card-body">
                    <h4 class="fw-bold card-title"><i class="fas fa-cash-register me-2"></i>Kasir (Point of Sales)</h4>
                    <p class="card-subtitle mb-4 text-muted">Scan barcode untuk input cepat, atau cari manual.</p>

                    <div class="alert alert-primary d-flex align-items-center p-3" role="alert">
                        <div class="me-3"><i class="fas fa-barcode fa-3x"></i></div>
                        <div class="flex-grow-1">
                            <h5 class="alert-heading fw-bold mb-1">Scan Barcode Disini</h5>
                            <input type="text" id="scan-checker" class="form-control form-control-lg border-primary fw-bold"
                                   placeholder="Scan barang di sini (Otomatis Masuk Keranjang)..." autofocus autocomplete="off">
                            <small id="scan-status" class="text-muted mt-1 d-block">Siap scan...</small>
                        </div>
                    </div>



                    <div class="row g-2 mb-3 align-items-center p-3 bg-light rounded border">
                        <div class="col-md-5">
                            <label for="barang-selector" class="form-label">Cari Manual (Tanpa Barcode)</label>
                            <select id="barang-selector" class="form-select"></select>
                        </div>
                        <div class="col-md-4">
                            <label for="jumlah-input" class="form-label">Jumlah</label>
                            <input type="number" id="jumlah-input" class="form-control" min="1" placeholder="Qty">
                        </div>
                        <div class="col-md-3 d-grid">
                             <label class="form-label d-block">&nbsp;</label>
                            <button type="button" id="add-to-cart-btn" class="btn btn-success" disabled>
                                <i class="fas fa-plus me-2"></i>Tambah
                            </button>
                        </div>
                        <div class="col-12">
                             <small id="stock-info" class="text-danger d-block" style="display:none;"></small>
                        </div>
                    </div>

                    <h5 class="mt-4">Keranjang Belanja</h5>
                    <form action="{{ route('transaksis.store') }}" method="POST" id="transaction-form">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Barang</th>
                                        <th class="text-center" width="15%">Jumlah</th>
                                        <th class="text-end">Subtotal</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items-body">
                                    </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="shadow-sm border-primary" style="position: sticky; top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="total-harga" class="form-label fw-bold">Total Tagihan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">Rp</span>
                            <input type="text" id="total-harga" class="form-control form-control-lg text-end fw-bold text-primary" value="0" readonly style="font-size: 1.8rem;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="pembayaran" class="form-label">Uang Pembeli</label>
                         <div class="input-group input-group-lg">
                            <span class="input-group-text">Rp</span>
                            <input type="number" id="pembayaran" class="form-control text-end" placeholder="0">
                        </div>
                        <small id="pembayaran-warning" class="text-danger mt-1 d-block fw-bold" style="display: none;"></small>
                    </div>
                    <div class="mb-4">
                        <label for="kembalian" class="form-label">Kembalian</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-success text-white">Rp</span>
                            <input type="text" id="kembalian" class="form-control text-end fw-bold text-success" value="0" readonly>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" form="transaction-form" id="simpan-transaksi-btn" class="btn btn-primary btn-lg py-3" disabled>
                            <i class="fas fa-save me-2"></i>PROSES TRANSAKSI
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- SETUP VARIABEL ---
    const scannerInput = document.getElementById('scan-checker');
    const scanStatus = document.getElementById('scan-status');
    const barangSelector = $('#barang-selector');
    const jumlahInput = $('#jumlah-input');
    const addToCartBtn = $('#add-to-cart-btn');
    const cartItemsBody = $('#cart-items-body');
    const stockInfo = $('#stock-info');
    const totalHargaEl = $('#total-harga');
    const pembayaranEl = $('#pembayaran');
    const kembalianEl = $('#kembalian');
    const simpanBtn = $('#simpan-transaksi-btn');
    const pembayaranWarning = $('#pembayaran-warning');

    let itemIndex = 0;
    let typingTimer;
    const doneTypingInterval = 500; // Jeda 0.5 detik (Debounce)

    // Fokus awal ke scanner
    scannerInput.focus();

    // --- 1. LOGIKA SCANNER (AUTO-PROCESS) ---
    scannerInput.addEventListener('input', function() {
        clearTimeout(typingTimer);
        scanStatus.innerText = 'Sedang memindai...';

        if (scannerInput.value) {
            typingTimer = setTimeout(processBarcode, doneTypingInterval);
        }
    });

    scannerInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(typingTimer);
            processBarcode();
        }
    });

    function processBarcode() {
        let kode = scannerInput.value.trim();
        if (!kode) return;

        scanStatus.innerHTML = '<span class="text-warning"><i class="fas fa-spinner fa-spin"></i> Mencari barang...</span>';

        // Panggil API
        fetch('/api/cari-barang/' + kode)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Barang Ditemukan -> Masukkan Keranjang
                    addToCart(data.data, 1);

                    scanStatus.innerHTML = `<span class="text-success fw-bold"><i class="fas fa-check"></i> Berhasil: ${data.data.nama_barang}</span>`;
                    scannerInput.value = ''; // Kosongkan input
                    scannerInput.focus();    // Fokus ulang
                } else {
                    scanStatus.innerHTML = `<span class="text-danger fw-bold"><i class="fas fa-times"></i> Barang tidak ditemukan!</span>`;
                    scannerInput.select();   // Block teks biar mudah diganti
                }
            })
            .catch(err => {
                console.error(err);
                scanStatus.innerHTML = '<span class="text-danger">Error koneksi server.</span>';
            });
    }

    // --- 2. LOGIKA KERANJANG (Shared Function) ---
    function addToCart(barang, jumlah) {
        // Cek Stok
        if (jumlah > barang.stok) {
            showSimpleNotification(`Stok tidak cukup! Sisa: ${barang.stok}`, 'danger');
            return;
        }

        // Cek apakah barang sudah ada di keranjang
        let existingRow = cartItemsBody.find(`tr[data-id="${barang.id}"]`);

        if (existingRow.length > 0) {
            // Jika ada, UPDATE QTY
            let qtyInput = existingRow.find('.qty-input');
            let qtyDisplay = existingRow.find('.qty-display'); // Cari elemen teks qty
            let currentQty = parseInt(qtyInput.val());
            let newQty = currentQty + jumlah;

            if (newQty > barang.stok) {
                showSimpleNotification(`Mencapai batas stok! Sisa: ${barang.stok}`, 'warning');
                return;
            }

            qtyInput.val(newQty);
            // Update teks jumlah di tabel (karena di HTML bawah kita pakai class qty-display)
            existingRow.find('td:nth-child(2)').contents().filter(function(){ return this.nodeType == 3; }).first().replaceWith(newQty + " ");

            // Update Subtotal
            let newSubtotal = barang.harga * newQty;
            existingRow.data('subtotal', newSubtotal);
            existingRow.find('.text-end').text(formatRupiah(newSubtotal));

        } else {
            // Jika belum, TAMBAH BARIS BARU
            let subtotal = barang.harga * jumlah;
            let newRow = `
                <tr data-id="${barang.id}" data-subtotal="${subtotal}">
                    <td>
                        <div class="fw-bold">${barang.nama_barang}</div>
                        <small class="text-muted">${barang.kode_barang}</small>
                        <input type="hidden" name="items[${itemIndex}][barang_id]" value="${barang.id}">
                    </td>
                    <td class="text-center">
                        ${jumlah}
                        <input type="hidden" name="items[${itemIndex}][jumlah]" class="qty-input" value="${jumlah}">
                    </td>
                    <td class="text-end">${formatRupiah(subtotal)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>`;
            cartItemsBody.prepend(newRow); // Tambah di atas
            itemIndex++;
        }

        calculateTotal();
    }

    // --- 3. LOGIKA INPUT MANUAL (Select2) ---
    const barangDataEl = document.getElementById('barang-data');
    const barangsRaw = JSON.parse(barangDataEl.dataset.barangs);
    const barangOptions = Object.values(barangsRaw).map(b => ({
        id: b.id,
        text: `${b.kode_barang} - ${b.nama_barang} (Stok: ${b.stok})`,
        stok: b.stok, harga: b.harga,
        kode_barang: b.kode_barang, nama_barang: b.nama_barang
    }));

    barangSelector.select2({
        data: barangOptions, theme: 'bootstrap-5', placeholder: 'Ketik nama barang...', width: '100%',
        matcher: function(params, data) {
            if ($.trim(params.term) === '') return data;
            if (typeof data.text === 'undefined') return null;
            if (data.kode_barang.toLowerCase().includes(params.term.toLowerCase()) ||
                data.nama_barang.toLowerCase().includes(params.term.toLowerCase())) return data;
            return null;
        }
    }).val(null).trigger('change');

    // Tombol Tambah Manual
    addToCartBtn.on('click', function() {
        const sel = barangSelector.select2('data')[0];
        const qty = parseInt(jumlahInput.val());

        // Panggil fungsi shared
        addToCart(sel, qty);

        // Reset Form Manual
        barangSelector.val(null).trigger('change');
        jumlahInput.val('');
        validateInput();
        scannerInput.focus(); // Kembalikan fokus ke scanner
    });

    // Validasi Manual Input
    function validateInput() {
        const sel = barangSelector.select2('data')[0];
        const qty = parseInt(jumlahInput.val());
        let isValid = true;
        stockInfo.hide();

        if (!sel || !sel.id || isNaN(qty) || qty <= 0) isValid = false;
        else if (qty > sel.stok) {
            stockInfo.text(`Stok kurang! (${sel.stok})`).show();
            isValid = false;
        }
        addToCartBtn.prop('disabled', !isValid);
    }
    barangSelector.on('select2:select', () => { validateInput(); jumlahInput.focus(); });
    jumlahInput.on('input', validateInput);


    // --- 4. KALKULASI & UTILITIES ---
    cartItemsBody.on('click', '.remove-item-btn', function() {
        $(this).closest('tr').remove();
        calculateTotal();
        scannerInput.focus();
    });

    function calculateTotal() {
        let total = 0;
        cartItemsBody.find('tr').each(function() {
            total += parseFloat($(this).data('subtotal'));
        });

        totalHargaEl.val(formatRupiah(total, false)); // false = tanpa Rp di value input
        calculateChange();
        validateSaveButton(total);
    }

    function calculateChange() {
        const total = parseFloat(totalHargaEl.val().replace(/\./g, '')) || 0;
        const bayar = parseFloat(pembayaranEl.val()) || 0;
        const kembali = bayar - total;

        if (kembali >= 0) {
            kembalianEl.val(formatRupiah(kembali, false));
            pembayaranWarning.hide();
        } else {
            kembalianEl.val('0');
            pembayaranWarning.text('Kurang: ' + formatRupiah(Math.abs(kembali))).show();
        }
        validateSaveButton(total, bayar);
    }
    pembayaranEl.on('input', calculateChange);

    function validateSaveButton(total, bayar) {
        const hasItem = cartItemsBody.find('tr').length > 0;
        if(typeof total === 'undefined') total = parseFloat(totalHargaEl.val().replace(/\./g, '')) || 0;
        if(typeof bayar === 'undefined') bayar = parseFloat(pembayaranEl.val()) || 0;

        simpanBtn.prop('disabled', !(hasItem && bayar >= total));
    }

    // Helper Format Rupiah
    function formatRupiah(angka, prefix = 'Rp ') {
        let number_string = angka.toString().replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return prefix === false ? rupiah : (prefix + rupiah);
    }

    function showSimpleNotification(msg, type) {
        let div = $(`<div class="alert alert-${type} position-fixed top-0 start-50 translate-middle-x mt-3 shadow" style="z-index:2000">${msg}</div>`);
        $('body').append(div);
        setTimeout(() => div.fadeOut(() => div.remove()), 2500);
    }

    // Notifikasi dari session
    @if (session('success')) showSimpleNotification("{{ session('success') }}", 'success'); @endif
});
</script>
@endpush
