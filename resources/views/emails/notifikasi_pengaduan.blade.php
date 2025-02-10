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
        }
        .pengaduan-image {
            display: block;
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            border-radius: 8px;
            margin: 10px 0;
        }
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
        <h2>📢 Notifikasi Pengaduan Baru</h2>
        <p class="content">
            <strong>Judul:</strong> {{ $judul }} <br>
            <strong>Deskripsi:</strong> {{ $deskripsi }} <br>
            <strong>Tanggal:</strong> {{ $tanggal }}
        </p>
        
        <!-- Tampilkan Foto Pengaduan Kalau Ada -->
        @if (!empty($foto))
        <img src="{{ $foto }}" alt="Foto Pengaduan" class="pengaduan-image" style="max-width: 100%;">
        @endif
        
        <p class="content">Silakan cek sistem untuk menindaklanjuti pengaduan ini.</p>
        <div class="footer">
            <p>Pesan ini dikirim oleh <span class="app-name">Kita Bicarakan</span></p>
        </div>
    </div>
</body>
</html>