# 📦 Toko Pintar (Smart Inventory System)

**Sistem Manajemen Inventaris dan Gudang Toko** yang dirancang untuk efisiensi pencatatan stok barang secara *real-time*, akurat, dan terstruktur.

<img width="1011" height="954" alt="Screenshot 2025-11-19 011441" src="https://github.com/user-attachments/assets/d1246ebe-2807-4372-9b94-922a409c29b8" />


## 📖 Tentang Aplikasi

**Toko Pintar** adalah aplikasi berbasis web yang dibangun untuk menangani permasalahan klasik dalam pengelolaan gudang, yaitu ketidakcocokan antara data stok dengan fisik barang. Aplikasi ini mengadopsi konsep **"Smart Stock Tracking"** dimana setiap pergerakan barang (In/Out) akan divalidasi dan dikalkulasi secara otomatis oleh sistem.

Sistem ini menggunakan pendekatan **Single Administrator** untuk memudahkan kontrol penuh terhadap aset toko tanpa birokrasi yang rumit, menjadikannya solusi ideal untuk UMKM atau toko ritel.

## 🚀 Fitur Unggulan

Berikut adalah fitur-fitur "Pintar" yang ada di dalam sistem:

### 1. 🔐 Single-Gate Authentication
Keamanan data terjamin dengan sistem login terpusat. Hanya Administrator yang memiliki akses penuh untuk mengelola data sensitif dan melakukan transaksi.

### 2. 📝 Manajemen Master Data
Kemudahan dalam mengelola database produk:
- Tambah barang baru dengan detail lengkap (Kode, Nama, Kategori, Satuan).
- Edit informasi barang secara cepat.
- Hapus data barang yang sudah tidak aktif.

### 3. 🔄 Smart In-Out Transaction
Sistem transaksi yang menjadi inti dari aplikasi:
- **Barang Masuk (Stok In):** Otomatis menambah jumlah stok saat barang diterima dari supplier. Mencatat detail tanggal dan sumber barang.
- **Barang Keluar (Stok Out) dengan Validasi:** Sistem cerdas yang otomatis **mencegah transaksi** jika stok yang diminta melebihi ketersediaan di gudang (Anti Stok Minus).

### 4. 📊 Laporan & Riwayat Otomatis
Tidak perlu rekap manual. Sistem menyediakan:
- **Kartu Stok Digital:** Melihat riwayat pergerakan setiap item.
- **Laporan Real-time:** Mengetahui posisi stok terakhir kapan saja.
- **Export Data:** Kemudahan cetak laporan untuk kebutuhan audit bulanan.

---

## ⚙️ Alur Kerja Sistem (Workflow)

Sistem bekerja dengan alur logika berikut:

1.  **Inisialisasi:** Admin login dan mendaftarkan item barang ke dalam **Master Data**.
2.  **Restock (IN):** Saat barang fisik datang, Admin menginput data ke menu **Barang Masuk**. Sistem otomatis mengupdate `Stok Akhir = Stok Awal + Jumlah Masuk`.
3.  **Distribusi (OUT):** Saat terjadi penjualan/pemakaian, Admin menginput ke menu **Barang Keluar**. Sistem mengecek ketersediaan stok. Jika aman, `Stok Akhir = Stok Awal - Jumlah Keluar`.
4.  **Reporting:** Pemilik/Admin dapat memantau seluruh aktivitas melalui menu **Laporan**.

---

## 🛠️ Teknologi yang Digunakan

* **Bahasa Pemrograman:** PHP (Framework Laravel11)
* **Database:** MySQL
* **Frontend:** HTML, CSS, Tailwind, JavaScript
* **Tools:** VS Code, XAMPP

---

## 📦 Cara Instalasi

1.  Clone repositori ini:
    ```bash
    git clone [https://github.com/indra-bit/toko-pintar.git](https://github.com/indra-bit/toko-pintar.git)
    ```
2.  Import database `toko_pintar.sql` ke phpMyAdmin.
3.  Atur koneksi database pada file konfigurasi.
4.  Buka aplikasi di browser (misal: `localhost/toko-pintar`).
5.  Login dengan akun default (jika ada).

---

**Dikembangkan oleh:** [Indra & Salman /Indra-Bit]  
*Untuk kebutuhan penyusunan laporan dan pengelolaan gudang.*
