<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIChatbotController extends Controller
{
    public function index()
    {
        return view('owner.chatbot.index');
    }

    public function chat(Request $request)
    {
        Log::info('Chat request received', [
            'user_id' => auth()->id(),
            'message' => $request->message
        ]);

        $request->validate([
            'message' => 'required|string'
        ]);

        try {
            Log::info('Sending request to Groq API', [
                'api_key_exists' => !empty(env('GROQ_API_KEY')),
                'message' => $request->message
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                'Content-Type' => 'application/json'
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a veterinary AI assistant. Help pet owners with general pet care advice, but always remind them to consult a real veterinarian for serious medical issues. Format your responses using markdown for better readability. Use bullet points for lists, bold for important points, and code blocks for specific instructions or measurements.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $request->message
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 1000
            ]);

            if (!$response->successful()) {
                Log::error('Groq API error', [
                    'status' => $response->status(),
                    'body' => $response->json()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get response from AI API: ' . $response->status()
                ], 500);
            }

            $responseData = $response->json();
            
            if (!isset($responseData['choices'][0]['message']['content'])) {
                Log::error('Unexpected Groq API response format', [
                    'response' => $responseData
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid response format from AI API'
                ], 500);
            }

            $aiResponse = $responseData['choices'][0]['message']['content'];
            
            Log::info('Successfully received response from Groq API', [
                'response_length' => strlen($aiResponse)
            ]);

            // Store response in session
            session()->flash('response', $aiResponse);

            return response()->json([
                'success' => true,
                'message' => $aiResponse
            ]);

        } catch (\Exception $e) {
            Log::error('Error in chat endpoint', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get response from AI. Please try again later.'
            ], 500);
        }
    }
} 