<?php
$pageTitle = 'Quản lý đơn hàng';
$currentPage = 'order';
require_once ROOT_PATH . '/app/views/shares/header_admin.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-shopping-cart me-2"></i>Quản lý đơn hàng</h2>
        <div>
            <button class="btn btn-outline-secondary me-2" type="button" data-bs-toggle="collapse" data-bs-target="#filterSection">
                <i class="fas fa-filter me-1"></i>Lọc
            </button>
            <div class="btn-group">
                <button class="btn btn-outline-primary" data-status="all">Tất cả</button>
                <button class="btn btn-outline-warning" data-status="pending">Chờ xử lý</button>
                <button class="btn btn-outline-success" data-status="completed">Hoàn thành</button>
                <button class="btn btn-outline-danger" data-status="cancelled">Đã hủy</button>
            </div>
        </div>
    </div>

    <!-- Phần lọc -->
    <div class="collapse mb-4" id="filterSection">
        <div class="card card-body">
            <form class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" class="form-control" name="from_date">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" class="form-control" name="to_date">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tìm kiếm</label>
                    <input type="text" class="form-control" placeholder="Mã đơn hoặc tên khách hàng">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block w-100">
                        <i class="fas fa-search me-1"></i>Tìm kiếm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách đơn hàng -->
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Số điện thoại</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td><a href="#" class="text-primary fw-bold" data-bs-toggle="modal" data-bs-target="#orderDetail<?= $order['id'] ?>">#<?= $order['id'] ?></a></td>
                                <td><?= htmlspecialchars($order['username']) ?></td>
                                <td><?= htmlspecialchars($order['phone'] ?? '') ?></td>
                                <td class="text-end"><?= number_format($order['total_amount']) ?> đ</td>
                                <td>Hoàn thành</td>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" title="Xem chi tiết" data-bs-toggle="modal" data-bs-target="#orderDetail<?= $order['id'] ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" title="Hủy đơn" onclick="cancelOrder(<?= $order['id'] ?>)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .status-select {
        min-width: 120px;
    }
</style>

<script>
    $(document).ready(function() {
        // Xử lý thay đổi trạng thái đơn hàng
        $('.status-select').change(function() {
            const orderId = $(this).data('order-id');
            const status = $(this).val();

            $.ajax({
                url: '<?= ROOT_URL ?>/admin/order/updateStatus',
                method: 'POST',
                data: {
                    order_id: orderId,
                    status: status
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        alert('Cập nhật trạng thái thành công!');
                    } else {
                        alert('Có lỗi xảy ra: ' + data.message);
                    }
                }
            });
        });
    });
</script>

<?php require_once ROOT_PATH . '/app/views/shares/footer_admin.php'; ?>