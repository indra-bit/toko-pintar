@extends('layout')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="fw-bold"><i class="fas fa-file-alt me-2"></i>Laporan Penjualan</h2>
                <a href="{{ route('laporan.export', request()->query()) }}" class="btn btn-success">
                    <i class="fas fa-file-excel me-2"></i>Ekspor ke CSV
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
                    <form action="{{ route('laporan.index') }}" method="GET" class="row g-3 align-items-center">
                        <div class="col-md-3">
                            <label for="periode" class="form-label">Filter Cepat:</label>
                            <select name="periode" id="periode" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Waktu</option>
                                <option value="harian" {{ request('periode') == 'harian' ? 'selected' : '' }}>Hari Ini</option>
                                <option value="mingguan" {{ request('periode') == 'mingguan' ? 'selected' : '' }}>Minggu Ini</option>
                                <option value="bulanan" {{ request('periode') == 'bulanan' ? 'selected' : '' }}>Bulan Ini</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Dari Tanggal:</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">Hingga Tanggal:</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label d-block">&nbsp;</label>
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary flex-grow-1 me-2">Terapkan Filter</button>
                                <a href="{{ route('laporan.index') }}" class="btn btn-secondary flex-grow-1">Reset</a>
                            </div>
                        </div>
                    </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-chart-line me-2"></i>Grafik Penjualan
                </div>
                <div class="card-body">
                    <div style="height: 400px;">
                        <canvas id="grafikPenjualan"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Barang</th>
                                    <th>Jumlah</th>
                                    <th>Total Harga</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transaksis as $index => $transaksi)
                                    <tr>
                                        <td>{{ $transaksis->firstItem() + $index }}</td>
                                        <td>{{ $transaksi->barang->nama_barang ?? '-' }}</td>
                                        <td>{{ $transaksi->jumlah }}</td>
                                        <td>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                        <td>{{ $transaksi->created_at->format('d-m-Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data transaksi untuk periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="alert alert-success fw-bold mb-0">
                            Total Penjualan (Sesuai Filter): Rp {{ number_format($total, 0, ',', '.') }}
                        </div>
                        <div>
                            {{ $transaksis->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const canvas = document.getElementById('grafikPenjualan');
        let labels = @json($labels);
        let data = @json($data);
        if (canvas) {
            const ctx = canvas.getContext('2d');
            const grafikPenjualan = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Penjualan',
                        data: data,
                        backgroundColor: 'rgba(74, 144, 226, 0.2)',
                        borderColor: 'rgba(74, 144, 226, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        } else {
            console.warn('Canvas grafikPenjualan tidak ditemukan!');
        }
    });
</script>
@endpush
