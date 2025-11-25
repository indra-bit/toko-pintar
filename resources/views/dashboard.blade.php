@extends('layout')

@section('content')
<div class="container mt-4">
    <div class="alert alert-primary d-flex justify-content-between align-items-center" role="alert">
        <div>
            Selamat datang, <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->role }})
        </div>
    </div>

    @if($stokHampirHabis->isNotEmpty())
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-warning">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Peringatan Stok Rendah!</h4>
                            <p class="mb-0">Terdapat <strong>{{ $stokHampirHabis->count() }}</strong> barang dengan stok di bawah batas minimal. Harap segera lakukan pengadaan ulang.</p>
                        </div>
                        <button class="btn btn-sm btn-outline-dark" type="button" data-bs-toggle="collapse" data-bs-target="#stokHampirHabisList" aria-expanded="false" aria-controls="stokHampirHabisList">
                            Lihat Detail
                        </button>
                    </div>
                    <div class="collapse mt-3" id="stokHampirHabisList">
                        <hr>
                        <ul class="mb-0 list-group" style="max-height: 250px; overflow-y: auto;">
                            @foreach($stokHampirHabis as $barang)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $barang->nama_barang }}
                                    <span class="badge bg-danger rounded-pill">Sisa: {{ $barang->stok }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($barangAkanKadaluarsa->isNotEmpty())
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-danger">
                     <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="alert-heading"><i class="fas fa-calendar-times me-2"></i>Peringatan Barang Akan Kadaluarsa!</h4>
                            <p class="mb-0">Terdapat <strong>{{ $barangAkanKadaluarsa->count() }}</strong> barang yang akan atau sudah kadaluarsa. Segera periksa dan lakukan tindakan.</p>
                        </div>
                        <button class="btn btn-sm btn-outline-dark" type="button" data-bs-toggle="collapse" data-bs-target="#barangKadaluarsaList" aria-expanded="false" aria-controls="barangKadaluarsaList">
                            Lihat Detail
                        </button>
                    </div>
                    <div class="collapse mt-3" id="barangKadaluarsaList">
                        <hr>
                        <ul class="mb-0 list-group" style="max-height: 250px; overflow-y: auto;">
                            @foreach($barangAkanKadaluarsa as $barang)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $barang->nama_barang }}</strong>
                                        <small class="d-block text-muted">
                                            Kadaluarsa pada: {{ \Carbon\Carbon::parse($barang->tanggal_kadaluarsa)->format('d F Y') }}
                                        </small>
                                    </div>
                                    <span class="badge bg-dark rounded-pill">{{ \Carbon\Carbon::parse($barang->tanggal_kadaluarsa)->diffForHumans() }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row mt-4">
        @if(Auth::user()->role === 'admin')
        <div class="col-md-4 mb-3">
            <a href="{{ route('barangs.index') }}" class="text-decoration-none">
                <div class="card border-primary shadow-sm h-100">
                    <div class="card-body text-center text-primary">
                        <i class="fas fa-boxes fa-2x mb-2"></i>
                        <h5 class="card-title">Daftar Barang</h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="{{ route('barangs.create') }}" class="text-decoration-none">
                <div class="card border-info shadow-sm h-100">
                    <div class="card-body text-center text-info">
                        <i class="fas fa-plus-circle fa-2x mb-2"></i>
                        <h5 class="card-title">Input Barang</h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="{{ route('incoming_transactions.index') }}" class="text-decoration-none">
                <div class="card border-success shadow-sm h-100">
                    <div class="card-body text-center text-success">
                        <i class="fas fa-dolly-flatbed fa-2x mb-2"></i>
                        <h5 class="card-title">Daftar Transaksi Masuk</h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="{{ route('incoming_transactions.create') }}" class="text-decoration-none">
                <div class="card border-warning shadow-sm h-100">
                    <div class="card-body text-center text-warning">
                        <i class="fas fa-truck-loading fa-2x mb-2"></i>
                        <h5 class="card-title">Input Transaksi Masuk</h5>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4 mb-3">
            <a href="{{ route('transaksis.index') }}" class="text-decoration-none">
                <div class="card border-success shadow-sm h-100">
                    <div class="card-body text-center text-success">
                        <i class="fas fa-history fa-2x mb-2"></i>
                        <h5 class="card-title">Riwayat Transaksi</h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="{{ route('transaksis.create') }}" class="text-decoration-none">
                <div class="card border-warning shadow-sm h-100">
                    <div class="card-body text-center text-warning">
                        <i class="fas fa-cash-register fa-2x mb-2"></i>
                        <h5 class="card-title">Input Transaksi</h5>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4 mb-3">
            <a href="{{ route('laporan.index') }}" class="text-decoration-none">
                <div class="card border-danger shadow-sm h-100">
                    <div class="card-body text-center text-danger">
                        <i class="fas fa-chart-line fa-2x mb-2"></i>
                        <h5 class="card-title">Laporan Penjualan</h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="{{ route('laporan.inventaris') }}" class="text-decoration-none">
                <div class="card border-primary shadow-sm h-100">
                    <div class="card-body text-center text-primary">
                        <i class="fas fa-boxes fa-2x mb-2"></i>
                        <h5 class="card-title">Laporan Inventaris</h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="{{ route('stock_history.index') }}" class="text-decoration-none">
                <div class="card border-info shadow-sm h-100">
                    <div class="card-body text-center text-info">
                        <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                        <h5 class="card-title">Riwayat Stok</h5>
                    </div>
                </div>
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
@section('title', 'Dashboard')
