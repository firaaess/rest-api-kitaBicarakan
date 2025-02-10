<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Pengaduan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            margin: auto;
        }
        h2 {
            color: #007BFF;
            text-align: center;
        }
        .content {
            font-size: 16px;
            color: #333;
            line-height: 1.6;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px;
            color: #fff;
        }
        .status-proses { background-color: #ffc107; } /* Kuning */
        .status-diterima { background-color: #17a2b8; } /* Biru */
        .status-selesai { background-color: #28a745; } /* Hijau */
        .footer {
            margin-top: 20px;
            font-size: 14px;
            text-align: center;
            color: #777;
        }
        .app-name {
            font-weight: bold;
            color: #007BFF;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📢 Update Status Pengaduan</h2>
        <p class="content">
            <strong>Judul:</strong> {{ $judul }} <br>
            <strong>Status:</strong> 
            <span class="status-badge 
                {{ $status == 'proses' ? 'status-proses' : ($status == 'diterima' ? 'status-diterima' : 'status-selesai') }}">
                {{ strtoupper($status) }}
            </span> 
            <br>
            <strong>Isi Pengaduan:</strong> {{ $isi_pengaduan }} <br>
            <strong>Terakhir Diperbarui:</strong> {{ \Carbon\Carbon::parse($updated_at)->format('d M Y H:i') }}
        </p>
        
        <p class="content">Silakan cek sistem untuk melihat detail pengaduan.</p>
        
        <div class="footer">
            <p>Pesan ini dikirim oleh <span class="app-name">{{ config('app.name') }}</span></p>
        </div>
    </div>
</body>
</html>
