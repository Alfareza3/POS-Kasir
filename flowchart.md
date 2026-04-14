graph TD
A([Mulai]) --> B{Apakah sesi aktif?}

    B -- Belum --> C[Halaman Login]
    C --> D{Cek Username & Password}
    D -- Tidak Valid --> C
    D -- Valid --> E{Cek Hak Akses}

    B -- Sesi Aktif --> E

    %% Cabang berdasarkan Hak Akses
    E -- Administrator --> F[Dashboard Admin]
    E -- Petugas/Kasir --> G[Halaman Transaksi Kasir]

    %% Alur Administrator
    F --> H[Kelola Data Produk CRUD]
    F --> I[Kelola Data Pengguna CRUD]
    F --> J[Lihat Laporan Penjualan]
    H --> P
    I --> P
    J --> P

    %% Alur Petugas/Kasir (Transaksi)
    G --> K[Cari & Pilih Produk]
    K --> L[Masukkan Produk ke Keranjang]
    L --> M[Masukkan Nominal Pembayaran]
    M --> N{Cek Pembayaran}
    N --> O[Simpan Transaksi]
    O --> P1[Cetak Struk/Nota]
    P1 --> G

    %% Logout
    P[Logout] --> Q([Selesai])
    G -. Jika ingin keluar .-> P
    F -. Jika ingin keluar .-> P
