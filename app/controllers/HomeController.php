<?php
// app/controllers/HomeController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Movie;
use App\Models\Room;

class HomeController extends Controller
{
    private $movieModel;
    private $roomModel;

    public function __construct()
    {
        $this->movieModel = $this->model('Movie');
        $this->roomModel = $this->model('Room');
    }

    public function index()
    {
        // Lấy danh sách phim mới nhất
        $latestMovies = $this->movieModel->getLatest(8);

        // Lấy danh sách phim phổ biến
        $popularMovies = $this->movieModel->getPopular(8);

        // Lấy danh sách phòng đang mở
        $openRooms = $this->roomModel->getAllOpen(4);

        // Render view
        $this->view('home/index', [
            'latestMovies' => $latestMovies,
            'popularMovies' => $popularMovies,
            'openRooms' => $openRooms,
            'title' => APP_NAME . ' - Trang chủ'
        ]);
    }

    public function about()
    {
        $this->view('home/about', [
            'title' => APP_NAME . ' - Giới thiệu'
        ]);
    }

    public function contact()
    {
        $this->view('home/contact', [
            'title' => APP_NAME . ' - Liên hệ'
        ]);
    }

    public function search()
    {
        $keyword = $this->getQuery('keyword', '');
        $page = (int) $this->getQuery('page', 1);
        $limit = 12;
        $offset = ($page - 1) * $limit;

        // Tìm kiếm phim
        $movies = $this->movieModel->search($keyword, $limit, $offset);
        $totalMovies = $this->movieModel->countSearch($keyword);

        // Tính toán phân trang
        $totalPages = ceil($totalMovies / $limit);

        // Render view
        $this->view('movies/index', [
            'movies' => $movies,
            'keyword' => $keyword,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalMovies' => $totalMovies,
            'title' => APP_NAME . ' - Kết quả tìm kiếm: ' . $keyword
        ]);
    }

    public function error($code = 404)
    {
        http_response_code($code);

        $this->view('home/error', [
            'code' => $code,
            'title' => APP_NAME . ' - Lỗi ' . $code
        ]);
    }
}
