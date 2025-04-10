<?php
// app/models/Chat.php
namespace App\Models;

use App\Core\Database;

class Chat
{
    private $db;
    private $table = 'chat_messages';

    public function __construct()
    {
        $this->db = new Database();
    }

    // Thêm tin nhắn mới
    public function addMessage($data)
    {
        return $this->db->insert($this->table, $data);
    }

    // Lấy tin nhắn của phòng
    public function getMessagesByRoomId($roomId, $limit = 50, $offset = 0)
    {
        return $this->db->fetchAll(
            "SELECT c.*, u.username, u.avatar
            FROM {$this->table} c
            JOIN users u ON c.user_id = u.id
            WHERE c.room_id = ?
            ORDER BY c.created_at DESC
            LIMIT ? OFFSET ?",
            [$roomId, $limit, $offset]
        );
    }

    // Lấy tin nhắn của phòng theo khoảng thời gian
    public function getMessagesByRoomIdAndTimeRange($roomId, $startTime, $endTime)
    {
        return $this->db->fetchAll(
            "SELECT c.*, u.username, u.avatar
            FROM {$this->table} c
            JOIN users u ON c.user_id = u.id
            WHERE c.room_id = ? AND c.created_at BETWEEN ? AND ?
            ORDER BY c.created_at ASC",
            [$roomId, $startTime, $endTime]
        );
    }

    // Đếm số tin nhắn của phòng
    public function countMessagesByRoomId($roomId)
    {
        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->table} WHERE room_id = ?",
            [$roomId]
        );
    }

    // Xóa tin nhắn theo ID
    public function deleteMessage($id)
    {
        return $this->db->delete($this->table, "id = ?", [$id]);
    }

    // Xóa tất cả tin nhắn của phòng
    public function deleteMessagesByRoomId($roomId)
    {
        return $this->db->delete($this->table, "room_id = ?", [$roomId]);
    }

    // Lấy tin nhắn mới nhất của phòng
    public function getLatestMessageByRoomId($roomId)
    {
        return $this->db->fetch(
            "SELECT c.*, u.username, u.avatar
            FROM {$this->table} c
            JOIN users u ON c.user_id = u.id
            WHERE c.room_id = ?
            ORDER BY c.created_at DESC
            LIMIT 1",
            [$roomId]
        );
    }

    // Tìm kiếm tin nhắn trong phòng
    public function searchMessages($roomId, $keyword, $limit = 20, $offset = 0)
    {
        return $this->db->fetchAll(
            "SELECT c.*, u.username, u.avatar
            FROM {$this->table} c
            JOIN users u ON c.user_id = u.id
            WHERE c.room_id = ? AND c.message LIKE ?
            ORDER BY c.created_at DESC
            LIMIT ? OFFSET ?",
            [$roomId, "%{$keyword}%", $limit, $offset]
        );
    }

    // Lấy thống kê chat của người dùng
    public function getUserChatStats($userId)
    {
        // Tổng số tin nhắn đã gửi
        $totalMessages = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->table} WHERE user_id = ?",
            [$userId]
        );

        // Số phòng đã chat
        $roomsCount = $this->db->fetchColumn(
            "SELECT COUNT(DISTINCT room_id) FROM {$this->table} WHERE user_id = ?",
            [$userId]
        );

        // Thời gian chat gần đây nhất
        $lastChatTime = $this->db->fetchColumn(
            "SELECT MAX(created_at) FROM {$this->table} WHERE user_id = ?",
            [$userId]
        );

        return [
            'total_messages' => $totalMessages,
            'rooms_count' => $roomsCount,
            'last_chat_time' => $lastChatTime
        ];
    }

    // Lấy thống kê chat của phòng
    public function getRoomChatStats($roomId)
    {
        // Tổng số tin nhắn
        $totalMessages = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->table} WHERE room_id = ?",
            [$roomId]
        );

        // Số người dùng đã chat
        $usersCount = $this->db->fetchColumn(
            "SELECT COUNT(DISTINCT user_id) FROM {$this->table} WHERE room_id = ?",
            [$roomId]
        );

        // Thời gian chat gần đây nhất
        $lastChatTime = $this->db->fetchColumn(
            "SELECT MAX(created_at) FROM {$this->table} WHERE room_id = ?",
            [$roomId]
        );

        // Người dùng chat nhiều nhất
        $mostActiveUser = $this->db->fetch(
            "SELECT u.id, u.username, u.avatar, COUNT(*) as message_count
            FROM {$this->table} c
            JOIN users u ON c.user_id = u.id
            WHERE c.room_id = ?
            GROUP BY u.id, u.username, u.avatar
            ORDER BY message_count DESC
            LIMIT 1",
            [$roomId]
        );

        return [
            'total_messages' => $totalMessages,
            'users_count' => $usersCount,
            'last_chat_time' => $lastChatTime,
            'most_active_user' => $mostActiveUser
        ];
    }
}
