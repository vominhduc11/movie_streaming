<?php
// app/controllers/RoomController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Room;
use App\Models\Movie;
use App\Models\User;

class RoomController extends Controller
{
    private $roomModel;
    private $movieModel;
    private $userModel;

    public function __construct()
    {
        $this->roomModel = $this->model('Room');
        $this->movieModel = $this->model('Movie');
        $this->userModel = $this->model('User');
    }

    // Hiển thị danh sách phòng đang mở
    public function index()
    {
        // Kiểm tra đăng nhập
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
            return;
        }

        $page = (int) $this->getQuery('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Lấy danh sách phòng đang mở
        $rooms = $this->roomModel->getAllOpen($limit, $offset);
        $totalRooms = $this->roomModel->countOpen();

        // Tính toán phân trang
        $totalPages = ceil($totalRooms / $limit);

        // Render view
        $this->view('rooms/index', [
            'rooms' => $rooms,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalRooms' => $totalRooms,
            'title' => APP_NAME . ' - Danh sách phòng xem phim'
        ]);
    }

    // Vào phòng xem phim
    public function viewRoom($id)
    {
        // Kiểm tra đăng nhập
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
            return;
        }

        // Lấy thông tin phòng
        $room = $this->roomModel->getById($id);

        if (!$room) {
            $this->redirect('home/error/404');
            return;
        }

        // Kiểm tra phòng có đang mở không
        if ($room['status'] !== 'open') {
            $_SESSION['error_message'] = 'Phòng này đã đóng.';
            $this->redirect('rooms');
            return;
        }

        $userId = $_SESSION['user_id'];

        // Kiểm tra user đã mua phim chưa
        if (!$this->userModel->hasPurchasedMovie($userId, $room['movie_id'])) {
            $_SESSION['error_message'] = 'Bạn chưa mua phim này.';
            $this->redirect('movies/detail/' . $room['movie_id']);
            return;
        }

        // Lấy thông tin phim
        $movie = $this->movieModel->getById($room['movie_id']);

        // Lấy danh sách user trong phòng
        $users = $this->roomModel->getUsers($id);

        // Thêm user vào phòng
        $this->roomModel->addUser($id, $userId);

        // Tạo token cho WebSocket
        $token = $this->generateToken($userId, $id);

        // Render view
        $this->view('rooms/viewRoom', [
            'room' => $room,
            'movie' => $movie,
            'users' => $users,
            'token' => $token,
            'title' => APP_NAME . ' - ' . $room['name']
        ]);
    }

    // Admin view phòng xem phim
    public function adminView($id)
    {
        // Kiểm tra đăng nhập admin
        if (!$this->isAdmin()) {
            $this->redirect('auth/admin-login');
            return;
        }

        // Lấy thông tin phòng
        $room = $this->roomModel->getById($id);

        if (!$room) {
            $this->redirect('home/error/404');
            return;
        }

        $adminId = $_SESSION['admin_id'];

        // Lấy thông tin phim
        $movie = $this->movieModel->getById($room['movie_id']);

        // Lấy danh sách user trong phòng
        $users = $this->roomModel->getUsers($id);

        // Tạo token cho WebSocket
        $token = $this->generateAdminToken($adminId, $id);

        // Render view
        $this->view('admin/rooms/view', [
            'room' => $room,
            'movie' => $movie,
            'users' => $users,
            'token' => $token,
            'title' => APP_NAME . ' - Admin: ' . $room['name']
        ]);
    }

    // API lấy danh sách phòng
    public function getRooms()
    {
        // Kiểm tra đăng nhập
        if (!$this->isLoggedIn()) {
            $this->json(['error' => 'Bạn chưa đăng nhập.'], 401);
            return;
        }

        $limit = (int) $this->getQuery('limit', 10);
        $offset = (int) $this->getQuery('offset', 0);

        // Lấy danh sách phòng đang mở
        $rooms = $this->roomModel->getAllOpen($limit, $offset);
        $totalRooms = $this->roomModel->countOpen();

        $this->json([
            'rooms' => $rooms,
            'total' => $totalRooms
        ]);
    }

    // API lấy danh sách user trong phòng
    public function getUsers($roomId)
    {
        // Kiểm tra đăng nhập
        if (!$this->isLoggedIn() && !$this->isAdmin()) {
            $this->json(['error' => 'Bạn chưa đăng nhập.'], 401);
            return;
        }

        // Lấy danh sách user trong phòng
        $users = $this->roomModel->getUsers($roomId);

        $this->json([
            'users' => $users,
            'total' => count($users)
        ]);
    }

    // Rời phòng xem phim
    public function leave($id)
    {
        // Kiểm tra đăng nhập
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
            return;
        }

        $userId = $_SESSION['user_id'];

        // Đánh dấu user rời phòng
        $this->roomModel->removeUser($id, $userId);

        // Chuyển hướng đến trang danh sách phòng
        $this->redirect('rooms');
    }

    // Tạo token cho WebSocket
    private function generateToken($userId, $roomId)
    {
        // TODO: Sử dụng JWT hoặc phương pháp khác để tạo token an toàn hơn
        $data = [
            'user_id' => $userId,
            'room_id' => $roomId,
            'timestamp' => time()
        ];

        return base64_encode(json_encode($data));
    }

    // Tạo token admin cho WebSocket
    private function generateAdminToken($adminId, $roomId)
    {
        // TODO: Sử dụng JWT hoặc phương pháp khác để tạo token an toàn hơn
        $data = [
            'admin_id' => $adminId,
            'room_id' => $roomId,
            'timestamp' => time(),
            'is_admin' => true
        ];

        return base64_encode(json_encode($data));
    }
}
