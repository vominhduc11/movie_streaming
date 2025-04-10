<?php require_once VIEW_PATH . '/layouts/header.php'; ?>

<div class="payment-page">
    <div class="container">
        <div class="payment-container fade-in-trigger">
            <div class="payment-header">
                <h2><i class="fas fa-shopping-cart"></i> Thanh toán</h2>
                <p>Vui lòng hoàn tất thanh toán để có thể xem phim</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <div class="payment-movie">
                <div class="payment-movie-image">
                    <img src="<?= PUBLIC_PATH ?>/assets/uploads/thumbnails/<?= $movie['thumbnail'] ?>" alt="<?= $movie['title'] ?>">
                </div>
                <div class="payment-movie-info">
                    <h3><?= $movie['title'] ?></h3>
                    <div class="movie-meta">
                        <span><i class="fas fa-clock"></i> <?= $movie['duration'] ?> phút</span>
                        <span><i class="fas fa-film"></i> <?= $movie['genre'] ?></span>
                        <span><i class="fas fa-calendar"></i> <?= $movie['release_year'] ?></span>
                    </div>
                    <div class="movie-price">
                        <i class="fas fa-tag"></i> <?= number_format($movie['price'], 0, ',', '.') ?> VND
                    </div>
                </div>
            </div>

            <form action="<?= APP_URL ?>/movies/purchase/<?= $movie['id'] ?>" method="POST" id="paymentForm">
                <div class="payment-methods">
                    <h4>Chọn phương thức thanh toán</h4>

                    <div class="payment-method-item active">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="balanceMethod" value="balance" checked>
                            <label class="form-check-label" for="balanceMethod">
                                <span class="payment-icon"><i class="fas fa-wallet"></i></span>
                                <div class="payment-method-info">
                                    <div class="payment-method-name">Số dư tài khoản</div>
                                    <div class="payment-method-balance">Số dư hiện tại: <?= number_format($_SESSION['balance'] ?? 0, 0, ',', '.') ?> VND</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="payment-method-item">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="momoMethod" value="momo">
                            <label class="form-check-label" for="momoMethod">
                                <span class="payment-icon"><i class="fas fa-mobile-alt"></i></span>
                                <div class="payment-method-info">
                                    <div class="payment-method-name">Ví MoMo</div>
                                    <div class="payment-method-desc">Thanh toán nhanh chóng qua ví điện tử MoMo</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="payment-method-item">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="bankMethod" value="bank">
                            <label class="form-check-label" for="bankMethod">
                                <span class="payment-icon"><i class="fas fa-university"></i></span>
                                <div class="payment-method-info">
                                    <div class="payment-method-name">Chuyển khoản ngân hàng</div>
                                    <div class="payment-method-desc">Thanh toán bằng tài khoản ngân hàng của bạn</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="payment-method-item">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="cardMethod" value="card">
                            <label class="form-check-label" for="cardMethod">
                                <span class="payment-icon"><i class="fas fa-credit-card"></i></span>
                                <div class="payment-method-info">
                                    <div class="payment-method-name">Thẻ tín dụng/ghi nợ</div>
                                    <div class="payment-method-desc">Thanh toán bằng thẻ VISA, MasterCard, JCB</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Các phương thức thanh toán khác (ẩn mặc định) -->
                <div id="momoPaymentDetails" class="payment-details" style="display: none;">
                    <div class="alert alert-info">
                        <p>Quét mã QR bên dưới bằng ứng dụng MoMo để thanh toán:</p>
                        <div class="qr-container">
                            <img src="<?= PUBLIC_PATH ?>/assets/img/momo-qr.png" alt="MoMo QR Code" class="qr-code">
                        </div>
                    </div>
                </div>

                <div id="bankPaymentDetails" class="payment-details" style="display: none;">
                    <div class="alert alert-info">
                        <p>Vui lòng chuyển khoản đến tài khoản sau:</p>
                        <ul class="bank-details">
                            <li><strong>Ngân hàng:</strong> Vietcombank</li>
                            <li><strong>Số tài khoản:</strong> 1234567890</li>
                            <li><strong>Chủ tài khoản:</strong> CÔNG TY TNHH MOVIE STREAMING</li>
                            <li><strong>Nội dung chuyển khoản:</strong> MS<?= $movie['id'] ?>_UID<?= $_SESSION['user_id'] ?></li>
                        </ul>
                    </div>
                </div>

                <div id="cardPaymentDetails" class="payment-details" style="display: none;">
                    <div class="card-payment-form">
                        <div class="form-group">
                            <label for="cardNumber">Số thẻ</label>
                            <input type="text" class="form-control" id="cardNumber" placeholder="1234 5678 9012 3456">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cardExpiry">Ngày hết hạn</label>
                                    <input type="text" class="form-control" id="cardExpiry" placeholder="MM/YY">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cardCVV">Mã bảo mật (CVV)</label>
                                    <input type="text" class="form-control" id="cardCVV" placeholder="123">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="cardName">Tên chủ thẻ</label>
                            <input type="text" class="form-control" id="cardName" placeholder="NGUYEN VAN A">
                        </div>
                    </div>
                </div>

                <!-- Nạp tiền vào tài khoản (Hiển thị khi số dư không đủ) -->
                <?php if (isset($_SESSION['balance']) && $_SESSION['balance'] < $movie['price']): ?>
                    <div class="topup-reminder">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Số dư tài khoản của bạn không đủ để thanh toán. Vui lòng nạp thêm tiền hoặc chọn phương thức thanh toán khác.
                        </div>
                        <a href="<?= APP_URL ?>/users/topup" class="btn btn-warning">Nạp tiền ngay</a>
                    </div>
                <?php endif; ?>

                <div class="payment-summary">
                    <h4>Thông tin đơn hàng</h4>

                    <div class="payment-summary-item">
                        <span>Tên phim:</span>
                        <span><?= $movie['title'] ?></span>
                    </div>

                    <div class="payment-summary-item">
                        <span>Giá:</span>
                        <span><?= number_format($movie['price'], 0, ',', '.') ?> VND</span>
                    </div>

                    <div class="payment-summary-item">
                        <span>Thuế VAT (10%):</span>
                        <span><?= number_format($movie['price'] * 0.1, 0, ',', '.') ?> VND</span>
                    </div>

                    <div class="payment-summary-item payment-summary-total">
                        <span>Tổng cộng:</span>
                        <span><?= number_format($movie['price'] * 1.1, 0, ',', '.') ?> VND</span>
                    </div>
                </div>

                <div class="payment-terms">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                        <label class="form-check-label" for="agreeTerms">
                            Tôi đồng ý với <a href="#" data-toggle="modal" data-target="#paymentTermsModal">Điều khoản thanh toán</a> và <a href="#" data-toggle="modal" data-target="#privacyPolicyModal">Chính sách bảo mật</a>
                        </label>
                    </div>
                </div>

                <div class="payment-actions">
                    <button type="submit" class="btn btn-primary btn-lg btn-hover ripple" id="payButton">
                        <i class="fas fa-lock"></i> Thanh toán an toàn
                    </button>
                    <a href="<?= APP_URL ?>/movies/detail/<?= $movie['id'] ?>" class="btn btn-outline-secondary btn-lg">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Payment Terms Modal -->
