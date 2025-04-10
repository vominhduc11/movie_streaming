<?php
// app/controllers/AdminController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Admin;
use App\Models\Movie;
use App\Models\Room;
use App\Models\User;
use App\Models\Payment;

class AdminController extends Controller
{
    private $adminModel;
    private $movieModel;
    private $roomModel;
    private $userModel;
    private $paymentModel;

    public function __construct()
    {
        // Kiểm tra đăng nhập admin cho tất cả các action
        if (!isset($_SESSION['admin_id']) && $_SERVER['REQUEST_URI'] !== '/admin/login') {
            header('Location: ' . APP_URL . '/auth/admin-login');
            exit;
        }

        $this->adminModel = $this->model('Admin');
        $this->movieModel = $this->model('Movie');
        $this->roomModel = $this->model('Room');
        $this->userModel = $this->model('User');
        $this->paymentModel = $this->model('Payment');
    }

    // Dashboard admin
    public function index()
    {
        // Lấy thống kê tổng quan
        $totalMovies = $this->movieModel->countAll();
        $totalUsers = $this->userModel->countAll();
        $totalRooms = $this->roomModel->countAll();
        $openRooms = $this->roomModel->countOpen();
        $totalRevenue = $this->paymentModel->getTotalRevenue();
        $recentPayments = $this->paymentModel->getRecent(5);
        $popularMovies = $this->movieModel->getPopular(5);

        // Render view
        $this->view('admin/dashboard', [
            'totalMovies' => $totalMovies,
            'totalUsers' => $totalUsers,
            'totalRooms' => $totalRooms,
            'openRooms' => $openRooms,
            'totalRevenue' => $totalRevenue,
            'recentPayments' => $recentPayments,
            'popularMovies' => $popularMovies,
            'title' => APP_NAME . ' - Admin Dashboard'
        ]);
    }

    // Quản lý phim
    public function movies()
    {
        $page = (int) $this->getQuery('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Lấy danh sách phim
        $movies = $this->movieModel->getAll($limit, $offset);
        $totalMovies = $this->movieModel->countAll();

        // Tính toán phân trang
        $totalPages = ceil($totalMovies / $limit);

        // Render view
        $this->view('admin/movies', [
            'movies' => $movies,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalMovies' => $totalMovies,
            'title' => APP_NAME . ' - Quản lý phim'
        ]);
    }

    // Thêm phim mới
    public function addMovie()
    {
        // Xử lý form thêm phim
        if ($this->isPost()) {
            $title = $this->getPost('title');
            $description = $this->getPost('description');
            $price = (float) $this->getPost('price');
            $duration = (int) $this->getPost('duration');
            $genre = $this->getPost('genre');
            $releaseYear = (int) $this->getPost('release_year');

            // Validate dữ liệu
            $errors = [];

            if (empty($title)) {
                $errors['title'] = 'Vui lòng nhập tiêu đề phim';
            }

            if ($price <= 0) {
                $errors['price'] = 'Giá phải lớn hơn 0';
            }

            if ($duration <= 0) {
                $errors['duration'] = 'Thời lượng phải lớn hơn 0';
            }

            // Upload thumbnail
            $thumbnail = 'default.jpg';
            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                $thumbnailName = time() . '_' . $_FILES['thumbnail']['name'];
                $thumbnailPath = ROOT_PATH . '/public/assets/uploads/thumbnails/' . $thumbnailName;

                if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbnailPath)) {
                    $thumbnail = $thumbnailName;
                } else {
                    $errors['thumbnail'] = 'Không thể upload thumbnail';
                }
            }

            // Upload file phim
            $filePath = '';
            if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
                $videoName = time() . '_' . $_FILES['video']['name'];
                $videoPath = ROOT_PATH . '/public/assets/uploads/movies/' . $videoName;

