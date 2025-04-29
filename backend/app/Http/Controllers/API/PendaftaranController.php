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
use Illuminate\Support\Facades\DB;

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

        // Buat direktori jika belum ada
        if (!Storage::disk('public')->exists('qr-codes')) {
            Storage::disk('public')->makeDirectory('qr-codes');
        }

        // Mulai database transaction
        DB::beginTransaction();

        try {
            // Ambil semua field dari event
            $formFields = EventFormField::where('event_id', $request->event_id)->get();

            // Validasi field wajib diisi
            foreach ($formFields as $field) {
                if ($field->is_required && empty($request->jawaban[(string)$field->id])) {
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

            // Simpan jawaban
            foreach ($request->jawaban as $fieldId => $answer) {
                FormJawaban::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'field_id' => $fieldId,
                    'jawaban' => $answer
                ]);
            }

            // Generate QR Code
            $qrContent = Str::uuid();
            $fileName = 'qr-codes/' . uniqid('qr_') . '.png';
            
            $qrImage = QrCode::format('svg')
                ->size(300)
                ->generate($qrContent);

            // Simpan sebagai file SVG
            Storage::disk('public')->put($fileName, $qrImage);

            // Verifikasi QR code tersimpan
            if (!Storage::disk('public')->exists($fileName)) {
                throw new \Exception('Gagal menyimpan QR code');
            }

            $pendaftaran->qr_code = $fileName;
            $pendaftaran->save();

            DB::commit();

            return response()->json([
                'message' => 'Pendaftaran berhasil',
                'pendaftaran' => $pendaftaran
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Pendaftaran gagal: ' . $e->getMessage(),
                'error_details' => 'Pastikan GD library aktif di PHP. Hubungi administrator jika masalah berlanjut.'
            ], 500);
        }
    }

    public function absen(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);

        $pendaftaran = Pendaftaran::where('qr_code', $request->qr_code)->first();

        if (!$pendaftaran) {
            return response()->json([
                'message' => 'QR Code tidak valid.'
            ], 404);
        }

        if ($pendaftaran->status_kehadiran === 'hadir') {
            return response()->json([
                'message' => 'Peserta sudah absen sebelumnya.'
            ], 400);
        }

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

    try {
        // Pastikan untuk memuat relasi user dan event
        $pendaftaran = Pendaftaran::with(['user', 'event'])->findOrFail($request->pendaftaran_id);

        if ($pendaftaran->status_kehadiran !== 'hadir') {
            return response()->json([
                'message' => 'Peserta belum absen. Tidak bisa generate sertifikat.'
            ], 403);
        }

        // Buat direktori jika belum ada
        if (!Storage::disk('public')->exists('sertifikats')) {
            Storage::disk('public')->makeDirectory('sertifikats');
        }

        $data = [
            'nama' => $pendaftaran->user->name, // Sekarang bisa diakses
            'event' => $pendaftaran->event->nama_event,
            'tanggal' => $pendaftaran->event->jadwal,
            'qr_code_path' => $pendaftaran->qr_code ? asset('storage/' . $pendaftaran->qr_code) : null,
        ];

        $pdf = Pdf::loadView('sertifikat', $data);
        $fileName = 'sertifikats/sertifikat_' . uniqid() . '.pdf';
        Storage::disk('public')->put($fileName, $pdf->output());

        // Simpan informasi sertifikat ke database (opsional)
        $pendaftaran->sertifikat()->create([
            'file_path' => $fileName,
            'tanggal_terbit' => now(),
        ]);

        return response()->json([
            'message' => 'Sertifikat berhasil dibuat!',
            'file_url' => asset('storage/' . $fileName)
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Gagal membuat sertifikat: ' . $e->getMessage(),
            'error_details' => 'Pastikan relasi user dan event sudah terdefinisi dengan benar.'
        ], 500);
    }
}
}