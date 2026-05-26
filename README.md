# ♻️ Bank Sampah Enterprise ERP (BSE-ERP)

BSE-ERP adalah sistem informasi manajemen terintegrasi berskala korporat yang dirancang khusus untuk entitas Bank Sampah. Sistem ini mentransformasi operasional bank sampah konvensional menjadi entitas bisnis modern dengan pembukuan standar akuntansi, manajemen likuiditas kas, manufaktur produk turunan, hingga pengelolaan instrumen investasi (*Corporate Treasury*).

## 🚀 Teknologi yang Digunakan

Proyek ini dibangun menggunakan *stack* teknologi modern untuk performa tinggi dan reaktivitas antarmuka:
* **Backend:** [Laravel](https://laravel.com/) (PHP)
* **Frontend:** [Vue.js 3](https://vuejs.org/) (Composition API)
* **Penyambung (Bridge):** [Inertia.js](https://inertiajs.com/) (Monolith SPA)
* **Styling:** [Tailwind CSS](https://tailwindcss.com/)

## ✨ Fitur Unggulan

### 1. Manajemen Operasional & Gudang Inti
* **Pencatatan Timbangan Terpisah:** Memisahkan jenis transaksi **Tabungan** (menjadi liabilitas/utang) dan **Sedekah Sampah** (margin 100% masuk ke ekuitas/hibah).
* **Manajemen Log Niaga:** Pencatatan komoditas yang terjual langsung ke bandar/pengepul besar dengan sistem *upload* bukti nota fisik.
* **Filter Stok Agregat:** Pemantauan arus masuk-keluar sampah secara *real-time* dengan filter spesifik per bulan dan tahun.

### 2. Manufaktur & Penjualan Retail (Produk Turunan)
* **Konversi Barang Mentah:** Sistem produksi yang memotong stok komoditas sampah mentah dan mengubahnya menjadi produk bernilai tambah (misal: Pupuk Kompos, Biji Plastik).
* **Perhitungan HPP Otomatis:** Otomatisasi Harga Pokok Produksi (HPP) berdasarkan nilai sampah dan biaya *overhead*, guna menghasilkan Laba Kotor yang presisi.
* **Point of Sales (POS) Retail:** Pencatatan penjualan produk turunan ke masyarakat umum.

### 3. Corporate Treasury & Investasi 
Merupakan instrumen pengelola arus kas (*cash flow*) agar uang tidak mengendap dan terdepresiasi inflasi:
* **Manajemen Reksadana Pasar Uang (RDPU):** Mengamankan dan memutar Dana Pihak Ketiga (saldo utang nasabah) ke instrumen likuid dengan *yield* stabil.
* **Portofolio Ekuitas/Saham:** Manajemen Laba Bersih (*Retained Earnings*) yang dialokasikan ke instrumen pasar saham untuk pertumbuhan nilai aset perusahaan jangka panjang.
* **Indikator Likuiditas Kas:** *Warning system* jika kas tunai riil tidak cukup untuk mem- *back up* total saldo penarikan nasabah (mencegah illikuid/gagal bayar).

### 4. Akuntansi & Manajemen Aset
* **Laporan Neraca (Balance Sheet):** Visualisasi seimbang antara Harta (Aset Tetap, Kas, Investasi) dengan Pasiva (Utang Nasabah + Modal/Ekuitas).
* **Laporan Laba/Rugi (Income Statement):** Kalkulasi *Gross Profit* hingga *Net Profit* setelah dikurangi Beban Operasional (OPEX) seperti upah pilah, konsumsi, dan logistik.
* **Manajemen Aset Tetap (CAPEX):** Pencatatan inventaris berwujud (mesin *press*, kendaraan, timbangan digital) beserta sistem penyusutan nilai buku.

## 🛠️ Panduan Instalasi (Development)

Untuk menjalankan proyek ini di mesin lokal, pastikan Anda telah menginstal **PHP, Composer, Node.js**, dan perangkat lunak *Database* (MySQL/PostgreSQL).

1. **Clone repositori ini:**
   ```bash
   git clone [https://github.com/username/bse-erp.git](https://github.com/username/bse-erp.git)
   cd bse-erp
