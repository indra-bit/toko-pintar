@extends('layout')

@section('content')
    <div class="container mt-4">
        <h2 class="fw-bold"><i class="fas fa-history me-2"></i>Riwayat Perubahan Stok</h2>
        <p>Berikut adalah catatan setiap perubahan stok barang dalam inventaris Anda.</p>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th>Tanggal & Waktu</th>
                        <th>Barang</th>
                        <th>Kode Barang</th>
                        <th>Stok Lama</th>
                        <th>Stok Baru</th>
                        <th>Perubahan</th>
                        <th>Alasan</th>
                        <th>Oleh User</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($history as $record)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $record->created_at->format('d M Y H:i:s') }}</td>
                            <td>{{ $record->barang->nama_barang ?? 'Barang Tidak Ditemukan' }}</td>
                            <td>{{ $record->barang->kode_barang ?? '-' }}</td>
                            <td>{{ $record->old_stock }}</td>
                            <td>{{ $record->new_stock }}</td>
                            <td>
                                @if ($record->change_quantity > 0)
                                    <span class="badge bg-success">{{ $record->change_quantity }}</span>
                                @elseif ($record->change_quantity < 0)
                                    <span class="badge bg-danger">{{ $record->change_quantity }}</span>
                                 @else
                                   <span class="badge bg-secondary">{{ $record->change_quantity }}</span>
                                @endif
                            </td>
                            <td>{{ $record->reason }}</td>
                            <td>{{ $record->user->name ?? 'Sistem/Tidak Diketahui' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada riwayat perubahan stok yang tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Tampilkan link paginasi --}}
        <div class="d-flex justify-content-center">
            {{ $history->links() }}
        </div>
    </div>
@endsection
