<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $consultations = Consultation::with(['owner', 'pet'])
            ->where('doctor_id', auth()->user()->doctor->id)
            ->where('payment_status', 'paid')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('doctor.chat.index', compact('consultations'));
    }

    public function show(Consultation $consultation)
    {
        // Check if the consultation belongs to the authenticated doctor
        if ($consultation->doctor_id !== auth()->user()->doctor->id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the consultation is paid
        if ($consultation->payment_status !== 'paid') {
            return redirect()->route('doctor.consultations.index')
                ->with('error', 'This consultation has not been paid for yet.');
        }

        return view('doctor.chat.show', compact('consultation'));
    }

    public function getMessages(Consultation $consultation)
    {
        // Check if the consultation belongs to the authenticated doctor
        if ($consultation->doctor_id !== auth()->user()->doctor->id) {
            abort(403, 'Unauthorized action.');
        }

        // Get messages and mark them as read
        $messages = $consultation->messages()
            ->with(['sender:id,name'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark unread messages as read
        $consultation->messages()
            ->where('is_read', false)
            ->where('sender_id', '!=', auth()->id())
            ->update(['is_read' => true]);

        return response()->json($messages);
    }

    public function storeMessage(Request $request, Consultation $consultation)
    {
        // Check if the consultation belongs to the authenticated doctor
        if ($consultation->doctor_id !== auth()->user()->doctor->id) {
            abort(403, 'Unauthorized action.');
        }

        // Validate request
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        // Create message
        $message = $consultation->messages()->create([
            'sender_id' => auth()->id(),
            'message' => $request->message,
            'is_read' => false,
        ]);

        // Load the sender relationship
        $message->load('sender:id,name');

        return response()->json($message);
    }
} 