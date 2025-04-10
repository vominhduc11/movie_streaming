<?php
// app/socket/VideoHandler.php
namespace App\Socket;

use App\Models\Room;
use App\Models\Movie;

class VideoHandler
{
    private $roomModel;
    private $movieModel;

    public function __construct()
    {
        $this->roomModel = new Room();
        $this->movieModel = new Movie();
    }

    /**
     * Cập nhật thời gian hiện tại của video
     * 
     * @param int $roomId ID của phòng
     * @param int $time Thời gian (giây)
     * @return bool Kết quả cập nhật
     */
    public function updateCurrentTime($roomId, $time)
    {
        return $this->roomModel->updateCurrentTime($roomId, $time);
    }

    /**
     * Lấy thời gian hiện tại của video
     * 
     * @param int $roomId ID của phòng
     * @return int Thời gian hiện tại (giây)
     */
    public function getCurrentTime($roomId)
    {
        $room = $this->roomModel->getById($roomId);

        if (!$room) {
            return 0;
        }

        return $room['current_time'];
    }

    /**
     * Lấy thông tin phim của phòng
     * 
     * @param int $roomId ID của phòng
     * @return array|null Thông tin phim
     */
    public function getMovieInfo($roomId)
    {
        $room = $this->roomModel->getById($roomId);

        if (!$room) {
            return null;
        }

        return $this->movieModel->getById($room['movie_id']);
    }

    /**
     * Xác thực quyền xem phim
     * 
     * @param int $userId ID của người dùng
     * @param int $roomId ID của phòng
     * @return bool Kết quả xác thực
     */
    public function canWatchMovie($userId, $roomId)
    {
        return $this->roomModel->canUserWatch($userId, $roomId);
    }

    /**
     * Kiểm tra phòng có đang mở không
     * 
     * @param int $roomId ID của phòng
     * @return bool Trạng thái phòng
     */
    public function isRoomOpen($roomId)
    {
        $room = $this->roomModel->getById($roomId);

        if (!$room) {
            return false;
        }

        return $room['status'] === 'open';
    }

    /**
     * Lấy thời lượng phim
     * 
     * @param int $roomId ID của phòng
     * @return int Thời lượng phim (phút)
     */
    public function getMovieDuration($roomId)
    {
        $movie = $this->getMovieInfo($roomId);

        if (!$movie) {
            return 0;
        }

        return $movie['duration'];
    }

    /**
     * Tạo thông tin về trạng thái phát video
     * 
     * @param int $roomId ID của phòng
     * @return array Thông tin trạng thái video
     */
    public function getVideoStatus($roomId)
    {
        $room = $this->roomModel->getById($roomId);

        if (!$room) {
            return [
                'status' => 'closed',
                'current_time' => 0
            ];
        }

        return [
            'status' => $room['status'],
            'current_time' => $room['current_time']
        ];
    }

    /**
     * Kiểm tra video đã kết thúc chưa
     * 
     * @param int $roomId ID của phòng
     * @return bool Kết quả kiểm tra
     */
    public function isVideoEnded($roomId)
    {
        $room = $this->roomModel->getById($roomId);
        $movie = $this->getMovieInfo($roomId);

        if (!$room || !$movie) {
            return false;
        }

        // Chuyển đổi thời lượng phim từ phút sang giây
        $duration = $movie['duration'] * 60;

        // Kiểm tra xem thời gian hiện tại có gần với thời lượng phim không
        return $room['current_time'] >= ($duration - 10); // Còn 10 giây cuối cùng
    }
}
