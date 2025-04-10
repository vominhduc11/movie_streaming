<?php
// Bắt đầu với Admin Layout
$content = ob_get_clean();
require_once VIEW_PATH . '/layouts/admin.php';
?>

<div class="admin-room-view">
    <input type="hidden" id="room-id" value="<?= $room['id'] ?>">
    <input type="hidden" id="admin-id" value="<?= $_SESSION['admin_id'] ?>">
    <input type="hidden" id="ws-token" value="<?= $token ?>">
    <input type="hidden" id="current-time" value="<?= $room['current_time'] ?>">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Quản lý phòng: <?= $room['name'] ?></h1>
        <div class="room-status">
            <?php if ($room['status'] === 'open'): ?>
                <span class="badge badge-success">Đang mở</span>
            <?php else: ?>
                <span class="badge badge-secondary">Đã đóng</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Video Player -->
            <div class="card mb-4">
                <div class="card-body p-0">
                    <div class="video-container">
                        <video id="movie-player" controls poster="<?= PUBLIC_PATH ?>/assets/uploads/thumbnails/<?= $movie['thumbnail'] ?>">
                            <source src="<?= PUBLIC_PATH ?>/assets/uploads/movies/<?= $movie['file_path'] ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                </div>
            </div>

            <!-- Video Controls -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Điều khiển phòng</h5>
                </div>
                <div class="card-body">
                    <div class="btn-toolbar">
                        <div class="btn-group mr-2">
                            <button id="playButton" class="btn btn-primary">
                                <i class="fas fa-play"></i> Phát
                            </button>
                            <button id="pauseButton" class="btn btn-secondary">
                                <i class="fas fa-pause"></i> Tạm dừng
                            </button>
                        </div>

                        <div class="btn-group mr-2">
                            <button id="backwardButton" class="btn btn-info">
                                <i class="fas fa-backward"></i> -10s
                            </button>
                            <button id="forwardButton" class="btn btn-info">
                                <i class="fas fa-forward"></i> +10s
                            </button>
                        </div>

                        <div class="btn-group">
                            <?php if ($room['status'] === 'open'): ?>
                                <a href="<?= APP_URL ?>/admin/rooms/close/<?= $room['id'] ?>" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn đóng phòng này?')">
                                    <i class="fas fa-times"></i> Đóng phòng
                                </a>
                            <?php else: ?>
                                <a href="<?= APP_URL ?>/admin/rooms/open/<?= $room['id'] ?>" class="btn btn-success">
                                    <i class="fas fa-door-open"></i> Mở phòng
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="currentPosition">Vị trí hiện tại:</label>
                                <div class="input-group">
                                    <input type="text" id="currentPosition" class="form-control" readonly>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="copyTimeButton">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jumpToPosition">Nhảy đến vị trí:</label>
                                <div class="input-group">
                                    <input type="text" id="jumpToPosition" class="form-control" placeholder="HH:MM:SS">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" id="jumpButton">
                                            <i class="fas fa-play-circle"></i> Nhảy
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Movie Info -->
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Thông tin phim</h5>
                    <a href="<?= APP_URL ?>/admin/movies/edit/<?= $movie['id'] ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i> Sửa phim
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <img src="<?= PUBLIC_PATH ?>/assets/uploads/thumbnails/<?= $movie['thumbnail'] ?>" alt="<?= $movie['title'] ?>" class="img-fluid rounded">
                        </div>
                        <div class="col-md-9">
                            <h4><?= $movie['title'] ?></h4>
                            <div class="mb-3">
                                <span class="badge badge-primary"><?= $movie['genre'] ?></span>
                                <span class="badge badge-secondary"><?= $movie['release_year'] ?></span>
                                <span class="badge badge-info"><?= $movie['duration'] ?> phút</span>
                                <span class="badge badge-success"><?= number_format($movie['price'], 0, ',', '.') ?> VND</span>
                            </div>
                            <p><?= $movie['description'] ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- User List -->
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Người xem (<span id="viewers-count"><?= count($users) ?></span>)</h5>
                    <button class="btn btn-sm btn-outline-secondary" id="refreshUsersButton">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="card-body p-0">
                    <ul class="user-list" id="user-list">
                        <?php foreach ($users as $user): ?>
                            <li class="user-item" data-user-id="<?= $user['id'] ?>">
                                <div class="media align-items-center">
                                    <img src="<?= PUBLIC_PATH ?>/assets/uploads/<?= $user['avatar'] ?? 'default-avatar.jpg' ?>" class="user-avatar mr-3">
                                    <div class="media-body">
                                        <h6 class="mt-0 mb-1"><?= $user['username'] ?></h6>
                                        <small class="text-muted">Tham gia: <?= date('H:i', strtotime($user['joined_at'])) ?></small>
                                    </div>
                                    <div class="user-actions">
                                        <button class="btn btn-sm btn-outline-danger kick-user-btn" data-user-id="<?= $user['id'] ?>">
                                            <i class="fas fa-sign-out-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>

                        <?php if (count($users) === 0): ?>
                            <li class="text-center py-4">
                                <i class="fas fa-users empty-state-icon d-block mb-2"></i>
                                <p class="text-muted">Chưa có người xem trong phòng này.</p>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Chat -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Chat</h5>
                </div>
                <div class="card-body p-0">
                    <div class="chat-container">
                        <div class="chat-messages" id="chat-messages">
                            <div class="chat-welcome">
                                <p>Chào mừng đến với phòng xem phim!</p>
                                <p>Bạn có thể trao đổi với người xem ở đây.</p>
                            </div>
                        </div>

                        <div class="chat-form">
                            <div class="input-group">
                                <input type="text" id="chat-input" class="form-control" placeholder="Nhập tin nhắn...">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button" id="chat-send">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Room Settings -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Cài đặt phòng</h5>
                </div>
                <div class="card-body">
                    <form id="roomSettingsForm">
                        <div class="form-group">
                            <label for="roomName">Tên phòng</label>
                            <input type="text" class="form-control" id="roomName" value="<?= $room['name'] ?>">
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="allowChat" checked>
                                <label class="custom-control-label" for="allowChat">Cho phép chat</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="autoSync" checked>
                                <label class="custom-control-label" for="autoSync">Tự động đồng bộ thời gian</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Lưu cài đặt</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Templates -->
