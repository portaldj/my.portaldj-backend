<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\DjEquipment;
use App\Models\EquipmentModel;
use App\Services\AiService;
use Illuminate\Support\Facades\Gate;

class AssistantController extends Controller
{
    protected $aiService;

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Display a list of the user's equipment that has documentation available.
     */
    public function index(Request $request)
    {
        // Check PRO Access
        $assistantIsPro = \App\Models\Setting::where('key', 'assistant_is_pro')->value('value') === '1';
        if ($assistantIsPro && !$request->user()->is_pro) {
            return redirect()->route('subscription.index')->with('message', 'The AI Assistant is for PRO members only.');
        }

        // Get user's equipment where the related model has documentation
        $equipments = DjEquipment::where('user_id', $request->user()->id)
            ->whereNotNull('equipment_model_id')
            ->whereHas('equipmentModel', function ($query) {
                $query->whereNotNull('documentation')->where('documentation', '!=', '');
            })
            ->with(['equipmentModel', 'brand', 'type'])
            ->get();

        return Inertia::render('Assistant/Index', [
            'equipments' => $equipments
        ]);
    }

    /**
     * Display the chat interface for a specific equipment model.
     */
    public function show(Request $request, EquipmentModel $model)
    {
        // Check PRO Access
        $assistantIsPro = \App\Models\Setting::where('key', 'assistant_is_pro')->value('value') === '1';
        if ($assistantIsPro && !$request->user()->is_pro) {
            return redirect()->route('subscription.index')->with('message', 'The AI Assistant is for PRO members only.');
        }

        // Ensure the user actually owns this equipment
        $ownsEquipment = DjEquipment::where('user_id', $request->user()->id)
            ->where('equipment_model_id', $model->id)
            ->exists();

        if (!$ownsEquipment && !$request->user()->hasRole('Admin')) {
            abort(403, "You don't own this equipment.");
        }

        if (empty($model->documentation)) {
            return redirect()->route('assistant.index')->with('error', 'This equipment has no documentation available.');
        }

        return Inertia::render('Assistant/Chat', [
            'model' => $model->load('brand', 'type'),
            'openai_enabled' => \App\Models\Setting::where('key', 'openai_enabled')->value('value') ?? '1',
            'gemini_enabled' => \App\Models\Setting::where('key', 'gemini_enabled')->value('value') ?? '1',
        ]);
    }

    /**
     * Handle the chat message.
     */
    public function chat(Request $request, EquipmentModel $model)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'provider' => 'required|in:openai,gemini',
        ]);

        // Check PRO Access
        $assistantIsPro = \App\Models\Setting::where('key', 'assistant_is_pro')->value('value') === '1';
        if ($assistantIsPro && !$request->user()->is_pro) {
            return response()->json(['error' => 'PRO subscription required.'], 403);
        }

        // Security check
        $ownsEquipment = DjEquipment::where('user_id', $request->user()->id)
            ->where('equipment_model_id', $model->id)
            ->exists();

        if (!$ownsEquipment && !$request->user()->hasRole('Admin')) {
            abort(403, "You don't own this equipment.");
        }

        // Build System Prompt
        $documentation = $model->documentation;
        $equipmentName = $model->brand->name . ' ' . $model->name;

        $systemPrompt = "You are the {$equipmentName}. You are a helpful assistant for a DJ who owns this equipment. 
        Use the following documentation to answer the user's question. 
        If the answer is not found in the documentation, state that you don't have that information based on the manual.
        Do not make up features that are not in the text.
        
        Documentation:
        {$documentation}
        ";

        // Call AI Service
        $response = $this->aiService->chat(
            $request->provider,
            $systemPrompt,
            $request->message,
            $request->user()
        );

        if (!$response) {
            return response()->json(['error' => 'Failed to get response from AI provider.'], 500);
        }

        return response()->json(['reply' => $response]);
    }
}
