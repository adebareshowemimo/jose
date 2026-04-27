import './bootstrap';

const chatRoot = document.querySelector('[data-chat-conversation-id]');

if (chatRoot && window.Echo) {
    const conversationId = chatRoot.dataset.chatConversationId;
    let reloadQueued = false;

    window.Echo.private(`chat.conversation.${conversationId}`)
        .listen('.message.sent', () => {
            if (reloadQueued) {
                return;
            }

            reloadQueued = true;
            window.setTimeout(() => window.location.reload(), 250);
        });
}
