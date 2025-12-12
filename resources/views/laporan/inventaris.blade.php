@extends('layout')

@section('content')
<div class="container">
    <h2 class="fw-bold"><i class="fas fa-file-alt me-2"></i>Laporan Inventaris Barang</h2>
    <p>Laporan ini menunjukkan daftar semua barang, stok terkini, dan nilai total inventaris Anda saat ini.</p>

    <div class="table-responsive">
        {{-- Menggunakan kelas tabel yang sama dengan halaman lain untuk konsistensi --}}
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Stok Saat Ini</th>
                    <th>Harga Beli/Satuan</th>
                    <th>Total Nilai per Barang</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($barangs as $index => $barang)
                    <tr>
                        <td>{{ $barangs->firstItem() + $index }}</td>
                        <td>{{ $barang->kode_barang }}</td>
                        <td>{{ $barang->nama_barang }}</td>
                        <td>{{ $barang->stok }}</td>
                        <td>Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                        {{-- Menghitung nilai per barang (stok * harga) --}}
                        <td>Rp {{ number_format($barang->stok * $barang->harga, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data barang di inventaris.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="table-success-subtle">
                <tr>
                    <th colspan="5" class="text-end">Total Nilai Keseluruhan Inventaris:</th>
                    <th>Rp {{ number_format($totalNilaiInventaris, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $barangs->links() }}
    </div>
</div>
@endsection
