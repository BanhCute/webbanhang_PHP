<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
</head>

<body class="bg-light">
    <div class="container">
        <div class="form-container">
            <div class="card">
                <div class="card-body p-4">
                    <h1 class="text-center mb-4">Đăng ký tài khoản</h1>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <form method="POST" action="/T6-Sang/webbanhang/Account/register" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="username" class="form-label">Email (Gmail)</label>
                            <input type="email" class="form-control" id="username" name="username" placeholder="example@gmail.com" required>
                            <div class="invalid-feedback">
                                Vui lòng nhập email Gmail hợp lệ (example@gmail.com)
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="invalid-feedback">
                                Vui lòng nhập mật khẩu
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            <div class="invalid-feedback">
                                Vui lòng xác nhận mật khẩu
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg btn-action">
                                <i class="bi bi-person-plus"></i> Đăng ký
                            </button>
                            <a href="/T6-Sang/webbanhang/Account/login" class="btn btn-outline-secondary btn-action">
                                <i class="bi bi-arrow-left"></i> Đã có tài khoản? Đăng nhập
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript để kiểm tra định dạng Gmail
        (function() {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    var emailInput = form.querySelector('#username');
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
</body>

</html>