<?php require_once VIEW_PATH . '/layouts/header.php'; ?>

<div class="room-container">
    <input type="hidden" id="room-id" value="<?= $room['id'] ?>">
    <input type="hidden" id="user-id" value="<?= $_SESSION['user_id'] ?>">
    <input type="hidden" id="username" value="<?= $_SESSION['username'] ?>">
    <input type="hidden" id="ws-token" value="<?= $token ?>">
    <input type="hidden" id="current-time" value="<?= $room['current_time'] ?>">

    <div class="room-header">
        <div class="container">
            <div class="room-info">
                <h1 class="room-title fade-in-trigger"><?= $room['name'] ?></h1>
                <p class="movie-title">Phim: <?= $movie['title'] ?></p>
                <p class="admin-name">Admin: <?= $room['admin_username'] ?></p>
            </div>
            <div class="room-actions">
                <button id="leave-room" class="btn btn-danger btn-hover ripple">Rời phòng</button>
            </div>
        </div>
    </div>

    <div class="container room-content">
        <div class="row">
            <div class="col-md-9">
                <div class="video-container fade-in-trigger">
                    <video id="movie-player" controls poster="<?= PUBLIC_PATH ?>/assets/uploads/thumbnails/<?= $movie['thumbnail'] ?>">
                        <source src="<?= PUBLIC_PATH ?>/assets/uploads/movies/<?= $movie['file_path'] ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <div class="video-overlay" id="video-overlay">
                        <div class="spinner"></div>
                        <div class="overlay-message">Đang đồng bộ với Admin...</div>
                    </div>
                </div>

                <div class="video-info">
                    <h2 class="movie-title"><?= $movie['title'] ?></h2>
                    <div class="movie-meta">
                        <span class="duration"><i class="fas fa-clock"></i> <?= $movie['duration'] ?> phút</span>
                        <span class="genre"><i class="fas fa-film"></i> <?= $movie['genre'] ?></span>
                        <span class="year"><i class="fas fa-calendar"></i> <?= $movie['release_year'] ?></span>
                    </div>
                    <div class="movie-description">
                        <h3>Nội dung phim</h3>
                        <p><?= $movie['description'] ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="room-sidebar">
                    <div class="users-list-container">
                        <h3>Người xem (<span id="users-count"><?= count($users) ?></span>)</h3>
                        <ul class="users-list" id="users-list">
                            <?php foreach ($users as $user): ?>
                                <li class="user-item" data-user-id="<?= $user['id'] ?>">
                                    <div class="user-avatar">
                                        <img src="<?= PUBLIC_PATH ?>/assets/uploads/<?= $user['avatar'] ?? 'default-avatar.jpg' ?>" alt="<?= $user['username'] ?>">
                                    </div>
                                    <div class="user-name"><?= $user['username'] ?></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="chat-container">
                        <h3>Chat</h3>
                        <div class="chat-messages" id="chat-messages">
                            <div class="chat-welcome">
                                <p>Chào mừng bạn đến với phòng xem phim!</p>
                                <p>Bạn có thể trò chuyện với những người xem khác ở đây.</p>
                            </div>
                        </div>
                        <div class="chat-form">
                            <input type="text" id="chat-input" class="form-control" placeholder="Nhập tin nhắn...">
                            <button id="chat-send" class="btn btn-primary btn-hover ripple">Gửi</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Template chat message -->
<template id="chat-message-template">
    <div class="chat-message">
        <div class="message-info">
            <span class="message-username"></span>
            <span class="message-time"></span>
        </div>
        <div class="message-content"></div>
    </div>
</template>

<!-- Template user item -->
<template id="user-item-template">
    <li class="user-item">
        <div class="user-avatar">
            <img src="<?= PUBLIC_PATH ?>/assets/uploads/default-avatar.jpg" alt="">
        </div>
        <div class="user-name"></div>
    </li>
</template>

<?php ob_start(); ?>
<script src="<?= PUBLIC_PATH ?>/assets/js/socket.js"></script>
<script src="<?= PUBLIC_PATH ?>/assets/js/room.js"></script>
<script src="<?= PUBLIC_PATH ?>/assets/js/video.js"></script>
<script src="<?= PUBLIC_PATH ?>/assets/js/chat.js"></script>
<script>
    // Khởi tạo kết nối socket khi trang tải xong
    document.addEventListener('DOMContentLoaded', function() {
        // Kết nối WebSocket
        initSocket();

        // Khởi tạo video player
        initVideoPlayer();

        // Khởi tạo chat
        initChat();

        // Nút rời phòng
        document.getElementById('leave-room').addEventListener('click', function() {
            leaveRoom();
        });
    });
</script>
<?php
$scripts = ob_get_clean();
require_once VIEW_PATH . '/layouts/footer.php';
?>