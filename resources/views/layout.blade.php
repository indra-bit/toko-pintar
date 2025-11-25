<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Mart @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS untuk Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        * {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: #333;
        }

        /* Navbar Styling */
        .navbar {
            background: linear-gradient(135deg, #4A90E2 0%, #87CEEB 100%) !important;
            box-shadow: 0 4px 20px rgba(74, 144, 226, 0.2);
            padding: 10px 0;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 24px;
            color: white !important;
        }

        .navbar-brand i {
            margin-right: 10px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 8px 15px !important;
            border-radius: 6px;
            margin: 0 5px;
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.15);
        }

        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.25);
            color: white !important;
        }

        /* Main Content */
        .main-container {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin: 30px auto;
            padding: 30px;
            min-height: calc(100vh - 180px);
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 20px;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(74, 144, 226, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, #4A90E2 0%, #87CEEB 100%);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            padding: 15px 20px;
            font-weight: 600;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #4A90E2 0%, #87CEEB 100%);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(74, 144, 226, 0.3);
            transition: all 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 144, 226, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-outline-primary {
            color: #4A90E2;
            border-color: #4A90E2;
        }

        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #4A90E2 0%, #87CEEB 100%);
            color: white;
        }

        /* Forms */
        .form-control {
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #4A90E2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.2);
        }

        /* Alerts */
        .alert {
            border-radius: 8px;
            padding: 15px;
        }

        /* Table */
        .table {
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead th {
            background-color: #4A90E2;
            color: white;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 14px;
            margin-top: 30px;
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .main-container {
            animation: fadeIn 0.5s ease-out;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-container {
                padding: 20px;
                margin: 15px auto;
            }

            .navbar-brand {
                font-size: 20px;
            }
        }

        @media (min-width: 992px) {
        .navbar .dropdown:hover .dropdown-menu {
            display: block;
            margin-top: 0;
        }
    }
    </style>
</head>
<body>
    @auth
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard">
                <i class="fas fa-boxes"></i>Smart Mart
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('dashboard')) active @endif" href="{{ url('/dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>

                    @if(Auth::check() && (Auth::user()->role === 'admin'))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle @if(request()->is('barangs*')) active @endif" href="#" id="navbarDropdownBarang" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-box-open"></i> Barang
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownBarang">
                            <li><a class="dropdown-item" href="{{ route('barangs.index') }}">Daftar Barang</a></li>
                            <li><a class="dropdown-item" href="{{ route('barangs.create') }}">Input Barang Baru</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle @if(request()->is('incoming-transactions*')) active @endif" href="#" id="navbarDropdownIncoming" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-dolly-flatbed"></i> Transaksi Masuk
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownIncoming">
                            <li><a class="dropdown-item" href="{{ route('incoming_transactions.index') }}">Daftar Transaksi Masuk</a></li>
                            <li><a class="dropdown-item" href="{{ route('incoming_transactions.create') }}">Input Transaksi Masuk Baru</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle @if(request()->is('transaksis*')) active @endif" href="#" id="navbarDropdownTransaksi" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-exchange-alt"></i> Transaksi
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownTransaksi">
                            <li><a class="dropdown-item" href="{{ route('transaksis.index') }}">Riwayat Transaksi</a></li>
                            <li><a class="dropdown-item" href="{{ route('transaksis.create') }}">Input Transaksi Baru</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle @if(request()->is('laporan*')) active @endif" href="#" id="navbarDropdownLaporan" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-file-alt"></i> Laporan
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownLaporan">
                            <li><a class="dropdown-item" href="{{ route('laporan.index') }}">Laporan Penjualan</a></li>
                            <li><a class="dropdown-item" href="{{ route('laporan.inventaris') }}">Laporan Inventaris</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('stock-history*')) active @endif" href="{{ route('stock_history.index') }}">
                            <i class="fas fa-history"></i> Riwayat Stok
                        </a>
                    </li>
                    @endif

                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle"></i> {{ Auth::user()->name }} ({{ Auth::user()->role }})
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user-cog me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item" type="submit">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @endauth

    <!-- Main Content -->
    <div class="container">
        <div class="main-container">
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>Sistem Manajemen Stok Barang - SMart &copy; {{ date('Y') }}</p>
        </div>
    </footer>

    <!-- Pustaka JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @stack('scripts')
</body>
</html>
