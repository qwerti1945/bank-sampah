<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LaporanDashboardExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CollectorSale;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\Waste;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Parameter Filter (Default ke Bulan & Tahun Saat Ini)
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->format('m')); 

        $applyDateFilter = function ($query) use ($year, $month) {
            if ($year && $year !== 'all') {
                $query->whereYear('created_at', $year);
            }
            if ($month && $month !== 'all') {
                $query->whereMonth('created_at', $month);
            }
        };

        // ==================================================
        // A. METRIK GLOBAL (AKUMULATIF)
        // ==================================================
        $totalNasabah = User::where('role', 'nasabah')->count();
        $totalSaldoNasabah = (float) User::where('role', 'nasabah')->sum('balance');
        
        $globalMasuk = (float) DB::table('deposit_details')->sum('qty');
        $globalKeluar = (float) DB::table('collector_sale_details')->sum('qty');
        $totalStokGudang = max(0, $globalMasuk - $globalKeluar);

        $globalPendapatan = (float) CollectorSale::sum('total_amount');
        $globalPenarikan = (float) Withdrawal::sum('amount');
        $kasPengelola = $globalPendapatan - $globalPenarikan;

        // ==================================================
        // B. LOGIKA GRAFIK DINAMIS (MENGIKUTI FILTER)
        // ==================================================
        $chartDates = [];
        $chartDepositData = [];
        $chartWithdrawalData = [];
        $chartTitle = "";
        $chartSubtitle = "";

        if ($year !== 'all' && $month !== 'all') {
            // MODE HARIAN (1 BULAN)
            $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $chartDates[] = $i;
                $chartDepositData[$i] = 0;
                $chartWithdrawalData[$i] = 0;
            }

            $deposits = Deposit::with('details')->whereYear('created_at', $year)->whereMonth('created_at', $month)->get();
            foreach ($deposits as $d) {
                $day = (int) $d->created_at->format('d');
                $chartDepositData[$day] += $d->details->sum('qty');
            }

            $withdrawals = Withdrawal::whereYear('created_at', $year)->whereMonth('created_at', $month)->get();
            foreach ($withdrawals as $w) {
                $day = (int) $w->created_at->format('d');
                $chartWithdrawalData[$day] += $w->amount;
            }

            $chartTitle = "Tren Harian";
            $chartSubtitle = "Bulan " . Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');
            
        } elseif ($year !== 'all' && $month === 'all') {
            // MODE BULANAN (1 TAHUN)
            for ($i = 1; $i <= 12; $i++) {
                $chartDates[] = Carbon::createFromDate($year, $i, 1)->translatedFormat('M');
                $chartDepositData[$i] = 0;
                $chartWithdrawalData[$i] = 0;
            }

            $deposits = Deposit::with('details')->whereYear('created_at', $year)->get();
            foreach ($deposits as $d) {
                $m = (int) $d->created_at->format('m');
                $chartDepositData[$m] += $d->details->sum('qty');
            }

            $withdrawals = Withdrawal::whereYear('created_at', $year)->get();
            foreach ($withdrawals as $w) {
                $m = (int) $w->created_at->format('m');
                $chartWithdrawalData[$m] += $w->amount;
            }

            $chartTitle = "Tren Bulanan";
            $chartSubtitle = "Sepanjang Tahun " . $year;

        } else {
            // MODE TAHUNAN (5 TAHUN TERAKHIR)
            $currentY = Carbon::now()->year;
            for ($i = 4; $i >= 0; $i--) {
                $y = $currentY - $i;
                $chartDates[] = (string)$y;
                $chartDepositData[$y] = 0;
                $chartWithdrawalData[$y] = 0;
            }

            $deposits = Deposit::with('details')->whereYear('created_at', '>=', $currentY - 4)->get();
            foreach ($deposits as $d) {
                $y = (int) $d->created_at->format('Y');
                if (isset($chartDepositData[$y])) $chartDepositData[$y] += $d->details->sum('qty');
            }

            $withdrawals = Withdrawal::whereYear('created_at', '>=', $currentY - 4)->get();
            foreach ($withdrawals as $w) {
                $y = (int) $w->created_at->format('Y');
                if (isset($chartWithdrawalData[$y])) $chartWithdrawalData[$y] += $w->amount;
            }

            $chartTitle = "Tren Tahunan";
            $chartSubtitle = "Perbandingan 5 Tahun Terakhir";
        }

        // ==================================================
        // C. METRIK PERIODE (SESUAI FILTER)
        // ==================================================
        $totalPendapatanPengepul = (float) CollectorSale::where($applyDateFilter)->sum('total_amount'); 
        $totalModalBeli = (float) Deposit::where($applyDateFilter)->sum('total_amount'); 
        $keuntunganBersih = $totalPendapatanPengepul - $totalModalBeli;

        // Log Transaksi (Di Periode Tersebut)
        $recentDeposits = Deposit::with('nasabah:id,name')->where($applyDateFilter)->latest()->take(4)->get()
            ->map(fn($d) => [
                'id' => $d->id, 'nasabah_name' => $d->nasabah->name ?? 'User Dihapus', 
                'amount' => $d->total_amount, 'date' => $d->created_at->diffForHumans()
            ]);

        $recentWithdrawals = Withdrawal::with('nasabah:id,name')->where($applyDateFilter)->latest()->take(4)->get()
            ->map(fn($w) => [
                'id' => $w->id, 'nasabah_name' => $w->nasabah->name ?? 'User Dihapus', 
                'amount' => $w->amount, 'date' => $w->created_at->diffForHumans()
            ]);
            
        $recentSales = CollectorSale::where($applyDateFilter)->latest()->take(4)->get()
            ->map(fn($s) => [
                'id' => $s->id, 'pengepul_name' => 'Pengepul Umum',
                'amount' => $s->total_amount, 'date' => $s->created_at->diffForHumans()
            ]);

        // Top 5 Gudang
        $topInventories = Waste::withSum(['depositDetails as total_in' => $applyDateFilter], 'qty')
            ->withSum(['collectorSaleDetails as total_out' => $applyDateFilter], 'qty')
            ->get()
            ->map(function ($waste) {
                return [
                    'name' => $waste->name, 'category' => $waste->category, 'unit' => $waste->unit,
                    'stock' => max(0, (float)($waste->total_in ?? 0) - (float)($waste->total_out ?? 0)), 
                    'total_in' => (float)($waste->total_in ?? 0), 
                ];
            })
            ->sortByDesc('total_in')->take(4)->values();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_nasabah' => $totalNasabah,
                'total_saldo' => $totalSaldoNasabah,
                'kas_pengelola' => $kasPengelola,
                'total_stok' => $totalStokGudang,
                'total_pendapatan' => $totalPendapatanPengepul, 
                'untung_bersih' => $keuntunganBersih, 
            ],
            'chart' => [
                'title' => $chartTitle,
                'subtitle' => $chartSubtitle,
                'dates' => $chartDates,
                'seriesDeposit' => array_values($chartDepositData),
                'seriesWithdrawal' => array_values($chartWithdrawalData)
            ],
            'recentDeposits' => $recentDeposits,
            'recentWithdrawals' => $recentWithdrawals,
            'recentSales' => $recentSales,
            'topInventories' => $topInventories,
            'filters' => [
                'year' => $year,
                'month' => str_pad($month, 2, '0', STR_PAD_LEFT)
            ]
        ]);
    }

    public function exportExcel(Request $request)
    {
        $year = $request->input('year', 'all');
        $month = $request->input('month', 'all');

        $fileName = "Laporan_Transaksi_{$year}_{$month}.xlsx";

        return Excel::download(new LaporanDashboardExport($year, $month), $fileName);
    }
}