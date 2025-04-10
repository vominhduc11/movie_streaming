<?php require_once VIEW_PATH . '/layouts/header.php'; ?>

<div class="auth-container fade-in-trigger">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="auth-card">
                    <div class="auth-header text-center">
                        <h2><i class="fas fa-film"></i> <?= APP_NAME ?></h2>
                        <h4 class="mt-3">Đăng ký tài khoản</h4>
                    </div>

                    <div class="auth-body">
                        <?php if (isset($errors['register'])): ?>
                            <div class="alert alert-danger"><?= $errors['register'] ?></div>
                        <?php endif; ?>

                        <form action="<?= APP_URL ?>/auth/register" method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username">Tên đăng nhập</label>
                                        <input type="text" id="username" name="username" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" value="<?= $username ?? '' ?>" required>
                                        <?php if (isset($errors['username'])): ?>
                                            <div class="invalid-feedback"><?= $errors['username'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" id="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" value="<?= $email ?? '' ?>" required>
                                        <?php if (isset($errors['email'])): ?>
                                            <div class="invalid-feedback"><?= $errors['email'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="full_name">Họ và tên</label>
                                <input type="text" id="full_name" name="full_name" class="form-control" value="<?= $full_name ?? '' ?>">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Mật khẩu</label>
                                        <div class="input-group">
                                            <input type="password" id="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" required>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary toggle-password" tabindex="-1" data-target="password">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <?php if (isset($errors['password'])): ?>
                                            <div class="invalid-feedback d-block"><?= $errors['password'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="confirm_password">Xác nhận mật khẩu</label>
                                        <div class="input-group">
                                            <input type="password" id="confirm_password" name="confirm_password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" required>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary toggle-password" tabindex="-1" data-target="confirm_password">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <?php if (isset($errors['confirm_password'])): ?>
                                            <div class="invalid-feedback d-block"><?= $errors['confirm_password'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="terms" name="terms" required>
                                    <label class="custom-control-label" for="terms">
                                        Tôi đồng ý với <a href="#" data-toggle="modal" data-target="#termsModal">Điều khoản sử dụng</a> và <a href="#" data-toggle="modal" data-target="#privacyModal">Chính sách bảo mật</a>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block btn-hover ripple">
                                    <i class="fas fa-user-plus"></i> Đăng ký
                                </button>
                            </div>
                        </form>

                        <div class="social-login">
                            <p class="text-center">Hoặc đăng ký với</p>
                            <div class="row">
                                <div class="col-6">
                                    <a href="#" class="btn btn-outline-primary btn-block">
                                        <i class="fab fa-facebook-f"></i> Facebook
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="#" class="btn btn-outline-danger btn-block">
                                        <i class="fab fa-google"></i> Google
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="auth-footer text-center">
                        <p>Đã có tài khoản? <a href="<?= APP_URL ?>/auth/login">Đăng nhập</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terms Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Điều khoản sử dụng</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>1. Chấp nhận điều khoản</h5>
                <p>Bằng việc truy cập và sử dụng dịch vụ của <?= APP_NAME ?>, bạn đồng ý tuân thủ và chấp nhận ràng buộc bởi các điều khoản và điều kiện này.</p>

                <h5>2. Tài khoản người dùng</h5>
                <p>Bạn phải tạo một tài khoản để sử dụng dịch vụ của chúng tôi. Bạn chịu trách nhiệm duy trì tính bảo mật của tài khoản của mình và chịu trách nhiệm cho tất cả các hoạt động diễn ra dưới tài khoản của bạn.</p>

                <h5>3. Thanh toán và hoàn tiền</h5>
                <p>Khi bạn mua phim trên <?= APP_NAME ?>, giao dịch được xem là cuối cùng và không được hoàn lại, trừ khi có lỗi kỹ thuật từ phía chúng tôi.</p>

                <h5>4. Nội dung</h5>
                <p>Tất cả nội dung trên <?= APP_NAME ?> đều được bảo vệ bởi bản quyền. Bạn không được phép sao chép, phân phối, hoặc tạo các tác phẩm phái sinh từ nội dung của chúng tôi.</p>

                <h5>5. Hành vi bị cấm</h5>
                <p>Bạn không được sử dụng dịch vụ của chúng tôi để thực hiện bất kỳ hành vi vi phạm pháp luật hoặc gây hại đến người khác.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Đồng ý</button>
            </div>
        </div>
    </div>
</div>

<!-- Privacy Modal -->
<div class="modal fade" id="privacyModal" tabindex="-1" role="dialog" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="privacyModalLabel">Chính sách bảo mật</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>1. Thông tin chúng tôi thu thập</h5>
                <p>Chúng tôi thu thập thông tin cá nhân như tên, email, và thông tin thanh toán khi bạn đăng ký tài khoản hoặc mua phim.</p>

                <h5>2. Cách chúng tôi sử dụng thông tin</h5>
                <p>Chúng tôi sử dụng thông tin của bạn để cung cấp dịch vụ, xử lý thanh toán, gửi thông báo và cải thiện trải nghiệm người dùng.</p>

                <h5>3. Bảo mật thông tin</h5>
                <p>Chúng tôi áp dụng các biện pháp bảo mật hợp lý để bảo vệ thông tin cá nhân của bạn khỏi truy cập trái phép, sửa đổi hoặc tiết lộ.</p>

                <h5>4. Chia sẻ thông tin</h5>
                <p>Chúng tôi không chia sẻ thông tin cá nhân của bạn với bên thứ ba, trừ khi được yêu cầu bởi pháp luật hoặc để cung cấp dịch vụ cho bạn.</p>

                <h5>5. Cookie</h5>
                <p>Chúng tôi sử dụng cookie để cải thiện trải nghiệm người dùng và phân tích cách sử dụng trang web của chúng tôi.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Đồng ý</button>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<style>
    .auth-container {
        padding: 50px 0;
    }

    .auth-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 30px;
    }

    .auth-header {
        padding: 30px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #eee;
    }

    .auth-body {
        padding: 30px;
    }

    .auth-footer {
        padding: 20px;
        background-color: #f8f9fa;
        border-top: 1px solid #eee;
    }

    .social-login {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    .forgot-password {
        font-size: 0.9rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        const toggleButtons = document.querySelectorAll('.toggle-password');

        toggleButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target') || 'password';
                const passwordInput = document.getElementById(targetId);

                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Toggle icon
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        });
    });
</script>
<?php
$scripts = ob_get_clean();
require_once VIEW_PATH . '/layouts/footer.php';
?>