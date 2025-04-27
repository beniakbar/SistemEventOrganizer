<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat</title>
    <style>
        body { text-align: center; font-family: Arial, sans-serif; }
        h1 { font-size: 40px; margin-top: 50px; }
        p { font-size: 20px; }
    </style>
</head>
<body>
    <h1>SERTIFIKAT</h1>
    <p>Dengan ini menyatakan bahwa</p>
    <h2>{{ $nama }}</h2>
    <p>telah mengikuti event</p>
    <h3>{{ $event }}</h3>
    <p>pada tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</p>

    @if($qr_code_path)
        <div style="margin-top: 20px;">
            <img src="{{ $qr_code_path }}" width="100">
        </div>
    @endif
</body>
</html>
