<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pendaftaran;
use App\Models\FormJawaban;
use App\Models\Event;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

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

        // Generate QR Code (bisa pakai library SimpleSoftwareIO QrCode)
        $qrContent = Str::uuid(); // contoh generate random code unik
        $pendaftaran->qr_code = $qrContent;
        $pendaftaran->save();

        return response()->json([
            'message' => 'Pendaftaran berhasil',
            'pendaftaran' => $pendaftaran
        ]);
    }
}
