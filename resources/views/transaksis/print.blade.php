<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk - {{ $penjualan->kode }}</title>
    <style>
        body { font-family: monospace; font-size: 12px; }
        .receipt { width: 320px; margin: 0 auto; }
        .center { text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 4px 0; }
        .total { font-weight: bold; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="center">
            <h3>Toko 134</h3>
            <div>Jl. Sukamenak No.134</div>
            <div>Telp: 0857-9499-3687</div>
            <hr>
        </div>

        <div>
            <div>Kode: <strong>{{ $penjualan->kode }}</strong></div>
            <div>Tanggal: {{ $penjualan->created_at->format('d-m-Y') }}</div>
            <div>Jam: {{ $penjualan->created_at->format('H:i') }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th align="left">Item</th>
                    <th align="right">Qty</th>
                    <th align="right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penjualan->transaksis as $t)
                    <tr>
                        <td>{{ $t->barang->nama_barang ?? 'N/A' }}</td>
                        <td align="right">{{ $t->jumlah }}</td>
                        <td align="right">Rp {{ number_format($t->total_harga,0,',','.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <hr>
        <div style="display:flex; justify-content:space-between;">
            <div>Total</div>
            <div class="total">Rp {{ number_format($penjualan->total,0,',','.') }}</div>
        </div>

        <br>
        <br>
        <div class="center">
            <p>Sistem Manajemen Stok Barang - SMart</p>
        </div>


        <div class="center no-print" style="margin-top:10px;">
            <button onclick="window.print()">Cetak</button>
        </div>

    </div>
</body>
</html>
