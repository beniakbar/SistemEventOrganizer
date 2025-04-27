<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::latest()->get();
        return response()->json($events);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_event' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'jadwal' => 'required|date',
            'lokasi' => 'nullable|string|max:150',
            'kuota' => 'nullable|integer',
            'jenis' => 'required|in:gratis,berbayar',
            'is_sertifikat' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $event = Event::create($request->all());

        return response()->json([
            'message' => 'Event created successfully',
            'data' => $event
        ]);
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);
        return response()->json($event);
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $request->validate([
            'nama_event' => 'sometimes|required|string|max:150',
            'deskripsi' => 'nullable|string',
            'jadwal' => 'sometimes|required|date',
            'lokasi' => 'nullable|string|max:150',
            'kuota' => 'nullable|integer',
            'jenis' => 'sometimes|required|in:gratis,berbayar',
            'is_sertifikat' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $event->update($request->all());

        return response()->json([
            'message' => 'Event updated successfully',
            'data' => $event
        ]);
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json([
            'message' => 'Event deleted successfully'
        ]);
    }
}
