@extends('layout')

@section('content')
<div class="container mt-4">
    <div id="barang-data" data-barangs='{!! json_encode($barangs->keyBy('id')) !!}' style="display: none;"></div>
    <div class="row justify-content-center">

        <h4 class="fw-bold card-title"><i class="fas fa-box-open me-2"></i>Input Barang Masuk (Restock)</h4>
        <p class="card-subtitle mb-4 text-muted">Scan barcode untuk input cepat.</p>

        <form action="{{ route('incoming_transactions.store') }}" method="POST" id="incoming-form">
            @csrf

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Supplier / Sumber Barang <span class="text-danger">*</span></label>
                    <input type="text" name="supplier" class="form-control" placeholder="Contoh: PT. Gudang Garam / Pasar Induk" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Masuk</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="alert alert-primary d-flex align-items-center p-3 mb-4" role="alert">
                <div class="me-3"><i class="fas fa-barcode fa-3x"></i></div>
                <div class="flex-grow-1">
                    <h5 class="alert-heading fw-bold mb-1">Scan Barcode Disini</h5>
                    <input type="text" id="barcode-input" class="form-control form-control-lg border-primary fw-bold"
                           placeholder="Scan barang (Otomatis Masuk List)..." autofocus autocomplete="off">
                    <small id="scan-status" class="text-muted mt-1 d-block">Siap scan...</small>
                </div>
            </div>

            <div class="row g-2 align-items-end mb-4 p-3 bg-light rounded border">
                <div class="col-md-5">
                    <label class="form-label">Cari Manual (Jika Barcode Rusak)</label>
                    <select id="barang-selector" class="form-select"></select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jumlah Masuk</label>
                    <input type="number" id="jumlah-input" class="form-control" min="1" placeholder="Qty">
                </div>
                <div class="col-md-4 d-grid">
                    <button type="button" id="add-to-cart-btn" class="btn btn-success" disabled>
                        <i class="fas fa-plus-circle me-2"></i>Tambah ke List
                    </button>
                </div>
            </div>

            <h5 class="mb-3">Daftar Barang yang Akan Masuk</h5>
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-success">
                        <tr>
                            <th>Barang</th>
                            <th class="text-center" width="20%">Harga Satuan (Rp)</th>
                            <th class="text-center" width="15%">Jumlah</th>
                            <th class="text-center">Stok Awal</th>
                            <th class="text-center">Stok Akhir (Estimasi)</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="cart-items-body">
                        <tr id="empty-row">
                            <td colspan="6" class="text-center text-muted py-4">Belum ada barang di list</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" id="save-btn" class="btn btn-primary btn-lg" disabled>
                    <i class="fas fa-save me-2"></i>Simpan Data Barang Masuk
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- Inisialisasi Variabel ---
    const barcodeInput = $('#barcode-input');
    const scanStatus = document.getElementById('scan-status');
    const barangSelector = $('#barang-selector');
    const jumlahInput = $('#jumlah-input');
    const addBtn = $('#add-to-cart-btn');
    const cartBody = $('#cart-items-body');
    const saveBtn = $('#save-btn');
    const emptyRow = $('#empty-row');

    let itemIndex = 0;
    let typingTimer;
    const doneTypingInterval = 500;

    // 1. Fokus Otomatis
    barcodeInput.focus();

    // --- LOGIKA SCANNER (AUTO DETECT) ---
    barcodeInput.on('input', function() {
        clearTimeout(typingTimer);
        scanStatus.innerText = 'Sedang memindai...';

        if ($(this).val()) {
            typingTimer = setTimeout(processBarcode, doneTypingInterval);
        }
    });

    barcodeInput.on('keypress', function(e) {
        if (e.which == 13) { // Enter
            e.preventDefault();
            clearTimeout(typingTimer);
            processBarcode();
        }
    });

    function processBarcode() {
        let kode = barcodeInput.val().trim();
        if(!kode) return;

        scanStatus.innerHTML = '<span class="text-warning"><i class="fas fa-spinner fa-spin"></i> Mencari barang...</span>';

        $.ajax({
            url: '/api/cari-barang/' + kode,
            type: 'GET',
            success: function(res) {
                if(res.status == 'success') {
                    tambahKeList(res.data);
                    scanStatus.innerHTML = `<span class="text-success fw-bold"><i class="fas fa-check"></i> Berhasil: ${res.data.nama_barang}</span>`;
                    barcodeInput.val('').focus();
                }
            },
            error: function() {
                scanStatus.innerHTML = `<span class="text-danger fw-bold"><i class="fas fa-times"></i> Barang tidak ditemukan!</span>`;
                barcodeInput.select();
            }
        });
    }

    // --- LOGIKA MANUAL SELECT2 ---
    const barangData = JSON.parse(document.getElementById('barang-data').dataset.barangs);
    const options = Object.values(barangData).map(b => ({
        id: b.id,
        text: `${b.kode_barang} - ${b.nama_barang}`,
        stok: b.stok,
        nama: b.nama_barang,
        kode: b.kode_barang,
        harga: b.harga // Ambil data harga
    }));

    barangSelector.select2({
        data: options, theme: 'bootstrap-5', placeholder: 'Pilih barang...', width: '100%',
        matcher: function(params, data) {
            if ($.trim(params.term) === '') return data;
            if (typeof data.text === 'undefined') return null;
            if (data.kode.toLowerCase().includes(params.term.toLowerCase()) ||
                data.nama.toLowerCase().includes(params.term.toLowerCase())) return data;
            return null;
        }
    }).val(null).trigger('change');

    function validateManual() {
        const sel = barangSelector.select2('data')[0];
        const qty = parseInt(jumlahInput.val());
        addBtn.prop('disabled', (!sel || !sel.id || isNaN(qty) || qty <= 0));
    }
    barangSelector.on('select2:select', () => { validateManual(); jumlahInput.focus(); });
    jumlahInput.on('input', validateManual);

    addBtn.on('click', function() {
        const sel = barangSelector.select2('data')[0];
        const qty = parseInt(jumlahInput.val());

        let barangObj = {
            id: sel.id,
            nama_barang: sel.nama,
            kode_barang: sel.kode,
            stok: sel.stok,
            harga: sel.harga // Pass harga
        };
        tambahKeList(barangObj, qty);

        barangSelector.val(null).trigger('change');
        jumlahInput.val('');
        validateManual();
        barcodeInput.focus();
    });

    // --- FUNGSI UTAMA TAMBAH LIST ---
    function tambahKeList(barang, jumlah = 1) {
        emptyRow.hide();

        let existing = cartBody.find(`tr[data-id="${barang.id}"]`);

        if(existing.length > 0) {
            // Update Qty jika barang sudah ada
            let qtyInput = existing.find('.qty-input');
            let newQty = parseInt(qtyInput.val()) + jumlah;
            qtyInput.val(newQty).trigger('change'); // Trigger change untuk update stok akhir

            // Efek visual
            existing.addClass('table-warning');
            setTimeout(() => existing.removeClass('table-warning'), 500);

        } else {
            // Buat Baris Baru dengan Input Harga & Qty yang Editable
            let row = `
                <tr data-id="${barang.id}">
                    <td>
                        <div class="fw-bold">${barang.nama_barang}</div>
                        <small class="text-muted">${barang.kode_barang}</small>
                        <input type="hidden" name="items[${itemIndex}][barang_id]" value="${barang.id}">
                        <input type="hidden" class="stok-awal" value="${barang.stok}">
                    </td>
                    <td>
                        <input type="number" name="items[${itemIndex}][harga]" class="form-control harga-input"
                               value="${barang.harga}" min="0" step="100">
                    </td>
                    <td class="text-center">
                        <input type="number" name="items[${itemIndex}][jumlah]" class="form-control text-center qty-input"
                               value="${jumlah}" min="1">
                    </td>
                    <td class="text-center">${barang.stok}</td>
                    <td class="text-center fw-bold text-success stok-akhir">${barang.stok + jumlah}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger del-btn"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
            cartBody.prepend(row);
            itemIndex++;
        }
        checkSave();
    }

    // --- Event Listener untuk Perubahan di Tabel (Delegation) ---
    // Update Stok Akhir saat Qty diedit
    cartBody.on('input change', '.qty-input', function() {
        let row = $(this).closest('tr');
        let stokAwal = parseInt(row.find('.stok-awal').val()) || 0;
        let inputVal = parseInt($(this).val()) || 0;

        // Update tampilan Stok Akhir
        row.find('.stok-akhir').text(stokAwal + inputVal);
    });

    // Hapus Item
    cartBody.on('click', '.del-btn', function() {
        $(this).closest('tr').remove();
        if(cartBody.find('tr').length <= 1) emptyRow.show();
        checkSave();
        barcodeInput.focus();
    });

    function checkSave() {
        saveBtn.prop('disabled', cartBody.find('tr').not('#empty-row').length === 0);
    }

    function showSimpleNotification(msg, type) {
        let div = $(`<div class="alert alert-${type} position-fixed top-0 start-50 translate-middle-x mt-3 shadow" style="z-index:2000">${msg}</div>`);
        $('body').append(div);
        setTimeout(() => div.fadeOut(() => div.remove()), 2000);
    }

    // Notifikasi dari session
    @if (session('success')) showSimpleNotification("{{ session('success') }}", 'success'); @endif
    @if (session('error')) showSimpleNotification("{{ session('error') }}", 'danger'); @endif
});
</script>
@endpush
