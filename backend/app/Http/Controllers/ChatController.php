<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;
use App\Events\MessageRead;

use App\Services\ChatService;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function index()
    {
        $user = Auth::user();
        $conversations = $this->chatService->getUserConversations($user);
        return view('chat.index', compact('user', 'conversations'));
    }

    public function show(Conversation $conversation)
    {
        $user = Auth::user();
        if ($conversation->buyer_id !== $user->id && $conversation->seller_id !== $user->id) {
            abort(403);
        }
        $conversations = $this->chatService->getUserConversations($user);
        $messages = $this->chatService->getMessages($conversation);
        $conversation->load(['product.thumbnail', 'buyer', 'seller']);
        
        return view('chat.index', compact('user', 'conversations', 'conversation', 'messages'));
    }

    /**
     * Get or create a conversation between buyer and seller for a product.
     */
    public function getOrCreateConversation(Product $product)
    {
        $buyerId = Auth::id();
        $sellerId = $product->user_id;

        // Can't chat with yourself
        if ($buyerId === $sellerId) {
            return back()->with('error', 'Cannot chat with yourself');
        }

        $conversation = $this->chatService->getOrCreateConversation($product);

        return redirect()->route('chat.show', $conversation);
    }

    /**
     * Get messages for a conversation.
     */
    public function getMessages(Conversation $conversation)
    {
        // Verify user is part of this conversation
        $userId = Auth::id();
        if ($conversation->buyer_id !== $userId && $conversation->seller_id !== $userId) {
            abort(403);
        }

        $messages = $this->chatService->getMessages($conversation);
        $user = Auth::user();
        $conversations = $this->chatService->getUserConversations($user);

        return view('chat.index', compact('user', 'conversations', 'conversation', 'messages'));
    }

    /**
     * Send a message in a conversation.
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        // Verify user is part of this conversation
        $userId = Auth::id();
        if ($conversation->buyer_id !== $userId && $conversation->seller_id !== $userId) {
            abort(403);
        }

        $this->chatService->sendMessage($conversation, $request->input('content'));

        return back()->with('success', 'Message sent');
    }

    /**
     * Get user's conversations (both as buyer and seller).
     */
    public function getUserConversations()
    {
        $user = Auth::user();
        $conversations = $this->chatService->getUserConversations($user);

        return view('chat.index', compact('user', 'conversations'));
    }

    /**
     * Mark messages as read.
     */
    public function markAsRead(Conversation $conversation)
    {
        $userId = Auth::id();

        if ($conversation->buyer_id !== $userId && $conversation->seller_id !== $userId) {
            abort(403);
        }

        $this->chatService->markAsRead($conversation);

        return back()->with('success', 'Messages marked as read');
    }
}
