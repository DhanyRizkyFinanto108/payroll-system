<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembayaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 16px;
            color: #555;
        }
        .info-container {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            width: 200px;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
        }
        .divider {
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">BUKTI PEMBAYARAN</div>
        <div class="subtitle">PT. Sejahtera Indonesia</div>
    </div>

    <div class="info-container">
        <div class="info-row">
            <div class="info-label">ID Pembayaran:</div>
            <div class="info-value">{{ $riwayatPembayaran->id_pembayaran }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Tanggal Pembayaran:</div>
            <div class="info-value">{{ date('d F Y', strtotime($riwayatPembayaran->waktu)) }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Metode Pembayaran:</div>
            <div class="info-value">{{ $riwayatPembayaran->metode }}</div>
        </div>
    </div>

    <div class="divider"></div>

    <div class="info-container">
        <div class="info-row">
            <div class="info-label">Nama Karyawan:</div>
            <div class="info-value">{{ $riwayatPembayaran->karyawan->nama }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Jabatan:</div>
            <div class="info-value">{{ $riwayatPembayaran->karyawan->jabatan }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Komponen</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Gaji Pokok</td>
                <td>Rp {{ number_format($riwayatPembayaran->karyawan->gaji_pokok, 0, ',', '.') }}</td>
            </tr>
            <!-- Additional components would be dynamically added here -->
            <tr>
                <td><strong>Total</strong></td>
                <td><strong>Rp {{ number_format($riwayatPembayaran->karyawan->gaji_pokok, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dihasilkan secara otomatis dan sah tanpa tanda tangan.
    </div>
</body>
</html>
