// public/assets/js/socket.js

let socket;
let isConnected = false;
let reconnectTimeout;
const RECONNECT_DELAY = 5000; // 5 giây

// Khởi tạo kết nối WebSocket
function initSocket() {
    const roomId = document.getElementById('room-id').value;
    const userId = document.getElementById('user-id').value;
    const token = document.getElementById('ws-token').value;

    // Lấy host và port từ cấu hình
    const socketHost = window.location.hostname; // Hoặc có thể hardcode nếu cần thiết
    const socketPort = 8080; // Cần đảm bảo khớp với cấu hình SOCKET_PORT trong config

    // Tạo kết nối WebSocket
    socket = new WebSocket(`ws://${socketHost}:${socketPort}`);

    // Xử lý sự kiện khi kết nối thành công
    socket.onopen = function () {
        console.log('WebSocket connected');
        isConnected = true;

        // Xóa timeout reconnect nếu đang có
        if (reconnectTimeout) {
            clearTimeout(reconnectTimeout);
            reconnectTimeout = null;
        }

        // Gửi thông tin tham gia phòng
        joinRoom(roomId, userId, token);
    };

    // Xử lý khi nhận được tin nhắn từ server
    socket.onmessage = function (event) {
        const data = JSON.parse(event.data);
        handleSocketMessage(data);
    };

    // Xử lý khi kết nối đóng
    socket.onclose = function () {
        console.log('WebSocket disconnected');
        isConnected = false;

        // Thử kết nối lại sau một khoảng thời gian
        if (!reconnectTimeout) {
            reconnectTimeout = setTimeout(function () {
                console.log('Trying to reconnect...');
                initSocket();
            }, RECONNECT_DELAY);
        }
    };

    // Xử lý khi có lỗi
    socket.onerror = function (error) {
        console.error('WebSocket error:', error);
    };
}

// Tham gia phòng
function joinRoom(roomId, userId, token) {
    if (!isConnected) return;

    const data = {
        action: 'join_room',
        room_id: roomId,
        user_id: userId,
        token: token
    };

    socket.send(JSON.stringify(data));
}

// Rời phòng
function leaveRoom() {
    const roomId = document.getElementById('room-id').value;

    if (isConnected) {
        // Gửi thông tin rời phòng qua WebSocket
        const data = {
            action: 'leave_room',
            room_id: roomId
        };

        socket.send(JSON.stringify(data));
    }

    // Chuyển hướng về trang danh sách phòng
    window.location.href = window.location.origin + '/rooms';
}

// Gửi tin nhắn chat
function sendChatMessage(message) {
    if (!isConnected) return;

    const roomId = document.getElementById('room-id').value;

    const data = {
        action: 'chat_message',
        room_id: roomId,
        message: message
    };

    socket.send(JSON.stringify(data));
}

// Xử lý tin nhắn từ server
function handleSocketMessage(data) {
    if (!data || !data.action) return;

    switch (data.action) {
        case 'joined_room':
            handleJoinedRoom(data);
            break;

        case 'user_joined':
            handleUserJoined(data);
            break;

        case 'user_left':
            handleUserLeft(data);
            break;

        case 'admin_joined':
            handleAdminJoined(data);
            break;

        case 'admin_left':
            handleAdminLeft(data);
            break;

        case 'chat_message':
            handleChatMessage(data);
            break;

        case 'video_seek':
            handleVideoSeek(data);
            break;

        case 'video_play':
            handleVideoPlay(data);
            break;

        case 'video_pause':
            handleVideoPause(data);
            break;

        case 'room_closed':
            handleRoomClosed(data);
            break;
    }
}

// Xử lý khi tham gia phòng thành công
function handleJoinedRoom(data) {
    console.log('Joined room:', data);

    // Cập nhật thời gian video nếu có
    if (data.current_time) {
        const videoPlayer = document.getElementById('movie-player');
        if (videoPlayer) {
            videoPlayer.currentTime = parseInt(data.current_time);
        }
    }

    // Ẩn overlay nếu đang hiển thị
    hideVideoOverlay();
}

// Xử lý khi có người tham gia phòng
function handleUserJoined(data) {
    console.log('User joined:', data);

    // Cập nhật danh sách người dùng
    updateUsersList(data.users);

    // Hiển thị thông báo trong chat
    const username = getUsernameById(data.user_id, data.users);
    if (username) {
        addSystemMessage(`${username} đã tham gia phòng.`);
    }
}

// Xử lý khi có người rời phòng
function handleUserLeft(data) {
    console.log('User left:', data);

    // Xóa user khỏi danh sách
    const userItem = document.querySelector(`.user-item[data-user-id="${data.user_id}"]`);
    if (userItem) {
        userItem.remove();

        // Cập nhật số lượng người xem
        const usersCount = document.getElementById('users-count');
        if (usersCount) {
            usersCount.textContent = parseInt(usersCount.textContent) - 1;
        }
    }

    // Hiển thị thông báo trong chat
    const username = userItem ? userItem.querySelector('.user-name').textContent : 'Người dùng';
    addSystemMessage(`${username} đã rời phòng.`);
}

