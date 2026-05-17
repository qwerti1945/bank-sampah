<table>
    <tr>
        <td colspan="2" style="font-size: 16px; font-weight: bold; text-align: center;">MASTER SHEET: RINGKASAN EKSEKUTIF</td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: center;">Periode: {{ $periode }}</td>
    </tr>
    <tr></tr>

    <!-- RINGKASAN FINANSIAL -->
    <tr>
        <td colspan="2" style="background-color: #1f2937; color: #ffffff; font-weight: bold;">RINGKASAN FINANSIAL (Rp)</td>
    </tr>
    <tr>
        <td>Omzet Penjualan ke Pengepul (Pemasukan)</td>
        <td style="font-weight: bold; color: #10b981;">{{ $total_sales }}</td>
    </tr>
    <tr>
        <td>Total Modal Beli Sampah (Setoran Nasabah)</td>
        <td style="font-weight: bold; color: #f59e0b;">{{ $total_deposit }}</td>
    </tr>
    <tr>
        <td>Keuntungan Bersih (Laba)</td>
        <td style="font-weight: bold; color: #3b82f6;">{{ $untung_bersih }}</td>
    </tr>
    <tr>
        <td>Total Penarikan Saldo oleh Nasabah</td>
        <td style="font-weight: bold; color: #f43f5e;">{{ $total_withdrawal }}</td>
    </tr>
    <tr></tr>

    <!-- RINGKASAN LALU LINTAS TRANSAKSI -->
    <tr>
        <td colspan="2" style="background-color: #1f2937; color: #ffffff; font-weight: bold;">LALU LINTAS TRANSAKSI (Frekuensi)</td>
    </tr>
    <tr>
        <td>Total Transaksi Setor Sampah</td>
        <td>{{ $count_deposit }} Kali</td>
    </tr>
    <tr>
        <td>Total Transaksi Penarikan Tunai</td>
        <td>{{ $count_withdrawal }} Kali</td>
    </tr>
    <tr>
        <td>Total Transaksi Jual ke Pengepul</td>
        <td>{{ $count_sales }} Kali</td>
    </tr>
</table>