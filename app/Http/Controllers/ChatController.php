<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Show chat interface
     */
    public function show(Consultation $consultation)
    {
        $this->authorizeConsultation($consultation);

        $user = Auth::user();
        $viewName = $user->role === 'doctor' ? 'doctor.chat.show' : 'owner.chat.show';

        return view($viewName, compact('consultation'));
    }

    /**
     * Get messages for a consultation
     */
    public function getMessages(Consultation $consultation)
    {
        $this->authorizeConsultation($consultation);

        $messages = $consultation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $messages
        ]);
    }

    /**
     * Send a new message
     */
    public function sendMessage(Request $request, Consultation $consultation)
    {
        $this->authorizeConsultation($consultation);

        $validated = $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $message = new ChatMessage([
                'consultation_id' => $consultation->id,
                'message' => $validated['message'],
                'is_read' => false
            ]);

            // Use polymorphic relationship
            $message->sender()->associate(Auth::user());
            $message->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Message sent successfully',
                'data' => $message->load('sender')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(Consultation $consultation)
    {
        $this->authorizeConsultation($consultation);

        try {
            DB::beginTransaction();

            // Mark messages from other sender as read
            ChatMessage::where('consultation_id', $consultation->id)
                ->where('sender_id', '!=', Auth::id())
                ->where('is_read', false)
                ->update(['is_read' => true]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Messages marked as read'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to mark messages as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Authorize consultation access
     */
    private function authorizeConsultation(Consultation $consultation)
    {
        $user = Auth::user();
        
        if ($user->role === 'owner' && $consultation->owner_id !== $user->id) {
            abort(403, 'Unauthorized access to consultation');
        }
        
        if ($user->role === 'doctor' && $consultation->doctor_id !== $user->doctor->id) {
            abort(403, 'Unauthorized access to consultation');
        }

        // Check if consultation is paid
        if ($consultation->payment_status !== 'paid') {
            abort(403, 'Consultation payment is pending');
        }
    }
} 