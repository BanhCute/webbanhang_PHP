<?php
$pageTitle = 'Đặt hàng thành công';
$currentPage = 'cart';
?>

<div class="container py-5">
    <div class="card">
        <div class="card-body text-center">
            <i class="fas fa-check-circle text-success" style="font-size: 64px;"></i>
            <h2 class="mt-4 mb-3">Đặt hàng thành công!</h2>
            <p class="lead mb-4">Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất.</p>

            <div class="text-start">
                <h5 class="mb-3">Thông tin đơn hàng #<?= $order['id'] ?></h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Họ tên:</strong> <?= htmlspecialchars($order['name']) ?></p>
                        <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                        <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['address']) ?></p>
                        <?php if (!empty($order['note'])): ?>
                            <p><strong>Ghi chú:</strong> <?= htmlspecialchars($order['note']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Đơn giá</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['details'] as $detail): ?>
                                <tr>
                                    <td><?= htmlspecialchars($detail['product_name']) ?></td>
                                    <td class="text-center"><?= $detail['quantity'] ?></td>
                                    <td class="text-end"><?= number_format($detail['price']) ?> đ</td>
                                    <td class="text-end"><?= number_format($detail['price'] * $detail['quantity']) ?> đ</td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                                <td class="text-end"><strong><?= number_format($order['total_amount']) ?> đ</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                <a href="<?= ROOT_URL ?>/Product/list" class="btn btn-primary">
                    <i class="fas fa-home me-2"></i>Tiếp tục mua sắm
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .table th {
        background-color: #f8f9fa;
    }

    .fas.fa-check-circle {
        animation: scale 0.5s ease-in-out;
    }

    @keyframes scale {
        0% {
            transform: scale(0);
        }

        50% {
            transform: scale(1.2);
        }

        100% {
            transform: scale(1);
        }
    }
</style>