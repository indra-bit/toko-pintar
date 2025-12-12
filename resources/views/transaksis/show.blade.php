@extends('layout')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4>Detail Transaksi Penjualan</h4>
            <span class="badge bg-primary">Struk: {{ $penjualan->kode }}</span>
        </div>
        <div>
            <a href="{{ route('transaksis.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <a href="{{ route('transaksis.print', $penjualan->id) }}" target="_blank" class="btn btn-success shadow-sm">
                <i class="fas fa-print me-2"></i>Cetak Struk
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="shadow-sm h-100 border-0">
                <div class="card-header bg-light fw-bold">
                    <i class="fas fa-info-circle me-2"></i>Info Transaksi
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted ps-0">Tanggal</td>
                            <td class="fw-bold text-end">{{ $penjualan->created_at->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Jam</td>
                            <td class="fw-bold text-end">{{ $penjualan->created_at->format('H:i') }} WIB</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Kasir</td>
                            <td class="fw-bold text-end">{{ Auth::user()->name ?? 'Admin' }}</td>
                        </tr>
                        <tr class="border-top">
                            <td class="ps-0 pt-3 h5 text-primary">Total</td>
                            <td class="pt-3 h5 text-end text-primary">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-3">
            <div class="shadow-sm h-100 border-0">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-shopping-basket me-2"></i>Rincian Barang
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Nama Barang</th>
                                    <th>Kode</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end pe-4">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($penjualan->transaksis as $item)
                                    <tr>
                                        <td class="ps-4 fw-bold">
                                            {{ $item->barang->nama_barang ?? 'Barang Dihapus' }}
                                        </td>
                                        <td class="text-muted small">
                                            {{ $item->barang->kode_barang ?? '-' }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary px-3">{{ $item->jumlah }}</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold pt-3">Grand Total</td>
                                    <td class="text-end fw-bold text-primary pe-4 pt-3 fs-5">
                                        Rp {{ number_format($penjualan->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
