<?php
// app/models/Admin.php
namespace App\Models;

use App\Core\Database;

class Admin
{
    private $db;
    private $table = 'admins';

    public function __construct()
    {
        $this->db = new Database();
    }

    // Kiểm tra đăng nhập
    public function login($username, $password)
    {
        $admin = $this->db->fetch("SELECT * FROM {$this->table} WHERE username = ?", [$username]);

        if ($admin && password_verify($password, $admin['password'])) {
            return $admin;
        }

        return false;
    }

    // Lấy thông tin admin theo ID
    public function getById($id)
    {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    // Lấy danh sách admin
    public function getAll($limit = null, $offset = null)
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY id ASC";

        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";

            if ($offset !== null) {
                $sql .= " OFFSET {$offset}";
            }
        }

        return $this->db->fetchAll($sql);
    }

    // Thêm admin mới
    public function add($data)
    {
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        return $this->db->insert($this->table, $data);
    }

    // Cập nhật thông tin admin
    public function update($id, $data)
    {
        // Nếu cập nhật password thì hash password
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            // Không cập nhật password nếu trống
            unset($data['password']);
        }

        return $this->db->update($this->table, $data, "id = ?", [$id]);
    }

    // Xóa admin
    public function delete($id)
    {
        // Không cho phép xóa admin cuối cùng
        $count = $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->table}");
        if ($count <= 1) {
            return false;
        }

        return $this->db->delete($this->table, "id = ?", [$id]);
    }

    // Kiểm tra username tồn tại
    public function usernameExists($username, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE username = ?";
        $params = [$username];

        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $count = $this->db->fetchColumn($sql, $params);
        return $count > 0;
    }

    // Kiểm tra email tồn tại
    public function emailExists($email, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE email = ?";
        $params = [$email];

        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $count = $this->db->fetchColumn($sql, $params);
        return $count > 0;
    }

    // Lấy thống kê hoạt động của admin
    public function getAdminStats($adminId)
    {
        // Số phòng đã tạo
        $roomsCreated = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM rooms WHERE admin_id = ?",
            [$adminId]
        );

        // Số phòng đang mở
        $openRooms = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM rooms WHERE admin_id = ? AND status = 'open'",
            [$adminId]
        );

        // Tổng thời gian xem phim (phút)
        $totalWatchTime = $this->db->fetchColumn(
            "SELECT SUM(TIMESTAMPDIFF(MINUTE, ru.joined_at, IFNULL(ru.left_at, NOW()))) 
            FROM room_users ru
            JOIN rooms r ON r.id = ru.room_id
            WHERE r.admin_id = ?",
            [$adminId]
        );

        return [
            'rooms_created' => $roomsCreated,
            'open_rooms' => $openRooms,
            'total_watch_time' => $totalWatchTime ?: 0
        ];
    }

    // Đổi mật khẩu
    public function changePassword($id, $currentPassword, $newPassword)
    {
        // Kiểm tra mật khẩu hiện tại
        $admin = $this->getById($id);

        if (!$admin || !password_verify($currentPassword, $admin['password'])) {
            return false;
        }

        // Cập nhật mật khẩu mới
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->db->update($this->table, ['password' => $hashedPassword], "id = ?", [$id]);
    }
}
