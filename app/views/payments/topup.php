<?php require_once VIEW_PATH . '/layouts/header.php'; ?>

<div class="topup-page">
    <div class="container">
        <div class="topup-container fade-in-trigger">
            <div class="topup-header">
                <h2><i class="fas fa-wallet"></i> Nạp tiền vào tài khoản</h2>
                <p>Nạp tiền để mua phim và sử dụng các dịch vụ của chúng tôi</p>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="payment-methods-container">
                        <h3>Chọn phương thức nạp tiền</h3>

                        <form action="<?= APP_URL ?>/payments/topup" method="POST" id="topupForm">
                            <div class="amount-selection">
                                <h4>Chọn số tiền nạp</h4>
                                <div class="amount-options">
                                    <div class="amount-option">
                                        <input type="radio" name="amount" id="amount-50k" value="50000" class="amount-radio">
                                        <label for="amount-50k" class="amount-label">50.000 VND</label>
                                    </div>
                                    <div class="amount-option">
                                        <input type="radio" name="amount" id="amount-100k" value="100000" class="amount-radio">
                                        <label for="amount-100k" class="amount-label">100.000 VND</label>
                                    </div>
                                    <div class="amount-option">
                                        <input type="radio" name="amount" id="amount-200k" value="200000" class="amount-radio">
                                        <label for="amount-200k" class="amount-label">200.000 VND</label>
                                    </div>
                                    <div class="amount-option">
                                        <input type="radio" name="amount" id="amount-500k" value="500000" class="amount-radio">
                                        <label for="amount-500k" class="amount-label">500.000 VND</label>
                                    </div>
                                    <div class="amount-option">
                                        <input type="radio" name="amount" id="amount-custom" value="custom" class="amount-radio">
                                        <label for="amount-custom" class="amount-label amount-custom">
                                            <input type="text" name="custom_amount" id="custom-amount" class="form-control currency-input" placeholder="Số tiền khác">
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="payment-methods">
                                <h4>Chọn phương thức thanh toán</h4>

                                <div class="payment-method-item active">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="momoMethod" value="momo" checked>
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

                            <!-- Các phương thức thanh toán chi tiết (ẩn mặc định) -->
                            <div id="momoPaymentDetails" class="payment-details">
                                <div class="alert alert-info">
                                    <p>Sau khi nhấn "Nạp tiền", bạn sẽ được chuyển đến trang thanh toán MoMo.</p>
                                </div>
                            </div>

                            <div id="bankPaymentDetails" class="payment-details" style="display: none;">
                                <div class="alert alert-info">
                                    <p>Vui lòng chuyển khoản đến tài khoản sau:</p>
                                    <ul class="bank-details">
                                        <li><strong>Ngân hàng:</strong> Vietcombank</li>
                                        <li><strong>Số tài khoản:</strong> 1234567890</li>
                                        <li><strong>Chủ tài khoản:</strong> CÔNG TY TNHH MOVIE STREAMING</li>
                                        <li><strong>Nội dung chuyển khoản:</strong> NAPTIEN_<?= $user['username'] ?></li>
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

                            <div class="topup-actions">
                                <button type="submit" class="btn btn-primary btn-lg btn-hover ripple" id="topupButton">
                                    <i class="fas fa-wallet"></i> Nạp tiền
                                </button>
                                <a href="<?= APP_URL ?>/users/profile" class="btn btn-outline-secondary btn-lg">Hủy</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="topup-sidebar">
                        <div class="account-balance">
                            <h3>Số dư hiện tại</h3>
                            <div class="balance-amount"><?= number_format($user['balance'], 0, ',', '.') ?> VND</div>
                        </div>

                        <div class="topup-summary">
                            <h3>Thông tin nạp tiền</h3>
                            <div class="summary-item">
                                <span class="summary-label">Số tiền nạp:</span>
                                <span class="summary-value" id="summary-amount">0 VND</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Phí giao dịch:</span>
                                <span class="summary-value" id="summary-fee">0 VND</span>
                            </div>
                            <div class="summary-item total">
                                <span class="summary-label">Tổng cộng:</span>
                                <span class="summary-value" id="summary-total">0 VND</span>
                            </div>
                        </div>

                        <div class="topup-note">
                            <h3>Lưu ý</h3>
                            <ul>
                                <li>Số tiền nạp tối thiểu là 50.000 VND.</li>
                                <li>Số dư tài khoản có thể sử dụng để mua phim và các dịch vụ khác.</li>
                                <li>Trong trường hợp gặp sự cố khi nạp tiền, vui lòng liên hệ với chúng tôi qua email: support@moviestreaming.com</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<style>
    .topup-page {
        padding: 50px 0;
        background-color: #f8f9fa;
    }

    .topup-container {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        padding: 30px;
        margin-bottom: 30px;
    }

    .topup-header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }

    .topup-header h2 {
        font-weight: 700;
        color: var(--primary-color);
    }

    .payment-methods-container {
        margin-bottom: 30px;
    }

    .payment-methods-container h3 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .amount-selection {
        margin-bottom: 30px;
    }

    .amount-selection h4 {
        font-size: 1.25rem;
        margin-bottom: 15px;
    }

    .amount-options {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }

    .amount-option {
        position: relative;
    }

    .amount-radio {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .amount-label {
        display: block;
        padding: 15px 25px;
        border: 2px solid #ddd;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .amount-label:hover {
        border-color: var(--primary-color);
    }

    .amount-radio:checked+.amount-label {
        border-color: var(--primary-color);
        background-color: rgba(63, 81, 181, 0.05);
    }

    .amount-custom {
        padding: 8px;
        min-width: 150px;
    }

    .amount-custom input {
        border: none;
        background: transparent;
        width: 100%;
        padding: 5px;
    }

    .payment-method-item {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .payment-method-item:hover,
    .payment-method-item.active {
        border-color: var(--primary-color);
        background-color: rgba(63, 81, 181, 0.05);
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

    .payment-method-desc {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .payment-details {
        margin: 20px 0;
        animation: fadeIn 0.3s ease-in-out;
    }

    .card-payment-form {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
    }

    .topup-actions {
        margin-top: 30px;
    }

    .topup-sidebar {
        position: sticky;
        top: 20px;
    }

    .account-balance,
    .topup-summary,
    .topup-note {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .account-balance h3,
    .topup-summary h3,
    .topup-note h3 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .balance-amount {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px dashed #ddd;
    }

    .summary-item.total {
        font-weight: 700;
        font-size: 1.1rem;
        border-bottom: none;
        margin-top: 15px;
        padding-top: 10px;
        border-top: 2px solid #ddd;
    }

    .topup-note ul {
        padding-left: 20px;
        margin-bottom: 0;
    }

    .topup-note li {
        margin-bottom: 8px;
        color: #6c757d;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Payment Method Selection
        const paymentMethodItems = document.querySelectorAll('.payment-method-item');
        const paymentMethodRadios = document.querySelectorAll('input[name="payment_method"]');

        // Amount options
        const amountRadios = document.querySelectorAll('input[name="amount"]');
        const customAmountInput = document.getElementById('custom-amount');

        // Summary elements
        const summaryAmount = document.getElementById('summary-amount');
        const summaryFee = document.getElementById('summary-fee');
        const summaryTotal = document.getElementById('summary-total');

        // Additional payment details containers
        const momoPaymentDetails = document.getElementById('momoPaymentDetails');
        const bankPaymentDetails = document.getElementById('bankPaymentDetails');
        const cardPaymentDetails = document.getElementById('cardPaymentDetails');

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

                    // Update fee and total
                    updateSummary();
                }
            });
        });

        // Handle amount selection
        amountRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                // If custom amount is selected, focus on input
                if (this.value === 'custom') {
                    customAmountInput.focus();
                }

                // Update summary
                updateSummary();
            });
        });

        // Handle custom amount input
        customAmountInput.addEventListener('input', function() {
            // Select custom amount radio
            document.getElementById('amount-custom').checked = true;

            // Update summary
            updateSummary();
        });

        // Format custom amount input
        customAmountInput.addEventListener('input', function(e) {
            // Remove non-digit characters
            let value = this.value.replace(/\D/g, '');

            // Format with thousand separator
            if (value.length > 0) {
                value = parseInt(value).toLocaleString('vi-VN');
            }

            // Update input value
            this.value = value;
        });

        // Function to update summary
        function updateSummary() {
            let amount = 0;

            // Get selected amount
            const selectedAmount = document.querySelector('input[name="amount"]:checked');
            if (selectedAmount) {
                if (selectedAmount.value === 'custom') {
                    // Parse custom amount
                    amount = parseInt(customAmountInput.value.replace(/\D/g, '') || 0);
                } else {
                    amount = parseInt(selectedAmount.value);
                }
            }

            // Calculate fee (0% for demo)
            const fee = 0;

            // Calculate total
            const total = amount + fee;

            // Update summary
            summaryAmount.textContent = amount.toLocaleString('vi-VN') + ' VND';
            summaryFee.textContent = fee.toLocaleString('vi-VN') + ' VND';
            summaryTotal.textContent = total.toLocaleString('vi-VN') + ' VND';
        }

        // Form validation
        const topupForm = document.getElementById('topupForm');
        topupForm.addEventListener('submit', function(e) {
            let amount = 0;

            // Get selected amount
            const selectedAmount = document.querySelector('input[name="amount"]:checked');
            if (selectedAmount) {
                if (selectedAmount.value === 'custom') {
                    // Parse custom amount
                    amount = parseInt(customAmountInput.value.replace(/\D/g, '') || 0);
                } else {
                    amount = parseInt(selectedAmount.value);
                }
            }

            // Validate amount
            if (!amount || amount < 50000) {
                e.preventDefault();
                alert('Vui lòng chọn số tiền nạp tối thiểu 50.000 VND');
                return;
            }

            // Validate card info if card payment is selected
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
            if (selectedMethod && selectedMethod.value === 'card') {
                const cardNumber = document.getElementById('cardNumber').value;
                const cardExpiry = document.getElementById('cardExpiry').value;
                const cardCVV = document.getElementById('cardCVV').value;
                const cardName = document.getElementById('cardName').value;

                if (!cardNumber || !cardExpiry || !cardCVV || !cardName) {
                    e.preventDefault();
                    alert('Vui lòng nhập đầy đủ thông tin thẻ');
                    return;
                }
            }

            // Show loading state
            const topupButton = document.getElementById('topupButton');
            topupButton.disabled = true;
            topupButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
        });

        // Initialize summary
        updateSummary();
    });
</script>
<?php
$scripts = ob_get_clean();
require_once VIEW_PATH . '/layouts/footer.php';
?>