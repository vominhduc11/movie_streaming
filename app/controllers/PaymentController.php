<?php
// app/controllers/PaymentController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Payment;
use App\Models\User;
use App\Models\Movie;

class PaymentController extends Controller
{
    private $paymentModel;
    private $userModel;
    private $movieModel;

    public function __construct()
    {
        $this->paymentModel = $this->model('Payment');
        $this->userModel = $this->model('User');
        $this->movieModel = $this->model('Movie');
    }

    // Trang lịch sử thanh toán của người dùng
    public function index()
    {
        // Kiểm tra đăng nhập
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
            return;
        }

        $userId = $_SESSION['user_id'];
        $page = (int) $this->getQuery('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Lấy danh sách thanh toán
        $payments = $this->paymentModel->getByUserId($userId, $limit, $offset);
        $totalPayments = $this->paymentModel->countByUserId($userId);

        // Tính toán phân trang
        $totalPages = ceil($totalPayments / $limit);

        // Lấy thông tin user
        $user = $this->userModel->getById($userId);

        // Render view
        $this->view('payments/index', [
            'payments' => $payments,
            'user' => $user,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalPayments' => $totalPayments,
            'title' => APP_NAME . ' - Lịch sử thanh toán'
        ]);
    }

    // Xem chi tiết thanh toán
    public function detail($id)
    {
        // Kiểm tra đăng nhập
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
            return;
        }

        $userId = $_SESSION['user_id'];

        // Lấy thông tin thanh toán
        $payment = $this->paymentModel->getById($id);

        // Kiểm tra thanh toán tồn tại và thuộc về user hiện tại
        if (!$payment || $payment['user_id'] != $userId) {
            $this->redirect('home/error/404');
            return;
        }

