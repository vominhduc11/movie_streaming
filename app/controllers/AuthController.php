<?php
// app/controllers/AuthController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Admin;

class AuthController extends Controller
{
    private $userModel;
    private $adminModel;

    public function __construct()
    {
        $this->userModel = $this->model('User');
        $this->adminModel = $this->model('Admin');
    }

    // Đăng nhập user
    public function login()
    {
        // Nếu đã đăng nhập thì chuyển hướng đến trang chủ
        if ($this->isLoggedIn()) {
            $this->redirect('');
            return;
        }

        // Xử lý form đăng nhập
        if ($this->isPost()) {
            $email = $this->getPost('email');
            $password = $this->getPost('password');

            // Validate dữ liệu
            $errors = [];

            if (empty($email)) {
                $errors['email'] = 'Vui lòng nhập email';
            }

            if (empty($password)) {
                $errors['password'] = 'Vui lòng nhập mật khẩu';
            }

            // Nếu không có lỗi thì tiến hành đăng nhập
            if (empty($errors)) {
                $user = $this->userModel->login($email, $password);

                if ($user) {
                    // Lưu thông tin user vào session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['is_admin'] = false;

                    // Chuyển hướng đến trang chủ
                    $this->redirect('');
                    return;
                } else {
                    $errors['login'] = 'Email hoặc mật khẩu không đúng';
                }
            }

            // Nếu có lỗi thì hiển thị lại form với thông báo lỗi
            $this->view('auth/login', [
                'errors' => $errors,
                'email' => $email,
                'title' => APP_NAME . ' - Đăng nhập'
            ]);
            return;
        }

        // Hiển thị form đăng nhập
        $this->view('auth/login', [
            'title' => APP_NAME . ' - Đăng nhập'
        ]);
    }

    // Đăng nhập admin
    public function adminLogin()
    {
        // Nếu đã đăng nhập thì chuyển hướng đến trang admin
        if ($this->isAdmin()) {
            $this->redirect('admin');
            return;
        }

        // Xử lý form đăng nhập admin
        if ($this->isPost()) {
            $username = $this->getPost('username');
            $password = $this->getPost('password');

            // Validate dữ liệu
            $errors = [];

            if (empty($username)) {
                $errors['username'] = 'Vui lòng nhập tên đăng nhập';
            }

            if (empty($password)) {
                $errors['password'] = 'Vui lòng nhập mật khẩu';
            }

            // Nếu không có lỗi thì tiến hành đăng nhập
            if (empty($errors)) {
                $admin = $this->adminModel->login($username, $password);

                if ($admin) {
                    // Lưu thông tin admin vào session
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_email'] = $admin['email'];
                    $_SESSION['admin_role'] = $admin['role'];
                    $_SESSION['is_admin'] = true;

                    // Chuyển hướng đến trang admin
                    $this->redirect('admin');
                    return;
                } else {
                    $errors['login'] = 'Tên đăng nhập hoặc mật khẩu không đúng';
                }
            }

            // Nếu có lỗi thì hiển thị lại form với thông báo lỗi
            $this->view('auth/admin-login', [
                'errors' => $errors,
                'username' => $username,
                'title' => APP_NAME . ' - Đăng nhập Admin'
            ]);
            return;
        }

        // Hiển thị form đăng nhập admin
        $this->view('auth/admin-login', [
            'title' => APP_NAME . ' - Đăng nhập Admin'
        ]);
    }

    // Đăng ký
    public function register()
    {
        // Nếu đã đăng nhập thì chuyển hướng đến trang chủ
        if ($this->isLoggedIn()) {
            $this->redirect('');
            return;
        }

        // Xử lý form đăng ký
        if ($this->isPost()) {
            $username = $this->getPost('username');
            $email = $this->getPost('email');
            $password = $this->getPost('password');
            $confirmPassword = $this->getPost('confirm_password');
            $fullName = $this->getPost('full_name');

            // Validate dữ liệu
            $errors = [];

            if (empty($username)) {
                $errors['username'] = 'Vui lòng nhập tên đăng nhập';
            } elseif (strlen($username) < 3 || strlen($username) > 50) {
                $errors['username'] = 'Tên đăng nhập phải từ 3 đến 50 ký tự';
            } elseif ($this->userModel->usernameExists($username)) {
                $errors['username'] = 'Tên đăng nhập đã tồn tại';
            }

            if (empty($email)) {
                $errors['email'] = 'Vui lòng nhập email';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Email không hợp lệ';
            } elseif ($this->userModel->emailExists($email)) {
                $errors['email'] = 'Email đã tồn tại';
            }

            if (empty($password)) {
                $errors['password'] = 'Vui lòng nhập mật khẩu';
            } elseif (strlen($password) < 6) {
                $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự';
            }

            if ($password !== $confirmPassword) {
                $errors['confirm_password'] = 'Mật khẩu xác nhận không khớp';
            }

            // Nếu không có lỗi thì tiến hành đăng ký
            if (empty($errors)) {
                $userData = [
                    'username' => $username,
                    'email' => $email,
                    'password' => $password,
                    'full_name' => $fullName,
                    'balance' => 0.00,
                    'is_active' => 1
                ];

                $userId = $this->userModel->register($userData);

                if ($userId) {
                    // Đăng nhập tự động sau khi đăng ký
                    $_SESSION['user_id'] = $userId;
                    $_SESSION['username'] = $username;
                    $_SESSION['email'] = $email;
                    $_SESSION['is_admin'] = false;

                    // Chuyển hướng đến trang chủ
                    $this->redirect('');
                    return;
                } else {
                    $errors['register'] = 'Đăng ký không thành công, vui lòng thử lại';
                }
            }

            // Nếu có lỗi thì hiển thị lại form với thông báo lỗi
            $this->view('auth/register', [
                'errors' => $errors,
                'username' => $username,
                'email' => $email,
                'full_name' => $fullName,
                'title' => APP_NAME . ' - Đăng ký'
            ]);
            return;
        }

        // Hiển thị form đăng ký
        $this->view('auth/register', [
            'title' => APP_NAME . ' - Đăng ký'
        ]);
    }

    // Đăng xuất
    public function logout()
    {
        // Xóa session
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['email']);
        unset($_SESSION['is_admin']);

        // Chuyển hướng đến trang đăng nhập
        $this->redirect('auth/login');
    }

    // Đăng xuất admin
    public function adminLogout()
    {
        // Xóa session
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_username']);
        unset($_SESSION['admin_email']);
        unset($_SESSION['admin_role']);
        unset($_SESSION['is_admin']);

        // Chuyển hướng đến trang đăng nhập admin
        $this->redirect('auth/admin-login');
    }
}