<template id="chat-message-template">
    <div class="chat-message">
        <div class="message-info">
            <span class="message-username"></span>
            <span class="message-time"></span>
        </div>
        <div class="message-content"></div>
    </div>
</template>

<template id="user-item-template">
    <li class="user-item">
        <div class="media align-items-center">
            <img src="" class="user-avatar mr-3">
            <div class="media-body">
                <h6 class="mt-0 mb-1"></h6>
                <small class="text-muted"></small>
            </div>
            <div class="user-actions">
                <button class="btn btn-sm btn-outline-danger kick-user-btn">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </div>
        </div>
    </li>
</template>

<?php ob_start(); ?>
<style>
    /* Admin Room View Styles */
    .video-container {
        position: relative;
        width: 100%;
        background-color: black;
    }

    #movie-player {
        width: 100%;
        max-height: 500px;
        display: block;
    }

    .user-list {
        list-style: none;
        padding: 0;
        margin: 0;
        max-height: 300px;
        overflow-y: auto;
    }

    .user-item {
        padding: 10px 15px;
        border-bottom: 1px solid #eee;
    }

    .user-item:last-child {
        border-bottom: none;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .empty-state-icon {
        font-size: 3rem;
        color: #ccc;
    }

    /* Chat Styles */
    .chat-container {
        display: flex;
        flex-direction: column;
        height: 400px;
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 15px;
        background-color: #f8f9fa;
    }

    .chat-welcome {
        text-align: center;
        padding: 15px;
        color: #6c757d;
    }

    .chat-message {
        margin-bottom: 15px;
        padding: 10px;
        background-color: white;
        border-radius: 8px;
        border-left: 3px solid #3f51b5;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .chat-message.admin-message {
        border-left-color: #f44336;
        background-color: #fff8f8;
    }

    .message-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
        font-size: 0.85rem;
    }

    .message-username {
        font-weight: 600;
    }

    .message-time {
        color: #6c757d;
    }

    .chat-form {
        padding: 10px;
        background-color: white;
        border-top: 1px solid #eee;
    }

    .chat-system-message {
        text-align: center;
        padding: 5px 10px;
        margin: 10px 0;
        color: #6c757d;
        font-style: italic;
        font-size: 0.9rem;
    }
</style>

