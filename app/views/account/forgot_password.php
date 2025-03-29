<?php
$pageTitle = 'Quên mật khẩu';
require_once ROOT_PATH . '/app/views/shares/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">Quên mật khẩu</h3>

                    <!-- Form nhập email -->
                    <div id="emailForm">
                        <p class="text-muted text-center mb-4">
                            Nhập email Gmail của bạn để nhận mã xác nhận
                        </p>
                        <form id="forgotPasswordForm" method="POST" action="<?= ROOT_URL ?>/Account/sendResetCode" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label class="form-label">Email (Gmail)</label>
                                <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required>
                                <div class="invalid-feedback">
                                    Vui lòng nhập email Gmail hợp lệ
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    Gửi mã xác nhận
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Form nhập mã xác nhận và mật khẩu mới (ẩn ban đầu) -->
                    <div id="resetForm" style="display: none;">
                        <p class="text-muted text-center mb-4">
                            Nhập mã xác nhận đã được gửi đến email của bạn
                        </p>
                        <form id="resetPasswordForm" method="POST" action="<?= ROOT_URL ?>/Account/resetPassword">
                            <input type="hidden" name="email" id="confirmedEmail">
                            <div class="mb-3">
                                <label class="form-label">Mã xác nhận</label>
                                <input type="text" name="reset_code" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mật khẩu mới</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Xác nhận mật khẩu mới</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    Đặt lại mật khẩu
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="text-center mt-3">
                        <a href="<?= ROOT_URL ?>/Account/login" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i>Quay lại đăng nhập
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Truyền ROOT_URL từ PHP sang JavaScript
    const ROOT_URL = '<?= ROOT_URL ?>';

    document.addEventListener('DOMContentLoaded', function() {
        const forgotPasswordForm = document.getElementById('forgotPasswordForm');
        const resetPasswordForm = document.getElementById('resetPasswordForm');

        forgotPasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const emailInput = this.querySelector('input[name="email"]');
            const email = emailInput.value;

            // Kiểm tra email có phải Gmail không
            if (!email.endsWith('@gmail.com')) {
                emailInput.setCustomValidity('Vui lòng nhập email Gmail hợp lệ');
                emailInput.classList.add('is-invalid');
                this.classList.add('was-validated');
                return;
            } else {
                emailInput.setCustomValidity('');
            }

            fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `email=${encodeURIComponent(email)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('emailForm').style.display = 'none';
                        document.getElementById('resetForm').style.display = 'block';
                        document.getElementById('confirmedEmail').value = email;

                        Swal.fire({
                            icon: 'success',
                            title: 'Đã gửi mã xác nhận',
                            text: 'Vui lòng kiểm tra email của bạn',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi gửi yêu cầu:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Đã có lỗi xảy ra, vui lòng thử lại'
                    });
                });
        });

        resetPasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(formData).toString()
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: 'Mật khẩu đã được đặt lại',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            console.log('Chuyển hướng đến:', `${ROOT_URL}/Account/login`);
                            window.location.href = `${ROOT_URL}/Account/login`;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi gửi yêu cầu:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Đã có lỗi xảy ra, vui lòng thử lại'
                    });
                });
        });
    });
</script>

<?php require_once ROOT_PATH . '/app/views/shares/footer.php'; ?>