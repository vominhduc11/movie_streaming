<?php
// app/models/User.php
namespace App\Models;

use App\Core\Database;

class User
{
    private $db;
    private $table = 'users';

    public function __construct()
    {
        $this->db = new Database();
    }

    // Kiểm tra đăng nhập
    public function login($email, $password)
    {
        $user = $this->db->fetch("SELECT * FROM {$this->table} WHERE email = ? AND is_active = 1", [$email]);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    // Đăng ký tài khoản
    public function register($userData)
    {
        // Hash password
        $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);

        // Insert user
        return $this->db->insert($this->table, $userData);
    }

    // Kiểm tra email tồn tại
    public function emailExists($email)
    {
        $count = $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->table} WHERE email = ?", [$email]);
        return $count > 0;
    }

    // Kiểm tra username tồn tại
    public function usernameExists($username)
    {
        $count = $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->table} WHERE username = ?", [$username]);
        return $count > 0;
    }

    // Lấy thông tin user theo ID
    public function getById($id)
    {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    // Cập nhật thông tin user
    public function update($id, $data)
    {
        return $this->db->update($this->table, $data, "id = ?", [$id]);
    }

    // Cập nhật mật khẩu
    public function updatePassword($id, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->db->update($this->table, ['password' => $hashedPassword], "id = ?", [$id]);
    }

    // Lấy danh sách phim đã mua
    public function getPurchasedMovies($userId)
    {
        return $this->db->fetchAll(
            "SELECT m.* FROM movies m 
            JOIN user_movies um ON m.id = um.movie_id 
            WHERE um.user_id = ?",
            [$userId]
        );
    }

    // Kiểm tra user đã mua phim chưa
    public function hasPurchasedMovie($userId, $movieId)
    {
        $count = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM user_movies WHERE user_id = ? AND movie_id = ?",
            [$userId, $movieId]
        );
        return $count > 0;
    }

    // Mua phim
    public function purchaseMovie($userId, $movieId)
    {
        $this->db->beginTransaction();

        try {
            // Kiểm tra đã mua chưa
            if ($this->hasPurchasedMovie($userId, $movieId)) {
                $this->db->rollback();
                return ['status' => false, 'message' => 'Bạn đã mua phim này rồi'];
            }

            // Lấy thông tin user và phim
            $user = $this->getById($userId);
            $movie = $this->db->fetch("SELECT * FROM movies WHERE id = ?", [$movieId]);

            // Kiểm tra phim tồn tại
            if (!$movie) {
                $this->db->rollback();
                return ['status' => false, 'message' => 'Phim không tồn tại'];
            }

            // Kiểm tra số dư
            if ($user['balance'] < $movie['price']) {
                $this->db->rollback();
                return ['status' => false, 'message' => 'Số dư không đủ'];
            }

            // Trừ tiền
            $newBalance = $user['balance'] - $movie['price'];
            $this->db->update($this->table, ['balance' => $newBalance], "id = ?", [$userId]);

            // Thêm vào danh sách phim đã mua
            $this->db->insert('user_movies', [
                'user_id' => $userId,
                'movie_id' => $movieId
            ]);

            // Thêm vào lịch sử thanh toán
            $this->db->insert('payments', [
                'user_id' => $userId,
                'movie_id' => $movieId,
                'amount' => $movie['price'],
                'status' => 'completed',
                'payment_method' => 'balance'
            ]);

            $this->db->commit();
            return ['status' => true, 'message' => 'Mua phim thành công'];
        } catch (\Exception $e) {
            $this->db->rollback();
            return ['status' => false, 'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()];
        }
    }

    // Nạp tiền vào tài khoản
    public function addBalance($userId, $amount)
    {
        $user = $this->getById($userId);
        $newBalance = $user['balance'] + $amount;

        return $this->db->update($this->table, ['balance' => $newBalance], "id = ?", [$userId]);
    }

    // Lấy danh sách tất cả user
    public function getAll($limit = null, $offset = null)
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";

            if ($offset !== null) {
                $sql .= " OFFSET {$offset}";
            }
        }

        return $this->db->fetchAll($sql);
    }

    // Đếm tổng số user
    public function countAll()
    {
        return $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->table}");
    }
}
