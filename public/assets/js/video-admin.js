// public/assets/js/video-admin.js

let videoPlayer;
let isAdminControlling = false;
let updateTimeInterval;
const UPDATE_INTERVAL = 3000; // Cập nhật thời gian mỗi 3 giây

// Khởi tạo video player cho admin
function initAdminVideo() {
    videoPlayer = document.getElementById('movie-player');

    if (!videoPlayer) return;

    // Lấy thời gian hiện tại từ server
    const currentTime = parseInt(document.getElementById('current-time').value || 0);

    // Đặt thời gian hiện tại cho video
    videoPlayer.currentTime = currentTime;

    // Hiển thị thời gian hiện tại
    updateCurrentTimeDisplay();

    // Đăng ký các sự kiện
    videoPlayer.addEventListener('play', onVideoPlay);
    videoPlayer.addEventListener('pause', onVideoPause);
    videoPlayer.addEventListener('timeupdate', onVideoTimeUpdate);
    videoPlayer.addEventListener('seeked', onVideoSeeked);

    // Đăng ký các sự kiện nút điều khiển
    const playButton = document.getElementById('playButton');
    const pauseButton = document.getElementById('pauseButton');
    const backwardButton = document.getElementById('backwardButton');
    const forwardButton = document.getElementById('forwardButton');
    const jumpButton = document.getElementById('jumpButton');

    if (playButton) playButton.addEventListener('click', playVideo);
    if (pauseButton) pauseButton.addEventListener('click', pauseVideo);
    if (backwardButton) backwardButton.addEventListener('click', backwardVideo);
    if (forwardButton) forwardButton.addEventListener('click', forwardVideo);
    if (jumpButton) jumpButton.addEventListener('click', jumpToTime);

    // Bắt đầu interval cập nhật thời gian
    startUpdateTimeInterval();
}

// Bắt đầu interval cập nhật thời gian
function startUpdateTimeInterval() {
    // Xóa interval cũ nếu có
    if (updateTimeInterval) {
        clearInterval(updateTimeInterval);
    }

    // Tạo interval mới
    updateTimeInterval = setInterval(function () {
        updateCurrentTimeToServer();
    }, UPDATE_INTERVAL);
}

// Dừng interval cập nhật thời gian
function stopUpdateTimeInterval() {
    if (updateTimeInterval) {
        clearInterval(updateTimeInterval);
        updateTimeInterval = null;
    }
}

// Cập nhật thời gian hiện tại lên server
function updateCurrentTimeToServer() {
    if (!videoPlayer) return;

    const currentTime = Math.floor(videoPlayer.currentTime);
    const roomId = document.getElementById('room-id').value;

    // Gửi thời gian hiện tại qua WebSocket
    if (socket && isConnected) {
        const data = {
            action: 'update_video_time',
            room_id: roomId,
            time: currentTime
        };

        socket.send(JSON.stringify(data));
    }
}

// Cập nhật hiển thị thời gian hiện tại
function updateCurrentTimeDisplay() {
    if (!videoPlayer) return;

    const currentPosition = document.getElementById('currentPosition');
    if (currentPosition) {
        const time = Math.floor(videoPlayer.currentTime);
        currentPosition.value = formatTimeForDisplay(time);
    }
}

// Format thời gian để hiển thị
function formatTimeForDisplay(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = Math.floor(seconds % 60);

    if (hours > 0) {
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    } else {
        return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }
}

// Xử lý sự kiện khi video play
function onVideoPlay() {
    console.log('Video played by admin');

    // Bắt đầu interval cập nhật thời gian
    startUpdateTimeInterval();

    // Gửi lệnh play cho tất cả người xem
    if (isAdminControlling) {
        broadcastPlayCommand();
    }
}

// Xử lý sự kiện khi video pause
function onVideoPause() {
    console.log('Video paused by admin');

    // Dừng interval cập nhật thời gian
    stopUpdateTimeInterval();

    // Gửi lệnh pause cho tất cả người xem
    if (isAdminControlling) {
        broadcastPauseCommand();
    }
}

// Xử lý sự kiện khi thời gian video thay đổi
function onVideoTimeUpdate() {
    // Cập nhật hiển thị thời gian hiện tại
    updateCurrentTimeDisplay();
}

