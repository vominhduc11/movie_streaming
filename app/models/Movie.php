<?php
// app/models/Movie.php
namespace App\Models;

use App\Core\Database;

class Movie
{
    private $db;
    private $table = 'movies';

    public function __construct()
    {
        $this->db = new Database();
    }

    // Lấy tất cả phim
    public function getAll($limit = null, $offset = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";

            if ($offset !== null) {
                $sql .= " OFFSET {$offset}";
            }
        }

        return $this->db->fetchAll($sql);
    }

    // Đếm tổng số phim
    public function countAll()
    {
        return $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->table} WHERE is_active = 1");
    }

    // Lấy phim theo ID
    public function getById($id)
    {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    // Tìm kiếm phim
    public function search($keyword, $limit = null, $offset = null)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE is_active = 1 AND (title LIKE ? OR description LIKE ?) 
                ORDER BY created_at DESC";

        $params = ["%{$keyword}%", "%{$keyword}%"];

        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";

            if ($offset !== null) {
                $sql .= " OFFSET {$offset}";
            }
        }

        return $this->db->fetchAll($sql, $params);
    }

    // Đếm số phim tìm kiếm
    public function countSearch($keyword)
    {
        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->table} 
            WHERE is_active = 1 AND (title LIKE ? OR description LIKE ?)",
            ["%{$keyword}%", "%{$keyword}%"]
        );
    }

    // Thêm phim mới
    public function add($data)
    {
        return $this->db->insert($this->table, $data);
    }

    // Cập nhật phim
    public function update($id, $data)
    {
        return $this->db->update($this->table, $data, "id = ?", [$id]);
    }

    // Xóa phim (soft delete)
    public function delete($id)
    {
        return $this->db->update($this->table, ['is_active' => 0], "id = ?", [$id]);
    }

    // Phục hồi phim đã xóa
    public function restore($id)
    {
        return $this->db->update($this->table, ['is_active' => 1], "id = ?", [$id]);
    }

    // Tăng lượt xem
    public function incrementViews($id)
    {
        $movie = $this->getById($id);
        $newViews = $movie['views'] + 1;

        return $this->db->update($this->table, ['views' => $newViews], "id = ?", [$id]);
    }

    // Lấy phim theo thể loại
    public function getByGenre($genre, $limit = null, $offset = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 AND genre = ? ORDER BY created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";

            if ($offset !== null) {
                $sql .= " OFFSET {$offset}";
            }
        }

        return $this->db->fetchAll($sql, [$genre]);
    }

    // Lấy phim theo năm phát hành
    public function getByYear($year, $limit = null, $offset = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 AND release_year = ? ORDER BY created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";

            if ($offset !== null) {
                $sql .= " OFFSET {$offset}";
            }
        }

        return $this->db->fetchAll($sql, [$year]);
    }

    // Lấy phim phổ biến (nhiều lượt xem)
    public function getPopular($limit = 10)
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY views DESC LIMIT ?",
            [$limit]
        );
    }

    // Lấy phim mới nhất
    public function getLatest($limit = 10)
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY created_at DESC LIMIT ?",
            [$limit]
        );
    }

    // Lấy tất cả thể loại phim
    public function getAllGenres()
    {
        return $this->db->fetchAll("SELECT DISTINCT genre FROM {$this->table} WHERE is_active = 1 AND genre != ''");
    }

    // Lấy tất cả năm phát hành
    public function getAllYears()
    {
        return $this->db->fetchAll("SELECT DISTINCT release_year FROM {$this->table} WHERE is_active = 1 ORDER BY release_year DESC");
    }
}
