<?php
// app/socket/RoomHandler.php
namespace App\Socket;

use App\Models\Room;
use App\Models\User;
use App\Models\Admin;

class RoomHandler
{
    private $roomModel;
    private $userModel;
    private $adminModel;

    public function __construct()
    {
        $this->roomModel = new Room();
        $this->userModel = new User();
        $this->adminModel = new Admin();
    }

    /**
     * Kiểm tra token hợp lệ và lấy thông tin từ token
     * 
     * @param string $token Token từ client
     * @return array|bool Thông tin từ token hoặc false nếu không hợp lệ
     */
    public function validateToken($token)
    {
        // Giải mã token (base64 encoded JSON)
        $tokenData = json_decode(base64_decode($token), true);

        if (!$tokenData) {
            return false;
        }

        // Kiểm tra timestamp (token chỉ có hiệu lực trong 24h)
        if (!isset($tokenData['timestamp'])) {
            return false;
        }

        $timestamp = $tokenData['timestamp'];
        $now = time();

        if ($now - $timestamp > 86400) { // 24 hours
            return false;
        }

        // Nếu là token của admin
        if (isset($tokenData['admin_id'])) {
            $adminId = $tokenData['admin_id'];
            $admin = $this->adminModel->getById($adminId);

            if (!$admin) {
                return false;
            }

            return $tokenData;
        }

        // Nếu là token của user
        if (isset($tokenData['user_id'])) {
            $userId = $tokenData['user_id'];
            $user = $this->userModel->getById($userId);

            if (!$user) {
                return false;
            }

            return $tokenData;
        }

        return false;
    }

    /**
     * Xử lý người dùng tham gia phòng
     * 
     * @param int $roomId ID của phòng
     * @param int $userId ID của người dùng
     * @return bool Kết quả thêm user vào phòng
     */
    public function joinRoom($roomId, $userId)
    {
        // Kiểm tra phòng có tồn tại không
        $room = $this->roomModel->getById($roomId);

        if (!$room) {
            return false;
        }

        // Kiểm tra phòng có đang mở không
        if ($room['status'] !== 'open') {
            return false;
        }

        // Kiểm tra user có quyền xem phim không
        if (!$this->roomModel->canUserWatch($userId, $roomId)) {
            return false;
        }

        // Thêm user vào phòng
        return $this->roomModel->addUser($roomId, $userId);
    }

    /**
     * Xử lý người dùng rời phòng
     * 
     * @param int $roomId ID của phòng
     * @param int $userId ID của người dùng
     * @return bool Kết quả xóa user khỏi phòng
     */
    public function leaveRoom($roomId, $userId)
    {
        return $this->roomModel->removeUser($roomId, $userId);
    }

    /**
     * Lấy danh sách người dùng trong phòng
     * 
     * @param int $roomId ID của phòng
     * @return array Danh sách người dùng
     */
    public function getUsers($roomId)
    {
        return $this->roomModel->getUsers($roomId);
    }

    /**
     * Đếm số người dùng trong phòng
     * 
     * @param int $roomId ID của phòng
     * @return int Số người dùng
     */
    public function countUsers($roomId)
    {
        return $this->roomModel->countUsers($roomId);
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
     * Mở phòng
     * 
     * @param int $roomId ID của phòng
     * @return bool Kết quả mở phòng
     */
    public function openRoom($roomId)
    {
        return $this->roomModel->open($roomId);
    }

    /**
     * Đóng phòng
     * 
     * @param int $roomId ID của phòng
     * @return bool Kết quả đóng phòng
     */
    public function closeRoom($roomId)
    {
        // Đóng phòng
        $result = $this->roomModel->close($roomId);

        // Xóa tất cả người dùng khỏi phòng
        if ($result) {
            $this->roomModel->removeAllUsers($roomId);
        }

        return $result;
    }

    /**
     * Lấy thông tin phòng
     * 
     * @param int $roomId ID của phòng
     * @return array|null Thông tin phòng
     */
    public function getRoomInfo($roomId)
    {
        return $this->roomModel->getById($roomId);
    }

    /**
     * Kiểm tra user có phải là admin của phòng không
     * 
     * @param int $roomId ID của phòng
     * @param int $adminId ID của admin
     * @return bool Kết quả kiểm tra
     */
    public function isRoomAdmin($roomId, $adminId)
    {
        $room = $this->roomModel->getById($roomId);

        if (!$room) {
            return false;
        }

        return $room['admin_id'] == $adminId;
    }
}
