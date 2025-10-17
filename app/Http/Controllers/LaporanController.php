<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Controllers\Controller;


class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // Ambil filter dari request
        $periode = $request->periode;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        // Query transaksi sesuai filter
        $query = Transaksi::query();
        if ($start_date && $end_date) {
            $query->whereBetween('created_at', [$start_date, $end_date]);
        } elseif ($periode == 'harian') {
            $query->whereDate('created_at', now()->toDateString());
        } elseif ($periode == 'mingguan') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($periode == 'bulanan') {
            $query->whereMonth('created_at', now()->month);
        }
    // Tampilkan data tabel berdasarkan tanggal terbaru (descending)
    $transaksis = $query->orderByDesc('created_at')->paginate(15);

        // Kelompokkan transaksi per tanggal
        $grouped = $query->get()->groupBy(function($item) {
            return $item->created_at->format('d-m-Y');
        });
        $labels = $grouped->keys()->toArray();
        $data = $grouped->map(function($items) {
            return $items->sum('total_harga');
        })->values()->toArray();

        // Balikkan urutan agar grafik menampilkan tanggal terbaru di sebelah kiri (descending)
        $labels = array_reverse($labels);
        $data = array_reverse($data);

        $total = $query->sum('total_harga');

        return view('laporan.index', compact('transaksis', 'labels', 'data', 'total'));
    }
        public function inventaris()
    {
        $barangs = Barang::orderBy('nama_barang', 'asc')->paginate(50);

        // Hitung total nilai inventaris (Harga Beli * Stok)
        $totalNilaiInventaris = Barang::sum(DB::raw('harga * stok'));

        return view('laporan.inventaris', compact('barangs', 'totalNilaiInventaris'));
    }
    public function export(Request $request): StreamedResponse
    {
        $fileName = 'laporan-penjualan-' . Carbon::now()->format('Y-m-d') . '.csv';

        $query = Transaksi::with('barang')->latest();

        // (Salin logika filter yang sama dari metode index)
        if ($request->has('periode')) {
            switch ($request->periode) {
                case 'harian':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'mingguan':
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'bulanan':
                    $query->whereMonth('created_at', Carbon::now()->month);
                    break;
            }
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $transaksis = $query->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID Transaksi', 'Nama Barang', 'Kode Barang', 'Jumlah', 'Total Harga', 'Tanggal Transaksi'];

        $callback = function() use($transaksis, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($transaksis as $transaksi) {
                $row['ID Transaksi']  = $transaksi->id;
                $row['Nama Barang']    = $transaksi->barang->nama_barang ?? 'N/A';
                $row['Kode Barang']  = $transaksi->barang->kode_barang ?? 'N/A';
                $row['Jumlah']  = $transaksi->jumlah;
                $row['Total Harga'] = $transaksi->total_harga;
                $row['Tanggal Transaksi'] = $transaksi->created_at->format('Y-m-d H:i:s');

                fputcsv($file, [
                    $row['ID Transaksi'],
                    $row['Nama Barang'],
                    $row['Kode Barang'],
                    $row['Jumlah'],
                    $row['Total Harga'],
                    $row['Tanggal Transaksi']
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
