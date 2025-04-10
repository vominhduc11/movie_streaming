<?php require_once VIEW_PATH . '/layouts/header.php'; ?>

<div class="auth-container fade-in-trigger">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="auth-card">
                    <div class="auth-header text-center">
                        <h2><i class="fas fa-film"></i> <?= APP_NAME ?></h2>
                        <h4 class="mt-3">Đăng nhập</h4>
                    </div>

                    <div class="auth-body">
                        <?php if (isset($errors['login'])): ?>
                            <div class="alert alert-danger"><?= $errors['login'] ?></div>
                        <?php endif; ?>

                        <form action="<?= APP_URL ?>/auth/login" method="POST">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" value="<?= $email ?? '' ?>" required>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback"><?= $errors['email'] ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="password">Mật khẩu</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary toggle-password" tabindex="-1">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback d-block"><?= $errors['password'] ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group d-flex justify-content-between align-items-center">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                                    <label class="custom-control-label" for="remember">Ghi nhớ đăng nhập</label>
                                </div>
                                <a href="<?= APP_URL ?>/auth/forgot-password" class="forgot-password">Quên mật khẩu?</a>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block btn-hover ripple">
                                    <i class="fas fa-sign-in-alt"></i> Đăng nhập
                                </button>
                            </div>
                        </form>

                        <div class="social-login">
                            <p class="text-center">Hoặc đăng nhập với</p>
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
                        <p>Chưa có tài khoản? <a href="<?= APP_URL ?>/auth/register">Đăng ký ngay</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        const togglePassword = document.querySelector('.toggle-password');
        const passwordInput = document.querySelector('#password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Toggle icon
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    });
</script>
<?php
$scripts = ob_get_clean();
require_once VIEW_PATH . '/layouts/footer.php';
?>