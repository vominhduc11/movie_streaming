<?php require_once VIEW_PATH . '/layouts/header.php'; ?>

<div class="hero-section">
    <div class="overlay"></div>
    <div class="container">
        <div class="hero-content fade-in-trigger">
            <h1 class="typing">Chào mừng đến với <?= APP_NAME ?></h1>
            <p class="lead">Xem phim chất lượng cao và trải nghiệm với bạn bè</p>
            <div class="hero-buttons">
                <a href="<?= APP_URL ?>/movies" class="btn btn-primary btn-lg btn-hover ripple">Xem phim ngay</a>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="<?= APP_URL ?>/auth/register" class="btn btn-outline-light btn-lg btn-hover ripple ml-3">Đăng ký</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<section class="latest-movies">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title scroll-reveal" data-animation="slide-in-right">Phim mới nhất</h2>
            <a href="<?= APP_URL ?>/movies" class="view-all">Xem tất cả</a>
        </div>

        <div class="row">
            <?php foreach ($latestMovies as $movie): ?>
                <div class="col-md-3 col-sm-6 movie-card-container scroll-reveal" data-animation="fade-in">
                    <div class="movie-card card-hover">
                        <a href="<?= APP_URL ?>/movies/detail/<?= $movie['id'] ?>">
                            <div class="movie-card-image">
                                <img src="<?= PUBLIC_PATH ?>/assets/uploads/thumbnails/<?= $movie['thumbnail'] ?>" alt="<?= $movie['title'] ?>">
                                <div class="movie-card-overlay">
                                    <span class="price"><?= number_format($movie['price'], 0, ',', '.') ?> VND</span>
                                    <span class="duration"><?= $movie['duration'] ?> phút</span>
                                </div>
                            </div>
                            <div class="movie-card-content">
                                <h3 class="movie-title"><?= $movie['title'] ?></h3>
                                <div class="movie-info">
                                    <span class="genre"><?= $movie['genre'] ?></span>
                                    <span class="year"><?= $movie['release_year'] ?></span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="popular-movies">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title scroll-reveal" data-animation="slide-in-left">Phim phổ biến</h2>
            <a href="<?= APP_URL ?>/movies" class="view-all">Xem tất cả</a>
        </div>

        <div class="row">
            <?php foreach ($popularMovies as $movie): ?>
                <div class="col-md-3 col-sm-6 movie-card-container scroll-reveal" data-animation="fade-in">
                    <div class="movie-card card-hover">
                        <a href="<?= APP_URL ?>/movies/detail/<?= $movie['id'] ?>">
                            <div class="movie-card-image">
                                <img src="<?= PUBLIC_PATH ?>/assets/uploads/thumbnails/<?= $movie['thumbnail'] ?>" alt="<?= $movie['title'] ?>">
                                <div class="movie-card-overlay">
                                    <span class="price"><?= number_format($movie['price'], 0, ',', '.') ?> VND</span>
                                    <span class="duration"><?= $movie['duration'] ?> phút</span>
                                    <span class="views"><i class="fas fa-eye"></i> <?= $movie['views'] ?></span>
                                </div>
                            </div>
                            <div class="movie-card-content">
                                <h3 class="movie-title"><?= $movie['title'] ?></h3>
                                <div class="movie-info">
                                    <span class="genre"><?= $movie['genre'] ?></span>
                                    <span class="year"><?= $movie['release_year'] ?></span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if (count($openRooms) > 0): ?>
    <section class="open-rooms">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title scroll-reveal" data-animation="slide-in-right">Phòng xem phim đang mở</h2>
                <a href="<?= APP_URL ?>/rooms" class="view-all">Xem tất cả</a>
            </div>

            <div class="row">
                <?php foreach ($openRooms as $room): ?>
                    <div class="col-md-6 room-card-container scroll-reveal" data-animation="fade-in">
                        <div class="room-card card-hover">
                            <div class="room-info">
                                <h3 class="room-name"><?= $room['name'] ?></h3>
                                <p class="movie-title">Phim: <?= $room['movie_title'] ?></p>
                                <p class="admin-name">Admin: <?= $room['admin_username'] ?></p>
                            </div>
                            <div class="room-action">
                                <a href="<?= APP_URL ?>/rooms/view/<?= $room['id'] ?>" class="btn btn-primary btn-hover ripple">Vào xem</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<section class="features">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title scroll-reveal" data-animation="slide-in-up">Tính năng nổi bật</h2>
        </div>

        <div class="row">
            <div class="col-md-4 feature-item scroll-reveal" data-animation="fade-in">
                <div class="feature-icon">
                    <i class="fas fa-film"></i>
                </div>
                <h3>Phim chất lượng cao</h3>
                <p>Trải nghiệm xem phim với chất lượng video tốt nhất, âm thanh sống động.</p>
            </div>

            <div class="col-md-4 feature-item scroll-reveal" data-animation="fade-in">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Xem phim cùng bạn bè</h3>
                <p>Trò chuyện và thảo luận với bạn bè trong khi xem phim.</p>
            </div>

            <div class="col-md-4 feature-item scroll-reveal" data-animation="fade-in">
                <div class="feature-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <h3>Bảo mật an toàn</h3>
                <p>Thanh toán an toàn và bảo mật thông tin cá nhân của người dùng.</p>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <div class="cta-content text-center fade-in-trigger">
            <h2>Bắt đầu trải nghiệm ngay hôm nay</h2>
            <p>Đăng ký tài khoản và khám phá thế giới phim ảnh tuyệt vời.</p>
            <a href="<?= APP_URL ?>/auth/register" class="btn btn-primary btn-lg btn-hover ripple">Đăng ký ngay</a>
        </div>
    </div>
</section>

<?php require_once VIEW_PATH . '/layouts/footer.php'; ?>