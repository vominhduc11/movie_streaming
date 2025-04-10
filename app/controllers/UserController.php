<?php
// app/controllers/UserController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Movie;
use App\Models\Payment;
use App\Models\Room;

class UserController extends Controller
{
    private $userModel;
    private $movieModel;
    private $paymentModel;
    private $roomModel;

    public function __construct()
    {
        $this->userModel = $this->model('User');
        $this->movieModel = $this->model('Movie');
        $this->paymentModel = $this->model('Payment');
        $this->roomModel = $this->model('Room');
    }

    // Trang thông tin cá nhân
    public function profile()
    {
        // Kiểm tra đăng nhập
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
            return;
        }

        $userId = $_SESSION['user_id'];

        // Lấy thông tin user
        $user = $this->userModel->getById($userId);

        // Lấy số lượng phim đã mua
        $purchasedMoviesCount = count($this->userModel->getPurchasedMovies($userId));

        // Lấy thanh toán gần đây
        $recentPayments = $this->paymentModel->getByUserId($userId, 5);

        // Render view
        $this->view('users/profile', [
            'user' => $user,
            'purchasedMoviesCount' => $purchasedMoviesCount,
            'recentPayments' => $recentPayments,
            'title' => APP_NAME . ' - Thông tin cá nhân'
        ]);
    }

    // Cập nhật thông tin cá nhân
    public function updateProfile()
    {
        // Kiểm tra đăng nhập
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
            return;
        }

        $userId = $_SESSION['user_id'];

        // Xử lý form cập nhật
        if ($this->isPost()) {
            $fullName = $this->getPost('full_name');
            $email = $this->getPost('email');

            // Validate dữ liệu
            $errors = [];

            if (empty($email)) {
                $errors['email'] = 'Vui lòng nhập email';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Email không hợp lệ';
            } elseif ($this->userModel->emailExists($email, $userId)) {
                $errors['email'] = 'Email đã tồn tại';
            }

            // Upload avatar mới (nếu có)
            $avatar = null;
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

                if (!in_array($_FILES['avatar']['type'], $allowedTypes)) {
                    $errors['avatar'] = 'Chỉ cho phép upload ảnh dạng JPG, PNG, GIF';
                } else {
                    $avatarName = time() . '_' . $_FILES['avatar']['name'];
                    $avatarPath = ROOT_PATH . '/public/assets/uploads/' . $avatarName;

                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $avatarPath)) {
                        $avatar = $avatarName;
                    } else {
                        $errors['avatar'] = 'Không thể upload avatar';
                    }
                }
            }

            // Nếu không có lỗi thì cập nhật thông tin
            if (empty($errors)) {
                $userData = [
                    'full_name' => $fullName,
                    'email' => $email
                ];

                // Thêm avatar nếu có
                if ($avatar) {
                    $userData['avatar'] = $avatar;
                }

                // Cập nhật thông tin
                $result = $this->userModel->update($userId, $userData);

                if ($result) {
                    // Cập nhật session
                    $_SESSION['email'] = $email;

                    $_SESSION['success_message'] = 'Cập nhật thông tin thành công';
                    $this->redirect('users/profile');
                    return;
                } else {
                    $errors['update'] = 'Cập nhật thông tin không thành công, vui lòng thử lại';
                }
            }

            // Lấy thông tin user
            $user = $this->userModel->getById($userId);

            // Render view với thông báo lỗi
            $this->view('users/edit-profile', [
                'user' => $user,
                'errors' => $errors,
                'title' => APP_NAME . ' - Cập nhật thông tin cá nhân'
            ]);
            return;
        }

        // Lấy thông tin user
        $user = $this->userModel->getById($userId);

        // Render view
        $this->view('users/edit-profile', [
            'user' => $user,
            'title' => APP_NAME . ' - Cập nhật thông tin cá nhân'
        ]);
    }

    // Đổi mật khẩu
    public function changePassword()
    {
        // Kiểm tra đăng nhập
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
            return;
        }

        $userId = $_SESSION['user_id'];

        // Xử lý form đổi mật khẩu
        if ($this->isPost()) {
            $currentPassword = $this->getPost('current_password');
            $newPassword = $this->getPost('new_password');
            $confirmPassword = $this->getPost('confirm_password');

            // Validate dữ liệu
            $errors = [];

            if (empty($currentPassword)) {
                $errors['current_password'] = 'Vui lòng nhập mật khẩu hiện tại';
            }

            if (empty($newPassword)) {
                $errors['new_password'] = 'Vui lòng nhập mật khẩu mới';
            } elseif (strlen($newPassword) < 6) {
                $errors['new_password'] = 'Mật khẩu mới phải có ít nhất 6 ký tự';
            }

            if ($newPassword !== $confirmPassword) {
                $errors['confirm_password'] = 'Mật khẩu xác nhận không khớp';
            }

            // Nếu không có lỗi thì tiến hành đổi mật khẩu
            if (empty($errors)) {
                // Lấy thông tin user
                $user = $this->userModel->getById($userId);

                // Kiểm tra mật khẩu hiện tại
                if (password_verify($currentPassword, $user['password'])) {
                    // Cập nhật mật khẩu mới
                    $result = $this->userModel->updatePassword($userId, $newPassword);

                    if ($result) {
                        $_SESSION['success_message'] = 'Đổi mật khẩu thành công';
                        $this->redirect('users/profile');
                        return;
                    } else {
                        $errors['update'] = 'Đổi mật khẩu không thành công, vui lòng thử lại';
                    }
                } else {
                    $errors['current_password'] = 'Mật khẩu hiện tại không đúng';
                }
            }

            // Render view với thông báo lỗi
            $this->view('users/change-password', [
                'errors' => $errors,
                'title' => APP_NAME . ' - Đổi mật khẩu'
            ]);
            return;
        }

        // Render view
        $this->view('users/change-password', [
            'title' => APP_NAME . ' - Đổi mật khẩu'
        ]);
    }

    // Trang phim đã mua
    public function movies()
    {
        // Kiểm tra đăng nhập
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
            return;
        }

        $userId = $_SESSION['user_id'];

        // Lấy danh sách phim đã mua
        $purchasedMovies = $this->userModel->getPurchasedMovies($userId);

        // Render view
        $this->view('users/movies', [
            'movies' => $purchasedMovies,
            'title' => APP_NAME . ' - Phim đã mua'
        ]);
    }

    // Trang lịch sử thanh toán
    public function payments()
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

        // Render view
        $this->view('users/payments', [
            'payments' => $payments,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalPayments' => $totalPayments,
            'title' => APP_NAME . ' - Lịch sử thanh toán'
        ]);
    }

    // Trang nạp tiền
    public function topup()
    {
        // Chuyển hướng đến trang nạp tiền trong PaymentController
        $this->redirect('payments/topup');
    }

    // API lấy thông tin user
    public function getInfo()
    {
        // Kiểm tra đăng nhập
        if (!$this->isLoggedIn()) {
            $this->json(['status' => 'error', 'message' => 'Bạn chưa đăng nhập'], 401);
            return;
        }

        $userId = $_SESSION['user_id'];

        // Lấy thông tin user
        $user = $this->userModel->getById($userId);

        // Loại bỏ thông tin nhạy cảm
        unset($user['password']);

        $this->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    // API lấy danh sách phim đã mua
    public function getPurchasedMovies()
    {
        // Kiểm tra đăng nhập
        if (!$this->isLoggedIn()) {
            $this->json(['status' => 'error', 'message' => 'Bạn chưa đăng nhập'], 401);
            return;
        }

        $userId = $_SESSION['user_id'];

        // Lấy danh sách phim đã mua
        $purchasedMovies = $this->userModel->getPurchasedMovies($userId);

        $this->json([
            'status' => 'success',
            'data' => [
                'movies' => $purchasedMovies,
                'total' => count($purchasedMovies)
            ]
        ]);
    }
}
