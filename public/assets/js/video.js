// public/assets/js/video.js

let videoPlayer;
let updateTimeInterval;
const UPDATE_INTERVAL = 5000; // Cập nhật thời gian mỗi 5 giây

// Khởi tạo video player
function initVideoPlayer() {
    videoPlayer = document.getElementById('movie-player');

    if (!videoPlayer) return;

    // Lấy thời gian hiện tại từ server
    const currentTime = parseInt(document.getElementById('current-time').value || 0);

    // Đặt thời gian hiện tại cho video
    videoPlayer.currentTime = currentTime;

    // Hiển thị overlay chờ đồng bộ
    showVideoOverlay('Đang đồng bộ với Admin...');

    // Đăng ký các sự kiện
    videoPlayer.addEventListener('play', onVideoPlay);
    videoPlayer.addEventListener('pause', onVideoPause);
    videoPlayer.addEventListener('seeking', onVideoSeeking);
    videoPlayer.addEventListener('seeked', onVideoSeeked);
    videoPlayer.addEventListener('timeupdate', onVideoTimeUpdate);
    videoPlayer.addEventListener('ended', onVideoEnded);

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
        // Chỉ cập nhật nếu video đang play
        if (videoPlayer && !videoPlayer.paused) {
            updateCurrentTime();
        }
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
function updateCurrentTime() {
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

// Xử lý sự kiện khi video play
function onVideoPlay() {
    console.log('Video played');

    // Bắt đầu interval cập nhật thời gian
    startUpdateTimeInterval();
}

// Xử lý sự kiện khi video pause
function onVideoPause() {
    console.log('Video paused');

    // Dừng interval cập nhật thời gian
    stopUpdateTimeInterval();
}

// Xử lý sự kiện khi video đang seek
function onVideoSeeking() {
    console.log('Video seeking');
}

// Xử lý sự kiện khi video đã seek xong
function onVideoSeeked() {
    console.log('Video seeked');

    // Cập nhật thời gian ngay lập tức
    updateCurrentTime();
}

// Xử lý sự kiện khi thời gian video thay đổi
function onVideoTimeUpdate() {
    // Chúng ta không cần làm gì ở đây vì đã có interval cập nhật thời gian
}

// Xử lý sự kiện khi video kết thúc
function onVideoEnded() {
    console.log('Video ended');

    // Dừng interval cập nhật thời gian
    stopUpdateTimeInterval();

    // Hiển thị overlay thông báo
    showVideoOverlay('Phim đã kết thúc');
}

// Cleanup khi rời trang
window.addEventListener('beforeunload', function () {
    // Dừng interval cập nhật thời gian
    stopUpdateTimeInterval();

    // Dừng video
    if (videoPlayer) {
        videoPlayer.pause();
    }
});