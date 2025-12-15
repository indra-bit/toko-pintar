@extends('layout')

@section('content')
    <div class="container mt-4">
        <h2>Detail Transaksi Penjualan</h2>
        <p>Informasi lengkap mengenai transaksi penjualan #{{ $penjualan->kode }}.</p>
        <hr class="mb-4">

        {{-- BAGIAN 1: INFORMASI UMUM (Style disamakan dengan Incoming Transaction) --}}
        <div class="shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Umum Transaksi</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nomor Struk:</strong> <span class="badge bg-warning text-dark">{{ $penjualan->kode }}</span></p>
                        <p><strong>Kasir / User:</strong> {{ Auth::user()->name ?? 'Admin' }}</p>
                        <p><strong>Status:</strong> <span class="badge bg-success">Berhasil</span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Tanggal Transaksi:</strong> {{ $penjualan->created_at->format('d M Y') }}</p>
                        <p><strong>Waktu:</strong> {{ $penjualan->created_at->format('H:i:s') }} WIB</p>
                        <p><strong>Total Nilai Transaksi:</strong> <span class="fw-bold text-primary fs-5">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</span></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- BAGIAN 2: DAFTAR BARANG (Tabel Full Width) --}}
        <h4 class="mb-0">Rincian Barang Terjual</h4>
        <hr class="mb-4">

        <div class="shadow-sm mb-4 card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Kode Barang</th>
                            <th class="text-center">Jumlah (Qty)</th>
                            <th class="text-end">Harga Satuan</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penjualan->transaksis as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-bold">{{ $item->barang->nama_barang ?? 'Barang Dihapus' }}</td>
                                <td>{{ $item->barang->kode_barang ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary px-3">{{ $item->jumlah }}</span>
                                </td>
                                <td class="text-end">
                                    {{-- Menghitung harga satuan rata-rata (Total / Jumlah) --}}
                                    Rp {{ number_format($item->total_harga / $item->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Tidak ada barang dalam transaksi ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-primary-subtle">
                        <tr>
                            <th colspan="5" class="text-end py-3">Grand Total Penjualan:</th>
                            <th class="text-end fw-bold text-primary fs-5 py-3">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- BAGIAN TOMBOL AKSI --}}
        <div class="d-flex justify-content-between mt-4 mb-5">
            <a href="{{ route('transaksis.index') }}" class="btn btn-secondary btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Riwayat
            </a>

            <a href="{{ route('transaksis.print', $penjualan->id) }}" target="_blank" class="btn btn-success btn-lg shadow">
                <i class="fas fa-print me-2"></i>Cetak Struk
            </a>
        </div>

    </div>
@endsection
