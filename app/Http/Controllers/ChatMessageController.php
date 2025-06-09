<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatMessageController extends Controller
{
    /**
     * Store a newly created message.
     */
    public function store(Request $request, Consultation $consultation)
    {
        // Check if user has access to this consultation
        $user = $request->user();
        if (
            ($user->role === 'owner' && $consultation->owner_id !== $user->id) ||
            ($user->role === 'doctor' && $consultation->doctor_id !== $user->doctor->id)
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to send message in this consultation'
            ], 403);
        }

        $validated = $request->validate([
            'message' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $message = ChatMessage::create([
                'consultation_id' => $consultation->id,
                'sender_id' => $user->id,
                'message' => $validated['message']
            ]);

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
     * Remove the specified message.
     */
    public function destroy(Request $request, Consultation $consultation, ChatMessage $message)
    {
        // Users can only delete their own messages
        if ($message->sender_id !== $request->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to delete this message'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $message->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Message deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete message',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 