@extends('layout')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold"><i class="fas fa-box-open me-2"></i>Daftar Barang</h2>
            <p class="text-muted">Kelola data master barang di gudang Anda.</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('barangs.create') }}" class="btn btn-primary btn-lg shadow-sm">
                <i class="fas fa-plus me-2"></i>Tambah Barang Baru
            </a>
        </div>
    </div>

    <div class="shadow-sm mb-4">
        <div class="card-body bg-light">
            <form action="{{ route('barangs.index') }}" method="GET" id="search-form">
                <div class="row g-2 align-items-center">
                    <div class="col-md-1 text-center">
                        <i class="fas fa-barcode fa-2x text-muted"></i>
                    </div>
                    <div class="col-md-11">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white text-primary"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" id="scanner-search" class="form-control"
                                   placeholder="Scan Barcode disini (Otomatis Cari)..."
                                   value="{{ request('search') }}" autofocus autocomplete="off">

                            @if(request('search'))
                                <a href="{{ route('barangs.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="fas fa-times"></i></a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

<div class="shadow-sm mb-4 border-light">
        <div class="card-body p-3 bg-light rounded">
            <form action="{{ route('barangs.index') }}" method="GET">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label class="fw-bold text-muted mb-0"><i class="fas fa-search me-2"></i>Pencarian Manual:</label>
                    </div>
                    <div class="col-md-9">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                   placeholder="Ketik Nama Barang atau Kode Barang disini..."
                                   value="{{ request('search') }}">

                            @if(request('search'))
                                <a href="{{ route('barangs.index') }}" class="btn btn-outline-secondary" title="Reset Pencarian"><i class="fas fa-times"></i></a>
                            @endif

                            <button class="btn btn-primary px-4" type="submit">Cari</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th class="text-center">Stok</th>
                            <th class="text-end">Harga</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($barangs as $barang)
                            <tr class="{{ $barang->stok <= $barang->minimal_stok ? 'table-warning' : '' }}">
                                <td class="fw-bold text-primary">{{ $barang->kode_barang }}</td>
                                <td>
                                    <div class="fw-bold">{{ $barang->nama_barang }}</div>
                                    @if($barang->stok <= $barang->minimal_stok)
                                        <small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Stok Menipis</small>
                                    @endif
                                </td>
                                <td><span class="badge bg-info text-dark">{{ $barang->category->name ?? '-' }}</span></td>
                                <td class="text-center fs-5">{{ $barang->stok }}</td>
                                <td class="text-end">Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('barangs.edit', $barang->id) }}" class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('barangs.destroy', $barang->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus barang ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-box-open fa-3x mb-3"></i><br>
                                    Data barang tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $barangs->links() }}</div>
        </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('scanner-search');
    const searchForm = document.getElementById('search-form');
    let typingTimer;                // Timer identifier
    const doneTypingInterval = 600;  // Waktu tunggu (0.6 detik) setelah scan selesai

    // 1. Fokus otomatis
    searchInput.focus();

    // 2. Logic "Tunggu sampai selesai ngetik/scan"
    searchInput.addEventListener('input', function () {
        clearTimeout(typingTimer);
        if (searchInput.value) {
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
        }
    });

    // 3. Fungsi yang dijalankan saat scan dianggap selesai
    function doneTyping () {
        // Otomatis Submit Form
        searchForm.submit();
    }

    // 4. Pastikan kursor selalu aktif (Opsional, untuk mode kiosk)
    /*
    document.addEventListener('click', (e) => {
        if(e.target.tagName !== 'A' && e.target.tagName !== 'BUTTON' && e.target.tagName !== 'INPUT') {
            searchInput.focus();
        }
    });
    */
   if ($('.alert').length > 0) {
        setTimeout(function() {
            // Efek fadeOut menggunakan jQuery
            $('.alert').fadeOut(500, function() {
                $(this).remove();
            });
        }, 2000); // 3000ms = 3 detik
    }
});
</script>
@endpush
