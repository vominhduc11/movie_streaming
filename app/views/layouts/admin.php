<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? APP_NAME . ' - Quản trị' ?></title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= PUBLIC_PATH ?>/assets/img/favicon.ico" type="image/x-icon">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= PUBLIC_PATH ?>/assets/css/admin.css">
    <link rel="stylesheet" href="<?= PUBLIC_PATH ?>/assets/css/animations.css">
</head>

<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="bg-dark">
            <div class="sidebar-header">
                <h3><i class="fas fa-film"></i> <?= APP_NAME ?></h3>
                <p>Quản trị viên</p>
            </div>

            <ul class="list-unstyled components">
                <li>
                    <a href="<?= APP_URL ?>/admin">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="#movieSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fas fa-film"></i> Quản lý phim
                    </a>
                    <ul class="collapse list-unstyled" id="movieSubmenu">
                        <li>
                            <a href="<?= APP_URL ?>/admin/movies">Danh sách phim</a>
                        </li>
                        <li>
                            <a href="<?= APP_URL ?>/admin/movies/add">Thêm phim mới</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#roomSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fas fa-video"></i> Quản lý phòng
                    </a>
                    <ul class="collapse list-unstyled" id="roomSubmenu">
                        <li>
                            <a href="<?= APP_URL ?>/admin/rooms">Danh sách phòng</a>
                        </li>
                        <li>
                            <a href="<?= APP_URL ?>/admin/rooms/add">Tạo phòng mới</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="<?= APP_URL ?>/admin/users">
                        <i class="fas fa-users"></i> Quản lý người dùng
                    </a>
                </li>
                <li>
                    <a href="<?= APP_URL ?>/admin/payments">
                        <i class="fas fa-money-bill-wave"></i> Quản lý thanh toán
                    </a>
                </li>
                <li>
                    <a href="<?= APP_URL ?>/admin/settings">
                        <i class="fas fa-cog"></i> Cài đặt
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <a href="<?= APP_URL ?>" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Xem trang chủ
                </a>
                <a href="<?= APP_URL ?>/auth/admin-logout">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
            </div>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-info">
                        <i class="fas fa-bars"></i>
                    </button>

                    <div class="ml-auto d-flex align-items-center">
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                                <i class="fas fa-bell"></i>
                                <span class="badge badge-danger">3</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="#">
                                    <div class="notification-item">
                                        <div class="notification-title">Phòng mới được tạo</div>
                                        <div class="notification-desc">Phòng "Xem phim cuối tuần" đã được tạo.</div>
                                        <div class="notification-time">5 phút trước</div>
                                    </div>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">
                                    <div class="notification-item">
                                        <div class="notification-title">Thanh toán mới</div>
                                        <div class="notification-desc">Người dùng user123 đã mua phim.</div>
                                        <div class="notification-time">30 phút trước</div>
                                    </div>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">
                                    <div class="notification-item">
                                        <div class="notification-title">Người dùng mới</div>
                                        <div class="notification-desc">Có 5 người dùng mới đăng ký hôm nay.</div>
                                        <div class="notification-time">1 giờ trước</div>
                                    </div>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-center" href="#">Xem tất cả thông báo</a>
                            </div>
                        </div>

                        <div class="dropdown ml-3">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                                <img src="<?= PUBLIC_PATH ?>/assets/img/admin-avatar.jpg" class="avatar" alt="Admin Avatar">
                                <span class="ml-2"><?= $_SESSION['admin_username'] ?? 'Admin' ?></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="<?= APP_URL ?>/admin/profile">
                                    <i class="fas fa-user-circle"></i> Thông tin cá nhân
                                </a>
                                <a class="dropdown-item" href="<?= APP_URL ?>/admin/settings">
                                    <i class="fas fa-cog"></i> Cài đặt
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?= APP_URL ?>/auth/admin-logout">
                                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Flash Messages -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success_message'] ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error_message'] ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <!-- Main Content -->
            <div class="content-wrapper">
            </div>

            <!-- Footer -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. All Rights Reserved.</p>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <p>Version 1.0</p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- jQuery and Bootstrap Bundle (includes Popper) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/js/bootstrap.bundle.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>

    <!-- Custom JS -->
    <script src="<?= PUBLIC_PATH ?>/assets/js/admin.js"></script>

    <!-- Additional scripts from views -->
    <?= $scripts ?? '' ?>

    <script>
        // Toggle sidebar
        $(document).ready(function() {
            $('#sidebarCollapse').on('click', function() {
                $('#sidebar').toggleClass('active');
            });

            // Auto hide alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
    </script>
</body>

</html>