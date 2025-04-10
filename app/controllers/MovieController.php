<?php
// app/controllers/MovieController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Movie;
use App\Models\User;
use App\Models\Room;

class MovieController extends Controller
{
    private $movieModel;
    private $userModel;
    private $roomModel;

    public function __construct()
    {
        $this->movieModel = $this->model('Movie');
        $this->userModel = $this->model('User');
        $this->roomModel = $this->model('Room');
    }

    // Hiển thị danh sách phim
    public function index()
    {
        $page = (int) $this->getQuery('page', 1);
        $limit = 12;
        $offset = ($page - 1) * $limit;

        // Lấy danh sách phim
        $movies = $this->movieModel->getAll($limit, $offset);
        $totalMovies = $this->movieModel->countAll();

        // Tính toán phân trang
        $totalPages = ceil($totalMovies / $limit);

        // Render view
        $this->view('movies/index', [
            'movies' => $movies,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalMovies' => $totalMovies,
            'title' => APP_NAME . ' - Danh sách phim'
        ]);
    }

    // Hiển thị chi tiết phim
    public function detail($id)
    {
        // Lấy thông tin phim
        $movie = $this->movieModel->getById($id);

        if (!$movie) {
            $this->redirect('home/error/404');
            return;
        }

        // Tăng lượt xem
        $this->movieModel->incrementViews($id);

        // Lấy thông tin user hiện tại
        $userId = $_SESSION['user_id'] ?? null;
        $hasPurchased = false;

        if ($userId) {
            $hasPurchased = $this->userModel->hasPurchasedMovie($userId, $id);
        }

        // Lấy phòng đang mở của phim (nếu có)
        $openRoom = $this->roomModel->getOpenByMovieId($id);

        // Render view
        $this->view('movies/detail', [
            'movie' => $movie,
            'hasPurchased' => $hasPurchased,
            'openRoom' => $openRoom,
            'title' => APP_NAME . ' - ' . $movie['title']
        ]);
    }

    // Lọc phim theo thể loại
    public function genre($genre)
    {
        $page = (int) $this->getQuery('page', 1);
        $limit = 12;
        $offset = ($page - 1) * $limit;

        // Lấy danh sách phim theo thể loại
        $movies = $this->movieModel->getByGenre($genre, $limit, $offset);
        $totalMovies = count($this->movieModel->getByGenre($genre));

        // Tính toán phân trang
        $totalPages = ceil($totalMovies / $limit);

        // Render view
        $this->view('movies/index', [
            'movies' => $movies,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalMovies' => $totalMovies,
            'genre' => $genre,
            'title' => APP_NAME . ' - Thể loại: ' . $genre
        ]);
    }

    // Lọc phim theo năm
    public function year($year)
    {
        $page = (int) $this->getQuery('page', 1);
        $limit = 12;
        $offset = ($page - 1) * $limit;

        // Lấy danh sách phim theo năm
        $movies = $this->movieModel->getByYear($year, $limit, $offset);
        $totalMovies = count($this->movieModel->getByYear($year));

        // Tính toán phân trang
        $totalPages = ceil($totalMovies / $limit);

        // Render view
        $this->view('movies/index', [
            'movies' => $movies,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalMovies' => $totalMovies,
            'year' => $year,
            'title' => APP_NAME . ' - Năm: ' . $year
        ]);
    }

    // Xử lý mua phim
    public function purchase($id)
    {
        // Kiểm tra đăng nhập
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
            return;
        }

        // Lấy thông tin phim
        $movie = $this->movieModel->getById($id);

        if (!$movie) {
            $this->redirect('home/error/404');
            return;
        }

        $userId = $_SESSION['user_id'];

        // Kiểm tra đã mua chưa
        if ($this->userModel->hasPurchasedMovie($userId, $id)) {
            $this->redirect('movies/detail/' . $id);
            return;
        }

        // Xử lý form thanh toán
        if ($this->isPost()) {
            $paymentMethod = $this->getPost('payment_method', 'balance');

            // Thanh toán bằng số dư tài khoản
            if ($paymentMethod === 'balance') {
                $result = $this->userModel->purchaseMovie($userId, $id);

                if ($result['status']) {
                    // Chuyển hướng đến trang chi tiết phim
                    $_SESSION['success_message'] = 'Mua phim thành công. Bạn có thể xem phim khi admin mở phòng.';
                    $this->redirect('movies/detail/' . $id);
                    return;
                } else {
                    // Hiển thị lại form thanh toán với thông báo lỗi
                    $this->view('movies/payment', [
                        'movie' => $movie,
                        'error' => $result['message'],
                        'title' => APP_NAME . ' - Thanh toán'
                    ]);
                    return;
                }
            }

            // TODO: Xử lý các phương thức thanh toán khác (Paypal, Stripe, ...)
        }

        // Hiển thị form thanh toán
        $this->view('movies/payment', [
            'movie' => $movie,
            'title' => APP_NAME . ' - Thanh toán'
        ]);
    }

    // API lấy danh sách phim
    public function getMovies()
    {
        $limit = (int) $this->getQuery('limit', 10);
        $offset = (int) $this->getQuery('offset', 0);

        $movies = $this->movieModel->getAll($limit, $offset);
        $total = $this->movieModel->countAll();

        $this->json([
            'movies' => $movies,
            'total' => $total
        ]);
    }

    // API tìm kiếm phim
    public function searchMovies()
    {
        $keyword = $this->getQuery('keyword', '');
        $limit = (int) $this->getQuery('limit', 10);
        $offset = (int) $this->getQuery('offset', 0);

        $movies = $this->movieModel->search($keyword, $limit, $offset);
        $total = $this->movieModel->countSearch($keyword);

        $this->json([
            'movies' => $movies,
            'total' => $total
        ]);
    }
}
