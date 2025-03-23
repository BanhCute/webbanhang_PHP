<div class="container py-5">
    <div class="row">
        <!-- Thông tin cá nhân -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-circle me-2"></i>Thông tin cá nhân
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted">Tên đăng nhập:</label>
                        <div class="fw-bold"><?= htmlspecialchars($user['username']) ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted">Vai trò:</label>
                        <div><?= htmlspecialchars($user['role']) ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted">Ngày tham gia:</label>
                        <div><?= date('d/m/Y', strtotime($user['created_at'])) ?></div>
                    </div>

                    <!-- Form đổi mật khẩu -->
                    <form id="changePasswordForm" method="POST" action="<?= ROOT_URL ?>/Account/changePassword" class="mt-3">
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu hiện tại</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('currentPassword')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu mới</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="newPassword" name="new_password" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('newPassword')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Xác nhận mật khẩu mới</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('confirmPassword')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-key me-2"></i>Đổi mật khẩu
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Lịch sử đơn hàng -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shopping-bag me-2"></i>Lịch sử đơn hàng
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($orders)): ?>
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                            </div>
                            <h5 class="text-muted mb-3">Bạn chưa có đơn hàng nào</h5>
                            <a href="<?= ROOT_URL ?>/Product/list" class="btn btn-primary btn-lg">
                                <i class="fas fa-shopping-cart me-2"></i>Mua sắm ngay
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã đơn hàng</th>
                                        <th>Ngày đặt</th>
                                        <th class="text-center">Số sản phẩm</th>
                                        <th class="text-end">Tổng tiền</th>
                                        <th class="text-center">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-primary">#<?= $order['id'] ?></span>
                                            </td>
                                            <td>
                                                <i class="far fa-calendar-alt text-muted me-1"></i>
                                                <?= date('d/m/Y', strtotime($order['created_at'])) ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-box me-1"></i>
                                                    <?= $order['total_items'] ?>
                                                </span>
                                            </td>
                                            <td class="text-end text-danger fw-bold">
                                                <?= number_format($order['total_amount']) ?>đ
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                $statusClass = '';
                                                $statusIcon = '';
                                                $statusText = '';
                                                switch ($order['status']) {
                                                    case 0:
                                                        $statusClass = 'bg-warning';
                                                        $statusIcon = 'fa-clock';
                                                        $statusText = 'Chờ xác nhận';
                                                        break;
                                                    case 1:
                                                        $statusClass = 'bg-info';
                                                        $statusIcon = 'fa-spinner fa-spin';
                                                        $statusText = 'Đang xử lý';
                                                        break;
                                                    case 2:
                                                        $statusClass = 'bg-success';
                                                        $statusIcon = 'fa-check-circle';
                                                        $statusText = 'Đã giao';
                                                        break;
                                                    case 3:
                                                        $statusClass = 'bg-danger';
                                                        $statusIcon = 'fa-times-circle';
                                                        $statusText = 'Đã hủy';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?= $statusClass ?>">
                                                    <i class="fas <?= $statusIcon ?> me-1"></i>
                                                    <?= $statusText ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const button = input.nextElementSibling;
        const icon = button.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (newPassword !== confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: 'Mật khẩu xác nhận không khớp!'
            });
            return;
        }

        // Submit form if validation passes
        fetch(this.action, {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: data.message
                    }).then(() => {
                        this.reset();
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
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Đã có lỗi xảy ra, vui lòng thử lại sau'
                });
            });
    });

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