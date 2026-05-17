@extends('layouts.main')

@section('title', 'Messages - LetUsDonate')

@section('content')
<div x-data="{
    search: '',
    newMessage: '',
    async sendMessage() {
        if (!this.newMessage.trim()) return;
        const form = this.$refs.messageForm;
        form.submit();
    }
}" class="h-[calc(100vh-80px)] flex bg-[var(--bgPrimary)] overflow-hidden">

    <!-- Sidebar - Conversation List -->
    <div class="w-[380px] border-r border-[var(--border)] flex flex-col bg-[var(--bgSecondary)] shrink-0">
        <div class="p-6 border-b border-[var(--border)]">
            <h1 class="text-2xl font-extrabold text-[var(--textPrimary)] m-0">Messages</h1>
            <div class="mt-4 relative flex items-center">
                <i class="ph ph-magnifying-glass absolute left-3 text-[var(--textMuted)]"></i>
                <input type="text" x-model="search" placeholder="Search conversations..." 
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border-none bg-[var(--bgTertiary)] text-[var(--textPrimary)] text-sm outline-none">
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-3 space-y-2">
            @forelse($conversations as $conv)
                @php
                    $otherPerson = $conv->buyer_id === $user->id ? $conv->seller : $conv->buyer;
                    $isSelected = isset($conversation) && $conversation->id === $conv->id;
                    $isUnread = $conv->messages->where('is_read', false)->where('sender_id', '!=', $user->id)->count() > 0;
                @endphp
                <a href="{{ route('chat.show', $conv->slug) }}" 
                   class="flex items-center gap-3 p-3 rounded-2xl transition-all {{ $isSelected ? 'bg-[var(--bgTertiary)]' : 'hover:bg-[var(--bgPrimary)]' }}">
                    <div class="w-14 h-14 rounded-2xl bg-[var(--bgTertiary)] overflow-hidden shrink-0 relative">
                        @if($conv->product->thumbnail)
                            <img src="{{ $conv->product->thumbnail->url }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center opacity-30"><i class="ph ph-package text-2xl"></i></div>
                        @endif
                        @if($isUnread)
                            <div class="absolute -top-1 -right-1 w-3.5 h-3.5 rounded-full bg-[var(--primary)] border-2 border-[var(--bgSecondary)]"></div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-center mb-1">
                            <span class="font-bold text-[var(--textPrimary)] text-sm truncate {{ $isUnread ? 'font-extrabold' : '' }}">{{ $otherPerson->name }}</span>
                            <span class="text-[10px] text-[var(--textMuted)]">{{ $conv->updated_at->diffForHumans(null, true) }}</span>
                        </div>
                        <div class="text-xs {{ $isUnread ? 'text-[var(--textPrimary)] font-bold' : 'text-[var(--textSecondary)]' }} truncate">
                            {{ $conv->product->title }}
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center py-10 text-[var(--textMuted)]">
                    <i class="ph ph-chat-circle text-5xl opacity-20 mb-3 block mx-auto"></i>
                    <p class="text-sm">No messages yet</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Main Chat Area -->
    <div class="flex-1 flex flex-col bg-[var(--bgPrimary)]">
        @if(isset($conversation))
            <!-- Chat Header -->
            <div class="px-6 py-4 border-b border-[var(--border)] bg-[var(--bgSecondary)] flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[var(--primary)] text-white flex items-center justify-center font-bold">
                        {{ strtoupper(substr($conversation->buyer_id === $user->id ? $conversation->seller->name : $conversation->buyer->name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-[var(--textPrimary)] m-0">
                            {{ $conversation->buyer_id === $user->id ? $conversation->seller->name : $conversation->buyer->name }}
                        </h2>
                        <div class="text-[11px] text-[var(--success)] flex items-center gap-1 font-bold">
                            <div class="w-1.5 h-1.5 rounded-full bg-[var(--success)]"></div>
                            Online
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="flex-1 overflow-y-auto p-6 scroll-smooth" id="message-container">
                <!-- Product Card -->
                <div class="flex flex-col items-center p-8 bg-[var(--bgSecondary)] rounded-3xl mb-8 border border-[var(--border)] text-center max-w-md mx-auto">
                    <div class="w-24 h-24 rounded-2xl bg-[var(--bgTertiary)] overflow-hidden mb-4 shadow-sm">
                        @if($conversation->product->thumbnail)
                            <img src="{{ $conversation->product->thumbnail->url }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center opacity-20"><i class="ph ph-package text-3xl"></i></div>
                        @endif
                    </div>
                    <h3 class="text-lg font-extrabold text-[var(--textPrimary)] mb-1">{{ $conversation->product->title }}</h3>
                    <div class="text-xl font-black text-[var(--primary)] mb-5">
                        {{ $conversation->product->price ? number_format($conversation->product->price).' MAD' : 'FREE' }}
                    </div>
                    <a href="{{ route('marketplace.show', $conversation->product->slug) }}" 
                       class="px-6 py-2.5 rounded-full bg-[var(--bgTertiary)] text-[var(--textPrimary)] border border-[var(--border)] font-bold text-xs hover:bg-[var(--border)] transition-all">
                        View Listing Details
                    </a>
                </div>

                <!-- Message List -->
                <div class="space-y-4">
                    @foreach($messages as $msg)
                        @php $mine = $msg->sender_id === $user->id; @endphp
                        <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }} items-end gap-2">
                            @if(!$mine)
                                <div class="w-7 h-7 rounded-full bg-[var(--bgTertiary)] flex items-center justify-center text-[10px] text-[var(--textMuted)] font-bold shrink-0">
                                    {{ strtoupper(substr($msg->sender->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="max-w-[70%] px-4 py-3 rounded-2xl shadow-sm relative {{ $mine ? 'bg-[var(--primary)] text-white rounded-br-md' : 'bg-white text-[var(--textPrimary)] rounded-bl-md border border-gray-100' }}">
                                <div class="text-sm leading-relaxed">{{ $msg->content }}</div>
                                <div class="text-[9px] mt-1.5 flex items-center justify-end gap-1 opacity-70">
                                    {{ $msg->created_at->format('H:i') }}
                                    @if($mine)
                                        <i class="ph {{ $msg->is_read ? 'ph-check-circle' : 'ph-check' }}"></i>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Message Input -->
            <form x-ref="messageForm" action="{{ route('chat.messages.send', $conversation->slug) }}" method="POST" 
                  class="p-5 bg-[var(--bgSecondary)] border-t border-[var(--border)] flex gap-3 items-center">
                @csrf
                <input type="text" name="content" x-model="newMessage" @keyup.enter="sendMessage" 
                       placeholder="Type a message..." 
                       class="flex-1 px-6 py-3.5 rounded-full border-2 border-[var(--border)] bg-[var(--bgPrimary)] text-[var(--textPrimary)] text-sm outline-none focus:border-[var(--primary)] transition-all">
                <button type="button" @click="sendMessage" :disabled="!newMessage.trim()" 
                        class="w-12 h-12 rounded-full bg-[var(--primary)] text-white flex items-center justify-center shadow-lg shadow-blue-500/30 hover:bg-[var(--primaryHover)] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="ph ph-paper-plane-right text-xl"></i>
                </button>
            </form>
        @else
            <div class="flex-1 flex flex-col items-center justify-center text-[var(--textMuted)] p-10">
                <div class="w-20 h-20 rounded-full bg-[var(--bgTertiary)] flex items-center justify-center mb-5">
                    <i class="ph ph-chat-circle text-4xl opacity-30"></i>
                </div>
                <h3 class="text-xl font-bold text-[var(--textPrimary)] mb-2">Select a conversation</h3>
                <p class="text-sm">Choose a chat from the left to start messaging</p>
            </div>
        @endif
    </div>
</div>

<script>
    const container = document.getElementById('message-container');
    if (container) container.scrollTop = container.scrollHeight;
</script>
@endsection