// Xử lý sự kiện khi video đã seek xong
function onVideoSeeked() {
    console.log('Video seeked by admin');

    // Cập nhật thời gian ngay lập tức
    if (isAdminControlling) {
        broadcastSeekCommand();
    }
}

// Phát video
function playVideo() {
    if (!videoPlayer) return;

    isAdminControlling = true;

    // Phát video
    videoPlayer.play().then(() => {
        console.log('Video played successfully');
    }).catch(error => {
        console.error('Error playing video:', error);
    });
}

// Tạm dừng video
function pauseVideo() {
    if (!videoPlayer) return;

    isAdminControlling = true;

    // Tạm dừng video
    videoPlayer.pause();
}

// Tua lùi video 10 giây
function backwardVideo() {
    if (!videoPlayer) return;

    isAdminControlling = true;

    // Tua lùi video 10 giây
    videoPlayer.currentTime = Math.max(0, videoPlayer.currentTime - 10);
}

// Tua tiến video 10 giây
function forwardVideo() {
    if (!videoPlayer) return;

    isAdminControlling = true;

    // Tua tiến video 10 giây
    videoPlayer.currentTime = Math.min(videoPlayer.duration, videoPlayer.currentTime + 10);
}

// Nhảy đến thời gian cụ thể
function jumpToTime() {
    const jumpToPosition = document.getElementById('jumpToPosition');
    if (!jumpToPosition || !videoPlayer) return;

    const timeString = jumpToPosition.value;
    if (!timeString) return;

    const seconds = parseTimeString(timeString);
    if (isNaN(seconds)) {
        alert('Vui lòng nhập thời gian hợp lệ (HH:MM:SS hoặc MM:SS)');
        return;
    }

    // Nhảy đến thời gian cụ thể
    seekVideo(seconds);
}

// Seek video đến thời gian cụ thể
function seekVideo(seconds) {
    if (!videoPlayer) return;

    isAdminControlling = true;

    // Kiểm tra thời gian hợp lệ
    seconds = Math.max(0, Math.min(videoPlayer.duration, seconds));

    // Seek video
    videoPlayer.currentTime = seconds;

    // Cập nhật hiển thị
    updateCurrentTimeDisplay();

    console.log('Seeking to', seconds, 'seconds');
}

// Parse chuỗi thời gian thành giây
function parseTimeString(timeString) {
    const parts = timeString.split(':');
    let seconds = 0;

    if (parts.length === 3) {
        // HH:MM:SS
        seconds = parseInt(parts[0]) * 3600 + parseInt(parts[1]) * 60 + parseInt(parts[2]);
    } else if (parts.length === 2) {
        // MM:SS
        seconds = parseInt(parts[0]) * 60 + parseInt(parts[1]);
    } else if (parts.length === 1) {
        // SS
        seconds = parseInt(parts[0]);
    }

    return seconds;
}

// Gửi lệnh play cho tất cả người xem
function broadcastPlayCommand() {
    if (!socket || !isConnected) return;

    const roomId = document.getElementById('room-id').value;

    const data = {
        action: 'play_video',
        room_id: roomId
    };

    socket.send(JSON.stringify(data));
}

// Gửi lệnh pause cho tất cả người xem
function broadcastPauseCommand() {
    if (!socket || !isConnected) return;

    const roomId = document.getElementById('room-id').value;

    const data = {
        action: 'pause_video',
        room_id: roomId
    };

    socket.send(JSON.stringify(data));
}

// Gửi lệnh seek cho tất cả người xem
function broadcastSeekCommand() {
    if (!socket || !isConnected || !videoPlayer) return;

    const roomId = document.getElementById('room-id').value;
    const currentTime = Math.floor(videoPlayer.currentTime);

    const data = {
        action: 'video_seek',
        room_id: roomId,
        time: currentTime
    };

    socket.send(JSON.stringify(data));
}

// Reset trạng thái khi rời trang
window.addEventListener('beforeunload', function () {
    // Dừng interval cập nhật thời gian
    stopUpdateTimeInterval();

    // Hủy đăng ký các sự kiện
    if (videoPlayer) {
        videoPlayer.removeEventListener('play', onVideoPlay);
        videoPlayer.removeEventListener('pause', onVideoPause);
        videoPlayer.removeEventListener('timeupdate', onVideoTimeUpdate);
        videoPlayer.removeEventListener('seeked', onVideoSeeked);
    }
});