<script src="<?= PUBLIC_PATH ?>/assets/js/socket.js"></script>
<script src="<?= PUBLIC_PATH ?>/assets/js/video-admin.js"></script>
<script src="<?= PUBLIC_PATH ?>/assets/js/chat.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize video player for admin
        initAdminVideo();

        // Initialize WebSocket connection
        initSocket();

        // Initialize chat
        initChat();

        // Room settings form
        const roomSettingsForm = document.getElementById('roomSettingsForm');
        if (roomSettingsForm) {
            roomSettingsForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const roomName = document.getElementById('roomName').value;
                const allowChat = document.getElementById('allowChat').checked;
                const autoSync = document.getElementById('autoSync').checked;

                // Demo only - in a real app, this would save to the database
                alert('Cài đặt phòng đã được lưu!');
            });
        }

        // Kick user buttons
        document.addEventListener('click', function(e) {
            if (e.target.closest('.kick-user-btn')) {
                const btn = e.target.closest('.kick-user-btn');
                const userId = btn.getAttribute('data-user-id');

                if (confirm('Bạn có chắc chắn muốn đuổi người dùng này?')) {
                    kickUser(userId);
                }
            }
        });

        // Format time input
        const jumpToPosition = document.getElementById('jumpToPosition');
        if (jumpToPosition) {
            jumpToPosition.addEventListener('input', function(e) {
                let value = e.target.value;
                value = value.replace(/[^0-9:]/g, '');

                // Auto add colon after 2 digits
                if (value.length === 2 && !value.includes(':')) {
                    value += ':';
                } else if (value.length === 5 && value.indexOf(':') === 2 && value.indexOf(':', 3) === -1) {
                    value += ':';
                }

                e.target.value = value;
            });
        }

        // Jump to time button
        const jumpButton = document.getElementById('jumpButton');
        if (jumpButton) {
            jumpButton.addEventListener('click', function() {
                const timeStr = jumpToPosition.value;

                // Parse time string (HH:MM:SS or MM:SS)
                let seconds = 0;
                const parts = timeStr.split(':');

                if (parts.length === 3) {
                    // HH:MM:SS
                    seconds = parseInt(parts[0]) * 3600 + parseInt(parts[1]) * 60 + parseInt(parts[2]);
                } else if (parts.length === 2) {
                    // MM:SS
                    seconds = parseInt(parts[0]) * 60 + parseInt(parts[1]);
                } else {
                    // Seconds only
                    seconds = parseInt(parts[0]);
                }

                if (isNaN(seconds)) {
                    alert('Vui lòng nhập thời gian hợp lệ (HH:MM:SS)');
                    return;
                }

                // Jump to the specified time
                seekVideo(seconds);
            });
        }

        // Copy current time button
        const copyTimeButton = document.getElementById('copyTimeButton');
        if (copyTimeButton) {
            copyTimeButton.addEventListener('click', function() {
                const currentPosition = document.getElementById('currentPosition');

                navigator.clipboard.writeText(currentPosition.value).then(function() {
                    alert('Đã sao chép thời gian hiện tại!');
                }, function() {
                    console.error('Không thể sao chép thời gian');
                });
            });
        }

        // Refresh users button
        const refreshUsersButton = document.getElementById('refreshUsersButton');
        if (refreshUsersButton) {
            refreshUsersButton.addEventListener('click', function() {
                refreshUsersList();
            });
        }
    });

    // Function to kick a user from the room
    function kickUser(userId) {
        // In a real app, this would use WebSocket or AJAX to kick the user
        console.log('Kicking user:', userId);

        // Demo functionality - remove the user from the list
        const userItem = document.querySelector(`.user-item[data-user-id="${userId}"]`);
        if (userItem) {
            userItem.remove();

            // Update count
            const viewersCount = document.getElementById('viewers-count');
            if (viewersCount) {
                viewersCount.textContent = parseInt(viewersCount.textContent) - 1;
            }

            // Add system message to chat
            addSystemMessage('Người dùng đã bị đuổi khỏi phòng.');
        }
    }

    // Function to refresh users list
    function refreshUsersList() {
        // In a real app, this would fetch the latest user list from the server
        console.log('Refreshing users list');

        // Demo spinner animation
        const refreshButton = document.getElementById('refreshUsersButton');
        const originalHTML = refreshButton.innerHTML;
        refreshButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        refreshButton.disabled = true;

        // Simulate API call delay
        setTimeout(function() {
            refreshButton.innerHTML = originalHTML;
            refreshButton.disabled = false;

            // Demo: Show success message
            alert('Danh sách người xem đã được cập nhật!');
        }, 1000);
    }
</script>
<?php
$scripts = ob_get_clean();
?>