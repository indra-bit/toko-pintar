@extends('layout')

@section('content')
    <div class="container mt-4">
        <h2>Input Transaksi Masuk</h2>
        <p>Catat pembelian barang dari supplier untuk menambah stok inventaris.</p>

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Oops! Ada beberapa masalah dengan input Anda:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="incoming-transaction-form" action="{{ route('incoming_transactions.store') }}" method="POST">
            @csrf

            <div class="shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Informasi Transaksi</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="supplier_name" class="form-label">Nama Supplier:</label>
                            <input type="text" name="supplier_name" id="supplier_name" class="form-control" value="{{ old('supplier_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="reference_number" class="form-label">Nomor Referensi/Invoice (Opsional):</label>
                            <input type="text" name="reference_number" id="reference_number" class="form-control" value="{{ old('reference_number') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Catatan (Opsional):</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>


                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Detail Barang</h4>
                </div>
                <div class="card-body">
                    <div id="incoming-transaction-items-container">
                        {{-- Baris item akan ditambahkan oleh JavaScript --}}
                    </div>
                    <button type="button" id="add-item-incoming" class="btn btn-success mt-3" disabled><i class="fas fa-plus me-2"></i>Tambah Barang</button>
                </div>

            <hr>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan Transaksi Masuk</button>
            <a href="{{ route('incoming_transactions.index') }}" class="btn btn-secondary">Lihat Daftar Transaksi Masuk</a>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let itemIndex = 0;
        const barangs = @json($barangs); // Data barang dari controller
        const itemsContainer = document.getElementById('incoming-transaction-items-container');
        const addItemButton = document.getElementById('add-item-incoming');
        const supplierNameInput = document.getElementById('supplier_name');
        const referenceNumberInput = document.getElementById('reference_number');
        // Scope tombol submit hanya untuk form transaksi ini
        const form = document.getElementById('incoming-transaction-form');
        const scopedSubmit = form ? form.querySelector('button[type="submit"]') : null;

        // Fungsi untuk membuat dan menambahkan baris input barang baru
        function createNewRow() {
            const newRow = document.createElement('div');
            newRow.className = 'row incoming-item-row mb-3 align-items-end p-3 border rounded bg-light';

            let options = '<option value="">-- Pilih Barang --</option>';
            barangs.forEach(function(barang) {
                options += `<option value="${barang.id}" data-stok="${barang.stok}" data-kode-barang="${barang.kode_barang}">${barang.nama_barang} (Stok: ${barang.stok})</option>`;
            });

            newRow.innerHTML = `
                <div class="col-md-3">
                    <label for="barang_id_inc_${itemIndex}" class="form-label">Pilih Barang:</label>
                    <select name="items[${itemIndex}][barang_id]" id="barang_id_inc_${itemIndex}" class="form-select barang-select-incoming" required>
                        ${options}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Kode Barang:</label>
                    <input type="text" class="form-control kode-barang-display-incoming" readonly>
                </div>
                <div class="col-md-2">
                    <label for="quantity_inc_${itemIndex}" class="form-label">Jumlah Masuk:</label>
                    <input type="number" name="items[${itemIndex}][quantity]" id="quantity_inc_${itemIndex}" class="form-control quantity-input-incoming" min="1" required>
                    <small class="text-danger quantity-alert-incoming" style="display:none;"></small>
                </div>
                <div class="col-md-3">
                    <label for="unit_cost_inc_${itemIndex}" class="form-label">Harga Beli/Unit (Opsional):</label>
                    <input type="number" step="0.01" name="items[${itemIndex}][unit_cost]" id="unit_cost_inc_${itemIndex}" class="form-control unit-cost-input-incoming">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-item-incoming w-100">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            `;

            itemsContainer.appendChild(newRow);
            itemIndex++;

            newRow.querySelector('.barang-select-incoming').focus();
            toggleAddItemButton();
            toggleSubmitButton(); // Perbarui status tombol simpan
        }

        // Fungsi untuk mengaktifkan/menonaktifkan tombol "Tambah Barang"
        function toggleAddItemButton() {
            const lastRow = itemsContainer.querySelector('.incoming-item-row:last-child');
            if (!lastRow) {
                addItemButton.disabled = true;
                return;
            }

            const selectElement = lastRow.querySelector('.barang-select-incoming');
            const quantityElement = lastRow.querySelector('.quantity-input-incoming');
            const quantityAlertElement = lastRow.querySelector('.quantity-alert-incoming');

            const isSelectFilled = selectElement && selectElement.value !== '';
            const isQuantityFilled = quantityElement && quantityElement.value !== '' && parseInt(quantityElement.value) > 0;
            const noQuantityAlert = quantityAlertElement && quantityAlertElement.style.display === 'none';

            if (isSelectFilled && isQuantityFilled && noQuantityAlert) {
                addItemButton.disabled = false;
            } else {
                addItemButton.disabled = true;
            }
        }

        // Fungsi untuk memvalidasi input di baris item dan menampilkan kode barang
        function validateItemRow(row) {
            const selectElement = row.querySelector('.barang-select-incoming');
            const quantityElement = row.querySelector('.quantity-input-incoming');
            const quantityAlertElement = row.querySelector('.quantity-alert-incoming');
            const kodeBarangDisplay = row.querySelector('.kode-barang-display-incoming');

            quantityAlertElement.style.display = 'none';
            quantityElement.setCustomValidity('');

            // Tampilkan Kode Barang
            if (selectElement.value) {
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                kodeBarangDisplay.value = selectedOption.dataset.kodeBarang;
            } else {
                kodeBarangDisplay.value = '';
            }

            if (quantityElement.value && parseInt(quantityElement.value) <= 0) {
                quantityAlertElement.textContent = 'Jumlah harus lebih dari 0.';
                quantityAlertElement.style.display = 'block';
                quantityElement.setCustomValidity("Jumlah harus positif.");
            }

            toggleAddItemButton();
            toggleSubmitButton();
        }

        // Fungsi untuk mengaktifkan/menonaktifkan tombol submit form utama (scoped ke form ini)
        function toggleSubmitButton() {
            const submitButton = scopedSubmit;
            const allRows = itemsContainer.querySelectorAll('.incoming-item-row');
            const isSupplierFilled = supplierNameInput.value.trim() !== '';

            let allRowsValid = true;
            if (allRows.length === 0) { // Harus ada setidaknya satu baris barang
                allRowsValid = false;
            } else {
                allRows.forEach(row => {
                    const selectElement = row.querySelector('.barang-select-incoming');
                    const quantityElement = row.querySelector('.quantity-input-incoming');
                    const quantityAlertElement = row.querySelector('.quantity-alert-incoming');

                    if (!selectElement || selectElement.value === '' ||
                        !quantityElement || quantityElement.value === '' || parseInt(quantityElement.value) <= 0 ||
                        (quantityAlertElement && quantityAlertElement.style.display !== 'none')) {
                        allRowsValid = false;
                    }
                });
            }

            if (isSupplierFilled && allRowsValid) {
                submitButton.disabled = false;
            } else {
                submitButton.disabled = true;
            }
        }


        // --- Event Listeners ---

        // Tambahkan baris pertama saat halaman dimuat
        createNewRow();
        // Nonaktifkan tombol submit hanya untuk form transaksi saat halaman dimuat
        if (scopedSubmit) scopedSubmit.disabled = true;

        // Event listener untuk tombol "Tambah Barang"
        addItemButton.addEventListener('click', function () {
            createNewRow();
        });

        // Event listener untuk perubahan pada input supplier name dan reference number
        supplierNameInput.addEventListener('input', toggleSubmitButton);
        referenceNumberInput.addEventListener('input', toggleSubmitButton); // Opsional, jika reference_number juga jadi syarat submit

        // Event listener untuk perubahan pada input barang, jumlah, dan harga beli (delegasi event)
        itemsContainer.addEventListener('change', function (e) {
            if (e.target.classList.contains('barang-select-incoming') ||
                e.target.classList.contains('quantity-input-incoming') ||
                e.target.classList.contains('unit-cost-input-incoming')) {
                validateItemRow(e.target.closest('.incoming-item-row'));
            }
        });

        // Event listener untuk input (ketika user mengetik) pada jumlah dan harga beli
        itemsContainer.addEventListener('input', function (e) {
            if (e.target.classList.contains('quantity-input-incoming') ||
                e.target.classList.contains('unit-cost-input-incoming')) {
                validateItemRow(e.target.closest('.incoming-item-row'));
            }
        });

        // Event listener untuk tombol "Hapus" (delegasi event)
        itemsContainer.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('remove-item-incoming')) {
                if (itemsContainer.querySelectorAll('.incoming-item-row').length > 1) {
                    e.target.closest('.incoming-item-row').remove();
                    // Setelah menghapus, perbarui status tombol
                    toggleAddItemButton();
                    toggleSubmitButton();
                } else {
                    alert('Minimal harus ada satu barang dalam transaksi masuk.');
                }
            }
        });

        // Panggil fungsi validasi awal saat halaman dimuat
        toggleAddItemButton();
        toggleSubmitButton();
    });
</script>
@endpush
