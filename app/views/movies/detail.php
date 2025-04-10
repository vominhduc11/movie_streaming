<?php require_once VIEW_PATH . '/layouts/header.php'; ?>

<div class="movie-detail">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="movie-poster fade-in-trigger">
                    <img src="<?= PUBLIC_PATH ?>/assets/uploads/thumbnails/<?= $movie['thumbnail'] ?>" alt="<?= $movie['title'] ?>" class="img-fluid">
                </div>
            </div>
            <div class="col-md-8">
                <div class="movie-info-detail">
                    <h1 class="slide-in-left"><?= $movie['title'] ?></h1>

                    <div class="movie-meta-detail">
                        <span><i class="fas fa-clock"></i> <?= $movie['duration'] ?> phút</span>
                        <span><i class="fas fa-film"></i> <?= $movie['genre'] ?></span>
                        <span><i class="fas fa-calendar"></i> <?= $movie['release_year'] ?></span>
                        <span><i class="fas fa-eye"></i> <?= $movie['views'] ?> lượt xem</span>
                    </div>

                    <div class="movie-price">
                        <i class="fas fa-tag"></i> <?= number_format($movie['price'], 0, ',', '.') ?> VND
                    </div>

                    <div class="movie-desc">
                        <h5>Nội dung phim</h5>
                        <p><?= $movie['description'] ?></p>
                    </div>

                    <div class="movie-actions">
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <a href="<?= APP_URL ?>/auth/login" class="btn btn-primary btn-lg btn-hover ripple">
                                <i class="fas fa-sign-in-alt"></i> Đăng nhập để mua phim
                            </a>
                        <?php elseif ($hasPurchased): ?>
                            <?php if ($openRoom): ?>
                                <a href="<?= APP_URL ?>/rooms/view/<?= $openRoom['id'] ?>" class="btn btn-success btn-lg btn-hover ripple">
                                    <i class="fas fa-play"></i> Xem phim ngay
                                </a>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-lg" disabled>
                                    <i class="fas fa-clock"></i> Đã mua - Chờ Admin mở phòng
                                </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="<?= APP_URL ?>/movies/purchase/<?= $movie['id'] ?>" class="btn btn-primary btn-lg btn-hover ripple">
                                <i class="fas fa-shopping-cart"></i> Mua phim
                            </a>
                        <?php endif; ?>

                        <button class="btn btn-outline-secondary btn-lg btn-hover ripple" id="shareButton">
                            <i class="fas fa-share-alt"></i> Chia sẻ
                        </button>

                        <button class="btn btn-outline-danger btn-lg btn-hover ripple" id="favoriteButton">
                            <i class="far fa-heart"></i> Yêu thích
                        </button>
                    </div>

                    <!-- Share Options (Initially Hidden) -->
                    <div class="share-options" id="shareOptions" style="display: none;">
                        <div class="share-title">Chia sẻ với bạn bè:</div>
                        <div class="share-buttons">
                            <a href="#" class="share-btn facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="share-btn twitter"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="share-btn telegram"><i class="fab fa-telegram-plane"></i></a>
                            <a href="#" class="share-btn email"><i class="fas fa-envelope"></i></a>
                            <button class="share-btn copy-link" id="copyLinkBtn" data-link="<?= APP_URL ?>/movies/detail/<?= $movie['id'] ?>">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Movie Trailer and More Info -->
        <div class="row mt-5">
            <div class="col-md-12">
                <ul class="nav nav-tabs" id="movieTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="trailer-tab" data-toggle="tab" href="#trailer" role="tab">
                            <i class="fas fa-film"></i> Trailer
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="reviews-tab" data-toggle="tab" href="#reviews" role="tab">
                            <i class="fas fa-star"></i> Đánh giá
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="related-tab" data-toggle="tab" href="#related" role="tab">
                            <i class="fas fa-th-list"></i> Phim liên quan
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="movieTabsContent">
                    <div class="tab-pane fade show active" id="trailer" role="tabpanel">
                        <div class="trailer-container">
                            <div class="embed-responsive embed-responsive-16by9">
                                <!-- Placeholder for trailer - in a real app, you would have a trailer URL in your database -->
                                <img src="<?= PUBLIC_PATH ?>/assets/uploads/thumbnails/<?= $movie['thumbnail'] ?>" alt="<?= $movie['title'] ?>" class="img-fluid">
                                <div class="trailer-play-btn">
                                    <i class="fas fa-play"></i>
                                </div>
                            </div>
                            <div class="trailer-info">
                                <h4>Trailer: <?= $movie['title'] ?></h4>
                                <p>Xem trước nội dung phim trước khi quyết định mua.</p>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="reviews" role="tabpanel">
                        <div class="reviews-container">
                            <div class="review-stats">
                                <div class="rating-average">
                                    <div class="rating-score">4.5</div>
                                    <div class="rating-stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                    <div class="rating-count">Dựa trên 28 đánh giá</div>
                                </div>

                                <div class="rating-bars">
                                    <div class="rating-bar-item">
                                        <span class="rating-label">5 sao</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" style="width: 70%"></div>
                                        </div>
                                        <span class="rating-count">20</span>
                                    </div>
                                    <div class="rating-bar-item">
                                        <span class="rating-label">4 sao</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" style="width: 20%"></div>
                                        </div>
                                        <span class="rating-count">5</span>
                                    </div>
                                    <div class="rating-bar-item">
                                        <span class="rating-label">3 sao</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-warning" style="width: 10%"></div>
                                        </div>
                                        <span class="rating-count">2</span>
                                    </div>
                                    <div class="rating-bar-item">
                                        <span class="rating-label">2 sao</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-danger" style="width: 5%"></div>
                                        </div>
                                        <span class="rating-count">1</span>
                                    </div>
                                    <div class="rating-bar-item">
                                        <span class="rating-label">1 sao</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-danger" style="width: 0%"></div>
                                        </div>
                                        <span class="rating-count">0</span>
                                    </div>
                                </div>
                            </div>

                            <div class="reviews-list">
                                <div class="review-item">
                                    <div class="review-header">
                                        <div class="reviewer-info">
                                            <img src="<?= PUBLIC_PATH ?>/assets/img/user1.jpg" alt="User Avatar" class="reviewer-avatar">
                                            <div class="reviewer-details">
                                                <div class="reviewer-name">Nguyễn Văn A</div>
                                                <div class="review-date">15/08/2023</div>
                                            </div>
                                        </div>
                                        <div class="review-rating">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                    </div>
                                    <div class="review-content">
                                        <p>Phim rất hay và hấp dẫn. Diễn xuất của các diễn viên rất tuyệt vời. Tôi đã xem phim này nhiều lần và vẫn thấy thích thú.</p>
                                    </div>
                                </div>

                                <div class="review-item">
                                    <div class="review-header">
                                        <div class="reviewer-info">
                                            <img src="<?= PUBLIC_PATH ?>/assets/img/user2.jpg" alt="User Avatar" class="reviewer-avatar">
                                            <div class="reviewer-details">
                                                <div class="reviewer-name">Trần Thị B</div>
                                                <div class="review-date">10/08/2023</div>
                                            </div>
                                        </div>
                                        <div class="review-rating">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                        </div>
                                    </div>
                                    <div class="review-content">
                                        <p>Nội dung phim khá hấp dẫn, tuy nhiên tôi nghĩ kết thúc hơi đột ngột. Nhìn chung vẫn là một bộ phim đáng xem.</p>
                                    </div>
                                </div>

                                <!-- Write Review Form (Only for users who purchased the movie) -->
                                <?php if (isset($_SESSION['user_id']) && $hasPurchased): ?>
                                    <div class="write-review">
                                        <h5>Viết đánh giá của bạn</h5>
                                        <form id="reviewForm">
                                            <div class="form-group">
                                                <label>Đánh giá:</label>
                                                <div class="rating-input">
                                                    <i class="far fa-star" data-rating="1"></i>
                                                    <i class="far fa-star" data-rating="2"></i>
                                                    <i class="far fa-star" data-rating="3"></i>
                                                    <i class="far fa-star" data-rating="4"></i>
                                                    <i class="far fa-star" data-rating="5"></i>
                                                </div>
                                                <input type="hidden" name="rating" id="rating" value="0">
                                            </div>
                                            <div class="form-group">
                                                <label for="reviewContent">Nội dung đánh giá:</label>
                                                <textarea class="form-control" id="reviewContent" name="review" rows="4" required></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="related" role="tabpanel">
                        <div class="related-movies">
                            <div class="row">
                                <!-- Giả lập phim liên quan - trong ứng dụng thực tế, bạn sẽ truy vấn CSDL để lấy phim cùng thể loại -->
                                <div class="col-md-3 col-sm-6 movie-card-container">
                                    <div class="movie-card card-hover">
                                        <a href="#">
                                            <div class="movie-card-image">
                                                <img src="<?= PUBLIC_PATH ?>/assets/img/movie1.jpg" alt="Related Movie 1">
                                                <div class="movie-card-overlay">
                                                    <span class="price">150.000 VND</span>
                                                    <span class="duration">120 phút</span>
                                                </div>
                                            </div>
                                            <div class="movie-card-content">
                                                <h3 class="movie-title">Phim liên quan 1</h3>
                                                <div class="movie-info">
                                                    <span class="genre"><?= $movie['genre'] ?></span>
                                                    <span class="year">2023</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6 movie-card-container">
                                    <div class="movie-card card-hover">
                                        <a href="#">
                                            <div class="movie-card-image">
                                                <img src="<?= PUBLIC_PATH ?>/assets/img/movie2.jpg" alt="Related Movie 2">
                                                <div class="movie-card-overlay">
                                                    <span class="price">180.000 VND</span>
                                                    <span class="duration">135 phút</span>
                                                </div>
                                            </div>
                                            <div class="movie-card-content">
                                                <h3 class="movie-title">Phim liên quan 2</h3>
                                                <div class="movie-info">
                                                    <span class="genre"><?= $movie['genre'] ?></span>
                                                    <span class="year">2022</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6 movie-card-container">
                                    <div class="movie-card card-hover">
                                        <a href="#">
                                            <div class="movie-card-image">
                                                <img src="<?= PUBLIC_PATH ?>/assets/img/movie3.jpg" alt="Related Movie 3">
                                                <div class="movie-card-overlay">
                                                    <span class="price">120.000 VND</span>
                                                    <span class="duration">110 phút</span>
                                                </div>
                                            </div>
                                            <div class="movie-card-content">
                                                <h3 class="movie-title">Phim liên quan 3</h3>
                                                <div class="movie-info">
                                                    <span class="genre"><?= $movie['genre'] ?></span>
                                                    <span class="year">2023</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6 movie-card-container">
                                    <div class="movie-card card-hover">
                                        <a href="#">
                                            <div class="movie-card-image">
                                                <img src="<?= PUBLIC_PATH ?>/assets/img/movie4.jpg" alt="Related Movie 4">
                                                <div class="movie-card-overlay">
                                                    <span class="price">160.000 VND</span>
                                                    <span class="duration">125 phút</span>
                                                </div>
                                            </div>
                                            <div class="movie-card-content">
                                                <h3 class="movie-title">Phim liên quan 4</h3>
                                                <div class="movie-info">
                                                    <span class="genre"><?= $movie['genre'] ?></span>
                                                    <span class="year">2021</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<style>
    /* Movie Detail Page Custom Styles */
    .movie-detail {
        padding: 50px 0;
    }

    .movie-poster {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .movie-info-detail h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .movie-meta-detail {
        margin-bottom: 20px;
    }

    .movie-meta-detail span {
        display: inline-block;
        margin-right: 20px;
        margin-bottom: 10px;
        color: #6c757d;
    }

    .movie-meta-detail i {
        color: var(--primary-color);
        margin-right: 5px;
    }

    .movie-price {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 20px;
    }

    .movie-desc {
        margin-bottom: 30px;
    }

    .movie-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 30px;
    }

    .share-options {
        margin-top: 20px;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 8px;
        animation: fadeIn 0.3s ease-in-out;
    }

    .share-title {
        margin-bottom: 10px;
        font-weight: 600;
    }

    .share-buttons {
        display: flex;
        gap: 10px;
    }

    .share-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .share-btn:hover {
        transform: translateY(-3px);
    }

    .share-btn.facebook {
        background-color: #3b5998;
    }

    .share-btn.twitter {
        background-color: #1da1f2;
    }

    .share-btn.telegram {
        background-color: #0088cc;
    }

    .share-btn.email {
        background-color: #ea4335;
    }

    .share-btn.copy-link {
        background-color: #6c757d;
        border: none;
        cursor: pointer;
    }

    /* Tabs Styles */
    #movieTabs {
        margin-bottom: 20px;
    }

    #movieTabs .nav-link {
        font-weight: 600;
        color: #495057;
    }

    #movieTabs .nav-link.active {
        color: var(--primary-color);
        border-color: var(--primary-color);
    }

    #movieTabs .nav-link i {
        margin-right: 5px;
    }

    .tab-content {
        padding: 20px;
        background-color: white;
        border: 1px solid #dee2e6;
        border-top: none;
        border-radius: 0 0 8px 8px;
    }

    /* Trailer Styles */
    .trailer-container {
        position: relative;
    }

    .trailer-play-btn {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 80px;
        height: 80px;
        background-color: rgba(0, 0, 0, 0.7);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .trailer-play-btn:hover {
        background-color: var(--primary-color);
        transform: translate(-50%, -50%) scale(1.1);
    }

    .trailer-info {
        margin-top: 20px;
    }

    /* Reviews Styles */
    .reviews-container {
        padding: 20px 0;
    }

    .review-stats {
        display: flex;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }

    .rating-average {
        text-align: center;
        padding-right: 30px;
        border-right: 1px solid #eee;
        margin-right: 30px;
    }

    .rating-score {
        font-size: 3rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .rating-stars {
        color: #ffc107;
        font-size: 1.25rem;
        margin-bottom: 5px;
    }

    .rating-count {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .rating-bars {
        flex: 1;
    }

    .rating-bar-item {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
    }

    .rating-label {
        width: 50px;
    }

    .progress {
        flex: 1;
        height: 10px;
        margin: 0 10px;
    }

    .review-item {
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .reviewer-info {
        display: flex;
        align-items: center;
    }

    .reviewer-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 15px;
        object-fit: cover;
    }

    .reviewer-name {
        font-weight: 600;
    }

    .review-date {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .review-rating {
        color: #ffc107;
    }

    .write-review {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    .rating-input {
        font-size: 1.5rem;
        color: #ccc;
        margin-bottom: 15px;
    }

    .rating-input i {
        cursor: pointer;
        margin-right: 5px;
        transition: all 0.2s ease;
    }

    .rating-input i:hover,
    .rating-input i.selected {
        color: #ffc107;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Share Button Toggle
        const shareButton = document.getElementById('shareButton');
        const shareOptions = document.getElementById('shareOptions');

        if (shareButton && shareOptions) {
            shareButton.addEventListener('click', function() {
                shareOptions.style.display = shareOptions.style.display === 'none' ? 'block' : 'none';
            });
        }

        // Copy Link Button
        const copyLinkBtn = document.getElementById('copyLinkBtn');

        if (copyLinkBtn) {
            copyLinkBtn.addEventListener('click', function() {
                const link = this.getAttribute('data-link');
                navigator.clipboard.writeText(link).then(function() {
                    // Show success message
                    alert('Đã sao chép đường dẫn!');
                }, function() {
                    // Fallback
                    console.error('Không thể sao chép đường dẫn');
                });
            });
        }

        // Favorite Button
        const favoriteButton = document.getElementById('favoriteButton');

        if (favoriteButton) {
            favoriteButton.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (icon.classList.contains('far')) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    this.classList.add('active');

                    // Show message
                    alert('Đã thêm vào danh sách yêu thích!');
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    this.classList.remove('active');

                    // Show message
                    alert('Đã xóa khỏi danh sách yêu thích!');
                }
            });
        }

        // Trailer Play Button
        const trailerPlayBtn = document.querySelector('.trailer-play-btn');

        if (trailerPlayBtn) {
            trailerPlayBtn.addEventListener('click', function() {
                alert('Chức năng xem trailer sẽ được cập nhật sau!');
            });
        }

        // Rating Input
        const ratingStars = document.querySelectorAll('.rating-input i');
        const ratingInput = document.getElementById('rating');

        if (ratingStars.length && ratingInput) {
            ratingStars.forEach(function(star) {
                star.addEventListener('mouseover', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    updateStars(rating);
                });

                star.addEventListener('mouseout', function() {
                    const currentRating = parseInt(ratingInput.value) || 0;
                    updateStars(currentRating);
                });

                star.addEventListener('click', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    ratingInput.value = rating;
                    updateStars(rating);
                });
            });

            function updateStars(rating) {
                ratingStars.forEach(function(star, index) {
                    if (index < rating) {
                        star.classList.remove('far');
                        star.classList.add('fas');
                        star.classList.add('selected');
                    } else {
                        star.classList.remove('fas');
                        star.classList.add('far');
                        star.classList.remove('selected');
                    }
                });
            }
        }

        // Review Form Submission
        const reviewForm = document.getElementById('reviewForm');

        if (reviewForm) {
            reviewForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const rating = document.getElementById('rating').value;
                const review = document.getElementById('reviewContent').value;

                if (rating === '0') {
                    alert('Vui lòng chọn số sao đánh giá!');
                    return;
                }

                if (!review.trim()) {
                    alert('Vui lòng nhập nội dung đánh giá!');
                    return;
                }

                // Display success message
                alert('Cảm ơn bạn đã đánh giá! Đánh giá của bạn sẽ được hiển thị sau khi được kiểm duyệt.');

                // Reset form
                this.reset();
                updateStars(0);
            });
        }
    });
</script>
<?php
$scripts = ob_get_clean();
require_once VIEW_PATH . '/layouts/footer.php';
?>