<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Consultation;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function show(Consultation $consultation)
    {
        // Verify that the consultation belongs to the authenticated owner
        if ($consultation->owner_id !== auth()->id()) {
            abort(403);
        }

        // Verify that the consultation is paid
        if ($consultation->payment_status !== 'paid') {
            return redirect()->route('owner.consultations.index')
                ->with('error', 'You need to complete the payment first.');
        }

        return view('owner.chat.show', compact('consultation'));
    }

    public function getMessages(Consultation $consultation)
    {
        // Verify ownership
        if ($consultation->owner_id !== auth()->id()) {
            abort(403);
        }

        $messages = ChatMessage::with('sender')
            ->where('consultation_id', $consultation->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function store(Request $request, Consultation $consultation)
    {
        // Verify ownership
        if ($consultation->owner_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $message = ChatMessage::create([
            'consultation_id' => $consultation->id,
            'sender_id' => auth()->id(),
            'message' => $validated['message']
        ]);

        return response()->json($message->load('sender'));
    }
} 