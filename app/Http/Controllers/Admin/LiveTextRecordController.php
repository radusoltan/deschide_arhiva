<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveText;
use App\Models\LiveTextRecord;
use Illuminate\Http\Request;

class LiveTextRecordController extends Controller
{
    // Obține toate LiveTextRecords pentru un LiveText specific
    public function index(LiveText $liveText)
    {
        return $liveText->records;
    }

    // Creează un nou LiveTextRecord pentru un LiveText specific
    public function store(Request $request, LiveText $livetext)
    {
        $validatedData = $request->validate([
            'content' => 'string',
            'tg_embed' => 'string',
            'title' => 'string',
        ]);

        $record = $livetext->records()->create([
//            'live_text_id' => $livetext->id,
            'title' => $validatedData['title'],
            'content' => $validatedData['content'],
            'tg_embed' => $validatedData['tg_embed'] ?? null,
            'published_at' => now(),
        ]);
        return response()->json($record);
    }

    // Afișează un LiveTextRecord specific
    public function show(LiveTextRecord $record)
    {
        return $record;
    }

    // Actualizează un LiveTextRecord specific
    public function update(Request $request, LiveTextRecord $record)
    {
        $validatedData = $request->validate([
            'content' => 'sometimes|required|string',
        ]);

        $record->update($validatedData);
        return response()->json($record);
    }

    // Șterge un LiveTextRecord specific
    public function destroy(LiveTextRecord $record)
    {
        $record->delete();
        return response()->json(['message' => 'LiveTextRecord deleted successfully']);
    }
}