        // Render view
        $this->view('payments/detail', [
            'payment' => $payment,
            'title' => APP_NAME . ' - Chi tiết thanh toán #' . $id
        ]);
    }

    // Xử lý thanh toán từ số dư tài khoản
    public function processBalance()
    {
        // Kiểm tra phương thức request
        if (!$this->isPost()) {
            $this->redirect('home');
            return;
        }

        // Kiểm tra đăng nhập
        if (!$this->isLoggedIn()) {
            $this->json(['status' => 'error', 'message' => 'Bạn chưa đăng nhập'], 401);
            return;
        }

        $userId = $_SESSION['user_id'];
        $movieId = (int) $this->getPost('movie_id');

        // Kiểm tra dữ liệu
        if (!$movieId) {
            $this->json(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ'], 400);
            return;
        }

        // Xử lý thanh toán
        $result = $this->userModel->purchaseMovie($userId, $movieId);

        // Trả về kết quả
        $this->json($result);
    }

    // Xử lý thanh toán qua MoMo
    public function processMomo()
    {
        // Kiểm tra phương thức request
        if (!$this->isPost()) {
            $this->redirect('home');
            return;
        }

        // Kiểm tra đăng nhập
        if (!$this->isLoggedIn()) {
            $this->json(['status' => 'error', 'message' => 'Bạn chưa đăng nhập'], 401);
            return;
        }

        $userId = $_SESSION['user_id'];
        $movieId = (int) $this->getPost('movie_id');

        // Kiểm tra dữ liệu
        if (!$movieId) {
            $this->json(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ'], 400);
            return;
        }

        // Lấy thông tin phim
        $movie = $this->movieModel->getById($movieId);

        if (!$movie) {
            $this->json(['status' => 'error', 'message' => 'Phim không tồn tại'], 404);
            return;
        }

        // Tạo thanh toán với trạng thái 'pending'
        $paymentId = $this->paymentModel->create([
            'user_id' => $userId,
            'movie_id' => $movieId,
            'amount' => $movie['price'],
            'status' => 'pending',
            'payment_method' => 'momo',
            'transaction_id' => 'MOMO_' . time() . rand(1000, 9999)
        ]);

        // TODO: Tích hợp API MoMo thực tế
        // Trong demo này, chúng ta giả lập thành công

        // Trả về URL để chuyển hướng đến trang thanh toán MoMo
        $this->json([
            'status' => 'success',
            'message' => 'Đang chuyển hướng đến trang thanh toán MoMo',
            'payment_id' => $paymentId,
            'redirect_url' => APP_URL . '/payments/momo-redirect/' . $paymentId
        ]);
    }

    // Xử lý thanh toán qua thẻ
    public function processCard()
    {
        // Kiểm tra phương thức request
        if (!$this->isPost()) {
            $this->redirect('home');
            return;
        }

        // Kiểm tra đăng nhập
        if (!$this->isLoggedIn()) {
            $this->json(['status' => 'error', 'message' => 'Bạn chưa đăng nhập'], 401);
            return;
        }

        $userId = $_SESSION['user_id'];
        $movieId = (int) $this->getPost('movie_id');
        $cardNumber = $this->getPost('card_number');
        $cardExpiry = $this->getPost('card_expiry');
        $cardCVV = $this->getPost('card_cvv');
        $cardName = $this->getPost('card_name');

        // Kiểm tra dữ liệu
        if (!$movieId || !$cardNumber || !$cardExpiry || !$cardCVV || !$cardName) {
            $this->json(['status' => 'error', 'message' => 'Vui lòng nhập đầy đủ thông tin thẻ'], 400);
            return;
        }

        // Validate thông tin thẻ (demo)
        $cardNumber = preg_replace('/\s+/', '', $cardNumber);
        if (!preg_match('/^\d{16}$/', $cardNumber)) {
            $this->json(['status' => 'error', 'message' => 'Số thẻ không hợp lệ'], 400);
            return;
        }

        if (!preg_match('/^\d{2}\/\d{2}$/', $cardExpiry)) {
            $this->json(['status' => 'error', 'message' => 'Ngày hết hạn không hợp lệ'], 400);
            return;
        }

        if (!preg_match('/^\d{3,4}$/', $cardCVV)) {
            $this->json(['status' => 'error', 'message' => 'Mã CVV không hợp lệ'], 400);
            return;
        }

        // Lấy thông tin phim
        $movie = $this->movieModel->getById($movieId);

        if (!$movie) {
            $this->json(['status' => 'error', 'message' => 'Phim không tồn tại'], 404);
            return;
        }

        // Tạo thanh toán với trạng thái 'pending'
        $paymentId = $this->paymentModel->create([
            'user_id' => $userId,
            'movie_id' => $movieId,
            'amount' => $movie['price'],
            'status' => 'pending',
            'payment_method' => 'card',
            'transaction_id' => 'CARD_' . time() . rand(1000, 9999)
        ]);

        // TODO: Tích hợp cổng thanh toán thẻ thực tế
        // Trong demo này, chúng ta giả lập thành công

        // Cập nhật trạng thái thanh toán thành 'completed'
        $this->paymentModel->updateStatus($paymentId, 'completed');

        // Thêm phim vào danh sách phim đã mua
        $this->userModel->purchaseMovie($userId, $movieId);

        $this->json([
            'status' => 'success',
            'message' => 'Thanh toán thành công',
            'payment_id' => $paymentId,
            'redirect_url' => APP_URL . '/payments/success/' . $paymentId
        ]);
    }

    // Trang chuyển hướng sau khi thanh toán MoMo
    public function momoRedirect($paymentId)
    {
        // TODO: Xử lý callback từ MoMo
        // Trong demo này, chúng ta giả lập thành công

        // Cập nhật trạng thái thanh toán thành 'completed'
        $this->paymentModel->updateStatus($paymentId, 'completed');

        // Lấy thông tin thanh toán
        $payment = $this->paymentModel->getById($paymentId);

        // Thêm phim vào danh sách phim đã mua
        $this->userModel->purchaseMovie($payment['user_id'], $payment['movie_id']);

        // Chuyển hướng đến trang thành công
        $this->redirect('payments/success/' . $paymentId);
    }

    // Trang thành công sau khi thanh toán
    public function success($paymentId)
    {
        // Kiểm tra đăng nhập
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
            return;
        }

        $userId = $_SESSION['user_id'];

        // Lấy thông tin thanh toán
        $payment = $this->paymentModel->getById($paymentId);

        // Kiểm tra thanh toán tồn tại và thuộc về user hiện tại
        if (!$payment || $payment['user_id'] != $userId) {
            $this->redirect('home/error/404');
            return;
        }

        // Lấy thông tin phim
        $movie = $this->movieModel->getById($payment['movie_id']);

        // Render view
        $this->view('payments/success', [
            'payment' => $payment,
            'movie' => $movie,
            'title' => APP_NAME . ' - Thanh toán thành công'
        ]);
    }

    // Nạp tiền vào tài khoản
    public function topup()
    {
        // Kiểm tra đăng nhập
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
            return;
        }

        $userId = $_SESSION['user_id'];

        // Xử lý form nạp tiền
        if ($this->isPost()) {
            $amount = (float) $this->getPost('amount');
            $paymentMethod = $this->getPost('payment_method', 'momo');

            // Validate số tiền
            if ($amount <= 0) {
                $_SESSION['error_message'] = 'Số tiền nạp phải lớn hơn 0';
                $this->redirect('payments/topup');
                return;
            }

            // TODO: Tích hợp các cổng thanh toán thực tế
            // Trong demo này, chúng ta giả lập thành công

            // Nạp tiền vào tài khoản
            $this->userModel->addBalance($userId, $amount);

            $_SESSION['success_message'] = 'Nạp tiền thành công: ' . number_format($amount, 0, ',', '.') . ' VND';
            $this->redirect('users/profile');
            return;
        }

        // Lấy thông tin user
        $user = $this->userModel->getById($userId);

        // Render view
        $this->view('payments/topup', [
            'user' => $user,
            'title' => APP_NAME . ' - Nạp tiền'
        ]);
    }

    // API lấy lịch sử thanh toán
    public function getHistory()
    {
        // Kiểm tra đăng nhập
        if (!$this->isLoggedIn()) {
            $this->json(['status' => 'error', 'message' => 'Bạn chưa đăng nhập'], 401);
            return;
        }

        $userId = $_SESSION['user_id'];
        $limit = (int) $this->getQuery('limit', 10);
        $offset = (int) $this->getQuery('offset', 0);

        // Lấy danh sách thanh toán
        $payments = $this->paymentModel->getByUserId($userId, $limit, $offset);
        $totalPayments = $this->paymentModel->countByUserId($userId);

        $this->json([
            'status' => 'success',
            'data' => [
                'payments' => $payments,
                'total' => $totalPayments
            ]
        ]);
    }
}
