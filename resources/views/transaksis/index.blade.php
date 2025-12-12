@extends('layout')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold"><i class="fas fa-history me-2"></i>Riwayat Transaksi</h2>
            <p class="text-muted mb-0">Daftar struk transaksi yang telah berhasil dibayar.</p>
        </div>
        <a href="{{ route('transaksis.create') }}" class="btn btn-primary btn-lg shadow-sm">
            <i class="fas fa-cash-register me-2"></i>Transaksi Baru
        </a>
    </div>

    <div class="shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th width="20%">No. Struk</th>
                            <th width="20%">Tanggal & Waktu</th>
                            <th class="text-center">Jumlah Item</th>
                            <th class="text-end" width="20%">Total Transaksi</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penjualans as $penjualan)
                            <tr>
                                <td class="text-center">{{ $loop->iteration + $penjualans->firstItem() - 1 }}</td>

                                <td class="fw-bold text-primary">
                                    {{ $penjualan->kode }}
                                </td>

                                <td>
                                    <div class="fw-bold">{{ $penjualan->created_at->format('d M Y') }}</div>
                                    <small class="text-muted"><i class="far fa-clock me-1"></i> {{ $penjualan->created_at->format('H:i') }} WIB</small>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-secondary rounded-pill px-3">
                                        {{ $penjualan->transaksis->count() }} Jenis Barang
                                    </span>
                                </td>

                                <td class="text-end fw-bold text-success fs-5">
                                    Rp {{ number_format($penjualan->total, 0, ',', '.') }}
                                </td>

                                <td class="text-center">
                                    <div class="btn-group shadow-sm">
                                        <a href="{{ route('transaksis.show', $penjualan->id) }}" class="btn btn-sm btn-info text-white" title="Lihat Detail Barang">
                                            <i class="fas fa-eye me-1"></i> Detail
                                        </a>
                                        <a href="{{ route('transaksis.print', $penjualan->id) }}" target="_blank" class="btn btn-sm btn-secondary" title="Cetak Struk">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-receipt fa-3x mb-3 text-secondary"></i>
                                        <p class="mb-2">Belum ada transaksi hari ini.</p>
                                        <a href="{{ route('transaksis.create') }}" class="btn btn-sm btn-primary">
                                            Mulai Kasir
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($penjualans->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $penjualans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
