<?php require_once VIEW_PATH . '/layouts/header.php'; ?>

<div class="user-movies-page">
    <div class="container">
        <div class="page-header">
            <h1 class="page-title fade-in-trigger">Phim đã mua</h1>
            <p class="lead">Danh sách phim bạn đã mua và có thể xem khi có phòng mở</p>
        </div>

        <div class="row">
            <div class="col-md-3">
                <!-- Sidebar -->
                <div class="user-sidebar slide-in-left">
                    <div class="user-info">
                        <div class="user-avatar">
                            <img src="<?= PUBLIC_PATH ?>/assets/uploads/<?= $_SESSION['avatar'] ?? 'default-avatar.jpg' ?>" alt="<?= $_SESSION['username'] ?>">
                        </div>
                        <h3 class="user-name"><?= $_SESSION['username'] ?></h3>
                        <p class="user-email"><?= $_SESSION['email'] ?></p>
                    </div>

                    <ul class="user-menu">
                        <li>
                            <a href="<?= APP_URL ?>/users/profile">
                                <i class="fas fa-user-circle"></i> Thông tin cá nhân
                            </a>
                        </li>
                        <li class="active">
                            <a href="<?= APP_URL ?>/users/movies">
                                <i class="fas fa-film"></i> Phim đã mua
                            </a>
                        </li>
                        <li>
                            <a href="<?= APP_URL ?>/users/payments">
                                <i class="fas fa-history"></i> Lịch sử thanh toán
                            </a>
                        </li>
                        <li>
                            <a href="<?= APP_URL ?>/users/topup">
                                <i class="fas fa-wallet"></i> Nạp tiền
                            </a>
                        </li>
                        <li>
                            <a href="<?= APP_URL ?>/users/change-password">
                                <i class="fas fa-lock"></i> Đổi mật khẩu
                            </a>
                        </li>
                        <li>
                            <a href="<?= APP_URL ?>/auth/logout">
                                <i class="fas fa-sign-out-alt"></i> Đăng xuất
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-md-9">
                <!-- Filter and Sort -->
                <div class="filter-bar fade-in-trigger">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="input-group">
                                <input type="text" id="searchBox" class="form-control" placeholder="Tìm kiếm phim...">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select id="sortOptions" class="form-control">
                                <option value="newest">Mới nhất</option>
                                <option value="oldest">Cũ nhất</option>
                                <option value="title_asc">Tên A-Z</option>
                                <option value="title_desc">Tên Z-A</option>
                            </select>
                        </div>
                    </div>
                </div>

                <?php if (empty($movies)): ?>
                    <div class="empty-state fade-in-trigger">
                        <div class="empty-state-icon">
                            <i class="fas fa-film"></i>
                        </div>
                        <h2>Bạn chưa mua phim nào</h2>
                        <p>Hãy mua phim để có thể xem khi có phòng mở.</p>
                        <a href="<?= APP_URL ?>/movies" class="btn btn-primary btn-hover ripple">
                            <i class="fas fa-shopping-cart"></i> Mua phim ngay
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Movie List Grid -->
                    <div class="row movie-grid">
                        <?php foreach ($movies as $movie): ?>
                            <div class="col-md-4 col-sm-6 movie-item" data-title="<?= strtolower($movie['title']) ?>" data-purchased="<?= date('Y-m-d', strtotime($movie['purchased_at'])) ?>">
                                <div class="movie-card card-hover zoom-in">
                                    <div class="movie-card-image">
                                        <img src="<?= PUBLIC_PATH ?>/assets/uploads/thumbnails/<?= $movie['thumbnail'] ?>" alt="<?= $movie['title'] ?>">
                                        <div class="movie-card-overlay">
                                            <div class="overlay-buttons">
                                                <a href="<?= APP_URL ?>/movies/detail/<?= $movie['id'] ?>" class="btn btn-light btn-sm">
                                                    <i class="fas fa-info-circle"></i> Chi tiết
                                                </a>
                                                <?php
                                                // Check if there's an open room for this movie
                                                $openRoom = null;
                                                foreach ($openRooms ?? [] as $room) {
                                                    if ($room['movie_id'] == $movie['id']) {
                                                        $openRoom = $room;
                                                        break;
                                                    }
                                                }
                                                ?>

                                                <?php if ($openRoom): ?>
                                                    <a href="<?= APP_URL ?>/rooms/view/<?= $openRoom['id'] ?>" class="btn btn-success btn-sm">
                                                        <i class="fas fa-play"></i> Xem ngay
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-secondary btn-sm" disabled>
                                                        <i class="fas fa-clock"></i> Chờ phòng mở
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="movie-card-content">
                                        <h3 class="movie-title"><?= $movie['title'] ?></h3>
                                        <div class="movie-info">
                                            <span class="movie-genre"><?= $movie['genre'] ?></span>
                                            <span class="movie-year"><?= $movie['release_year'] ?></span>
                                        </div>
                                        <div class="movie-purchase-date">
                                            <i class="fas fa-calendar-check"></i> Mua: <?= date('d/m/Y', strtotime($movie['purchased_at'])) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<style>
    .user-movies-page {
        padding: 50px 0;
        background-color: #f8f9fa;
    }

    .page-header {
        margin-bottom: 30px;
    }

    .page-title {
        font-weight: 700;
        color: var(--primary-color);
    }

    /* User Sidebar */
    .user-sidebar {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .user-info {
        text-align: center;
        padding-bottom: 20px;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
    }

    .user-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto 15px;
        border: 3px solid var(--primary-color);
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .user-name {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .user-email {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .user-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .user-menu li {
        margin-bottom: 8px;
    }

    .user-menu li a {
        display: block;
        padding: 10px 15px;
        border-radius: 5px;
        color: #495057;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .user-menu li a:hover {
        background-color: #f8f9fa;
        color: var(--primary-color);
    }

    .user-menu li.active a {
        background-color: var(--primary-color);
        color: white;
    }

    .user-menu li a i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }

    /* Filter Bar */
    .filter-bar {
        background-color: white;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    /* Movie Grid */
    .movie-grid {
        margin-bottom: 30px;
    }

    .movie-item {
        margin-bottom: 30px;
    }

    .movie-card {
        background-color: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        height: 100%;
    }

    .movie-card-image {
        position: relative;
        height: 200px;
        overflow: hidden;
    }

    .movie-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .movie-card:hover .movie-card-image img {
        transform: scale(1.05);
    }

    .movie-card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .movie-card:hover .movie-card-overlay {
        opacity: 1;
    }

    .overlay-buttons {
        text-align: center;
    }

    .overlay-buttons .btn {
        margin: 5px;
    }

    .movie-card-content {
        padding: 15px;
    }

    .movie-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        height: 50px;
    }

    .movie-info {
        display: flex;
        justify-content: space-between;
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 10px;
    }

    .movie-purchase-date {
        font-size: 0.85rem;
        color: #6c757d;
    }

    /* Empty State */
    .empty-state {
        background-color: white;
        border-radius: 10px;
        padding: 50px 20px;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .empty-state-icon {
        font-size: 4rem;
        color: #ddd;
        margin-bottom: 20px;
    }

    .empty-state h2 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: #6c757d;
        margin-bottom: 20px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchBox = document.getElementById('searchBox');
        const movieItems = document.querySelectorAll('.movie-item');

        if (searchBox) {
            searchBox.addEventListener('keyup', function() {
                const searchValue = this.value.toLowerCase().trim();

                movieItems.forEach(item => {
                    const title = item.getAttribute('data-title');

                    if (title.includes(searchValue)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }

        // Sort functionality
        const sortOptions = document.getElementById('sortOptions');

        if (sortOptions) {
            sortOptions.addEventListener('change', function() {
                const sortValue = this.value;
                const movieGrid = document.querySelector('.movie-grid');
                const movieItemsArray = Array.from(movieItems);

                // Sort items
                movieItemsArray.sort((a, b) => {
                    switch (sortValue) {
                        case 'newest':
                            return new Date(b.getAttribute('data-purchased')) - new Date(a.getAttribute('data-purchased'));
                        case 'oldest':
                            return new Date(a.getAttribute('data-purchased')) - new Date(b.getAttribute('data-purchased'));
                        case 'title_asc':
                            return a.getAttribute('data-title').localeCompare(b.getAttribute('data-title'));
                        case 'title_desc':
                            return b.getAttribute('data-title').localeCompare(a.getAttribute('data-title'));
                        default:
                            return 0;
                    }
                });

                // Reorder DOM
                movieItemsArray.forEach(item => {
                    movieGrid.appendChild(item);
                });
            });
        }
    });
</script>
<?php
$scripts = ob_get_clean();
require_once VIEW_PATH . '/layouts/footer.php';
?>