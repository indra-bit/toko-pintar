<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\Penjualan;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Menampilkan riwayat transaksi penjualan.
     */
    public function index()
    {
        // Ambil penjualan yang memiliki transaksis, termasuk relasi barang
        $penjualans = Penjualan::with(['transaksis.barang'])->latest()->paginate(25);
        return view('transaksis.index', compact('penjualans'));
    }

    /**
     * Menampilkan form untuk membuat transaksi penjualan baru.
     */
    public function create()
    {
        $barangs = Barang::orderBy('nama_barang')->get();
        return view('transaksis.create', compact('barangs'));
    }

    /**
     * Menyimpan transaksi penjualan dan mengurangi stok.
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Buat header penjualan
                $penjualan = Penjualan::create([
                    'kode' => 'PJ' . now()->format('YmdHis'),
                    'total' => 0,
                ]);

                $grandTotal = 0;

                foreach ($request->items as $item) {
                    if (!isset($item['barang_id']) || !isset($item['jumlah'])) {
                        continue;
                    }

                    $barang = Barang::findOrFail($item['barang_id']);

                    if ($barang->stok < $item['jumlah']) {
                        throw new \Exception("Stok untuk barang '{$barang->nama_barang}' tidak cukup.");
                    }

                    $total = $barang->harga * $item['jumlah'];

                    $barang->stok -= $item['jumlah'];
                    $barang->save();

                    // Membuat catatan di tabel transaksi terkait penjualan
                    Transaksi::create([
                        'penjualan_id' => $penjualan->id,
                        'barang_id' => $barang->id,
                        'jumlah' => $item['jumlah'],
                        'total_harga' => $total,
                    ]);

                    $grandTotal += $total;
                }

                // Update total penjualan
                $penjualan->update(['total' => $grandTotal]);
            });

            return back()->with('success', 'Transaksi berhasil disimpan.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan form untuk mencatat pembelian barang (stok masuk).
     */
    public function createPembelian()
    {
        $barangs = Barang::orderBy('nama_barang')->get();
        return view('pembelian.create', compact('barangs'));
    }

    /**
     * Menyimpan data pembelian dan menambah stok barang.
     */
    public function storePembelian(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->items as $item) {
                    if (!isset($item['barang_id']) || !isset($item['jumlah'])) {
                        continue;
                    }

                    $barang = Barang::findOrFail($item['barang_id']);

                    $barang->stok += $item['jumlah'];
                    $barang->save();
                }
            });

            return back()->with('success', 'Data pembelian berhasil disimpan dan stok telah diperbarui.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan detail transaksi penjualan tertentu (Struk).
     */
    public function show($id)
    {
        // Cari data penjualan berdasarkan ID, lengkap dengan item transaksinya
        $penjualan = Penjualan::with(['transaksis.barang', 'transaksis'])->findOrFail($id);

        return view('transaksis.show', compact('penjualan'));
    }
    public function edit(Transaksi $transaksi) {}
    public function update(Request $request, Transaksi $transaksi) {}
    public function destroy(Transaksi $transaksi) {}

    /**
     * Cetak struk untuk penjualan tertentu
     */
    public function print($penjualanId)
    {
        $penjualan = Penjualan::with('transaksis.barang')->findOrFail($penjualanId);
        return view('transaksis.print', compact('penjualan'));
    }
}
