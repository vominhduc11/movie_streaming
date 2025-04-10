// public/assets/js/chat.js

// Khởi tạo chat
function initChat() {
    const chatInput = document.getElementById('chat-input');
    const chatSendButton = document.getElementById('chat-send');

    if (!chatInput || !chatSendButton) return;

    // Đăng ký sự kiện khi nhấn nút gửi
    chatSendButton.addEventListener('click', function () {
        const message = chatInput.value.trim();

        if (message) {
            // Gửi tin nhắn
            sendChatMessage(message);

            // Xóa nội dung input
            chatInput.value = '';

            // Focus lại vào input
            chatInput.focus();
        }
    });

    // Đăng ký sự kiện khi nhấn Enter trong input
    chatInput.addEventListener('keypress', function (event) {
        if (event.key === 'Enter') {
            // Ngăn chặn hành động mặc định
            event.preventDefault();

            // Kích hoạt nút gửi
            chatSendButton.click();
        }
    });

    // Focus vào input
    chatInput.focus();
}

// Thêm tin nhắn vào khung chat
function addChatMessage(username, message, time) {
    const chatMessages = document.getElementById('chat-messages');
    const template = document.getElementById('chat-message-template');

    if (!chatMessages || !template) return;

    // Tạo clone từ template
    const messageElement = document.importNode(template.content, true).querySelector('.chat-message');

    // Đặt nội dung tin nhắn
    messageElement.querySelector('.message-username').textContent = username;
    messageElement.querySelector('.message-time').textContent = time || new Date().toLocaleTimeString();
    messageElement.querySelector('.message-content').textContent = message;

    // Kiểm tra tin nhắn của mình hay người khác
    const currentUsername = document.getElementById('username').value;
    if (username === currentUsername) {
        messageElement.classList.add('my-message');
    }

    // Thêm vào khung chat
    chatMessages.appendChild(messageElement);

    // Cuộn xuống cuối
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Thêm tin nhắn hệ thống vào khung chat
function addSystemMessage(message) {
    const chatMessages = document.getElementById('chat-messages');

    if (!chatMessages) return;

    // Tạo phần tử tin nhắn hệ thống
    const messageElement = document.createElement('div');
    messageElement.className = 'chat-system-message';
    messageElement.textContent = message;

    // Thêm vào khung chat
    chatMessages.appendChild(messageElement);

    // Cuộn xuống cuối
    chatMessages.scrollTop = chatMessages.scrollHeight;
}