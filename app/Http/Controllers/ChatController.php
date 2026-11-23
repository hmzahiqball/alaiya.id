<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $conversation = Conversation::firstOrCreate(['user_id' => $user->id]);
        $conversation->load('messages.sender');

        return view('customer.chat', compact('conversation'));
    }

    public function send(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $user = $request->user();
        $conversation = Conversation::firstOrCreate(['user_id' => $user->id]);

        $conversation->messages()->create([
            'sender_id' => $user->id,
            'body' => $data['body'],
        ]);

        return back()->with('success', 'Pesan terkirim.');
    }

    public function adminIndex(Request $request): View
    {
        $conversations = Conversation::query()
            ->with(['user', 'messages.sender'])
            ->orderByDesc('updated_at')
            ->get();

        $selectedId = $request->integer('conversation_id');
        $activeConversation = $selectedId
            ? $conversations->firstWhere('id', $selectedId)
            : $conversations->first();

        return view('admin.chat', compact('conversations', 'activeConversation'));
    }

    public function adminSend(Request $request, Conversation $conversation): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $conversation->messages()->create([
            'sender_id' => $request->user()->id,
            'body' => $data['body'],
        ]);
        $conversation->touch();

        return redirect()->route('admin.chat.index', ['conversation_id' => $conversation->id])
            ->with('success', 'Balasan terkirim ke pelanggan.');
    }
}
