<?php
$pageTitle = 'Đăng nhập';
require_once ROOT_PATH . '/app/views/shares/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Đăng nhập</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error) && !empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?= $_SESSION['success'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['error'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <form method="POST" action="<?= ROOT_URL ?>/Account/login" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Email (Gmail)</label>
                            <input type="email" class="form-control" name="username" placeholder="example@gmail.com" required>
                            <div class="invalid-feedback">
                                Vui lòng nhập email Gmail hợp lệ
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="invalid-feedback">
                                Vui lòng nhập mật khẩu
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p class="mb-0">Chưa có tài khoản?
                        <a href="<?= ROOT_URL ?>/Account/register" class="text-primary">Đăng ký ngay</a>
                    </p>
                    <div class="text-center mt-3">
                        <a href="<?= ROOT_URL ?>/Account/forgotPassword" class="text-decoration-none">
                            Quên mật khẩu?
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/app/views/shares/footer.php'; ?>

<style>
    .form-container {
        max-width: 500px;
        margin: 50px auto;
    }

    .card {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }

    .btn-action {
        transition: all 0.2s;
    }

    .btn-action:hover {
        transform: scale(1.05);
    }
</style>

<script>
    // Bootstrap form validation
    (function() {
        'use strict';
        var forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                var emailInput = form.querySelector('input[name="username"]');
                var emailValue = emailInput.value;
                if (!emailValue.endsWith('@gmail.com')) {
                    emailInput.setCustomValidity('Vui lòng nhập email Gmail hợp lệ');
                    emailInput.classList.add('is-invalid');
                } else {
                    emailInput.setCustomValidity('');
                }

                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>