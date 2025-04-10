<?php
// app/models/Room.php
namespace App\Models;

use App\Core\Database;

class Room
{
    private $db;
    private $table = 'rooms';

    public function __construct()
    {
        $this->db = new Database();
    }

    // Lấy tất cả phòng
    public function getAll($limit = null, $offset = null)
    {
        $sql = "SELECT r.*, m.title as movie_title, a.username as admin_username 
                FROM {$this->table} r
                JOIN movies m ON r.movie_id = m.id
                JOIN admins a ON r.admin_id = a.id
                ORDER BY r.created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";

            if ($offset !== null) {
                $sql .= " OFFSET {$offset}";
            }
        }

        return $this->db->fetchAll($sql);
    }

    // Lấy tất cả phòng đang mở
    public function getAllOpen($limit = null, $offset = null)
    {
        $sql = "SELECT r.*, m.title as movie_title, a.username as admin_username 
                FROM {$this->table} r
                JOIN movies m ON r.movie_id = m.id
                JOIN admins a ON r.admin_id = a.id
                WHERE r.status = 'open'
                ORDER BY r.created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";

            if ($offset !== null) {
                $sql .= " OFFSET {$offset}";
            }
        }

        return $this->db->fetchAll($sql);
    }

    // Đếm tổng số phòng
    public function countAll()
    {
        return $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->table}");
    }

    // Đếm số phòng đang mở
    public function countOpen()
    {
        return $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->table} WHERE status = 'open'");
    }

    // Lấy phòng theo ID
    public function getById($id)
    {
        return $this->db->fetch(
            "SELECT r.*, m.title as movie_title, m.file_path, a.username as admin_username 
            FROM {$this->table} r
            JOIN movies m ON r.movie_id = m.id
            JOIN admins a ON r.admin_id = a.id
            WHERE r.id = ?",
            [$id]
        );
    }

    // Tạo phòng mới
    public function create($data)
    {
        return $this->db->insert($this->table, $data);
    }

    // Mở phòng
    public function open($id)
    {
        return $this->db->update($this->table, ['status' => 'open'], "id = ?", [$id]);
    }

    // Đóng phòng
    public function close($id)
    {
        return $this->db->update($this->table, ['status' => 'closed'], "id = ?", [$id]);
    }

    // Cập nhật thời gian hiện tại của video
    public function updateCurrentTime($id, $currentTime)
    {
        return $this->db->update($this->table, ['current_time' => $currentTime], "id = ?", [$id]);
    }

    // Kiểm tra user đã vào phòng chưa
    public function userInRoom($roomId, $userId)
    {
        $count = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM room_users 
            WHERE room_id = ? AND user_id = ? AND left_at IS NULL",
            [$roomId, $userId]
        );

        return $count > 0;
    }

    // Thêm user vào phòng
    public function addUser($roomId, $userId)
    {
        // Kiểm tra user đã trong phòng chưa
        if ($this->userInRoom($roomId, $userId)) {
            return true;
        }

        // Thêm user vào phòng
        return $this->db->insert('room_users', [
            'room_id' => $roomId,
            'user_id' => $userId
        ]);
    }

    // User rời phòng
    public function removeUser($roomId, $userId)
    {
        return $this->db->update(
            'room_users',
            ['left_at' => date('Y-m-d H:i:s')],
            "room_id = ? AND user_id = ? AND left_at IS NULL",
            [$roomId, $userId]
        );
    }

    // Lấy danh sách user trong phòng
    public function getUsers($roomId)
    {
        return $this->db->fetchAll(
            "SELECT u.id, u.username, u.avatar 
            FROM users u
            JOIN room_users ru ON u.id = ru.user_id
            WHERE ru.room_id = ? AND ru.left_at IS NULL",
            [$roomId]
        );
    }

    // Đếm số user trong phòng
    public function countUsers($roomId)
    {
        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM room_users WHERE room_id = ? AND left_at IS NULL",
            [$roomId]
        );
    }

    // Xóa tất cả user khỏi phòng khi đóng phòng
    public function removeAllUsers($roomId)
    {
        return $this->db->update(
            'room_users',
            ['left_at' => date('Y-m-d H:i:s')],
            "room_id = ? AND left_at IS NULL",
            [$roomId]
        );
    }

    // Kiểm tra user có quyền xem phim hay không
    public function canUserWatch($userId, $roomId)
    {
        // Lấy thông tin phòng
        $room = $this->getById($roomId);

        if (!$room || $room['status'] !== 'open') {
            return false;
        }

        // Kiểm tra user đã mua phim chưa
        $hasPurchased = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM user_movies WHERE user_id = ? AND movie_id = ?",
            [$userId, $room['movie_id']]
        );

        return $hasPurchased > 0;
    }

    // Lấy phòng theo movie_id
    public function getByMovieId($movieId)
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE movie_id = ? ORDER BY created_at DESC",
            [$movieId]
        );
    }

    // Lấy phòng đang mở theo movie_id
    public function getOpenByMovieId($movieId)
    {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE movie_id = ? AND status = 'open' LIMIT 1",
            [$movieId]
        );
    }
}
