<div class="container">
    <div class="form-container">
        <div class="card">
            <div class="card-body p-4">
                <h1 class="text-center mb-4">Đăng nhập</h1>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/T6-Sang/webbanhang/Account/login" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="username" class="form-label">Tên đăng nhập</label>
                        <input type="text" class="form-control" id="username" name="username"
                            value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                            required>
                        <div class="invalid-feedback">
                            Vui lòng nhập tên đăng nhập
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">
                            Vui lòng nhập mật khẩu
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg btn-action">
                            <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                        </button>
                        <a href="/T6-Sang/webbanhang/Account/register" class="btn btn-outline-secondary btn-action">
                            <i class="bi bi-person-plus"></i> Chưa có tài khoản? Đăng ký
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const button = input.nextElementSibling;
        const icon = button.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    // Bootstrap form validation
    (function() {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>