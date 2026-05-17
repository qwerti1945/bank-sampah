<table>
    <tr><td colspan="4"><b>DETAIL TRANSAKSI PENARIKAN SALDO</b></td></tr>
    <tr><td colspan="4">Periode: {{ $periode }}</td></tr>
    <tr></tr>
    <tr>
        <th style="background-color: #f43f5e; color: #ffffff; font-weight: bold;">No</th>
        <th style="background-color: #f43f5e; color: #ffffff; font-weight: bold;">Waktu Transaksi</th>
        <th style="background-color: #f43f5e; color: #ffffff; font-weight: bold;">Nama Nasabah</th>
        <th style="background-color: #f43f5e; color: #ffffff; font-weight: bold;">Nominal Ditarik (Rp)</th>
    </tr>
    @foreach($withdrawals as $index => $w)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $w->created_at->format('Y-m-d H:i:s') }}</td>
        <td>{{ $w->nasabah->name ?? 'User Dihapus' }}</td>
        <td>{{ $w->amount }}</td>
    </tr>
    @endforeach
</table>