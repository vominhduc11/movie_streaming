<?php require_once VIEW_PATH . '/layouts/header.php'; ?>

<div class="payment-success-page">
    <div class="container">
        <div class="payment-success-container fade-in-trigger">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>

            <h1>Thanh toán thành công!</h1>
            <p class="success-message">Cảm ơn bạn đã mua phim này. Bạn có thể xem phim ngay khi phòng được mở.</p>

            <div class="payment-details">
                <div class="row">
                    <div class="col-md-6">
                        <div class="payment-info-card">
                            <h3>Thông tin thanh toán</h3>
                            <ul class="payment-info-list">
                                <li>
                                    <span class="info-label">Mã giao dịch:</span>
                                    <span class="info-value">#<?= $payment['id'] ?></span>
                                </li>
                                <li>
                                    <span class="info-label">Ngày thanh toán:</span>
                                    <span class="info-value"><?= date('d/m/Y H:i', strtotime($payment['created_at'])) ?></span>
                                </li>
                                <li>
                                    <span class="info-label">Phương thức:</span>
                                    <span class="info-value"><?= ucfirst($payment['payment_method']) ?></span>
                                </li>
                                <li>
                                    <span class="info-label">Số tiền:</span>
                                    <span class="info-value"><?= number_format($payment['amount'], 0, ',', '.') ?> VND</span>
                                </li>
                                <li>
                                    <span class="info-label">Trạng thái:</span>
                                    <span class="info-value status-completed">Đã thanh toán</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="movie-info-card">
                            <h3>Thông tin phim</h3>
                            <div class="movie-info-content">
                                <div class="movie-thumbnail">
                                    <img src="<?= PUBLIC_PATH ?>/assets/uploads/thumbnails/<?= $movie['thumbnail'] ?>" alt="<?= $movie['title'] ?>">
                                </div>
                                <div class="movie-details">
                                    <h4><?= $movie['title'] ?></h4>
                                    <p class="movie-meta">
                                        <span><i class="fas fa-clock"></i> <?= $movie['duration'] ?> phút</span>
                                        <span><i class="fas fa-film"></i> <?= $movie['genre'] ?></span>
                                    </p>
                                    <p class="movie-price">
                                        <i class="fas fa-tag"></i> <?= number_format($movie['price'], 0, ',', '.') ?> VND
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="action-buttons">
                <a href="<?= APP_URL ?>/movies/detail/<?= $movie['id'] ?>" class="btn btn-primary btn-lg btn-hover ripple">
                    <i class="fas fa-play-circle"></i> Xem phim
                </a>
                <a href="<?= APP_URL ?>/users/movies" class="btn btn-outline-primary btn-lg btn-hover ripple">
                    <i class="fas fa-film"></i> Phim đã mua
                </a>
                <a href="<?= APP_URL ?>/payments" class="btn btn-outline-secondary btn-lg btn-hover ripple">
                    <i class="fas fa-history"></i> Lịch sử thanh toán
                </a>
            </div>

            <div class="more-movies">
                <h3>Có thể bạn cũng thích</h3>
                <div class="row">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div class="col-md-3 col-sm-6 movie-card-container">
                            <div class="movie-card card-hover">
                                <a href="#">
                                    <div class="movie-card-image">
                                        <img src="<?= PUBLIC_PATH ?>/assets/img/movie<?= $i ?>.jpg" alt="Recommended Movie">
                                        <div class="movie-card-overlay">
                                            <span class="price"><?= number_format(rand(50, 200) * 1000, 0, ',', '.') ?> VND</span>
                                            <span class="duration"><?= rand(90, 180) ?> phút</span>
                                        </div>
                                    </div>
                                    <div class="movie-card-content">
                                        <h3 class="movie-title">Phim đề xuất <?= $i ?></h3>
                                        <div class="movie-info">
                                            <span class="genre"><?= $movie['genre'] ?></span>
                                            <span class="year">2023</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<style>
    .payment-success-page {
        padding: 50px 0;
        background-color: #f8f9fa;
    }

    .payment-success-container {
        max-width: 900px;
        margin: 0 auto;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        padding: 40px;
        text-align: center;
    }

    .success-icon {
        font-size: 5rem;
        color: #4caf50;
        margin-bottom: 20px;
        animation: bounceIn 1s;
    }

    .success-message {
        font-size: 1.2rem;
        color: #6c757d;
        margin-bottom: 30px;
    }

    .payment-details {
        margin: 30px 0;
        text-align: left;
    }

    .payment-info-card,
    .movie-info-card {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        height: 100%;
    }

    .payment-info-card h3,
    .movie-info-card h3 {
        font-size: 1.25rem;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #dee2e6;
    }

    .payment-info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .payment-info-list li {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px dashed #dee2e6;
    }

    .payment-info-list li:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .info-label {
        font-weight: 500;
        color: #6c757d;
    }

    .status-completed {
        color: #4caf50;
        font-weight: 600;
    }

    .movie-info-content {
        display: flex;
    }

    .movie-thumbnail {
        width: 100px;
        height: 150px;
        border-radius: 5px;
        overflow: hidden;
        margin-right: 15px;
    }

    .movie-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .movie-details h4 {
        font-size: 1.2rem;
        margin-bottom: 10px;
    }

    .movie-meta {
        margin-bottom: 5px;
        color: #6c757d;
    }

    .movie-meta span {
        margin-right: 10px;
    }

    .movie-price {
        font-weight: 600;
        color: var(--primary-color);
        font-size: 1.1rem;
    }

    .action-buttons {
        margin: 30px 0;
    }

    .action-buttons .btn {
        margin: 0 10px 10px 0;
    }

    .more-movies {
        margin-top: 40px;
        text-align: left;
    }

    .more-movies h3 {
        font-size: 1.5rem;
        margin-bottom: 20px;
    }

    @keyframes bounceIn {

        0%,
        20%,
        40%,
        60%,
        80%,
        100% {
            transition-timing-function: cubic-bezier(0.215, 0.610, 0.355, 1.000);
        }

        0% {
            opacity: 0;
            transform: scale3d(.3, .3, .3);
        }

        20% {
            transform: scale3d(1.1, 1.1, 1.1);
        }

        40% {
            transform: scale3d(.9, .9, .9);
        }

        60% {
            opacity: 1;
            transform: scale3d(1.03, 1.03, 1.03);
        }

        80% {
            transform: scale3d(.97, .97, .97);
        }

        100% {
            opacity: 1;
            transform: scale3d(1, 1, 1);
        }
    }
</style>
<?php
$scripts = ob_get_clean();
require_once VIEW_PATH . '/layouts/footer.php';
?>