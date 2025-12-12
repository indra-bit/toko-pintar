@extends('layout')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">

                <h4 class="fw-bold card-title"><i class="fas fa-plus-circle me-2"></i>Tambah Barang Baru</h4>
                <p class="card-subtitle mb-4 text-muted">Scan barcode untuk input cepat.</p>
                    <div class="alert alert-primary d-flex align-items-center" role="alert">
                        <div class="me-3"><i class="fas fa-barcode fa-2x"></i></div>
                        <div class="flex-grow-1">
                            <h5 class="alert-heading fw-bold mb-1">Scan Barcode Disini</h5>
                            <input type="text" id="scan-checker" class="form-control mt-2 border-info fw-bold" placeholder="Scan barang..." autofocus autocomplete="off">
                            <small id="scan-status" class="text-muted">Siap scan...</small>
                        </div>
                    </div>


                    <form action="{{ route('barangs.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Kode Barang <span class="text-danger">*</span></label>
                            <input type="text" name="kode_barang" id="kode_barang" class="form-control" required placeholder="Otomatis terisi setelah scan">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="nama_barang" id="nama_barang" class="form-control" required placeholder="Ketik nama barang...">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Harga Jual (Rp) <span class="text-danger">*</span></label>
                                <input type="number" name="harga" class="form-control" required min="0">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Stok Awal</label>
                                <input type="number" name="stok" class="form-control" value="0" required min="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Min. Stok (Alert)</label>
                                <input type="number" name="minimal_stok" class="form-control" value="5">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Expired Date</label>
                                <input type="date" name="tanggal_kadaluarsa" class="form-control">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('barangs.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary px-5"><i class="fas fa-save me-2"></i>Simpan</button>
                        </div>
                    </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scannerInput = document.getElementById('scan-checker');
    const kodeInput = document.getElementById('kode_barang');
    const namaInput = document.getElementById('nama_barang');
    const statusText = document.getElementById('scan-status');

    let typingTimer;
    const doneTypingInterval = 500; // 0.5 detik jeda

    scannerInput.focus();

    // Event Listener Input (Mendeteksi scan selesai)
    scannerInput.addEventListener('input', function() {
        clearTimeout(typingTimer);
        statusText.innerText = 'Sedang memindai...';

        if (scannerInput.value) {
            typingTimer = setTimeout(processBarcode, doneTypingInterval);
        }
    });

    // Juga deteksi Enter (untuk scanner yang support Enter) agar lebih cepat
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

        statusText.innerHTML = '<span class="text-warning"><i class="fas fa-spinner fa-spin"></i> Mengecek database...</span>';

        // Panggil API
        fetch('/api/cari-barang/' + kode)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // BARANG DUPLIKAT
                    statusText.innerHTML = `<span class="text-danger fw-bold"><i class="fas fa-times-circle"></i> Sudah Ada: ${data.data.nama_barang}</span>`;
                    alert('Barang ini sudah terdaftar!\nNama: ' + data.data.nama_barang);
                    scannerInput.value = ''; // Reset scanner
                } else {
                    // BARANG BARU (AMAN)
                    statusText.innerHTML = '<span class="text-success fw-bold"><i class="fas fa-check-circle"></i> Kode Baru. Silakan isi detail.</span>';

                    // Isi kolom kode barang
                    kodeInput.value = kode;

                    // Pindah fokus ke Nama Barang
                    namaInput.focus();
                }
            })
            .catch(err => {
                console.error(err);
                // Jika error 404 (Barang tidak ketemu), itu justru bagus untuk input baru
                if (err) {
                    statusText.innerHTML = '<span class="text-success fw-bold"><i class="fas fa-check-circle"></i> Kode Baru. Silakan isi detail.</span>';
                    kodeInput.value = kode;
                    namaInput.focus();
                }
            });
    }
});
</script>
@endpush
