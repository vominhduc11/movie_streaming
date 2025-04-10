<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? APP_NAME ?></title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= PUBLIC_PATH ?>/assets/img/favicon.ico" type="image/x-icon">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= PUBLIC_PATH ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?= PUBLIC_PATH ?>/assets/css/animations.css">
    <link rel="stylesheet" href="<?= PUBLIC_PATH ?>/assets/css/movie.css">
    <link rel="stylesheet" href="<?= PUBLIC_PATH ?>/assets/css/room.css">
    <link rel="stylesheet" href="<?= PUBLIC_PATH ?>/assets/css/chat.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?= APP_URL ?>">
                <i class="fas fa-film"></i> <?= APP_NAME ?>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/movies">Phim</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= APP_URL ?>/rooms">Phòng xem phim</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/home/about">Giới thiệu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/home/contact">Liên hệ</a>
                    </li>
                </ul>

                <!-- Search Form -->
                <form class="form-inline my-2 my-lg-0 mr-3" action="<?= APP_URL ?>/home/search" method="GET">
                    <div class="input-group">
                        <input type="text" name="keyword" class="form-control" placeholder="Tìm kiếm phim...">
                        <div class="input-group-append">
                            <button class="btn btn-light" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- User Menu -->
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                                <i class="fas fa-user"></i> <?= $_SESSION['username'] ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="<?= APP_URL ?>/users/profile">
                                    <i class="fas fa-user-circle"></i> Thông tin cá nhân
                                </a>
                                <a class="dropdown-item" href="<?= APP_URL ?>/users/movies">
                                    <i class="fas fa-film"></i> Phim đã mua
                                </a>
                                <a class="dropdown-item" href="<?= APP_URL ?>/users/payments">
                                    <i class="fas fa-money-bill"></i> Lịch sử thanh toán
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?= APP_URL ?>/auth/logout">
                                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                                </a>
                            </div>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= APP_URL ?>/auth/login">
                                <i class="fas fa-sign-in-alt"></i> Đăng nhập
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= APP_URL ?>/auth/register">
                                <i class="fas fa-user-plus"></i> Đăng ký
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show flash-message" role="alert">
            <?= $_SESSION['success_message'] ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show flash-message" role="alert">
            <?= $_SESSION['error_message'] ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="main-content">