<?php
// app/socket/ChatHandler.php
namespace App\Socket;

use App\Models\Chat;
use App\Models\User;

class ChatHandler
{
    private $chatModel;
    private $userModel;

    public function __construct()
    {
        $this->chatModel = new Chat();
        $this->userModel = new User();
    }

    /**
     * Xử lý tin nhắn chat
     * 
     * @param int $roomId ID của phòng
     * @param int $userId ID của người dùng (null nếu là admin)
     * @param int $adminId ID của admin (null nếu là người dùng thường)
     * @param string $message Nội dung tin nhắn
     * @return int ID của tin nhắn đã tạo
     */
    public function handleMessage($roomId, $userId, $adminId, $message)
    {
        // Tạo dữ liệu tin nhắn
        $chatData = [
            'room_id' => $roomId,
            'user_id' => $userId,
            'admin_id' => $adminId,
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Lưu tin nhắn vào database
        return $this->chatModel->addMessage($chatData);
    }

    /**
     * Lấy tin nhắn gần đây của phòng
     * 
     * @param int $roomId ID của phòng
     * @param int $limit Số lượng tin nhắn tối đa
     * @return array Danh sách tin nhắn
     */
    public function getRecentMessages($roomId, $limit = 20)
    {
        return $this->chatModel->getMessagesByRoomId($roomId, $limit);
    }

    /**
     * Lọc nội dung tin nhắn (loại bỏ từ ngữ nhạy cảm)
     * 
     * @param string $message Tin nhắn gốc
     * @return string Tin nhắn đã lọc
     */
    public function filterMessage($message)
    {
        // Danh sách các từ ngữ cần lọc
        $badWords = [
            'fuck',
            'shit',
            'damn',
            'bitch',
            'ass',
            'đụ',
            'địt',
            'đm',
            'đéo',
            'lồn',
            'buồi',
            'cặc'
        ];

        // Thay thế các từ ngữ cần lọc bằng dấu *
        foreach ($badWords as $word) {
            $replacement = str_repeat('*', strlen($word));
            $message = preg_replace('/\b' . preg_quote($word, '/') . '\b/i', $replacement, $message);
        }

        return $message;
    }

    /**
     * Xóa tin nhắn
     * 
     * @param int $messageId ID của tin nhắn
     * @return bool Kết quả xóa
     */
    public function deleteMessage($messageId)
    {
        return $this->chatModel->deleteMessage($messageId);
    }

    /**
     * Lấy thông tin người dùng để hiển thị trong chat
     * 
     * @param int $userId ID của người dùng
     * @return array|null Thông tin người dùng
     */
    public function getUserInfo($userId)
    {
        $user = $this->userModel->getById($userId);

        if ($user) {
            return [
                'id' => $user['id'],
                'username' => $user['username'],
                'avatar' => $user['avatar'] ?? 'default-avatar.jpg'
            ];
        }

        return null;
    }

    /**
     * Xóa tất cả tin nhắn của phòng
     * 
     * @param int $roomId ID của phòng
     * @return bool Kết quả xóa
     */
    public function clearRoomMessages($roomId)
    {
        return $this->chatModel->deleteMessagesByRoomId($roomId);
    }
}