<div class="modal fade" id="paymentTermsModal" tabindex="-1" role="dialog" aria-labelledby="paymentTermsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentTermsModalLabel">Điều khoản thanh toán</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>1. Quy định chung</h5>
                <p>Khi tiến hành thanh toán trên <?= APP_NAME ?>, khách hàng mặc nhiên đồng ý với các điều khoản thanh toán được quy định tại đây.</p>

                <h5>2. Phương thức thanh toán</h5>
                <p><?= APP_NAME ?> cung cấp nhiều phương thức thanh toán khác nhau như: thanh toán bằng số dư tài khoản, ví điện tử, thẻ tín dụng/ghi nợ và chuyển khoản ngân hàng.</p>

                <h5>3. Giá cả và thuế</h5>
                <p>Giá cả sản phẩm đã bao gồm thuế VAT 10%. Khách hàng có trách nhiệm thanh toán đầy đủ số tiền đã được hiển thị trên trang thanh toán.</p>

                <h5>4. Hoàn tiền</h5>
                <p>Việc hoàn tiền chỉ được thực hiện trong trường hợp phim không thể xem được do lỗi từ phía <?= APP_NAME ?>. Các trường hợp khác sẽ không được hoàn tiền.</p>

                <h5>5. Bảo mật thông tin</h5>
                <p><?= APP_NAME ?> cam kết bảo mật thông tin thanh toán của khách hàng theo quy định của pháp luật.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Đồng ý</button>
            </div>
        </div>
    </div>
