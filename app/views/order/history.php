<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2"></i>Lịch sử đơn hàng
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
                                    <td>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>
                                            Hoàn tất
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