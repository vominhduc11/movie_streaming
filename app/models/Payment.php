<?php
// app/models/Payment.php
namespace App\Models;

use App\Core\Database;

class Payment
{
    private $db;
    private $table = 'payments';

    public function __construct()
    {
        $this->db = new Database();
    }

    // Lấy tất cả thanh toán
    public function getAll($limit = null, $offset = null)
    {
        $sql = "SELECT p.*, u.username, m.title as movie_title 
                FROM {$this->table} p
                JOIN users u ON p.user_id = u.id
                JOIN movies m ON p.movie_id = m.id
                ORDER BY p.created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";

            if ($offset !== null) {
                $sql .= " OFFSET {$offset}";
            }
        }

        return $this->db->fetchAll($sql);
    }

    // Đếm tổng số thanh toán
    public function countAll()
    {
        return $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->table}");
    }

    // Lấy thanh toán theo ID
    public function getById($id)
    {
        return $this->db->fetch(
            "SELECT p.*, u.username, m.title as movie_title 
            FROM {$this->table} p
            JOIN users u ON p.user_id = u.id
            JOIN movies m ON p.movie_id = m.id
            WHERE p.id = ?",
            [$id]
        );
    }

    // Lấy danh sách thanh toán của user
    public function getByUserId($userId, $limit = null, $offset = null)
    {
        $sql = "SELECT p.*, m.title as movie_title, m.thumbnail 
                FROM {$this->table} p
                JOIN movies m ON p.movie_id = m.id
                WHERE p.user_id = ?
                ORDER BY p.created_at DESC";

        $params = [$userId];

        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";

            if ($offset !== null) {
                $sql .= " OFFSET {$offset}";
            }
        }

        return $this->db->fetchAll($sql, $params);
    }

    // Đếm số thanh toán của user
    public function countByUserId($userId)
    {
        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->table} WHERE user_id = ?",
            [$userId]
        );
    }

    // Tạo thanh toán mới
    public function create($data)
    {
        return $this->db->insert($this->table, $data);
    }

    // Cập nhật trạng thái thanh toán
    public function updateStatus($id, $status)
    {
        return $this->db->update(
            $this->table,
            ['status' => $status],
            "id = ?",
            [$id]
        );
    }

    // Lấy tổng doanh thu
    public function getTotalRevenue()
    {
        return $this->db->fetchColumn(
            "SELECT SUM(amount) FROM {$this->table} WHERE status = 'completed'"
        );
    }

    // Lấy doanh thu theo khoảng thời gian
    public function getRevenueByDateRange($startDate, $endDate)
    {
        return $this->db->fetchColumn(
            "SELECT SUM(amount) FROM {$this->table} 
            WHERE status = 'completed' 
            AND created_at BETWEEN ? AND ?",
            [$startDate, $endDate]
        );
    }

    // Lấy doanh thu theo tháng
    public function getRevenueByMonth($month, $year)
    {
        return $this->db->fetchColumn(
            "SELECT SUM(amount) FROM {$this->table} 
            WHERE status = 'completed' 
            AND MONTH(created_at) = ? 
            AND YEAR(created_at) = ?",
            [$month, $year]
        );
    }

    // Lấy doanh thu theo ngày
    public function getRevenueByDate($date)
    {
        return $this->db->fetchColumn(
            "SELECT SUM(amount) FROM {$this->table} 
            WHERE status = 'completed' 
            AND DATE(created_at) = ?",
            [$date]
        );
    }

    // Lấy các thanh toán gần đây
    public function getRecent($limit = 5)
    {
        return $this->db->fetchAll(
            "SELECT p.*, u.username, m.title as movie_title 
            FROM {$this->table} p
            JOIN users u ON p.user_id = u.id
            JOIN movies m ON p.movie_id = m.id
            ORDER BY p.created_at DESC
            LIMIT ?",
            [$limit]
        );
    }

    // Thống kê thanh toán theo phương thức
    public function getPaymentMethodStats()
    {
        return $this->db->fetchAll(
            "SELECT payment_method, COUNT(*) as count, SUM(amount) as total
            FROM {$this->table}
            WHERE status = 'completed'
            GROUP BY payment_method"
        );
    }

    // Thống kê thanh toán theo trạng thái
    public function getPaymentStatusStats()
    {
        return $this->db->fetchAll(
            "SELECT status, COUNT(*) as count, SUM(amount) as total
            FROM {$this->table}
            GROUP BY status"
        );
    }

    // Hoàn tiền
    public function refund($id)
    {
        $payment = $this->getById($id);

        if (!$payment || $payment['status'] !== 'completed') {
            return false;
        }

        $this->db->beginTransaction();

        try {
            // Cập nhật trạng thái thanh toán thành 'refunded'
            $this->updateStatus($id, 'refunded');

            // Hoàn tiền vào tài khoản user
            $this->db->update(
                'users',
                ['balance' => "balance + {$payment['amount']}"],
                "id = ?",
                [$payment['user_id']]
            );

            // Xóa phim đã mua
            $this->db->delete(
                'user_movies',
                "user_id = ? AND movie_id = ?",
                [$payment['user_id'], $payment['movie_id']]
            );

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
}
