@extends('layouts.main')

@section('title', 'AI FAQ Assistant - LetUsDonate')

@section('content')
<div class="max-w-2xl mx-auto py-12 px-4">
    <div class="bg-white rounded-3xl shadow-xl border border-[var(--border)] overflow-hidden flex flex-col h-[600px]">
        <!-- Header -->
        <div class="p-6 bg-[var(--primary)] text-white flex items-center gap-4">
            <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                <i class="ph ph-robot text-2xl"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold">Chat with Chati</h3>
                <p class="text-xs text-white/80">AI Assistant • LetUsDonate.uk</p>
            </div>
        </div>

        <!-- Chat Area -->
        <div id="chatMessages" class="flex-1 overflow-y-auto p-6 space-y-4 flex flex-col bg-[var(--bgSecondary)]">
            <div class="self-start bg-white p-4 rounded-2xl rounded-tl-none border border-[var(--border)] shadow-sm text-sm text-[var(--textPrimary)]">
                Hello I'm Chati! Ask me anything about LetUsDonate.uk
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-4 bg-white border-t border-[var(--border)]">
            <form id="chatForm" class="flex gap-2">
                <input type="text" id="chatInput" placeholder="Ask a question..." class="flex-1 px-4 py-3 bg-[var(--bgSecondary)] border border-[var(--border)] rounded-xl outline-none focus:border-[var(--primary)] transition-colors">
                <button type="submit" class="w-12 h-12 bg-[var(--primary)] text-white rounded-xl flex items-center justify-center hover:bg-[var(--primaryHover)] transition-all">
                    <i class="ph ph-paper-plane-right-bold text-xl"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('chatForm');
        const input = document.getElementById('chatInput');
        const container = document.getElementById('chatMessages');

        function appendMessage(text, sender) {
            const div = document.createElement('div');
            div.className = sender === 'user' 
                ? 'self-end bg-[var(--primary)] text-white p-4 rounded-2xl rounded-tr-none shadow-sm text-sm'
                : 'self-start bg-white p-4 rounded-2xl rounded-tl-none border border-[var(--border)] shadow-sm text-sm text-[var(--textPrimary)]';
            div.textContent = text;
            container.appendChild(div);
            container.scrollTop = container.scrollHeight;
            return div;
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const question = input.value.trim();
            if (!question) return;

            appendMessage(question, 'user');
            input.value = '';

            const loadingMsg = appendMessage('Thinking...', 'bot');
            
            try {
                const response = await fetch('/api/ask-faq', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ question: question })
                });

                const data = await response.json();
                loadingMsg.remove();
                appendMessage(data.answer || "Sorry, I couldn't find an answer.", 'bot');
            } catch (error) {
                loadingMsg.remove();
                appendMessage("Sorry, there was a problem connecting to the AI.", 'bot');
            }
        });
    });
</script>
@endsection
