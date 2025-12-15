<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\IncomingTransaction;
use App\Models\IncomingTransactionItem;
use App\Models\StockHistory;
use Illuminate\Support\Facades\DB;

class IncomingTransactionController extends Controller
{
    /**
     * Menampilkan daftar transaksi masuk.
     */
    public function index()
    {
        $transactions = IncomingTransaction::with('user')
                                                 ->latest()
                                                 ->paginate(10);

        return view('incoming_transactions.index', compact('transactions'));
    }

    /**
     * Menampilkan formulir untuk membuat transaksi masuk baru.
     */
    public function create()
    {
        $barangs = Barang::all();
        return view('incoming_transactions.create', compact('barangs'));
    }

    /**
     * Menyimpan transaksi masuk.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'supplier' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0', // Validasi Harga
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 2. Simpan Header Transaksi (Total Amount dihitung nanti)
                $incomingTransaction = IncomingTransaction::create([
                    'supplier_name' => $request->supplier,
                    'reference_number' => 'RX-' . time(),
                    'notes' => 'Barang Masuk Tanggal ' . $request->tanggal,
                    'user_id' => auth()->id(),
                    'total_amount' => 0,
                    'created_at' => $request->tanggal . ' ' . now()->format('H:i:s'),
                ]);

                $grandTotal = 0;

                // 3. Loop Barang
                foreach ($request->items as $itemData) {
                    $barang = Barang::findOrFail($itemData['barang_id']);
                    $oldStock = $barang->stok;

                    $quantity = $itemData['jumlah'];
                    $unitCost = $itemData['harga']; // Ambil harga dari inputan user
                    $subTotal = $quantity * $unitCost;

                    $grandTotal += $subTotal;

                    // A. Update Stok Barang
                    $barang->stok += $quantity;
                    // OPSI: Jika ingin harga master ikut berubah saat restock, uncomment baris bawah:
                    // $barang->harga = $unitCost;
                    $barang->save();

                    // B. Catat History Stok
                    StockHistory::create([
                        'barang_id' => $barang->id,
                        'user_id' => auth()->id(),
                        'old_stock' => $oldStock,
                        'new_stock' => $barang->stok,
                        'change_quantity' => $quantity,
                        'reason' => 'Penerimaan Barang (Supl: ' . $request->supplier . ')',
                        'reference_type' => IncomingTransaction::class,
                        'reference_id' => $incomingTransaction->id,
                    ]);

                    // C. Simpan Detail Item Transaksi
                    IncomingTransactionItem::create([
                        'incoming_transaction_id' => $incomingTransaction->id,
                        'barang_id' => $barang->id,
                        'quantity' => $quantity,
                        'unit_cost' => $unitCost, // Simpan harga beli
                        'sub_total' => $subTotal,
                    ]);
                }

                // Update total amount transaksi
                $incomingTransaction->update(['total_amount' => $grandTotal]);
            });

            return redirect()->route('incoming_transactions.index')
                             ->with('success', 'Transaksi masuk berhasil disimpan.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan detail.
     */
    public function show(IncomingTransaction $incomingTransaction)
    {
        $incomingTransaction->load('items.barang', 'user');
        return view('incoming_transactions.show', compact('incomingTransaction'));
    }
}