// Xử lý khi admin tham gia phòng
function handleAdminJoined(data) {
    console.log('Admin joined:', data);

    // Hiển thị thông báo trong chat
    addSystemMessage('Admin đã tham gia phòng.');

    // Ẩn overlay nếu đang hiển thị
    hideVideoOverlay();
}

// Xử lý khi admin rời phòng
function handleAdminLeft(data) {
    console.log('Admin left:', data);

    // Hiển thị thông báo trong chat
    addSystemMessage('Admin đã rời phòng.');

    // Hiển thị overlay thông báo
    showVideoOverlay('Đang chờ Admin...');
}

// Xử lý khi nhận được tin nhắn chat
function handleChatMessage(data) {
    console.log('Chat message:', data);

    // Thêm tin nhắn vào khung chat
    const username = data.admin_id ? 'Admin' : getUsernameById(data.user_id);
    addChatMessage(username, data.message, data.time);
}

// Xử lý khi admin điều chỉnh thời gian video
function handleVideoSeek(data) {
    console.log('Video seek:', data);

    const videoPlayer = document.getElementById('movie-player');
    if (videoPlayer) {
        videoPlayer.currentTime = parseInt(data.time);
    }
}

// Xử lý khi admin play video
function handleVideoPlay(data) {
    console.log('Video play');

    const videoPlayer = document.getElementById('movie-player');
    if (videoPlayer) {
        // Tắt sự kiện để tránh vòng lặp
        videoPlayer.removeEventListener('play', onVideoPlay);

        // Play video
        const playPromise = videoPlayer.play();

        if (playPromise !== undefined) {
            playPromise.then(_ => {
                // Đăng ký lại sự kiện
                videoPlayer.addEventListener('play', onVideoPlay);
            })
                .catch(error => {
                    console.error('Error playing video:', error);
                    // Đăng ký lại sự kiện
                    videoPlayer.addEventListener('play', onVideoPlay);
                });
        } else {
            // Đăng ký lại sự kiện
            videoPlayer.addEventListener('play', onVideoPlay);
        }
    }
}

// Xử lý khi admin pause video
function handleVideoPause(data) {
    console.log('Video pause');

    const videoPlayer = document.getElementById('movie-player');
    if (videoPlayer) {
        // Tắt sự kiện để tránh vòng lặp
        videoPlayer.removeEventListener('pause', onVideoPause);

        // Pause video
        videoPlayer.pause();

        // Đăng ký lại sự kiện
        videoPlayer.addEventListener('pause', onVideoPause);
    }
}

// Xử lý khi phòng bị đóng
function handleRoomClosed(data) {
    console.log('Room closed');

    // Hiển thị thông báo
    alert('Phòng đã bị đóng bởi Admin.');

    // Chuyển hướng về trang danh sách phòng
    window.location.href = window.location.origin + '/rooms';
}

// Hàm helper lấy tên người dùng theo ID
function getUsernameById(userId, users) {
    // Nếu có danh sách users được truyền vào
    if (users) {
        const user = users.find(u => u.id === userId);
        return user ? user.username : null;
    }

    // Nếu không, tìm trong DOM
    const userItem = document.querySelector(`.user-item[data-user-id="${userId}"]`);
    if (userItem) {
        return userItem.querySelector('.user-name').textContent;
    }

    return null;
}

// Cập nhật danh sách người dùng
function updateUsersList(users) {
    if (!users || !Array.isArray(users)) return;

    const usersList = document.getElementById('users-list');
    const usersCount = document.getElementById('users-count');

    if (usersList) {
        // Xóa tất cả các user hiện tại
        usersList.innerHTML = '';

        // Thêm lại danh sách user mới
        users.forEach(user => {
            const userItem = document.createElement('li');
            userItem.className = 'user-item';
            userItem.setAttribute('data-user-id', user.id);

            userItem.innerHTML = `
                <div class="user-avatar">
                    <img src="${window.location.origin}/public/assets/uploads/${user.avatar || 'default-avatar.jpg'}" alt="${user.username}">
                </div>
                <div class="user-name">${user.username}</div>
            `;

            usersList.appendChild(userItem);
        });

        // Cập nhật số lượng người xem
        if (usersCount) {
            usersCount.textContent = users.length;
        }
    }
}

// Hiển thị overlay video
function showVideoOverlay(message) {
    const overlay = document.getElementById('video-overlay');
    const overlayMessage = overlay.querySelector('.overlay-message');

    if (overlayMessage) {
        overlayMessage.textContent = message;
    }

    overlay.style.display = 'flex';
}

// Ẩn overlay video
function hideVideoOverlay() {
    const overlay = document.getElementById('video-overlay');
    overlay.style.display = 'none';
}