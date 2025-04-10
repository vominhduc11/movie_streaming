<?php
// app/core/Database.php
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $charset;
    private $options;
    private $conn;

    public function __construct()
    {
        $dbConfig = require APP_PATH . '/config/database.php';

        $this->host = $dbConfig['host'];
        $this->dbname = $dbConfig['dbname'];
        $this->username = $dbConfig['username'];
        $this->password = $dbConfig['password'];
        $this->charset = $dbConfig['charset'];
        $this->options = $dbConfig['options'];

        $this->connect();
    }

    // Kết nối database
    private function connect()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";

        try {
            $this->conn = new PDO($dsn, $this->username, $this->password, $this->options);
        } catch (PDOException $e) {
            die('Lỗi kết nối database: ' . $e->getMessage());
        }
    }

    // Thực hiện query
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            die('Lỗi truy vấn: ' . $e->getMessage());
        }
    }

    // Lấy nhiều dòng dữ liệu
    public function fetchAll($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchAll();
    }

    // Lấy một dòng dữ liệu
    public function fetch($sql, $params = [])
    {
        return $this->query($sql, $params)->fetch();
    }

    // Lấy một giá trị đơn lẻ
    public function fetchColumn($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchColumn();
    }

    // Insert dữ liệu
    public function insert($table, $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";

        $this->query($sql, array_values($data));
        return $this->conn->lastInsertId();
    }

    // Update dữ liệu
    public function update($table, $data, $where, $whereParams = [])
    {
        $sets = [];
        foreach (array_keys($data) as $column) {
            $sets[] = "{$column} = ?";
        }

        $sql = "UPDATE {$table} SET " . implode(', ', $sets) . " WHERE {$where}";

        $params = array_merge(array_values($data), $whereParams);
        $this->query($sql, $params);

        return $this->conn->rowCount();
    }

    // Delete dữ liệu
    public function delete($table, $where, $params = [])
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $this->query($sql, $params);

        return $this->conn->rowCount();
    }

    // Bắt đầu transaction
    public function beginTransaction()
    {
        return $this->conn->beginTransaction();
    }

    // Commit transaction
    public function commit()
    {
        return $this->conn->commit();
    }

    // Rollback transaction
    public function rollback()
    {
        return $this->conn->rollBack();
    }
}
