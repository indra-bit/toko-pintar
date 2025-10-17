@extends('layout')

@section('content')
    <h2>Riwayat Penjualan</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <a href="{{ route('transaksis.create') }}" class="btn btn-primary mb-3">Tambah Transaksi Baru</a>

    @forelse($penjualans as $index => $penjualan)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <strong>Kode:</strong> {{ $penjualan->kode }}<br>
                    <small>{{ $penjualan->created_at->format('d-m-Y H:i') }}</small>
                </div>
                <div>
                    <a href="{{ route('transaksis.print', $penjualan->id) }}" class="btn btn-sm btn-outline-secondary" target="_blank">Cetak Struk</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                                <th>Harga Satuan</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penjualan->transaksis as $i => $t)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $t->barang->nama_barang ?? 'N/A' }}</td>
                                    <td>{{ $t->jumlah }}</td>
                                    <td>Rp {{ number_format($t->barang->harga ?? 0, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Total</strong></td>
                                <td><strong>Rp {{ number_format($penjualan->total, 0, ',', '.') }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">Belum ada penjualan.</div>
    @endforelse

    <div class="d-flex justify-content-center">
        {{ $penjualans->links() }}
    </div>
@endsection
