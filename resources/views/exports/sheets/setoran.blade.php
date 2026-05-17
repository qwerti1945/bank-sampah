<table>
    <tr><td colspan="4"><b>DETAIL TRANSAKSI SETORAN NASABAH</b></td></tr>
    <tr><td colspan="4">Periode: {{ $periode }}</td></tr>
    <tr></tr>
    <tr>
        <th style="background-color: #10b981; color: #ffffff; font-weight: bold;">No</th>
        <th style="background-color: #10b981; color: #ffffff; font-weight: bold;">Waktu Transaksi</th>
        <th style="background-color: #10b981; color: #ffffff; font-weight: bold;">Nama Nasabah</th>
        <th style="background-color: #10b981; color: #ffffff; font-weight: bold;">Nominal Setoran (Rp)</th>
    </tr>
    @foreach($deposits as $index => $d)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $d->created_at->format('Y-m-d H:i:s') }}</td>
        <td>{{ $d->nasabah->name ?? 'User Dihapus' }}</td>
        <td>{{ $d->total_amount }}</td>
    </tr>
    @endforeach
</table>