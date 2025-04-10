</main>

<!-- Footer -->
<footer class="footer bg-dark text-white mt-5">
    <div class="container py-5">
        <div class="row">
            <div class="col-md-4">
                <h5 class="mb-4"><i class="fas fa-film"></i> <?= APP_NAME ?></h5>
                <p>Ứng dụng xem phim trực tuyến chất lượng cao với nhiều tính năng hấp dẫn. Xem phim cùng bạn bè, trò chuyện và chia sẻ những khoảnh khắc thú vị.</p>
            </div>
            <div class="col-md-2">
                <h5 class="mb-4">Liên kết</h5>
                <ul class="list-unstyled">
                    <li><a href="<?= APP_URL ?>">Trang chủ</a></li>
                    <li><a href="<?= APP_URL ?>/movies">Phim</a></li>
                    <li><a href="<?= APP_URL ?>/rooms">Phòng xem phim</a></li>
                    <li><a href="<?= APP_URL ?>/home/about">Giới thiệu</a></li>
                    <li><a href="<?= APP_URL ?>/home/contact">Liên hệ</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h5 class="mb-4">Thể loại phim</h5>
                <ul class="list-unstyled">
                    <li><a href="<?= APP_URL ?>/movies/genre/Hành Động">Hành Động</a></li>
                    <li><a href="<?= APP_URL ?>/movies/genre/Kinh Dị">Kinh Dị</a></li>
                    <li><a href="<?= APP_URL ?>/movies/genre/Tình Cảm">Tình Cảm</a></li>
                    <li><a href="<?= APP_URL ?>/movies/genre/Hài Hước">Hài Hước</a></li>
                    <li><a href="<?= APP_URL ?>/movies/genre/Khoa Học Viễn Tưởng">Khoa Học Viễn Tưởng</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h5 class="mb-4">Liên hệ với chúng tôi</h5>
                <ul class="list-unstyled">
                    <li><i class="fas fa-map-marker-alt mr-2"></i> 123 Đường Phim, Quận Giải Trí, TP. Hồ Chí Minh</li>
                    <li><i class="fas fa-phone mr-2"></i> (028) 1234 5678</li>
                    <li><i class="fas fa-envelope mr-2"></i> info@moviestreaming.com</li>
                </ul>
                <div class="social-links mt-3">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom py-3">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?= date('Y') ?> <?= APP_NAME ?>. Tất cả các quyền được bảo lưu.</p>
                </div>
                <div class="col-md-6 text-md-right">
                    <p class="mb-0">Điều khoản sử dụng | Chính sách bảo mật | Trợ giúp</p>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- jQuery and Bootstrap Bundle (includes Popper) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="<?= PUBLIC_PATH ?>/assets/js/main.js"></script>
<script src="<?= PUBLIC_PATH ?>/assets/js/animations.js"></script>

<!-- Additional scripts from views -->
<?= $scripts ?? '' ?>

<script>
    // Tự động ẩn flash messages
    $(document).ready(function() {
        setTimeout(function() {
            $('.flash-message').fadeOut('slow');
        }, 5000); // 5 seconds
    });
</script>
</body>

</html>