                if (move_uploaded_file($_FILES['video']['tmp_name'], $videoPath)) {
                    $filePath = $videoName;
                } else {
                    $errors['video'] = 'Không thể upload file phim';
                }
            } else {
                $errors['video'] = 'Vui lòng chọn file phim';
            }

            // Nếu không có lỗi thì tiến hành thêm phim
            if (empty($errors)) {
                $movieData = [
                    'title' => $title,
                    'description' => $description,
                    'price' => $price,
                    'duration' => $duration,
                    'thumbnail' => $thumbnail,
                    'file_path' => $filePath,
                    'genre' => $genre,
                    'release_year' => $releaseYear,
                    'is_active' => 1
                ];

                $movieId = $this->movieModel->add($movieData);

                if ($movieId) {
                    $_SESSION['success_message'] = 'Thêm phim thành công.';
                    $this->redirect('admin/movies');
                    return;
                } else {
                    $errors['add'] = 'Thêm phim không thành công, vui lòng thử lại';
                }
            }

            // Nếu có lỗi thì hiển thị lại form với thông báo lỗi
            $this->view('admin/add-movie', [
                'errors' => $errors,
                'title' => $title,
                'description' => $description,
                'price' => $price,
                'duration' => $duration,
                'genre' => $genre,
                'release_year' => $releaseYear,
                'title_page' => APP_NAME . ' - Thêm phim mới'
            ]);
            return;
        }

        // Hiển thị form thêm phim
        $this->view('admin/add-movie', [
            'title_page' => APP_NAME . ' - Thêm phim mới'
        ]);
    }

    // Sửa thông tin phim
    public function editMovie($id)
    {
        // Lấy thông tin phim
        $movie = $this->movieModel->getById($id);

        if (!$movie) {
            $this->redirect('home/error/404');
            return;
        }

        // Xử lý form sửa phim
        if ($this->isPost()) {
            $title = $this->getPost('title');
            $description = $this->getPost('description');
            $price = (float) $this->getPost('price');
            $duration = (int) $this->getPost('duration');
            $genre = $this->getPost('genre');
            $releaseYear = (int) $this->getPost('release_year');

            // Validate dữ liệu
            $errors = [];

            if (empty($title)) {
                $errors['title'] = 'Vui lòng nhập tiêu đề phim';
            }

            if ($price <= 0) {
                $errors['price'] = 'Giá phải lớn hơn 0';
            }

            if ($duration <= 0) {
                $errors['duration'] = 'Thời lượng phải lớn hơn 0';
            }

            // Upload thumbnail mới (nếu có)
            $thumbnail = $movie['thumbnail'];
            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                $thumbnailName = time() . '_' . $_FILES['thumbnail']['name'];
                $thumbnailPath = ROOT_PATH . '/public/assets/uploads/thumbnails/' . $thumbnailName;

                if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbnailPath)) {
                    $thumbnail = $thumbnailName;
                } else {
                    $errors['thumbnail'] = 'Không thể upload thumbnail';
                }
            }

            // Upload file phim mới (nếu có)
            $filePath = $movie['file_path'];
            if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
                $videoName = time() . '_' . $_FILES['video']['name'];
                $videoPath = ROOT_PATH . '/public/assets/uploads/movies/' . $videoName;

                if (move_uploaded_file($_FILES['video']['tmp_name'], $videoPath)) {
                    $filePath = $videoName;
                } else {
                    $errors['video'] = 'Không thể upload file phim';
                }
            }

            // Nếu không có lỗi thì tiến hành cập nhật phim
            if (empty($errors)) {
                $movieData = [
                    'title' => $title,
                    'description' => $description,
                    'price' => $price,
                    'duration' => $duration,
                    'thumbnail' => $thumbnail,
                    'file_path' => $filePath,
                    'genre' => $genre,
                    'release_year' => $releaseYear
                ];

                $result = $this->movieModel->update($id, $movieData);

                if ($result) {
                    $_SESSION['success_message'] = 'Cập nhật phim thành công.';
                    $this->redirect('admin/movies');
                    return;
                } else {
                    $errors['edit'] = 'Cập nhật phim không thành công, vui lòng thử lại';
                }
            }

            // Nếu có lỗi thì hiển thị lại form với thông báo lỗi
            $this->view('admin/edit-movie', [
                'errors' => $errors,
                'movie' => array_merge($movie, [
                    'title' => $title,
                    'description' => $description,
                    'price' => $price,
                    'duration' => $duration,
                    'genre' => $genre,
                    'release_year' => $releaseYear
                ]),
                'title_page' => APP_NAME . ' - Sửa phim'
            ]);
            return;
        }

        // Hiển thị form sửa phim
        $this->view('admin/edit-movie', [
            'movie' => $movie,
            'title_page' => APP_NAME . ' - Sửa phim'
        ]);
    }

    // Xóa phim
    public function deleteMovie($id)
    {
        // Xóa phim (soft delete)
        $result = $this->movieModel->delete($id);

        if ($result) {
            $_SESSION['success_message'] = 'Xóa phim thành công.';
        } else {
            $_SESSION['error_message'] = 'Xóa phim không thành công.';
        }

        $this->redirect('admin/movies');
    }

    // Phục hồi phim đã xóa
    public function restoreMovie($id)
    {
        // Phục hồi phim
        $result = $this->movieModel->restore($id);

        if ($result) {
            $_SESSION['success_message'] = 'Phục hồi phim thành công.';
        } else {
            $_SESSION['error_message'] = 'Phục hồi phim không thành công.';
        }

        $this->redirect('admin/movies');
    }

    // Quản lý phòng
    public function rooms()
    {
        $page = (int) $this->getQuery('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Lấy danh sách phòng
        $rooms = $this->roomModel->getAll($limit, $offset);
        $totalRooms = $this->roomModel->countAll();

        // Tính toán phân trang
        $totalPages = ceil($totalRooms / $limit);

        // Render view
        $this->view('admin/rooms', [
            'rooms' => $rooms,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalRooms' => $totalRooms,
            'title' => APP_NAME . ' - Quản lý phòng'
        ]);
    }

    // Tạo phòng mới
    public function addRoom()
    {
        // Lấy danh sách phim
        $movies = $this->movieModel->getAll();

        // Xử lý form tạo phòng
        if ($this->isPost()) {
            $name = $this->getPost('name');
            $movieId = (int) $this->getPost('movie_id');
            $adminId = $_SESSION['admin_id'];

            // Validate dữ liệu
            $errors = [];

            if (empty($name)) {
                $errors['name'] = 'Vui lòng nhập tên phòng';
            }

            if ($movieId <= 0) {
                $errors['movie_id'] = 'Vui lòng chọn phim';
            }

            // Nếu không có lỗi thì tiến hành tạo phòng
            if (empty($errors)) {
                $roomData = [
                    'name' => $name,
                    'movie_id' => $movieId,
                    'admin_id' => $adminId,
                    'status' => 'closed',
                    'current_time' => 0
                ];

                $roomId = $this->roomModel->create($roomData);

                if ($roomId) {
                    $_SESSION['success_message'] = 'Tạo phòng thành công.';
                    $this->redirect('admin/rooms');
                    return;
                } else {
                    $errors['add'] = 'Tạo phòng không thành công, vui lòng thử lại';
                }
            }

            // Nếu có lỗi thì hiển thị lại form với thông báo lỗi
            $this->view('admin/add-room', [
                'errors' => $errors,
                'movies' => $movies,
                'name' => $name,
                'movie_id' => $movieId,
                'title' => APP_NAME . ' - Tạo phòng mới'
            ]);
            return;
        }

        // Hiển thị form tạo phòng
        $this->view('admin/add-room', [
            'movies' => $movies,
            'title' => APP_NAME . ' - Tạo phòng mới'
        ]);
    }

    // Mở phòng
    public function openRoom($id)
    {
        // Kiểm tra phòng tồn tại
        $room = $this->roomModel->getById($id);

        if (!$room) {
            $this->redirect('home/error/404');
            return;
        }

        // Mở phòng
        $result = $this->roomModel->open($id);

        if ($result) {
            $_SESSION['success_message'] = 'Mở phòng thành công.';

            // Chuyển hướng đến trang quản lý phòng của admin
            $this->redirect('admin/rooms/view/' . $id);
            return;
        } else {
            $_SESSION['error_message'] = 'Mở phòng không thành công.';
            $this->redirect('admin/rooms');
            return;
        }
    }

    // Đóng phòng
    public function closeRoom($id)
    {
        // Kiểm tra phòng tồn tại
        $room = $this->roomModel->getById($id);

        if (!$room) {
            $this->redirect('home/error/404');
            return;
        }

        // Đóng phòng
        $result = $this->roomModel->close($id);

        // Đánh dấu tất cả user rời phòng
        $this->roomModel->removeAllUsers($id);

        if ($result) {
            $_SESSION['success_message'] = 'Đóng phòng thành công.';
        } else {
            $_SESSION['error_message'] = 'Đóng phòng không thành công.';
        }

        $this->redirect('admin/rooms');
    }

    // Quản lý user
    public function users()
    {
        $page = (int) $this->getQuery('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Lấy danh sách user
        $users = $this->userModel->getAll($limit, $offset);
        $totalUsers = $this->userModel->countAll();

        // Tính toán phân trang
        $totalPages = ceil($totalUsers / $limit);

        // Render view
        $this->view('admin/users', [
            'users' => $users,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalUsers' => $totalUsers,
            'title' => APP_NAME . ' - Quản lý người dùng'
        ]);
    }

    // Quản lý thanh toán
    public function payments()
    {
        $page = (int) $this->getQuery('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Lấy danh sách thanh toán
        $payments = $this->paymentModel->getAll($limit, $offset);
        $totalPayments = $this->paymentModel->countAll();
        $totalRevenue = $this->paymentModel->getTotalRevenue();

        // Tính toán phân trang
        $totalPages = ceil($totalPayments / $limit);

        // Render view
        $this->view('admin/payments', [
            'payments' => $payments,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalPayments' => $totalPayments,
            'totalRevenue' => $totalRevenue,
            'title' => APP_NAME . ' - Quản lý thanh toán'
        ]);
    }

    // API lấy thông tin thống kê
    public function getStats()
    {
        // Kiểm tra đăng nhập admin
        if (!$this->isAdmin()) {
            $this->json(['error' => 'Bạn không có quyền truy cập.'], 403);
            return;
        }

        // Lấy thống kê
        $totalMovies = $this->movieModel->countAll();
        $totalUsers = $this->userModel->countAll();
        $totalRooms = $this->roomModel->countAll();
        $openRooms = $this->roomModel->countOpen();
        $totalRevenue = $this->paymentModel->getTotalRevenue();

        $this->json([
            'total_movies' => $totalMovies,
            'total_users' => $totalUsers,
            'total_rooms' => $totalRooms,
            'open_rooms' => $openRooms,
            'total_revenue' => $totalRevenue
        ]);
    }
}
