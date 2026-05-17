<table>
    <tr><td colspan="4"><b>DETAIL PENJUALAN KE PENGEPUL BESAR</b></td></tr>
    <tr><td colspan="4">Periode: {{ $periode }}</td></tr>
    <tr></tr>
    <tr>
        <th style="background-color: #3b82f6; color: #ffffff; font-weight: bold;">No</th>
        <th style="background-color: #3b82f6; color: #ffffff; font-weight: bold;">Waktu Transaksi</th>
        <th style="background-color: #3b82f6; color: #ffffff; font-weight: bold;">Pengepul</th>
        <th style="background-color: #3b82f6; color: #ffffff; font-weight: bold;">Total Pendapatan (Rp)</th>
    </tr>
    @foreach($sales as $index => $s)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $s->created_at->format('Y-m-d H:i:s') }}</td>
        <td>Pengepul Umum</td>
        <td>{{ $s->total_amount }}</td>
    </tr>
    @endforeach
</table>