<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\CollectorSale;

class LaporanDashboardExport implements WithMultipleSheets
{
    protected $year;
    protected $month;

    public function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function sheets(): array
    {
        $applyFilter = function ($query) {
            if ($this->year !== 'all') {
                $query->whereYear('created_at', $this->year);
            }
            if ($this->month !== 'all') {
                $query->whereMonth('created_at', $this->month);
            }
        };

        // 1. Ambil Data
        $deposits = Deposit::with('nasabah')->where($applyFilter)->get();
        $withdrawals = Withdrawal::with('nasabah')->where($applyFilter)->get();
        $sales = CollectorSale::where($applyFilter)->get();

        $periode = "Bulan: " . ($this->month === 'all' ? 'Semua' : $this->month) . " | Tahun: " . ($this->year === 'all' ? 'Semua' : $this->year);

        // 2. Kalkulasi Data Master Sheet
        $masterData = [
            'periode' => $periode,
            'total_deposit' => $deposits->sum('total_amount'),
            'total_withdrawal' => $withdrawals->sum('amount'),
            'total_sales' => $sales->sum('total_amount'),
            'count_deposit' => $deposits->count(),
            'count_withdrawal' => $withdrawals->count(),
            'count_sales' => $sales->count(),
        ];
        $masterData['untung_bersih'] = $masterData['total_sales'] - $masterData['total_deposit'];

        // 3. Daftarkan Semua Sheet
        return [
            // Sheet 1: Master Ringkasan
            new LaporanSheetExport('exports.sheets.master', $masterData, 'Master Data'),
            
            // Sheet 2: Detail Setoran
            new LaporanSheetExport('exports.sheets.setoran', ['deposits' => $deposits, 'periode' => $periode], 'Detail Setoran'),
            
            // Sheet 3: Detail Penarikan
            new LaporanSheetExport('exports.sheets.penarikan', ['withdrawals' => $withdrawals, 'periode' => $periode], 'Detail Penarikan'),
            
            // Sheet 4: Detail Penjualan
            new LaporanSheetExport('exports.sheets.penjualan', ['sales' => $sales, 'periode' => $periode], 'Detail Penjualan'),
        ];
    }
}