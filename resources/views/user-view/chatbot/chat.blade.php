<div id="chatbot-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000; font-family: 'Inter', sans-serif;">
    <!-- Chat Toggle Button -->
    <button id="chat-toggle" style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: white; border: none; border-radius: 50%; width: 60px; height: 60px; cursor: pointer; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); display: flex; align-items: center; justify-content: center; transition: transform 0.2s;">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/></svg>
    </button>

    <!-- Chat Window -->
    <div id="chat-window" style="display: none; width: 380px; height: 520px; background: white; border-radius: 15px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); flex-direction: column; overflow: hidden; margin-bottom: 20px;">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: white; padding: 15px; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 10px; height: 10px; background: #4ade80; border-radius: 50%;"></div>
                <span style="font-weight: 600;">Family AI Assistant</span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <button id="chat-clear" title="New conversation" style="background: rgba(255,255,255,0.2); border: none; color: white; cursor: pointer; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                </button>
                <button id="chat-close" style="background: none; border: none; color: white; cursor: pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Messages Area -->
        <div id="chat-messages" style="flex: 1; padding: 15px; overflow-y: auto; background: #f9fafb; display: flex; flex-direction: column; gap: 10px;">
            <div style="background: #e5e7eb; padding: 10px 15px; border-radius: 15px 15px 15px 0; max-width: 80%; align-self: flex-start; font-size: 14px; line-height: 1.5;">
                Hello! I'm your Family AI Assistant. I can:<br><br>
                <b>Answer questions</b> about your family tree<br>
                <b>Add, update, or remove</b> members (e.g. "mark John as deceased")<br>
                <b>Open pages</b> — say "take me to photos" or "open messages"
            </div>
        </div>

        <!-- Input Area -->
        <div style="padding: 15px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px;">
            <input type="text" id="chat-input" placeholder="Type your message..." style="flex: 1; border: 1px solid #d1d5db; border-radius: 20px; padding: 8px 15px; outline: none; font-size: 14px;" autocomplete="off">
            <button id="chat-send" style="background: #6366f1; color: white; border: none; border-radius: 50%; width: 35px; height: 35px; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatToggle = document.getElementById('chat-toggle');
    const chatWindow = document.getElementById('chat-window');
    const chatClose = document.getElementById('chat-close');
    const chatClear = document.getElementById('chat-clear');
    const chatSend = document.getElementById('chat-send');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');

    let conversationHistory = [];
    let isSending = false;

    chatToggle.onclick = () => {
        chatWindow.style.display = chatWindow.style.display === 'none' ? 'flex' : 'none';
        chatToggle.style.display = chatWindow.style.display === 'none' ? 'flex' : 'none';
        if (chatWindow.style.display === 'flex') chatInput.focus();
    };

    chatClose.onclick = () => {
        chatWindow.style.display = 'none';
        chatToggle.style.display = 'flex';
    };

    chatClear.onclick = () => {
        conversationHistory = [];
        chatMessages.innerHTML = '';
        addMessage("Hello! I'm your Family AI Assistant.\n\nAsk about your family, add or update members, mark someone deceased, or say \"open photos\" to navigate.", false);
    };

    const addMessage = (text, isUser = false) => {
        const div = document.createElement('div');
        div.style.padding = '10px 15px';
        div.style.borderRadius = isUser ? '15px 15px 0 15px' : '15px 15px 15px 0';
        div.style.maxWidth = '85%';
        div.style.alignSelf = isUser ? 'flex-end' : 'flex-start';
        div.style.background = isUser ? '#6366f1' : '#e5e7eb';
        div.style.color = isUser ? 'white' : '#1f2937';
        div.style.fontSize = '14px';
        div.style.lineHeight = '1.5';
        div.style.whiteSpace = 'pre-wrap';
        div.style.wordBreak = 'break-word';

        if (!isUser) {
            let formatted = text
                .replace(/\*\*(.*?)\*\*/g, '<b>$1</b>')
                .replace(/\*(.*?)\*/g, '<i>$1</i>')
                .replace(/\n/g, '<br>');
            div.innerHTML = formatted;
        } else {
            div.innerText = text;
        }

        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    };

    const showActionNotice = (text, isSuccess = true) => {
        const notice = document.createElement('div');
        notice.style.padding = '8px 12px';
        notice.style.borderRadius = '10px';
        notice.style.maxWidth = '85%';
        notice.style.alignSelf = 'center';
        notice.style.background = isSuccess ? '#dcfce7' : '#fee2e2';
        notice.style.color = isSuccess ? '#166534' : '#991b1b';
        notice.style.fontSize = '13px';
        notice.style.fontWeight = '500';
        notice.style.textAlign = 'center';
        notice.style.border = isSuccess ? '1px solid #bbf7d0' : '1px solid #fecaca';
        notice.innerHTML = (isSuccess ? '&#10003; ' : '&#10007; ') + text;
        chatMessages.appendChild(notice);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    };

    const navPages = {
        photos: '{{ route("user.photos", [], false) }}',
        blog: '{{ route("user.blog", [], false) }}',
        messages: '{{ route("user.messageboard", [], false) }}',
        messageto: '{{ route("user.messageto", [], false) }}',
        sendmessage: '{{ route("user.send.message", [], false) }}',
        deceased: '{{ route("user.deceased", [], false) }}',
        dashboard: '{{ route("user.dashboard", [], false) }}',
        familytree: '{{ route("user.familytree", [], false) }}',
        familylisting: '{{ route("user.familylisting", [], false) }}',
        import: '{{ route("user.import", [], false) }}',
        export: '{{ route("user.export", [], false) }}',
        memberships: '{{ route("user.memberships", [], false) }}',
        profile: '{{ route("user.profile", [], false) }}',
        settings: '{{ route("user.setting", [], false) }}'
    };

    const tryClientNavigation = (text) => {
        const lower = text.toLowerCase().trim();
        if (!/\b(go to|take me to|open|show me|navigate to|bring me to|visit)\b/.test(lower) && lower.length > 35) {
            return false;
        }
        const rules = [
            ['messageto', ['messages to you', 'inbox']],
            ['sendmessage', ['send message', 'compose message']],
            ['messages', ['message board', 'messages']],
            ['photos', ['photo upload', 'photos', 'photo', 'pictures']],
            ['blog', ['blog']],
            ['deceased', ['deceased', 'death certificate']],
            ['dashboard', ['dashboard', 'home']],
            ['familytree', ['family tree', 'members listing']],
            ['familylisting', ['family listing', 'families']],
            ['import', ['import']],
            ['export', ['export']],
            ['memberships', ['membership', 'plan']],
            ['profile', ['profile']],
            ['settings', ['settings']]
        ];
        for (const [page, keywords] of rules) {
            if (keywords.some(k => lower.includes(k)) && navPages[page]) {
                addMessage('Opening ' + page + ' for you now.');
                setTimeout(() => { window.location.href = navPages[page]; }, 600);
                return true;
            }
        }
        return false;
    };

    const sendMessage = async () => {
        const message = chatInput.value.trim();
        if (!message || isSending) return;

        if (tryClientNavigation(message)) {
            chatInput.value = '';
            return;
        }

        isSending = true;
        addMessage(message, true);
        chatInput.value = '';
        chatSend.style.opacity = '0.5';

        const loadingDiv = document.createElement('div');
        loadingDiv.style.fontSize = '13px';
        loadingDiv.style.color = '#9ca3af';
        loadingDiv.style.padding = '5px 10px';
        loadingDiv.innerHTML = '<span class="chatbot-typing">Thinking</span>';
        chatMessages.appendChild(loadingDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        try {
            const response = await fetch('{{ route("chatbot.chat", [], false) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    message: message,
                    conversation: conversationHistory
                })
            });

            let data;
            try {
                data = await response.json();
            } catch (e) {
                throw new Error('Invalid server response');
            }
            if (loadingDiv.parentNode) loadingDiv.parentNode.removeChild(loadingDiv);

            if (!response.ok && !data.response && !data.error) {
                throw new Error('HTTP ' + response.status);
            }

            if (data.response) {
                addMessage(data.response);
                if (data.conversation) {
                    conversationHistory = data.conversation;
                }
                if (data.action_result) {
                    const ar = data.action_result;
                    if (ar.message) {
                        showActionNotice(ar.message, !!ar.success);
                    }
                    if (ar.success && ar.action === 'navigate' && ar.url) {
                        setTimeout(() => { window.location.href = ar.url; }, 800);
                    }
                } else if (data.member_added && data.member_added.success) {
                    showActionNotice('Member added successfully!');
                }
            } else {
                const errMsg = data.error || '{{ __("messages.Sorry, something went wrong. Please try again.") }}';
                addMessage(errMsg);
                if (typeof toastr !== 'undefined') {
                    toastr.error(errMsg);
                }
            }
        } catch (error) {
            if (loadingDiv.parentNode) loadingDiv.parentNode.removeChild(loadingDiv);
            addMessage('Error: Could not connect to the server. Please try again.');
        }

        isSending = false;
        chatSend.style.opacity = '1';
    };

    chatSend.onclick = sendMessage;
    chatInput.onkeypress = (e) => { if (e.key === 'Enter') sendMessage(); };
});
</script>

<style>
@keyframes chatbotTyping {
    0% { content: ''; }
    25% { content: '.'; }
    50% { content: '..'; }
    75% { content: '...'; }
}
.chatbot-typing::after {
    content: '';
    animation: chatbotTyping 1.5s infinite;
}
</style>