</div>

<!-- Privacy Policy Modal -->
<div class="modal fade" id="privacyPolicyModal" tabindex="-1" role="dialog" aria-labelledby="privacyPolicyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="privacyPolicyModalLabel">Chính sách bảo mật</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>1. Thông tin chúng tôi thu thập</h5>
                <p>Chúng tôi thu thập các thông tin cá nhân như tên, email, địa chỉ và thông tin thanh toán của bạn khi bạn sử dụng dịch vụ thanh toán của chúng tôi.</p>

                <h5>2. Mục đích thu thập thông tin</h5>
                <p>Chúng tôi thu thập thông tin để xử lý thanh toán, cung cấp dịch vụ, gửi thông báo về giao dịch và hỗ trợ khách hàng.</p>

                <h5>3. Bảo mật thông tin</h5>
                <p>Chúng tôi sử dụng các biện pháp bảo mật tiên tiến để bảo vệ thông tin cá nhân và thông tin thanh toán của bạn.</p>

                <h5>4. Chia sẻ thông tin</h5>
                <p>Chúng tôi không chia sẻ thông tin cá nhân của bạn với bên thứ ba trừ khi được yêu cầu bởi pháp luật hoặc cần thiết để cung cấp dịch vụ cho bạn.</p>

                <h5>5. Quyền của bạn</h5>
                <p>Bạn có quyền truy cập, chỉnh sửa hoặc xóa thông tin cá nhân của mình theo quy định của pháp luật.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Đồng ý</button>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<style>
    .payment-page {
        padding: 50px 0;
        background-color: #f8f9fa;
    }

    .payment-container {
        max-width: 800px;
        margin: 0 auto;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        padding: 30px;
    }

    .payment-header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }

    .payment-header h2 {
        font-weight: 700;
        color: var(--primary-color);
    }

    .payment-movie {
        display: flex;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }

    .payment-movie-image {
        width: 100px;
        border-radius: 5px;
        overflow: hidden;
        margin-right: 20px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
    }

    .payment-movie-image img {
        width: 100%;
        height: auto;
    }

    .payment-movie-info h3 {
        font-weight: 600;
        margin-bottom: 10px;
    }

    .movie-meta {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }

    .movie-meta span {
        margin-right: 15px;
        font-size: 0.9rem;
        color: #6c757d;
    }

    .movie-meta i {
        margin-right: 5px;
        color: var(--primary-color);
    }

    .movie-price {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .payment-methods {
        margin-bottom: 30px;
    }

    .payment-methods h4 {
        font-weight: 600;
        margin-bottom: 15px;
    }

    .payment-method-item {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .payment-method-item:hover,
    .payment-method-item.active {
        border-color: var(--primary-color);
        background-color: rgba(63, 81, 181, 0.05);
    }

    .payment-method-item .form-check {
        padding-left: 0;
    }

    .payment-method-item .form-check-input {
        position: static;
        margin-left: 0;
        margin-right: 10px;
    }

    .payment-method-item .form-check-label {
        display: flex;
        align-items: center;
        width: 100%;
    }

    .payment-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--primary-color);
        color: white;
        border-radius: 50%;
        font-size: 1.25rem;
        margin-right: 15px;
    }

    .payment-method-info {
        flex: 1;
    }

    .payment-method-name {
        font-weight: 600;
        margin-bottom: 5px;
    }

    .payment-method-desc,
    .payment-method-balance {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .payment-details {
        margin-bottom: 30px;
        animation: fadeIn 0.3s ease-in-out;
    }

    .qr-container {
        text-align: center;
        margin: 15px 0;
    }

    .qr-code {
        max-width: 200px;
    }

    .bank-details {
        list-style: none;
        padding-left: 0;
    }

    .bank-details li {
        margin-bottom: 5px;
    }

    .card-payment-form {
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 8px;
    }

    .topup-reminder {
        margin-bottom: 30px;
    }

    .payment-summary {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
    }

    .payment-summary h4 {
        font-weight: 600;
        margin-bottom: 15px;
    }

    .payment-summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .payment-summary-total {
        font-weight: 700;
        font-size: 1.1rem;
        padding-top: 10px;
        margin-top: 10px;
        border-top: 1px solid #ddd;
    }

    .payment-terms {
        margin-bottom: 20px;
    }

    .payment-actions {
        display: flex;
        gap: 10px;
    }

    .payment-actions button,
    .payment-actions a {
        flex: 1;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Payment Method Selection
        const paymentMethodItems = document.querySelectorAll('.payment-method-item');
        const paymentMethodRadios = document.querySelectorAll('input[name="payment_method"]');

        // Additional payment details containers
        const momoPaymentDetails = document.getElementById('momoPaymentDetails');
        const bankPaymentDetails = document.getElementById('bankPaymentDetails');
        const cardPaymentDetails = document.getElementById('cardPaymentDetails');

        // Pay Button
        const payButton = document.getElementById('payButton');
        const paymentForm = document.getElementById('paymentForm');

        // Add click event to payment method items
        paymentMethodItems.forEach(item => {
            item.addEventListener('click', function() {
                // Find the radio input in this item
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;

                    // Remove active class from all items
                    paymentMethodItems.forEach(item => {
                        item.classList.remove('active');
                    });

                    // Add active class to selected item
                    this.classList.add('active');

                    // Hide all payment details
                    momoPaymentDetails.style.display = 'none';
                    bankPaymentDetails.style.display = 'none';
                    cardPaymentDetails.style.display = 'none';

                    // Show the corresponding payment details
                    switch (radio.value) {
                        case 'momo':
                            momoPaymentDetails.style.display = 'block';
                            break;
                        case 'bank':
                            bankPaymentDetails.style.display = 'block';
                            break;
                        case 'card':
                            cardPaymentDetails.style.display = 'block';
                            break;
                    }
                }
            });
        });

        // Add change event to payment method radios
        paymentMethodRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                // Find the parent payment method item
                const item = this.closest('.payment-method-item');
                if (item) {
                    // Trigger click event on the item
                    item.click();
                }
            });
        });

        // Payment Form Submission
        if (paymentForm) {
            paymentForm.addEventListener('submit', function(e) {
                const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;

                // For demo purposes, only balance payment method works directly
                if (selectedMethod !== 'balance') {
                    e.preventDefault();

                    // Show loading state
                    payButton.disabled = true;
                    payButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

                    // Simulate payment processing
                    setTimeout(function() {
                        alert('Chức năng thanh toán qua ' + getPaymentMethodName(selectedMethod) + ' đang được phát triển. Vui lòng sử dụng số dư tài khoản để thanh toán.');

                        // Reset button state
                        payButton.disabled = false;
                        payButton.innerHTML = '<i class="fas fa-lock"></i> Thanh toán an toàn';
                    }, 2000);
                }
            });
        }

        // Helper function to get payment method name
        function getPaymentMethodName(method) {
            switch (method) {
                case 'momo':
                    return 'Ví MoMo';
                case 'bank':
                    return 'Chuyển khoản ngân hàng';
                case 'card':
                    return 'Thẻ tín dụng/ghi nợ';
                default:
                    return 'Số dư tài khoản';
            }
        }
    });
</script>
<?php
$scripts = ob_get_clean();
require_once VIEW_PATH . '/layouts/footer.php';
?>