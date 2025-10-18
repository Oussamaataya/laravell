<?php

namespace App\Http\Controllers;

use App\Services\AIAssistantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AIAssistantController extends Controller
{
    protected $aiService;

    public function __construct(AIAssistantService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
        $messages = $this->aiService->getChatHistory(Auth::id());
        return view('assistant.chat', compact('messages'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        try {
            $response = $this->aiService->sendMessage(
                $request->message,
                Auth::id()
            );

            return response()->json([
                'message' => $response->content,
                'timestamp' => $response->created_at->format('H:i'),
                'success' => true
            ]);
        } catch (\Exception $e) {
            Log::error('AI Assistant Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Désolé, une erreur est survenue. Veuillez réessayer.',
                'success' => false
            ], 500);
        }
    }

    public function getHistory()
    {
        $messages = $this->aiService->getChatHistory(Auth::id());
        return response()->json($messages);
    }
}