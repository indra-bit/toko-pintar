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
        // Menggunakan variabel $incomingTransactions agar sesuai dengan View
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
     * Menyimpan transaksi masuk (Perbaikan Logika Mapping).
     */
    public function store(Request $request)
    {
        // 1. Validasi Input sesuai nama field di Form (create.blade.php)
        $request->validate([
            'supplier' => 'required|string|max:255', // Di form namanya 'supplier'
            'tanggal' => 'required|date',            // Di form ada input tanggal
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah' => 'required|integer|min:1', // Di form namanya 'jumlah'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $totalAmount = 0;

                // 2. Simpan Header Transaksi
                // Kita mapping dari input form 'supplier' ke kolom database 'supplier_name'
                $incomingTransaction = IncomingTransaction::create([
                    'supplier_name' => $request->supplier,
                    'reference_number' => 'RX-' . time(), // Generate nomor referensi otomatis
                    'notes' => 'Barang Masuk Tanggal ' . $request->tanggal,
                    'user_id' => auth()->id(),
                    'total_amount' => 0,
                    'created_at' => $request->tanggal . ' ' . now()->format('H:i:s'), // Set tanggal sesuai input
                ]);

                // 3. Loop Barang
                foreach ($request->items as $itemData) {
                    $barang = Barang::findOrFail($itemData['barang_id']);
                    $oldStock = $barang->stok;

                    // Mapping: Form 'jumlah' -> Database 'quantity'
                    $quantity = $itemData['jumlah'];
                    $unitCost = 0; // Default 0 jika tidak ada input harga beli
                    $subTotal = 0; // Default 0

                    // A. Update Stok Barang
                    $barang->stok += $quantity;
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
                        'reference_id' => $incomingTransaction->id, // Bisa null jika kolom belum ada di DB
                    ]);

                    // C. Simpan Detail Item Transaksi
                    IncomingTransactionItem::create([
                        'incoming_transaction_id' => $incomingTransaction->id,
                        'barang_id' => $barang->id,
                        'quantity' => $quantity, // Masuk ke kolom quantity
                        'unit_cost' => $unitCost,
                        'sub_total' => $subTotal,
                    ]);
                }

                // Update total jika ada logika harga (saat ini 0 dulu)
                $incomingTransaction->update(['total_amount' => $totalAmount]);
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
