<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pendaftaran;
use App\Models\FormJawaban;
use App\Models\Event;
use App\Models\EventFormField;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PendaftaranController extends Controller
{
    public function showForm($id)
    {
        $event = Event::with('formFields')->findOrFail($id);

        return response()->json([
            'event' => $event->nama_event,
            'fields' => $event->formFields
        ]);
    }

    public function daftar(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'jawaban' => 'required|array'
        ]);

        // Ambil semua field dari event
        $formFields = EventFormField::where('event_id', $request->event_id)->get();

        // Validasi semua field yang wajib diisi
        foreach ($formFields as $field) {
            if ($field->is_required && empty($request->jawaban[$field->id])) {
                return response()->json([
                    'message' => 'Field "' . $field->label . '" wajib diisi.'
                ], 422);
            }
        }

        // Buat pendaftaran
        $pendaftaran = Pendaftaran::create([
            'user_id' => $request->user()->id,
            'event_id' => $request->event_id,
        ]);

        // Simpan semua jawaban peserta
        foreach ($request->jawaban as $fieldId => $answer) {
            FormJawaban::create([
                'pendaftaran_id' => $pendaftaran->id,
                'field_id' => $fieldId,
                'jawaban' => $answer
            ]);
        }

        // Generate random UUID untuk isi QR Code
        $qrContent = Str::uuid();

        // Nama file unik QR Code
        $fileName = 'qr-codes/' . uniqid('qr_') . '.png';

        // Buat QR Code dan simpan ke storage/app/public/qr-codes/
        QrCode::format('png')->size(300)->generate($qrContent, storage_path('app/public/' . $fileName));

        // Simpan nama file ke kolom qr_code
        $pendaftaran->qr_code = $fileName;
        $pendaftaran->save();

        return response()->json([
            'message' => 'Pendaftaran berhasil',
            'pendaftaran' => $pendaftaran
        ]);
    }

    public function absen(Request $request)
    {
    $request->validate([
        'qr_code' => 'required|string'
    ]);

    // Cari pendaftaran berdasarkan QR Code
    $pendaftaran = Pendaftaran::where('qr_code', $request->qr_code)->first();

    if (!$pendaftaran) {
        return response()->json([
            'message' => 'QR Code tidak valid.'
        ], 404);
    }

    // Cek apakah sudah pernah absen
    if ($pendaftaran->status_kehadiran === 'hadir') {
        return response()->json([
            'message' => 'Peserta sudah absen sebelumnya.'
        ], 400);
    }

    // Tandai sebagai hadir
    $pendaftaran->status_kehadiran = 'hadir';
    $pendaftaran->save();

    return response()->json([
        'message' => 'Absensi berhasil. Selamat datang!',
        'pendaftaran' => $pendaftaran
    ]);
    }

    public function generateSertifikat(Request $request)
{
    $request->validate([
        'pendaftaran_id' => 'required|exists:pendaftaran,id',
    ]);

    // Ambil pendaftaran
    $pendaftaran = Pendaftaran::with('user', 'event')->findOrFail($request->pendaftaran_id);

    // Cek apakah sudah absen
    if ($pendaftaran->status_kehadiran !== 'hadir') {
        return response()->json([
            'message' => 'Peserta belum absen. Tidak bisa generate sertifikat.'
        ], 403);
    }

    // Data yang akan dimasukkan ke sertifikat
    $data = [
        'nama' => $pendaftaran->user->name,
        'event' => $pendaftaran->event->nama_event,
        'tanggal' => $pendaftaran->event->jadwal,
        'qr_code_path' => $pendaftaran->qr_code ? asset('storage/' . $pendaftaran->qr_code) : null,
    ];

    // Buat PDF dari view
    $pdf = Pdf::loadView('sertifikat', $data);

    // Simpan PDF
    $fileName = 'sertifikats/sertifikat_' . uniqid() . '.pdf';
    Storage::disk('public')->put($fileName, $pdf->output());

    // Update sertifikat di database (opsional, kalau kamu punya kolom `sertifikat`)
    // $pendaftaran->sertifikat = $fileName;
    // $pendaftaran->save();

    return response()->json([
        'message' => 'Sertifikat berhasil dibuat!',
        'file_url' => asset('storage/' . $fileName)
    ]);
}
}
