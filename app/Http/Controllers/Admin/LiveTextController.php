<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveText;
use Illuminate\Http\Request;

class LiveTextController extends Controller
{
    // Obține toate LiveText-urile
    public function index()
    {
        return LiveText::with('records')->get();
    }

    // Creează un nou LiveText
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'description',
            'name' => 'required|string|max:255',
        ]);

        $liveText = LiveText::create($validatedData);
        return response()->json($liveText);
    }

    // Afișează un LiveText specific
    public function show(LiveText $livetext)
    {
        return response()->json(
            $livetext->load(['records' => function ($query) {
                $query->orderBy('published_at', 'desc');
            }])
        );
    }

    // Actualizează un LiveText specific
    public function update(Request $request, LiveText $liveText)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
        ]);

        $liveText->update($validatedData);
        return response()->json($liveText);
    }

    // Șterge un LiveText specific
    public function destroy(LiveText $liveText)
    {
        $liveText->delete();
        return response()->json(['message' => 'LiveText deleted successfully']);
    }
}
